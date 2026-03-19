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
        Schema::create('training_batches', function (Blueprint $table) {
            $table->id();
            $table->string('nc_program'); // e.g., "BOOKKEEPING NC III"
            $table->integer('batch_number'); // 1, 2, 3, etc.
            $table->integer('max_students')->default(25);
            $table->enum('status', ['enrolling', 'full', 'scheduled', 'ongoing', 'completed', 'cancelled'])->default('enrolling');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // Ensure one batch number per program
            $table->unique(['nc_program', 'batch_number']);
            $table->index(['nc_program', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_batches');
    }
};
