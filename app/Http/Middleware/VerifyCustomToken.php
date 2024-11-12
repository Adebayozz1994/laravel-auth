<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCustomToken
{
    public function handle(Request $request, Closure $next)
    {
        // Retrieve the token from the Authorization header
        $token = $request->bearerToken(); 

        if (!$token) {
            return response()->json(['error' => 'Token not provided.'], 401);
        }

        // Find the admin with the provided token
        $admin = Admin::where('token', $token)->first();

        if (!$admin) {
            return response()->json(['error' => 'Invalid token.'], 401);
        }

        // Attach the admin user to the request
        $request->merge(['admin' => $admin]);

        return $next($request);
    }
}
