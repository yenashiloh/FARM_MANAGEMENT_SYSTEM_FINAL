<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadSchedule extends Model
{
    use HasFactory;

    protected $table = 'upload_schedule';
    protected $primaryKey = 'id';

    protected $fillable = [
        'start_date',
        'end_date',
        'start_time',
        'stop_time',
    ];

    protected $dates = [
        'start_date',
        'end_date',
    ];

    public $timestamps = true; 
}