<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeSection: window.location.hash || '' }" x-init="
    // 为所有标题元素添加ID
    setTimeout(() => {
        const headings = document.querySelectorAll('.markdown-content h1, .markdown-content h2, .markdown-content h3');
        headings.forEach((heading, index) => {
            if (!heading.id) {
                // 使用标题文本创建slug作为ID
                const text = heading.textContent.trim().toLowerCase();
                const slug = text.replace(/[^a-z0-9\u4e00-\u9fa5]/g, '-').replace(/-+/g, '-');
                heading.id = slug || 'heading-' + index;
            }
        });
    }, 100);
">
    <div class="mb-4 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-gray-700">首页</a> /
        <a href="{{ route('categories.show', $pattern->category->slug) }}" class="hover:text-gray-700">{{ $pattern->category->getNameAttribute() }}</a> /
        <span class="text-gray-900 font-medium">{{ $pattern->getNameAttribute() }}</span>
    </div>

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-8">
        <!-- 移动端目录 - 显示在内容上方 -->
        <aside class="lg:hidden bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
            <details class="group" open>
                <summary class="flex items-center justify-between cursor-pointer font-semibold text-gray-900">
                    <span>{{ __('patterns.table_of_contents') }}</span>
                    <svg class="w-4 h-4 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </summary>
                <nav class="mt-3 space-y-1 max-h-60 overflow-y-auto">
                    @if(count($tableOfContents) > 0)
                        @foreach($tableOfContents as $item)
                            <button type="button"
                               class="block w-full text-left py-2 px-3 text-sm rounded transition-colors border-l-2 border-transparent"
                               :class="activeSection === '#{{ $item['slug'] }}' ?
                                       'text-blue-600 bg-blue-50 font-medium border-blue-600' :
                                       'text-gray-600 hover:text-blue-600 hover:bg-blue-50'"
                               x-on:click="
                                   // 关闭移动端目录
                                   $el.closest('details')?.removeAttribute('open');
                                   // 更新激活状态
                                   activeSection = '#{{ $item['slug'] }}';
                                   // 滚动到目标位置
                                   const element = document.getElementById('{{ $item['slug'] }}');
                                   if (element) {
                                       element.scrollIntoView({ behavior: 'smooth' });
                                       window.location.hash = '#{{ $item['slug'] }}';
                                   } else {
                                       // 备用方案：查找包含标题文本的元素
                                       const headings = document.querySelectorAll('.markdown-content h1, .markdown-content h2, .markdown-content h3');
                                       for (let heading of headings) {
                                           if (heading.textContent.trim() === '{{ $item['title'] }}') {
                                               heading.scrollIntoView({ behavior: 'smooth' });
                                               window.location.hash = '#' + (heading.id || '{{ $item['slug'] }}');
                                               break;
                                           }
                                       }
                                   }
                               ">
                                {{ $item['title'] }}
                            </button>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500 px-3">{{ __('patterns.no_table_of_contents') }}</p>
                    @endif
                </nav>
            </details>
        </aside>

        <!-- 桌面端目录 - 侧边栏固定 -->
        <aside class="hidden lg:block w-64 flex-shrink-0">
            <div class="sticky top-8">
                <h3 class="font-semibold text-gray-900 mb-3">{{ __('patterns.table_of_contents') }}</h3>
                <nav class="space-y-1">
                    @if(count($tableOfContents) > 0)
                        @foreach($tableOfContents as $item)
                            <button type="button"
                               class="block w-full text-left py-1 px-2 text-sm rounded transition-colors"
                               :class="activeSection === '#{{ $item['slug'] }}' ?
                                       'text-blue-600 bg-blue-50 font-medium border-l-2 border-blue-600' :
                                       'text-gray-600 hover:text-blue-600 hover:bg-blue-50'"
                               x-on:click="
                                   activeSection = '#{{ $item['slug'] }}';
                                   const element = document.getElementById('{{ $item['slug'] }}');
                                   if (element) {
                                       element.scrollIntoView({ behavior: 'smooth' });
                                       window.location.hash = '#{{ $item['slug'] }}';
                                   } else {
                                       // 备用方案：查找包含标题文本的元素
                                       const headings = document.querySelectorAll('.markdown-content h1, .markdown-content h2, .markdown-content h3');
                                       for (let heading of headings) {
                                           if (heading.textContent.trim() === '{{ $item['title'] }}') {
                                               heading.scrollIntoView({ behavior: 'smooth' });
                                               window.location.hash = '#' + (heading.id || '{{ $item['slug'] }}');
                                               break;
                                           }
                                       }
                                   }
                               ">
                                {{ $item['title'] }}
                            </button>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500">{{ __('patterns.no_table_of_contents') }}</p>
                    @endif
                </nav>
            </div>
        </aside>

        <main class="flex-1 min-w-0">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 lg:p-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ $pattern->getNameAttribute() }}</h1>
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 mb-6 text-sm text-gray-600">
                    <span class="bg-gray-100 px-3 py-1 rounded-full text-gray-700 font-medium">{{ $pattern->category->getNameAttribute() }}</span>
                    <span class="text-gray-500">更新: {{ $pattern->updated_at->diffForHumans() }}</span>
                </div>

                @if($pattern->description)
                <p class="text-lg text-gray-700 mb-6 sm:mb-8">{{ $pattern->description }}</p>
                @endif

                <x-markdown class="markdown-content prose prose-gray max-w-none"
                    :options="['html_input' => 'strip', 'allow_unsafe_links' => false, 'heading_permalink' => true]">
                    {!! $pattern->getContent() !!}
                </x-markdown>
            </div>
        </main>
    </div>

    <!-- 评论区域 -->
    <div class="mt-8 sm:mt-12">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">{{ __('commentify.discussion') }}</h2>
        @livewire('custom-comments', ['model' => $pattern])
    </div>
</div>
