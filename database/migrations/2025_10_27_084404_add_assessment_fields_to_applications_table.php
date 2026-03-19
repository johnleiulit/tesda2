<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Add fields WITHOUT foreign key constraint (yet)
            $table->unsignedBigInteger('assessment_batch_id')->nullable();
            $table->enum('assessment_status', ['pending', 'assigned', 'completed', 'failed'])->default('pending');
            $table->timestamp('assessment_date')->nullable();
            
            $table->index('assessment_status');
            $table->index('assessment_batch_id');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['assessment_status']);
            $table->dropIndex(['assessment_batch_id']);
            $table->dropColumn(['assessment_batch_id', 'assessment_status', 'assessment_date']);
        });
    }
};