<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'email' => 'erno801@gmail.com',
            'password' => 'pwd11',
            'tier' => 0,
        ]);
        $this->call(TemplateSeeder::class);
        $this->call(CVSeeder::class);
    }
}
