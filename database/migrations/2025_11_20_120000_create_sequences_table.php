<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('sequences')) {
            Schema::create('sequences', function (Blueprint $table) {
                $table->id();
                $table->string('scope');
                $table->integer('year');
                $table->unsignedBigInteger('next_number')->default(1);
                $table->timestamps();
                $table->unique(['scope','year']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequences');
    }
};
