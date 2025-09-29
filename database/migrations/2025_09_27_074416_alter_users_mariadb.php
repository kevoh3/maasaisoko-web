<?php
// database/migrations/2025_09_27_120000_alter_users_mariadb.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Expand enum to include 'partnership'
        DB::statement("
            ALTER TABLE `users`
            MODIFY `classification` ENUM('individual','partnership','company')
            NOT NULL DEFAULT 'individual'
        ");

        // 2) Add unique index on shop_url if missing
        if (! $this->indexExists('users', 'users_shop_url_unique')) {
            DB::statement("ALTER TABLE `users` ADD UNIQUE KEY `users_shop_url_unique` (`shop_url`)");
        }

        // 3) Optional helper indexes (add only if missing)
        if (! $this->indexExists('users', 'users_status_id_index')) {
            DB::statement("ALTER TABLE `users` ADD INDEX `users_status_id_index` (`status_id`)");
        }
        if (! $this->indexExists('users', 'users_kyc_status_index')) {
            DB::statement("ALTER TABLE `users` ADD INDEX `users_kyc_status_index` (`kyc_status`)");
        }
        if (! $this->indexExists('users', 'users_group_id_index')) {
            DB::statement("ALTER TABLE `users` ADD INDEX `users_group_id_index` (`group_id`)");
        }
    }

    public function down(): void
    {
        // revert enum (drops partnership)
        DB::statement("
            ALTER TABLE `users`
            MODIFY `classification` ENUM('individual','company')
            NOT NULL DEFAULT 'individual'
        ");

        if ($this->indexExists('users', 'users_shop_url_unique')) {
            DB::statement("ALTER TABLE `users` DROP INDEX `users_shop_url_unique`");
        }
        if ($this->indexExists('users', 'users_status_id_index')) {
            DB::statement("ALTER TABLE `users` DROP INDEX `users_status_id_index`");
        }
        if ($this->indexExists('users', 'users_kyc_status_index')) {
            DB::statement("ALTER TABLE `users` DROP INDEX `users_kyc_status_index`");
        }
        if ($this->indexExists('users', 'users_group_id_index')) {
            DB::statement("ALTER TABLE `users` DROP INDEX `users_group_id_index`");
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $dbName = DB::getDatabaseName();
        $count = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $dbName)
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->count();
        return $count > 0;
    }
};
