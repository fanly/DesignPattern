<div class="container mx-auto px-4 py-8">
    <!-- 页面标题和刷新按钮 -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">设计模式图书推荐</h1>
            @if($lastUpdated)
                <p class="text-sm text-gray-600 mt-2">
                    最后更新: {{ $lastUpdated->format('Y-m-d H:i') }}
                </p>
            @endif
        </div>
        <button 
            wire:click="refreshBooks" 
            wire:loading.attr="disabled"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center transition-colors"
        >
            <span wire:loading.remove>刷新数据</span>
            <span wire:loading>
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                更新中...
            </span>
        </button>
    </div>

    <!-- 消息提示 -->
    @if(session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if(session()->has('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
            {{ session('info') }}
        </div>
    @endif

    <!-- 图书网格 -->
    @if(count($books) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($books as $book)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                    <!-- 图书封面 -->
                    <div class="relative">
                        <img 
                            src="{{ $book['image_url'] ?: '/images/book-placeholder.jpg' }}" 
                            alt="{{ $book['title'] }}"
                            class="w-full h-48 object-cover"
                            onerror="this.src='/images/book-placeholder.jpg'"
                        >
                        @if($book['commission_rate'] > 0)
                            <span class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                佣金 {{ $book['commission_rate'] }}%
                            </span>
                        @endif
                    </div>

                    <!-- 图书信息 -->
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2 line-clamp-2 hover:text-blue-600 transition-colors">
                            <a href="{{ $book['product_url'] }}" target="_blank" rel="noopener noreferrer" class="hover:underline">
                                {{ $book['title'] }}
                            </a>
                        </h3>
                        
                        <p class="text-gray-600 text-sm mb-2">
                            {{ $book['author'] }} · {{ $book['publisher'] }}
                        </p>

                        @if($book['publish_date'])
                            <p class="text-gray-500 text-xs mb-3">
                                出版日期: {{ \Carbon\Carbon::parse($book['publish_date'])->format('Y-m-d') }}
                            </p>
                        @endif

                        <!-- 价格信息 -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-red-600 font-bold text-lg">
                                    ¥{{ number_format($book['price'], 2) }}
                                </span>
                                @if($book['original_price'] > $book['price'])
                                    <span class="text-gray-400 text-sm line-through">
                                        ¥{{ number_format($book['original_price'], 2) }}
                                    </span>
                                @endif
                            </div>
                            @if($book['sales_volume'] > 0)
                                <span class="text-green-600 text-sm">
                                    销量 {{ $book['sales_volume'] }}
                                </span>
                            @endif
                        </div>

                        <!-- 购买按钮 -->
                        <a 
                            href="{{ $book['product_url'] }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="w-full bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white text-center py-2 px-4 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 block"
                        >
                            立即购买
                        </a>

                        @if($book['commission_amount'] > 0)
                            <p class="text-green-600 text-sm text-center mt-2">
                                预计佣金: ¥{{ number_format($book['commission_amount'], 2) }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">暂无图书数据</h3>
            <p class="mt-1 text-sm text-gray-500">点击刷新按钮获取设计模式相关图书推荐</p>
            <div class="mt-6">
                <button 
                    wire:click="refreshBooks" 
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    获取图书数据
                </button>
            </div>
        </div>
    @endif
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>