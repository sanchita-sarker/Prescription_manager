<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reminder;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReminderNotification;
use Carbon\Carbon;

class SendReminderEmails extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send email reminders for upcoming medicines';

    public function handle()
    {
        $now = Carbon::now()->format('H:i:s');
        $today = Carbon::today()->toDateString();

        $reminders = Reminder::where('status', 'Active')
            ->where('reminder_time', $now) // matches current time
            ->where('start_date', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->where('end_date', '>=', $today)
                      ->orWhereNull('end_date');
            })
            ->get();

        foreach ($reminders as $reminder) {
            // Replace with the user's actual email
            Mail::to('sarkersanchita319@gmail.com')->send(new ReminderNotification($reminder));
            $this->info("Reminder sent for: {$reminder->medicine_name}");
        }
    }
}

