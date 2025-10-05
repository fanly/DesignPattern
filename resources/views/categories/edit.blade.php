<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('编辑分类') }}: {{ $category->getNameAttribute() }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('categories.show', $category->slug) }}" class="px-3 py-1 text-sm text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">
                    {{ __('查看') }}
                </a>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('{{ __('确定要删除此分类吗？') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1 text-sm text-white bg-red-600 rounded-md hover:bg-red-700">
                        {{ __('删除') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name_zh" class="block text-sm font-medium text-gray-700">{{ __('中文名称') }}</label>
                            <input type="text" name="name_zh" id="name_zh" value="{{ old('name_zh', $category->name_zh) }}" required
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            @error('name_zh')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">{{ __('描述') }}</label>
                            <textarea name="description" id="description" rows="3"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="sort_order" class="block text-sm font-medium text-gray-700">{{ __('排序') }}</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category->sort_order) }}"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('categories.show', $category->slug) }}" class="text-sm text-gray-600 underline hover:text-gray-900">
                                {{ __('取消') }}
                            </a>

                            <button type="submit" class="px-4 py-2 ml-4 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('更新') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>