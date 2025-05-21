<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'home_team',
        'home_team_logo',
        'away_team',
        'away_team_logo',
        'match_time',
        'is_home_game',
        'tournament_name',
        'purchase_deadline',
        'stadium_name',
        'status',
        'description',
        'image_path'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_home_game' => 'boolean',
        'match_time' => 'datetime',
        'purchase_deadline' => 'datetime',
    ];
    
    /**
     * Get the tickets for the game
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    
    /**
     * Get the orders for the game
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Get the ticket scans for the game through orders
     */
    public function ticketScans()
    {
        return $this->hasManyThrough(TicketScan::class, Order::class);
    }
    
    /**
     * Check if ticket sales are still open
     *
     * @return bool
     */
    public function isTicketSalesOpen()
    {
        if (!$this->purchase_deadline) {
            return false;
        }
        
        return now()->lt($this->purchase_deadline);
    }
    
    /**
     * For backward compatibility with existing code
     * @deprecated Use isTicketSalesOpen() instead
     */
    public function isPurchaseDurationPassed()
    {
        return !$this->isTicketSalesOpen();
    }
}
