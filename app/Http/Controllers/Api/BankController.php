<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class BankController extends Controller
{
    public function fetchAllBank()
    {
        $client = new Client();
        $url = 'https://api.paystack.co/bank';
        try {
            $response = $client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('paystack.secretKey'),
                ],
            ]);

            return API_Response(200, 
               json_decode( $response->getBody())
            );
        } catch (\Exception $e) {
            return API_Response(500, [
                "message" => $e->getMessage()
            ]);
        }
    }
}
