<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolderName extends Model
{
    use HasFactory;

    protected $table = 'folder_name';
    protected $primaryKey = 'folder_name_id';

    protected $fillable = [
        'user_login_id',
        'folder_name',
        'main_folder_name',
    ];

    public function coursesFiles()
    {
        return $this->hasMany(CoursesFile::class, 'folder_name_id');
    }

    public function folderInputs()
    {
        return $this->hasMany(FolderInput::class, 'folder_name_id');
    }
}
