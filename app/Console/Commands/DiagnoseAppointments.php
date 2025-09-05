<?php

// Create this as: app/Console/Commands/DiagnoseAppointments.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use App\Models\Appointment;
use App\Models\User;

class DiagnoseAppointments extends Command
{
    protected $signature = 'appointments:diagnose';
    protected $description = 'Diagnose appointment system issues';

    public function handle()
    {
        $this->info('🔍 Diagnosing Appointment System...');
        $this->newLine();

        // Check database connection
        try {
            \DB::connection()->getPdo();
            $this->info('✅ Database connection: OK');
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: ' . $e->getMessage());
            return;
        }

        // Check appointments table structure
        $this->info('📋 Checking appointments table structure...');
        
        if (!Schema::hasTable('appointments')) {
            $this->error('❌ appointments table does not exist!');
            return;
        }
        
        $columns = Schema::getColumnListing('appointments');
        $this->info('✅ appointments table exists');
        $this->line('Columns: ' . implode(', ', $columns));
        
        $requiredColumns = ['appointment_id', 'user_id', 'doctor_name', 'appointment_date', 'notes'];
        $missingColumns = array_diff($requiredColumns, $columns);
        
        if (!empty($missingColumns)) {
            $this->error('❌ Missing required columns: ' . implode(', ', $missingColumns));
        } else {
            $this->info('✅ All required columns present');
        }

        // Check optional columns
        $optionalColumns = ['location', 'status'];
        foreach ($optionalColumns as $column) {
            if (in_array($column, $columns)) {
                $this->info("✅ Optional column '{$column}': Present");
            } else {
                $this->warn("⚠️  Optional column '{$column}': Missing (this is OK)");
            }
        }

        // Check User model
        try {
            $userCount = User::count();
            $this->info("✅ Users table: {$userCount} users found");
        } catch (\Exception $e) {
            $this->error('❌ User model issue: ' . $e->getMessage());
        }

        // Check Appointment model
        try {
            $appointmentCount = Appointment::count();
            $this->info("✅ Appointment model: {$appointmentCount} appointments found");
        } catch (\Exception $e) {
            $this->error('❌ Appointment model issue: ' . $e->getMessage());
        }

        // Check if current user can be retrieved
        try {
            if (auth()->check()) {
                $this->info('✅ User authenticated: ' . auth()->user()->email ?? 'No email');
            } else {
                $this->warn('⚠️  No user currently authenticated (this is normal for CLI)');
            }
        } catch (\Exception $e) {
            $this->error('❌ Auth issue: ' . $e->getMessage());
        }

        // Check required classes
        $classes = [
            'App\Models\Appointment' => Appointment::class,
            'App\Mail\AppointmentNotification' => 'App\Mail\AppointmentNotification',
            'App\Jobs\SendAppointmentNotification' => 'App\Jobs\SendAppointmentNotification',
        ];

        foreach ($classes as $name => $class) {
            if (class_exists($class)) {
                $this->info("✅ Class exists: {$name}");
            } else {
                $this->error("❌ Class missing: {$name}");
            }
        }

        // Check email template
        $emailTemplate = resource_path('views/emails/appointment-notification.blade.php');
        if (file_exists($emailTemplate)) {
            $this->info('✅ Email template exists');
        } else {
            $this->error('❌ Email template missing: ' . $emailTemplate);
        }

        // Check queue configuration
        $queueConnection = config('queue.default');
        $this->info("✅ Queue connection: {$queueConnection}");

        if ($queueConnection === 'database') {
            if (Schema::hasTable('jobs')) {
                $jobCount = \DB::table('jobs')->count();
                $this->info("✅ Jobs table exists with {$jobCount} pending jobs");
            } else {
                $this->error('❌ Jobs table missing! Run: php artisan queue:table && php artisan migrate');
            }
        }

        $this->newLine();
        $this->info('🎯 Diagnosis complete!');
        
        // Test appointment creation
        $this->newLine();
        if ($this->confirm('Would you like to test appointment creation?')) {
            $this->testAppointmentCreation();
        }
    }

    private function testAppointmentCreation()
    {
        $this->info('🧪 Testing appointment creation...');

        try {
            // Get a user for testing
            $user = User::first();
            if (!$user) {
                $this->error('❌ No users found for testing');
                return;
            }

            $testData = [
                'user_id' => $user->id,
                'doctor_name' => 'Test Doctor',
                'appointment_date' => now()->addDay(),
                'notes' => 'Test appointment for diagnosis',
            ];

            // Add optional fields if they exist
            if (Schema::hasColumn('appointments', 'location')) {
                $testData['location'] = 'Test Clinic';
            }
            
            if (Schema::hasColumn('appointments', 'status')) {
                $testData['status'] = 'scheduled';
            }

            $appointment = Appointment::create($testData);
            
            $this->info('✅ Test appointment created successfully!');
            $this->line('Appointment ID: ' . $appointment->appointment_id);
            
            // Clean up
            $appointment->delete();
            $this->info('✅ Test appointment cleaned up');
            
        } catch (\Exception $e) {
            $this->error('❌ Test appointment creation failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}