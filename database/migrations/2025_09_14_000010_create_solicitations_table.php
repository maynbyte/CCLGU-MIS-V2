<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitationsTable extends Migration
{
    public function up()
    {
        Schema::create('solicitations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('solicitation')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
