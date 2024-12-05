<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    // Fungsi Deposit melalui API
    public function deposit(Request $request)
    {
        // Validasi data input
        $request->validate([
            'order_id' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        // API Key untuk autentikasi
        $apiKey = $request->query('API-Key'); // API-Key dikirimkan di query string

        if ($apiKey !== '1234567890abcdef') {
            // Jika API Key tidak valid, kembalikan error
            \Log::error('Invalid API Key: ' . $apiKey);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Membuat instance Guzzle Client
        $client = new Client([
            'retry' => 5,
            'backoff' => 'exponential',
            'timeout' => 10,
        ]);

        try {
            // Log informasi tentang permintaan API
            \Log::info('Sending deposit request to external API with data:', [
                'order_id' => $request->order_id,
                'amount' => $request->amount,
                'timestamp' => Carbon::now()->toIso8601String()
            ]);

            // Kirim request ke endpoint deposit pihak ketiga dengan API-Key di query string
            $response = $client->post(env('API_URL'), [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('API_TOKEN'),
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'API-Key' => $apiKey, // Menambahkan API-Key sebagai query parameter
                ],
                'json' => [
                    'order_id' => $request->order_id,
                    'amount' => $request->amount,
                    'timestamp' => Carbon::now()->toIso8601String(),
                ],
            ]);

            // Ambil body respons dan log untuk debugging
            $responseBody = $response->getBody()->getContents();

            // Log status code dan body response
            \Log::info('API Response Status: ' . $response->getStatusCode());
            \Log::info('API Response Body: ' . $responseBody);

            // Decode response JSON
            $data = json_decode($responseBody, true);

            // Log request data
            \Log::info('Request Data:', $request->all());

            if ($data === null) {
                $errorMessage = 'Error decoding JSON response: ' . json_last_error_msg();
                \Log::error($errorMessage);
                return response()->json(['error' => $errorMessage], 500);
            }

            // Tentukan status transaksi (success atau failed)
            $status = 'failed';  // Default status
            $errorMessage = 'Unknown error';

            if (isset($data['status']) && $data['status'] == 'success') {
                $status = 'success';
            } elseif (isset($data['error_message'])) {
                $errorMessage = $data['error_message']; // Ambil pesan error dari API jika ada
                \Log::error('API error message: ' . $errorMessage);
            }

            // Menyimpan transaksi ke database
            Transaction::create([
                'user_id' => auth()->id(),
                'order_id' => $request->order_id,
                'amount' => $request->amount,
                'status' => $status,
                'type' => 'deposit',
            ]);

            // Update saldo wallet pengguna setelah deposit
            $wallet = Wallet::where('user_id', auth()->id())->first();

            if (!$wallet) {
                // Jika wallet tidak ada, buat wallet baru dengan saldo 0 atau saldo awal
                $wallet = new Wallet();
                $wallet->user_id = auth()->id();
                $wallet->balance = 0; // Set saldo awal jika diperlukan
                $wallet->save();
            }

            // Tambahkan jumlah deposit ke saldo wallet
            $wallet->balance += $request->amount;
            $wallet->save();

            // Mengirimkan response sukses
            return response()->json(['message' => 'Deposit successful', 'status' => $status]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Log error client
            $errorMessage = $e->getResponse()->getBody()->getContents();
            \Log::error("Deposit request failed: " . $errorMessage);
            return response()->json(['error' => 'Deposit failed. Please try again later.'], 500);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Log error saat melakukan request
            \Log::error("Request failed: " . $e->getMessage());
            return response()->json(['error' => 'Network error occurred. Please try again later.'], 500);
        } catch (\Exception $e) {
            // Log unexpected errors
            \Log::error("Unexpected error: " . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }


    // Fungsi Withdrawal melalui API
    public function withdrawal(Request $request)
    {
        // Validasi input dari pihak ketiga
        $request->validate([
            'order_id' => 'required|string',
            'amount' => 'required|numeric|gt:0',
        ]);

        $orderId = $request->order_id;
        $amount = $request->amount;

        // Cek apakah wallet ada untuk user ini
        $wallet = Wallet::where('user_id', auth()->id())->first();

        if (!$wallet) {
            // Jika wallet tidak ada, buat wallet baru dengan saldo 0
            $wallet = new Wallet();
            $wallet->user_id = auth()->id();
            $wallet->balance = 0;
            $wallet->save();

            return response()->json(['status' => 'failed', 'message' => 'Wallet tidak ditemukan, wallet baru telah dibuat dengan saldo 0.'], 400);
        }

        // Periksa apakah saldo mencukupi untuk withdrawal
        if ($wallet->balance < $amount) {
            return response()->json(['status' => 'failed', 'message' => 'Saldo tidak mencukupi untuk melakukan withdrawal.'], 400);
        }

        // Simpan transaksi withdrawal
        $transaction = new Transaction();
        $transaction->user_id = auth()->id();
        $transaction->order_id = $orderId;
        $transaction->amount = $amount;
        $transaction->status = 'success';
        $transaction->type = 'withdrawal';
        $transaction->save();

        // Kurangi saldo wallet
        $wallet->balance -= $amount;
        $wallet->save();

        return response()->json(['status' => 'success', 'message' => 'Withdrawal berhasil dilakukan!'], 200);
    }
}
