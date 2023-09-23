<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->API_Response(500, ['error' => 'User not found']);
        }

        return $this->API_Response(200, ['user' => $user]);
    }

    protected function API_Response($status, $data)
    {
        return response()->json(['status' => $status, 'data' => $data]);
    }


}
