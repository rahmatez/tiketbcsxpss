<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'ticket_id',
        'quantity',
        'payment_method',
        'payment_status',
        'qr_code',
        'status',
        'payment_token',
        'midtrans_order_id'
    ];

    // Relasi ke model User, Game dan Ticket
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
    
    public function scans()
    {
        return $this->hasMany(TicketScan::class);
    }
    
    // Scope untuk mencari berdasarkan status
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
    
    public function scopeRedeemed($query)
    {
        return $query->where('status', 'redeemed');
    }
}
