<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailPesananTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->char('pesanan_id', 20);
            $table->integer('produk_id');
            $table->integer('jumlah');
            $table->tinyInteger('status')->comment('1 = belum bayar, 2 = sudah bayar, 3 = pengiriman, 4 = selesai / berhasil, 5 = gagal');
            $table->integer('origin');
            $table->integer('destination');
            $table->integer('weight');
            $table->char('courier', 15);
            $table->integer('ongkos');
            $table->text('alamat');
            $table->char('no_resi', 30)->nullable();

            $table->primary(['pesanan_id', 'produk_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_pesanan');
    }
}
