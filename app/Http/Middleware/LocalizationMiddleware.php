<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 检查会话中是否有语言设置
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            // 如果没有设置，使用默认语言
            Session::put('locale', config('app.locale'));
            App::setLocale(config('app.locale'));
        }

        return $next($request);
    }
}