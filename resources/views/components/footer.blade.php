<footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- 推荐资源区域 - 首页样式 -->
        <div class="mb-12">
            <h3 class="text-xl font-bold mb-6 text-center">{{ __('home.recommendations') }}</h3>
            <p class="text-lg text-gray-300 text-center mb-8">
                {{ __('home.recommendations_description') }}
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- 知识星球推荐 - 只保留大图片 -->
                <div class="flex justify-center">
                    <a href="https://t.zsxq.com/0duAujaAI" 
                       target="_blank"
                       class="block w-64 h-64 rounded-2xl overflow-hidden shadow-2xl hover:shadow-3xl hover:scale-105 transition-all duration-300">
                        <img src="https://image.coding01.cn/blog/48885581888558T2.JPG" 
                             alt="设计模式知识星球" 
                             class="w-full h-full object-cover">
                    </a>
                </div>

                <!-- Laravel 源码推荐 - 保持完整内容 -->
                <div class="bg-gray-800 rounded-xl p-8 text-center hover:bg-gray-700 transition-colors">
                    <div class="w-16 h-16 bg-gradient-to-br flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <!-- Laravel Logo SVG -->
                        <svg class="w-16 h-16 text-white" viewBox="0 0 50 52" fill="currentColor">
                            <path d="M49.626 11.564a.809.809 0 0 1 .028.209v10.972a.8.8 0 0 1-.402.694l-9.209 5.302V39.25c0 .286-.152.55-.4.694L20.42 51.01c-.044.025-.092.041-.14.058-.018.006-.035.017-.054.022a.805.805 0 0 1-.41 0c-.022-.006-.042-.018-.063-.026-.044-.016-.09-.03-.132-.054L.402 39.944A.801.801 0 0 1 0 39.25V6.334c0-.072.01-.142.028-.21.006-.023.02-.044.028-.067.015-.042.029-.085.051-.124.015-.026.037-.047.055-.071.023-.032.044-.065.071-.093.023-.023.053-.04.079-.06.029-.024.055-.050.088-.069h.001l9.61-5.533a.802.802 0 0 1 .8 0l9.61 5.533h.002c.032.02.059.045.088.068.026.02.055.038.078.06.028.029.048.062.072.094.017.024.04.045.054.071.023.04.036.082.052.124.008.023.022.044.028.068a.809.809 0 0 1 .028.209v20.559l8.008-4.611v-10.51c0-.07.01-.141.028-.208.007-.024.02-.045.028-.068.016-.042.03-.085.052-.124.015-.026.037-.047.054-.071.024-.032.044-.065.072-.093.023-.023.052-.04.078-.06.03-.024.056-.05.088-.069h.001l9.611-5.533a.801.801 0 0 1 .8 0l9.61 5.533c.034.02.06.045.09.068.025.02.054.038.077.06.028.029.048.062.072.094.018.024.04.045.054.071.023.039.036.082.052.124.009.023.022.044.028.068zm-1.574 10.718v-9.124l-3.363 1.936-4.646 2.675v9.124l8.01-4.611zm-9.61 16.505v-9.13l-4.57 2.61-13.05 7.448v9.216l17.62-10.144zM1.602 7.719v31.068L19.22 48.93v-9.214l-9.204-5.209-.003-.002-.004-.002c-.031-.018-.057-.044-.086-.066-.025-.02-.054-.036-.076-.058l-.002-.003c-.026-.025-.044-.056-.066-.084-.02-.027-.044-.05-.06-.078l-.001-.003c-.018-.03-.029-.066-.042-.1-.013-.03-.03-.058-.038-.09v-.001c-.01-.038-.012-.078-.016-.117-.004-.03-.012-.06-.012-.09v-.002-21.481L4.965 9.654 1.602 7.72zm8.81-5.994L2.405 6.334l8.005 4.609 8.006-4.61-8.005-4.608zm4.164 28.764l4.645-2.674V7.719l-3.363 1.936-4.646 2.675v20.096l3.364-1.937zM39.243 7.164l-8.006 4.609 8.006 4.609 8.005-4.61-8.005-4.608zm-.801 10.605l-4.646-2.675-3.363-1.936v9.124l4.645 2.674 3.364 1.937v-9.124zM20.02 38.33l11.743-6.704 5.87-3.35-8-4.606-9.211 5.303-8.395 4.833 7.993 4.524z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">
                        {{ __('home.laravel_knowledge') }}
                    </h3>
                    <p class="text-gray-300 mb-6">
                        {{ __('home.laravel_knowledge_description') }}
                    </p>
                    <a href="https://laravel.coding01.cn/" 
                       target="_blank"
                       class="inline-flex items-center bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-400 transition-colors">
                        {{ __('home.visit_now') }}
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Logo and Description -->
            <div class="md:col-span-2">
                <h3 class="text-xl font-bold mb-4">{{ __('nav.site_name') }}</h3>
                <p class="text-gray-400 text-sm leading-relaxed">
                    {{ __('footer.description') }}
                </p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <!-- GitHub Logo -->
                    <a href="https://github.com/fanly" target="_blank" rel="noopener noreferrer" 
                    class="text-gray-400 hover:text-white transition-colors flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                        <span class="text-sm">GitHub</span>
                    </a>
                    
                    <!-- 掘金 Logo -->
                    <a href="https://juejin.cn/user/1978776658642558/posts" target="_blank" rel="noopener noreferrer" 
                    class="text-gray-400 hover:text-white transition-colors flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                        </svg>
                        <span class="text-sm">掘金</span>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-sm font-semibold uppercase tracking-wider mb-4">{{ __('footer.quick_links') }}</h4>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('patterns.index') }}" class="text-gray-400 hover:text-white transition-colors text-sm">
                            {{ __('nav.patterns') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('categories.index') }}" class="text-gray-400 hover:text-white transition-colors text-sm">
                            {{ __('nav.categories') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors text-sm">
                            {{ __('common.home') }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h4 class="text-sm font-semibold uppercase tracking-wider mb-4">{{ __('footer.contact') }}</h4>
                <div class="space-y-2 text-sm text-gray-400">
                    <p>{{ __('footer.email') }}</p>
                    <p>{{ __('footer.wechat') }}</p>
                    <p>{{ __('footer.address') }}</p>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col items-center text-center">
            <p class="text-gray-400 text-sm">
                &copy; {{ date('Y') }} {{ __('nav.site_name') }}. {{ __('footer.rights_reserved') }}
            </p>
            <div class="mt-2">
                <a href="https://beian.miit.gov.cn/" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white text-sm">
                    闽ICP备17030024号-1
                </a>
            </div>
        </div>
    </div>
</footer>