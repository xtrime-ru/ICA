<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('source_icon', function (Blueprint $table) {
            $table->unsignedBigInteger('source_id')->primary();

            $table->foreign('source_id')
                ->references('id')
                ->on('sources')
                ->onDelete('cascade')
            ;

        });

        DB::statement('ALTER TABLE source_icon ADD COLUMN icon MEDIUMBLOB');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('source_icon');
    }
};
