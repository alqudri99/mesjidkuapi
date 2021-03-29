<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMesjidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mesjid', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_mesjid');
            $table->string('alamat_lengkap');
            $table->string('lat');
            $table->string('lng');
            $table->string('photo_mesjid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mesjid');
    }
}
