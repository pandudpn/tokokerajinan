<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Komentar;
use App\Models\Pertanyaan;
use App\Models\Kategori;
use App\Models\Visitor;
use App\Models\Attachment AS Foto;
use DB;

class ProdukController extends Controller
{
    public function kategori(){
        $kategori   = Kategori::all();

        if($kategori){
            $status = 200;
        }else{
            $status = 404;
        }

        return response()->json([
            'status'    => $status,
            'data'      => $kategori
        ]);
    }
    public function search(Request $request){
        $search     = $request->input('s');
        $kategori   = $request->input('k');

        $produk     = Produk::select(DB::raw('produk.id AS id_produk, nama_produk, slug_produk, nama_toko, nama_foto, slug_toko, sum(rating) / count(d.id) totalRating, count(d.id) totalTesti, count(t1.pesanan_id) totalPesanan, harga'))
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
                            ->when($search, function($query, $search){
                                return $query->where('nama_produk', 'like', "%$search%");
                            })
                            ->when($kategori, function($query, $kategori){
                                return $query->where('slug_kategori', $kategori);
                            })
                            ->where('produk.status', 2)
                            ->groupBy('produk.id')
                            ->orderByRaw('sum(rating) / count(d.id) DESC')
                            ->orderByRaw('count(t1.pesanan_id) DESC')
                            ->orderBy('produk.id', 'asc')
                            ->get();
        if($produk){
            $status = 200;
        }else{
            $status = 404;
        }

        return response()->json([
            'status'    => $status,
            'data'      => $produk
        ]);
    }

    public function details($slug, $id){
        $produk_id  = $id;
        Visitor::updateOrCreate([
            'produk_id' => $id,
            'tgl'       => date('Y-m-d', strtotime(now()))
        ],[
            'ip'        => request()->getClientIp(true)
        ]);

        $produk     = Produk::select(DB::raw('produk.id AS id_produk, stok, berat, nama_produk, nama_toko, kota, foto_toko, slug_toko, sum(rating) / count(d.id) totalRating, count(d.id) totalTesti, count(t1.pesanan_id) totalPesanan, harga, desc_produk'))
                            ->leftJoin(DB::raw('(SELECT * FROM detail_pesanan WHERE status = 4) AS t1'), function($join){
                                $join->on('t1.produk_id', '=', 'produk.id');
                            }) // subquery from
                            ->leftJoin('pesanan AS c', 'c.pesanan_id', '=', 't1.pesanan_id')
                            ->leftJoin('komentar AS d', function($join){
                                $join->on('d.pesanan_id', '=', 'c.pesanan_id');
                                $join->on('d.produk_id', '=', 'produk.id');
                            })->leftJoin('toko AS e', 'e.id', '=', 'produk.toko_id')
                            ->leftJoin('kategori AS k', 'k.id', '=', 'produk.kategori_id')
                            ->where('produk.id', $produk_id)
                            ->groupBy('produk.id')
                            ->orderByRaw('count(t1.pesanan_id) DESC')
                            ->orderBy('produk.id', 'asc')
                            ->first();
        $ulasan     = Komentar::join('pesanan AS a', 'a.pesanan_id', '=', 'komentar.pesanan_id')
                            ->join('users AS b', 'b.id', '=', 'a.user_id')
                            ->select('b.id AS id_user', 'nama', 'isi_komentar', 'rating', 'komentar.created_at AS tanggal_komentar')
                            ->where('komentar.produk_id', $produk_id)
                            ->orderBy('komentar.created_at', 'DESC')
                            ->get();
        $pertanyaan = Pertanyaan::join('users AS a', 'a.id', '=', 'pertanyaan.user_id')
                            ->select('a.id AS id_user', 'nama', 'isi_pertanyaan', 'pertanyaan.created_at AS tanggal_pertanyaan')
                            ->where('pertanyaan.produk_id', $produk_id)
                            ->orderBy('pertanyaan.created_at', 'DESC')
                            ->get();
        $foto       = Foto::join('produk AS a', 'a.id', '=', 'attachment.produk_id')
                            ->select('attachment.*')
                            ->where('a.id', $produk_id)
                            ->get();
        if($produk){
            $status = 200;
        }else{
            $status = 404;
        }
                    
        return response()->json([
            'status'    => $status,
            'data'      => [
                'produk'    => $produk,
                'foto'      => $foto,
                'ulasan'    => $ulasan,
                'tanya'     => $pertanyaan
            ]
        ]);
    }

    public function create(Request $request){
        if($request->hasFile('foto')){
            foreach($request->foto AS $image){
                $path       = 'assets/images/product/';
                $extension  = strtolower($image->getClientOriginalExtension());
                $name       = time(). str_random() . '.'. $extension;
                $image->move($path, $name);
                $foto[]     = $name;
            }
        }

        $pr = new Produk();
        $pr->kategori_id        = $request->input('kategori');
        $pr->toko_id            = $request->input('toko');
        $pr->nama_produk        = $request->input('nama');
        $pr->slug_produk        = strtolower(str_slug($request->input('nama')));
        $pr->harga              = $request->input('harga');
        $pr->stok               = $request->input('stok');
        $pr->berat              = $request->input('berat');
        $pr->status             = 1;
        $pr->desc_produk        = $request->input('desc');

        $pr->save();

        $o  = 0;
        for($i = 0; $i < count($foto); $i++){
            $f  = new Foto();
            $f->produk_id   = $pr->id;
            $f->nama_foto   = $foto[$i];

            $f->save();
            if($f->save()){
                $o++;
            }
        }
        if($o > 0){
            $status = 201;
            $pesan  = 'Produk berhasil ditambahkan, Tunggu konfirmasi untuk memulai berjualan.';
        }else{
            $status = 400;
            $pesan  = 'Terjadi kesalahan, silahkan coba beberapa menit lagi.';
        }

        return response()->json([
            'status'    => $status,
            'pesan'     => $pesan
        ]);
    }

    public function ubah($id, $user){
        $produk     = Produk::join('toko AS t', 't.id', '=', 'produk.toko_id')
                            ->select('produk.*', 'user_id')
                            ->where('produk.id', $id)
                            ->first();
        $foto       = Foto::join('produk AS p', 'p.id', '=', 'attachment.produk_id')
                            ->select('attachment.*')
                            ->where('produk_id', $id)
                            ->get();

        $status = '';
        if($produk->user_id == $user){
            $status = 200;
            $data['produk'] = $produk;
            $data['foto']   = $foto;
        }else {
            $status = 406;
            $data['pesan']  = 'Produk tersebut bukan milih anda.';
        }
        return response()->json([
            'status'    => $status,
            'data'      => $data
        ]);
    }

    public function edit(Request $request, $id){
        $produk_id  = $id;
        $foto       = array();
        
        if($request->hasFile('foto')){
            foreach($request->foto AS $image){
                $path       = 'assets/images/product/';
                $extension  = strtolower($image->getClientOriginalExtension());
                $name       = time(). str_random() . '.'. $extension;
                $image->move($path, $name);
                $foto[]     = $name;
            }
        }
        // dd($foto);

        $pr = Produk::find($produk_id);
        // dd($pr);
        $pr->kategori_id        = $request->input('kategori');
        $pr->toko_id            = $request->input('toko');
        $pr->nama_produk        = $request->input('nama');
        $pr->slug_produk        = strtolower(str_slug($request->input('nama')));
        $pr->harga              = $request->input('harga');
        $pr->stok               = $request->input('stok');
        $pr->berat              = $request->input('berat');
        $pr->status             = $request->input('status');
        $pr->desc_produk        = $request->input('desc');

        $pr->save();

        if(count($foto) > 0){
            $abc    = Foto::where('produk_id', $pr->id)->get();
            foreach($abc AS $data){
                unlink(public_path('assets/images/product/'.$data->nama_foto));
            }
            Foto::where('produk_id', $produk_id)->delete();
            for($i = 0; $i < count($foto); $i++){
                $f  = new Foto();
                $f->produk_id   = $pr->id;
                $f->nama_foto   = $foto[$i];
    
                $f->save();
            }
        }

        if($pr->save()){
            $status = 204;
            $pesan  = 'Produk '. $pr->nama_produk .' berhasil di ubah.';
        }else{
            $status = 400;
            $pesan  = 'Terjadi kesalahan, silahkan coba beberapa menit lagi.';
        }

        return response()->json([
            'status'    => $status,
            'pesan'     => $pesan
        ]);
    }

    public function delete($id, $user){
        $produk_id  = $id;
        $foto   = Foto::where('produk_id', $produk_id)->get();

        $cek    = Produk::join('toko AS t', 't.id', '=', 'produk.toko_id')
                        ->select('produk.*', 'user_id')
                        ->where('produk.id', $id)
                        ->first();

        if($cek->user_id == $user){
            foreach($foto AS $del){
                unlink(public_path('assets/images/product/'.$del->nama_foto));
            }

            Foto::where('produk_id', $produk_id)->delete();
            $produk = Produk::destroy($produk_id);
    
            if($produk){
                $status = 204;
                $pesan  = 'Produk berhasil dihapus';
            }else{
                $status = 400;
                $pesan  = 'Gagal menghapus produk, Silahkan coba beberapa saat lagi.';
            }
        }else{
            $status = 406;
            $pesan  = 'Maaf produk tersebut bukan milik Anda.';
        }

        return response()->json([
            'status'    => $status,
            'pesan'     => $pesan
        ]);
    }

    public function popular(){
        $produk     = Produk::select(DB::raw('produk.id AS id_produk, nama_produk, slug_produk, nama_toko, nama_foto, slug_toko, sum(rating) / count(d.id) totalRating, count(d.id) totalTesti, count(t1.pesanan_id) totalPesanan, harga'))
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
                            ->where('produk.status', 2)
                            ->groupBy('produk.id')
                            ->orderByRaw('sum(rating) / count(d.id) DESC')
                            ->orderByRaw('count(t1.pesanan_id) DESC')
                            ->orderBy('produk.id', 'asc')
                            ->limit(12)
                            ->get();
        
        if($produk){
            $status = 200;
        }else{
            $status = 404;
        }

        return response()->json([
            'status'    => $status,
            'data'      => $produk
        ]);
    }

}
