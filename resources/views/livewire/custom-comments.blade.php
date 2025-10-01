<div>
    <section class="bg-white dark:bg-gray-900 py-8 lg:py-16">
        <div class="max-w-2xl mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">
                    {{ trans('commentify.discussion') }} ({{ $comments->count() }})
                </h2>
            </div>

            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if (session()->has('message'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('message') }}
                </div>
            @endif

            @auth
                <!-- 用户信息区域 -->
                <div class="flex justify-between items-center mb-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="flex items-center">
                        <img class="w-8 h-8 rounded-full mr-3" 
                             src="{{ auth()->user()->avatar_url ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode(auth()->user()->name) }}" 
                             alt="{{ auth()->user()->name }}">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ auth()->user()->name }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ url()->current() }}">
                        <button type="submit" 
                                class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                            退出登录
                        </button>
                    </form>
                </div>

                <form wire:submit.prevent="postComment" class="mb-6">
                    <div class="py-2 px-4 mb-4 bg-white rounded-lg rounded-t-lg border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                        <label for="comment" class="sr-only">{{ trans('commentify::commentify.your_comment') }}</label>
                        <textarea wire:model="newCommentState.body" id="comment" rows="6"
                            class="px-0 w-full text-sm text-gray-900 border-0 focus:ring-0 focus:outline-none dark:text-white dark:placeholder-gray-400 dark:bg-gray-800"
                            placeholder="{{ trans('commentify::commentify.leave_a_comment') }}" required></textarea>
                    </div>
                    <button type="submit"
                        class="inline-flex items-center py-2.5 px-4 text-xs font-medium text-center text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-blue-800">
                        {{ trans('commentify::commentify.post_comment') }}
                    </button>
                </form>
            @else
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600 mb-2">{{ trans('commentify::commentify.login_to_comment') }}</p>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('login', ['intended' => url()->current()]) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                            {{ trans('commentify::commentify.login') }}
                        </a>
                        <a href="{{ route('register', ['intended' => url()->current()]) }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                            {{ trans('commentify::commentify.register') }}
                        </a>
                    </div>
                </div>
            @endauth

            @if($comments->count())
                @foreach($comments as $comment)
                    <article class="p-6 text-base bg-white rounded-lg dark:bg-gray-900 mb-4">
                        <footer class="flex justify-between items-center mb-2">
                            <div class="flex items-center">
                                <p class="inline-flex items-center mr-3 text-sm text-gray-900 dark:text-white font-semibold">
                                    <img class="mr-2 w-6 h-6 rounded-full" src="{{ $comment->user->avatar_url ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($comment->user->name) }}" alt="{{ $comment->user->name }}">
                                    {{ $comment->user->name }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <time pubdate datetime="{{ $comment->created_at->format('Y-m-d\TH:i:s') }}" title="{{ $comment->created_at->format('Y年m月d日 H:i') }}">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </time>
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button wire:click="toggleLike({{ $comment->id }})" 
                                        class="flex items-center space-x-1 text-sm text-gray-500 hover:text-red-500 transition-colors"
                                        title="{{ $this->isLikedByUser($comment->id) ? '取消点赞' : '点赞' }}">
                                    <svg class="w-4 h-4 {{ $this->isLikedByUser($comment->id) ? 'text-red-500 fill-current' : 'text-gray-400' }}" 
                                         fill="{{ $this->isLikedByUser($comment->id) ? 'currentColor' : 'none' }}" 
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span>{{ $comment->likes_count ?? 0 }}</span>
                                </button>
                            </div>
                        </footer>
                        <p class="text-gray-500 dark:text-gray-400">{{ $comment->body }}</p>
                    </article>
                @endforeach
                {{ $comments->links() }}
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center">{{ trans('commentify::commentify.no_comments_yet') }}</p>
            @endif
        </div>
    </section>
</div>
