<div {{ $attributes->merge(['class' => 'markdown-content prose prose-gray max-w-none']) }}>
    {!! $html !!}
</div>

@once
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11/styles/github.min.css">
<style>
    .mermaid {
        text-align: center;
        margin: 1rem 0;
        background: white;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .prose pre {
        background-color: #f6f8fa;
        border-radius: 6px;
        padding: 1rem;
        overflow-x: auto;
        position: relative;
    }
    
    .prose code {
        background-color: #f6f8fa;
        padding: 0.2rem 0.4rem;
        border-radius: 3px;
        font-size: 0.875em;
    }

    .prose pre code {
        background-color: transparent;
        padding: 0;
    }

    /* 代码块语言标签 */
    .prose pre[data-language]:before {
        content: attr(data-language);
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: rgba(0,0,0,0.1);
        color: #666;
        padding: 0.2rem 0.5rem;
        border-radius: 3px;
        font-size: 0.75rem;
        text-transform: uppercase;
    }

    /* 暗黑主题支持 */
    .dark .mermaid {
        background: #1f2937;
        color: #e5e7eb;
    }

    .dark .prose pre {
        background-color: #1f2937;
        color: #e5e7eb;
    }

    .dark .prose code {
        background-color: #374151;
        color: #e5e7eb;
    }

    /* 标题锚点样式 */
    .heading-permalink {
        opacity: 0;
        margin-left: 0.5rem;
        text-decoration: none;
        color: #6b7280;
        transition: opacity 0.2s;
    }

    .prose h1:hover .heading-permalink,
    .prose h2:hover .heading-permalink,
    .prose h3:hover .heading-permalink,
    .prose h4:hover .heading-permalink,
    .prose h5:hover .heading-permalink,
    .prose h6:hover .heading-permalink {
        opacity: 1;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 初始化 Mermaid
    mermaid.initialize({ 
        startOnLoad: true,
        theme: 'default',
        securityLevel: 'loose',
        fontFamily: 'inherit'
    });
});

// Livewire 更新后重新渲染 Mermaid
document.addEventListener('livewire:navigated', function () {
    mermaid.init(undefined, document.querySelectorAll('.mermaid'));
});

document.addEventListener('livewire:updated', function () {
    mermaid.init(undefined, document.querySelectorAll('.mermaid'));
});
</script>
@endpush
@endonce