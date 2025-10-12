<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>图书推荐 - 设计模式相关书籍</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/" class="text-xl font-bold text-gray-900">设计模式学习平台</a>
                    </div>
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="/patterns" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">设计模式</a>
                        <a href="/books" class="text-blue-600 hover:text-blue-900 px-3 py-2 rounded-md text-sm font-medium">图书推荐</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">📚 设计模式相关书籍推荐</h1>
                    <p class="text-gray-600 mb-4">精选优质设计模式相关书籍，助您深入学习和实践</p>
                    
                    <!-- 手动更新按钮 -->
                    <div class="flex items-center space-x-4 mb-6">
                        <form action="{{ route('admin.books.update') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                <i class="fas fa-sync-alt mr-2"></i>手动更新图书数据
                            </button>
                        </form>
                        <span class="text-sm text-gray-500">数据每小时自动更新</span>
                    </div>
                </div>
                
                @if($books->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($books as $book)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 transform hover:-translate-y-1">
                                <!-- 图书封面 -->
                                <div class="h-48 bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center">
                                    @if($book->image_url)
                                        <img src="{{ $book->image_url }}" alt="{{ $book->title }}" class="h-40 object-contain">
                                    @else
                                        <i class="fas fa-book text-6xl text-blue-400"></i>
                                    @endif
                                </div>
                                
                                <div class="p-6">
                                    <!-- 标题 -->
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ $book->title }}</h3>
                                    
                                    <!-- 作者和出版社 -->
                                    <div class="space-y-1 mb-3">
                                        <p class="text-gray-600 text-sm">
                                            <i class="fas fa-user-edit mr-2"></i>作者: {{ $book->author ?? '未知' }}
                                        </p>
                                        <p class="text-gray-600 text-sm">
                                            <i class="fas fa-building mr-2"></i>出版社: {{ $book->publisher ?? '未知' }}
                                        </p>
                                        <p class="text-gray-600 text-sm">
                                            <i class="fas fa-calendar-alt mr-2"></i>出版日期: {{ $book->publish_date ?? '未知' }}
                                        </p>
                                    </div>
                                    
                                    <!-- 价格和评分 -->
                                    <div class="flex justify-between items-center mb-4">
                                        @if($book->price)
                                            <span class="text-red-600 font-bold text-xl">¥{{ $book->price }}</span>
                                        @endif
                                        @if($book->rating)
                                            <div class="flex items-center">
                                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                                <span class="text-sm text-gray-600">{{ $book->rating }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- 购买按钮 -->
                                    @if($book->buy_url)
                                        <a href="{{ $book->buy_url }}" target="_blank" 
                                           class="block w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white text-center py-3 px-4 rounded-md hover:from-blue-600 hover:to-blue-700 transition-all duration-300 font-medium">
                                            <i class="fas fa-shopping-cart mr-2"></i>立即购买
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- 分页信息 -->
                    <div class="mt-8 text-center text-gray-500 text-sm">
                        共找到 {{ $books->count() }} 本设计模式相关书籍
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-book-open text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg mb-2">暂无图书数据</p>
                        <p class="text-gray-400 text-sm">请稍后刷新页面或联系管理员更新数据</p>
                    </div>
                @endif
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-white border-t mt-12">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-gray-500 text-sm">
                    © 2025 设计模式学习平台 - 通过多麦API获取图书数据
                </p>
            </div>
        </footer>
    </div>
</body>
</html>