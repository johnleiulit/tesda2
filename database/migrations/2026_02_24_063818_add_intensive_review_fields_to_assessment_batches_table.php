<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_batches', function (Blueprint $table) {
            $table->date('intensive_review_day1')->nullable()->after('assessment_date');
            $table->time('intensive_review_day1_start')->nullable()->after('intensive_review_day1');
            $table->time('intensive_review_day1_end')->nullable()->after('intensive_review_day1_start');
            
            $table->date('intensive_review_day2')->nullable()->after('intensive_review_day1_end');
            $table->time('intensive_review_day2_start')->nullable()->after('intensive_review_day2');
            $table->time('intensive_review_day2_end')->nullable()->after('intensive_review_day2_start');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_batches', function (Blueprint $table) {
            $table->dropColumn([
                'intensive_review_day1',
                'intensive_review_day1_start',
                'intensive_review_day1_end',
                'intensive_review_day2',
                'intensive_review_day2_start',
                'intensive_review_day2_end',
            ]);
        });
    }
};
