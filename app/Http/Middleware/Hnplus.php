<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User_Access;

class Hnplus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
             $username = auth()->check() ? auth()->user()->username : null;

        if ($username && User_Access::where('username', $username)->where('hn_plus', 'Y')->exists()) {
            return $next($request);
        }

        abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
    }
}
