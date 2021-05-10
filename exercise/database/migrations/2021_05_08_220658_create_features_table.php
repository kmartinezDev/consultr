<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('superhero_id')->unsigned();
            $table->string('full_name', 50)->nullable();
            $table->integer('strength');
            $table->integer('speed');
            $table->integer('durability');
            $table->integer('power');
            $table->integer('combat');
            $table->string('height_ft')->nullable();
            $table->string('height_cm')->nullable();
            $table->string('weight_lb')->nullable();
            $table->string('weight_kg')->nullable();
            $table->bigInteger('race_id')->unsigned()->nullable();
            $table->bigInteger('eye_color_id')->unsigned()->nullable();
            $table->bigInteger('hair_color_id')->unsigned()->nullable();
            $table->bigInteger('publisher_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('superhero_id')->references('id')->on('superheroes')->onDelete('cascade');
            $table->foreign('race_id')->references('id')->on('races')->onDelete('cascade');
            $table->foreign('eye_color_id')->references('id')->on('eye_colors')->onDelete('cascade');
            $table->foreign('hair_color_id')->references('id')->on('hair_colors')->onDelete('cascade');
            $table->foreign('publisher_id')->references('id')->on('publishers')->onDelete('cascade'); 

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('features');
    }
}
