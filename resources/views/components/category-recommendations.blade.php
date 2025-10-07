@php
    // 获取推荐的其他分类
    $recommendations = \App\Models\PatternCategory::query()
        ->where('id', '!=', $category->id)
        ->whereHas('designPatterns')
        ->inRandomOrder()
        ->limit(3)
        ->get();
@endphp

@if($recommendations->count() > 0)
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('category.recommendations') }}</h3>
    <div class="grid grid-cols-1 gap-4">
        @foreach($recommendations as $recommended)
            <a href="{{ route('categories.show', $recommended->slug) }}" 
               class="group block p-4 rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
                            {{ $recommended->getNameAttribute() }}
                        </h4>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $recommended->designPatterns->count() }} {{ __('patterns.patterns_count') }}
                        </p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif