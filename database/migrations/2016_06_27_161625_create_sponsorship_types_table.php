<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSponsorshipTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponsorship_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kind');
            $table->float('cost');
            $table->integer('total_slots');
            $table->integer('used_slots')->default(0);
            $table->integer('event_id')->unsigned();
            $table->index('event_id');
            $table->foreign('event_id')->references('id')->on('events');
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
        Schema::drop('sponsorship_types');
    }
}
