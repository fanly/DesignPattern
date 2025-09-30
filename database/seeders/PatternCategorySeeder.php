<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PatternCategory;

class PatternCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name_zh' => '创建型模式',
                'name_en' => 'Creational Patterns',
                'slug' => 'creational-patterns',
                'description_zh' => '关注对象创建机制，提高系统灵活性',
                'description_en' => 'Focus on object creation mechanisms to improve system flexibility',
                'sort_order' => 1,
            ],
            [
                'name_zh' => '结构型模式',
                'name_en' => 'Structural Patterns',
                'slug' => 'structural-patterns',
                'description_zh' => '处理类或对象的组合，形成更大结构',
                'description_en' => 'Handle class or object composition to form larger structures',
                'sort_order' => 2,
            ],
            [
                'name_zh' => '行为型模式',
                'name_en' => 'Behavioral Patterns',
                'slug' => 'behavioral-patterns',
                'description_zh' => '关注对象间的通信和职责分配',
                'description_en' => 'Focus on communication and responsibility distribution between objects',
                'sort_order' => 3,
            ],
        ];

        foreach ($categories as $category) {
            PatternCategory::create($category);
        }
    }
}
