<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('text');
            $table->boolean('type');
            $table->integer('owner_id')->unsigned();
            $table->index('owner_id');
            $table->foreign('owner_id')->references('id')->on('users');
            $table->integer('sponsorship_id')->unsigned();
            $table->index('sponsorship_id');
            $table->foreign('sponsorship_id')->references('id')->on('sponsorships');
            $table->enum('status', ['pending', 'completed']);
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
        Schema::drop('tasks');
    }
}
