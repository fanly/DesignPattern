<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
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
                            {{ config('app.name') }}
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('markdown.demo') }}" class="text-gray-600 hover:text-gray-900">
                            Markdown 演示
                        </a>
                        <a href="{{ route('patterns.index') }}" class="text-gray-600 hover:text-gray-900">
                            设计模式
                        </a>
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