<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('parsed_medicines', function (Blueprint $table) {
    $table->id('parsed_id');
    $table->foreignId('scan_id')->constrained('scanned_text')->onDelete('cascade');
    $table->string('medicine_name');
    $table->string('dosage')->nullable();
    $table->string('frequency')->nullable();
    $table->string('duration')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parsed_medicines');
    }
};
