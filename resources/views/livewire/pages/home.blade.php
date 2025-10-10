@push('meta')
    <meta name="description" content="{{ __('seo.home_description') }}">
    <meta name="keywords" content="{{ __('seo.home_keywords') }}">
    <meta property="og:title" content="{{ __('seo.home_title') }}">
    <meta property="og:description" content="{{ __('seo.home_description') }}">
    <meta property="og:type" content="website">
@endpush

@php
    use Illuminate\Support\Str;
@endphp

<div>
    <!-- Hero Section -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-20">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-4 sm:mb-6 leading-tight">
                    {{ __('home.title') }}
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl mb-6 sm:mb-8 text-gray-600 max-w-3xl mx-auto leading-relaxed px-2">
                    {{ __('home.subtitle') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center max-w-md sm:max-w-none mx-auto">
                    <a href="{{ route('patterns.index') }}" 
                       class="bg-blue-600 text-white px-6 sm:px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-lg text-center">
                        {{ __('home.browse_patterns') }}
                    </a>
                </div>
            </div>
        </div>
    </div>



    <!-- Latest Patterns Section -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    {{ __('home.latest_patterns') }}
                </h2>
                <p class="text-lg text-gray-600">
                    {{ __('home.latest_patterns_description') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($latestPatterns as $pattern)
                    <a href="{{ route('patterns.show', $pattern->slug) }}" 
                       class="group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 block">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-4">
                                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">
                                            {{ $pattern->category->getNameAttribute() }}
                                        </span>
                                    </div>
                                </div>
                                <span class="bg-green-100 text-green-600 text-xs font-medium px-2 py-1 rounded-full">
                                    {{ __('home.new') }}
                                </span>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">
                                {{ $pattern->getNameAttribute() }}
                            </h3>
                            
                            <p class="text-gray-600 mb-6 leading-relaxed line-clamp-3">
                                {{ Str::limit($pattern->description, 120) }}
                            </p>
                            
                            <div class="flex items-center text-blue-600 group-hover:text-blue-700 font-medium">
                                {{ __('home.view_pattern') }}
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                {{ __('home.no_patterns') }}
                            </h3>
                            <p class="text-gray-500">
                                {{ __('home.no_patterns_description') }}
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


</div>