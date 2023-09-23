<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
// use KingFlamez\Rave\Facades\Rave as Flutterwave;


class MoneyTransferController extends Controller
{
    public function verify_account(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "account_number" => "required|min:10|max:10",
            "bank_code" => "required",
        ]);

        if ($validator->fails()) {
            return API_Response(500, [
                "message" => $validator->messages()->first()
            ], $validator->errors());
        } else {
            try {
                $client = new Client();
                $response = $client->get('https://api.paystack.co/bank/resolve?account_number=' . $request->account_number . '&bank_code=' . $request->bank_code, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . config('paystack.secretKey'),
                    ]
                ]);
                return API_Response(
                    200,
                    json_decode($response->getBody())
                );
            } catch (\Exception $e) {
                return API_Response(500, [
                    "message" => "Could not resolve account name. Check parameters or try again"
                ]);
            }
        }
    }
}
