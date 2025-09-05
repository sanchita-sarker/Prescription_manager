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
        Schema::table('appointments', function (Blueprint $table) {
            // Add location column if it doesn't exist
            if (!Schema::hasColumn('appointments', 'location')) {
                $table->string('location')->nullable()->after('appointment_date');
            }
            
            // Modify status column to have default value if it doesn't exist
            if (!Schema::hasColumn('appointments', 'status')) {
                $table->enum('status', ['scheduled', 'completed', 'cancelled', 'no_show'])
                      ->default('scheduled')
                      ->after('notes');
            }
            
            // Add indexes for better performance
            $table->index(['user_id', 'appointment_date']);
            $table->index(['appointment_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Remove indexes
            $table->dropIndex(['user_id', 'appointment_date']);
            $table->dropIndex(['appointment_date', 'status']);
            
            // Remove columns if they were added by this migration
            if (Schema::hasColumn('appointments', 'location')) {
                $table->dropColumn('location');
            }
            
            if (Schema::hasColumn('appointments', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};