<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_login_id',
        'login_time',
        'ip_address',
        'user_agent',
        'login_message',
    ];

    public function user()
    {
        return $this->belongsTo(UserLogin::class, 'user_login_id');
    }
}
