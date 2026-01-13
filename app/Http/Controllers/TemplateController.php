<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Support\Facades\Cache;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Cache::remember('templates', 60, function () {
            return Template::with('colors')->get()->map(function ($template) {
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                    'PDF' => $template->PDF,
                    'img' => asset('storage/'.$template->img),
                    'created_at' => $template->created_at,
                    'updated_at' => $template->updated_at,
                    'colors' => $template->colors->pluck('color')->toArray(),
                ];
            });
        });

        return response()->json($templates);
    }
}
