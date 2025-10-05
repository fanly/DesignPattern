<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('home.title') }}</title>
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900">
                            {{ __('nav.site_name') }}
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('patterns.index') }}" class="text-gray-600 hover:text-gray-900">
                            {{ __('nav.patterns') }}
                        </a>
                        <a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-gray-900">
                            {{ __('nav.categories') }}
                        </a>
                        
                        <!-- Language Switcher -->
                        <div class="relative">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('change-locale', 'zh') }}" 
                                   class="px-2 py-1 text-sm {{ app()->getLocale() == 'zh' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:text-gray-900' }} rounded">
                                    中文
                                </a>
                                <span class="text-gray-400">|</span>
                                <a href="{{ route('change-locale', 'en') }}" 
                                   class="px-2 py-1 text-sm {{ app()->getLocale() == 'en' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:text-gray-900' }} rounded">
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

    @stack('scripts')
    @livewireScripts
</body>
</html>