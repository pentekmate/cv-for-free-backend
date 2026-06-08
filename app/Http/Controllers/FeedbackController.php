<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    //

    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'rating' => 'nullable|integer|between:1,5|required_without:message',
                'message' => 'nullable|string|max:250|required_without:rating',
            ]);

            $newFeedback = new Feedback;

            $newFeedback->rating = $validated['rating'];
            $newFeedback->message = $validated['message'];
            $newFeedback->save();

            return response()->json([
                'success' => true,
                'message' => 'Köszönjük a visszajelzést!',
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
                'message' => 'Hiba történt a feedback létrehozásakor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
