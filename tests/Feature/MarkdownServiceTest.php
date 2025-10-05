<?php

namespace Tests\Feature;

use App\Services\MarkdownService;
use Tests\TestCase;

class MarkdownServiceTest extends TestCase
{
    protected MarkdownService $markdownService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markdownService = app(MarkdownService::class);
    }

    public function test_basic_markdown_parsing()
    {
        $markdown = '# Hello World';
        $html = $this->markdownService->toHtml($markdown);
        
        $this->assertStringContainsString('<h1', $html);
        $this->assertStringContainsString('Hello World', $html);
    }

    public function test_code_block_parsing()
    {
        $markdown = "```php\n<?php echo 'Hello World';\n```";
        $html = $this->markdownService->toHtml($markdown);
        
        $this->assertStringContainsString('<pre>', $html);
        $this->assertStringContainsString('<code class="language-php hljs php"', $html);
        $this->assertStringContainsString('echo', $html);
    }

    public function test_mermaid_diagram_conversion()
    {
        $markdown = "```mermaid\ngraph TD\nA --> B\n```";
        $html = $this->markdownService->toHtml($markdown);
        
        $this->assertStringContainsString('<div class="mermaid">', $html);
        $this->assertStringContainsString('graph TD', $html);
        $this->assertStringContainsString('A --> B', $html);
    }

    public function test_table_parsing()
    {
        $markdown = "| Name | Age |\n|------|-----|\n| John | 25 |";
        $html = $this->markdownService->toHtml($markdown);
        
        $this->assertStringContainsString('<table>', $html);
        $this->assertStringContainsString('<th>', $html);
        $this->assertStringContainsString('<td>', $html);
        $this->assertStringContainsString('John', $html);
    }

    public function test_task_list_parsing()
    {
        $markdown = "- [x] Completed task\n- [ ] Incomplete task";
        $html = $this->markdownService->toHtml($markdown);
        
        $this->assertStringContainsString('type="checkbox"', $html);
        $this->assertStringContainsString('checked', $html);
        $this->assertStringContainsString('Completed task', $html);
    }

    public function test_autolink_parsing()
    {
        $markdown = "Visit https://example.com for more info";
        $html = $this->markdownService->toHtml($markdown);
        
        $this->assertStringContainsString('<a href="https://example.com">', $html);
    }
}