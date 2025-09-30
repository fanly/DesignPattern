<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel设计模式电子书</title>
    @vite(['resources/css/app.css'])
    <!-- 引入Livewire样式 -->
    @livewireStyles
</head>
<body class="font-sans antialiased text-gray-900">
    <div class="min-h-screen bg-gray-50">
        <!-- 主内容区 -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- 引入Livewire脚本 -->
    @livewireScripts
    <!-- 引入Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js" defer></script>
</body>
</html>