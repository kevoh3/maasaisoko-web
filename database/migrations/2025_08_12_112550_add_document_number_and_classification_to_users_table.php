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
        Schema::table('users', function (Blueprint $table) {
            // Put after group_id to keep related fields together
            $table->enum('classification', ['individual', 'company'])
                ->default('individual')
                ->after('group_id');

            $table->string('document_number', 191)
                ->nullable()
                ->after('classification');

            // Start with a normal index (you can switch to UNIQUE later once data is clean)
            $table->index('document_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['document_number']);
            $table->dropColumn(['document_number', 'classification']);
        });
    }
};
