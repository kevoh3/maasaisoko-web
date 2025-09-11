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
        Schema::table('products', function (Blueprint $t) {
            $t->foreignId('geo_unit_id')
                ->nullable()
                ->after('brand_id') // pick the right column for your schema
                ->constrained('geo_units')
                ->nullOnDelete();
            $t->index('geo_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $t) {
            $t->dropConstrainedForeignId('geo_unit_id');
        });
    }
};
