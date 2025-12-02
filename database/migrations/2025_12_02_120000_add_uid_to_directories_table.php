<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('directories', function (Blueprint $table) {
            // Add as nullable first to allow backfill before enforcing presence
            $table->string('uid', 16)->nullable()->unique()->after('id');
        });

        // Backfill existing rows
        $existing = DB::table('directories')->select('id')->whereNull('uid')->get();
        foreach ($existing as $row) {
            $uid = $this->generateUniqueUid();
            DB::table('directories')->where('id', $row->id)->update(['uid' => $uid]);
        }
        // (Optional) Make non-nullable later in separate migration if desired without DBAL.
    }

    public function down(): void
    {
        Schema::table('directories', function (Blueprint $table) {
            $table->dropUnique(['uid']);
            $table->dropColumn('uid');
        });
    }

    private function generateUniqueUid(): string
    {
        do {
            // Generate a 16-digit zero-padded numeric string
            $candidate = str_pad((string) random_int(0, 9999999999999999), 16, '0', STR_PAD_LEFT);
        } while (DB::table('directories')->where('uid', $candidate)->exists());
        return $candidate;
    }
};
