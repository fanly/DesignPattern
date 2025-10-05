<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
                @if($category->description)
                    <p class="text-lg text-gray-600">{{ $category->description }}</p>
                @endif
            </div>
            <a href="{{ route('categories.index') }}" 
               class="bg-gray-100 text-gray-700 text-sm font-medium px-4 py-2 rounded-md hover:bg-gray-200 transition-colors border border-gray-300">
                {{ __('common.back_to_categories') }}
            </a>
        </div>

        @if($patterns->isNotEmpty())
            <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                @foreach($patterns as $pattern)
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ $pattern->name }}</h3>
                        
                        @if($pattern->description)
                            <p class="text-gray-600 mb-4 leading-relaxed">{{ \Illuminate\Support\Str::limit($pattern->description, 120) }}</p>
                        @endif

                        <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                            <span class="text-sm text-gray-500">{{ __('patterns.design_pattern') }}</span>
                            
                            <a href="{{ route('patterns.show', $pattern->slug) }}" 
                               class="bg-blue-50 text-blue-700 text-sm font-medium px-3 py-2 rounded-md hover:bg-blue-100 transition-colors flex items-center">
                                {{ __('common.view_details') }}
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('patterns.no_patterns') }}</h3>
                <p class="text-gray-600">{{ __('patterns.no_patterns_in_category') }}</p>
            </div>
        @endif
    </div>
</div>