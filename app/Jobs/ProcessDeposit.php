<?php

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Jobs\Job;
use Illuminate\Http\Client\Factory as Http;
use GuzzleHttp\Client;

class ProcessDeposit extends Job implements ShouldQueue
{
    use Queueable;

    protected $orderId;
    protected $amount;
    protected $timestamp;

    public function __construct($orderId, $amount, $timestamp)
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->timestamp = $timestamp;
    }

    public function handle()
    {
        $url = url('/mock/deposit');

        // Kirim request ke API pihak ketiga menggunakan Guzzle atau HTTP Client
        $client = new Client();
        $response = $client->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . base64_encode('John Doe')
            ],
            'json' => [
                'order_id' => $this->orderId,
                'amount' => number_format($this->amount, 2, '.', ''),
                'timestamp' => $this->timestamp,
            ]
        ]);

        if ($response->successful()) {
            // Handle response
        } else {
            // Handle error
        }
    }
}
