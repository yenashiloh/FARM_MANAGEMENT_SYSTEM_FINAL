<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';
    protected $primaryKey = 'messages_id';

    protected $fillable = [
        'user_login_id',
        'courses_files_id',
        'folder_name_id',
        'message_body',
    ];

    public function userLogin()
    {
        return $this->belongsTo(UserLogin::class, 'user_login_id');
    }

    public function coursesFile()
    {
        return $this->belongsTo(CoursesFile::class, 'courses_files_id');
    }

    public function folderName()
    {
        return $this->belongsTo(FolderName::class, 'folder_name_id');
    }
}
