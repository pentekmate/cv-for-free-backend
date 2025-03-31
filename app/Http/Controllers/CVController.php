<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CVController extends Controller
{
    public function createCv(Request $request)
    {
        // Manuálisan végezzük el a validálást
        $validator = Validator::make($request->all(), [
            'data.user_id' => 'integer|required',
            'data.userName' => 'string|nullable',
            'data.image' => 'string|nullable',
            'data.firstName' => 'string|nullable',
            'data.lastName' => 'string|nullable',
            'data.phoneNumber' => 'string|nullable',
            'data.email' => 'email|nullable',
            'data.country' => 'string|nullable',
            'data.city' => 'string|nullable',
            'data.jobTitle' => 'integer|nullable',
            'data.introduce' => 'string|nullable',
            'data.age' => 'integer|nullable|min:18|max:100',
            'data.ethnic' => 'string|nullable',

            'previousJobs' => 'array|nullable',
            'previousJobs.*.employer' => 'string|nullable',
            'previousJobs.*.jobTitle' => 'string|nullable',
            'previousJobs.*.startDate' => 'date|nullable',
            'previousJobs.*.endDate' => 'date|nullable|after_or_equal:previousJobs.*.startDate',
            'previousJobs.*.description' => 'string|nullable',
            'previousJobs.*.city' => 'string|nullable',

            'skills' => 'array|nullable',
            'skills.*.skillName' => 'string|nullable',
            'skills.*.skillLevel' => 'integer|nullable',
        ]);

        // Ha a validálás nem sikerült, visszaadjuk a hibákat JSON formátumban
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Hiba történt a validálás során.',
                'errors' => $validator->errors(),
            ], 422); // 422-es státuszkód a validálási hibákhoz
        }

        // A validálás sikeres, folytatjuk az adat mentését
        $validatedData = $validator->validated();

        // Válasz visszaküldése
        return response()->json([
            'message' => 'A CV sikeresen elmentve!',
            'data' => $validatedData,
        ]);
    }
}
