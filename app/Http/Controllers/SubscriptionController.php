<?php

namespace App\Http\Controllers;

use App\Models\Tier;

class SubscriptionController extends Controller
{
    public function index()
    {
        $tiers = Tier::with('features')->get();

        return response()->json($tiers);
    }
}
