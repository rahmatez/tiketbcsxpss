<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Game;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Admin;
use App\Models\TicketScan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return redirect()->back()->withErrors(['login' => 'Email atau password salah']);
    }    public function dashboard()
    {
        // Get total games
        $totalGames = Game::count();
        $upcomingGames = Game::where('match_time', '>', now())->count();
          // Get ticket statistics
        $totalTickets = Ticket::sum('quantity');
          // Get current and last month sold tickets
        $now = Carbon::now();
        $soldTickets = Order::whereIn('status', ['paid', 'redeemed'])->sum('quantity');
          // Get sold tickets from last month
        $lastMonthSoldTickets = Order::whereIn('status', ['paid', 'redeemed'])
            ->whereMonth('created_at', $now->subMonth()->month)
            ->whereYear('created_at', $now->year)
            ->sum('quantity');
            
        // Calculate growth percentage
        $ticketGrowthPercentage = 0;
        if ($lastMonthSoldTickets > 0) {
            $ticketGrowthPercentage = (($soldTickets - $lastMonthSoldTickets) / $lastMonthSoldTickets) * 100;
        }
          // Get current month's revenue
        $ticketSalesAmount = Order::whereIn('status', ['paid', 'redeemed'])
            ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
            ->selectRaw('SUM(tickets.price * orders.quantity) as total_sales')
            ->first()->total_sales ?? 0;
              // Get last month's revenue
        $lastMonthSalesAmount = Order::whereIn('status', ['paid', 'redeemed'])
            ->whereMonth('orders.created_at', $now->subMonth()->month)
            ->whereYear('orders.created_at', $now->year)
            ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
            ->selectRaw('SUM(tickets.price * orders.quantity) as total_sales')
            ->first()->total_sales ?? 0;
            
        // Calculate revenue growth percentage
        $revenueGrowthPercentage = 0;
        if ($lastMonthSalesAmount > 0) {
            $revenueGrowthPercentage = (($ticketSalesAmount - $lastMonthSalesAmount) / $lastMonthSalesAmount) * 100;
        }
            
        // Get order statistics
        $totalOrders = Order::count();
        $redeemedTickets = Order::where('status', 'redeemed')->sum('quantity');
        
        // Get popular games based on ticket sales
        $popularGames = Game::join('tickets', 'games.id', '=', 'tickets.game_id')
            ->join('orders', 'tickets.id', '=', 'orders.ticket_id')
            ->where('orders.status', 'paid')
            ->select('games.id', 'games.home_team', 'games.away_team', DB::raw('SUM(orders.quantity) as tickets_sold'))
            ->groupBy('games.id', 'games.home_team', 'games.away_team')
            ->orderByDesc('tickets_sold')
            ->limit(5)
            ->get();
        $totalOrders = Order::count();
        $redeemedTickets = Order::where('status', 'redeemed')->sum('quantity');
        
        // Get popular game (most tickets sold)
        $popularGame = Game::join('orders', 'games.id', '=', 'orders.game_id')
            ->selectRaw('games.*, COUNT(orders.id) as order_count')
            ->groupBy('games.id')
            ->orderBy('order_count', 'desc')
            ->first();
            
        // Recent orders
        $recentOrders = Order::with(['user', 'game', 'ticket'])
            ->latest()
            ->take(5)
            ->get();
              return view('admin.dashboard', compact(
            'totalGames', 
            'upcomingGames', 
            'totalTickets', 
            'soldTickets',
            'ticketSalesAmount',
            'totalOrders',
            'redeemedTickets',
            'popularGame',
            'recentOrders',
            'ticketGrowthPercentage',
            'revenueGrowthPercentage'
        ));
    }
      // Game Management (handled by GameController)
    
    // Ticket Management
    public function ticketIndex(Request $request)
    {
        // Get all games for filter dropdown
        $gamesQuery = Game::query()->orderBy('match_time', 'desc');
        
        // Build the ticket query
        $ticketQuery = Ticket::with('game');
        
        // Apply filters to tickets query if provided
        if ($request->filled('game_id')) {
            $ticketQuery->where('game_id', $request->game_id);
            $gamesQuery->where('id', $request->game_id);
        }
        
        if ($request->filled('category')) {
            $ticketQuery->where('category', $request->category);
        }
        
        if ($request->filled('price_min')) {
            $ticketQuery->where('price', '>=', $request->price_min);
        }
        
        // Get all games for dropdown
        $dropdownGames = Game::orderBy('match_time', 'desc')->get();
        
        // Get the filtered games with their tickets
        $games = $gamesQuery->with(['tickets' => function($query) use ($request) {
            // Apply the same filters to the tickets relationship
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }
            if ($request->filled('price_min')) {
                $query->where('price', '>=', $request->price_min);
            }
        }])->get();
        
        // For pagination, we still need the original tickets query
        $tickets = $ticketQuery->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        return view('admin.tickets.index', compact('tickets', 'games', 'dropdownGames'));
    }
    
    public function ticketEdit($id)
    {
        $ticket = Ticket::with('game')->findOrFail($id);
        
        return view('admin.tickets.edit', compact('ticket'));
    }
    
    public function ticketUpdate(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0'
        ]);
        
        $ticket = Ticket::findOrFail($id);
        
        // Check if requested quantity is less than the number of tickets already sold
        $soldTickets = Order::where('ticket_id', $id)->sum('quantity');
        if ($request->quantity < $soldTickets) {
            return redirect()->back()->withErrors(['quantity' => 'Kuantitas tidak dapat kurang dari jumlah tiket yang sudah terjual: ' . $soldTickets]);
        }
        
        $ticket->price = $request->price;
        $ticket->quantity = $request->quantity;
        $ticket->save();
        
        return redirect()->route('admin.tickets.index')->with('success', 'Tiket berhasil diperbarui');
    }
    
    // Order Management
    public function orderIndex(Request $request)
    {
        $query = Order::with(['user', 'game', 'ticket']);
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by game
        if ($request->has('game_id') && $request->game_id != '') {
            $query->where('game_id', $request->game_id);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $orders = $query->latest()->paginate(20);
        $games = Game::all();
        
        return view('admin.orders.index', compact('orders', 'games'));
    }
    
    public function orderShow($id)
    {
        $order = Order::with(['user', 'game', 'ticket'])->findOrFail($id);
        $scans = TicketScan::where('order_id', $id)->latest()->get();
        
        return view('admin.orders.show', compact('order', 'scans'));
    }
    
    public function orderUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:paid,redeemed'
        ]);
        
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();
          return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui');
    }
    
    // User Management
    public function userIndex(Request $request)
    {
        $query = User::with(['province', 'city']);
        
        // Search by name or email
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('is_active') && $request->is_active != '') {
            $query->where('is_active', $request->is_active);
        }
        
        $users = $query->latest()->paginate(20);
        
        return view('admin.users.index', compact('users'));    }
    
    public function userShow($id)
    {
        $user = User::with(['province', 'city'])->findOrFail($id);
        $orders = Order::where('user_id', $id)
            ->with(['game', 'ticket'])
            ->latest()
            ->get();
            
        return view('admin.users.show', compact('user', 'orders'));    }
    
    public function userUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);
        
        $user = User::findOrFail($id);
        $user->is_active = $request->is_active;
        $user->save();
        
        $status = $request->is_active ? 'aktif' : 'nonaktif';
        
        return redirect()->back()->with('success', "Status pengguna berhasil diubah menjadi {$status}");
    }
    
    /**
     * Show form for creating a new user
     */
    public function userCreate()
    {
        return view('admin.users.create');
    }
    
    /**
     * Store a newly created user     */    
    public function userStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'province_id' => 'nullable|exists:provinces,id',
            'city_id' => 'nullable|exists:cities,id',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'is_active' => 'boolean',        ]);
        
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone_number = $request->phone_number;
        $user->address = $request->address;
        $user->province_id = $request->province_id;
        $user->city_id = $request->city_id;
        $user->birth_date = $request->birth_date;
        $user->gender = $request->gender;
        $user->is_active = $request->has('is_active') ? 1 : 0;
        $user->save();
        
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan');
    }
    
    /**
     * Show form for editing a user
     */
    public function userEdit($id)
    {
        $user = User::with(['province', 'city'])->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }
    
    /**
     * Update user data     */
    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'province_id' => 'nullable|exists:provinces,id',
            'city_id' => 'nullable|exists:cities,id',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8|confirmed',        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->address = $request->address;
        $user->province_id = $request->province_id;
        $user->city_id = $request->city_id;
        $user->birth_date = $request->birth_date;
        $user->gender = $request->gender;
        $user->is_active = $request->has('is_active') ? 1 : 0;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui');
    }
    
    /**
     * Delete user
     */
    public function userDestroy($id)
    {
        $user = User::findOrFail($id);
        
        // Cek apakah user memiliki pesanan
        $orderCount = Order::where('user_id', $id)->count();
        
        if ($orderCount > 0) {
            return redirect()->route('admin.users.index')->with('error', 'Pengguna tidak dapat dihapus karena memiliki ' . $orderCount . ' pesanan terkait');
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus');
    }
    
    // QR Code Scanning
    public function showScanForm()
    {
        return view('admin.scan');
    }    public function updateOrderStatus(Request $request)
    {
        try {
            // Debug: log bahwa function dipanggil
            Log::info('updateOrderStatus called', [
                'request_data' => $request->all(),
                'is_admin_logged_in' => Auth::guard('admin')->check(),
                'auth_id' => Auth::guard('admin')->id(),
                'ip' => $request->ip(),
                'url' => $request->url(),
                'path' => $request->path()
            ]);
            
            // Start a database transaction to prevent race conditions
            DB::beginTransaction();
            
            // Validasi input
            $request->validate([
                'qr_code' => 'required|string'
            ]);
            
            $qrCode = $request->input('qr_code');
            
            // Validate QR code isn't empty after trimming
            if (empty(trim($qrCode))) {
                Log::warning('Empty QR code data received');
                return response()->json([
                    'success' => false, 
                    'message' => 'Data QR code kosong'
                ], 422);
            }            // Log input untuk debugging
            Log::info('QR Code scanned', [
                'qr_code' => $qrCode,
                'admin_id' => Auth::guard('admin')->id(),
                'ip_address' => $request->ip()
            ]);
            
            $adminId = Auth::guard('admin')->id();
            if (!$adminId) {
                Log::warning('Admin tidak terautentikasi');
                return response()->json([
                    'success' => false, 
                    'message' => 'Admin tidak terautentikasi.'
                ], 401);
            }

            // Format QR Code adalah order_id-hash
            // Coba ambil order_id dari QR Code jika format adalah ID-HASH
            $order = null;
            if (strpos($qrCode, '-') !== false) {
                $parts = explode('-', $qrCode);
                if (count($parts) < 2) {
                    Log::warning('Invalid QR Code format', ['qr_code' => $qrCode]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Format QR Code tidak valid.'
                    ], 400);
                }
                
                $orderId = $parts[0];
                
                // Validasi order ID adalah angka
                if (!is_numeric($orderId)) {
                    Log::warning('Invalid order ID (not numeric)', ['order_id' => $orderId]);
                    return response()->json([
                        'success' => false,
                        'message' => 'ID pesanan tidak valid.'
                    ], 400);
                }
                
                // Eager load relasi untuk menghindari N+1 query
                $order = Order::with(['user', 'game', 'ticket'])->find($orderId);
                
                // Validasi apakah hash sesuai dengan yang diharapkan
                if ($order) {
                    // Pastikan order memiliki relasi user
                    if (!$order->user) {
                        Log::warning('Order found but user relation is missing', ['order_id' => $order->id]);
                        return response()->json([
                            'success' => false, 
                            'message' => 'Data pengguna tidak ditemukan untuk tiket ini.'
                        ], 404);
                    }
                    
                    try {
                        $expectedHash = substr(md5($order->user->email . $order->id), 0, 8);
                        Log::info('Hash validation', [
                            'expected' => $expectedHash,
                            'received' => $parts[1],
                            'order_id' => $order->id
                        ]);
                        
                        if ($parts[1] !== $expectedHash) {
                            Log::warning('Hash mismatch', [
                                'expected' => $expectedHash,
                                'received' => $parts[1],
                                'order_id' => $order->id
                            ]);
                            return response()->json([
                                'success' => false,
                                'message' => 'QR Code tidak valid. Hash tidak sesuai.'
                            ], 400);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error validating hash: ' . $e->getMessage(), ['order_id' => $order->id]);
                        return response()->json([
                            'success' => false,
                            'message' => 'Terjadi kesalahan saat validasi keamanan tiket.'
                        ], 500);
                    }
                } else {
                    Log::warning('Order not found', ['order_id' => $orderId]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Pesanan tidak ditemukan.'
                    ], 404);
                }
            } else {
                // Format lama, coba cari di kolom qr_code
                Log::info('Using legacy QR format', ['qr_code' => $qrCode]);
                $order = Order::with(['user', 'game', 'ticket'])->where('qr_code', $qrCode)->first();
                
                if (!$order) {
                    Log::warning('Order not found (legacy format)', ['qr_code' => $qrCode]);
                    return response()->json([
                        'success' => false, 
                        'message' => 'QR Code tidak valid. Pastikan tiket yang dipindai adalah tiket resmi Pundit FC.'
                    ], 404);
                }
                
                if (!$order->user) {
                    Log::warning('Order found but user relation is missing (legacy format)', ['order_id' => $order->id]);
                    return response()->json([
                        'success' => false, 
                        'message' => 'Data pengguna tidak ditemukan untuk tiket ini.'
                    ], 404);                }            }        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Data QR code tidak valid: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error processing QR: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
              // Return a more user-friendly error without technical details
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan saat memproses tiket. Silakan coba lagi.'
            ], 500);
        }
        
        // Validasi game dan ticket pada order
        if (!$order->game) {            // Rollback transaction
            DB::rollback();
            
            Log::warning('Game relation is missing', ['order_id' => $order->id]);
            return response()->json([
                'success' => false, 
                'message' => 'Data pertandingan tidak ditemukan untuk tiket ini.'
            ], 404);
        }
        
        if (!$order->ticket) {            // Rollback transaction
            DB::rollback();
            
            Log::warning('Ticket relation is missing', ['order_id' => $order->id]);
            return response()->json([
                'success' => false, 
                'message' => 'Data tiket tidak ditemukan.'
            ], 404);
        }
        
        // Cek apakah pertandingan sudah lewat
        if (Carbon::parse($order->game->match_time)->isPast() && Carbon::now()->diffInHours(Carbon::parse($order->game->match_time)) > 5) {
            Log::info('Game already finished', [
                'order_id' => $order->id, 
                'game_id' => $order->game->id,
                'match_time' => $order->game->match_time
            ]);
              // Rollback transaction
            DB::commit();
              TicketScan::create([
                'order_id' => $order->id,
                'admin_id' => $adminId,
                'status' => 'failed',
                'notes' => 'Pertandingan sudah selesai',
                'scanned_at' => now()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Pertandingan sudah selesai. Tiket tidak lagi berlaku.',
                'order' => $order,
                'user' => $order->user,
                'game' => $order->game,
                'ticket' => $order->ticket
            ]);
        }          // Cek apakah tiket sudah digunakan - PRIORITASKAN PENGECEKAN INI
        if ($order->status == 'redeemed') {
            // We can commit early since this is a read-only operation
            DB::commit();
            
            Log::info('Ticket already used', ['order_id' => $order->id]);
              // Catat upaya pemindaian
            TicketScan::create([
                'order_id' => $order->id,
                'admin_id' => $adminId,
                'status' => 'failed',
                'notes' => 'Tiket sudah digunakan sebelumnya',
                'scanned_at' => now()
            ]);
            
            // Ambil data scan sukses terakhir
            $lastScan = $order->scans()
                ->where('status', 'success')
                ->latest()
                ->first();
            
            $redeemedAt = $lastScan 
                ? $lastScan->created_at->format('d-m-Y H:i:s') 
                : $order->updated_at->format('d-m-Y H:i:s');
                
            Log::warning('Attempted reuse of already redeemed ticket', [
                'order_id' => $order->id, 
                'redeemed_at' => $redeemedAt,
                'admin_id' => $adminId,
                'ip' => request()->ip()
            ]);
                
            return response()->json([
                'success' => false, 
                'message' => 'Tiket sudah digunakan sebelumnya. Akses ditolak.',
                'order' => $order,
                'user' => $order->user,
                'game' => $order->game,
                'ticket' => $order->ticket,
                'redeemed_at' => $redeemedAt
            ]);
        }
        
        // Cek apakah tiket sudah dibayar
        if ($order->status != 'paid') {
            Log::info('Ticket not paid', ['order_id' => $order->id, 'status' => $order->status]);
              // Rollback transaction
            DB::commit();
              TicketScan::create([
                'order_id' => $order->id,
                'admin_id' => $adminId,
                'status' => 'failed',
                'notes' => 'Tiket belum dibayar, status: ' . $order->status,
                'scanned_at' => now()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Tiket belum dibayar. Status: ' . ucfirst($order->status),
                'order' => $order,
                'user' => $order->user,
                'game' => $order->game,
                'ticket' => $order->ticket
            ]);
        }        try {            // Update status pesanan
            Log::info('Validating ticket', ['order_id' => $order->id, 'current_status' => $order->status, 'admin_id' => $adminId]);
            $order->status = 'redeemed';
            $order->save();
            // Catat pemindaian berhasil
            TicketScan::create([
                'order_id' => $order->id,
                'admin_id' => $adminId,
                'status' => 'success',
                'notes' => 'Tiket berhasil divalidasi',
                'scanned_at' => now()
            ]);
            
            // Commit the transaction
            DB::commit();
            
            Log::info('Ticket validated successfully', ['order_id' => $order->id]);

            return response()->json([
                'success' => true,
                'message' => 'Tiket valid! Penonton diizinkan masuk.',
                'order' => $order,
                'user' => $order->user,
                'game' => $order->game,
                'ticket' => $order->ticket,
                'ticket_info' => [
                    'user_name' => $order->user->name,
                    'game_name' => $order->game->home_team . ' vs ' . $order->game->away_team,
                    'ticket_category' => $order->ticket->category,
                    'quantity' => $order->quantity
                ]
            ]);        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollback();
            
            Log::error('Error updating order status: ' . $e->getMessage(), [
                'order_id' => $order->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memvalidasi tiket: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function scanHistory(Request $request)
    {
        // Get all successful scans
        $successQuery = TicketScan::with(['order.user', 'order.game', 'admin'])
            ->where('status', 'success');
        
        // Get only the latest failed scan for each order_id to prevent duplicates
        $failedScansSubquery = TicketScan::where('status', 'failed')
            ->select(DB::raw('MAX(id) as id'))
            ->groupBy('order_id');
            
        $failedQuery = TicketScan::with(['order.user', 'order.game', 'admin'])
            ->whereIn('id', $failedScansSubquery);
        
        // Combine both queries
        $query = $successQuery->union($failedQuery);
        
        // Apply filters
        if ($request->has('status') && !empty($request->status)) {
            $query = TicketScan::with(['order.user', 'order.game', 'admin'])
                ->where('status', $request->status);
            
            if ($request->status === 'failed') {
                $query = TicketScan::with(['order.user', 'order.game', 'admin'])
                    ->whereIn('id', $failedScansSubquery);
            }
        }
        
        // Filter berdasarkan tanggal
        if ($request->has('date') && !empty($request->date)) {
            $date = Carbon::parse($request->date);
            $query = TicketScan::with(['order.user', 'order.game', 'admin'])
                ->whereIn('id', function($query) use ($date) {
                    $query->select('id')
                        ->from('ticket_scans')
                        ->whereDate('created_at', $date);
                });
        }
        
        $scans = $query->latest()->paginate(20);
        
        return view('admin.scan_history', compact('scans'));    }
    
    public function logScan(Request $request)
    {
        // Validasi input
        $request->validate([
            'order_id' => 'required|integer',
            'status' => 'required|in:success,failed',
            'notes' => 'nullable|string',
        ]);
        
        try {
            // Pastikan admin ID tersedia
            $adminId = Auth::guard('admin')->id();
            if (!$adminId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin tidak terautentikasi'
                ], 401);
            }
            
            // Verifikasi order_id valid jika bukan 0
            if ($request->order_id > 0) {
                $order = Order::find($request->order_id);
                if (!$order) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Order tidak ditemukan'
                    ], 404);
                }
            }
              // Simpan data pemindaian
            $ticketScan = TicketScan::create([
                'order_id' => $request->order_id,
                'admin_id' => $adminId,
                'status' => $request->status,
                'notes' => $request->notes ?? '',
                'scanned_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Scan berhasil dicatat',
                'scan_id' => $ticketScan->id
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat mencatat scan tiket: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat pemindaian: ' . $e->getMessage()
            ], 500);
        }
    }
      /**
     * Check if a ticket has already been redeemed
     */
    public function checkTicketStatus(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'qr_code' => 'required|string'
            ]);
            
            $qrCode = $request->input('qr_code');
            
            // Parse QR code to get order ID
            $parts = explode('-', $qrCode);
            if (count($parts) < 2 || !is_numeric($parts[0])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format QR code tidak valid'
                ]);
            }
            
            $orderId = $parts[0];
            $order = Order::with(['user', 'game', 'ticket', 'scans'])->find($orderId);
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket tidak ditemukan'
                ]);
            }
            
            // Return the status and details if the ticket is redeemed
            $isRedeemed = $order->status === 'redeemed';
            
            // Get last successful scan timestamp
            $redeemedAt = null;
            if ($isRedeemed) {
                $lastScan = $order->scans()
                    ->where('status', 'success')
                    ->latest()
                    ->first();
                
                $redeemedAt = $lastScan 
                    ? $lastScan->created_at->format('d-m-Y H:i:s') 
                    : $order->updated_at->format('d-m-Y H:i:s');
            }
            
            return response()->json([
                'success' => true,
                'status' => $order->status,
                'order' => $order,
                'user' => $order->user,
                'game' => $order->game,
                'ticket' => $order->ticket,
                'redeemed_at' => $redeemedAt
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking ticket status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memeriksa status tiket'
            ]);
        }
    }
    
    // Reports
    public function salesReport(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        
        // Sales by date
        $salesByDate = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(quantity) as tickets_sold'), DB::raw('COUNT(id) as orders_count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Sales by ticket category
        $salesByCategory = Order::join('tickets', 'orders.ticket_id', '=', 'tickets.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('tickets.category', DB::raw('SUM(orders.quantity) as tickets_sold'), DB::raw('SUM(tickets.price * orders.quantity) as revenue'))
            ->groupBy('tickets.category')
            ->orderBy('revenue', 'desc')
            ->get();
            
        // Sales by game
        $salesByGame = Order::join('games', 'orders.game_id', '=', 'games.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('games.id', 'games.home_team', 'games.away_team', 'games.match_time',
                    DB::raw('SUM(orders.quantity) as tickets_sold'), DB::raw('COUNT(DISTINCT orders.user_id) as unique_customers'))
            ->groupBy('games.id', 'games.home_team', 'games.away_team', 'games.match_time')
            ->orderBy('tickets_sold', 'desc')
            ->get();
        
        return view('admin.reports.sales', compact('salesByDate', 'salesByCategory', 'salesByGame', 'startDate', 'endDate'));
    }
      public function attendanceReport(Request $request)
    {        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonths(3);
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->addMonths(3)->endOfDay();
        
        // Get games with attendance stats - fetch all games if no specific range selected
        $gamesQuery = Game::withCount([
            'ticketScans' => function ($query) {
                $query->where('ticket_scans.status', 'success'); // Only count successful scans
            }, 
            'orders as tickets_sold' => function ($query) {
                $query->whereIn('orders.status', ['paid', 'redeemed']); // Count both paid and redeemed tickets as sold
            }
        ]);
        
        // Apply date filter only if explicitly provided in request
        if ($request->has('start_date') || $request->has('end_date')) {
            $gamesQuery->whereBetween('match_time', [$startDate, $endDate]);
        }
        
        $games = $gamesQuery->orderBy('match_time', 'desc')->get();
          // Attendance by category
        $attendanceByCategory = TicketScan::join('orders', 'ticket_scans.order_id', '=', 'orders.id')
            ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
            ->whereBetween('ticket_scans.created_at', [$startDate, $endDate])
            ->where('ticket_scans.status', 'success') // Only count successful scans
            ->select('tickets.category', DB::raw('COUNT(ticket_scans.id) as scan_count'))
            ->groupBy('tickets.category')
            ->orderBy('scan_count', 'desc')
            ->get();          // Attendance by time (hourly distribution)
        $attendanceByTime = TicketScan::whereBetween('ticket_scans.created_at', [$startDate, $endDate])
            ->where('ticket_scans.status', 'success') // Only count successful scans
            ->select(DB::raw('HOUR(ticket_scans.created_at) as hour'), DB::raw('COUNT(ticket_scans.id) as scan_count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
            
        // Format for chart
        $hourLabels = [];
        $hourData = array_fill(0, 24, 0); // Initialize with 0 for all 24 hours
        
        foreach($attendanceByTime as $item) {
            $hourData[$item->hour] = $item->scan_count;
        }
          for($i = 0; $i < 24; $i++) {
            $hourLabels[] = sprintf("%02d:00", $i);
        }
          // Check if we need to generate sample data for empty results - only if explicitly requested
        if (($games->isEmpty() && $request->has('generate_sample')) || $request->has('sample')) {
            $this->generateSampleAttendanceData($games, $attendanceByCategory, $hourData);
        }
        
        return view('admin.reports.attendance', compact('games', 'attendanceByCategory', 'hourLabels', 'hourData', 'startDate', 'endDate'));
    }
    
    public function ticketReport(Request $request)
    {
        $gameId = $request->game_id;
        
        if ($gameId) {
            $game = Game::findOrFail($gameId);
            
            $ticketStats = Ticket::where('game_id', $gameId)
                ->select('category', 'quantity', 'price')
                ->get()
                ->map(function($ticket) use ($gameId) {
                    $sold = Order::where('ticket_id', $ticket->id)->sum('quantity');
                    $available = $ticket->quantity - $sold;
                    $revenue = $sold * $ticket->price;
                    
                    return [
                        'category' => $ticket->category,
                        'total' => $ticket->quantity,
                        'sold' => $sold,
                        'available' => $available,
                        'price' => $ticket->price,
                        'revenue' => $revenue
                    ];
                });
                
            // Get redemption stats
            $redemptionStats = Order::where('game_id', $gameId)
                ->select('status', DB::raw('SUM(quantity) as count'))
                ->groupBy('status')
                ->pluck('count', 'status');
                
            return view('admin.reports.tickets', compact('game', 'ticketStats', 'redemptionStats'));
            
        } else {
            $games = Game::where('is_home_game', 1)->orderBy('match_time', 'desc')->get();
            return view('admin.reports.select_game', compact('games'));
        }
    }
    
    public function exportReport(Request $request, $type)
    {
        // To be implemented - would generate CSV exports for different reports
        return redirect()->back()->with('info', 'Export functionality will be implemented soon.');
    }
    
    // Admin management
    public function adminIndex()
    {
        $admins = Admin::latest()->paginate(15);
        return view('admin.admins.index', compact('admins'));
    }
    
    public function adminCreate()
    {
        return view('admin.admins.create');
    }
    
    public function adminStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        // Buat admin baru menggunakan query builder
        DB::table('admins')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin', // Default role
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return redirect()->route('admin.admins.index')->with('success', 'Admin berhasil ditambahkan');
    }
    
    public function profile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', ['admin' => $admin]);
    }
    
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $request->validate([
            'email' => 'required|string|email|max:255|unique:admins,email,'.$admin->id,
            'name' => 'required|string|max:255',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);
        
        // Siapkan data untuk diupdate
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        // Update password jika diinput
        if ($request->filled('password')) {
            // Verifikasi password saat ini
            if (!Hash::check($request->current_password, $admin->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak valid']);
            }
            
            $updateData['password'] = Hash::make($request->password);
        }
        
        // Update menggunakan query builder
        DB::table('admins')->where('id', $admin->id)->update($updateData);
        
        return redirect()->route('admin.profile')->with('success', 'Profil berhasil diperbarui');
    }

    public function settings()
    {
        // Ambil pengaturan sistem dari database atau file konfigurasi
        // Contoh sederhana, Anda bisa menyesuaikan dengan kebutuhan
        $settings = [
            'site_name' => config('app.name'),
            'ticket_expiry' => config('app.ticket_expiry', 24), // jam
            'notification_email' => config('mail.from.address'),
        ];
        
        return view('admin.settings', ['settings' => $settings]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:100',
            'ticket_expiry' => 'required|integer|min:1',
            'notification_email' => 'required|email',
        ]);
        
        // Di sini Anda bisa menyimpan pengaturan ke database atau file konfigurasi
        // Contoh sederhana, implementasi sebenarnya bisa berbeda
        
        // Update file .env untuk contoh sederhana (tidak disarankan untuk produksi)
        // Sebaiknya gunakan package seperti spatie/laravel-settings
        
        return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil diperbarui');
    }
    
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Generate sample attendance data for testing when real data is not available
     * 
     * @param \Illuminate\Database\Eloquent\Collection $games Collection of games to populate
     * @param \Illuminate\Database\Eloquent\Collection $attendanceByCategory Collection of category data
     * @param array $hourData Array of hourly attendance data
     * @return void
     */
    private function generateSampleAttendanceData(&$games, &$attendanceByCategory, &$hourData)
    {
        // Clear existing collections if they exist but are empty
        $games = collect();
        
        // Create 3 sample games with attendance data
        for ($i = 1; $i <= 3; $i++) {
            $sampleGame = new \stdClass();
            $sampleGame->id = 1000 + $i;
            $sampleGame->home_team = 'Pundit FC';
            $sampleGame->away_team = 'Lawan ' . $i;
            $sampleGame->match_time = now()->subDays(rand(1, 30))->format('Y-m-d H:i:s');
            $sampleGame->tickets_sold = rand(500, 1500);
            $sampleGame->ticket_scans_count = rand(300, $sampleGame->tickets_sold);
            
            $games->push($sampleGame);
        }
        
        // Sample attendance by category
        $attendanceByCategory = collect();
        $categories = ['VIP', 'Tribune', 'Regular'];
        
        foreach ($categories as $index => $category) {
            $item = new \stdClass();
            $item->category = $category;
            $item->scan_count = rand(50, 300);
            $attendanceByCategory->push($item);
        }
        
        // Sample hourly attendance data
        $peakHours = [16, 17, 18, 19]; // Common peak attendance hours
        
        foreach ($peakHours as $hour) {
            $hourData[$hour] = rand(30, 150);
        }
        
        // Add some data for other hours
        for ($i = 12; $i < 22; $i++) {
            if (!in_array($i, $peakHours)) {
                $hourData[$i] = rand(5, 50);
            }
        }
    }
}

