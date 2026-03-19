<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->string('field_name'); // e.g., 'email', 'mobile', 'photo'
            $table->string('field_label'); // e.g., 'Email Address', 'Mobile Number'
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamp('changed_at');
            $table->timestamps();
            
            $table->index(['application_id', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_changes');
    }
};
