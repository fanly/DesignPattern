@php
    // 获取推荐的设计模式
    $recommendations = \App\Models\DesignPattern::query()
        ->where('id', '!=', $pattern->id)
        ->where('category_id', $pattern->category_id)
        ->inRandomOrder()
        ->limit(4)
        ->get();
@endphp

@if($recommendations->count() > 0)
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('patterns.recommendations') }}</h3>
    <div class="space-y-3">
        @foreach($recommendations as $recommended)
            <a href="{{ route('patterns.show', $recommended->slug) }}" 
               class="group flex items-start space-x-3 p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-1">
                        {{ $recommended->getNameAttribute() }}
                    </h4>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">
                        {{ $recommended->description ?: __('patterns.no_description') }}
                    </p>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif