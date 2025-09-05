<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reminder;
use Illuminate\Support\Facades\Auth;
use App\Mail\ReminderNotification;
use Illuminate\Support\Facades\Mail;
class ReminderManager extends Component
{
    public $reminders;
    public $medicine_name, $reminder_time, $start_date, $end_date, $frequency;
    public $editMode = false;
    public $currentReminderId;

    protected $rules = [
        'medicine_name' => 'required|string|max:255',
        'reminder_time' => 'required',
        'start_date' => 'required|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'frequency' => 'required|in:Daily,Weekly,Monthly',
    ];

    public function mount()
    {
        $this->loadReminders();
    }

    public function render()
    {
        return view('livewire.reminder-manager')->layout('layouts.app');
    }

    public function loadReminders()
    {
        $this->reminders = Reminder::where('user_id', Auth::id())->get();
    }

    public function resetForm()
    {
        $this->medicine_name = '';
        $this->reminder_time = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->frequency = '';
        $this->editMode = false;
        $this->currentReminderId = null;
    }

    public function store()
    {
        $this->validate();

        Reminder::create([
            'user_id' => Auth::id(),
            'medicine_name' => $this->medicine_name,
            'reminder_time' => $this->reminder_time,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'frequency' => $this->frequency,
            'status' => 'Active',
        ]);

        $this->resetForm();
        $this->loadReminders();
        session()->flash('message', 'Reminder added successfully.');
    }

     public function edit($id)
     {
         $reminder = Reminder::findOrFail($id);

         $this->currentReminderId = $reminder->reminder_id;
         $this->medicine_name = $reminder->medicine_name;
         $this->reminder_time = $reminder->reminder_time;
         $this->start_date = $reminder->start_date;
         $this->end_date = $reminder->end_date;
         $this->frequency = $reminder->frequency;
         $this->editMode = true;
     }

     public function update()
     {
         $this->validate();

         $reminder = Reminder::findOrFail($this->currentReminderId);

        $reminder->update([
             'medicine_name' => $this->medicine_name,
             'reminder_time' => $this->reminder_time,
             'start_date' => $this->start_date,
             'end_date' => $this->end_date,
             'frequency' => $this->frequency,
         ]);

           $this->resetForm();
           $this->loadReminders();
           session()->flash('message', 'Reminder updated successfully.');
     }

     public function cancelEdit()
     {
         $this->resetForm();
     }

    public function delete($id)
    {
        Reminder::findOrFail($id)->delete();
        $this->loadReminders();
        session()->flash('message', 'Reminder deleted successfully.');
    }

    public function complete($id)
    {
        $reminder = Reminder::findOrFail($id);
        $reminder->update(['status' => 'Completed']);
        $this->loadReminders();
        session()->flash('message', 'Reminder marked as completed.');
    }

    public function sendReminder($reminder)
    {
        Mail::to(auth()->user()->email)->send(new ReminderNotification($reminder));
    }
}
