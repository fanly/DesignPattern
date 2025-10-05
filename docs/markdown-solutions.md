# Laravel Livewire Markdown 解析方案

## 方案一：league/commonmark + 自定义扩展 (推荐)

### 安装依赖
```bash
composer require league/commonmark
composer require spatie/commonmark-highlighter
npm install highlight.js mermaid
```

### 配置文件
```php
// config/markdown.php
<?php

return [
    'renderer' => [
        'block_separator' => "\n",
        'inner_separator' => "\n",
        'soft_break' => "\n",
    ],
    'enable_em' => true,
    'enable_strong' => true,
    'use_asterisk' => true,
    'use_underscore' => true,
    'html_input' => 'escape',
    'allow_unsafe_links' => false,
    'max_nesting_level' => PHP_INT_MAX,
    'extensions' => [
        // 代码高亮
        \Spatie\CommonMarkHighlighter\FencedCodeRenderer::class,
        // 表格支持
        \League\CommonMark\Extension\Table\TableExtension::class,
        // 任务列表
        \League\CommonMark\Extension\TaskList\TaskListExtension::class,
        // 自动链接
        \League\CommonMark\Extension\Autolink\AutolinkExtension::class,
    ],
];
```

### 自定义 Markdown 服务
```php
// app/Services/MarkdownService.php
<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use Spatie\CommonMarkHighlighter\FencedCodeRenderer;
use Spatie\CommonMarkHighlighter\HighlightCodeExtension;

class MarkdownService
{
    protected $converter;

    public function __construct()
    {
        $config = [
            'renderer' => [
                'block_separator' => "\n",
                'inner_separator' => "\n",
                'soft_break' => "\n",
            ],
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ];

        $environment = new Environment($config);
        
        // 添加扩展
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new TaskListExtension());
        $environment->addExtension(new AutolinkExtension());
        $environment->addExtension(new HighlightCodeExtension());

        $this->converter = new CommonMarkConverter($config, $environment);
    }

    public function parse(string $markdown): string
    {
        $html = $this->converter->convert($markdown)->getContent();
        
        // 处理 Mermaid 图表
        $html = $this->processMermaidDiagrams($html);
        
        return $html;
    }

    protected function processMermaidDiagrams(string $html): string
    {
        // 将 ```mermaid 代码块转换为 mermaid div
        $pattern = '/<pre><code class="language-mermaid">(.*?)<\/code><\/pre>/s';
        
        return preg_replace_callback($pattern, function ($matches) {
            $content = html_entity_decode($matches[1]);
            return '<div class="mermaid">' . $content . '</div>';
        }, $html);
    }
}
```

### Livewire 组件示例
```php
// app/Livewire/MarkdownViewer.php
<?php

namespace App\Livewire;

use App\Services\MarkdownService;
use Livewire\Component;

class MarkdownViewer extends Component
{
    public string $content = '';
    public string $parsedContent = '';

    protected $markdownService;

    public function boot(MarkdownService $markdownService)
    {
        $this->markdownService = $markdownService;
    }

    public function mount(string $content = '')
    {
        $this->content = $content;
        $this->parseContent();
    }

    public function updatedContent()
    {
        $this->parseContent();
    }

    protected function parseContent()
    {
        $this->parsedContent = $this->markdownService->parse($this->content);
    }

    public function render()
    {
        return view('livewire.markdown-viewer');
    }
}
```

### 前端视图
```blade
{{-- resources/views/livewire/markdown-viewer.blade.php --}}
<div class="markdown-container">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- 编辑器 -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Markdown 输入</label>
            <textarea 
                wire:model.live.debounce.500ms="content"
                class="w-full h-96 p-3 border border-gray-300 rounded-md font-mono text-sm"
                placeholder="输入 Markdown 内容..."
            ></textarea>
        </div>

        <!-- 预览 -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">预览</label>
            <div class="w-full h-96 p-3 border border-gray-300 rounded-md overflow-auto prose prose-sm max-w-none">
                {!! $parsedContent !!}
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <script>
        document.addEventListener('livewire:navigated', function () {
            mermaid.initialize({ startOnLoad: true });
        });
        
        document.addEventListener('livewire:updated', function () {
            mermaid.init(undefined, document.querySelectorAll('.mermaid'));
        });
    </script>
    @endpush

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11/styles/github.min.css">
    <style>
        .mermaid {
            text-align: center;
            margin: 1rem 0;
        }
        
        .prose pre {
            background-color: #f6f8fa;
            border-radius: 6px;
            padding: 1rem;
            overflow-x: auto;
        }
        
        .prose code {
            background-color: #f6f8fa;
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-size: 0.875em;
        }
    </style>
    @endpush
</div>
```

## 方案二：使用 Parsedown + 自定义扩展

### 安装
```bash
composer require erusev/parsedown
composer require erusev/parsedown-extra
```

### 自定义 Parsedown 类
```php
// app/Services/CustomParsedown.php
<?php

namespace App\Services;

use ParsedownExtra;

class CustomParsedown extends ParsedownExtra
{
    protected function blockFencedCode($Line)
    {
        $Block = parent::blockFencedCode($Line);
        
        if (isset($Block) && isset($Block['element']['text']['attributes']['class'])) {
            $language = str_replace('language-', '', $Block['element']['text']['attributes']['class']);
            
            // 处理 Mermaid 图表
            if ($language === 'mermaid') {
                return [
                    'element' => [
                        'name' => 'div',
                        'attributes' => [
                            'class' => 'mermaid'
                        ],
                        'text' => $Block['element']['text']['text']
                    ]
                ];
            }
        }
        
        return $Block;
    }
}
```

## 方案三：使用 Michelf Markdown + 扩展

### 安装
```bash
composer require michelf/php-markdown
```

## 推荐的最终方案

基于你的需求，我强烈推荐 **方案一**，因为：

1. **league/commonmark** 是最现代和可扩展的 PHP Markdown 解析器
2. 完美支持代码高亮（通过 highlight.js）
3. 可以轻松集成 Mermaid 图表
4. 与 Livewire 完美兼容
5. 性能优秀，社区活跃

### 安装步骤
```bash
# 1. 安装 PHP 依赖
composer require league/commonmark spatie/commonmark-highlighter

# 2. 安装前端依赖
npm install highlight.js mermaid

# 3. 发布配置文件
php artisan vendor:publish --provider="Spatie\CommonMarkHighlighter\CommonMarkHighlighterServiceProvider"
```

这个方案提供了：
- ✅ 完整的 Markdown 支持
- ✅ 语法高亮（支持 100+ 编程语言）
- ✅ Mermaid 流程图支持
- ✅ 表格、任务列表等扩展功能
- ✅ 与 Livewire 实时更新兼容
- ✅ 安全的 HTML 处理
- ✅ 高性能解析

需要我帮你实现这个方案吗？