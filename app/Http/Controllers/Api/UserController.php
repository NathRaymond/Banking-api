<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index($id)
    {
        $user = User::find($id);

        if (!$user) {
            return API_Response(500, ['error' => 'User not found']);
        }

        return API_Response(200, ['user' => $user]);
    }



}
