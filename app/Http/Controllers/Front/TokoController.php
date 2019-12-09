<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Models\Toko;
use App\Models\Produk;
use App\Models\Detail_pesanan;

class TokoController extends Controller
{
    public function index(){
        return view('frontend.toko.index');
    }

    public function baru(){
        if(!session('login')){
            return redirect('/login');
        }else{
            return view('frontend.toko.baru');
        }
    }

    public function produk(){
        if(!session('login')){
            return redirect('/login');
        }else{
            return view('frontend.toko.product');
        }
    }

    public function penjualan(){
        $toko   = Toko::join('produk AS pr', 'pr.toko_id', '=', 'toko.id')
                    ->join('detail_pesanan AS dp', 'dp.produk_id', '=', 'pr.id')
                    ->join('pesanan AS ps', 'ps.pesanan_id', '=', 'dp.pesanan_id')
                    ->join('users AS us', 'us.id', '=', 'ps.user_id')
                    ->join('attachment AS at', 'at.produk_id', '=', 'pr.id')
                    ->where('dp.status', '>=', 2)
                    ->where('dp.status', '<=', 4)
                    ->where('toko.id', session('toko'))
                    ->groupBy('ps.pesanan_id')
                    ->groupBy('pr.id')
                    ->orderBy('ps.updated_at', 'DESC')
                    ->select('ps.pesanan_id AS no_pesanan', 'nama_produk', 'jumlah', 'destination', 'dp.alamat', 'dp.status AS status_pengiriman', 'pr.id AS produk_id', 'nama_foto')
                    ->get();

        $t      = Toko::join('produk AS pr', 'pr.toko_id', '=', 'toko.id')
                    ->join('detail_pesanan AS dp', 'dp.produk_id', '=', 'pr.id')
                    ->join('pesanan AS ps', 'ps.pesanan_id', '=', 'dp.pesanan_id')
                    ->join('users AS us', 'us.id', '=', 'ps.user_id')
                    ->join('attachment AS at', 'at.produk_id', '=', 'pr.id')
                    ->where('dp.status', '>=', 2)
                    ->where('dp.status', '<=', 4)
                    ->where('toko.id', session('toko'))
                    ->groupBy('ps.pesanan_id')
                    ->orderBy('dp.status', 'ASC')
                    ->orderBy('ps.updated_at', 'DESC')
                    ->select('ps.pesanan_id AS no_pesanan', 'nama_produk', 'jumlah', 'destination', 'dp.alamat', 'pr.id AS produk_id', 'nama_foto', 'nama AS pembeli', 'no_telp', 'email')
                    ->paginate(4);

        return view('frontend.toko.penjualan', compact('toko', 't'));
    }

    public function noresi(Request $request, $produk, $pesanan){
        $prId   = \decrypt($produk);
        $pId    = \decrypt($pesanan);

        Detail_pesanan::where('pesanan_id', $pId)->where('produk_id', $prId)->update(['no_resi' => $request->input('noresi'), 'status' => 3]);

        return redirect('/toko/penjualan');
    }

    public function chart(Request $request){
        $id = session('toko');
        $from   = $request->input('f');
        $to     = $request->input('t');

        $toko   = Toko::select(DB::raw('sum((harga * jumlah) + ongkos) AS total, DATE_FORMAT(ps.updated_at, "%M %Y") AS bulan, YEAR(ps.updated_at) AS year, MONTH(ps.updated_at) AS month'))
                    ->join('produk AS pr', 'pr.toko_id', '=', 'toko.id')
                    ->join('detail_pesanan AS dp', 'dp.produk_id', '=', 'pr.id')
                    ->join('pesanan AS ps', 'ps.pesanan_id', '=', 'dp.pesanan_id')
                    ->where('toko.id', $id)
                    ->where('dp.status', 4)
                    ->when($from, function($query, $from){
                        return $query->where('ps.updated_at', '>=', $from.' 00:00:00');
                    })
                    ->when($to, function($query, $to){
                        return $query->where('ps.updated_at', '<=', $to. ' 23:59:59');
                    })
                    ->groupBy(DB::raw('year, month'))
                    ->get();

        return response()->json($toko);
    }
}
