<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReminderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $reminder;

    public function __construct($reminder)
    {
        $this->reminder = $reminder;
    }

    public function build()
{
    return $this->subject('Medicine Reminder: ' . $this->reminder->medicine_name)
                ->view('emails.reminder-notification') // must match the filename
                ->with([
                    'medicine' => $this->reminder->medicine_name,
                    'time' => $this->reminder->reminder_time,
                    'frequency' => $this->reminder->frequency,
                ]);
}
}
