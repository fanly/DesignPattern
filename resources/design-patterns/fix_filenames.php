<?php

// 修复中文文件名，移除括号部分
$files_to_rename = [
    // creational
    'creational/工厂方法模式_(factory_method_pattern)_zh.md' => 'creational/factory_method_zh.md',
    'creational/建造者模式_(builder_pattern)_zh.md' => 'creational/builder_zh.md',
    'creational/原型模式_(prototype_pattern)_zh.md' => 'creational/prototype_zh.md',
    'creational/抽象工厂模式_(abstract_factory_pattern)_zh.md' => 'creational/abstract_factory_zh.md',
    'creational/单例模式_(singleton_pattern)_zh.md' => 'creational/singleton_zh.md',
    
    // behavioral
    'behavioral/中介者模式_(mediator_pattern)_zh.md' => 'behavioral/mediator_zh.md',
    'behavioral/命令模式_(command_pattern)_zh.md' => 'behavioral/command_zh.md',
    'behavioral/访问者模式_(visitor_pattern)_zh.md' => 'behavioral/visitor_zh.md',
    'behavioral/迭代器模式_(iterator_pattern)_zh.md' => 'behavioral/iterator_zh.md',
    'behavioral/备忘录模式_(memento_pattern)_zh.md' => 'behavioral/memento_zh.md',
    'behavioral/责任链模式_(chain_of_responsibility_pattern)_zh.md' => 'behavioral/chain_of_responsibility_zh.md',
    'behavioral/状态模式_(state_pattern)_zh.md' => 'behavioral/state_zh.md',
    'behavioral/解释器模式_(interpreter_pattern)_zh.md' => 'behavioral/interpreter_zh.md',
    'behavioral/策略模式_(strategy_pattern)_zh.md' => 'behavioral/strategy_zh.md',
    'behavioral/模板方法模式_(template_method_pattern)_zh.md' => 'behavioral/template_method_zh.md',
    
    // structural
    'structural/享元模式_(flyweight_pattern)_zh.md' => 'structural/flyweight_zh.md',
    'structural/组合模式_(composite_pattern)_zh.md' => 'structural/composite_zh.md',
    'structural/桥接模式_(bridge_pattern)_zh.md' => 'structural/bridge_zh.md',
    'structural/代理模式_(proxy_pattern)_zh.md' => 'structural/proxy_zh.md',
    'structural/适配器模式_(adapter_pattern)_zh.md' => 'structural/adapter_zh.md',
    'structural/装饰器模式_(decorator_pattern)_zh.md' => 'structural/decorator_zh.md',
];

$renamed_files = [];

foreach ($files_to_rename as $old_name => $new_name) {
    $old_path = "resources/design-patterns/laravel/{$old_name}";
    $new_path = "resources/design-patterns/laravel/{$new_name}";
    
    if (file_exists($old_path)) {
        if (rename($old_path, $new_path)) {
            $renamed_files[] = [
                'old' => $old_name,
                'new' => $new_name
            ];
            echo "重命名成功: {$old_name} -> {$new_name}\n";
        } else {
            echo "重命名失败: {$old_name}\n";
        }
    } else {
        echo "文件不存在: {$old_name}\n";
    }
}

echo "\n=== 重命名报告 ===\n";
echo "总计重命名: " . count($renamed_files) . " 个文件\n";

// 更新数据库Seeder中的路径
echo "\n=== 更新数据库路径 ===\n";

// 读取现有的Seeder文件
$seeder_path = 'database/seeders/DesignPatternsSeeder.php';
if (file_exists($seeder_path)) {
    $seeder_content = file_get_contents($seeder_path);
    
    // 更新路径映射
    foreach ($files_to_rename as $old_name => $new_name) {
        $old_pattern = str_replace('_(', '_', $old_name);
        $old_pattern = preg_replace('/_pattern\)_zh\.md$/', '_zh.md', $old_pattern);
        $old_pattern = str_replace('resources/design-patterns/', 'resources/design-patterns/laravel/', $old_pattern);
        
        $new_pattern = str_replace('resources/design-patterns/', 'resources/design-patterns/laravel/', $new_name);
        
        $seeder_content = str_replace($old_pattern, $new_pattern, $seeder_content);
    }
    
    file_put_contents($seeder_path, $seeder_content);
    echo "数据库Seeder路径已更新\n";
}

// 生成最终的文件列表
echo "\n=== 最终文件结构 ===\n";
exec('find resources/design-patterns/laravel -name "*.md" | sort', $final_files);

foreach ($final_files as $file) {
    echo $file . "\n";
}

echo "\n总计文件: " . count($final_files) . " 个\n";