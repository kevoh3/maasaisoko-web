<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_onboarding_progress_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // progress
            if (!Schema::hasColumn('users', 'onboarding_step')) {
                $table->unsignedTinyInteger('onboarding_step')->default(1)->after('status_id');
            }

            // (optional) if your controller sets these:
            if (!Schema::hasColumn('users', 'kyc_status')) {
                $table->enum('kyc_status', ['not_submitted','pending','approved','rejected'])
                    ->default('not_submitted')->after('onboarding_step');
            }
            if (!Schema::hasColumn('users', 'kyc_submitted_at')) {
                $table->timestamp('kyc_submitted_at')->nullable()->after('kyc_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'kyc_submitted_at')) $table->dropColumn('kyc_submitted_at');
            if (Schema::hasColumn('users', 'kyc_status'))       $table->dropColumn('kyc_status');
            if (Schema::hasColumn('users', 'onboarding_step'))  $table->dropColumn('onboarding_step');
        });
    }
};
