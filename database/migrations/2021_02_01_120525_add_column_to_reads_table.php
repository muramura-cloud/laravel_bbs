<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToReadsTable extends Migration
{
    public function up()
    {
        Schema::table('reads', function (Blueprint $table) {
            $table->unsignedBigInteger('comment_id');
        });
    }

    public function down()
    {
        Schema::table('reads', function (Blueprint $table) {
            //
        });
    }
}
