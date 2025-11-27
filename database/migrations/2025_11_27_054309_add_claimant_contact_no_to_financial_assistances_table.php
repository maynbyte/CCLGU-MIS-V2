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
        Schema::table('financial_assistances', function (Blueprint $table) {
            $table->string('claimant_contact_no')->nullable()->after('claimant_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_assistances', function (Blueprint $table) {
            $table->dropColumn('claimant_contact_no');
        });
    }
};
