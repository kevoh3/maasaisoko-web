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
        Schema::create('groups', function (Blueprint $table) {
            // Basic Info
            $table->string('name');
            $table->string('registration_number')->nullable();
            $table->string('type')->default('organisation');
            $table->string('industry')->nullable();

            // Contact Info
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('county')->nullable();
            $table->string('sub_county')->nullable();

            // KYC Fields
            $table->string('kra_pin')->nullable();
            $table->string('business_permit_number')->nullable();
            $table->string('certificate_of_incorporation')->nullable(); // file path
            $table->string('tax_compliance_certificate')->nullable(); // file path
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_branch')->nullable();

            // Digital Presence
            $table->string('website')->nullable();
            $table->string('social_media')->nullable();

            // Status & Approval
            $table->enum('status', ['pending', 'active', 'suspended', 'inactive'])->default('pending');

            // Verification Workflow
            $table->unsignedBigInteger('verified_by')->nullable(); // admin/staff id
            $table->timestamp('verified_at')->nullable();
            $table->enum('verified_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('verified_notes')->nullable(); // remarks/reasons

            // Approval Workflow
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
