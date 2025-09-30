<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800 dark:text-neutral-200 leading-tight">
            {{ __('categories.title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-neutral-900 dark:text-neutral-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($categories as $category)
                            <div class="bg-neutral-50 dark:bg-neutral-700 rounded-lg p-6 hover:shadow-lg transition-shadow duration-300">
                                <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-2">
                                    {{ $category->name }}
                                </h3>
                                @if($category->description)
                                    <p class="text-neutral-600 dark:text-neutral-400 text-sm mb-4">
                                        {{ $category->description }}
                                    </p>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ $category->designPatterns()->count() }} {{ __('patterns') }}
                                    </span>
                                    <a href="{{ route('categories.show', $category->slug) }}" 
                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                                        {{ __('common.view_details') }} →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($categories->isEmpty())
                        <div class="text-center py-12">
                            <div class="text-neutral-500 dark:text-neutral-400">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                                <p class="text-lg">{{ __('categories.no_categories_found') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>