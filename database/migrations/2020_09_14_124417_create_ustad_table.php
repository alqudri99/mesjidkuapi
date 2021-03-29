<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUstadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ustad', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama');
            $table->string('role');
            $table->unsignedBigInteger('ustad_event_id');
            $table->timestamps();

            $table->foreign('ustad_event_id')->references('id')->on('event');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ustad');
    }
}
