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
       Schema::create('scanned_text', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('image_id');
    $table->text('text_content')->nullable();
    $table->timestamps();

    $table->foreign('image_id')
          ->references('id')->on('prescription_images')
          ->onDelete('cascade');
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scanned_text');
    }
};
