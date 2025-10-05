<?php

namespace Tests\Feature;

use App\Services\MarkdownService;
use Tests\TestCase;

class MarkdownServiceDebugTest extends TestCase
{
    protected MarkdownService $markdownService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markdownService = app(MarkdownService::class);
    }

    public function test_debug_table_parsing()
    {
        $markdown = "| Name | Age |\n|------|-----|\n| John | 25 |";
        $html = $this->markdownService->toHtml($markdown);
        
        echo "\n=== Table Test ===\n";
        echo "Markdown:\n" . $markdown . "\n";
        echo "HTML:\n" . $html . "\n";
        
        // 简单断言
        $this->assertNotEmpty($html);
    }

    public function test_debug_task_list_parsing()
    {
        $markdown = "- [x] Completed task\n- [ ] Incomplete task";
        $html = $this->markdownService->toHtml($markdown);
        
        echo "\n=== Task List Test ===\n";
        echo "Markdown:\n" . $markdown . "\n";
        echo "HTML:\n" . $html . "\n";
        
        $this->assertNotEmpty($html);
    }

    public function test_debug_autolink_parsing()
    {
        $markdown = "Visit https://example.com for more info";
        $html = $this->markdownService->toHtml($markdown);
        
        echo "\n=== Autolink Test ===\n";
        echo "Markdown:\n" . $markdown . "\n";
        echo "HTML:\n" . $html . "\n";
        
        $this->assertNotEmpty($html);
    }
}