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
            $table->string('contact_person_name')->nullable()->after('group_id');
            $table->enum('contact_person_designation', ['proprietor','director','manager','agent'])->nullable()->after('contact_person_name');
            $table->string('contact_person_phone', 30)->nullable()->after('contact_person_designation');
            $table->string('contact_person_email')->nullable()->after('contact_person_phone');

            $table->string('address_building')->nullable()->after('address');
            $table->string('address_street')->nullable()->after('address_building');

            $table->string('postal_box', 191)->nullable()->after('state');
            $table->string('postal_code', 191)->nullable()->after('postal_box');

            $table->string('geo_path')->nullable()->after('geo_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('contact_person_name')->nullable()->after('group_id');
            $table->enum('contact_person_designation', ['proprietor','director','manager','agent'])->nullable()->after('contact_person_name');
            $table->string('contact_person_phone', 30)->nullable()->after('contact_person_designation');
            $table->string('contact_person_email')->nullable()->after('contact_person_phone');

            $table->string('address_building')->nullable()->after('address');
            $table->string('address_street')->nullable()->after('address_building');

            $table->string('postal_box', 191)->nullable()->after('state');
            $table->string('postal_code', 191)->nullable()->after('postal_box');

            $table->string('geo_path')->nullable()->after('geo_unit_id');
        });
    }
};
