<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Pastikan DB diimport

class DashboardController extends Controller
{
    public function showDashboard()
    {
        // Ambil transaksi deposit dan withdrawal berdasarkan user yang login
        $deposits = Transaction::where('user_id', auth()->id())
            ->where('type', 'deposit')
            ->get();

        // Ambil saldo wallet berdasarkan user yang login
        $wallet = DB::table('wallets') // pastikan menggunakan tabel yang benar
            ->where('user_id', auth()->id())
            ->first();
        // Ambil transaksi withdrawal berdasarkan user yang login
        $withdrawals = Transaction::where('user_id', auth()->id())
            ->where('type', 'withdrawal')
            ->get();

        // Mengirim data transaksi dan saldo wallet ke view dashboard
        return view('dashboard', compact('deposits', 'withdrawals', 'wallet'));
    }
}
