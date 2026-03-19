<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Reassessment tracking
            $table->boolean('is_reassessment')->default(false)->after('application_type');
            $table->integer('reassessment_attempt')->default(0)->after('is_reassessment');

            // First Payment tracking
            $table->decimal('reassessment_fee', 10, 2)->nullable()->after('reassessment_attempt');
            $table->string('reassessment_payment_proof')->nullable()->after('reassessment_fee');
            $table->timestamp('reassessment_payment_date')->nullable()->after('reassessment_payment_proof');
            $table->enum('reassessment_payment_status', ['pending', 'verified', 'rejected'])->nullable()->after('reassessment_payment_date');

            // Second Reassessment Payment tracking
            $table->string('second_reassessment_payment_proof')->nullable()->after('reassessment_payment_status');
            $table->timestamp('second_reassessment_payment_date')->nullable()->after('second_reassessment_payment_proof');
            $table->enum('second_reassessment_payment_status', ['pending', 'verified', 'rejected'])->nullable()->after('second_reassessment_payment_date');

            // Index for faster queries
            $table->index('reassessment_payment_status');
            $table->index('second_reassessment_payment_status');
            $table->index(['is_reassessment', 'reassessment_attempt']);
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'is_reassessment',
                'reassessment_attempt',
                'reassessment_fee',
                'reassessment_payment_proof',
                'reassessment_payment_date',
                'reassessment_payment_status',
                'second_reassessment_payment_proof',
                'second_reassessment_payment_date',
                'second_reassessment_payment_status',
            ]);
        });
    }

};
