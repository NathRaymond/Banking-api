<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->verified == 1) {
                return response()->json(['message' => 'Login successful']);
            } else {
                Auth::logout();
                return response()->json(['error' => 'User has not been verified']);
            }
        } else {
            return response()->json(['error' => 'Invalid credentials']);
        }
    }
}
