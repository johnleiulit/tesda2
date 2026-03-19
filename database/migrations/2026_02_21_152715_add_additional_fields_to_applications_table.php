<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Page 3 fields
            $table->string('nationality')->nullable()->after('age');
            
            // Employment before training
            $table->string('employment_before_training_status')->nullable(); // Wage-employed, Self-employed, Unemployed, Underemployed
            $table->string('employment_before_training_type')->nullable(); // Regular, Casual, Job Order, etc.
            
            $table->string('birthplace_city')->nullable();
            $table->string('birthplace_province')->nullable();
            $table->string('birthplace_region')->nullable();
            
            // Page 4 fields
            // Educational attainment before training
            $table->string('educational_attainment_before_training')->nullable();
            
            // Parent/Guardian
            $table->string('parent_guardian_name')->nullable();
            $table->string('parent_guardian_street')->nullable();
            $table->string('parent_guardian_barangay')->nullable();
            $table->string('parent_guardian_district')->nullable();
            $table->string('parent_guardian_city')->nullable();
            $table->string('parent_guardian_province')->nullable();
            $table->string('parent_guardian_region')->nullable();
            
            // Learner Classification (can store as JSON array for multiple selections)
            $table->json('learner_classification')->nullable();
            
            // Scholarship
            $table->string('scholarship_type')->nullable(); // TWSP, PESFA, STEP, others
            
            // Privacy consent
            $table->boolean('privacy_consent')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'nationality',
                'employment_before_training_status',
                'employment_before_training_type',
                'birthplace_city',
                'birthplace_province',
                'birthplace_region',
                'educational_attainment_before_training',
                'parent_guardian_name',
                'parent_guardian_street',
                'parent_guardian_barangay',
                'parent_guardian_district',
                'parent_guardian_city',
                'parent_guardian_province',
                'parent_guardian_region',
                'learner_classification',
                'scholarship_type',
                'privacy_consent',
            ]);
        });
    }
};
