<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'recitation_points_per_page', 'value' => '10'],
            ['key' => 'revision_points_per_page', 'value' => '5'],
            ['key' => 'attendance_points', 'value' => '5'],
            ['key' => 'absence_penalty', 'value' => '-5'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], ['value' => $setting['value']]);
        }
    }
}
