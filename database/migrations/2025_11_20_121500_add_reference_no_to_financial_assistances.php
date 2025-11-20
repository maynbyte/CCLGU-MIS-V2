<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('financial_assistances') && !Schema::hasColumn('financial_assistances','reference_no')) {
            Schema::table('financial_assistances', function (Blueprint $table) {
                $table->string('reference_no',50)->nullable()->unique()->after('amount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('financial_assistances') && Schema::hasColumn('financial_assistances','reference_no')) {
            Schema::table('financial_assistances', function (Blueprint $table) {
                $table->dropUnique(['reference_no']);
                $table->dropColumn('reference_no');
            });
        }
    }
};
