<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('site.title') }}</title>
    <meta name="description" content="{{ __('site.description') }}">
    <meta name="keywords" content="Laravel, Design Patterns, PHP, Programming">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ __('site.title') }}">
    <meta property="og:description" content="{{ __('site.description') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="{{ __('site.title') }}">
    <meta property="twitter:description" content="{{ __('site.description') }}">

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-SH7CH70BE3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-SH7CH70BE3');
    </script>

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    @livewireStyles
</head>
<body class="font-sans antialiased text-gray-900">
    <div class="min-h-screen bg-gray-50">
        <!-- 导航栏 -->
        <nav class="bg-gradient-to-r from-blue-600 to-purple-600 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- 左侧标题区域 -->
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-100 hover:text-white transition-colors duration-200">
                            {{ __('site.title') }}
                        </a>
                    </div>

                    <!-- 右侧功能区域 -->
                    <div class="flex items-center space-x-8">
                        @livewire('language-switcher')
                    </div>
                </div>
            </div>
        </nav>

        <!-- 主内容区 -->
        <main>
            @if(isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </div>
        </main>

        <!-- 底部广告位 -->
        <footer class="bg-gradient-to-r from-gray-50 to-blue-50 border-t mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                    <!-- 星球广告 -->
                    <div class="text-center lg:text-left">
                        <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-all duration-300">
                            <h3 class="text-xl font-bold text-gray-800 mb-6">{{ __('footer.join_community') }}</h3>
                            <a href="https://image.coding01.cn/blog/48885581888558T2.JPG" target="_blank" class="inline-block">
                                <img src="https://image.coding01.cn/blog/48885581888558T2.JPG"
                                     alt="{{ __('footer.join_community') }}"
                                     class="rounded-lg shadow-md hover:shadow-lg transition-all duration-300 w-full max-w-xs mx-auto"
                                     loading="lazy"
                                     width="316"
                                     height="350"
                                     decoding="async"
                                     style="object-fit: cover;">
                            </a>
                            <p class="text-gray-600 mt-6 text-sm">{{ __('footer.join_community_description') }}</p>
                        </div>
                    </div>

                    <!-- Laravel源码解析外链 -->
                    <div class="text-center lg:text-left">
                        <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-all duration-300">
                            <h3 class="text-xl font-bold text-gray-800 mb-6">{{ __('footer.laravel_source_analysis') }}</h3>
                            <a href="https://laravel.coding01.cn/" target="_blank"
                               class="inline-flex items-center justify-center bg-white text-gray-900 border border-gray-300 px-8 py-4 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 text-lg font-semibold shadow-md hover:shadow-lg w-full">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path>
                                </svg>
                                {{ __('footer.laravel_source_analysis') }}
                            </a>
                            <p class="text-gray-600 mt-6 text-sm">{{ __('footer.laravel_source_description') }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-12 pt-8 border-t border-gray-200 text-center">
                    <p class="text-gray-500 text-sm">&copy; 2025 Laravel设计模式. 保留所有权利.</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- 引入Livewire脚本 -->
    @livewireScripts
    <!-- 引入Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js" defer></script>
</body>
</html>
