<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToDirectoriesTable extends Migration
{
    public function up()
    {
        Schema::table('directories', function (Blueprint $table) {
            $table->unsignedBigInteger('barangay_id')->nullable();
            $table->foreign('barangay_id', 'barangay_fk_10715536')->references('id')->on('barangays');
        });
    }
}
