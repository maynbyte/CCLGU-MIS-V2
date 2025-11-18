<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectoriesTable extends Migration
{
    public function up()
    {
        Schema::create('directories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('suffix')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_no')->nullable();
            $table->date('birthday')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->string('gender')->nullable();
            $table->string('highest_edu')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('religion')->nullable();
            $table->string('street_no')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('comelec_status')->nullable();
            $table->string('life_status')->nullable();
            $table->longText('description')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
