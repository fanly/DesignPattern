<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 性能优化配置
    |--------------------------------------------------------------------------
    |
    | 这里配置应用程序的性能优化设置，包括缓存时间、预加载策略等。
    |
    */

    'cache' => [
        // 设计模式内容缓存时间（秒）
        'pattern_content' => env('CACHE_PATTERN_CONTENT', 3600), // 1小时
        
        // 设计模式目录缓存时间（秒）
        'pattern_headings' => env('CACHE_PATTERN_HEADINGS', 3600), // 1小时
        
        // 分类列表缓存时间（秒）
        'categories' => env('CACHE_CATEGORIES', 3600), // 1小时
        
        // 首页缓存时间（秒）
        'home' => env('CACHE_HOME', 1800), // 30分钟
    ],
    
    'database' => [
        // 数据库连接池大小
        'pool_size' => env('DB_POOL_SIZE', 10),
        
        // 查询日志记录
        'query_log' => env('DB_QUERY_LOG', false),
    ],
    
    'optimizations' => [
        // 启用Gzip压缩
        'gzip' => env('ENABLE_GZIP', true),
        
        // 启用OPcache
        'opcache' => env('ENABLE_OPCACHE', true),
        
        // 静态资源缓存时间（秒）
        'static_assets' => env('CACHE_STATIC_ASSETS', 86400), // 24小时
    ],
];