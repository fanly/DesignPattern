<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('home.title') }}</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $seoDescription ?? __('seo.description') }}">
    <meta name="keywords" content="{{ $seoKeywords ?? __('seo.keywords') }}">
    <meta name="author" content="{{ __('seo.author') }}">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ $ogTitle ?? __('seo.og_title') }}">
    <meta property="og:description" content="{{ $ogDescription ?? __('seo.og_description') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ __('nav.site_name') }}">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $ogTitle ?? __('seo.og_title') }}">
    <meta name="twitter:description" content="{{ $ogDescription ?? __('seo.og_description') }}">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow" x-data="{ mobileMenuOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900">
                            {{ __('nav.site_name') }}
                        </a>
                    </div>
                    
                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="{{ route('patterns.index') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            {{ __('nav.patterns') }}
                        </a>
                        <a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            {{ __('nav.categories') }}
                        </a>
                        
                        <!-- Language Switcher -->
                        <div class="flex items-center space-x-2 border-l pl-6">
                            <a href="{{ route('change-locale', 'zh') }}" 
                               class="px-3 py-1 text-sm {{ app()->getLocale() == 'zh' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:text-gray-900' }} rounded-md transition-colors">
                                {{ __('nav.chinese') }}
                            </a>
                            <span class="text-gray-400">|</span>
                            <a href="{{ route('change-locale', 'en') }}" 
                               class="px-3 py-1 text-sm {{ app()->getLocale() == 'en' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:text-gray-900' }} rounded-md transition-colors">
                                EN
                            </a>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 hover:text-gray-900 focus:outline-none focus:text-gray-900 p-2">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation Menu -->
                <div x-show="mobileMenuOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="md:hidden border-t border-gray-200 bg-white">
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        <a href="{{ route('patterns.index') }}" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md text-base font-medium transition-colors">
                            {{ __('nav.patterns') }}
                        </a>
                        <a href="{{ route('categories.index') }}" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md text-base font-medium transition-colors">
                            {{ __('nav.categories') }}
                        </a>
                        
                        <!-- Mobile Language Switcher -->
                        <div class="px-3 py-2 border-t border-gray-200 mt-3 pt-3">
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-500 font-medium">{{ __('nav.language') }}:</span>
                                <a href="{{ route('change-locale', 'zh') }}" 
                                   class="px-3 py-1 text-sm {{ app()->getLocale() == 'zh' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} rounded-md transition-colors">
                                    {{ __('nav.chinese') }}
                                </a>
                                <a href="{{ route('change-locale', 'en') }}" 
                                   class="px-3 py-1 text-sm {{ app()->getLocale() == 'en' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} rounded-md transition-colors">
                                    EN
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- Footer -->
    @include('components.footer')

    @stack('scripts')
    @livewireScripts
</body>
</html>