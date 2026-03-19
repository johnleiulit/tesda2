// database/migrations/2026_03_12_XXXXXX_create_employment_records_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->date('date_employed');
            $table->string('occupation');
            $table->string('employer_name');
            $table->text('employer_address');
            $table->string('employer_classification'); // e.g., Private, Government, NGO
            $table->decimal('monthly_income', 10, 2);
            $table->timestamp('viewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_records');
    }
};
