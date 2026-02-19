<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password', 'email_verified_at', 'status'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status'            => 'boolean',
    ];

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = \Hash($value);
    }
}
