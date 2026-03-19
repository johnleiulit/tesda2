<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Now the assessment_batches table exists, so we can add the foreign key
        Schema::table('applications', function (Blueprint $table) {
            $table->foreign('assessment_batch_id')
                  ->references('id')
                  ->on('assessment_batches')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['assessment_batch_id']);
        });
    }
};