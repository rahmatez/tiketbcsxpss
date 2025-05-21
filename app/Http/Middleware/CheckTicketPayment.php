<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTicketPayment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the order ID from the route parameter
        $orderId = $request->route('id');
        
        if (!$orderId) {
            return redirect()->route('home')
                ->with('error', 'Tiket tidak ditemukan.');
        }
        
        // Find the order
        $order = Order::findOrFail($orderId);
        
        // Check if user owns this order
        if (Auth::id() !== $order->user_id && !Auth::guard('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if payment is completed
        if ($order->status !== 'paid') {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Tiket hanya dapat diakses setelah pembayaran berhasil. Silahkan selesaikan pembayaran Anda.');
        }
        
        return $next($request);
    }
}
