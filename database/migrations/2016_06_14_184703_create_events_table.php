<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('summary');
            $table->text('description');
            $table->string('image');
            $table->string('language', 5);
            $table->boolean('is_private');
            $table->boolean('is_outstanding');
            $table->string('country');
            $table->string('place_name');
            $table->string('place_id');
            $table->double('latitude', 10, 6);
            $table->double('longitude', 10, 6);
            $table->string('address');
            $table->timestamp('start');
            $table->timestamp('end');
            
            $table->integer('type_id')->unsigned();
            $table->index('type_id');
            $table->foreign('type_id')->references('id')->on('types');
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
        Schema::drop('events');
    }
}
