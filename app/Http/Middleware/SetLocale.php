<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 从session获取语言设置
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            // 默认使用中文
            App::setLocale('zh');
            Session::put('locale', 'zh');
        }
        
        return $next($request);
    }
}
