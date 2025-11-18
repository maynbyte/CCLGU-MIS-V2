<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectorySectorGroupPivotTable extends Migration
{
    public function up()
    {
        Schema::create('directory_sector_group', function (Blueprint $table) {
            $table->unsignedBigInteger('directory_id');
            $table->foreign('directory_id', 'directory_id_fk_10715538')->references('id')->on('directories')->onDelete('cascade');
            $table->unsignedBigInteger('sector_group_id');
            $table->foreign('sector_group_id', 'sector_group_id_fk_10715538')->references('id')->on('sector_groups')->onDelete('cascade');
        });
    }
}
