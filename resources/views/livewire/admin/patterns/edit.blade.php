<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">编辑设计模式</h1>
        <p class="text-gray-600 mt-2">修改设计模式内容</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form wire:submit.prevent="save">
            @if (session()->has('message'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('message') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6">
                <!-- 中文标题 -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">中文标题</label>
                    <input type="text" id="title" wire:model="title" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="输入设计模式的中文标题">
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- 英文标题 -->
                <div>
                    <label for="title_en" class="block text-sm font-medium text-gray-700 mb-2">英文标题</label>
                    <input type="text" id="title_en" wire:model="title_en" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="输入设计模式的英文标题">
                    @error('title_en') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- 描述 -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">描述</label>
                    <textarea id="description" wire:model="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              placeholder="输入设计模式的描述（可选）"></textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- 分类 -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">分类</label>
                    <select id="category_id" wire:model="category_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">选择分类</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->getNameAttribute() }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- 别名 -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">别名</label>
                    <input type="text" id="slug" wire:model="slug" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="输入设计模式的URL别名">
                    @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    <p class="text-sm text-gray-500 mt-1">别名将用于URL中，建议使用英文小写字母和连字符</p>
                </div>

                <!-- 内容 -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">内容</label>
                    <textarea id="content" wire:model="content" rows="15"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                              placeholder="输入设计模式的Markdown内容"></textarea>
                    @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    <p class="text-sm text-gray-500 mt-1">支持Markdown语法</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('admin.dashboard') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    取消
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    更新设计模式
                </button>
            </div>
        </form>
    </div>
</div>