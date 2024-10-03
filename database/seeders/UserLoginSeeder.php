<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\UserLogin;

class UserLoginSeeder extends Seeder
{
    public function run()
    {
        // Seed for admin
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
            'department' => null,
        ]);

        // Seed for faculty 1
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
            'department' => 'College of Information Technology',
        ]);

        // Seed for director
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
            'department' => null,
        ]);

        // Seed for faculty 2
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
            'department' => 'College of Engineering',
        ]);
    }
}
