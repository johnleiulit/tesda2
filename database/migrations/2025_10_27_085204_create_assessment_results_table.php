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
        Schema::create('assessment_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_batch_id')->constrained()->cascadeOnDelete();
            $table->enum('result', ['Competent', 'Not Yet Competent', 'incomplete'])->nullable();
            $table->decimal('score', 5, 2)->nullable(); // e.g., 85.50
            $table->text('remarks')->nullable();
            $table->foreignId('assessed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assessed_at')->nullable();
            $table->integer('assessment_attempt_count')->default(1);
            $table->timestamps();
    
            $table->unique(['application_id', 'assessment_batch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_results');
    }
};
