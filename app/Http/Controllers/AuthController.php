<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'rememberMe' => 'boolean',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Hibás adatok!',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'A bejelentkezési adatok helytelenek',
            ], 401);
        }
    
        // Token generálása
        $token = $user->createToken('auth_token')->plainTextToken;
    
        // Cookie időtartam (rememberMe esetén hosszabb)
        $minutes = $request->rememberMe ? 60 * 24 * 7 : 0; // 7 nap vagy session cookie (0)
    
        $cookie = cookie(
            'auth_token',
            $token,
            $minutes,
            '/',
            null,
            true,   // secure (HTTPS környezetben true legyen!)
            true,   // httpOnly
            false,
            'Strict'
        );
    
        return response()->json([
            'message' => 'Sikeres bejelentkezés',
            'user' => $user,
            'token'=>$token
        ])->cookie($cookie);
    }
    


    public function me(Request $request)
    {
        $cookieToken = $request->cookie('auth_token');
    
        if (!$cookieToken) {
            return response()->json(['message' => 'Nincs bejelentkezve'], 401);
        }
    
        $tokenModel = PersonalAccessToken::findToken($cookieToken);
    
        if (!$tokenModel) {
            return response()->json(['message' => 'Érvénytelen token'], 401);
        }
    
        $user = $tokenModel->tokenable;
    
        return response()->json(['user' => $user,'token'=>$cookieToken]);
    }
    

    // **Kijelentkezés és token törlése**
    public function logout(Request $request)
    {
        $cookieToken = $request->cookie('auth_token');
    
        if ($cookieToken) {
            $tokenModel = PersonalAccessToken::findToken($cookieToken);
    
            if ($tokenModel) {
                $tokenModel->delete();
            }
        }
    
        // auth_token cookie törlése (lejárati idő negatív, azonnali törlés)
        $clearCookie = cookie(
            'auth_token',
            '',
            -1,
            '/',
            null,
            true,    // secure
            true,    // httpOnly
            false,
            'Strict'
        );
    
        return response()->json([
            'message' => 'Sikeresen kijelentkeztél'
        ])->cookie($clearCookie);
    }
    


    public function regist(RegistRequest $request){
        $validatedData = $request->validated();
        try{
            User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => $validatedData['password'],
                'tier_id'=>1,
            ]);

            return response()->json(['message'=>'Sikeres regisztráció'],201);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Hiba történt a regisztráció során.',
                'error' => $e->getMessage(),
            ], 500);
        }
        
    }
}
