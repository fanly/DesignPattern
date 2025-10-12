<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Book;
use App\Services\DuomaiService;
use Illuminate\Support\Facades\Cache;

class BookRecommendations extends Component
{
    public $books = [];
    public $loading = false;
    public $lastUpdated;
    
    public function mount()
    {
        $this->loadBooks();
    }
    
    public function loadBooks()
    {
        $cacheKey = 'books_design_patterns';
        
        // 从缓存获取数据
        $this->books = Cache::remember($cacheKey, 3600, function () { // 缓存1小时
            return Book::where('title', 'like', '%设计模式%')
                      ->orWhere('title', 'like', '%design pattern%')
                      ->latest('publish_date')
                      ->limit(20)
                      ->get()
                      ->toArray();
        });
        
        $this->lastUpdated = Book::max('updated_at');
    }
    
    public function refreshBooks()
    {
        $this->loading = true;
        
        try {
            $service = new \App\Services\DuomaiService();
            $updatedCount = $service->updateBooksToDatabase();
            
            $this->loadBooks();
            
            if ($updatedCount > 0) {
                session()->flash('success', "成功更新 {$updatedCount} 本图书数据");
            } else {
                session()->flash('info', '图书数据已是最新，无需更新');
            }
            
        } catch (\Exception $e) {
            session()->flash('error', '更新图书数据失败：' . $e->getMessage());
        }
        
        $this->loading = false;
    }
    
    public function render()
    {
        return view('livewire.book-recommendations')
            ->layout('layouts.app');
    }
}