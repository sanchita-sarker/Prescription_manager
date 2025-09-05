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
        Schema::create('appointments', function (Blueprint $table) {
    $table->id('appointment_id');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->dateTime('appointment_date');
    $table->string('location')->nullable();
    $table->text('notes')->nullable();
    $table->enum('status',['Scheduled','Completed','Cancelled'])->default('Scheduled');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
