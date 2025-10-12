<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DuomaiService;
use Illuminate\Support\Facades\Log;

class UpdateBooksData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新多麦API图书数据到数据库';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('开始更新图书数据...');
        
        try {
            $service = new DuomaiService();
            $updatedCount = $service->updateBooksToDatabase();
            
            if ($updatedCount > 0) {
                $this->info("成功更新 {$updatedCount} 本图书数据");
                Log::info("定时任务成功更新 {$updatedCount} 本图书数据");
            } else {
                $this->info('图书数据已是最新，无需更新');
                Log::info('定时任务：图书数据已是最新，无需更新');
            }
            
        } catch (\Exception $e) {
            $this->error('更新图书数据失败：' . $e->getMessage());
            Log::error('定时任务更新图书数据失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
