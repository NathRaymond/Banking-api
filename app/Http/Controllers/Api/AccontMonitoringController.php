<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccontMonitoringController extends Controller
{
    public function restrict()
    {
        $user = auth()->user();
        $user->account_restriction_status = 1;

        if ($user->save()) {
            return API_Response(200, ['message' => 'Account Restricted Successfully']);
        } else {
            return API_Response(500, ['message' => 'Failed to restrict account']);
        }
    }


    public function close()
    {
        $user = auth()->user();
        $user->account_closed_status = 1;
        if ($user->save()) {
            return API_Response(200, ['message' => 'Account Closed Successfully']);
        } else {
            return API_Response(500, ['message' => 'Failed to close account']);
        }
    }

}
