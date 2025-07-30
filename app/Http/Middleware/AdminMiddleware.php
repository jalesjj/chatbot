<?php
// app/Http/Middleware/AdminMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is admin
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Akses ditolak. Anda tidak memiliki izin admin.');
        }

        return $next($request);
    }
}