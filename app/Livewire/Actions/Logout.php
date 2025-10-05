<?php

namespace App\Livewire\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke(Request $request)
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        // 获取重定向地址，如果不存在则重定向到首页
        $redirectTo = $request->input('redirect_to', '/');
        
        return redirect($redirectTo);
    }
}
