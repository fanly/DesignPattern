<?php

namespace App\View\Components;

use App\Services\MarkdownService;
use Illuminate\View\Component;

class EnhancedMarkdown extends Component
{
    public string $content;
    public array $options;

    public function __construct(string $content = '', array $options = [])
    {
        $this->content = $content;
        $this->options = $options;
    }

    public function render()
    {
        $markdownService = app(MarkdownService::class);
        $html = $markdownService->toHtml($this->content);

        return view('components.enhanced-markdown', [
            'html' => $html
        ]);
    }
}