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
        Schema::create('work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('position');
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->decimal('monthly_salary', 12, 2)->nullable();
            $table->string('appointment_status')->nullable(); // e.g., contractual, permanent
            $table->unsignedInteger('years_experience')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_experiences');
    }
};
