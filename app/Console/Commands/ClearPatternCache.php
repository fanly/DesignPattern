<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearPatternCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:patterns {--all : Clear all pattern caches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear design patterns related cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            // 清除所有缓存
            Cache::flush();
            $this->info('All cache cleared successfully.');
            return;
        }
        
        // 清除首页分类缓存
        Cache::forget('home_categories_zh');
        Cache::forget('home_categories_en');
        
        // 清除设计模式列表缓存
        Cache::forget('pattern_index_categories_zh');
        Cache::forget('pattern_index_categories_en');
        
        // 清除所有设计模式内容和目录缓存
        $patternKeys = Cache::get('pattern_keys', []);
        foreach ($patternKeys as $key) {
            Cache::forget($key);
        }
        Cache::forget('pattern_keys');
        
        $this->info('Design patterns cache cleared successfully.');
    }
}