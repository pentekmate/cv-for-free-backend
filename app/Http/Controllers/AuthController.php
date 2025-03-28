<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
class AuthController extends Controller
{
   
    
    public function login(Request $request)
    {
        $key = 'login-attempts:' . $request->ip();


        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Túl sok bejelentkezési kísérlet. Próbáld újra később.'
            ], 429);
        }
        RateLimiter::hit($key, 60); 
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422); // 422: Unprocessable Entity
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'A bejelentkezési adatok helytelenek'
            ], 401);
        }

        $user->tokens()->delete();

        // Token létrehozása
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }


    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    // **Kijelentkezés és token törlése**
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Sikeresen kijelentkeztél'
        ]);
    }
}
