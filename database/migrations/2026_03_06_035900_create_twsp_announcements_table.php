<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twsp_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('program_name')->default('Bookkeeping NC III');
            $table->integer('total_slots');
            $table->integer('filled_slots')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twsp_announcements');
    }
};
