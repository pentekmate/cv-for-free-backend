<?php

namespace App\Http\Controllers;

use App\Helpers\EncryptionHelper;
use App\Http\Requests\DeleteCVRequest;
use App\Http\Requests\StoreCvRequest;
use App\Http\Requests\UpdateCvRequest;
use App\Models\CV;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CVController extends Controller
{
    public function index(Request $request)
    {
        try {
            $cvs = CV::all();

            return response()->json([
                'cvs' => $cvs->count(),
            ]);

        } catch (\Exception $e) {
            // Hibakezelés: visszaadjuk a hibát JSON-ban
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a lekérdezés során.',
                'error' => $e->getMessage(),
            ], 500);
        }

        // #region
        // try {
        //     $userId = Auth::user()->id; // Teszteléshez
        //     $cvs = CV::where('user_id', $userId)->withAll()->get();

        //     $formattedCVs = $cvs->map(function ($cv) {
        //         $cvArray = $cv->toArray();
        //         $cvArray = EncryptionHelper::decryptFields($cvArray);
        //         // Ha az image mezőben van útvonal, csinálunk belőle teljes URL-t
        //         if ($cv->image) {
        //             $cvArray['image'] = url('api/image/'.basename($cv->image));

        //         } else {
        //             $cvArray['image'] = null;
        //         }

        //         return $cvArray;
        //     });

        //     return response()->json([
        //         'cvs' => $formattedCVs,
        //     ]);

        // } catch (\Exception $e) {
        //     return response()->json(['message' => $e->getMessage()], 401);
        // }
        // #endregion
    }
    // #region
    // public function createCv(StoreCvRequest $request)
    // {

    //     $cookie = $request->cookie('auth_token');

    //     $userId = Auth::user()->id;
    //     if (! $userId) {
    //         return response()->json(['message' => 'Nem található felhasználó'], 500);
    //     }
    //     $validatedData = $request->validated();
    //     $cvData = $validatedData['data'];
    //     $cvData['user_id'] = $userId;

    //     $imageBase64 = $cvData['image'] ?? null;
    //     unset($cvData['image']);
    //     unset($cvData['blob']);

    //     $cvData = EncryptionHelper::encryptFields($cvData);
    //     $newCv = CV::create($cvData);

    //     // PDF fájl mentése
    //     if ($file = $request->file('data.blob')) {

    //         $file = file_get_contents($file);
    //         $base64 = base64_encode($file);
    //         $newCv->blob = $base64;
    //         $newCv->save();
    //     } else {
    //         return response()->json(['error' => 'Nincs fájl feltöltve.'], 400);
    //     }

    //     // Base64 kép mentése
    //     if ($imageBase64) {
    //         $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageBase64));
    //         $fileName = uniqid('cv_image_').'.jpg';
    //         $filePath = 'cv-images/'.$fileName;
    //         Storage::disk('public')->put($filePath, $imageData);
    //         $newCv->image = $fileName;
    //         $newCv->save();
    //     }

    //     // Kapcsolatok kezelése
    //     $relations = (new CV)->getSupportedRelations();
    //     $response = [];

    //     foreach ($relations as $relation) {
    //         if ($request->has($relation) && method_exists(CV::class, $relation)) {
    //             $relationData = $request->$relation;
    //             if (is_array($relationData) && isset($relationData[0])) {
    //                 foreach ($relationData as $item) {
    //                     $newItem = $newCv->{$relation}()->create(array_merge($item, ['cv_id' => $newCv->id]));
    //                     $response[] = [$relation => $newItem];
    //                 }
    //             }
    //         }
    //     }

    //     return response()->json(['message' => 'Sikeres létrehozás']);
    // }
    // #endregion

    public function createCv(Request $request)
    {

        try {
            $validated = $request->validate([
                'cvType' => 'required|string',
                'pickedBG' => 'required|string',
            ]);

            $newCv = new CV;

            $newCv->cv_type = $validated['cvType'];
            $newCv->picked_bg = $validated['pickedBG'];
            $newCv->save();

            return response()->json([
                'success' => true,
                'message' => 'CV sikeresen létrehozva!',
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Ha nem küldted el az adatokat a frontendről, itt fog elszállni szép hibaüzenettel
            return response()->json([
                'success' => false,
                'message' => 'Hiányzó adatok.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a CV létrehozásakor.',
                'error' => $e->getMessage(),
            ], 500);
        }
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

    public function getAdminPageInfo(Request $request)
    {
        $weeklyStartDate = Carbon::now()->subDays(6)->startOfDay();

        $weeklyDownloads = DB::table('cvs')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', $weeklyStartDate)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('count', 'date');

        $weeklyChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateString = Carbon::now()->subDays($i)->toDateString();
            $weeklyChartData[$dateString] = $weeklyDownloads[$dateString] ?? 0;
        }

        $monthlyStartDate = Carbon::now()->subMonths(11)->startOfMonth();

        $monthlyDownloads = DB::table('cvs')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('count(*) as count'))
            ->where('created_at', '>=', $monthlyStartDate)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('count', 'month');

        $monthlyChartData = [];
        for ($i = 11; $i >= 0; $i--) {

            $monthString = Carbon::now()->subMonths($i)->format('Y-m');
            $monthlyChartData[$monthString] = $monthlyDownloads[$monthString] ?? 0;
        }

        $absoluteRecords = DB::table('cvs')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('count', 'desc')
            ->limit(3)
            ->get();

        $weeklyDistribution = DB::table('cvs')
            ->select(DB::raw('DAYOFWEEK(created_at) as day_num'), DB::raw('count(*) as count'))
            ->groupBy('day_num')
            ->orderBy('day_num', 'asc')
            ->pluck('count', 'day_num');

        $daysMap = [
            1 => 'Vasárnap', 2 => 'Hétfő', 3 => 'Kedd',
            4 => 'Szerda', 5 => 'Csütörtök', 6 => 'Péntek', 7 => 'Szombat',
        ];

        $bestDaysOfWeek = [];
        foreach ($daysMap as $num => $name) {
            $bestDaysOfWeek[$name] = $weeklyDistribution[$num] ?? 0;
        }

        return response()->json([
            'charts' => [
                'weekly' => $weeklyChartData,
                'monthly' => $monthlyChartData,
            ],
            'all_time_insights' => [
                'absolute_records' => $absoluteRecords,
                'best_days_of_week' => $bestDaysOfWeek,
            ],

        ]);
    }
}
