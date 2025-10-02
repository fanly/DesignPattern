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
        <a href="{{ route('categories.show', $pattern->category->slug) }}" class="hover:text-gray-700">{{ $pattern->category->getNameAttribute() }}</a> /
        <span class="text-gray-900 font-medium">{{ $pattern->getNameAttribute() }}</span>
    </div>

    <div class="flex gap-8">
        <aside class="w-64 flex-shrink-0">
            <div class="sticky top-8">
                <h3 class="font-semibold text-gray-900 mb-3">{{ __('patterns.table_of_contents') }}</h3>
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
                        <p class="text-sm text-gray-500">{{ __('patterns.no_table_of_contents') }}</p>
                    @endif
                </nav>
            </div>
        </aside>

        <main style="flex: 1; min-width: 0;">
            <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 2rem;">
                <h1 style="font-size: 1.875rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">{{ $pattern->getNameAttribute() }}</h1>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; font-size: 0.875rem; color: #6b7280;">
                    <span style="background-color: #f3f4f6; padding: 0.25rem 0.75rem; border-radius: 9999px;">{{ $pattern->category->getNameAttribute() }}</span>
                    <span>更新: {{ $pattern->updated_at->diffForHumans() }}</span>
                </div>

                @if($pattern->description)
                <p class="text-lg text-gray-700 mb-8">{{ $pattern->description }}</p>
                @endif

                <x-markdown class="markdown-content">
                    {!! $pattern->getContent() !!}
                </x-markdown>
            </div>
        </main>
    </div>

    <!-- 评论区域 -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('commentify.discussion') }}</h2>
        @livewire('custom-comments', ['model' => $pattern])
    </div>
</div>
