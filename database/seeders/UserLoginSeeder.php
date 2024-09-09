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
            'email' => 'jamesnabayra@gmail.com',
            'password' => Hash::make('adminpassword'),
            'role' => 'admin',
            'Fcode' => 'A001',
            'surname' => 'Nabayra',
            'first_name' => 'James',
            'middle_name' => 'Smith',
            'name_extension' => null,
            'employment_type' => 'fulltime',
            'department' => null,
        ]);

        // Seed for faculty 1
        UserLogin::create([
            'email' => 'dianrosefidel@gmail.com',
            'password' => Hash::make('facultypassword'),
            'role' => 'faculty',
            'Fcode' => 'F001',
            'surname' => 'Fidel',
            'first_name' => 'Dian Rose',
            'middle_name' => 'Rose',
            'name_extension' => null,
            'employment_type' => 'parttime',
            'department' => 'math',
        ]);

        // Seed for director
        UserLogin::create([
            'email' => 'edjudahmingo@gmail.com',
            'password' => Hash::make('directorpassword'),
            'role' => 'director',
            'Fcode' => 'D001',
            'surname' => 'Mingo',
            'first_name' => 'Edjudah',
            'middle_name' => 'John',
            'name_extension' => 'Jr',
            'employment_type' => 'fulltime',
            'department' => null,
        ]);

        // Seed for faculty 2
        UserLogin::create([
            'email' => 'kazelvillamarzo@gmail.com',
            'password' => Hash::make('faculty2024'),
            'role' => 'faculty',
            'Fcode' => 'F002',
            'surname' => 'Villamarzo',
            'first_name' => 'Kazel',
            'middle_name' => 'Jane',
            'name_extension' => null,
            'employment_type' => 'fulltime',
            'department' => 'IT',
        ]);
    }
}
