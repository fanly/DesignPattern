<div style="min-height: 100vh; background-color: #f3f4f6;">
    <div style="max-width: 80rem; margin-left: auto; margin-right: auto; padding-left: 1rem; padding-right: 1rem; padding-top: 3rem; padding-bottom: 3rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
            <div>
                <h1 style="font-size: 2.25rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">{{ $category->getNameAttribute() }}</h1>
                @if($category->description_zh)
                    <p style="font-size: 1.125rem; color: #6b7280;">{{ $category->description_zh }}</p>
                @endif
            </div>
            <a href="{{ route('categories.index') }}" 
               style="background-color: #f3f4f6; color: #374151; font-size: 0.875rem; font-weight: 500; padding: 0.5rem 1rem; border-radius: 0.375rem; text-decoration: none; border: 1px solid #d1d5db;">
                {{ __('common.back_to_categories') }}
            </a>
        </div>

        @if($patterns->isNotEmpty())
            <div style="display: grid; gap: 1.5rem; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                @foreach($patterns as $pattern)
                    <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 1.5rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 0.75rem;">{{ $pattern->getNameAttribute() }}</h3>
                        
                        @if($pattern->description_zh)
                            <p style="color: #6b7280; margin-bottom: 1rem; line-height: 1.5;">{{ \Illuminate\Support\Str::limit($pattern->description_zh, 120) }}</p>
                        @endif

                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid #f3f4f6;">
                            <span style="font-size: 0.875rem; color: #6b7280;">{{ __('patterns.design_pattern') }}</span>
                            
                            <a href="{{ route('patterns.show', $pattern->slug) }}" 
                               style="background-color: #dbeafe; color: #1e40af; font-size: 0.875rem; font-weight: 500; padding: 0.375rem 0.75rem; border-radius: 0.375rem; text-decoration: none; display: flex; align-items: center;">
                                {{ __('common.view_details') }}
                                <svg style="width: 1rem; height: 1rem; margin-left: 0.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 3rem;">
                <svg style="width: 4rem; height: 4rem; margin: 0 auto 1rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                <h3 style="font-size: 1.5rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">{{ __('patterns.no_patterns') }}</h3>
                <p style="color: #6b7280;">{{ __('patterns.no_patterns_in_category') }}</p>
            </div>
        @endif
    </div>
</div>