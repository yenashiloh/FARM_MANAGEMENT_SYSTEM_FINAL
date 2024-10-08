<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department; 

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            'College of Engineering',
            'College of Education',
            'College of Accountant',
            'College of Business Administration',
            'College of Information Technology',
            'College of Office Administration',
            'College of Psychology'
        ];

        foreach ($departments as $department) {
            Department::create(['name' => $department]);
        }
    }
}
