<?php

namespace Database\Seeders;

use App\Models\Tier;
use Illuminate\Database\Seeder;

class TierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tier::factory()->create([
            'name' => 'free',
            'pdf_limit' => 2,
        ]);

        Tier::factory()->create([
            'name' => 'pro',
            'pdf_limit' => 5,
        ]);
    }
}
