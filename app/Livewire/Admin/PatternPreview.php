<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Blade;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class PatternPreview extends Component
{
    public string $markdownContent = '';
    public string $renderedHtml = '';
    
    protected $listeners = ['contentUpdated' => 'updatePreview'];
    
    public function updatePreview($content)
    {
        $this->markdownContent = $content;
        $this->renderedHtml = app(MarkdownRenderer::class)->toHtml($content);
    }
    
    public function render()
    {
        return view('livewire.admin.pattern-preview');
    }
}