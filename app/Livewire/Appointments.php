<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Appointment;
use App\Jobs\SendAppointmentNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Appointments extends Component
{
    public $doctor_name, $appointment_date, $appointment_time, $location, $notes;
    public $send_email_notification = true;

    protected $rules = [
        'appointment_date' => 'required|date|after_or_equal:today',
        'appointment_time' => 'required',
        'doctor_name' => 'required|string|max:255',
        'location' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
        'send_email_notification' => 'boolean',
    ];

    protected $messages = [
        'appointment_date.after_or_equal' => 'The appointment date must be today or a future date.',
        'appointment_time.required' => 'Please select an appointment time.',
        'doctor_name.required' => 'Doctor name is required.',
    ];

    public function addAppointment()
    {
        // Debug: Log the input values
        Log::info('Attempting to create appointment', [
            'doctor_name' => $this->doctor_name,
            'appointment_date' => $this->appointment_date,
            'appointment_time' => $this->appointment_time,
            'location' => $this->location,
            'notes' => $this->notes,
            'user_id' => auth()->id(),
        ]);

        try {
            // Validate first
            $this->validate();
            
            Log::info('Validation passed');

            // Check if user is authenticated
            if (!auth()->check()) {
                throw new \Exception('User not authenticated');
            }

            // Combine date and time
            $appointmentDateTime = $this->appointment_date . ' ' . $this->appointment_time;
            
            Log::info('Combined datetime', ['datetime' => $appointmentDateTime]);

            // Create appointment with minimal required fields first
            $appointmentData = [
                'user_id' => auth()->id(),
                'doctor_name' => $this->doctor_name,
                'appointment_date' => $appointmentDateTime,
                'notes' => $this->notes,
            ];

            // Add optional fields only if your database has them
            if (\Schema::hasColumn('appointments', 'location')) {
                $appointmentData['location'] = $this->location;
            }
            
            if (\Schema::hasColumn('appointments', 'status')) {
                $appointmentData['status'] = 'scheduled';
            }

            Log::info('Appointment data prepared', $appointmentData);

            $appointment = Appointment::create($appointmentData);
            
            Log::info('Appointment created successfully', ['id' => $appointment->appointment_id]);

            // Try to send email notification (but don't fail if this fails)
            if ($this->send_email_notification && auth()->user()->email) {
                try {
                    // Check if the job class exists
                    if (class_exists('App\Jobs\SendAppointmentNotification')) {
                        SendAppointmentNotification::dispatch(
                            $appointment,
                            'created',
                            auth()->user()->email
                        );
                        Log::info('Email notification queued');
                        $emailMessage = ' Email notification sent.';
                    } else {
                        Log::warning('SendAppointmentNotification job class not found');
                        $emailMessage = '';
                    }
                } catch (\Exception $emailException) {
                    Log::error('Failed to queue email notification', [
                        'error' => $emailException->getMessage()
                    ]);
                    $emailMessage = ' (Email notification failed)';
                }
            } else {
                $emailMessage = '';
            }

            session()->flash('message', 'Appointment added successfully!' . $emailMessage);

            $this->reset(['doctor_name', 'appointment_date', 'appointment_time', 'location', 'notes']);
            $this->send_email_notification = true; // Reset to default

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            session()->flash('error', 'Validation failed: ' . implode(', ', array_flatten($e->errors())));
        } catch (\Exception $e) {
            Log::error('Failed to create appointment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Failed to add appointment: ' . $e->getMessage());
        }
    }

    public function deleteAppointment($appointment_id)
    {
        try {
            $appointment = Appointment::where('user_id', auth()->id())
                ->where('appointment_id', $appointment_id)
                ->first();

            if ($appointment) {
                // Try to send cancellation email before deleting (but don't fail if this fails)
                if (auth()->user()->email && class_exists('App\Jobs\SendAppointmentNotification')) {
                    try {
                        SendAppointmentNotification::dispatch(
                            $appointment,
                            'deleted',
                            auth()->user()->email
                        );
                    } catch (\Exception $emailException) {
                        Log::error('Failed to queue cancellation email', [
                            'error' => $emailException->getMessage()
                        ]);
                    }
                }

                $appointment->delete();
                session()->flash('message', 'Appointment deleted successfully.');
            } else {
                session()->flash('error', 'Appointment not found.');
            }

        } catch (\Exception $e) {
            Log::error('Failed to delete appointment', [
                'error' => $e->getMessage(),
                'appointment_id' => $appointment_id
            ]);
            session()->flash('error', 'Failed to delete appointment: ' . $e->getMessage());
        }
    }

    public function sendReminder($appointment_id)
    {
        try {
            $appointment = Appointment::where('user_id', auth()->id())
                ->where('appointment_id', $appointment_id)
                ->first();

            if ($appointment && auth()->user()->email && class_exists('App\Jobs\SendAppointmentNotification')) {
                SendAppointmentNotification::dispatch(
                    $appointment,
                    'reminder',
                    auth()->user()->email
                );

                session()->flash('message', 'Reminder email sent successfully.');
            } else {
                session()->flash('error', 'Unable to send reminder.');
            }

        } catch (\Exception $e) {
            Log::error('Failed to send reminder', [
                'error' => $e->getMessage(),
                'appointment_id' => $appointment_id
            ]);
            session()->flash('error', 'Failed to send reminder: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $appointments = Appointment::where('user_id', auth()->id())
            ->orderBy('appointment_date', 'asc')
            ->get();

        return view('livewire.appointments', [
            'appointments' => $appointments
        ])->layout('layouts.app');
    }
}