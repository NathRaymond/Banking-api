<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataRechargeController extends Controller
{
    public function recharge_data(Request $request)
{
    $validator = Validator::make($request->all(), [
        'phone_number' => 'required|numeric',
        'data_plan' => 'required|string', 
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 500);
    }

    $phone_number = $request->input('phone_number');
    $data_plan = $request->input('data_plan');

    return response()->json([
        'message' => "Successfully recharged $data_plan data for $phone_number",
    ], 200);
}

}
