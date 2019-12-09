<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('toko', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('nama_toko', 50);
            $table->string('slug_toko', 100);
            $table->text('desc_toko');
            $table->integer('provinsi');
            $table->integer('kota');
            $table->text('alamat_toko');
            $table->string('foto_toko', 150);
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
        Schema::dropIfExists('toko');
    }
}
