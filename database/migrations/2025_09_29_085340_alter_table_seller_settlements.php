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
        Schema::table('seller_settlements', function (Blueprint $table) {
            // Add after related fields for readability
            if (!Schema::hasColumn('seller_settlements', 'account_type')) {
                $table->string('account_type', 20)->nullable()->after('account_number'); // e.g. current/savings
            }

            if (!Schema::hasColumn('seller_settlements', 'mobile_money_paybill')) {
                $table->string('mobile_money_paybill', 191)->nullable()->after('mobile_money');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_settlements', function (Blueprint $table) {
            if (Schema::hasColumn('seller_settlements', 'mobile_money_paybill')) {
                $table->dropColumn('mobile_money_paybill');
            }
            if (Schema::hasColumn('seller_settlements', 'account_type')) {
                $table->dropColumn('account_type');
            }
        });
    }
};
