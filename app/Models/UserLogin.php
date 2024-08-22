<?php

namespace App\Models;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserLogin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'user_login';
    protected $primaryKey = 'user_login_id';

    protected $fillable = [
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    public function coursesFiles()
    {
        return $this->hasMany(CoursesFile::class, 'user_login_id');
    }
}
