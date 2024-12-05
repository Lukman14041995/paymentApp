<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisterController;
// Halaman utama, jika pengguna belum login, arahkan ke form login
Route::get('/', function () {
    // Jika sudah login, arahkan ke dashboard
    if (Auth::check()) {
        return redirect('/dashboard');
    }

    return redirect('/login'); // Jika belum login, arahkan ke halaman login
});

// Halaman Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
// Route untuk menampilkan form registrasi
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');

// Route untuk melakukan registrasi
Route::post('/register', [RegisterController::class, 'register']);
// Halaman Dashboard hanya bisa diakses oleh pengguna yang sudah login
Route::middleware(['auth'])->get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');

Route::middleware(['auth'])->get('/transactions/history', [PaymentController::class, 'transactionHistory'])->name('transactions.history');
// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Halaman Payment
Route::middleware(['auth'])->get('/payment', [PaymentController::class, 'showPaymentForm']);





// Deposit & Withdrawal routes
Route::middleware(['auth'])->post('/deposit', [PaymentController::class, 'deposit']);
Route::middleware(['auth'])->post('/withdrawal', [PaymentController::class, 'withdrawal']);
Route::middleware(['auth'])->post('/withdrawal', [PaymentController::class, 'withdrawal'])->name('withdrawal');
