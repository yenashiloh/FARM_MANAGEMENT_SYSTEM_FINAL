<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'notifications';

    protected $fillable = [
        'courses_files_id',
        'user_login_id',
        'folder_name_id',
        'sender',
        'notification_message',
        'is_read',
        'sender_user_login_id',
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
    ];
    
    public function coursesFile()
    {
        return $this->belongsTo(CoursesFile::class, 'courses_files_id', 'courses_files_id');
    }
    
    public function userLogin()
    {
        return $this->belongsTo(UserLogin::class, 'user_login_id');
    }

    public function folderName()
    {
        return $this->belongsTo(FolderName::class, 'folder_name_id');
    }

    
}
