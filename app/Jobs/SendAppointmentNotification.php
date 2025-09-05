<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\Appointment;
use App\Mail\AppointmentNotification;

class SendAppointmentNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $appointment;
    protected $type;
    protected $userEmail;

    /**
     * Create a new job instance.
     */
    public function __construct(Appointment $appointment, string $type, string $userEmail)
    {
        $this->appointment = $appointment;
        $this->type = $type;
        $this->userEmail = $userEmail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->userEmail)->send(
                new AppointmentNotification($this->appointment, $this->type)
            );
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to send appointment notification: ' . $e->getMessage());
            
            // Optionally, you can re-throw the exception to retry the job
            // throw $e;
        }
    }
}