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
        Schema::table('menu_childs', function (Blueprint $table) {
            $table->string('thumbnail')->nullable()->after('item_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_childs', function (Blueprint $table) {
            $table->dropColumn('thumbnail');
        });
    }
};
