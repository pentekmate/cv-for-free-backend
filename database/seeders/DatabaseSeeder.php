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
        $this->call(TierSeeder::class);
        User::factory()->create([
            'email' => 'erno801@gmail.com',
            'password' => 'pwd11',
        ]);
        User::factory()->create([
            'email' => 'kalap@gmail.com',
            'password' => 'pwd11',
        ]);
        $this->call(TemplateSeeder::class);
        // $this->call(CVSeeder::class);
        $this->call(ColorSeeder::class);
    }
}
