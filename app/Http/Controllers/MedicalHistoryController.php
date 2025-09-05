<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalHistory;

class MedicalHistoryController extends Controller
{
    public function index()
    {
        $histories = MedicalHistory::where('user_id', auth()->id())->get();
        return view('medical_history.index', compact('histories'));
    }

    public function store(Request $request)
    {
        MedicalHistory::create([
            'user_id' => auth()->id(),
            'condition_name' => $request->condition_name,
            'description' => $request->description,
            'diagnosed_date' => $request->diagnosed_date,
            'resolved_date' => $request->resolved_date,
        ]);

        return redirect()->back()->with('success', 'Medical history added.');
    }

    public function destroy($id)
    {
        $history = MedicalHistory::where('user_id', auth()->id())->findOrFail($id);
        $history->delete();

        return redirect()->back()->with('success', 'Medical history deleted.');
    }
}
