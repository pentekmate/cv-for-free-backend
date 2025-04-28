<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCvRequest;
use App\Http\Requests\UpdateCvRequest;
use App\Models\CV;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CVController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userId = 1; // Teszteléshez
            $cvs = CV::where('user_id', $userId)->withAll()->get();
    
            $formattedCVs = $cvs->map(function ($cv) {
                $cvArray = $cv->toArray();
    
                // PDF blob base64 formában
                if ($cv->blob) {
                    $cvArray['blob'] = base64_encode($cv->blob);
                }
    
                // Ha az image mezőben van útvonal, csinálunk belőle teljes URL-t
                if ($cv->image) {
                  $cvArray['image'] = url('api/image/' . basename($cv->image));

                } else {
                    $cvArray['image'] = null;
                }
    
                return $cvArray;
            });
    
            return response()->json([
                'cvs' => $formattedCVs,
            ]);
    
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }
    

    public function createCv(StoreCvRequest $request)
    {
        $validatedData = $request->validated();
        $cvData = $validatedData['data'];
    
        // image-t kiszedjük, ne üsse el az insertet
        $imageBase64 = $cvData['image'] ?? null;
        unset($cvData['image']);
    
        $newCv = CV::create($cvData);
    
        // PDF fájl mentése
        if ($file = $request->file('data.blob')) {
            $newCv->blob = file_get_contents($file);
            $newCv->save();
        } else {
            return response()->json(['error' => 'Nincs fájl feltöltve.'], 400);
        }
    
        // Base64 kép mentése
        if ($imageBase64) {
            $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageBase64));
            $fileName = uniqid('cv_image_') . '.jpg';
            $filePath = 'cv-images/' . $fileName;
            Storage::disk('public')->put($filePath, $imageData);
            $newCv->image = $fileName;
            $newCv->save();
        }
    
        // Kapcsolatok kezelése
        $relations = (new CV)->getSupportedRelations();
        $response = [];
    
        foreach ($relations as $relation) {
            if ($request->has($relation) && method_exists(CV::class, $relation)) {
                $relationData = $request->$relation;
                if (is_array($relationData) && isset($relationData[0])) {
                    foreach ($relationData as $item) {
                        $newItem = $newCv->{$relation}()->create(array_merge($item, ['cv_id' => $newCv->id]));
                        $response[] = [$relation => $newItem];
                    }
                }
            }
        }
    
        return response()->json(['message' => 'Sikeres létrehozás']);
    }
    

    public function update(UpdateCvRequest $request)
    {
        $validatedData = $request->validated();

        $cv = CV::find($validatedData['cvId']);

        if (! $cv) {
            return response()->json(['error' => 'A CV nem található!'], 404);
        }

        $cv->update($validatedData['data']);

        if ($request->hasFile('data.blob')) {
            $file = $request->file('data.blob');

            if ($file->isValid()) {
                $cv->blob = file_get_contents($file);
                $cv->save();
            } else {
                return response()->json(['error' => 'Hibás fájl.'], 400);
            }
        } else {
            return response()->json(['error' => 'A blob fájl hiányzik.'], 400);
        }

        $relations = (new CV)->getSupportedRelations();

        foreach ($relations as $relation) {
            if ($request->has($relation)) {
                if (method_exists(CV::class, $relation)) {
                    $relationData = $request->$relation;

                    // 1. Törlés
                    $cv->{$relation}()->delete();

                    // 2. Újak létrehozása
                    foreach ($relationData as $item) {
                        $cv->{$relation}()->create(array_merge($item, ['cv_id' => $cv->id]));
                    }
                }
            }
        }

        return response()->json([
            'message' => 'CV sikeresen frissítve!',
            'cv' => $cv->makeHidden(['blob']),
        ]);
    }
}
