<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProdukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kategori_id');
            $table->integer('toko_id');
            $table->string('nama_produk', 150);
            $table->string('slug_produk', 200);
            $table->bigInteger('harga');
            $table->integer('stok');
            $table->integer('berat');
            $table->text('desc_produk');
            $table->tinyInteger('status')->comment('1 = waiting, 2 = approve, 3 = rejected');
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
        Schema::dropIfExists('produk');
    }
}
