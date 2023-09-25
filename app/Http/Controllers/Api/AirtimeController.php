<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AirtimeController extends Controller
{
    public function recharge_airtime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|numeric',
            'amount' => 'required|numeric|min:11|max:11', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 500);
        }

        $phone_number = $request->input('phone_number');
        $amount = $request->input('amount');

        return response()->json([
            'message' => "Successfully recharged $amount NGN airtime for $phone_number",
        ], 200);
    }

}
