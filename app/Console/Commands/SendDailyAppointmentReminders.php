<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Jobs\SendAppointmentNotification;
use Carbon\Carbon;

class SendDailyAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders {--days=1 : Number of days ahead to send reminders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for upcoming appointments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysAhead = $this->option('days');
        $reminderDate = now()->addDays($daysAhead)->startOfDay();
        
        $this->info("Sending reminders for appointments on: " . $reminderDate->format('Y-m-d'));

        $appointments = Appointment::with('user')
            ->whereDate('appointment_date', $reminderDate)
            ->where('status', '!=', 'cancelled')
            ->get();

        if ($appointments->isEmpty()) {
            $this->info('No appointments found for the specified date.');
            return;
        }

        $successCount = 0;
        $failureCount = 0;

        foreach ($appointments as $appointment) {
            try {
                if ($appointment->user && $appointment->user->email) {
                    SendAppointmentNotification::dispatch(
                        $appointment,
                        'reminder',
                        $appointment->user->email
                    );
                    
                    $successCount++;
                    $this->line("✓ Reminder queued for: {$appointment->user->email} - {$appointment->doctor_name}");
                } else {
                    $failureCount++;
                    $this->line("✗ No email found for appointment ID: {$appointment->appointment_id}");
                }
            } catch (\Exception $e) {
                $failureCount++;
                $this->error("✗ Failed to queue reminder for appointment ID: {$appointment->appointment_id} - " . $e->getMessage());
            }
        }

        $this->info("\nSummary:");
        $this->info("Total appointments: " . $appointments->count());
        $this->info("Reminders queued: {$successCount}");
        
        if ($failureCount > 0) {
            $this->warn("Failures: {$failureCount}");
        }
    }
}