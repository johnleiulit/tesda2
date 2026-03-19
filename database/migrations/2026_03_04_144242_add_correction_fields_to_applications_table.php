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
        $table->boolean('correction_requested')->default(false)->after('status');
        $table->text('correction_message')->nullable()->after('correction_requested');
        $table->timestamp('correction_requested_at')->nullable()->after('correction_message');
        $table->boolean('was_corrected')->default(false)->after('correction_requested_at');
        $table->timestamp('resubmitted_at')->nullable()->after('was_corrected');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
             $table->dropColumn([
                'correction_requested',
                'correction_message',
                'correction_requested_at',
                'was_corrected',
                'resubmitted_at',
            ]);
        });
    }
};
