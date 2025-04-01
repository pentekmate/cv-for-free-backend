<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCvRequest;
use App\Models\CV;
use App\Models\PreviousJob;
use Illuminate\Http\Request;

class CVController extends Controller
{
    public function index(Request $request)
    {
        // Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
        try {
            $authUser = auth()->user();
            $cvs = CV::where('user_id', $authUser->id)
                ->with('previousJobs')  // Betöltjük a kapcsolódó adatokat
                ->get();

            return response()->json(['m' => $cvs]);

        } catch (\Exception $e) {
            // Ha bármilyen kivétel történik, hibát küldünk vissza
            return response()->json(['message' => $e->getMessage()], 401); // 401 Unauthorized
        }

    }

    public function createCv(StoreCvRequest $request)
    {
        $validatedData = $request->validated();

        // $cvData = $validatedData["data"];
        // if(isset($validatedData["licenses"])){
        //     $licenses = $validatedData["licenses"];
        // }
        $newCv = CV::create($validatedData['data']);

        if (isset($validatedData['previousJobs'])) {
            $previousJobs = $validatedData['previousJobs'];

            foreach ($previousJobs as $prevJob) {
                PreviousJob::create(array_merge($prevJob, [
                    'cv_id' => $newCv->id,
                ]));
            }
        }

        return response()->json(['data' => $newCv]);
    }
}
