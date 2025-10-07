<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PerformanceServiceProvider extends ServiceProvider
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
        // 记录慢查询
        if (config('performance.database.query_log', false)) {
            DB::listen(function ($query) {
                if ($query->time > 100) { // 超过100毫秒的查询
                    Log::warning('Slow Query Detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time,
                        'connection' => $query->connectionName,
                    ]);
                }
            });
        }
        
        // 注册缓存清理事件
        $this->registerCacheEvents();
    }
    
    /**
     * 注册缓存清理事件
     */
    protected function registerCacheEvents(): void
    {
        // 当设计模式被更新时清除相关缓存
        \App\Models\DesignPattern::updated(function ($pattern) {
            $pattern->clearCache();
        });
        
        // 当设计模式被删除时清除相关缓存
        \App\Models\DesignPattern::deleted(function ($pattern) {
            $pattern->clearCache();
        });
        
        // 当分类被更新时清除相关缓存
        \App\Models\PatternCategory::updated(function ($category) {
            Cache::forget('home_categories_zh');
            Cache::forget('home_categories_en');
            Cache::forget('pattern_index_categories_zh');
            Cache::forget('pattern_index_categories_en');
        });
    }
}