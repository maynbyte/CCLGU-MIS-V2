<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('directories', function (Blueprint $table) {
            // allow null if user will type a custom barangay
            $table->unsignedBigInteger('barangay_id')->nullable()->change();
        });
        
        if (!Schema::hasColumn('directories', 'barangay_other')) {
            Schema::table('directories', function (Blueprint $table) {
                $table->string('barangay_other', 191)->nullable()->after('barangay_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('directories', 'barangay_other')) {
            Schema::table('directories', function (Blueprint $table) {
                $table->dropColumn('barangay_other');
            });
        }
    }
};
