<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// 首页 - 使用Volt组件
Volt::route('/', \App\Livewire\Pages\Home::class)->name('home');

// 设计模式路由 - 使用Livewire组件
Volt::route('/patterns', \App\Livewire\Pages\PatternIndex::class)->name('patterns.index');
Volt::route('/patterns/{slug}', \App\Livewire\Pages\PatternShow::class)->name('patterns.show');

// 分类路由
Volt::route('/categories', 'categories.index')->name('categories.index'); 
Volt::route('/categories/{slug}', \App\Livewire\Pages\CategoryShow::class)->name('categories.show');

// 后台管理路由 (需要认证)
Route::middleware(['auth'])->group(function () {
    Volt::route('/admin/patterns/create', 'admin.patterns.create')->name('admin.patterns.create');
    Volt::route('/admin/patterns/{pattern}/edit', 'admin.patterns.edit')->name('admin.patterns.edit');
    
    Volt::route('/admin/categories/create', 'admin.categories.create')->name('admin.categories.create');
    Volt::route('/admin/categories/{category}/edit', 'admin.categories.edit')->name('admin.categories.edit');
});

// Laravel自带的路由
require __DIR__.'/auth.php';
