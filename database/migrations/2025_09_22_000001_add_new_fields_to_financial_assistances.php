<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add columns only if missing (idempotent)
        if (!Schema::hasColumn('financial_assistances', 'type_of_assistance')) {
            Schema::table('financial_assistances', function (Blueprint $table) {
                $table->string('type_of_assistance')->nullable()->after('directory_id');
            });
        }

        if (!Schema::hasColumn('financial_assistances', 'patient_name')) {
            Schema::table('financial_assistances', function (Blueprint $table) {
                $table->string('patient_name')->nullable();
            });
        }

        if (!Schema::hasColumn('financial_assistances', 'claimant_is_patient')) {
            Schema::table('financial_assistances', function (Blueprint $table) {
                $table->boolean('claimant_is_patient')->default(false);
            });
        }

        if (!Schema::hasColumn('financial_assistances', 'requirement_checklist')) {
            Schema::table('financial_assistances', function (Blueprint $table) {
                $table->json('requirement_checklist')->nullable();
            });
        }

        // 'status' already exists for you, so we only add it if missing
        if (!Schema::hasColumn('financial_assistances', 'status')) {
            Schema::table('financial_assistances', function (Blueprint $table) {
                $table->string('status')->nullable();
            });
        }

        if (!Schema::hasColumn('financial_assistances', 'problem_presented_value')) {
            Schema::table('financial_assistances', function (Blueprint $table) {
                $table->json('problem_presented_value')->nullable();
            });
        }

        if (!Schema::hasColumn('financial_assistances', 'social_welfare_name')) {
            Schema::table('financial_assistances', function (Blueprint $table) {
                $table->string('social_welfare_name')->nullable();
            });
        }

        if (!Schema::hasColumn('financial_assistances', 'social_welfare_desig')) {
            Schema::table('financial_assistances', function (Blueprint $table) {
                $table->string('social_welfare_desig')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Drop only if they exist
        foreach ([
            'type_of_assistance',
            'patient_name',
            'claimant_is_patient',
            'requirement_checklist',
            // Don't drop 'status' here if other code depends on it and it pre-existed.
            // If you DO want to drop it, uncomment the next line:
            // 'status',
            'problem_presented_value',
            'social_welfare_name',
            'social_welfare_desig',
        ] as $col) {
            if (Schema::hasColumn('financial_assistances', $col)) {
                Schema::table('financial_assistances', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }
    }
};
