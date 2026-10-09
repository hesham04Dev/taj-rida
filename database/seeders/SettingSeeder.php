<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'recitation_points_per_page', 'value' => '10'],
            ['key' => 'points_per_page_regular', 'value' => '10'],
            ['key' => 'points_per_page_serd', 'value' => '5'],
            ['key' => 'revision_points_per_page', 'value' => '5'],
            ['key' => 'attendance_points', 'value' => '5'],
            ['key' => 'absence_penalty', 'value' => '-5'],
            ['key' => 'expected_exchange_rate', 'value' => '0.1'],
            ['key' => 'gifts_balance', 'value' => '0'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], ['value' => $setting['value']]);
        }
    }
}
