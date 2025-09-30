<div class="bg-white rounded-lg shadow-sm border">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold text-gray-900">{{ __('Preview') }}</h3>
    </div>
    <div class="p-6">
        @if($renderedHtml)
            <div class="prose prose-lg max-w-none">
                {!! $renderedHtml !!}
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mt-2">{{ __('No content to preview') }}</p>
            </div>
        @endif
    </div>
</div>