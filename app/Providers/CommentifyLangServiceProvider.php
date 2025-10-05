<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CommentifyLangServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 注册 Commentify 包的语言包命名空间
        $this->loadTranslationsFrom(
            base_path('lang/vendor/commentify'),
            'commentify'
        );
    }
}