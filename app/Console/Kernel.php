<?php

namespace App\Console;

use App\Conversations\Followup;
use App\FbUser;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Facades\BotMan;
use BotMan\Drivers\Facebook\FacebookDriver;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookImageDriver::class);
DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookLocationDriver::class);


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $unfollowed_users = DB::select('SELECT * FROM fb_users WHERE DATEDIFF(NOW(),created_at)<10');
            if (count($unfollowed_users)>0){
                foreach ($unfollowed_users as $u_user){
                    $botman = resolve('botman');
                    $botman->say('Thank you for using the HIV Self Testing ChartBot.', $u_user->user_id, FacebookDriver::class);
                    $botman->startConversation(new Followup($botman), $u_user->user_id, FacebookDriver::class);
                    $botman = new BotMan;
                    $botman->say('Thank you for using the HIV Self Testing ChartBot2.', $u_user->user_id, FacebookDriver::class);
                }
            }
        })->everyMinute();
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
