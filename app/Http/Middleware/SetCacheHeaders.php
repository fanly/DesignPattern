<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 只对成功的GET请求设置缓存
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            // 静态资源缓存1年
            if ($request->is('build/*') || $request->is('images/*')) {
                $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            }
            // HTML页面缓存1小时
            else {
                $response->headers->set('Cache-Control', 'public, max-age=3600');
            }
            
            // 移除不必要的头信息
            $response->headers->remove('Pragma');
            $response->headers->remove('Expires');
        }

        return $response;
    }
}