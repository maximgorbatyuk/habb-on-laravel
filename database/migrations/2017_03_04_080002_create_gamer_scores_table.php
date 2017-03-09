<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamerScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gamer_scores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gamer_id')->comment("Аккаунт геймера, к которому привязана запись");
            $table->text('scores')->comment("Название дисциплины, к которой относится запись");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gamer_scores');
    }
}
