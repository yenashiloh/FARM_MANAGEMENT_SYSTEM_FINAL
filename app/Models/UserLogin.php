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
        'Fcode',
        'faculty_id',    
        'faculty_code',   
        'surname',           
        'first_name',
        'middle_name',
        'name_extension',
        'employment_type',
        'department',
        'email',
        'password',
        'role',
        'department_id',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    // Get full name accessor
    public function getFullNameAttribute()
    {
        $name = $this->first_name;
        
        if (!empty($this->middle_name)) {
            $name .= ' ' . substr($this->middle_name, 0, 1) . '.';
        }
        
        $name .= ' ' . $this->surname;
        
        if (!empty($this->name_extension)) {
            $name .= ' ' . $this->name_extension;
        }
        
        return $name;
    }
    
     public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }
    
    public function coursesFiles()
    {
        return $this->hasMany(CoursesFile::class, 'user_login_id');
    }
    
    public function userDetails()
    {
        return $this->hasOne(UserDetails::class, 'user_login_id');
    }
    
    public function courseSchedules()
    {
        return $this->hasMany(CourseSchedule::class, 'user_login_id');
    }
    
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}