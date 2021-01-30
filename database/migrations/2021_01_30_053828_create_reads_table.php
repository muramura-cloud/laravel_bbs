<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReadsTable extends Migration
{
    public function up()
    {
        Schema::create('reads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('post_id');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('posts');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reads');
    }
}
