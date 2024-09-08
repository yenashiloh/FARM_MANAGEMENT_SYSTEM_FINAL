<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSchedule extends Model
{
    use HasFactory;

    protected $table = 'course_schedules';

    protected $primaryKey = 'course_schedule_id';

    public $incrementing = false;

    protected $keyType = 'unsignedBigInteger';

    protected $fillable = [
        'course_schedule_id',
        'user_login_id',
        'sem_academic_year',
        'program',
        'course_code',
        'course_subjects',
        'year_section',
        'schedule',
    ];

    public function userLogin()
    {
        return $this->belongsTo(UserLogin::class, 'user_login_id');
    }

    public function coursesFiles()
    {
        return $this->hasMany(CoursesFile::class, 'course_schedule_id', 'course_schedule_id');
    }
}
