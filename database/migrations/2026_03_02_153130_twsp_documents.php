<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twsp_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->string('psa_birth_certificate')->nullable();
            $table->string('psa_marriage_contract')->nullable();
            $table->string('high_school_document')->nullable();
            $table->json('id_pictures_1x1')->nullable(); // 4 pcs
            $table->json('id_pictures_passport')->nullable(); // 4 pcs
            $table->json('government_school_id')->nullable(); // 2 pcs
            $table->string('certificate_of_indigency')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twsp_documents');
    }
};
