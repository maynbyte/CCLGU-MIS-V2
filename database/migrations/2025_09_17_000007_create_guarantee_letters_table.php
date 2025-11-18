<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuaranteeLettersTable extends Migration
{
    public function up()
    {
        Schema::create('guarantee_letters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('directory')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
