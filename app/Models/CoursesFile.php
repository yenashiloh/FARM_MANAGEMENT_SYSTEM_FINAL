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
        'course_schedule_id',
        'folder_input_id',
        'semester',
        'school_year',
        'original_file_name',
        'subject',
        'status',
        'declined_reason',
        'file_size',
        'is_archived',
      
    ];

    public function userLogin()
    {
        return $this->belongsTo(UserLogin::class, 'user_login_id');
    }

    public function folderName()
    {
        return $this->belongsTo(FolderName::class, 'folder_name_id', 'folder_name_id');
    }

    public function folderInput()
    {
        return $this->belongsTo(FolderInput::class, 'folder_input_id');
    }
    
    public function courseSchedule()
    {
        return $this->belongsTo(CourseSchedule::class, 'course_schedule_id');
    }
    
}

