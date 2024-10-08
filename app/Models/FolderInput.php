<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolderInput extends Model
{
    use HasFactory;

    protected $table = 'folder_inputs';
    protected $primaryKey = 'folder_input_id';

    protected $fillable = [
        'folder_name_id',
        'input_label',
        'input_type',
    ];

    public function folderName()
    {
        return $this->belongsTo(FolderName::class, 'folder_name_id');
    }
}