<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Book;
use OpenSDK\DuoMai\Client;

class DuomaiService
{
    private $appKey;
    private $appSecret;
    private $baseUrl;
    private $client;
    private $endPoint = 'cps-mesh.cpslink.jd.products.get';

    public function __construct()
    {
        $this->appKey = config('duomai.app_key');
        $this->appSecret = config('duomai.app_secret');
        $this->baseUrl = config('duomai.api_url', 'https://open.duomai.com');
        $this->client = new Client($this->appKey);
    }

    /**
     * 搜索设计模式相关书籍（带缓存机制）
     */
    public function searchDesignPatternBooks(): array
    {
        $cacheKey = 'duomai_design_pattern_books';
//
//        // 检查缓存
//        $cachedData = $this->getCachedData($cacheKey);
//        if ($cachedData !== null) {
//            Log::info('使用缓存数据');
//            return $cachedData;
//        }

        try {
            $timestamp = time();
            $keyword = '设计模式';
            $page = 1;
            $pageSize = 50;

            // 根据多麦API文档构建完整参数
            $params = [
                'app_key' => $this->appKey,
                'timestamp' => $timestamp,
                'service' => $this->endPoint,
            ];

            $header = [
                "Content-Type" => "application/json"
            ];

            $body = [
                "query"=> $keyword,
                'page' => $page,
                'page_size' => $pageSize
            ];
            // 生成签名
            $sign = $this->generateSignWithParams($params, $body);
            $params['sign'] = $sign;

            Log::info('Duomai API请求参数', $params);

            $response = Http::timeout(30)->withHeaders($header)->post(
                $this->baseUrl . '/apis', [
                    "verify" => false,
                    "headers" => $header,
                    "query" => $params,
                    "body" => $body
                ]
            );

            Log::info('Duomai API响应', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // 检查API响应格式
                if (isset($data['code']) && $data['code'] == 200) {
                    $booksData = $this->extractBooksData($data);
                    if (!empty($booksData)) {
                        // 缓存成功的数据
                        $this->setCachedData($cacheKey, $booksData, 30); // 缓存30分钟
                        return $booksData;
                    }
                }

                if (isset($data['status']) && $data['status'] == 200) {
                    $booksData = $this->extractBooksData($data);
                    if (!empty($booksData)) {
                        $this->setCachedData($cacheKey, $booksData, 30);
                        return $booksData;
                    }
                }

                // API返回业务错误
                Log::error('Duomai API业务错误', [
                    'response' => $data,
                    'params' => $params
                ]);
            } else {
                // HTTP请求失败
                Log::error('Duomai API请求失败', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'params' => $params
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Duomai API异常', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        // API调用失败时返回模拟数据（不缓存）
        Log::info('API调用失败，使用模拟数据');
        return $this->getMockBooksData();
    }

    /**
     * 从API响应中提取书籍数据
     */
    private function extractBooksData(array $apiResponse): array
    {
        if (isset($apiResponse['data']['list'])) {
            return $this->processBooksData($apiResponse['data']['list']);
        }

        if (isset($apiResponse['list'])) {
            return $this->processBooksData($apiResponse['list']);
        }

        if (isset($apiResponse['data'])) {
            return $this->processBooksData($apiResponse['data']);
        }

        return [];
    }

    /**
     * 根据多麦API文档生成签名
     * 参考：https://open.duomai.com/zh/docs/site/api-agreement
     */
    private function generateSignWithParams(array $params, $body): string
    {
        // 移除空值参数
        $params = array_filter($params, function($value) {
            return $value !== null && $value !== '';
        });

        ksort($params);
        $signStr = '';
        foreach ($params as $kev => $val) {
            $signStr .= $kev . $val;
        }
        $body = \GuzzleHttp\json_encode($body);
        return strtoupper(md5($this->appSecret . $signStr . $body . $this->appSecret));
    }

    /**
     * 检查是否需要缓存API响应
     * 根据API调用频率和数据更新频率决定缓存策略
     */
    private function shouldUseCache(): bool
    {
        // 检查上次API调用时间
        $lastCall = Cache::get('duomai_last_api_call');

        if (!$lastCall) {
            return false;
        }

        // 如果上次调用在5分钟内，使用缓存
        return now()->diffInMinutes($lastCall) < 5;
    }

    /**
     * 获取缓存数据
     */
    private function getCachedData(string $cacheKey): ?array
    {
        if ($this->shouldUseCache()) {
            return Cache::get($cacheKey);
        }

        return null;
    }

    /**
     * 设置缓存数据
     */
    private function setCachedData(string $cacheKey, array $data, int $minutes = 5): void
    {
        Cache::put($cacheKey, $data, now()->addMinutes($minutes));
        Cache::put('duomai_last_api_call', now(), now()->addMinutes(10));
    }

    /**
     * 获取模拟书籍数据（用于测试和API不可用时）
     */
    private function getMockBooksData(): array
    {
        return [
            [
                'title' => 'JavaScript设计模式与开发实践',
                'author' => '曾探',
                'publisher' => '人民邮电出版社',
                'isbn' => '9787115279460',
                'price' => '69.80',
                'original_price' => '79.80',
                'image_url' => 'https://img14.360buyimg.com/n1/s200x200_jfs/t1/123456/12/12345/123456/5f123456g12345678.jpg',
                'product_url' => 'https://item.jd.com/12345678901.html',
                'description' => 'JavaScript设计模式经典教程，深入浅出讲解设计模式在JavaScript中的应用',
                'publish_date' => '2023-01-01',
                'sales_volume' => 5000,
                'commission_rate' => '8.5',
                'commission_amount' => '5.93',
                'category' => '计算机/编程',
            ],
            [
                'title' => '设计模式之禅',
                'author' => '秦小波',
                'publisher' => '机械工业出版社',
                'isbn' => '9787111453077',
                'price' => '89.00',
                'original_price' => '99.00',
                'image_url' => 'https://img14.360buyimg.com/n1/s200x200_jfs/t1/234567/12/12345/123456/5f234567g23456789.jpg',
                'product_url' => 'https://item.jd.com/12345678902.html',
                'description' => '设计模式实战指南，结合大量实例讲解设计模式的应用',
                'publish_date' => '2022-05-15',
                'sales_volume' => 3000,
                'commission_rate' => '7.8',
                'commission_amount' => '6.94',
                'category' => '计算机/编程',
            ],
            [
                'title' => 'Head First 设计模式',
                'author' => 'Eric Freeman',
                'publisher' => '中国电力出版社',
                'isbn' => '9787508353937',
                'price' => '99.00',
                'original_price' => '119.00',
                'image_url' => 'https://img14.360buyimg.com/n1/s200x200_jfs/t1/345678/12/12345/123456/5f345678g34567890.jpg',
                'product_url' => 'https://item.jd.com/12345678903.html',
                'description' => 'Head First系列经典设计模式教程，图文并茂，易于理解',
                'publish_date' => '2021-08-20',
                'sales_volume' => 8000,
                'commission_rate' => '9.2',
                'commission_amount' => '9.11',
                'category' => '计算机/编程',
            ],
            [
                'title' => '设计模式：可复用面向对象软件的基础',
                'author' => 'Erich Gamma',
                'publisher' => '机械工业出版社',
                'isbn' => '9787111075756',
                'price' => '79.00',
                'original_price' => '89.00',
                'image_url' => 'https://img14.360buyimg.com/n1/s200x200_jfs/t1/456789/12/12345/123456/5f456789g45678901.jpg',
                'product_url' => 'https://item.jd.com/12345678904.html',
                'description' => '设计模式经典著作，GoF四人组的权威作品',
                'publish_date' => '2020-12-01',
                'sales_volume' => 10000,
                'commission_rate' => '8.0',
                'commission_amount' => '6.32',
                'category' => '计算机/编程',
            ]
        ];
    }

    /**
     * 生成签名
     */
    private function generateSign($timestamp, $keyword = '设计模式', $page = 1, $pageSize = 20): string
    {
        // 包含所有请求参数
        $params = [
            'app_key' => $this->appKey,
            'timestamp' => $timestamp,
            'keyword' => $keyword,
            'page' => $page,
            'page_size' => $pageSize,
        ];

        // 移除空值参数
        $params = array_filter($params, function($value) {
            return $value !== null && $value !== '';
        });

        ksort($params);
        $signString = '';

        foreach ($params as $key => $value) {
            $signString .= $key . $value;
        }

        $signString .= $this->appSecret;
        return md5($signString);
    }

    /**
     * 处理书籍数据
     */
    private function processBooksData(array $booksData): array
    {
        $processedBooks = [];

        foreach ($booksData as $bookData) {
            $book = [
                'title' => $bookData['title'] ?? '',
                'author' => $bookData['author'] ?? '',
                'publisher' => $bookData['publisher'] ?? '',
                'isbn' => $bookData['isbn'] ?? '',
                'price' => $bookData['price'] ?? 0,
                'original_price' => $bookData['original_price'] ?? $bookData['price'] ?? 0,
                'image_url' => $bookData['image_url'] ?? '',
                'product_url' => $bookData['product_url'] ?? '',
                'description' => $bookData['description'] ?? '',
                'publish_date' => $this->parsePublishDate($bookData['publish_date'] ?? ''),
                'sales_volume' => $bookData['sales_volume'] ?? 0,
                'commission_rate' => $bookData['commission_rate'] ?? 0,
                'commission_amount' => $bookData['commission_amount'] ?? 0,
                'category' => $bookData['category'] ?? '',
            ];

            $processedBooks[] = $book;
        }

        return $processedBooks;
    }

    /**
     * 解析出版日期
     */
    private function parsePublishDate($dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // 尝试多种日期格式
            $formats = ['Y-m-d', 'Y/m/d', 'Y年m月d日', 'Y.m.d'];

            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }

            // 如果无法解析，返回null
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * 更新书籍数据到数据库
     */
    public function updateBooksToDatabase(): int
    {
        $booksData = $this->searchDesignPatternBooks();
        $updatedCount = 0;

        foreach ($booksData as $bookData) {
            // 使用ISBN作为唯一标识
            $book = Book::where('isbn', $bookData['isbn'])->first();

            if (!$book) {
                $book = new Book();
            }

            $book->fill($bookData);
            $book->last_api_call = now();

            if ($book->save()) {
                $updatedCount++;
            }
        }

        // 清除缓存
        Book::clearCache();

        return $updatedCount;
    }

    /**
     * 测试API调用（用于调试）
     */
    public function testApiCall(): array
    {
        $timestamp = time();
        $sign = $this->generateSign($timestamp);

        $response = Http::timeout(30)->get($this->baseUrl . '/apis/cpslink/41/42', [
            'app_key' => $this->appKey,
            'timestamp' => $timestamp,
            'sign' => $sign,
            'keyword' => '设计模式',
            'page' => 1,
            'page_size' => 10,
        ]);

        return [
            'status' => $response->status(),
            'headers' => $response->headers(),
            'body' => $response->body(),
            'json' => $response->json(),
        ];
    }
}
