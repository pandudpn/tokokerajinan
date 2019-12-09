<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// halaman utama
Route::get('/', 'Front\HomeController@index');

// register dan login dan logout
Route::get('/register', 'Front\Auth\UserController@register');
Route::match(['get', 'post'], '/login', 'Front\Auth\UserController@login');
Route::get('/logout', 'Front\Auth\UserController@logout');

// confirmasi
Route::get('/user/confirmation/{id}', 'Front\Auth\UserController@confirm');

// toko
// Route::get('/{slug_toko}', 'Front\TokoController@index');
Route::prefix('/toko')->group(function(){
    // buat toko
    Route::get('/buat', 'Front\TokoController@baru');
    // seluruh produk
    Route::get('/produk', 'Front\TokoController@produk');
    // penjualan
    Route::get('/penjualan', 'Front\TokoController@penjualan');
    // chart
    Route::get('/penjualan/chart', 'Front\TokoController@chart');
    // toko
    Route::get('/{slug}', 'Front\TokoController@index');
    // input no resi
    Route::post('/resi/{produk}/{pesanan}', 'Front\TokoController@noresi');
});

// produk
Route::prefix('/p')->group(function(){
    // search or filter
    Route::get('/', 'Front\ProductController@index')->name('produk.filter');
    // tambah produk
    Route::get('/tambah', 'Front\ProductController@tambah');
    // adding to cart
    Route::post('/addingToCart', 'Front\ProductController@cart');
    // ubah produk
    Route::get('/ubah/{id}/{user}', 'Front\ProductController@ubah');
    // details produk
    Route::get('/{slug}/{id}', 'Front\ProductController@details')->name('produk.details');
});

// cart
Route::get('/cart', 'Front\CartController@index');
Route::get('/cart/getdata', 'Front\CartController@cart');
Route::post('/cart/update', 'Front\CartController@update');
Route::post('/cart/updatecost', 'Front\CartController@updateOngkos');
Route::get('/pembayaran', 'Front\PembayaranController@index');

Route::post('/pesanan/store', 'Api\PesananController@insertPesanan')->name('pesanan.store');
Route::post('/notification/handler', 'Api\PesananController@notificationHandler');
Route::get('/status/{id}', 'Api\PesananController@status');

// pembelian
Route::prefix('/pembelian')->group(function(){
    // index
    Route::get('/', 'Front\Auth\UserController@pembelian');
    // konfirmasi
    Route::get('/konfirmasi/{pesanan}/{produk}', 'Front\Auth\UserController@konfirmasi');
    // komentar
    Route::post('/komentar/{pesanan}/{produk}', 'Front\Auth\UserController@komentar');
});

// api
Route::prefix('/api')->group(function(){
    // rajaongkir
    Route::prefix('/rajaongkir')->group(function(){
        // provinsi
        Route::get('/provinsi', 'Api\RajaOngkir@provinsi');
        // city
        Route::get('/city', 'Api\RajaOngkir@city');
        // ongkir
        Route::post('/cost', 'Api\RajaOngkir@ongkir');
    });

    // produk
    Route::prefix('/p')->group(function(){
        // get produk
        Route::get('/', 'Api\ProdukController@search');
        // get kategori
        Route::get('/kategori', 'Api\ProdukController@kategori')->name('kategori');
        // popular produk
        Route::get('/popular', 'Api\ProdukController@popular');
        // simpan produk
        Route::post('/baru', 'Api\ProdukController@create');
        // edit produk
        Route::post('/edit/{id}', 'Api\ProdukController@edit');
        // get data edit produk
        Route::get('/ubah/{id}/{user}', 'Api\ProdukController@ubah');
        // delete produk
        Route::delete('/delete/{id}/{user}', 'Api\ProdukController@delete');
        // details produk
        Route::get('/{slug}/{id}', 'Api\ProdukController@details');
    });

    // toko
    Route::prefix('/toko')->group(function(){
        // cek nama toko
        Route::get('/baru/cek_toko', 'Api\TokoController@cek_toko');
        // get produk
        Route::get('/produk/{tokoSession}', 'Api\TokoController@produk');
        // buat toko
        Route::post('/baru', 'Api\TokoController@create');
        // edit toko
        Route::post('/edit/{id}', 'Api\TokoController@edit');
        // delete toko
        Route::delete('/delete/{id}/{user}', 'Api\TokoController@delete');
        // get toko
        Route::get('/{slug}', 'Api\TokoController@index');
    });

    // user
    Route::prefix('/user')->group(function(){
        // daftar / regis
        Route::post('/register', 'Api\UserController@register');
        // cek_email
        Route::get('/cek_email', 'Api\UserController@cek_email');
        // cek_username
        Route::get('/cek_username', 'Api\UserController@cek_username');
        // cek history pembelian
        Route::get('/history', 'Api\UserController@history_pembelian');
        // confirmation
        Route::get('/confirm/{id}', 'Api\UserController@confirmation');
    });
});
