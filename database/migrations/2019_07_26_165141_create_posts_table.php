<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url', 255)
                  ->charset('utf8')
                  ->collation('utf8_unicode_ci')
                  ->unique()
            ;
            $table->string('title', 100)->nullable();
            $table->string('description', 1024)->nullable();

            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('bookmarks')->default(0);

            $table->foreign('id')
                  ->references('post_id')
                  ->on('source_post')
                  ->onDelete('cascade')
            ;
        });

        Schema::table('source_post', function(Blueprint $table) {
            $table->foreign('post_id')
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
        Schema::dropIfExists('posts');

        Schema::table('source_post', function(Blueprint $table) {
            $table->dropForeign('post_id');
        });
    }
}
