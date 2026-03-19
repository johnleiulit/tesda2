<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Admin who viewed
            $table->timestamp('viewed_at');
            $table->string('view_type')->default('detail'); // 'detail', 'list', 'review', etc.
            
            // Indexes for performance
            $table->index(['application_id', 'user_id']);
            $table->index('viewed_at');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_views');
    }
};
