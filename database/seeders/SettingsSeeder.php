<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        DB::table('settings')->insertOrIgnore([
            ['key' => 'require_2fa', 'value' => '0'],
            ['key' => 'require_strong_passwords', 'value' => '1'],
            ['key' => 'auto_logout', 'value' => '30'], // minutes
            ['key' => 'login_notifications', 'value' => '1'],
        ]);
    }
} 