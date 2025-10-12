<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\DuomaiService;

class Book extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'author',
        'publisher',
        'isbn',
        'price',
        'original_price',
        'image_url',
        'product_url',
        'description',
        'publish_date',
        'sales_volume',
        'commission_rate',
        'commission_amount',
        'category',
        'last_api_call',
        'coupon_price',
        'final_price',
        'seller_name',
        'good_comment_rate',
        'comments_count',
        'item_id',
        'coupon_url',
        'coupon_quota',
        'coupon_start_time',
        'coupon_end_time',
    ];
    
    protected $casts = [
        'publish_date' => 'date',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'last_api_call' => 'datetime',
        'coupon_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'good_comment_rate' => 'decimal:2',
        'comments_count' => 'integer',
        'coupon_quota' => 'decimal:2',
        'coupon_start_time' => 'datetime:Y-m-d H:i:s',
        'coupon_end_time' => 'datetime:Y-m-d H:i:s',
    ];
    
    /**
     * 获取需要重新调用API的书籍（超过1小时未更新）
     */
    public static function needsRefresh()
    {
        return static::where(function ($query) {
            $query->whereNull('last_api_call')
                  ->orWhere('last_api_call', '<', now()->subHour());
        });
    }
    
    /**
     * 根据出版日期排序
     */
    public function scopeLatestPublished($query)
    {
        return $query->orderBy('publish_date', 'desc')
                    ->orderBy('sales_volume', 'desc');
    }
    
    /**
     * 搜索设计模式相关书籍
     */
    public function scopeDesignPatterns($query)
    {
        return $query->where('title', 'like', '%设计模式%')
                    ->orWhere('description', 'like', '%设计模式%')
                    ->orWhere('category', 'like', '%编程%')
                    ->orWhere('category', 'like', '%计算机%');
    }
    
    /**
     * 获取缓存键
     */
    public static function getCacheKey(): string
    {
        return 'design_pattern_books_' . app()->getLocale();
    }
    
    /**
     * 清除缓存
     */
    public static function clearCache(): void
    {
        Cache::forget(static::getCacheKey());
    }
    
    /**
     * 搜索设计模式相关书籍
     */
    public static function searchDesignPatternBooks()
    {
        $cacheKey = 'books_design_pattern';
        $cacheTime = 60; // 1小时缓存

        // 检查缓存
        if (Cache::has($cacheKey)) {
            Log::info('从缓存获取设计模式书籍数据');
            return collect(Cache::get($cacheKey));
        }

        try {
            $duomaiService = app(DuomaiService::class);
            $booksData = $duomaiService->searchDesignPatternBooks();
            
            if (empty($booksData)) {
                // 如果API调用失败，返回空集合
                return collect([]);
            }
            
            // 对书籍按价格和佣金排序，优先显示高价值书籍
            usort($booksData, function($a, $b) {
                $aScore = ($a['price'] ?? 0) * ($a['commission_rate'] ?? 0);
                $bScore = ($b['price'] ?? 0) * ($b['commission_rate'] ?? 0);
                return $bScore <=> $aScore;
            });

            // 缓存成功的数据
            Cache::put($cacheKey, $booksData, $cacheTime);
            Log::info('设计模式书籍数据已缓存，共' . count($booksData) . '本书');
            
            return collect($booksData);
            
        } catch (\Exception $e) {
            Log::error('搜索设计模式书籍失败', ['error' => $e->getMessage()]);
            return collect([]);
        }
    }
}