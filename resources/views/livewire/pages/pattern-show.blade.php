@push('meta')
    <meta name="description" content="{{ $pattern->description ?: __('seo.pattern_default_description', ['pattern' => $pattern->getNameAttribute()]) }}">
    <meta name="keywords" content="{{ __('seo.pattern_keywords', ['pattern' => $pattern->getNameAttribute(), 'category' => $pattern->category->getNameAttribute()]) }}">
    <meta property="og:title" content="{{ $pattern->getNameAttribute() }} - {{ __('seo.site_name') }}">
    <meta property="og:description" content="{{ $pattern->description ?: __('seo.pattern_default_description', ['pattern' => $pattern->getNameAttribute()]) }}">
    <meta property="og:type" content="article">
@endpush

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8" 
     x-data="{
         activeSection: window.location.hash || '',
         headings: [],
         observer: null,
         scrollProgress: 0,
         showScrollTop: false,
         
         init() {
             // 初始化标题ID和滚动监听
             this.$nextTick(() => {
                 this.initializeHeadings();
                 this.setupScrollObserver();
                 this.setupScrollTracking();
                 
                 // 处理初始哈希
                 if (window.location.hash) {
                     this.activeSection = window.location.hash;
                     this.scrollToSection(window.location.hash.substring(1), 300);
                 }
             });
             
             // Livewire更新后重新初始化
             this.$watch('$store.livewire.isLoading', (isLoading) => {
                 if (!isLoading) {
                     setTimeout(() => {
                         this.initializeHeadings();
                         this.setupScrollObserver();
                         this.setupScrollTracking();
                     }, 100);
                 }
             });
         },
         
         setupScrollTracking() {
             // 监听滚动事件来更新进度和显示返回顶部按钮
             window.addEventListener('scroll', () => {
                 this.updateScrollProgress();
                 this.updateScrollTopVisibility();
             });
             
             // 初始更新
             this.updateScrollProgress();
             this.updateScrollTopVisibility();
         },
         
         updateScrollProgress() {
             const contentElement = document.querySelector('.markdown-content');
             if (!contentElement) return;
             
             const contentRect = contentElement.getBoundingClientRect();
             const windowHeight = window.innerHeight;
             const documentHeight = document.documentElement.scrollHeight;
             
             // 计算内容在视口中的可见比例
             const visibleHeight = Math.min(contentRect.bottom, windowHeight) - Math.max(contentRect.top, 0);
             const progress = Math.max(0, Math.min(1, visibleHeight / windowHeight));
             
             this.scrollProgress = Math.round(progress * 100);
         },
         
         updateScrollTopVisibility() {
             this.showScrollTop = window.pageYOffset > 300;
         },
         
         scrollToTop() {
             window.scrollTo({
                 top: 0,
                 behavior: 'smooth'
             });
         },
         
         initializeHeadings() {
             const headingElements = document.querySelectorAll('.markdown-content h1, .markdown-content h2, .markdown-content h3, .markdown-content h4');
             this.headings = [];
             
             headingElements.forEach((heading, index) => {
                 let slug = heading.id;
                 
                 // 如果标题没有ID，使用后端生成的slug
                 if (!slug) {
                     const titleText = heading.textContent.trim();
                     slug = this.generateSlug(titleText);
                     heading.id = slug;
                 }
                 
                 this.headings.push({
                     element: heading,
                     slug: slug,
                     title: heading.textContent.trim(),
                     level: parseInt(heading.tagName.substring(1))
                 });
             });
         },
         
         setupScrollObserver() {
             // 清理旧的观察器
             if (this.observer) {
                 this.observer.disconnect();
             }
             
             // 创建Intersection Observer来检测可见标题
             this.observer = new IntersectionObserver((entries) => {
                 entries.forEach((entry) => {
                     if (entry.isIntersecting) {
                         const slug = entry.target.id;
                         this.activeSection = '#' + slug;
                         
                         // 更新URL哈希（不触发滚动）
                         if (history.replaceState) {
                             const newUrl = window.location.pathname + window.location.search + '#' + slug;
                             history.replaceState(null, null, newUrl);
                         }
                     }
                 });
             }, {
                 rootMargin: '-20% 0px -60% 0px', // 调整可见区域阈值
                 threshold: [0, 0.5, 1]
             });
             
             // 观察所有标题
             this.headings.forEach(heading => {
                 this.observer.observe(heading.element);
             });
         },
         
         scrollToSection(slug, delay = 0) {
             setTimeout(() => {
                 const element = document.getElementById(slug);
                 if (element) {
                     const headerOffset = 100; // 考虑固定头部的高度
                     const elementPosition = element.getBoundingClientRect().top;
                     const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                     
                     window.scrollTo({
                         top: offsetPosition,
                         behavior: 'smooth'
                     });
                 }
             }, delay);
         },
         
         generateSlug(text) {
             return text.toLowerCase()
                 .replace(/[^a-z0-9\u4e00-\u9fa5]/g, '-')
                 .replace(/-+/g, '-')
                 .replace(/^-+|-+$/g, '')
                 || 'section-' + Math.random().toString(36).substr(2, 8);
         }
     }" 
     x-init="init()"
>
    <div class="mb-4 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-gray-700">{{ __('common.home') }}</a> /
        <a href="{{ route('categories.show', $pattern->category->slug) }}" class="hover:text-gray-700">{{ $pattern->category->getNameAttribute() }}</a> /
        <span class="text-gray-900 font-medium">{{ $pattern->getNameAttribute() }}</span>
    </div>

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-8">
        <!-- 移动端目录 - 显示在内容上方 -->
        <aside class="lg:hidden bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
            <details class="group" x-data="{ isOpen: true }" x-init="isOpen = window.innerWidth >= 1024">
                <summary class="flex items-center justify-between cursor-pointer font-semibold text-gray-900"
                         @click="isOpen = !isOpen">
                    <span>{{ __('patterns.table_of_contents') }}</span>
                    <svg class="w-4 h-4 transform transition-transform" :class="isOpen ? 'rotate-180' : ''" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </summary>
                <nav class="mt-3 space-y-1 max-h-60 overflow-y-auto" x-show="isOpen" x-collapse>
                    @if(count($tableOfContents) > 0)
                        @foreach($tableOfContents as $item)
                            <button type="button"
                                    class="block w-full text-left py-2 px-3 text-sm rounded transition-all duration-200 border-l-2 border-transparent hover:bg-blue-50 hover:text-blue-600"
                                    :class="activeSection === '#{{ $item['slug'] }}' ? 
                                            'text-blue-600 bg-blue-50 font-medium border-blue-600 shadow-sm' : 
                                            'text-gray-600'"
                                    @click="
                                        activeSection = '#{{ $item['slug'] }}';
                                        scrollToSection('{{ $item['slug'] }}');
                                        if (window.innerWidth < 1024) {
                                            $el.closest('details').removeAttribute('open');
                                        }
                                    "
                                    x-bind:style="{
                                        'paddingLeft': 'calc(0.75rem + {{ $item['indent'] }} * 0.75rem)'
                                    }">
                                <span class="flex items-center">
                                    @if($item['level'] <= 2)
                                        <svg class="w-2.5 h-2.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <span class="w-2.5 h-2.5 mr-2 flex-shrink-0 rounded-full bg-gray-300"></span>
                                    @endif
                                    {{ $item['title'] }}
                                </span>
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
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-900">{{ __('patterns.table_of_contents') }}</h3>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                        {{ count($tableOfContents) }} {{ trans_choice('patterns.sections', count($tableOfContents)) }}
                    </span>
                </div>
                <nav class="space-y-1">
                    @if(count($tableOfContents) > 0)
                        @foreach($tableOfContents as $item)
                            <button type="button"
                                    class="block w-full text-left py-2 px-3 text-sm rounded transition-all duration-200 hover:bg-blue-50 hover:text-blue-600 border-l-2 border-transparent"
                                    :class="activeSection === '#{{ $item['slug'] }}' ? 
                                            'text-blue-600 bg-blue-50 font-medium border-blue-600 shadow-sm' : 
                                            'text-gray-600'"
                                    @click="activeSection = '#{{ $item['slug'] }}'; scrollToSection('{{ $item['slug'] }}')"
                                    x-bind:style="{
                                        'paddingLeft': 'calc(0.75rem + {{ $item['indent'] }} * 0.75rem)'
                                    }">
                                <span class="flex items-center">
                                    @if($item['level'] <= 2)
                                        <svg class="w-2.5 h-2.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <span class="w-2.5 h-2.5 mr-2 flex-shrink-0 rounded-full bg-gray-300"></span>
                                    @endif
                                    <span class="truncate">{{ $item['title'] }}</span>
                                </span>
                            </button>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500">{{ __('patterns.no_table_of_contents') }}</p>
                    @endif
                </nav>
            </div>
        </aside>

        <main class="flex-1 min-w-0">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- 顶部信息栏 - 弱化设计 -->
                <div class="bg-gray-50 border-b border-gray-200 px-4 py-3 sm:px-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <h1 class="text-lg sm:text-xl font-semibold text-gray-800 truncate">{{ $pattern->getNameAttribute() }}</h1>
                            <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                <span class="bg-white px-2 py-1 rounded border border-gray-300">{{ $pattern->category->getNameAttribute() }}</span>
                                <span>{{ __('patterns.updated') }}: {{ $pattern->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    
                    @if($pattern->description)
                    <p class="text-gray-600 mt-2 text-sm leading-relaxed line-clamp-2">{{ $pattern->description }}</p>
                    @endif
                </div>

                <!-- Markdown内容区域 -->
                <div class="p-4 sm:p-6 lg:p-8">
                    <x-enhanced-markdown 
                        class="markdown-content prose prose-gray max-w-none"
                        :content="$pattern->getContent()" />
                </div>
            </div>
        </main>
    </div>

    <!-- 滚动进度指示器 -->
    <div class="fixed bottom-8 right-8 z-50 hidden lg:block" 
         x-show="showScrollTop" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-90">
        <div class="relative">
            <!-- 进度环 -->
            <div class="relative w-12 h-12">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15.9155" 
                            fill="none" 
                            stroke="#e5e7eb" 
                            stroke-width="2" 
                            stroke-dasharray="100" 
                            stroke-dashoffset="0" />
                    <circle cx="18" cy="18" r="15.9155" 
                            fill="none" 
                            stroke="#3b82f6" 
                            stroke-width="2" 
                            stroke-dasharray="100" 
                            :stroke-dashoffset="100 - scrollProgress" 
                            stroke-linecap="round" />
                </svg>
                
                <!-- 返回顶部按钮 -->
                <button @click="scrollToTop" 
                        class="absolute inset-0 w-full h-full rounded-full bg-white shadow-lg hover:shadow-xl transition-shadow flex items-center justify-center group">
                    <svg class="w-5 h-5 text-blue-600 group-hover:text-blue-700 transition-colors" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                </button>
            </div>
            
            <!-- 进度百分比 -->
            <div class="absolute -top-1 -right-1 bg-blue-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-medium">
                <span x-text="scrollProgress"></span>%
            </div>
        </div>
    </div>
    </div>

    <!-- 推荐内容和评论区域 -->
    <div class="mt-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- 推荐内容 - 左侧 -->
                <div class="lg:col-span-1">
                    @include('components.pattern-recommendations', ['pattern' => $pattern])
                </div>

                <!-- 评论区域 - 右侧 -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('patterns.discussion') }}</h3>
                        @livewire('custom-comments', ['model' => $pattern])
                    </div>
                </div>
            </div>
        </div>
    </div>

