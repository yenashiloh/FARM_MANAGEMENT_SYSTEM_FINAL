<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestUploadAccess extends Model
{
    use HasFactory;

    protected $table = 'request_upload_access'; 
    protected $fillable = ['user_login_id', 'reason', 'status'];

    public function user()
    {
        return $this->belongsTo(UserLogin::class, 'user_login_id');
    }
}
