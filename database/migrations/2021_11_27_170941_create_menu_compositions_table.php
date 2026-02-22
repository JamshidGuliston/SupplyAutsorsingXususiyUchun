<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuCompositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_compositions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('title_menu_id');
            $table->integer('menu_meal_time_id');
            $table->integer('menu_food_id');
            $table->integer('product_name_id');
            $table->integer('age_range_id');
            $table->double('weight', 8, 5);
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
        Schema::dropIfExists('menu_compositions');
    }
}
