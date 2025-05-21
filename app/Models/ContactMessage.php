<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'order_id',
        'message',
        'status',
        'admin_notes',
        'admin_id',
    ];

    /**
     * Get the order associated with this message if any.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the admin who responded to this message if any.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the formatted subject type.
     */
    public function getSubjectTypeAttribute()
    {
        $types = [
            'general' => 'Pertanyaan Umum',
            'payment' => 'Masalah Pembayaran',
            'ticket' => 'Masalah Tiket',
            'account' => 'Akun',
            'other' => 'Lainnya'
        ];
        
        return $types[$this->subject] ?? ucfirst($this->subject);
    }
}
