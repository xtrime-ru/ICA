<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('description', 750)->nullable();
            $table->string('image', 2048)->nullable();

            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('bookmarks')->default(0);

            $table->timestamp('created_at', 0)->nullable();

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
        Schema::table('source_post', function(Blueprint $table) {
            $table->dropForeign('source_post_post_id_foreign');
        });

        Schema::dropIfExists('posts');
    }
}
