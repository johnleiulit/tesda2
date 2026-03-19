<?php
// database/migrations/XXXX_XX_XX_XXXXXX_add_application_type_to_applications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('application_type', ['TWSP', 'Assessment Only'])
                  ->nullable()
                  ->after('title_of_assessment_applied_for')
                  ->comment('Type of application: TWSP (training) or Assessment Only');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('application_type');
        });
    }
};
