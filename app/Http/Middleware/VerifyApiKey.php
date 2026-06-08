<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');
        $validKey = env('API_KEY_SITEPAD');

        if (!$apiKey || $apiKey !== $validKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid API Key'
            ], 401);
        }

        return $next($request);
    }
}
