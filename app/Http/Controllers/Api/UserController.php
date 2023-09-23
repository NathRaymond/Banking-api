<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function fetchAuthUser(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return API_Response(200, [
                "message" => $user
            ]);
        }else{
            return API_Response(500, [
                'message' => 'User unauthorize'
            ]);
        }

    }
}
