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
        Schema::create('training_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('training_batch_id')->constrained('training_batches')->cascadeOnDelete();
            $table->enum('result', ['completed', 'failed', 'ongoing'])->default('ongoing');
            $table->decimal('attendance_percentage', 5, 2)->nullable(); // e.g., 95.50
            $table->text('remarks')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete(); // admin who marked it
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // One result per application per batch
            $table->unique(['application_id', 'training_batch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_results');
    }
};
