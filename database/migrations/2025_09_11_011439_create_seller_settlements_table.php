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
        Schema::create('seller_settlements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->string('bank_name')->nullable();
            $t->string('bank_branch')->nullable();
            $t->string('account_name')->nullable();
            $t->string('account_number')->nullable();
            $t->string('swift_code')->nullable();
            $t->string('mobile_money')->nullable(); // +2547XXXXXXXX
            $t->timestamps();
            $t->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_settlements');
    }
};
