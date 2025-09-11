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
        Schema::create('seller_documents', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->string('document_number')->nullable();              // ID/Passport/Reg no.
            $t->string('kra_pin')->nullable();

            // Stored file paths (Storage::putFile*)
            $t->string('document_file_path')->nullable();           // ID/Passport copy
            $t->string('business_license_file_path')->nullable();   // Cert of Inc / License
            $t->string('brand_auth_file_path')->nullable();         // Optional brand auth

            $t->timestamps();
            $t->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_documents');
    }
};
