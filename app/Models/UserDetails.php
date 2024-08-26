<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    use HasFactory;

    protected $table = 'user_details';
    protected $primaryKey = 'user_details_id';

    protected $fillable = [
        'user_login_id',
        'first_name',
        'last_name',
        'phone_number',
    ];

    public function userLogin()
    {
        return $this->belongsTo(UserLogin::class, 'user_login_id');
    }
}
