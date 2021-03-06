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
            $table->string('game_name')->comment("Название дисциплины, к которой относится запись");
            $table->integer('total_value')->default(0)->comment("Общее значение набранных очков");
            $table->integer('total_change')->default(0)->comment("Показатель последнего изменения очков. Может быть больше или меньше нуля");
            $table->integer('month_value')->default(0)->comment("Показатель очков на начало месяца. Для того, чтобы считать, какой прирост за месяц произошел");
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
