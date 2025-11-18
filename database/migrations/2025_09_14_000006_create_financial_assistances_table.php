<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialAssistancesTable extends Migration
{
    public function up()
    {
        Schema::create('financial_assistances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('directory')->nullable();
            $table->string('family_composition')->nullable();
            $table->string('user')->nullable();
            $table->string('problem_presented')->nullable();
            $table->datetime('date_interviewed')->nullable();
            $table->string('assessment')->nullable();
            $table->string('recommendation')->nullable();
            $table->string('amount')->nullable();
            $table->string('scheduled_fa')->nullable();
            $table->string('status')->nullable();
            $table->string('date_claimed')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
