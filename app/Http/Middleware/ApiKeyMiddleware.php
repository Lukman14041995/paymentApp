<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah API-Key ada di query string dan valid
        $apiKey = $request->query('API-Key'); // Ambil API-Key dari query string

        if ($apiKey !== '1234567890abcdef') { // Cek apakah API-Key valid
            return response()->json(['error' => 'Unauthorized'], 401); // Return Unauthorized jika tidak valid
        }

        return $next($request);
    }
}
