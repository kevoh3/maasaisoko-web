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
        Schema::create('geo_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            // e.g. "county", "constituency", "ward" (or "state", "province", etc.)
            $table->string('name');
            $table->string('slug')->index();
            // Depth within a country (1 = top most below country)
            $table->unsignedTinyInteger('position');
            $table->boolean('is_publish')->default(true);
            $table->timestamps();

            $table->unique(['country_id', 'position']);
            $table->unique(['country_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_levels');
    }
};
