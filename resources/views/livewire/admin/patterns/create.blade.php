<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Create Design Pattern') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('Add a new design pattern with markdown content') }}</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- 编辑表单 -->
            <div class="space-y-6">
                <form wire:submit="save" class="space-y-6">
                    <!-- 标题 -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">{{ __('Title') }}</label>
                        <input type="text" 
                               id="title"
                               wire:model="title" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="{{ __('Enter pattern title') }}">
                        @error('title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700">{{ __('Slug') }}</label>
                        <input type="text" 
                               id="slug"
                               wire:model="slug" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="{{ __('URL slug') }}">
                        @error('slug') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- 分类 -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">{{ __('Category') }}</label>
                        <select id="category_id"
                                wire:model="category_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('Select a category') }}</option>
                            @foreach($categories as $id => $category)
                                <option value="{{ $id }}">{{ $category->getNameAttribute() }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- 描述 -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                        <textarea id="description"
                                  wire:model="description"
                                  rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="{{ __('Brief description of the pattern') }}"></textarea>
                    </div>
                    
                    <!-- 内容编辑 -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700">{{ __('Content (Markdown)') }}</label>
                        <textarea id="content"
                                  wire:model="content"
                                  wire:keydown.debounce.500ms="updatedContent"
                                  rows="15"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                                  placeholder="{{ __('Write your pattern content in markdown format...') }}"></textarea>
                        @error('content') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- 操作按钮 -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('patterns.index') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            {{ __('Save Pattern') }}
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- 实时预览 -->
            <div>
                <div class="sticky top-6">
                    @livewire('admin.pattern-preview')
                </div>
            </div>
        </div>
    </div>
</div>