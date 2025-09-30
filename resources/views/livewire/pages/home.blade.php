
<div style="min-height: 100vh; background-color: #f3f4f6;">
    <div style="max-width: 80rem; margin-left: auto; margin-right: auto; padding-left: 1rem; padding-right: 1rem; padding-top: 3rem; padding-bottom: 3rem;">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h1 style="font-size: 2.25rem; font-weight: 700; color: #111827; margin-bottom: 1rem;">{{ __('home.title') }}</h1>
            <p style="font-size: 1.25rem; color: #6b7280;">{{ __('home.subtitle') }}</p>
        </div>

        <div style="display: grid; gap: 1.5rem; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            @forelse($categories as $category)
                <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h2 style="font-size: 1.125rem; font-weight: 600; color: #111827;">{{ $category->getNameAttribute() }}</h2>
                        <span style="background-color: #dbeafe; color: #1e40af; font-size: 0.75rem; font-weight: 500; padding: 0.25rem 0.5rem; border-radius: 9999px;">
                            {{ $category->designPatterns->count() }} {{ __('home.patterns_count') }}
                        </span>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach($category->designPatterns as $pattern)
                            <a href="{{ route('patterns.show', $pattern->slug) }}" 
                               style="display: flex; align-items: center; padding: 0.5rem; border-radius: 0.25rem; color: #374151; text-decoration: none; transition: background-color 0.2s;">
                                <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ $pattern->getNameAttribute() }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">
                    <p style="color: #6b7280;">{{ __('home.no_categories') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>