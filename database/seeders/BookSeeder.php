<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use Carbon\Carbon;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空现有数据
        Book::truncate();
        
        // 创建示例设计模式图书数据
        $books = [
            [
                'title' => '设计模式：可复用面向对象软件的基础',
                'author' => 'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides',
                'publisher' => '机械工业出版社',
                'isbn' => '9787111075759',
                'price' => 79.00,
                'original_price' => 89.00,
                'image_url' => 'https://img14.360buyimg.com/n1/jfs/t1/123456/12/12345/123456/5f123456g12345678.jpg',
                'product_url' => 'https://item.jd.com/1234567890.html',
                'description' => '本书是设计模式领域的经典之作，深入浅出地介绍了23种经典设计模式。',
                'publish_date' => '2020-01-01',
                'sales_volume' => 5000,
                'commission_rate' => 8.5,
                'commission_amount' => 6.72,
                'category' => '计算机/编程',
                'last_api_call' => now(),
            ],
            [
                'title' => 'Head First 设计模式',
                'author' => 'Eric Freeman, Elisabeth Robson',
                'publisher' => '中国电力出版社',
                'isbn' => '9787508353937',
                'price' => 68.00,
                'original_price' => 78.00,
                'image_url' => 'https://img14.360buyimg.com/n1/jfs/t1/234567/12/12345/123456/5f234567g23456789.jpg',
                'product_url' => 'https://item.jd.com/2345678901.html',
                'description' => '以独特的方式讲解设计模式，让学习变得轻松有趣。',
                'publish_date' => '2021-03-15',
                'sales_volume' => 3000,
                'commission_rate' => 7.2,
                'commission_amount' => 4.90,
                'category' => '计算机/编程',
                'last_api_call' => now(),
            ],
            [
                'title' => '设计模式之禅',
                'author' => '秦小波',
                'publisher' => '机械工业出版社',
                'isbn' => '9787111407010',
                'price' => 59.00,
                'original_price' => 69.00,
                'image_url' => 'https://img14.360buyimg.com/n1/jfs/t1/345678/12/12345/123456/5f345678g34567890.jpg',
                'product_url' => 'https://item.jd.com/3456789012.html',
                'description' => '从实战角度讲解设计模式的应用，结合大量实例。',
                'publish_date' => '2022-06-20',
                'sales_volume' => 2500,
                'commission_rate' => 6.8,
                'commission_amount' => 4.01,
                'category' => '计算机/编程',
                'last_api_call' => now(),
            ],
            [
                'title' => 'JavaScript设计模式与开发实践',
                'author' => '曾探',
                'publisher' => '人民邮电出版社',
                'isbn' => '9787115360659',
                'price' => 49.00,
                'original_price' => 59.00,
                'image_url' => 'https://img14.360buyimg.com/n1/jfs/t1/456789/12/12345/123456/5f456789g45678901.jpg',
                'product_url' => 'https://item.jd.com/4567890123.html',
                'description' => '专门针对JavaScript的设计模式讲解，适合前端开发者。',
                'publish_date' => '2023-02-10',
                'sales_volume' => 1800,
                'commission_rate' => 5.5,
                'commission_amount' => 2.70,
                'category' => '计算机/前端开发',
                'last_api_call' => now(),
            ],
        ];
        
        foreach ($books as $bookData) {
            Book::create($bookData);
        }
        
        $this->command->info('成功创建 ' . count($books) . ' 本设计模式图书示例数据');
    }
}