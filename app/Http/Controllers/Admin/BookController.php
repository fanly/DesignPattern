<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DuomaiService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function update(Request $request)
    {
        $service = new DuomaiService();
        
        try {
            $updatedCount = $service->updateBooksToDatabase();
            
            if ($updatedCount > 0) {
                return redirect()->route('books')->with('success', "成功更新 {$updatedCount} 本图书数据");
            } else {
                return redirect()->route('books')->with('info', '图书数据已是最新，无需更新');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('books')->with('error', '更新图书数据失败：' . $e->getMessage());
        }
    }
}