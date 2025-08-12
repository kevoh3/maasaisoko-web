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
            // Add group_id column (nullable because not all users belong to a group)
            $table->unsignedBigInteger('group_id')->nullable()->after('role_id');

            // Optional: Add foreign key constraint if you have the groups table
            $table->foreign('group_id')
                ->references('id')->on('groups')
                ->nullOnDelete(); // sets group_id to NULL if group is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }
};
