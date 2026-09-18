<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->call([
            CurriculumSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
