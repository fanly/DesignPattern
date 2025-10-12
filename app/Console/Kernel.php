<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\UpdateBooksData::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // 每小时自动更新图书数据
        $schedule->command('books:update-data')->hourly();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}