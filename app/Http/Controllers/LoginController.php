<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Wallet;

class LoginController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('login');
    }

    public function showDashboard()
    {
        // Ambil transaksi deposit dan withdrawal berdasarkan user yang login
        $deposits = Transaction::where('user_id', auth()->id())
            ->where('type', 'deposit')
            ->get();

        $withdrawals = Transaction::where('user_id', auth()->id())
            ->where('type', 'withdrawal')
            ->get();

        return view('dashboard', compact('deposits', 'withdrawals'));
    }
    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ]);

        if (Auth::attempt($credentials)) {
            // Jika login berhasil, regenerasi session untuk mencegah session fixation
            $request->session()->regenerate();
            return redirect()->intended('/dashboard'); // Redirect ke halaman dashboard setelah login
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
