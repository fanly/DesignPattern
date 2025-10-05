<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Blade;


class PatternPreview extends Component
{
    public string $markdownContent = '';
    public string $renderedHtml = '';
    
    protected $listeners = ['contentUpdated' => 'updatePreview'];
    
    public function updatePreview($content)
    {
        $this->markdownContent = $content;
        $this->renderedHtml = app(\App\Services\MarkdownService::class)->toHtml($content);
    }
    
    public function render()
    {
        return view('livewire.admin.pattern-preview');
    }
}