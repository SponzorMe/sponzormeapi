<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->enum('type', ['organizer', 'sponsor', 'administrator']);
            $table->enum('gender', ['male', 'female']);

            $table->date('birthday')->nullable();
            $table->string('language')->nullable();
            $table->string('image')->nullable();
            $table->string('password')->nullable();
            $table->boolean('is_active')->default(0);
            $table->dateTime('activated_at')->nullable();
            $table->boolean('is_customized')->default(0);
            $table->boolean('is_demo_viewed')->default(0);
            $table->string('activation_code')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->string('description')->nullable();
            $table->string('eventbrite_key')->nullable();
            $table->string('meetup_key')->nullable();
            $table->string('ionic_id')->nullable();
            $table->rememberToken();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            //Social Networks Information
            $table->string('facebook_id')->nullable();
            $table->string('google_id')->nullable();
            //Location
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            //Sponsor Information
            $table->string('company_logo')->nullable();
            $table->longText('company_pitch')->nullable();
            $table->longText('newsletter')->nullable();
            //Organizer Information
            $table->integer('community_size')->default(0);
            
            
            

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
        Schema::drop('users');
    }
}
