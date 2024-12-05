<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessTokenResult;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', function (Request $request) {
    // Mencari pengguna berdasarkan email atau identifier lain (misalnya ID)
    $user = User::find(1);  // Mengambil pengguna dengan ID 1, atau bisa menggunakan data lain seperti email: User::where('email', $request->email)->first();

    // Cek jika pengguna ditemukan
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Buat token untuk pengguna ini
    $token = $user->createToken('YourApp')->plainTextToken;

    // Kirimkan token kepada pengguna
    return response()->json(['token' => $token]);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// routes/api.php

// routes/api.php

Route::post('/deposit', [PaymentController::class, 'deposit']);
