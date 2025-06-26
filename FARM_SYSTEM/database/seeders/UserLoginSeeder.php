<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\UserLogin;

class UserLoginSeeder extends Seeder
{
    public function run()
    {
        //admin
        UserLogin::create([
            'email' => 'pupt_admin@gmail.com',
            'password' => Hash::make('adminpassword'),
            'role' => 'admin',
            'Fcode' => 'A001',
            'surname' => 'Admin',
            'first_name' => 'PUPT',
            'middle_name' => 'Taguig',
            'name_extension' => null,
            'employment_type' => 'fulltime',
            'department_id' => null, 
        ]);

        //director
        UserLogin::create([
            'email' => 'pupt_director@gmail.com',
            'password' => Hash::make('directorpassword'),
            'role' => 'director',
            'Fcode' => 'D001',
            'surname' => '1',
            'first_name' => 'Director',
            'middle_name' => 'Director',
            'name_extension' => null,
            'employment_type' => 'fulltime',
            'department_id' => null, 
        ]);

        //faculty 1
        UserLogin::create([
            'email' => 'faculty01@gmail.com',
            'password' => Hash::make('facultypassword'),
            'role' => 'faculty',
            'Fcode' => 'F001',
            'surname' => '1',
            'first_name' => 'Faculty',
            'middle_name' => 'Faculty',
            'name_extension' => null,
            'employment_type' => 'parttime',
            'department_id' => 5, 
        ]);


        //faculty 2
        UserLogin::create([
            'email' => 'faculty02@gmail.com',
            'password' => Hash::make('facultypassword'),
            'role' => 'faculty',
            'Fcode' => 'F002',
            'surname' => '2',
            'first_name' => 'Faculty',
            'middle_name' => 'Faculty',
            'name_extension' => null,
            'employment_type' => 'fulltime',
            'department_id' => 1,
        ]);

        //faculty 3
        UserLogin::create([
            'email' => 'faculty03@gmail.com',
            'password' => Hash::make('facultypassword'),
            'role' => 'faculty-coordinator',
            'Fcode' => 'F003',
            'surname' => '3',
            'first_name' => 'Faculty',
            'middle_name' => 'Faculty',
            'name_extension' => null,
            'employment_type' => 'fulltime',
            'department_id' => 1,
        ]);

        //faculty 4
        UserLogin::create([
            'email' => 'faculty04@gmail.com',
            'password' => Hash::make('facultypassword'),
            'role' => 'faculty-coordinator',
            'Fcode' => 'F004',
            'surname' => '4',
            'first_name' => 'Faculty',
            'middle_name' => 'Faculty Coordinator',
            'name_extension' => null,
            'employment_type' => 'fulltime',
            'department_id' => 5,
        ]);
    }
}
