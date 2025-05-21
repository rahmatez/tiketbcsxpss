<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'category',
        'quantity',
        'price'
    ];
    
    /**
     * Get the game that owns the ticket
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
