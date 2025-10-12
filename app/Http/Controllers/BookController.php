<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::where('title', 'like', '%设计模式%')
                    ->orWhere('title', 'like', '%design pattern%')
                    ->latest('publish_date')
                    ->limit(20)
                    ->get();

        return view('books.index', compact('books'));
    }
}