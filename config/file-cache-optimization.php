<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 文件缓存优化配置
    |--------------------------------------------------------------------------
    |
    | 针对文件缓存环境的优化设置，避免文件系统I/O成为性能瓶颈。
    |
    */

    'optimizations' => [
        // 启用内存缓存预热
        'preload_frequent' => env('CACHE_PRELOAD_FREQUENT', true),
        
        // 缓存文件分片大小（KB）
        'chunk_size' => env('CACHE_CHUNK_SIZE', 1024),
        
        // 最大缓存文件数量
        'max_files' => env('CACHE_MAX_FILES', 1000),
        
        // 自动清理过期缓存间隔（分钟）
        'cleanup_interval' => env('CACHE_CLEANUP_INTERVAL', 60),
    ],
    
    'strategies' => [
        // 热门数据预加载策略
        'preload' => [
            'home_categories' => true,
            'pattern_index' => true,
            'frequent_patterns' => true,
        ],
        
        // 缓存压缩设置
        'compression' => [
            'enabled' => env('CACHE_COMPRESSION', false),
            'level' => env('CACHE_COMPRESSION_LEVEL', 6),
        ],
    ],
];