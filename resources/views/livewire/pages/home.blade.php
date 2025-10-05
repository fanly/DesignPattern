<div>
    <!-- Hero Section -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <div class="mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-6">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    {{ __('home.title') }}
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-gray-600 max-w-3xl mx-auto">
                    {{ __('home.subtitle') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('patterns.index') }}" 
                       class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-lg">
                        {{ __('home.browse_patterns') }}
                    </a>
                    <a href="{{ route('categories.index') }}" 
                       class="border-2 border-blue-600 text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-600 hover:text-white transition-colors">
                        {{ __('home.browse_categories') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    {{ __('home.pattern_categories') }}
                </h2>
                <p class="text-lg text-gray-600">
                    {{ __('home.categories_description') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($categories as $category)
                    <a href="{{ route('categories.show', $category->slug) }}" 
                       class="group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 block">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 mr-4">
                                    @if($category->name_zh === '创建型模式' || $category->name_en === 'Creational Patterns')
                                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                            </svg>
                                        </div>
                                    @elseif($category->name_zh === '结构型模式' || $category->name_en === 'Structural Patterns')
                                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </div>
                                    @elseif($category->name_zh === '行为型模式' || $category->name_en === 'Behavioral Patterns')
                                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {{ $category->name }}
                                    </h3>
                                    <span class="bg-gray-100 text-gray-600 text-sm font-medium px-2 py-1 rounded-full">
                                        {{ $category->designPatterns->count() }} {{ __('home.patterns') }}
                                    </span>
                                </div>
                            </div>
                            
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                {{ $category->description }}
                            </p>
                            
                            <div class="space-y-2 mb-6">
                                @foreach($category->designPatterns->take(3) as $pattern)
                                    <div class="flex items-center text-sm text-gray-500">
                                        <div class="w-2 h-2 bg-blue-400 rounded-full mr-3 flex-shrink-0"></div>
                                        <span class="truncate">{{ $pattern->name }}</span>
                                    </div>
                                @endforeach
                                @if($category->designPatterns->count() > 3)
                                    <div class="text-sm text-gray-400 pl-5">
                                        {{ __('home.and_more', ['count' => $category->designPatterns->count() - 3]) }}
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex items-center text-blue-600 group-hover:text-blue-700 font-medium">
                                {{ __('home.view_category') }}
                                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="max-w-md mx-auto">
                            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                {{ __('home.no_categories') }}
                            </h3>
                            <p class="text-gray-500">
                                {{ __('home.no_categories_description') }}
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    {{ __('home.why_design_patterns') }}
                </h2>
                <p class="text-lg text-gray-600">
                    {{ __('home.features_description') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        {{ __('home.feature_1_title') }}
                    </h3>
                    <p class="text-gray-600">
                        {{ __('home.feature_1_description') }}
                    </p>
                </div>

                <div class="text-center">
                    <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        {{ __('home.feature_2_title') }}
                    </h3>
                    <p class="text-gray-600">
                        {{ __('home.feature_2_description') }}
                    </p>
                </div>

                <div class="text-center">
                    <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        {{ __('home.feature_3_title') }}
                    </h3>
                    <p class="text-gray-600">
                        {{ __('home.feature_3_description') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations Section -->
    <div class="py-16 bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">
                    {{ __('home.recommendations') }}
                </h2>
                <p class="text-lg text-slate-300">
                    {{ __('home.recommendations_description') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- 知识星球推荐 -->
                <div class="bg-slate-800 rounded-xl p-8 text-center hover:bg-slate-700 transition-colors">
                    <div class="w-24 h-24 mx-auto mb-6 rounded-xl overflow-hidden">
                        <img src="https://image.coding01.cn/blog/48885581888558T2.JPG" 
                             alt="设计模式" 
                             class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Laravel 源码推荐 -->
                <div class="bg-slate-800 rounded-xl p-8 text-center hover:bg-slate-700 transition-colors">
                    <div class="w-16 h-16 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </div>
                     <h3 class="text-xl font-bold mb-4">
                        {{ __('home.laravel_knowledge') }}
                    </h3>
                    <p class="text-slate-300 mb-6">
                        {{ __('home.laravel_knowledge_description') }}
                    </p>
                    <a href="https://laravel.coding01.cn/" 
                       target="_blank"
                       class="inline-flex items-center bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-400 transition-colors">
                        {{ __('home.visit_now') }}
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="text-center mt-12 pt-8 border-t border-slate-700">
                <p class="text-slate-400">
                    &copy; 2024 {{ __('home.title') }}. 
                    @if(app()->getLocale() === 'zh')
                        保留所有权利。
                    @else
                        All rights reserved.
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>