<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call(UserLoginSeeder::class);
    }    

    public function runDetails()
    {
        $this->call(UserDetailsSeeder::class);
    }    

    public function runDepartment()
    {
        $this->call(DepartmentSeeder::class);
    }    
}
