<div class="bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        <!-- 页面标题区域 -->
        <div class="text-center mb-12">
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6 transform hover:scale-105 transition-transform duration-300">
                <h1 class="text-5xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-4">
                    📚 {{ __('books.title') }}
                </h1>
                <p class="text-xl text-gray-600 mb-3">{{ __('books.subtitle') }}</p>
                <p class="text-sm text-gray-500">{{ __('books.data_update') }}</p>
            </div>
        </div>

        <!-- 图书内容区域 -->
            <!-- 图书网格 -->
            @if(count($books) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($books as $book)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 transform hover:-translate-y-1">
                            <!-- 图书封面 - 优化显示，避免截取 -->
                            <div class="h-64 bg-gradient-to-br from-blue-50 to-indigo-100 overflow-hidden flex items-center justify-center p-4">
                                <img src="{{ $book['pic'] ?? $book['image_url'] ?? '/images/book-placeholder.jpg' }}" 
                                     alt="{{ $book['title'] }}"
                                     class="max-w-full max-h-full object-contain hover:scale-105 transition-transform duration-300 shadow-lg rounded-lg"
                                     onerror="this.src='/images/book-placeholder.jpg'">
                            </div>
                            
                            <div class="p-6">
                                <!-- 标题 -->
                                <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ $book['title'] }}</h3>
                                
                                <!-- 图书信息 -->
                                <div class="space-y-1 mb-3">
                                    @if(!empty($book['author']) && !in_array(strtolower($book['author']), ['未知', '未知作者', '无', '']))
                                    <p class="text-gray-600 text-sm">
                                        <i class="fas fa-user-edit mr-2"></i>{{ __('books.author') }}: {{ $book['author'] }}
                                    @endif
                                    @if(!empty($book['publisher']) && !in_array(strtolower($book['publisher']), ['未知', '未知出版社', '无', '']))
                                    <p class="text-gray-600 text-sm">
                                        <i class="fas fa-building mr-2"></i>{{ __('books.publisher') }}: {{ $book['publisher'] }}
                                    @endif
                                    @if(!empty($book['seller_name']) && !in_array(strtolower($book['seller_name']), ['未知', '无', '']))
                                    <p class="text-gray-600 text-sm">
                                        <i class="fas fa-store mr-2"></i>{{ __('books.store') }}: {{ $book['seller_name'] }}
                                    @endif
                                    @if(!empty($book['publish_date']) && !in_array(strtolower($book['publish_date']), ['未知', '无', '']))
                                    <p class="text-gray-600 text-sm">
                                        <i class="fas fa-calendar mr-2"></i>{{ __('books.publish_date') }}: {{ $book['publish_date'] }}
                                    @endif
                                </div>
                                
                                <!-- 价格信息 -->
                                <div class="flex justify-between items-center mb-4">
                                    @if($book['final_price'])
                                        <span class="text-red-600 font-bold text-xl">¥{{ $book['final_price'] }}</span>
                                        @if($book['price'] && $book['price'] > $book['final_price'])
                                            <span class="text-gray-400 text-sm line-through">¥{{ $book['price'] }}</span>
                                        @endif
                                    @elseif($book['price'])
                                        <span class="text-red-600 font-bold text-xl">¥{{ $book['price'] }}</span>
                                    @endif
                                    
                                    <!-- 好评率显示 - 更明显的呈现形式 -->
                                    @if(!empty($book['good_comment_rate']) && $book['good_comment_rate'] == 100)
                                        <div class="flex items-center bg-green-100 px-3 py-1 rounded-full">
                                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                                            <span class="text-sm font-semibold text-green-800">{{ __('books.100_percent_good_comment') }}</span>
                                        </div>
                                    @elseif(!empty($book['good_comment_rate']) && $book['good_comment_rate'] >= 95)
                                        <div class="flex items-center bg-blue-100 px-3 py-1 rounded-full">
                                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                                            <span class="text-sm font-semibold text-blue-800">{{ $book['good_comment_rate'] }}% {{ __('books.good_comment') }}</span>
                                        </div>
                                    @elseif(!empty($book['sales']) && $book['sales'] > 0)
                                        <div class="flex items-center">
                                            <i class="fas fa-chart-line text-green-500 mr-1"></i>
                                            <span class="text-sm text-gray-600">{{ __('books.sales') }}: {{ $book['sales'] }}</span>
                                        </div>
                                    @elseif(!empty($book['comment_count']) && $book['comment_count'] > 0)
                                        <div class="flex items-center">
                                            <i class="fas fa-comments text-blue-500 mr-1"></i>
                                            <span class="text-sm text-gray-600">{{ __('books.comments') }}: {{ $book['comment_count'] }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- 购买按钮 -->
                                @php
                                    $buyUrl = !empty($book['coupon_url']) ? $book['coupon_url'] : $book['product_url'];
                                @endphp
                                @if(!empty($buyUrl))
                                    <a href="{{ $buyUrl }}" target="_blank" rel="noopener noreferrer"
                                       class="block w-full bg-gradient-to-r from-orange-500 to-red-500 text-white text-center py-3 px-4 rounded-md hover:from-orange-600 hover:to-red-600 transition-all duration-300 font-medium">
                                        <i class="fas fa-shopping-cart mr-2"></i>{{ __('books.buy_now') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- 分页信息 -->
                @if($lastPage > 1)
                <div class="mt-8">
                    <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                        <!-- 分页信息 -->
                        <div class="text-gray-600 text-sm">
                            {{ __('books.showing') }} <span class="font-bold text-blue-600">{{ min(($currentPage - 1) * $perPage + 1, $total) }}</span>{{ __('books.to') }}<span class="font-bold text-blue-600">{{ min($currentPage * $perPage, $total) }}</span> {{ __('books.records') }}，
                            {{ __('books.of') }} <span class="font-bold text-purple-600">{{ $total }}</span> {{ __('books.records') }}
                        </div>
                        
                        <!-- 分页导航 -->
                        <nav class="flex items-center space-x-2">
                            <!-- 上一页 -->
                            @if($currentPage > 1)
                            <a href="?page={{ $currentPage - 1 }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <i class="fas fa-chevron-left mr-1"></i>{{ __('books.previous_page') }}
                            </a>
                            @else
                            <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded-lg cursor-not-allowed font-medium">
                                <i class="fas fa-chevron-left mr-1"></i>{{ __('books.previous_page') }}
                            </span>
                            @endif
                            
                            <!-- 页码 -->
                            <div class="flex items-center space-x-1">
                                @for($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++)
                                    @if($i == $currentPage)
                                    <span class="px-3 py-2 bg-blue-600 text-white rounded-lg font-bold">
                                        {{ $i }}
                                    </span>
                                    @else
                                    <a href="?page={{ $i }}" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                        {{ $i }}
                                    </a>
                                    @endif
                                @endfor
                            </div>
                            
                            <!-- 下一页 -->
                            @if($currentPage < $lastPage)
                            <a href="?page={{ $currentPage + 1 }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                {{ __('books.next_page') }}<i class="fas fa-chevron-right ml-1"></i>
                            </a>
                            @else
                            <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded-lg cursor-not-allowed font-medium">
                                {{ __('books.next_page') }}<i class="fas fa-chevron-right ml-1"></i>
                            </span>
                            @endif
                        </nav>
                    </div>
                </div>
                @else
                <div class="mt-8 text-center text-gray-500 text-sm">
                    {{ __('books.total_books') }} {{ $total }} {{ __('books.design_pattern_books') }}
                </div>
                @endif
            @else
                <!-- 无数据状态 -->
                <div class="text-center py-12">
                    <i class="fas fa-book-open text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg mb-2">{{ __('books.no_data') }}</p>
                    <p class="text-gray-400 text-sm">{{ __('books.data_will_update') }}</p>
                </div>
            @endif
    </div>
</div>