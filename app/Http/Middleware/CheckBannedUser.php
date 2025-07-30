<?php
// app/Http/Middleware/CheckBannedUser.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBannedUser
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if authenticated user is banned
        if (auth()->check() && auth()->user()->status === 'banned') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah diblokir. Silakan hubungi administrator.');
        }

        return $next($request);
    }
}