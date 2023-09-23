<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user(); 

        if ($user) {
            return response()->json(['user' => $user], 200);
        } else {
            return response()->json(['error' => 'User not found'], 404);
        }
    }
}
