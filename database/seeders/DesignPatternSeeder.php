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
                'name_zh' => $categoryData['name'],
                'name_en' => $this->getCategoryEnglishName($categoryData['name']),
                'slug' => $categorySlug,
                'description_zh' => $categoryData['description'],
                'description_en' => $this->getCategoryEnglishDescription($categoryData['description']),
                'sort_order' => $categoryData['sort_order']
            ]);

            // 为每个分类创建一些设计模式
            $patterns = $this->getPatternsForCategory($categoryData['name']);
            
            foreach ($patterns as $patternData) {
                // 为中文名称生成唯一的slug
                $slug = Str::slug($patternData['name']) ?: Str::slug($patternData['name'] . '-' . uniqid());
                
                // 获取英文翻译
                $englishData = $this->getEnglishTranslation($patternData['name'], $patternData['description']);
                
                $pattern = DesignPattern::create([
                    'category_id' => $category->id,
                    'name_zh' => $patternData['name'],
                    'name_en' => $englishData['name'],
                    'slug' => $slug,
                    'description_zh' => $patternData['description'],
                    'description_en' => $englishData['description'],
                    'sort_order' => $patternData['sort_order'],
                    'is_published' => true
                ]);

                // 创建中文Markdown内容
                $chineseContent = "# {$patternData['name']}\n\n## 概述\n\n{$patternData['description']}\n\n## Laravel中的实现\n\n待补充...";
                $pattern->saveContent($chineseContent, 'zh');
                
                // 创建英文Markdown内容
                $englishContent = "# {$englishData['name']}\n\n## Overview\n\n{$englishData['description']}\n\n## Implementation in Laravel\n\nTo be added...";
                $pattern->saveContent($englishContent, 'en');
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
    
    protected function getEnglishTranslation(string $chineseName, string $chineseDescription): array
    {
        $translations = [
            '工厂方法模式' => [
                'name' => 'Factory Method Pattern',
                'description' => 'Define an interface for creating an object, but let subclasses decide which class to instantiate'
            ],
            '抽象工厂模式' => [
                'name' => 'Abstract Factory Pattern',
                'description' => 'Provide an interface for creating families of related or dependent objects without specifying their concrete classes'
            ],
            '单例模式' => [
                'name' => 'Singleton Pattern',
                'description' => 'Ensure a class has only one instance and provide a global point of access to it'
            ],
            '适配器模式' => [
                'name' => 'Adapter Pattern',
                'description' => 'Convert the interface of a class into another interface clients expect'
            ],
            '装饰器模式' => [
                'name' => 'Decorator Pattern',
                'description' => 'Attach additional responsibilities to an object dynamically'
            ],
            '外观模式' => [
                'name' => 'Facade Pattern',
                'description' => 'Provide a unified interface to a set of interfaces in a subsystem'
            ],
            '策略模式' => [
                'name' => 'Strategy Pattern',
                'description' => 'Define a family of algorithms, encapsulate each one, and make them interchangeable'
            ],
            '观察者模式' => [
                'name' => 'Observer Pattern',
                'description' => 'Define a one-to-many dependency between objects so that when one object changes state, all its dependents are notified and updated automatically'
            ],
            '命令模式' => [
                'name' => 'Command Pattern',
                'description' => 'Encapsulate a request as an object, thereby letting you parameterize clients with different requests'
            ]
        ];
        
        return $translations[$chineseName] ?? [
            'name' => $chineseName . ' (English)',
            'description' => $chineseDescription . ' (English translation needed)'
        ];
    }
    
    protected function getCategoryEnglishName(string $chineseName): string
    {
        $translations = [
            '创建型模式' => 'Creational Patterns',
            '结构型模式' => 'Structural Patterns',
            '行为型模式' => 'Behavioral Patterns'
        ];
        
        return $translations[$chineseName] ?? $chineseName . ' (English)';
    }
    
    protected function getCategoryEnglishDescription(string $chineseDescription): string
    {
        $translations = [
            '关注对象的创建机制，试图以适合当前情况的方式创建对象' => 'Focus on object creation mechanisms, trying to create objects in a manner suitable to the situation',
            '关注类和对象的组合，形成更大的结构' => 'Focus on class and object composition, forming larger structures',
            '关注对象之间的通信和职责分配' => 'Focus on communication between objects and responsibility assignment'
        ];
        
        return $translations[$chineseDescription] ?? $chineseDescription . ' (English translation needed)';
    }
}
