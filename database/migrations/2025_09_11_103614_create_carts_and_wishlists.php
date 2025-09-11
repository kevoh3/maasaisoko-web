<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /* ---------------------------
         * carts
         * --------------------------- */
        if (!Schema::hasTable('carts')) {
            Schema::create('carts', function (Blueprint $t) {
                $t->bigIncrements('id');

                // Either a signed-in user OR a session (guest)
                $t->unsignedBigInteger('user_id')->nullable()->index();
                $t->string('session_id', 100)->nullable()->index();

                // Optional helpers for analysis
                $t->string('status', 20)->default('open')->index(); // open|converted|abandoned (free text)
                $t->char('currency', 3)->default('KES');

                // Optional analytics/audit fields
                $t->string('ip_address', 45)->nullable();
                $t->text('user_agent')->nullable();

                $t->timestamps();

                $t->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        /* ---------------------------
         * cart_items
         * --------------------------- */
        if (!Schema::hasTable('cart_items')) {
            Schema::create('cart_items', function (Blueprint $t) {
                $t->bigIncrements('id');

                $t->unsignedBigInteger('cart_id')->index();
                $t->unsignedBigInteger('product_id')->index();

                $t->unsignedInteger('quantity')->default(1);
                $t->decimal('unit_price', 12, 2); // copy from Product::sale_price at add time

                // Full snapshot of item & seller at time of add (name, thumbnail, unit, weight, seller block, etc.)
                $t->json('meta')->nullable();

                $t->timestamps();

                $t->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
                $t->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

                // Prevent duplicates of same product in a cart
                $t->unique(['cart_id', 'product_id']);
            });
        } else {
            // If the table already exists but 'meta' doesn't, add it
            if (!Schema::hasColumn('cart_items', 'meta')) {
                Schema::table('cart_items', function (Blueprint $t) {
                    $t->json('meta')->nullable()->after('unit_price');
                });
            }
        }

        /* ---------------------------
         * wishlists
         * --------------------------- */
        if (!Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $t) {
                $t->bigIncrements('id');

                $t->unsignedBigInteger('user_id')->nullable()->index();
                $t->string('session_id', 100)->nullable()->index();

                $t->timestamps();

                $t->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        /* ---------------------------
         * wishlist_items
         * --------------------------- */
        if (!Schema::hasTable('wishlist_items')) {
            Schema::create('wishlist_items', function (Blueprint $t) {
                $t->bigIncrements('id');

                $t->unsignedBigInteger('wishlist_id')->index();
                $t->unsignedBigInteger('product_id')->index();

                // Snapshot (same idea as cart_items.meta)
                $t->json('meta')->nullable();

                $t->timestamps();

                $t->foreign('wishlist_id')->references('id')->on('wishlists')->onDelete('cascade');
                $t->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

                $t->unique(['wishlist_id', 'product_id']);
            });
        } else {
            if (!Schema::hasColumn('wishlist_items', 'meta')) {
                Schema::table('wishlist_items', function (Blueprint $t) {
                    $t->json('meta')->nullable()->after('product_id');
                });
            }
        }
    }

    public function down(): void
    {
        // Drop children first due to FKs
        if (Schema::hasTable('wishlist_items')) {
            Schema::dropIfExists('wishlist_items');
        }
        if (Schema::hasTable('wishlists')) {
            Schema::dropIfExists('wishlists');
        }
        if (Schema::hasTable('cart_items')) {
            Schema::dropIfExists('cart_items');
        }
        if (Schema::hasTable('carts')) {
            Schema::dropIfExists('carts');
        }
    }
};
