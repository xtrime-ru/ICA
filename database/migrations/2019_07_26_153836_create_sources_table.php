<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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

            $table->string('name', 255);
            $table->enum('type', ['public', 'personal'])->default('personal');
            $table->boolean('active')->default(true);
            $table->enum('age_limit', [0,16,18])->default(0);
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('subscribers')->default(0);
            $table->unsignedInteger('views')->default(0);

            $table->longText('parser_rules')->nullable();
            $table->timestamp('next_parse_at')->nullable();
            $table->string('parse_interval', 20)->default('+6 hours')->nullable();
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
