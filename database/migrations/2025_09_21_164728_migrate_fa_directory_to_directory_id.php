<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Add directory_id (nullable first), no DBAL
        Schema::table('financial_assistances', function (Blueprint $table) {
            if (!Schema::hasColumn('financial_assistances', 'directory_id')) {
                $table->unsignedBigInteger('directory_id')->nullable()->after('id');
            }
        });

        // 2) Copy only valid numeric values that actually exist in directories
        DB::statement("
            UPDATE financial_assistances fa
            JOIN directories d ON d.id = CAST(fa.directory AS UNSIGNED)
            SET fa.directory_id = d.id
            WHERE fa.directory IS NOT NULL
              AND fa.directory <> ''
              AND fa.directory REGEXP '^[0-9]+$'
        ");

        // 2b) Safety: wipe accidental zeros
        DB::statement("UPDATE financial_assistances SET directory_id = NULL WHERE directory_id = 0");

        // 3) Add FK if it doesn't exist (MySQL)
        $db = DB::getDatabaseName();
        $fkExists = DB::table('information_schema.REFERENTIAL_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $db)
            ->where('TABLE_NAME', 'financial_assistances')
            ->where('CONSTRAINT_NAME', 'financial_assistances_directory_id_foreign')
            ->exists();

        if (!$fkExists) {
            DB::statement("
                ALTER TABLE `financial_assistances`
                ADD CONSTRAINT `financial_assistances_directory_id_foreign`
                FOREIGN KEY (`directory_id`) REFERENCES `directories`(`id`)
                ON DELETE CASCADE
            ");
        }

        // 4) Only make NOT NULL if there are no NULLs left
        $nulls = DB::table('financial_assistances')->whereNull('directory_id')->count();
        if ($nulls === 0) {
            DB::statement("
                ALTER TABLE `financial_assistances`
                MODIFY `directory_id` BIGINT UNSIGNED NOT NULL
            ");
        }

        // 5) Drop old 'directory' column if it exists
        if (Schema::hasColumn('financial_assistances', 'directory')) {
            Schema::table('financial_assistances', function (Blueprint $table) {
                $table->dropColumn('directory');
            });
        }
    }

    public function down(): void
    {
        // Recreate old column
        Schema::table('financial_assistances', function (Blueprint $table) {
            if (!Schema::hasColumn('financial_assistances', 'directory')) {
                $table->unsignedBigInteger('directory')->nullable()->after('id');
            }
        });

        // Copy values back
        DB::statement("UPDATE financial_assistances SET directory = directory_id");

        // Drop FK if present
        $db = DB::getDatabaseName();
        $fkExists = DB::table('information_schema.REFERENTIAL_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $db)
            ->where('TABLE_NAME', 'financial_assistances')
            ->where('CONSTRAINT_NAME', 'financial_assistances_directory_id_foreign')
            ->exists();

        if ($fkExists) {
            DB::statement("
                ALTER TABLE `financial_assistances`
                DROP FOREIGN KEY `financial_assistances_directory_id_foreign`
            ");
        }

        // Drop new column
        if (Schema::hasColumn('financial_assistances', 'directory_id')) {
            Schema::table('financial_assistances', function (Blueprint $table) {
                $table->dropColumn('directory_id');
            });
        }
    }
};
