<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_post', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('post_id')->index();

            $table->boolean('viewed')->default(false);
            $table->boolean('liked')->default(false);
            $table->boolean('bookmarked')->default(false);

            $table->primary(['user_id', 'post_id']);

            $table->foreign('user_id' , 'users')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
            ;

            $table->foreign('post_id', 'posts')
                ->references('id')
                ->on('posts')
                ->onDelete('cascade')
            ;

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_post');
    }
}
