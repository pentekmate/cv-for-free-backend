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
            'name' => 'Tokio',
            'colors' => '#641220 #6E1423 #B21E35 #C71F37 #DA1E37',
            'pdf' => '<TokioPDF/>',
            'img' => 'Tokio.png',
        ]);
        Template::factory()->create([
            'name' => 'Berlin',
            'colors' => '#2e294e #1B998B #c5d86d #F46036',
            'pdf' => '<BerlinPDF/>',
            'img' => 'berling.png',
        ]);
        Template::factory()->create([
            'name' => 'Stockholm',
            'colors' => ' #1B998B #F46036 #2E294E #E71D36 #C5D86D',
            'pdf' => '<StockholmPDF/>',
            'img' => 'stockholm.png',
        ]);
        Template::factory()->create([
            'name' => 'Dublin',
            'colors' => '#DAD7CD #A3B18A #588157 #3A5A40 #344E41',
            'pdf' => '<DublinPDF/>',
            'img' => 'Dublin.png',
        ]);
        Template::factory()->create([
            'name' => 'Sydney',
            'colors' => '#00A8E8 #007EA7 #003459 #00171F #051923',
            'pdf' => '<SydneyPDF/>',
            'img' => 'sydney.png',
        ]);

    }
}
