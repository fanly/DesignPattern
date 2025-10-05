<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use Spatie\CommonMarkHighlighter\FencedCodeRenderer;
use Spatie\CommonMarkHighlighter\IndentedCodeRenderer;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;

class MarkdownService
{
    protected $converter;

    public function __construct()
    {
        $config = [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'heading_permalink' => [
                'html_class' => 'heading-permalink',
                'id_prefix' => '',
                'fragment_prefix' => '',
                'insert' => 'before',
                'title' => 'Permalink',
                'symbol' => '#',
                'aria_hidden' => true,
            ],
        ];

        // 使用预配置的环境
        $environment = Environment::createCommonMarkEnvironment();
        
        // 添加额外扩展
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new TaskListExtension());
        $environment->addExtension(new AutolinkExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        // 添加代码高亮渲染器
        $environment->addRenderer(FencedCode::class, new FencedCodeRenderer());
        $environment->addRenderer(IndentedCode::class, new IndentedCodeRenderer());

        $this->converter = new MarkdownConverter($environment, $config);
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
            return '<div class="mermaid">' . trim($content) . '</div>';
        }, $html);
    }

    public function toHtml(string $markdown): string
    {
        return $this->parse($markdown);
    }
}