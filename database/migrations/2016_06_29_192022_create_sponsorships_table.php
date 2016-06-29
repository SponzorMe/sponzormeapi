<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSponsorshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponsorships', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cause');
            $table->integer('sponsor_id')->unsigned();
            $table->index('sponsor_id');
            $table->foreign('sponsor_id')->references('id')->on('users');
            $table->integer('sponsorship_type_id')->unsigned();
            $table->index('sponsorship_type_id');
            $table->foreign('sponsorship_type_id')->references('id')->on('sponsorship_types');
            $table->enum('status', ['pending', 'rejected', 'accepted', 'paid', 'in process']);
            $table->boolean('is_rated_by_organizer')->default(0);
            $table->boolean('is_rated_by_sponsor')->default(0);
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
        Schema::drop('sponsorships');
    }
}
