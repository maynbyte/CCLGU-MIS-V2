<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNgosTable extends Migration
{
    public function up()
    {
        Schema::create('ngos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('description')->nullable();
            $table->string('total_members')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
