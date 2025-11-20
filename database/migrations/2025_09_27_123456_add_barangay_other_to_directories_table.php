<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Schema::table('directories', function (Blueprint $table) {
    // allow null if user will type a custom barangay
    $table->unsignedBigInteger('barangay_id')->nullable()->change();
    $table->string('barangay_other', 191)->nullable()->after('barangay_id');
});
?>
