<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamilycompositionsTable extends Migration
{
    public function up()
    {
        Schema::create('familycompositions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('directory_id')->constrained('directories')->cascadeOnDelete();
            $table->string('family_name')->nullable();
            $table->date('family_birthday')->nullable();
            $table->string('family_relationship')->nullable();
            $table->string('family_civil_status')->nullable();
            $table->string('family_highest_edu')->nullable();
            $table->string('occupation')->nullable();
            $table->string('remarks')->nullable();
            $table->string('others')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
