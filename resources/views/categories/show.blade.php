<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $category->name }}
            </h2>
            @auth
            <a href="{{ route('admin.categories.edit', $category) }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                {{ __('编辑分类') }}
            </a>
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($category->description)
                        <div class="p-4 mb-6 bg-gray-50 rounded-md">
                            <p class="text-gray-700">{{ $category->description }}</p>
                        </div>
                    @endif

                    <h3 class="mb-4 text-lg font-semibold">{{ __('此分类下的设计模式') }}</h3>

                    @if($patterns->isEmpty())
                        <div class="p-4 text-center">
                            <p class="text-gray-500">{{ __('此分类下暂无设计模式') }}</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($patterns as $pattern)
                                <div class="overflow-hidden bg-white border rounded-lg shadow-sm">
                                    <div class="p-4">
                                        <h3 class="mb-2 text-lg font-semibold">
                                            <a href="{{ route('patterns.show', $pattern->slug) }}" class="text-blue-600 hover:underline">
                                                {{ $pattern->name }}
                                            </a>
                                        </h3>
                                        @if($pattern->description)
                                            <p class="text-gray-600">{{ Str::limit($pattern->description, 100) }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between px-4 py-2 bg-gray-50">
                                        <a href="{{ route('patterns.show', $pattern->slug) }}" class="text-sm text-blue-600 hover:underline">
                                            {{ __('阅读更多') }} &rarr;
                                        </a>
                                        @auth
                                            <a href="{{ route('admin.patterns.edit', $pattern) }}" class="text-sm text-gray-600 hover:underline">
                                                {{ __('编辑') }}
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $patterns->links() }}
                        </div>
                    @endif

                    <div class="flex justify-between mt-8 pt-4 border-t border-gray-200">
                        <a href="{{ route('categories.index') }}" class="text-blue-600 hover:underline">
                            &larr; {{ __('返回分类列表') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>