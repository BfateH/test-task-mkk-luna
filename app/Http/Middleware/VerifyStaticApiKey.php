<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyStaticApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = '';

        if($request->header('X-API-Key')) {
            $apiKey = $request->header('X-API-Key');
        }

        if(!$apiKey && $request->query('api_key')) {
            $apiKey = $request->query('api_key');
        }

        if ($apiKey !== config('services.api.static_key')) {
            return response()->json([
                'data' => [
                    'error' => 'Invalid API key'
                ]
            ], 401);
        }

        return $next($request);
    }
}
