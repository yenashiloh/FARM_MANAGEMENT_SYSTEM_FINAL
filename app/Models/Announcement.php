<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_announcement';

    protected $fillable = [
        'subject',
        'message',
        'type_of_recepient',
        'published'
    ];

    public function userLogin()
    {
        return $this->belongsTo(UserLogin::class, 'user_login_id');
    }

}
