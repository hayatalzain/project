<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Notifications\PrivateMessageBetweenUsers;

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
        // $schedule->command('inspire')
        //          ->hourly();

    //if (!$this->osProcessIsRunning('queue:work')) {

    $schedule->command('queue:work --queue=high,default --sleep=3 --tries=3')
    ->everyMinute()
    ->withoutOverlapping()
    ->sendOutputTo(storage_path() . '/logs/queue-jobs.log');


   // $schedule->call(function () {

   //  })->everyMinute();


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




    protected function osProcessIsRunning($needle)
    {
        // get process status. the "-ww"-option is important to get the full output!
        exec('ps aux -ww', $process_status);

        // search $needle in process status
        $result = array_filter($process_status, function($var) use ($needle) {
            return strpos($var, $needle);
        });

        // if the result is not empty, the needle exists in running processes
        if (!empty($result)) {
            return true;
        }
        return false;
    }




}
