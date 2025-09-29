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
        Schema::table('seller_stores', function (Blueprint $table) {
            $table->string('store_city', 120)->nullable()->after('store_description');
            $table->string('store_county', 120)->nullable()->after('store_city');
            $table->string('store_sub_county', 120)->nullable()->after('store_county');
            $table->string('store_ward', 120)->nullable()->after('store_sub_county');
            $table->string('store_coords', 60)->nullable()->after('store_ward');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_stores', function (Blueprint $table) {
            $table->dropColumn(['store_city','store_county','store_sub_county','store_ward','store_coords']);
        });
    }
};
