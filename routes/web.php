<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MedicalHistoryController; // Add this import
use App\Livewire\PrescriptionUpload;
use App\Livewire\MedicalHistory;
use App\Livewire\ReminderManager;
use App\Livewire\Appointments;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

// Public welcome page
Route::get('/', function () {
    return view('welcome');
});

// Authenticated + verified routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard (default to prescription upload)
    Route::get('/dashboard', PrescriptionUpload::class)->name('dashboard');

    // Livewire pages
    Route::get('/medical-history', MedicalHistory::class)->name('medical-history');

    
    // Add the POST route for storing medical history
    Route::post('/medical-history', [MedicalHistoryController::class, 'store'])
         ->name('medical_history.store');
    Route::delete('/medical-history/{id}', [MedicalHistoryController::class, 'destroy'])
     ->name('medical_history.destroy');
    Route::get('/reminders', ReminderManager::class)->name('reminders');
    Route::get('/appointments', Appointments::class)->name('appointments');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // routes/web.php
Route::get('/test-email', function () {
    try {
        Mail::raw('Test email from Laravel', function ($message) {
            $message->to('sarkersanchita319@gmail.com')
                    ->subject('Test Email');
        });
        return 'Email sent successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
});

// Authentication routes (login, register, etc.)
require __DIR__.'/auth.php';