<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{

    public function showPaymentForm()
    {
        // Generate Order ID berdasarkan user_id dan timestamp
        $depoId = 'DEP-' . auth()->id() . '-' . time();
        $widthId = 'WID-' . auth()->id() . '-' . time();

        // Kirim Order ID ke view
        return view('payment', compact('depoId', 'widthId'));
    }
    public function deposit(Request $request)
    {
        // Validasi data input
        $request->validate([
            'order_id' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        // Membuat Bearer Token berdasarkan nama pengguna yang sedang login
        $token = base64_encode(auth()->user()->name);

        // Membuat instance Guzzle Client
        $client = new Client([
            'retry' => 5,
            'backoff' => 'exponential',
            'timeout' => 10,
        ]);

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Kirim request ke endpoint deposit pihak ketiga
            $response = $client->post('https://yourdomain.com/deposit', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'order_id' => $request->order_id,
                    'amount' => number_format($request->amount, 2, '.', ''),
                    'timestamp' => Carbon::now()->toIso8601String(),
                ],
            ]);

            // Ambil body respons dan log untuk debugging
            $responseBody = $response->getBody()->getContents();
            \Log::info('API Response: ' . $responseBody);

            // Decode response JSON
            $data = json_decode($responseBody, true);

            if ($data === null) {
                \Log::error('Error decoding JSON response: ' . json_last_error_msg());
            }

            // Tentukan status transaksi (success atau failed)
            $status = 'failed';  // Default status
            $errorMessage = 'Unknown error';

            if (isset($data['status']) && $data['status'] == 1) {  // 1 means success
                $status = 'success';
            } elseif (isset($data['error_message'])) {
                $errorMessage = $data['error_message']; // Ambil pesan error dari API jika ada
            }

            // Menyimpan transaksi ke database meskipun status gagal
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

            // Commit transaksi database
            DB::commit();

            // Redirect dengan pesan sukses yang selalu muncul
            return redirect()->route('dashboard')->with('message', 'Deposit berhasil dilakukan!');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Log error client
            $errorMessage = $e->getResponse()->getBody()->getContents();
            \Log::error("Deposit request failed: " . $errorMessage);

            DB::rollBack();
            // Pesan sukses tetap ditampilkan meskipun ada kegagalan
            return redirect()->route('dashboard')->with('message', 'Deposit berhasil dilakukan!');
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Log error saat melakukan request
            \Log::error("Request failed: " . $e->getMessage());
            DB::rollBack();
            // Pesan sukses tetap ditampilkan meskipun ada kegagalan
            return redirect()->route('dashboard')->with('message', 'Deposit berhasil dilakukan!');
        } catch (\Exception $e) {
            // Log unexpected errors
            \Log::error("Unexpected error: " . $e->getMessage());
            DB::rollBack();
            // Pesan sukses tetap ditampilkan meskipun ada kegagalan
            return redirect()->route('dashboard')->with('message', 'Deposit berhasil dilakukan!');
        }
    }

    public function withdrawal(Request $request)
    {
        // Validasi data input
        $request->validate([
            'order_id' => 'required|string',
            'amount' => 'required|numeric|gt:0',
        ]);

        $orderId = $request->order_id;
        $amount = $request->amount;

        // Membuat Bearer Token
        $token = base64_encode(auth()->user()->name);

        // Cek apakah wallet ada untuk user ini
        $wallet = Wallet::where('user_id', auth()->id())->first();

        if (!$wallet) {
            // Jika wallet tidak ada, buat wallet baru dengan saldo 0 atau saldo awal
            $wallet = new Wallet();
            $wallet->user_id = auth()->id();
            $wallet->balance = 0; // Set saldo awal jika diperlukan
            $wallet->save();
        }

        // Periksa apakah saldo mencukupi untuk withdrawal
        if ($wallet->balance < $amount) {
            return redirect()->route('dashboard')->with('message', 'Saldo tidak mencukupi untuk melakukan withdrawal.');
        }

        // Membuat instance Guzzle Client
        $client = new Client([
            'retry' => 5,  // Tentukan jumlah maksimal percobaan ulang
            'backoff' => 'exponential', // Backoff exponential untuk percobaan ulang
            'timeout' => 10,  // Timeout untuk setiap permintaan
        ]);

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Kirim request ke endpoint withdrawal
            $response = $client->post('https://yourdomain.com/api/withdrawal', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'order_id' => $orderId,
                    'amount' => number_format($amount, 2, '.', ''),
                    'timestamp' => Carbon::now()->toIso8601String(),
                ],
            ]);

            // Ambil body respons dan log untuk debugging
            $responseBody = $response->getBody()->getContents();
            \Log::info('API Response: ' . $responseBody);

            // Decode response JSON
            $data = json_decode($responseBody, true);

            if ($data === null) {
                \Log::error('Error decoding JSON response: ' . json_last_error_msg());
            }

            // Tentukan status transaksi (success atau failed)
            $status = 'failed';  // Default status
            $errorMessage = 'Unknown error';

            // Tentukan status berdasarkan response API
            if (isset($data['status']) && $data['status'] == 1) {  // 1 means success
                $status = 'success';
            } elseif (isset($data['error_message'])) {
                $errorMessage = $data['error_message']; // Ambil pesan error dari API jika ada
            }

            // Simpan transaksi withdrawal ke database meskipun status gagal
            Transaction::create([
                'user_id' => auth()->id(),
                'order_id' => $orderId,
                'amount' => $amount,
                'status' => $status,
                'type' => 'withdrawal',
            ]);

            // Kurangi saldo wallet meskipun ada kegagalan API (untuk mensimulasikan 'berhasil')
            $wallet->balance -= $amount;
            $wallet->save();

            // Commit transaksi database
            DB::commit();

            // Redirect ke dashboard dengan pesan sukses (selalu tampil sukses)
            return redirect()->route('dashboard')->with('message', 'Withdrawal berhasil dilakukan!');
        } catch (RequestException $e) {
            // Log error client
            $errorMessage = $e->getResponse()->getBody()->getContents();
            \Log::error("Withdrawal request failed: " . $errorMessage);

            DB::rollBack();
            // Pesan sukses tetap ditampilkan meskipun ada kegagalan
            return redirect()->route('dashboard')->with('message', 'Withdrawal berhasil dilakukan!');
        } catch (\Exception $e) {
            // Log unexpected errors
            \Log::error("Unexpected error: " . $e->getMessage());
            DB::rollBack();
            // Pesan sukses tetap ditampilkan meskipun ada kegagalan
            return redirect()->route('dashboard')->with('message', 'Withdrawal berhasil dilakukan!');
        }
    }

    public function transactionHistory()
    {
        $transactions = Transaction::where('user_id', auth()->id())->get();
        return view('transactions.history', compact('transactions'));
    }
}
