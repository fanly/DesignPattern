<x-layouts.app>
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Enhanced Markdown 演示</h1>
        <p class="text-gray-600 mt-2">测试代码高亮和 Mermaid 流程图功能</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 编辑器 -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow">
                <div class="px-4 py-3 bg-gray-50 rounded-t-lg border-b">
                    <h2 class="text-lg font-semibold text-gray-800">Markdown 编辑器</h2>
                </div>
                <div class="p-4">
                    <textarea 
                        wire:model.live.debounce.500ms="content"
                        class="w-full h-96 p-3 border border-gray-300 rounded-md font-mono text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="输入 Markdown 内容..."
                    ></textarea>
                </div>
            </div>
        </div>

        <!-- 预览 -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow">
                <div class="px-4 py-3 bg-gray-50 rounded-t-lg border-b">
                    <h2 class="text-lg font-semibold text-gray-800">实时预览</h2>
                </div>
                <div class="p-4">
                    <div class="h-96 overflow-auto">
                        <x-enhanced-markdown 
                            class="prose prose-sm max-w-none"
                            :content="$content" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 功能说明 -->
    <div class="mt-8 bg-blue-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">支持的功能</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-blue-800">代码语法高亮</span>
            </div>
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-blue-800">Mermaid 流程图</span>
            </div>
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-blue-800">表格支持</span>
            </div>
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-blue-800">任务列表</span>
            </div>
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-blue-800">标题锚点</span>
            </div>
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-blue-800">自动链接</span>
            </div>
        </div>
    </div>
</div>
</x-layouts.app>