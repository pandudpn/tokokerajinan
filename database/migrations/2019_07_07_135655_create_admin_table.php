<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('divisi_id')->unsigned();
            $table->char('nama', 30);
            $table->string('email', 40);
            $table->string('password', 100);
            $table->char('phone', 15);
            $table->enum('jk', ['Laki-laki', 'Perempuan'])->default('Laki-laki');
            $table->char('tempat_lahir', 20);
            $table->date('tgl_lahir');
            $table->binary('foto');
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
        Schema::dropIfExists('admin');
    }
}
