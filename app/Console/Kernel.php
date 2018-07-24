<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        '\App\Console\Commands\AddHolidayYearly',
        '\App\Console\Commands\CheckAbsence',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 01. 매년 국가공휴일을 등록하는 스케쥴 설정
        // 01. 毎年の祝日を登録するスケジュールを設定
        $schedule->command('holiday:add')->yearly();

        // 02. 매일 결석자의 결석 데이터를 데이터베이스에 등록하는 스케쥴 설정
        // 02. 毎日の欠席者を照会して、データベースに登録するスケジュールを設定
        $schedule->command('attendance:check_absence')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
