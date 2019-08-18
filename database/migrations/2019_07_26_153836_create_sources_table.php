<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sources', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->unsignedBigInteger('social_id')->nullable()->index();

            $table->string('url', 2048);
            $table->string('name', 255);
            $table->enum('access', ['public', 'personal'])->default('personal');
            $table->boolean('active')->default(true);
            $table->enum('age_limit', [0,16,18])->default(0);
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('subscribers')->default(0);
            $table->unsignedInteger('views')->default(0);

            $table->string('parser_url', 2048);
            $table
                ->enum(
                'parser_type',
                    ['rss', 'html', 'vk', 'fb', 'tg', 'instagram', 'youtube', 'twitter', 'web']
                )
                ->index()
            ;
            $table->json('parser_rules')->nullable();
            $table->timestamp('next_parse_at')->nullable();
            $table->unsignedInteger('parse_interval')
                ->default(360)
                ->nullable()
                ->comment('minutes')
            ;
            $table->timestamp('parsed_at')->nullable();
            $table->timestamps();

            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->onDelete('set null')
            ;
            $table->foreign('social_id')
                ->references('id')->on('socials')
                ->onDelete('cascade')
            ;
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('set null')
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
        Schema::dropIfExists('sources');
    }
}
