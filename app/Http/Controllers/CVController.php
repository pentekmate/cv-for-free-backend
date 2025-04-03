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
            $authUser = auth()->user();
            $cvs = CV::where('user_id', $authUser->id)
                ->withAll()
                ->get();

            return response()->json(['cvs' => $cvs]);

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
}
