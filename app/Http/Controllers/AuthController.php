<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $key = 'login-attempts:'.$request->ip();
        $remember_me = $request->rememberMe;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Túl sok bejelentkezési kísérlet. Próbáld újra később.',
            ], 429);
        }
        RateLimiter::hit($key, 60);
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'rememberMe' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'A bejelentkezési adatok helytelenek',
            ], 401);
        }

        $user->tokens()->delete();

        // Token létrehozása
        $token = $user->createToken('api-token')->plainTextToken;

        if ($remember_me) {
            $cookie = Cookie::make('auth_token', $token, 60 * 24 * 7, '/', null, true, true, false, 'None');

            return response()->json([
                'user' => $user,
            ])->cookie($cookie);
        }

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);

    }

    public function me(Request $request)
    {
        $cookie = $request->cookie('auth_token');
        $token = PersonalAccessToken::findToken($cookie);

        if ($token) {
            return response()->json(['user' => $token->tokenable]);
        }

        return response()->json([
            'message' => 'Nincs bejelentkezve',
        ], 401);
    }

    // **Kijelentkezés és token törlése**
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Sikeresen kijelentkeztél',
        ]);
    }
}
