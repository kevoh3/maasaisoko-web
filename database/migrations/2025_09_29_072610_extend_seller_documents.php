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
        Schema::table('seller_documents', function (Blueprint $table) {
            $table->string('kra_cert_file_path', 191)->nullable()->after('kra_pin');
            $table->json('additional_id_files')->nullable()->after('brand_auth_file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_documents', function (Blueprint $table) {
            $table->dropColumn(['kra_cert_file_path','additional_id_files']);
        });
    }
};
