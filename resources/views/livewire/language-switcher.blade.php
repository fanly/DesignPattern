<div class="flex items-center space-x-3">
    <span class="text-sm font-medium text-white/90">{{ __('Language') }}:</span>
    <div class="flex bg-gradient-to-r from-blue-500/20 to-purple-600/20 backdrop-blur-sm rounded-lg p-1">
        @foreach($availableLocales as $locale => $name)
            <button 
                wire:click="switchLanguage('{{ $locale }}')"
                class="px-4 py-2 text-sm font-medium transition-all duration-200 rounded-md {{ $currentLocale === $locale ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-blue-100 shadow-lg' : 'text-white/90 hover:text-white hover:bg-white/20' }}"
            >
                {{ $name }}
            </button>
        @endforeach
    </div>
</div>