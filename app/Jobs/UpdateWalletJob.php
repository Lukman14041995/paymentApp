<?php

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Jobs\Job;
use Illuminate\Http\Client\Factory as Http;
use GuzzleHttp\Client;
use App\Models\Wallet;

class UpdateWalletJob implements ShouldQueue
{
    protected $wallet;
    protected $amount;

    public function __construct(Wallet $wallet, $amount)
    {
        $this->wallet = $wallet;
        $this->amount = $amount;
    }

    public function handle()
    {
        $this->wallet->balance += $this->amount;
        $this->wallet->save();
    }
}
