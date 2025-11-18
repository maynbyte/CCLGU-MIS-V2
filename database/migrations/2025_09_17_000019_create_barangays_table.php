<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangaysTable extends Migration
{
    public function up()
    {
        Schema::create('barangays', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('barangay_name');
            $table->string('barangay')->nullable();
            $table->string('barangay_chairman')->nullable();
            $table->string('sk_chairman')->nullable();
            $table->integer('total_no_of_voters')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
