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
        Schema::create('geo_units', function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnDelete();

            $table->foreignId('level_id')
                ->constrained('geo_levels')
                ->cascadeOnDelete();

            // self–reference by primary key (id), NOT by (country_id,level_id,name)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('geo_units')
                ->cascadeOnDelete();

            // optional external code (e.g. county_code)
            $table->string('code')->nullable();

            $table->string('name');
            $table->string('slug');
            $table->boolean('is_publish')->default(true);
            $table->timestamps();

            // helpful indexes
            $table->index(['country_id','level_id','parent_id']);
            $table->index('parent_id');
            $table->index('code');

            // ✅ scoped uniques (allow same name/slug under different parents)
            $table->unique(
                ['country_id','level_id','parent_id','name'],
                'geo_units_country_level_parent_name_unique'
            );
            $table->unique(
                ['country_id','level_id','parent_id','slug'],
                'geo_units_country_level_parent_slug_unique'
            );

            // (optional) if codes should be unique within a level in a country:
            // $table->unique(['country_id','level_id','code'], 'geo_units_country_level_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_units');
    }
};
