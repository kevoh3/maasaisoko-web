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
        Schema::create('seller_stores', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // You already use users.shop_name/shop_url elsewhere.
            // Keep only meta here to avoid collisions.
            $t->unsignedBigInteger('store_category_id')->nullable();
            $t->foreign('store_category_id')->references('id')->on('pro_categories')->nullOnDelete();

            $t->string('store_logo_path')->nullable();
            $t->string('store_banner_path')->nullable();
            $t->text('store_description')->nullable();
            $t->json('shipping_methods')->nullable(); // ["local_pickup","within_county","nationwide"]

            $t->timestamps();
            $t->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_stores');
    }
};
