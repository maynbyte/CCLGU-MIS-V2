<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBurialAssistancesTable extends Migration
{
    public function up()
    {
        Schema::create('burial_assistances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('burial_assitance')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
