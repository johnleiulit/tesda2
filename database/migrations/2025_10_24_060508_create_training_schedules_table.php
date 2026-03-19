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
        Schema::create('training_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('nc_program'); // matches title_of_assessment_applied_for
            $table->foreignId('training_batch_id')->nullable()->constrained('training_batches')->nullOnDelete();
            $table->string('schedule_name'); // e.g., "Batch 1", "Weekend Class"
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('days'); // e.g., "Monday-Friday", "Saturday-Sunday"
            $table->integer('max_students')->default(25);
            $table->string('venue');
            $table->string('instructor');
            $table->text('description')->nullable();
            $table->string('status')->default('active'); // active, completed, cancelled
            $table->timestamp('schedule_notifications_sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_schedules');
    }
};
