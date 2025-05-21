<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone_number', 'address', 'province_id', 'city_id', 'birth_date', 'gender',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];
    
    /**
     * Get the province associated with the user.
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
    
    /**
     * Get the city associated with the user.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    
    /**
     * Get the user's full address.
     */
    public function getFullAddressAttribute()
    {
        $parts = [$this->address];
        
        if ($this->city) {
            $parts[] = $this->city->name;
        }
        
        if ($this->province) {
            $parts[] = $this->province->name;
        }
        
        return implode(', ', array_filter($parts));
    }
    
    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Get the user's notifications.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    
    /**
     * Get the user's unread notifications.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }
    
    /**
     * Create a notification for the user
     *
     * @param string $title The notification title
     * @param string $message The notification message
     * @param string $type Type of notification (match_reminder, payment_reminder, etc)
     * @param int|null $referenceId ID of referenced model
     * @param string|null $referenceType Class of referenced model
     * @param array|null $data Additional data
     * @param \Carbon\Carbon|null $scheduledAt When to send the notification
     * @return \App\Models\Notification
     */
    public function notify($title, $message, $type, $referenceId = null, $referenceType = null, $data = null, $scheduledAt = null)
    {
        return $this->notifications()->create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'data' => $data,
            'scheduled_at' => $scheduledAt,
            'is_read' => false,
            'sent_at' => $scheduledAt ? null : now(),
        ]);
    }
}
