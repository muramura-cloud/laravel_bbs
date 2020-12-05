<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            // これは符号なしの整数値を条件として設定している。これをしないエラーになるらしい。
            $table->unsignedInteger('post_id');
            $table->text('body');
            $table->timestamps();

            // foreignメソッドでpost_idを外部キー(従テーブルから主テーブルを参照するためのキーのこと)として設定する。
            // referencesメソッドで従テーブルと紐づいている主テーブルのidを設定する。
            // onメソッドで主テーブルを指定する。
            $table->foreign('post_id')->references('id')->on('posts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
