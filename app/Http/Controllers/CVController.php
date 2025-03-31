<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CVController extends Controller
{
    //

    public function createCv(Request $request){
        $cv = $request->all();
        return response()->json([
        'm'=>$cv,
        "pj"=>$cv["previousJobs"]
    ]);
    }
}
