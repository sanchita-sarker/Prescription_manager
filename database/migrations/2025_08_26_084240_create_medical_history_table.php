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
        Schema::create('medical_history', function (Blueprint $table) {
    $table->id('history_id');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('condition_name');
    $table->text('description')->nullable();
    $table->date('diagnosed_date')->nullable();
    $table->date('resolved_date')->nullable();
    $table->timestamps();
});

       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_history');
    }
};
