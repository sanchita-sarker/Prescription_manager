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
        Schema::create('reminders', function (Blueprint $table) {
    $table->id('reminder_id');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('medicine_name');
    $table->time('reminder_time');
    $table->date('start_date');
    $table->date('end_date')->nullable();
    $table->enum('frequency',['Daily','Weekly','Monthly'])->default('Daily');
    $table->enum('status',['Active','Completed'])->default('Active');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
