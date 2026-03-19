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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Application data
            $table->string('title_of_assessment_applied_for');
            $table->string('photo')->nullable();
            $table->string('surname');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('middleinitial', 5)->nullable();
            $table->string('name_extension')->nullable(); // Jr., Sr., III, etc.

            // Address (store PSGC codes + display names for stability)
            $table->string('region_code');
            $table->string('region_name');
            $table->string('province_code');
            $table->string('province_name');
            $table->string('city_code');
            $table->string('city_name');
            $table->string('barangay_code');
            $table->string('barangay_name');
            $table->string('district')->nullable();
            $table->string('street_address')->nullable(); // number, street, purok combined
            $table->string('zip_code', 10)->nullable();

            // Parents
            $table->string('mothers_name')->nullable();
            $table->string('fathers_name')->nullable();

            // Personal
            $table->enum('sex', ['male', 'female', 'prefer_not_to_say'])->nullable();
            $table->string('civil_status')->nullable();
            $table->string('mobile', 32)->nullable();
            $table->string('email')->nullable();

            // Education and employment
            $table->string('highest_educational_attainment')->nullable();
            $table->string('employment_status')->nullable();

            // Birth
            $table->date('birthdate')->nullable();
            $table->string('birthplace')->nullable();
            $table->unsignedInteger('age')->nullable();

            // Review fields
            $table->string('status')->default('pending')->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('review_remarks')->nullable();

            $table->string('training_status')->default('enrolled'); // enrolled, completed, failed
            $table->date('training_completed_at')->nullable();
            $table->text('training_remarks')->nullable();

            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
