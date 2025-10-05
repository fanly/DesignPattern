<?php

// 设计模式分类映射
$pattern_categories = [
    'creational' => [
        'factory_method', 'abstract_factory', 'builder', 'prototype', 'singleton'
    ],
    'structural' => [
        'adapter', 'bridge', 'composite', 'decorator', 'facade', 'flyweight', 'proxy'
    ],
    'behavioral' => [
        'chain_of_responsibility', 'command', 'interpreter', 'iterator', 'mediator',
        'memento', 'observer', 'state', 'strategy', 'template_method', 'visitor'
    ]
];

// 获取所有不包含"待补充"的Markdown文件
$files = [];
exec('find resources/patterns -name "*.md" -exec grep -L "待补充" {} \;', $files);

$moved_files = [];

foreach ($files as $file) {
    // 读取文件内容获取模式名称
    $content = file_get_contents($file);
    
    // 从文件内容中提取模式名称
    $pattern_name = '';
    if (preg_match('/^#\s+(.+?)\s+(?:Pattern)?\s*$/m', $content, $matches)) {
        $pattern_name = strtolower($matches[1]);
        
        // 处理中文模式名称
        if (strpos($pattern_name, '模式') !== false) {
            // 提取英文名称部分
            if (preg_match('/^([a-z\s]+)\s+模式/i', $pattern_name, $chinese_matches)) {
                $pattern_name = trim($chinese_matches[1]);
            }
        }
        
        // 标准化模式名称
        $pattern_name = str_replace(' ', '_', $pattern_name);
    }
    
    // 如果无法从内容提取，尝试从文件名推断
    if (empty($pattern_name)) {
        $filename = basename($file);
        if (preg_match('/(factory|builder|prototype|singleton|adapter|bridge|composite|decorator|facade|flyweight|proxy|chain|command|interpreter|iterator|mediator|memento|observer|state|strategy|template|visitor)/i', $filename, $matches)) {
            $pattern_name = strtolower($matches[1]);
        }
    }
    
    // 确定文件类型（英文或中文）
    $file_type = (strpos($file, '_en.md') !== false) ? 'en' : 'zh';
    
    // 确定目标目录
    $target_category = '';
    foreach ($pattern_categories as $category => $patterns) {
        foreach ($patterns as $pattern) {
            if (strpos($pattern_name, $pattern) !== false) {
                $target_category = $category;
                break 2;
            }
        }
    }
    
    if (empty($target_category)) {
        echo "无法分类文件: $file (模式: $pattern_name)\n";
        continue;
    }
    
    // 构建目标文件名
    $target_filename = $pattern_name . '_' . $file_type . '.md';
    $target_path = "resources/design-patterns/laravel/{$target_category}/{$target_filename}";
    
    // 移动文件
    if (copy($file, $target_path)) {
        $moved_files[] = [
            'source' => $file,
            'target' => $target_path,
            'pattern' => $pattern_name,
            'category' => $target_category,
            'language' => $file_type
        ];
        echo "移动成功: {$file} -> {$target_path}\n";
    } else {
        echo "移动失败: {$file}\n";
    }
}

// 生成分类报告
echo "\n=== 分类报告 ===\n";
$category_count = [];
foreach ($moved_files as $moved) {
    $category = $moved['category'];
    $category_count[$category] = ($category_count[$category] ?? 0) + 1;
}

foreach ($category_count as $category => $count) {
    echo "{$category}: {$count} 个文件\n";
}

echo "总计移动: " . count($moved_files) . " 个文件\n";

// 保存移动记录到JSON文件
file_put_contents('resources/design-patterns/move_log.json', json_encode($moved_files, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));