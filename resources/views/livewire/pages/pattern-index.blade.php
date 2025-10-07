@push('meta')
    <meta name="description" content="{{ __('seo.patterns_description') }}">
    <meta name="keywords" content="{{ __('seo.patterns_keywords') }}">
    <meta property="og:title" content="{{ __('seo.patterns_title') }}">
    <meta property="og:description" content="{{ __('seo.patterns_description') }}">
    <meta property="og:type" content="website">
@endpush

<div>
    <!-- Hero Section -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-4 sm:mb-6 leading-tight">
                    {{ __('patterns.title') }}
                </h1>
                <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed px-2">
                    {{ __('patterns.subtitle') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="py-16 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($categories as $category)
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-4">
                                        @if($category->name_zh === '创建型模式' || $category->name_en === 'Creational Patterns')
                                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                                </svg>
                                            </div>
                                        @elseif($category->name_zh === '结构型模式' || $category->name_en === 'Structural Patterns')
                                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                            </div>
                                        @elseif($category->name_zh === '行为型模式' || $category->name_en === 'Behavioral Patterns')
                                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <span class="bg-blue-100 text-blue-600 text-sm font-medium px-3 py-1 rounded-full">
                                    {{ $category->designPatterns->count() }} {{ __('patterns.patterns_count') }}
                                </span>
                            </div>
                            
                            <h2 class="text-xl font-bold text-gray-900 mb-3">
                                {{ $category->getNameAttribute() }}
                            </h2>
                            
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                {{ $category->description }}
                            </p>
                            
                            <div class="space-y-3">
                                @foreach($category->designPatterns as $pattern)
                                    <a href="{{ route('patterns.show', $pattern->slug) }}" 
                                       class="group flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                        <div class="w-2 h-2 bg-blue-400 rounded-full mr-3 flex-shrink-0 group-hover:bg-blue-600 transition-colors"></div>
                                        <span class="text-gray-700 group-hover:text-blue-600 transition-colors truncate">
                                            {{ $pattern->getNameAttribute() }}
                                        </span>
                                        <svg class="w-4 h-4 ml-auto text-gray-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="max-w-md mx-auto">
                            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                {{ __('patterns.no_categories') }}
                            </h3>
                            <p class="text-gray-500">
                                {{ __('patterns.no_categories_description') }}
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>