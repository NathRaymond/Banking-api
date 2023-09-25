<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ElectricityRechargeController extends Controller
{
    public function recharge_electricity(Request $request)
{
    $validator = Validator::make($request->all(), [
        'meter_number' => 'required|numeric',
        'amount' => 'required|numeric|min:1', 
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 500);
    }

    $meter_number = $request->input('meter_number');
    $amount = $request->input('amount');

    return response()->json([
        'message' => "Successfully recharged NGN $amount for meter $meter_number",
    ], 200);
}

}
