<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'data',
            'email' => 'Admin@hubshooting.com',
            'phone_no' => '+911234567890',
            'email_verified'=> 1,
            'phone_verified'=> 1,
            'otp_verified'=> 1,
            'password' => bcrypt('Hub@0620'),
            'user_type' =>'admin',
        ]);
    }
}
