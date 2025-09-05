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
        // Register your custom commands here, e.g.:
        // Commands\SendReminders::class,
    ];

    /**
     * Define the application's command schedule.
     */
   protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        $reminders = \App\Models\Reminder::where('status', 'Active')->get();
        foreach ($reminders as $reminder) {
            \Mail::to('youremail@example.com')->send(new \App\Mail\ReminderNotification($reminder));
        }
    })->everyMinute(); // use everyMinute for testing
}


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        // require base_path('routes/console.php'); // optional
    }
}
