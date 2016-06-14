<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AssocicateEventsWithUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {

            // Create the author_id column as an unsigned integer
            $table->integer('user_id')->after('id')->unsigned();

            // Create a basic index for the author_id column
            $table->index('user_id');

            // Create a foreign key constraint and cascade on delete.
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            // Drop the foreign key first 
            $table->dropForeign('events_user_id_foreign');
            // Now drop the basic index
            $table->dropIndex('events_user_id_index');
            // Lastly, now it's safe to drop the column
            $table->dropColumn('user_id');
        });
    }
}
