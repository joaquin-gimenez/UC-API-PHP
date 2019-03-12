<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city', function (Blueprint $table) {
            $table->bigIncrements('id');
            //$table->timestamps();
            $table->string('name', 50);
            $table->string('countryname', 50);
            $table->float('lat');
            $table->float('lng');
            $table->string('thumburl', 200);
            $table->string('description', 1000);
            $table->timestamp('createdate');
            $table->timestamp('lastupdated');
            $table->string('heroimage', 200);
            $table->string('budget', 30);
            $table->string('besttime', 50);
            $table->string('language', 20);
            $table->string('population', 15);
            $table->string('traveladvice', 200);
            $table->string('currency', 20);
            $table->integer('tour_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city');
    }
}
