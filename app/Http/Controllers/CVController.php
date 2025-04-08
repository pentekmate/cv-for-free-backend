<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCvRequest;
use App\Models\CV;
use Illuminate\Http\Request;

class CVController extends Controller
{
    public function index(Request $request)
    {
        // Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
        try {
            // $authUser = auth()->user;
            $userId =1;
            $cvs = CV::where('user_id', $userId)
                ->withAll()
                ->get();

                return response()->json([
                    'cvs' => $cvs->map(function($cv) {
                        return [
                            'id' => $cv->id,
                            'cv_type_id' => $cv->cv_type_id,
                            'created_at' => $cv->created_at,
                            'blob' => base64_encode($cv->blob), 
                        ];
                    })
                ]);

        } catch (\Exception $e) {
            // Ha bármilyen kivétel történik, hibát küldünk vissza
            return response()->json(['message' => $e->getMessage()], 401); // 401 Unauthorized
        }

    }

    public function createCv(StoreCvRequest $request)
    {
        $validatedData = $request->validated();

        $newCv = CV::create($validatedData['data']);
        $relations = (new CV)->getSupportedRelations();
        $response = [];
        foreach ($relations as $relation) {
            if ($request->has($relation)) {
                if (method_exists(CV::class, $relation)) {
                    $relationData = $request->$relation;

                    // Ha tömb, akkor többet hozunk létre (hasMany kapcsolat)
                    if (is_array($relationData) && isset($relationData[0])) {
                        foreach ($relationData as $item) {
                            $newItem = $newCv->{$relation}()->create(array_merge($item, ['cv_id' => $newCv->id]));
                            $response[] = [$relation => $newItem];
                        }
                    }
                }
            }
        }

        return response()->json($response);
    }

    public function store(Request $request)
{
    try {
        // A fájl adatainak feldolgozása, bináris adatként
        $file = $request->file('blob');
        if ($file) {
            // Bináris fájl tartalom
            $fileContents = file_get_contents($file);
        } else {
            return response()->json(['error' => 'Nincs fájl feltöltve.'], 400);
        }

        // Validáljuk a bejövő adatokat
        $validated = $request->validate([
            'blob' => 'required|file|mimes:pdf|max:10240',  // Példa validálás, ha PDF-t vársz
            'user_id' => 'required|integer',
            'cv_type_id' => 'required|integer',
        ]);

        // // A rekord létrehozása
        $cv = CV::create([
            'user_id' =>$validated['user_id'],
            'cv_type_id' =>$validated['cv_type_id'],
            'blob' => $fileContents
        ]);

        
        return response()->json([
            'cv' => $cv,
            'fh' =>$validated['user_id'],
            'blob' => base64_encode($fileContents) 
        ]);
    
    } catch (\Exception $e) {
        // Ha hiba történik, azt itt naplózzuk
        return response()->json(['error' => 'Hiba: ' . $e->getMessage()], 500);
    }
}

    
    

}



