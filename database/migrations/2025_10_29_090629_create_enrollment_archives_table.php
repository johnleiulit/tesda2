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
        Schema::create('enrollment_archives', function (Blueprint $table) {
            $table->id();
            $table->string('program')->index(); // matches applications.title_of_assessment_applied_for
            $table->foreignId('archived_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('archived_at')->useCurrent();
            $table->unique(['program']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_archives');
    }
};
