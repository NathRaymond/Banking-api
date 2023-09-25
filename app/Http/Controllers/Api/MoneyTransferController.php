<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Unicodeveloper\Paystack\Facades\Paystack;
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

    public function createRecipient(Request $request)
    {
        $wallet = Wallet::where("user_id", auth()->user()->id)->first();
        $validator = Validator::make($request->all(), [
            "recipient_name" => "required",
            "account_number" => "required|min:10|max:10",
            "bank_code" => "required",
            "amount" => "required|numeric",
        ]);
        if ($validator->fails()) {
            return API_Response(500, [
                "message" => $validator->messages()->first()
            ], $validator->errors());
        } else {
            if ($request->amount > ($wallet->balance + get_settings("transfer_charges"))) {
                $status = "Insufficient balance";
            } else {
                $status = "success";
            }

            $client = new Client();
            $url = 'https://api.paystack.co/transferrecipient';
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('paystack.secretKey'),
                ],
                'json' => [
                    'type' => "nuban",
                    'name' => "Transfer Fund",
                    'description' => "Transfer Fund",
                    'account_number' => $request->account_number,
                    'account_name' => $request->recipient_name,
                    'bank_code' => $request->bank_code,
                    'currency' => 'NGN'
                ],
            ]);

            return API_Response(200, [
                ...json_decode($response->getBody(), true),
                "status" => $status,
                "amount" => $request->amount,
                "charges" => get_settings("transfer_charges") == 0 ? "free" : get_settings("transfer_charges"),
                "total_amount" => ($request->amount + get_settings("transfer_charges")),
            ]);
        }
    }

    public function initiateTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "recipient_code" => "required",
            "amount" => "required|numeric",
            "reason" => "required|max:100",
            "transfer_pin" => "required",
        ]);
        if ($validator->fails()) {
            return API_Response(500, [
                "message" => $validator->messages()->first()
            ], $validator->errors());
        } else {

            $reference = generateReferenceId();
            $client = new Client();
            $url = 'https://api.paystack.co/transfer';
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('paystack.secretKey'),
                ],
                'json' => [
                    'source' => "balance",
                    'amount' => $request->amount,
                    'reference' => $reference,
                    'recipient' => $request->recipient_code,
                    'reason' => $request->reason,
                ],
            ]);

            return json_decode($response->getBody(), true);
        }
    }
}
