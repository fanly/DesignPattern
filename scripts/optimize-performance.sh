#!/bin/bash

# 性能优化脚本
echo "开始性能优化..."

# 1. 清除缓存
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 2. 优化自动加载
composer dump-autoload -o

# 3. 预编译视图
php artisan view:cache

# 4. 预编译路由
php artisan route:cache

# 5. 预编译配置
php artisan config:cache

# 6. 清除设计模式缓存
php artisan cache:patterns

# 7. 设置文件权限
chmod -R 755 storage bootstrap/cache

# 8. 启用OPcache（如果可用）
if command -v php > /dev/null 2>&1; then
    php -r "if (extension_loaded('Zend OPcache')) { opcache_reset(); }"
fi

echo "性能优化完成！"