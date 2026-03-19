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
        Schema::table('applications', function (Blueprint $table) {
            // Add training_batch_id after training_status
            $table->foreignId('training_batch_id')
                ->nullable()
                ->after('training_status')
                ->constrained('training_batches')
                ->nullOnDelete();
            
            // Add training_schedule_id after training_batch_id
            $table->foreignId('training_schedule_id')
                ->nullable()
                ->after('training_batch_id')
                ->constrained('training_schedules')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['training_batch_id']);
            $table->dropForeign(['training_schedule_id']);
            $table->dropColumn(['training_batch_id', 'training_schedule_id']);
        });
    }
};
