<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index()
    {
        // Lekérjük az összes sablont az adatbázisból
        $templates = Template::all();

        // A képek URL-jének beállítása
        foreach ($templates as $template) {
            $template->img = asset('storage/' . $template->img);  // Ha a képek a storage/public mappában vannak
        }

        // JSON válasz visszaadása
        return response()->json($templates);
    }
}
