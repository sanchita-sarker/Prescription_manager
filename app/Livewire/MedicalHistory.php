<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MedicalHistory as MH;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MedicalHistory extends Component
{
    public $condition_name;
    public $description;
    public $diagnosed_date;
    public $resolved_date;

    public $medical_histories;

    public function mount()
    {
        $this->loadHistories();
    }

    public function render()
    {
        // Use the new slot-compatible layout
       return view('livewire.medical-history')->layout('layouts.app');
    }

    private function loadHistories()
    {
        $this->medical_histories = MH::where('user_id', Auth::id())->get();
    }

    public function add()
    {
        $this->validate([
            'condition_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'diagnosed_date' => 'nullable|date',
            'resolved_date' => 'nullable|date',
        ]);

        MH::create([
            'user_id' => Auth::id(),
            'condition_name' => $this->condition_name,
            'description' => $this->description,
            'diagnosed_date' => $this->diagnosed_date,
            'resolved_date' => $this->resolved_date,
        ]);

        // Clear input fields
        $this->condition_name = '';
        $this->description = '';
        $this->diagnosed_date = '';
        $this->resolved_date = '';

        $this->loadHistories();
    }

    public function delete($history_id)
    {
        $history = MH::find($history_id);
        if ($history) {
            // Optional: save a copy to a log table before deleting
            DB::table('medical_history_log')->insert([
                'user_id' => $history->user_id,
                'condition_name' => $history->condition_name,
                'description' => $history->description,
                'diagnosed_date' => $history->diagnosed_date,
                'resolved_date' => $history->resolved_date,
                'deleted_at' => now(),
            ]);

            $history->delete();
        }

        $this->loadHistories();
    }
}
