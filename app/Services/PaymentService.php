<?php

namespace App\Services;

use GuzzleHttp\Client;

class PaymentService
{
    public function makeDeposit($orderId, $amount)
    {
        $client = new Client();

        // Mengirim permintaan Deposit ke API pihak ketiga
        $response = $client->post('http://127.0.0.1:8000/api/deposit', [
            'json' => [
                'order_id' => $orderId,
                'amount' => $amount,
            ]
        ]);

        // Mengambil response dari API
        return json_decode($response->getBody()->getContents(), true);
    }
}
