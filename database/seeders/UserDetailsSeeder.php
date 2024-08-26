<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserDetails;

class UserDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $details = [
            [
                'user_login_id' => 1,
                'first_name' => 'James',
                'last_name' => 'Nabayra',
                'phone_number' => '09246654892',
            ],
            [
                'user_login_id' => 3,
                'first_name' => 'Ed Judah',
                'last_name' => 'Mingo',
                'phone_number' => '09457863215',
            ],
        ];

        foreach ($details as $detail) {
            UserDetails::create($detail);
        }
    }
}
