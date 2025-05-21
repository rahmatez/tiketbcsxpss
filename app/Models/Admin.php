<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Models\Admin
 *
 * @method bool save()
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 */
class Admin extends Authenticatable
{
    use HasFactory;

    protected $table = 'admins';
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];
}

