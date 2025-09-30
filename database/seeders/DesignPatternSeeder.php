<?php

namespace Database\Seeders;

use App\Models\DesignPattern;
use App\Models\PatternCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DesignPatternSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 创建设计模式分类
        $categories = [
            [
                'name' => '创建型模式',
                'description' => '关注对象的创建机制，试图以适合当前情况的方式创建对象',
                'sort_order' => 1
            ],
            [
                'name' => '结构型模式',
                'description' => '关注类和对象的组合，形成更大的结构',
                'sort_order' => 2
            ],
            [
                'name' => '行为型模式',
                'description' => '关注对象之间的通信和职责分配',
                'sort_order' => 3
            ]
        ];

        foreach ($categories as $categoryData) {
            // 为中文分类名称生成唯一的slug
            $categorySlug = Str::slug($categoryData['name']);
            if (empty($categorySlug)) {
                $categorySlug = 'category-' . uniqid();
            }
            
            $category = PatternCategory::create([
                'name' => $categoryData['name'],
                'slug' => $categorySlug,
                'description' => $categoryData['description'],
                'sort_order' => $categoryData['sort_order']
            ]);

            // 为每个分类创建一些设计模式
            $patterns = $this->getPatternsForCategory($categoryData['name']);
            
            foreach ($patterns as $patternData) {
                // 为中文名称生成唯一的slug
                $slug = Str::slug($patternData['name']) ?: Str::slug($patternData['name'] . '-' . uniqid());
                
                $pattern = DesignPattern::create([
                    'category_id' => $category->id,
                    'name' => $patternData['name'],
                    'slug' => $slug,
                    'description' => $patternData['description'],
                    'sort_order' => $patternData['sort_order'],
                    'is_published' => true
                ]);

                // 创建初始Markdown内容
                $content = "# {$pattern->name}\n\n## 概述\n\n{$pattern->description}\n\n## Laravel中的实现\n\n待补充...";
                $pattern->saveContent($content);
            }
        }
    }

    protected function getPatternsForCategory(string $categoryName): array
    {
        return match ($categoryName) {
            '创建型模式' => [
                [
                    'name' => '工厂方法模式',
                    'description' => '定义一个创建对象的接口，但让子类决定实例化哪个类',
                    'sort_order' => 1
                ],
                [
                    'name' => '抽象工厂模式',
                    'description' => '提供一个创建一系列相关或相互依赖对象的接口，而无需指定它们具体的类',
                    'sort_order' => 2
                ],
                [
                    'name' => '单例模式',
                    'description' => '确保一个类只有一个实例，并提供一个全局访问点',
                    'sort_order' => 3
                ]
            ],
            '结构型模式' => [
                [
                    'name' => '适配器模式',
                    'description' => '将一个类的接口转换成客户希望的另一个接口',
                    'sort_order' => 1
                ],
                [
                    'name' => '装饰器模式',
                    'description' => '动态地给一个对象添加一些额外的职责',
                    'sort_order' => 2
                ],
                [
                    'name' => '外观模式',
                    'description' => '为子系统中的一组接口提供一个一致的界面',
                    'sort_order' => 3
                ]
            ],
            '行为型模式' => [
                [
                    'name' => '策略模式',
                    'description' => '定义一系列算法，把它们一个个封装起来，并且使它们可相互替换',
                    'sort_order' => 1
                ],
                [
                    'name' => '观察者模式',
                    'description' => '定义对象间的一种一对多的依赖关系，当一个对象的状态发生改变时，所有依赖于它的对象都得到通知并被自动更新',
                    'sort_order' => 2
                ],
                [
                    'name' => '命令模式',
                    'description' => '将一个请求封装为一个对象，从而使你可用不同的请求对客户进行参数化',
                    'sort_order' => 3
                ]
            ],
            default => []
        };
    }
}
