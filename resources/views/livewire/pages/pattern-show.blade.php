<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeSection: window.location.hash || '' }" x-init="
    // 监听hash变化
    window.addEventListener('hashchange', () => {
        activeSection = window.location.hash;
        // 滚动到对应标题
        const target = document.querySelector(window.location.hash);
        if (target) {
            setTimeout(() => {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    });
    
    // 监听滚动，更新激活状态
    const sections = document.querySelectorAll('.markdown-content h1, .markdown-content h2, .markdown-content h3');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = '#' + entry.target.id;
                if (id !== activeSection) {
                    activeSection = id;
                    window.history.replaceState(null, null, id);
                }
            }
        });
    }, { rootMargin: '-20% 0px -80% 0px' });
    
    sections.forEach(section => observer.observe(section));
">
    <div class="mb-4 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-gray-700">首页</a> / 
        <a href="{{ route('categories.show', $pattern->category->slug) }}" class="hover:text-gray-700">{{ $pattern->category->name }}</a> / 
        <span class="text-gray-900 font-medium">{{ $pattern->name }}</span>
    </div>

    <div class="flex gap-8">
        <aside class="w-64 flex-shrink-0">
            <div class="sticky top-8">
                <h3 class="font-semibold text-gray-900 mb-3">目录</h3>
                <nav class="space-y-1">
                    @if(count($tableOfContents) > 0)
                        @foreach($tableOfContents as $item)
                            <a href="#{{ $item['slug'] }}" 
                               class="block py-1 px-2 text-sm rounded transition-colors"
                               :class="activeSection === '#{{ $item['slug'] }}' ? 
                                       'text-blue-600 bg-blue-50 font-medium border-l-2 border-blue-600' : 
                                       'text-gray-600 hover:text-blue-600 hover:bg-blue-50'">
                                {{ $item['title'] }}
                            </a>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500">暂无目录</p>
                    @endif
                </nav>
            </div>
        </aside>

        <main style="flex: 1; min-width: 0;">
            <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 2rem;">
                <h1 style="font-size: 1.875rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">{{ $pattern->name }}</h1>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; font-size: 0.875rem; color: #6b7280;">
                    <span style="background-color: #f3f4f6; padding: 0.25rem 0.75rem; border-radius: 9999px;">{{ $pattern->category->name }}</span>
                    <span>更新: {{ $pattern->updated_at->diffForHumans() }}</span>
                </div>
                
                @if($pattern->description)
                <p class="text-lg text-gray-700 mb-8">{{ $pattern->description }}</p>
                @endif

                <div style="max-width: none; line-height: 1.7; color: #374151;">
                    <div style="font-size: 1.125rem; line-height: 1.75;">
                        <div style="
                            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                            line-height: 1.8;
                            color: #2d3748;
                        ">
                            <!-- 标题样式 -->
                            <style>
                                .markdown-content h1 { font-size: 2rem; font-weight: 700; margin: 2rem 0 1rem; color: #1a202c; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem; scroll-margin-top: 80px; }
                                .markdown-content h2 { font-size: 1.5rem; font-weight: 600; margin: 1.5rem 0 1rem; color: #2d3748; scroll-margin-top: 80px; }
                                .markdown-content h3 { font-size: 1.25rem; font-weight: 600; margin: 1.25rem 0 0.75rem; color: #4a5568; scroll-margin-top: 80px; }
                                .markdown-content h1:target, .markdown-content h2:target, .markdown-content h3:target { 
                                    color: #2563eb; 
                                    border-left: 4px solid #2563eb;
                                    padding-left: 1rem;
                                    margin-left: -1rem;
                                }
                                .markdown-content p { margin: 1rem 0; }
                                .markdown-content ul, .markdown-content ol { margin: 1rem 0; padding-left: 1.5rem; }
                                .markdown-content li { margin: 0.5rem 0; }
                                .markdown-content code { background-color: #f7fafc; padding: 0.125rem 0.25rem; border-radius: 0.25rem; font-size: 0.875rem; color: #e53e3e; }
                                .markdown-content pre { background-color: #1a202c; color: #e2e8f0; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin: 1rem 0; }
                                .markdown-content pre code { background-color: transparent; color: inherit; padding: 0; }
                                .markdown-content blockquote { border-left: 4px solid #4299e1; padding-left: 1rem; margin: 1rem 0; color: #4a5568; font-style: italic; }
                                .markdown-content table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
                                .markdown-content th, .markdown-content td { border: 1px solid #e2e8f0; padding: 0.75rem; text-align: left; }
                                .markdown-content th { background-color: #f7fafc; font-weight: 600; }
                            </style>
                            <div class="markdown-content">
                                {!! $pattern->getHtmlContent() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>