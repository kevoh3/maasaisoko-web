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
            // KYC
            $table->enum('kyc_status', ['not_submitted','pending','verified','rejected'])
                ->default('not_submitted')
                ->after('classification');
            $table->timestamp('kyc_submitted_at')->nullable()->after('kyc_status');
            $table->timestamp('kyc_verified_at')->nullable()->after('kyc_submitted_at');
            $table->timestamp('kyc_rejected_at')->nullable()->after('kyc_verified_at');
            $table->text('kyc_notes')->nullable()->after('kyc_rejected_at');

            // Registration fee
            $table->boolean('registration_fee_paid')->default(false)->after('kyc_notes');
            $table->decimal('registration_fee_amount', 12, 2)->nullable()->after('registration_fee_paid');
            $table->string('registration_fee_currency', 3)->default('KES')->after('registration_fee_amount');
            $table->timestamp('registration_fee_paid_at')->nullable()->after('registration_fee_currency');
            $table->string('registration_fee_txn_ref', 100)->nullable()->after('registration_fee_paid_at');
            $table->string('registration_fee_method', 50)->nullable()->after('registration_fee_txn_ref');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'kyc_status','kyc_submitted_at','kyc_verified_at','kyc_rejected_at','kyc_notes',
                'registration_fee_paid','registration_fee_amount','registration_fee_currency',
                'registration_fee_paid_at','registration_fee_txn_ref','registration_fee_method',
            ]);
        });
    }
};
