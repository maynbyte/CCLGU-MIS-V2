<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectoryNgoPivotTable extends Migration
{
    public function up()
    {
        Schema::create('directory_ngo', function (Blueprint $table) {
            $table->unsignedBigInteger('directory_id');
            $table->foreign('directory_id', 'directory_id_fk_10715537')->references('id')->on('directories')->onDelete('cascade');
            $table->unsignedBigInteger('ngo_id');
            $table->foreign('ngo_id', 'ngo_id_fk_10715537')->references('id')->on('ngos')->onDelete('cascade');
        });
    }
}
