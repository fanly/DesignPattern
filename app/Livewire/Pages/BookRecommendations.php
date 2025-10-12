<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Book;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class BookRecommendations extends Component
{
    use WithPagination;
    
    public $lastUpdated;
    protected $paginationTheme = 'bootstrap';
    
    public function mount()
    {
        $this->lastUpdated = now();
    }
    
    public function getBooks()
    {
        $cacheKey = 'books_design_patterns';
        
        // 从缓存获取数据
        return Cache::remember($cacheKey, 3600, function () { // 缓存1小时
            return Book::searchDesignPatternBooks();
        });
    }
    
    public function render()
    {
        $books = $this->getBooks();
        
        // 手动实现分页
        $perPage = 12;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedBooks = array_slice($books->toArray(), $offset, $perPage);
        $total = count($books);
        
        return view('livewire.book-recommendations', [
            'books' => collect($paginatedBooks),
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $currentPage,
            'lastPage' => ceil($total / $perPage),
        ]);
    }
}