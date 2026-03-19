<?php
// database/migrations/2026_03_04_create_payment_proof_fields.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('payment_proof')->nullable()->after('assessment_status');
            $table->enum('payment_status', ['pending', 'submitted', 'verified', 'rejected'])
                  ->default('pending')
                  ->after('payment_proof');
            $table->timestamp('payment_submitted_at')->nullable()->after('payment_status');
            $table->text('payment_remarks')->nullable()->after('payment_submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['payment_proof', 'payment_status', 'payment_submitted_at', 'payment_remarks']);
        });
    }
};
