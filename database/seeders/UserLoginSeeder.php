<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\UserLogin;

class UserLoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // seed for admin
        UserLogin::create([
            'email' => 'jamesnabayra@gmail.com',
            'password' => Hash::make('adminpassword'), 
            'role' => 'admin',
        ]);

        // seed for faculty
        UserLogin::create([
            'email' => 'dianrosefidel@gmail.com',
            'password' => Hash::make('facultypassword'), 
            'role' => 'faculty',
        ]);

        // seed for director
        UserLogin::create([
            'email' => 'edjudahmingo@gmail.com',
            'password' => Hash::make('directorpassword'), 
            'role' => 'director',
        ]);
    }
}
