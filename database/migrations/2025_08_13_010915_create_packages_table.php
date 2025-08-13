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
        Schema::create('packages', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('name'); // Standard, Bronze, etc.
            $table->integer('items'); // Number of items in package
            $table->decimal('base_monthly_price', 10, 2); // Price without discount

            // Discounts in percentage (0 = no discount)
            $table->decimal('quarterly_discount', 5, 2)->default(0);
            $table->decimal('bi_annual_discount', 5, 2)->default(0);
            $table->decimal('annual_discount', 5, 2)->default(0);

            // Final calculated totals (can be edited later manually)
            $table->decimal('quarterly_total', 10, 2)->nullable();
            $table->decimal('quarterly_monthly_rate', 10, 2)->nullable();
            $table->decimal('bi_annual_total', 10, 2)->nullable();
            $table->decimal('bi_annual_monthly_rate', 10, 2)->nullable();
            $table->decimal('annual_total', 10, 2)->nullable();
            $table->decimal('annual_monthly_rate', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
