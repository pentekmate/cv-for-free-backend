<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCvRequest;
use App\Models\CV;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CVController extends Controller
{
    public function index(Request $request)
    {
        // Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
        try {
            $authUser = auth()->user;
            $userId = $authUser->id;
            $cvs = CV::where('user_id', $userId)
                ->get();

            return response()->json([
                'cvs' => $cvs->map(function ($cv) {
                    return [
                        'id' => $cv->id,
                        'cv_type_id' => $cv->cv_type_id,
                        'created_at' => $cv->created_at,
                        'blob' => base64_encode($cv->blob),
                    ];
                }),
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

        $file = $request->file('blob');
        if ($file) {
            // Bináris fájl tartalom
            $fileContents = file_get_contents($file);
            $newCv->blob = $fileContents;
            $newCv->save();
        } else {
            return response()->json(['error' => 'Nincs fájl feltöltve.'], 400);
        }

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

        return response()->json(['message'=>'Sikeres létrehozás']);
    }

    public function show(Request $request){
        $cv_id = $request->cv_id;
        $cv = CV::where('id', $cv_id)
            ->where('user_id', Auth::id()) 
            ->withAll()
            ->first();

    if (!$cv) {
        return response()->json(['error' => 'Nincs jogosultság vagy nem létezik'], 403);
    }

    $cv->makeHidden(['blob']); // Opció: elrejted a blob mezőt

    return response()->json($cv);
    }
}
