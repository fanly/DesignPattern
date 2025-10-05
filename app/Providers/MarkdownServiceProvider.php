<?php

namespace App\Providers;

use App\Services\MarkdownService;
use Illuminate\Support\ServiceProvider;

class MarkdownServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(MarkdownService::class, function ($app) {
            return new MarkdownService();
        });

        // 为了兼容现有代码，也注册一个别名
        $this->app->alias(MarkdownService::class, 'markdown');
    }

    public function boot()
    {
        //
    }
}