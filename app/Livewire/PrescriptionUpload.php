<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Storage;

class PrescriptionUpload extends Component
{
    use WithFileUploads;

    public $uploadedFile;
    public $uploadSuccess = false;
    public $extractedText = '';
    public $imageUrl;

    protected $rules = [
        'uploadedFile' => 'required|image|mimes:jpg,jpeg,png|max:5120',
    ];

    public function uploadPrescription()
{
    $this->validate();

    if (!$this->uploadedFile) {
        $this->addError('uploadedFile', 'Please select a file.');
        return;
    }

    // Store the uploaded image
    $path = $this->uploadedFile->store('uploads', 'public');

    // Save record in DB
    $prescription = \App\Models\PrescriptionImage::create([
        'user_id' => auth()->id(),
        'file_path' => $path,
    ]);

    $this->imageUrl = asset('storage/' . $path);

    // Run OCR
    try {
        $this->extractedText = (new TesseractOCR(storage_path('app/public/' . $path)))
                                ->lang('eng')
                                ->run();
    } catch (\Exception $e) {
        $this->addError('ocr', 'Failed to scan the image: ' . $e->getMessage());
    }

    $this->uploadedFile = null;
    $this->uploadSuccess = true;
}

    public function deleteImage($id)
{
    $prescription = \App\Models\PrescriptionImage::findOrFail($id);

    if ($prescription->user_id == auth()->id()) {
        if (Storage::disk('public')->exists($prescription->file_path)) {
            Storage::disk('public')->delete($prescription->file_path);
        }
        $prescription->delete();
    }
}


    public function render()
{
    $prescriptions = \App\Models\PrescriptionImage::where('user_id', auth()->id())->get();

    return view('livewire.prescription-upload', [
        'prescriptions' => $prescriptions
    ])->layout('layouts.app');
}

}
