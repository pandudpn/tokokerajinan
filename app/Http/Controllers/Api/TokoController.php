<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Toko;
use App\Models\Produk;
use App\Models\Attachment AS Foto;
use DB;

class TokoController extends Controller
{
    public function index($slug){
        $toko   = Toko::select(DB::raw('toko.*, count(pe.pesanan_id) AS totalPenjualan'))
                    ->leftJoin('produk AS p', 'p.toko_id', '=', 'toko.id')
                    ->leftJoin(DB::raw('(SELECT * FROM detail_pesanan WHERE status = 4) AS t1'), function($join){
                        $join->on('t1.produk_id', '=', 'p.id');
                    }) // subquery from
                    ->leftJoin('pesanan AS pe', 'pe.pesanan_id', '=', 't1.pesanan_id')
                    ->groupBy('toko.id')
                    ->where('slug_toko', $slug)->first();

        $produk = Toko::select(DB::raw('toko.id AS id_toko, us.nama, no_telp, email, username, produk.id AS id_produk, nama_produk, slug_produk, harga, nama_foto, sum(rating) / count(d.id) totalRating, count(d.id) totalTesti, count(t1.pesanan_id) totalPesanan'))
                    ->leftJoin('produk', 'produk.toko_id', '=', 'toko.id')
                    ->leftJoin(DB::raw('(SELECT * FROM detail_pesanan WHERE status = 4) AS t1'), function($join){
                        $join->on('t1.produk_id', '=', 'produk.id');
                    }) // subquery from
                    ->leftJoin('pesanan AS c', 'c.pesanan_id', '=', 't1.pesanan_id')
                    ->leftJoin('users AS us', 'us.id', '=', 'toko.user_id')
                    ->leftJoin('komentar AS d', function($join){
                        $join->on('d.pesanan_id', '=', 'c.pesanan_id');
                        $join->on('d.produk_id', '=', 'produk.id');
                    })->leftJoin('kategori AS k', 'k.id', '=', 'produk.kategori_id')
                    ->leftJoin('attachment AS at', 'at.produk_id', '=', 'produk.id')
                    ->where('toko.slug_toko', $slug)
                    ->where('produk.status', 2)
                    ->groupBy('produk.id')
                    ->orderByRaw('count(t1.pesanan_id) DESC')
                    ->orderBy('produk.id', 'asc')
                    ->get();

        if($toko){
            $status = 200;
        }else{
            $status = 400;
        }

        return response()->json([
            'status'    => $status,
            'data'      => [
                'toko'  => $toko,
                'produk'=> $produk
            ]
        ]);
    }

    public function produk(Request $request, $tokoSession){
        // $tokoSession    = $request->input('toko');
        $nama   = $request->input('produk');

        $toko   = Toko::where('id', $tokoSession)->first();
        $produk = Produk::select(DB::raw('produk.*, nama_foto, sum(rating) / count(d.id) totalRating, count(d.id) totalTesti, count(t1.pesanan_id) totalPesanan, harga'))
                            ->leftJoin(DB::raw('(SELECT * FROM detail_pesanan WHERE status = 4) AS t1'), function($join){
                                $join->on('t1.produk_id', '=', 'produk.id');
                            }) // subquery from
                            ->leftJoin('pesanan AS c', 'c.pesanan_id', '=', 't1.pesanan_id')
                            ->leftJoin('komentar AS d', function($join){
                                $join->on('d.pesanan_id', '=', 'c.pesanan_id');
                                $join->on('d.produk_id', '=', 'produk.id');
                            })->leftJoin('toko AS e', 'e.id', '=', 'produk.toko_id')
                            ->leftJoin('kategori AS k', 'k.id', '=', 'produk.kategori_id')
                            ->leftJoin('attachment AS at', 'at.produk_id', '=', 'produk.id')
                            ->where('e.id', $tokoSession)
                            ->when($nama, function($query, $nama){
                                return $query->where('nama_produk', 'like', "%$nama%");
                            })
                            ->groupBy('produk.id')
                            ->orderByRaw('count(t1.pesanan_id) DESC')
                            ->orderBy('produk.id', 'asc')
                            ->get();
        
        return response()->json([
            'toko'  => $toko,
            'produk'=> $produk
        ]);
    }

    public function create(Request $request){
        $foto = '';
        if($request->hasFile('foto')){
            $path       = 'assets/images/toko/';
            $extension  = strtolower($request->file('foto')->getClientOriginalExtension());
            $name       = time(). str_random() . '.'. $extension;
            $request->file('foto')->move($path, $name);
            $foto       = $name;
        }

        $user   = $request->input('user');
        $toko   = Toko::where('user_id', $user)->first();
        if($toko){
            $status = 406;
            $pesan  = 'Kamu sudah memiliki Toko, nama Toko tersebut ialah <b>'. $toko->nama_toko .'</b>';
        }else{
            $t  = new Toko();
            $t->user_id     = $user;
            $t->nama_toko   = $request->input('nama');
            $t->desc_toko   = $request->input('desc');
            $t->slug_toko   = strtolower(str_slug($request->input('nama')));
            $t->provinsi    = $request->input('provinsi');
            $t->kota        = $request->input('kota');
            $t->alamat_toko = $request->input('alamat');
            $t->foto_toko   = $foto;

            $t->save();

            if($t->save()){
                $status = 201;
                $pesan  = 'Toko '. $t->nama_toko .' berhasil dibuat.';
            }else{
                $status = 400;
                $pesan  = 'Terjadi kesalahan, silahkan coba beberapa saat lagi.';
            }
        }
        return response()->json([
            'status'    => $status,
            'pesan'     => $pesan
        ]);
    }

    public function edit(Request $request, $id){
        $toko_id    = $id;
        $foto = '';
        if($request->hasFile('foto')){
            $path       = 'assets/images/toko/';
            $extension  = strtolower($request->file('foto')->getClientOriginalExtension());
            $name       = time(). str_random() . '.'. $extension;
            $request->file('foto')->move($path, $name);
            $foto       = $name;
        }else{
            $foto       = $request->input('foto_lama');
        }

        $user   = $request->input('user');
        $toko   = Toko::find($toko_id);
        if($toko->user_id != $user){
            $status = 406;
            $pesan  = 'Kamu bukan pemilik toko tersebut.';
        }else{
            if($request->hasFile('foto')){
                if($toko->foto_toko != ''){
                    unlink(public_path('assets/images/toko/'.$toko->foto_toko));
                }
            }
            $toko->user_id     = $user;
            $toko->nama_toko   = $request->input('nama');
            $toko->desc_toko   = $request->input('desc');
            $toko->slug_toko   = strtolower(str_slug($request->input('nama')));
            $toko->provinsi    = $request->input('provinsi');
            $toko->kota        = $request->input('kota');
            $toko->alamat_toko = $request->input('alamat');
            $toko->foto_toko   = $foto;

            $toko->save();

            if($toko->save()){
                $status = 204;
                $pesan  = 'Toko '. $toko->nama_toko .' berhasil diubah.';
            }else{
                $status = 400;
                $pesan  = 'Terjadi kesalahan, silahkan coba beberapa saat lagi.';
            }
        }
        return response()->json([
            'status'    => $status,
            'pesan'     => $pesan
        ]);
    }

    public function delete($id, $user){
        $toko_id    = $id;
        $t          = Toko::find($toko_id);

        if($t->user_id != $user){
            $status = 406;
            $pesan  = 'Anda tidak bisa menghapus toko yang bukan milik anda.';
        }else{
            if($t->foto_toko != ''){
                unlink(public_path('assets/images/toko/'.$t->foto_toko));
            }
            $produk     = Produk::where('toko_id', $toko_id)->get();
            foreach($produk AS $data){
                $foto       = Foto::where('produk_id', $data->id)->get();
                foreach($foto AS $row){
                    unlink(public_path('assets/images/product/'.$row->nama_foto));
                    Foto::destroy($row->id);
                }
            }
            Produk::where('toko_id', $toko_id)->delete();
            $toko   = Toko::destroy($toko_id);
            if($toko){
                $status = 204;
                $pesan  = 'Toko berhasil dihapus.';
            }else{
                $status = 400;
                $pesan  = 'Terjadi kesalahan, silahkan coba beberapa saat lagi.';
            }
        }

        return response()->json([
            'status'    => $status,
            'pesan'     => $pesan
        ]);
    }

    public function cek_toko(Request $request){
        $nama   = $request->input('nama');
        $toko   = Toko::where('nama_toko', $nama)->first();
        if($toko){
            $status = 406;
            $pesan  = 'Toko <b><i>' . $nama . '</i></b> sudah ada, silahkan gunakan yang lain.';
        }else{
            $status = 200;
            $pesan  = '';
        }

        return response()->json([
            'status'    => $status,
            'pesan'     => $pesan
        ]);
    }

}
