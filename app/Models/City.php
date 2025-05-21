<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    
    protected $fillable = ['province_id', 'name', 'type', 'postal_code'];
    
    /**
     * Get the province that owns the city.
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
    
    /**
     * Get the users that belong to the city.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
