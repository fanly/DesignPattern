<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


// 首页 - 使用Volt组件
Volt::route('/', \App\Livewire\Pages\Home::class)->name('home');

// 设计模式路由 - 使用Livewire组件
Volt::route('/patterns', \App\Livewire\Pages\PatternIndex::class)->name('patterns.index');
Volt::route('/patterns/{slug}', \App\Livewire\Pages\PatternShow::class)->name('patterns.show');

// 分类路由 - 重定向到模式页面
Route::redirect('/categories', '/patterns')->name('categories.index');
Route::redirect('/categories/{slug}', '/patterns')->name('categories.show');

// 后台管理路由 (需要认证)
Route::middleware(['auth'])->group(function () {
    // 管理员仪表板
    Volt::route('/admin', 'admin.dashboard')->name('admin.dashboard');
    
    Volt::route('/admin/patterns/create', 'admin.patterns.create')->name('admin.patterns.create');
    Volt::route('/admin/patterns/{pattern}/edit', 'admin.patterns.edit')->name('admin.patterns.edit');
    
    Volt::route('/admin/categories/create', 'admin.categories.create')->name('admin.categories.create');
    Volt::route('/admin/categories/{category}/edit', 'admin.categories.edit')->name('admin.categories.edit');
    
    // 管理员密码修改
    Volt::route('/admin/password', 'admin.password')->name('admin.password');
});


// 图书推荐路由
Route::get('/books', [\App\Http\Controllers\BookController::class, 'index'])->name('books');

// 管理员图书更新路由
Route::post('/admin/books/update', [\App\Http\Controllers\Admin\BookController::class, 'update'])->name('admin.books.update');

// 语言切换路由
Route::get('/change-locale/{locale}', function ($locale) {
    if (in_array($locale, ['zh', 'en'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('change-locale');

// Laravel自带的路由
require __DIR__.'/auth.php';
