<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCvRequest;
use App\Models\CV;
use App\Models\PreviousJob;
use App\Models\Skill;
use Illuminate\Http\Request;

class CVController extends Controller
{
    public function index(Request $request)
    {
        // Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
        try {
            $authUser = auth()->user();
            $cvs = CV::where('user_id', $authUser->id)
                ->withAll() // Betöltjük a kapcsolódó adatokat
                ->get();

            // return response()->json(['m' => $cvs]);

            $relations = (new CV)->getSupportedRelations();

            return response()->json(['relations' => $relations]);

        } catch (\Exception $e) {
            // Ha bármilyen kivétel történik, hibát küldünk vissza
            return response()->json(['message' => $e->getMessage()], 401); // 401 Unauthorized
        }

    }

    public function createCv(StoreCvRequest $request)
    {
        $validatedData = $request->validated();

        $newCv = CV::create($validatedData['data']);

        if (isset($validatedData['previousJobs'])) {
            $previousJobs = $validatedData['previousJobs'];

            foreach ($previousJobs as $prevJob) {
                PreviousJob::create(array_merge($prevJob, [
                    'cv_id' => $newCv->id,
                ]));
            }
        }

        if (isset($validatedData['skills'])) {
            $skills = $validatedData['skills'];

            foreach ($skills as $skill) {
                Skill::create(array_merge($skill, [
                    'cv_id' => $newCv->id,
                ]));
            }
        }

        if (isset($validatedData['languages'])) {
            $languages = $validatedData['languages'];

            foreach ($languages as $language) {
                Skill::create(array_merge($language, [
                    'cv_id' => $newCv->id,
                ]));
            }
        }

        return response()->json([
            'message' => 'cv Sikeresen létrehozva',
            'data' => $newCv]);
    }
}
