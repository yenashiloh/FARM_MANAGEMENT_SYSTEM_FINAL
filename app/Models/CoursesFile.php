<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursesFile extends Model
{
    use HasFactory;

    protected $table = 'courses_files';
    protected $primaryKey = 'courses_files_id';
    protected $fillable = [
        'files',
        'user_login_id',
        'folder_name_id',
        'semester',
        'original_file_name',
        'subject',
        'status',
        'declined_reason'
    ];

    public function userLogin()
    {
        return $this->belongsTo(UserLogin::class, 'user_login_id');
    }

    public function folderName()
    {
        return $this->belongsTo(FolderName::class, 'folder_name_id', 'folder_name_id');
    }
    
}

