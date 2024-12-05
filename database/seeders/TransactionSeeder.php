<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::create([
            'wallet_id' => 1, // ID wallet yang valid
            'amount' => 500.00,
            'type' => 'deposit', // atau 'withdrawal'
            'status' => 'success', // atau 'failed'
        ]);
    }
}
