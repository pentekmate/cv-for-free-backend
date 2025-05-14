<?php

namespace App\Http\Controllers;

use App\Helpers\EncryptionHelper;
use App\Http\Requests\DeleteCVRequest;
use App\Http\Requests\StoreCvRequest;
use App\Http\Requests\UpdateCvRequest;
use App\Models\CV;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CVController extends Controller
{
    public function index(Request $request)
    {
        try {
            // $userId = Auth::user()->id; // Teszteléshez
            $userId = 1;
            $cvs = CV::where('user_id', $userId)->withAll()->get();

            $formattedCVs = $cvs->map(function ($cv) {
                $cvArray = $cv->toArray();
                $cvArray = EncryptionHelper::decryptFields($cvArray);
                // Ha az image mezőben van útvonal, csinálunk belőle teljes URL-t
                if ($cv->image) {
                    $cvArray['image'] = url('api/image/'.basename($cv->image));

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

        $cookie = $request->cookie('auth_token');

        $userId = Auth::user()->id;
        if (! $userId) {
            return response()->json(['message' => 'Nem található felhasználó'], 500);
        }
        $validatedData = $request->validated();
        $cvData = $validatedData['data'];
        $cvData['user_id'] = $userId;

        $imageBase64 = $cvData['image'] ?? null;
        unset($cvData['image']);
        unset($cvData['blob']);

        $cvData = EncryptionHelper::encryptFields($cvData);
        $newCv = CV::create($cvData);

        // PDF fájl mentése
        if ($file = $request->file('data.blob')) {

            $file = file_get_contents($file);
            $base64 = base64_encode($file);
            $newCv->blob = $base64;
            $newCv->save();
        } else {
            return response()->json(['error' => 'Nincs fájl feltöltve.'], 400);
        }

        // Base64 kép mentése
        if ($imageBase64) {
            $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageBase64));
            $fileName = uniqid('cv_image_').'.jpg';
            $filePath = 'cv-images/'.$fileName;
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
        $cvData = $validatedData['data'];

        $imageBase64 = $cvData['image'] ?? null;
        unset($cvData['image']);
        unset($cvData['blob']);

        $cv = CV::find($validatedData['cvId']);

        if (! $cv) {
            return response()->json(['error' => 'A CV nem található!'], 404);
        }
        $cvData = EncryptionHelper::encryptFields($cvData);
        $cv->update($cvData);

        // PDF fájl frissítése
        if ($file = $request->file('data.blob')) {
            if ($file->isValid()) {
                $fileContents = file_get_contents($file);
                $cv->blob = base64_encode($fileContents);
                $cv->save();
            } else {
                return response()->json(['error' => 'Hibás fájl.'], 400);
            }
        }

        // Kép frissítése, ha van új
        if ($imageBase64) {
            $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageBase64));
            $fileName = uniqid('cv_image_').'.jpg';
            $filePath = 'cv-images/'.$fileName;
            Storage::disk('public')->put($filePath, $imageData);

            $cv->image = $fileName;
            $cv->save();
        }

        // Kapcsolatok frissítése
        $relations = (new CV)->getSupportedRelations();
        $response = [];

        foreach ($relations as $relation) {
            if ($request->has($relation) && method_exists(CV::class, $relation)) {
                $relationData = $request->$relation;

                if (is_array($relationData) && isset($relationData[0])) {
                    $cv->{$relation}()->delete(); // előzőek törlése

                    foreach ($relationData as $item) {
                        $newItem = $cv->{$relation}()->create(array_merge($item, ['cv_id' => $cv->id]));
                        $response[] = [$relation => $newItem];
                    }
                }
            }
        }

        return response()->json([
            'message' => 'CV sikeresen frissítve!',
            'cv' => $cv->makeHidden(['blob']),
        ]);
    }

    public function delete(DeleteCVRequest $request)
    {
        $cv = CV::find($request->cvId);
        try {
            $cv->delete();

            return response()->json(['message' => 'Sikeresen törölve.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Hiba történt a törlés során.'], 500);
        }
    }
}
