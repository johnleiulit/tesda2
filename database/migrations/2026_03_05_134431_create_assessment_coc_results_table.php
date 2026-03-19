<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_coc_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_result_id')->constrained('assessment_results')->cascadeOnDelete();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->string('coc_code'); // e.g., 'COC 1', 'COC 2'
            $table->string('coc_title'); // e.g., 'Journalize Transactions'
            $table->enum('result', ['competent', 'not_yet_competent'])->default('not_yet_competent');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['application_id', 'coc_code']);
            $table->index('result');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_coc_results');
    }
};
