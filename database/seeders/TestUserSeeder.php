<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'testactiveuser',
            'email' => 'testactive@example.com',
            'password' => Hash::make('password'),
            'role' => 'supplier',
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
} 