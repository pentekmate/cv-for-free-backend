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
        // 'name', 'pdf_pages','pdf_limit','price'
        Tier::create([
            'name' => 'Prémium',
            'price' => 1199,
            'pdf_pages' => 5,
            'pdf_limit' => 3,
        ])->features()->createMany([
            ['label' => 'Hozzáférés az összes prémium sablonhoz', 'is_checked' => true],
            ['label' => 'Önéletrajzaid mentése', 'is_checked' => true],
            ['label' => 'Maximális önéletrajz oldalszám', 'value' => 5, 'is_checked' => true],
            ['label' => 'Létrehozható Önéletrajzok', 'value' => '3/0', 'is_checked' => true],
        ]);


        Tier::create([
            'name' => 'Standard',
            'price' => 899,
            'pdf_pages' => 5,
            'pdf_limit' => 3,
        ])->features()->createMany([
            ['label' => 'Korlátozott hozzáférés a sablonokhoz', 'is_checked' => false],
             ['label' => 'Önéletrajzaid mentése', 'is_checked' => true],
            ['label' => 'Maximális önéletrajz oldalszám', 'value' => 2, 'is_checked' => false],
            ['label' => 'Létrehozható Önéletrajzok', 'value' => '1/0', 'is_checked' => true],
        ]);



        Tier::create([
            'name' => 'Ingyenes',
            'price' => null,
            'pdf_pages' => 5,
            'pdf_limit' => 3,
        ])->features()->createMany([
            ['label' => 'Hozzáférés ingyenes sablonokhoz', 'is_checked' => false],
            ['label' => 'Önéletrajzaid mentése', 'is_checked' => false],
            ['label' => 'Maximális önéletrajz oldalszám', 'value' => 2, 'is_checked' => false],
        ]);
    }
}
