<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        
        // Admin değilse yetkilendirme hatası göster
        if (!$user->isAdmin()) {
            return redirect('/')->with('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        return $next($request);
    }
}