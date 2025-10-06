@auth
<div>
    <a href="{{ route('admin.patterns.create') }}" 
       class="flex items-center px-3 py-2 text-sm font-medium rounded-md group hover:bg-gray-700 @if(str_starts_with($this->currentRoute, 'admin.patterns')) bg-gray-700 @endif">
        <span class="truncate">{{ __('admin.pattern_management') }}</span>
    </a>
    <a href="{{ route('admin.categories.create') }}" 
       class="flex items-center px-3 py-2 text-sm font-medium rounded-md group hover:bg-gray-700 @if(str_starts_with($this->currentRoute, 'admin.categories')) bg-gray-700 @endif">
        <span class="truncate">{{ __('admin.category_management') }}</span>
    </a>
    <a href="{{ route('admin.password') }}" 
       class="flex items-center px-3 py-2 text-sm font-medium rounded-md group hover:bg-gray-700 @if(str_starts_with($this->currentRoute, 'admin.password')) bg-gray-700 @endif">
        <span class="truncate">{{ __('admin.password_management') }}</span>
    </a>
</div>
@endauth