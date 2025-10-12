<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DuomaiService;

class UpdateBooksFromDuomai extends Command
{
    protected $signature = 'books:update';
    protected $description = '从多麦API更新设计模式相关书籍数据';

    public function handle()
    {
        $service = new DuomaiService();
        
        try {
            $updatedCount = $service->updateBooksToDatabase();
            
            if ($updatedCount > 0) {
                $this->info("成功更新 {$updatedCount} 本图书数据");
            } else {
                $this->info('图书数据已是最新，无需更新');
            }
            
        } catch (\Exception $e) {
            $this->error('更新图书数据失败：' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}