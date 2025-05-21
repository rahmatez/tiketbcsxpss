<?php

namespace App\Http\Controllers;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Ticket;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\Image\PngImageBackEnd;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('not_logged_in');
        }

        // Retrieve data from request
        $gameId = $request->input('game_id');
        $ticketCategory = $request->input('ticket_category');
        $quantity = $request->input('purchase_quantity');

        // Validate input data
        $request->validate([
            'game_id' => 'required|integer|exists:games,id',
            'ticket_category' => 'required|string',
            'purchase_quantity' => 'required|integer|min:1|max:2',
        ]);

        // Get game and ticket details
        $game = Game::findOrFail($gameId);
        $ticket = Ticket::where('game_id', $gameId)->where('category', $ticketCategory)->firstOrFail();
        
        // Check if ticket sales are still open
        if (!$game->isTicketSalesOpen()) {
            return redirect()->back()->with('error', 'Batas waktu pembelian tiket untuk pertandingan ini telah berakhir.');
        }

        // Get the total tickets already purchased by the user for this game
        $userId = Auth::id();
        $totalTicketsPurchased = Order::where('user_id', $userId)
            ->where('game_id', $gameId)
            ->sum('quantity');

        if ($totalTicketsPurchased + $quantity > 2) {
            return view('orders.limit_exceeded'); // Show the limit exceeded message
        }

        return view('orders.checkout', [
            'user' => Auth::user(),
            'game' => $game,
            'ticket' => $ticket,
            'quantity' => $quantity
        ]);
    }


    public function finalizeCheckout(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'game_id' => 'required|exists:games,id',
            'ticket_id' => 'required|exists:tickets,id',
            'quantity' => 'required|integer|min:1|max:2',
        ]);

        // Create order with pending status
        $order = new Order();
        $order->user_id = $request->input('user_id');
        $order->game_id = $request->input('game_id');
        $order->ticket_id = $request->input('ticket_id');
        $order->quantity = $request->input('quantity');
        $order->payment_method = 'pending'; // Will be updated after payment
        $order->payment_status = 'pending'; // Will be updated after payment
        $order->status = 'pending'; // Overall order status
        
        // Generate temporary order code
        $tempId = Str::uuid()->toString();
        $order->qr_code = $tempId; // Temporary QR code, will be updated after payment confirmed
        $order->save();
        
        // Redirect to payment page
        return redirect()->route('orders.show', $order->id);
    }

    public function myTickets()
    {
        $user = Auth::user();
        
        // Ambil semua order untuk diklasifikasikan di view
        $orders = Order::where('user_id', $user->id)
                  ->with('game', 'ticket')
                  ->orderBy('created_at', 'desc')
                  ->get();
        
        // Pisahkan orders berdasarkan status untuk kemudahan di view
        $paidOrders = $orders->whereIn('status', ['paid', 'redeemed']); // Include redeemed tickets
        $pendingOrders = $orders->where('status', 'pending');
        
        return view('orders.my_tickets', [
            'orders' => $orders,
            'paidOrders' => $paidOrders,
            'pendingOrders' => $pendingOrders
        ]);
    }

    /**
     * Menampilkan detail pesanan dan status pembayaran
     */
    public function show($id)
    {
        $order = Order::with(['game', 'ticket', 'user'])->findOrFail($id);
        
        // Cek otorisasi - hanya user pemilik order atau admin yang bisa melihat
        if (Auth::id() !== $order->user_id && !Auth::guard('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Jika order masih pending tapi sudah memiliki midtrans_order_id, cek status di Midtrans
        if ($order->status === 'pending' && $order->midtrans_order_id) {
            try {
                // Cek status pembayaran di Midtrans
                $midtransController = new \App\Http\Controllers\PaymentController();
                $response = $midtransController->checkStatus($order);
                
                // Refresh order data setelah cek status
                $order = Order::with(['game', 'ticket', 'user'])->findOrFail($id);
                
                Log::info('Order status refreshed: ' . $order->status);
            } catch (\Exception $e) {
                Log::error('Error checking payment status: ' . $e->getMessage());
            }
        }
        
        // Ambil flash data untuk status pembayaran jika ada
        $paymentStatus = session('payment_status');
        $message = session('message');
        
        return view('payment.show', [
            'order' => $order,
            'paymentStatus' => $paymentStatus,
            'message' => $message
        ]);
    }
    
    public function ticketDetail($id)
    {
        $order = Order::findOrFail($id);

        // Cek otorisasi - hanya user pemilik order yang bisa melihat
        if (Auth::id() !== $order->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Jika tiket sudah di redeem (digunakan), redirect ke halaman my tickets dengan pesan
        if ($order->status === 'redeemed') {
            return redirect()->route('my.tickets')
                ->with('warning', 'Tiket ini telah digunakan untuk memasuki venue. Anda tidak dapat lagi melihat detail tiket.');
        }
        
        // Jika status pembayaran masih pending, redirect ke halaman pembayaran
        if ($order->status !== 'paid') {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Tiket hanya dapat diakses setelah pembayaran berhasil. Silahkan selesaikan pembayaran Anda.');
        }

        // Generate the QR code
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($order->qr_code);

        return view('orders.ticket_detail', [
            'order' => $order,
            'qrCode' => $qrCode,
        ]);
    }
    
    public function purchaseHistory(Request $request)
    {
        $user = Auth::user();
        
        $ordersQuery = Order::where('user_id', $user->id)
            ->with(['game', 'ticket']);
            
        // Filter berdasarkan parameter
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'upcoming':
                    $ordersQuery->whereHas('game', function($q) {
                        $q->where('match_time', '>', Carbon::now());
                    });
                    // Hanya tampilkan tiket yang sudah dibayar untuk pertandingan yang akan datang
                    $ordersQuery->where('status', 'paid');
                    break;
                case 'past':
                    $ordersQuery->whereHas('game', function($q) {
                        $q->where('match_time', '<', Carbon::now());
                    });
                    break;
                case 'paid':
                    $ordersQuery->where('status', 'paid');
                    break;
                case 'pending':
                    $ordersQuery->where('status', 'pending');
                    break;
            }
        }
        
        $orders = $ordersQuery->orderBy('created_at', 'desc')->paginate(10);
        
        // Statistik pembelian
        $totalOrders = Order::where('user_id', $user->id)->count();
        $totalTickets = Order::where('user_id', $user->id)
            ->where('status', 'paid')
            ->sum('quantity');
        $upcomingMatches = Order::where('user_id', $user->id)
            ->where('status', 'paid')
            ->whereHas('game', function($q) {
                $q->where('match_time', '>', Carbon::now());
            })
            ->distinct('game_id')
            ->count();
        
        return view('orders.history', compact('orders', 'totalOrders', 'totalTickets', 'upcomingMatches'));
    }
    
    /**
     * Download tiket dalam format PDF
     */
    public function downloadTicketPdf($id)
    {
        $order = Order::findOrFail($id);

        // Cek otorisasi - hanya user pemilik order yang bisa melihat
        if (Auth::id() !== $order->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Jika status pembayaran masih pending, redirect ke halaman pembayaran
        if ($order->status !== 'paid') {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Tiket hanya dapat diakses setelah pembayaran berhasil. Silahkan selesaikan pembayaran Anda.');
        }

        // Generate PDF
        $ticketPdfService = app(\App\Services\TicketPdfService::class);
        $pdf = $ticketPdfService->generatePdf($order);
        
        // Generate filename
        $filename = 'ticket-' . $order->id . '-' . Str::slug($order->game->home_team . '-vs-' . $order->game->away_team) . '.pdf';
        
        // Stream or download PDF
        return $pdf->download($filename);
    }
}

