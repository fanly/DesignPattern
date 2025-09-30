<div style="min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #dbeafe 100%);">
    <div style="max-width: 80rem; margin-left: auto; margin-right: auto; padding-left: 1rem; padding-right: 1rem; padding-top: 2rem; padding-bottom: 2rem;">
        <!-- 面包屑导航 -->
        <nav style="margin-bottom: 2rem;">
            <div style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.875rem; color: #6b7280;">
                <a href="{{ route('home') }}" style="color: #2563eb; text-decoration: none; display: flex; align-items: center; transition: color 0.2s;">
                    <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    首页
                </a>
                <span style="color: #9ca3af;">/</span>
                <span style="color: #111827; font-weight: 500;">{{ $category->name }}</span>
            </div>
        </nav>

        <!-- 分类头部 -->
        <div style="background-color: white; border-radius: 0.75rem; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1); border: 1px solid #f3f4f6; padding: 2rem; margin-bottom: 2rem;">
            <div style="text-align: center;">
                <h1 style="font-size: 2.25rem; font-weight: 700; color: #111827; margin-bottom: 1rem;">{{ $category->name }}</h1>
                <p style="font-size: 1.125rem; color: #6b7280; max-width: 48rem; margin-left: auto; margin-right: auto;">
                    {{ $category->description ?? '该分类下的设计模式详细介绍' }}
                </p>
                <div style="margin-top: 1rem; font-size: 0.875rem; color: #6b7280;">
                    包含 {{ $patterns->count() }} 个设计模式
                </div>
            </div>
        </div>

        <!-- 模式列表 -->
        <div style="display: grid; gap: 1.5rem; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            @foreach($patterns as $pattern)
            <a href="{{ route('patterns.show', $pattern->slug) }}" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); border: 1px solid #e5e7eb; padding: 1.5rem; text-decoration: none; transition: all 0.2s;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 0.75rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; transition: color 0.2s;">{{ $pattern->name }}</h3>
                    <svg style="width: 1.25rem; height: 1.25rem; color: #9ca3af; transition: color 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <p style="color: #6b7280; font-size: 0.875rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $pattern->description ?? '了解该设计模式的详细实现和应用场景' }}</p>
                <div style="margin-top: 1rem; display: flex; align-items: center; font-size: 0.75rem; color: #6b7280;">
                    <span style="background-color: #dbeafe; color: #1e40af; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">查看详情</span>
                </div>
            </a>
            @endforeach
        </div>

        <!-- 空状态 -->
        @if($patterns->isEmpty())
        <div style="text-align: center; padding: 3rem;">
            <svg style="margin-left: auto; margin-right: auto; height: 4rem; width: 4rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <h3 style="margin-top: 1rem; font-size: 1.125rem; font-weight: 500; color: #111827;">暂无设计模式</h3>
            <p style="margin-top: 0.5rem; color: #6b7280;">该分类下还没有添加设计模式</p>
        </div>
        @endif
    </div>
</div>