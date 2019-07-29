<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSourcePostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('source_post', function (Blueprint $table) {
            $table->unsignedBigInteger('source_id');
            $table->unsignedBigInteger('post_id')->index();

            $table->primary(['source_id', 'post_id']);

            $table->foreign('source_id')
                ->references('id')
                ->on('sources')
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
        Schema::dropIfExists('source_post');
    }
}
