<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderTracking extends Model
{
    protected $table = 'reminder_tracking';
    protected $fillable = ['user_login_id', 'sent_date'];
}