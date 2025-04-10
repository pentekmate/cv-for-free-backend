<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $colors = [
            '1' => ['#2e294e', '#1B998B', '#c5d86d', '#F46036'],
            '2' => ['#1B998B', '#F46036', '#2E294E', '#E71D36'],
            '3' => ['#DAD7CD', '#A3B18A', '#588157', '#3A5A40'],
            '4' => ['#00A8E8', '#007EA7', '#003459', '#00171F'],
            '5' => ['#641220', '#6E1423', '#B21E35', '#C71F37'],
        ];

        foreach ($colors as $templateId => $colorSet) {
            foreach ($colorSet as $color) {
                Color::factory()->create([
                    'template_id' => $templateId,
                    'color' => $color, // Feltételezve, hogy van egy `hex` meződ az adatbázisban
                ]);
            }
        }

    }
}
