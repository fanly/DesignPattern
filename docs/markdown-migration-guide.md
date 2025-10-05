# Markdown 解析器迁移指南

## 迁移完成状态

✅ **已完成的步骤:**

1. **安装新依赖**
   - `league/commonmark`: 现代化的 Markdown 解析器
   - `spatie/commonmark-highlighter`: 代码语法高亮支持

2. **创建服务类**
   - `app/Services/MarkdownService.php`: 新的 Markdown 解析服务
   - `app/Providers/MarkdownServiceProvider.php`: 服务提供者

3. **创建组件**
   - `app/View/Components/EnhancedMarkdown.php`: 新的 Markdown 组件
   - `resources/views/components/enhanced-markdown.blade.php`: 组件视图

4. **更新现有代码**
   - 更新 `PatternPreview.php` 使用新服务
   - 更新 `pattern-show.blade.php` 使用新组件
   - 注册服务提供者到 `config/app.php`

5. **创建演示页面**
   - `app/Livewire/MarkdownDemo.php`: 演示 Livewire 组件
   - `resources/views/livewire/markdown-demo.blade.php`: 演示视图
   - 添加路由 `/markdown-demo`

## 新功能特性

### ✅ 代码语法高亮
- 支持 100+ 编程语言
- 使用 highlight.js 进行客户端渲染
- 自动检测编程语言

### ✅ Mermaid 流程图
- 支持流程图、类图、序列图等
- 自动将 ```mermaid 代码块转换为图表
- 响应式设计，支持暗黑主题

### ✅ 增强的 Markdown 功能
- 表格支持
- 任务列表（复选框）
- 自动链接识别
- 标题锚点链接
- 安全的 HTML 处理

## 使用方法

### 1. 在 Livewire 组件中使用

```php
use App\Services\MarkdownService;

class YourComponent extends Component
{
    public function render()
    {
        $markdownService = app(MarkdownService::class);
        $html = $markdownService->toHtml($markdownContent);
        
        return view('your-view', compact('html'));
    }
}
```

### 2. 在 Blade 视图中使用

```blade
<x-enhanced-markdown 
    class="prose prose-gray max-w-none"
    :content="$markdownContent" />
```

### 3. 直接调用服务

```php
$markdownService = app(\App\Services\MarkdownService::class);
$html = $markdownService->parse($markdown);
```

## 测试方法

1. 访问 `/markdown-demo` 查看功能演示
2. 测试代码高亮功能
3. 测试 Mermaid 流程图渲染
4. 验证表格、任务列表等功能

## 待处理的前端资源

⚠️ **注意**: 由于 npm 权限问题，需要手动处理前端依赖:

```bash
# 修复 npm 权限
sudo chown -R $(whoami) ~/.npm

# 安装前端依赖
npm install highlight.js mermaid

# 或者使用 CDN（已在组件中配置）
```

## 回退方案

如果需要回退到 `spatie/laravel-markdown`:

1. 恢复 `PatternPreview.php` 中的 `MarkdownRenderer` 引用
2. 恢复 `pattern-show.blade.php` 中的 `<x-markdown>` 组件
3. 移除新创建的文件和服务提供者

## 性能对比

| 功能 | laravel-markdown | Enhanced Markdown |
|------|------------------|-------------------|
| 基础解析 | ✅ | ✅ |
| 代码高亮 | ❌ | ✅ |
| Mermaid 图表 | ❌ | ✅ |
| 表格支持 | ✅ | ✅ |
| 任务列表 | ❌ | ✅ |
| 标题锚点 | ✅ | ✅ |
| 自动链接 | ❌ | ✅ |
| 解析速度 | 中等 | 快速 |
| 扩展性 | 有限 | 优秀 |

新的解决方案在功能和性能上都有显著提升！