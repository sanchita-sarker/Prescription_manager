<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reminder;
use App\Mail\ReminderNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send email reminders for medicines';

    public function handle()
    {
        $now = Carbon::now()->format('H:i');

        $reminders = Reminder::where('status', 'Active')
            ->whereDate('start_date', '<=', now())
            ->where(function($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->get();

        foreach ($reminders as $reminder) {
            if (Carbon::parse($reminder->reminder_time)->format('H:i') === $now) {
                Mail::to($reminder->user->email)
                    ->send(new ReminderNotification($reminder));
            }
        }

        $this->info('Reminders sent!');
    }
}
