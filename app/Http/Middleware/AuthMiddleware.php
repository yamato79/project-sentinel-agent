<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the "X-SENTINEL-HEADER" header exists
        if (! $request->hasHeader('X-SENTINEL-HEADER')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Get the header value
        $sentinelHeaderValue = $request->header('X-SENTINEL-HEADER');

        // Compare with the hashed secret from config
        if (! Hash::check($sentinelHeaderValue, config('app.secret'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Continue to the next middleware or controller
        return $next($request);
    }
}
