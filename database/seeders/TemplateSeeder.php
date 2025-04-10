<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Template::factory()->create([
            'name' => 'Berlin',
            'img' => 'Berlin.png',
        ]);
        Template::factory()->create([
            'name' => 'Stockholm',
            'img' => 'Stockholm.png',
        ]);
        Template::factory()->create([
            'name' => 'Dublin',
            'img' => 'Dublin.png',
        ]);
        Template::factory()->create([
            'name' => 'Sydney',
            'img' => 'Sydney.png',
        ]);
        Template::factory()->create([
            'name' => 'Tokio',
            'img' => 'Tokio.png',
        ]);

    }
}
