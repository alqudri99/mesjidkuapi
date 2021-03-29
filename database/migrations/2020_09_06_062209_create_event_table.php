<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('event_name');
            $table->string('deskripsi');
            $table->string('jadwal');
            $table->unsignedBigInteger('jenis_event_id');
            $table->string('photo_event');
            $table->unsignedBigInteger('id_mesjid');
            $table->timestamps();
            $table->foreign('jenis_event_id')->references('id')->on('jenis_event');
            $table->foreign('id_mesjid')->references('id')->on('mesjid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event');
    }
}
