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
            $table->string('qr_token')->nullable()->unique()->index()->after('reference_no');
            $table->string('payout_location')->nullable()->after('scheduled_fa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_assistances', function (Blueprint $table) {
            $table->dropColumn(['qr_token', 'payout_location']);
        });
    }
};
