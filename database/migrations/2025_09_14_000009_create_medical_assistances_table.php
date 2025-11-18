<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalAssistancesTable extends Migration
{
    public function up()
    {
        Schema::create('medical_assistances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('medical_assistance')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
