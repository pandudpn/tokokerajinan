<?php

namespace App\Http\Controllers\Front\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Models\Pesanan;
use App\Models\Komentar;
use App\Models\Detail_pesanan;
use Hash;
use Session;
use DB;

class UserController extends Controller
{
    public function register(){
        return view('frontend.user.register');
    }

    public function confirm(){
        return view('frontend.user.confirm');
    }

    public function login(Request $request){
        if($request->isMethod('get')){
            return view('frontend.user.login');
        }else{
            $username   = $request->input('username');
            $password   = sha1($request->input('password'));

            $user       = Users::leftJoin('toko AS t', 't.user_id', '=', 'users.id')
                                ->select('users.*', 'nama_toko', 'slug_toko', 't.id AS toko_id')
                                ->where('username', $username)->where('status', 2)->first();
            if($user){
                if(Hash::check($password, $user->password)){
                    Session::put('id', $user->id);
                    Session::put('username', $user->username);
                    Session::put('nama', $user->nama);
                    Session::put('email', $user->email);
                    Session::put('telp', $user->no_telp);
                    Session::put('toko', $user->toko_id);
                    Session::put('nama_toko', $user->nama_toko);
                    Session::put('slug', $user->slug_toko);
                    Session::put('uang', $user->uang);
                    Session::put('login', TRUE);

                    return redirect('/');
                }else{
                    return redirect('/login')->with('alert', 'Username atau password salah');
                }
            }else{
                return redirect('/login')->with('alert', 'Username atau password salah');
            }
        }
    }

    public function logout(){
        Session::flush();
        return redirect('/');
    }

    public function pembelian(){
        $id = session('id');
        $pembelian  = Pesanan::join('users AS us', 'us.id', '=', 'pesanan.user_id')
                            ->join('detail_pesanan AS dp', 'dp.pesanan_id', '=', 'pesanan.pesanan_id')
                            ->join('produk AS pr', 'pr.id', '=', 'dp.produk_id')
                            ->join('attachment AS at', 'at.produk_id', '=', 'pr.id')
                            ->join('pembayaran AS pem', 'pem.pesanan_id', '=', 'pesanan.pesanan_id')
                            ->where('us.id', $id)
                            ->where('dp.status', '>=', 1)
                            ->where('dp.status', '<=', 3)
                            ->groupBy('pesanan.pesanan_id')
                            ->groupBy('pr.id')
                            ->orderBy('pesanan.pesanan_id', 'DESC')
                            ->select('pesanan.pesanan_id AS no_pesanan', 'pr.id AS produk_id', 'nama_produk', 'harga', 'nama_foto', 'pesanan.created_at', 'dp.status AS status_pengiriman', 'dp.jumlah AS jumlah_pesanan', 'pem.status AS status_pembayaran')
                            ->get();

        $p          = Pesanan::join('users AS us', 'us.id', '=', 'pesanan.user_id')
                            ->join('detail_pesanan AS dp', 'dp.pesanan_id', '=', 'pesanan.pesanan_id')
                            ->join('produk AS pr', 'pr.id', '=', 'dp.produk_id')
                            ->join('attachment AS at', 'at.produk_id', '=', 'pr.id')
                            ->join('pembayaran AS pem', 'pem.pesanan_id', '=', 'pesanan.pesanan_id')
                            ->where('us.id', $id)
                            ->where('dp.status', '>=', 1)
                            ->where('dp.status', '<=', 3)
                            ->groupBy('pesanan.pesanan_id')
                            ->orderBy('pesanan.pesanan_id', 'DESC')
                            ->select('pesanan.pesanan_id AS no_pesanan', 'pr.id AS produk_id', 'nama_produk', 'harga', 'nama_foto', 'dp.status AS status_pengiriman', 'dp.jumlah AS jumlah_pesanan', 'pem.status AS status_pembayaran')
                            ->paginate(4);

        $selesai    = Pesanan::join('users AS us', 'us.id', '=', 'pesanan.user_id')
                            ->join('detail_pesanan AS dp', 'dp.pesanan_id', '=', 'pesanan.pesanan_id')
                            ->join('produk AS pr', 'pr.id', '=', 'dp.produk_id')
                            ->join('attachment AS at', 'at.produk_id', '=', 'pr.id')
                            ->join('pembayaran AS pem', 'pem.pesanan_id', '=', 'pesanan.pesanan_id')
                            ->leftJoin('komentar AS k', function($join){
                                $join->on('k.pesanan_id', '=', 'pesanan.pesanan_id');
                                $join->on('k.produk_id', '=', 'pr.id');
                            }) // subquery join
                            ->where('us.id', $id)
                            ->where('dp.status', 4)
                            ->groupBy('pesanan.pesanan_id')
                            ->groupBy('pr.id')
                            ->orderBy('pesanan.pesanan_id', 'DESC')
                            ->select('k.id AS komentar_id', 'pesanan.pesanan_id AS no_pesanan', 'pr.id AS produk_id', 'nama_produk', 'harga', 'nama_foto', 'dp.status AS status_pengiriman', 'dp.jumlah AS jumlah_pesanan', 'pem.status AS status_pembayaran')
                            ->get();

        $sp         = Pesanan::join('users AS us', 'us.id', '=', 'pesanan.user_id')
                            ->join('detail_pesanan AS dp', 'dp.pesanan_id', '=', 'pesanan.pesanan_id')
                            ->join('produk AS pr', 'pr.id', '=', 'dp.produk_id')
                            ->join('attachment AS at', 'at.produk_id', '=', 'pr.id')
                            ->join('pembayaran AS pem', 'pem.pesanan_id', '=', 'pesanan.pesanan_id')
                            ->where('us.id', $id)
                            ->where('dp.status', 4)
                            ->groupBy('pesanan.pesanan_id')
                            ->orderBy('pesanan.pesanan_id', 'DESC')
                            ->select('pesanan.pesanan_id AS no_pesanan', 'pr.id AS produk_id', 'nama_produk', 'harga', 'nama_foto', 'dp.status AS status_pengiriman', 'dp.jumlah AS jumlah_pesanan', 'pem.status AS status_pembayaran')
                            ->paginate(4);

        $laporan    = Pesanan::leftJoin('users AS us', 'us.id', '=', 'pesanan.user_id')
                            ->leftJoin('detail_pesanan AS dp', 'dp.pesanan_id', '=', 'pesanan.pesanan_id')
                            ->leftJoin('produk AS pr', 'pr.id', '=', 'dp.produk_id')
                            ->leftJoin('attachment AS at', 'at.produk_id', '=', 'pr.id')
                            ->join('pembayaran AS pem', 'pem.pesanan_id', '=', 'pesanan.pesanan_id')
                            ->where('us.id', $id)
                            ->where('dp.status', '>=', 4)
                            ->groupBy('pesanan.pesanan_id')
                            ->groupBy('pr.id')
                            ->orderBy('pesanan.pesanan_id', 'DESC')
                            ->select('pesanan.pesanan_id AS no_pesanan', 'pr.id AS produk_id', 'nama_produk', 'harga', 'nama_foto', 'dp.status AS status_pengiriman', 'dp.jumlah AS jumlah_pesanan', 'pem.status AS status_pembayaran')
                            ->get();

        $lp         = Pesanan::leftJoin('users AS us', 'us.id', '=', 'pesanan.user_id')
                            ->leftJoin('detail_pesanan AS dp', 'dp.pesanan_id', '=', 'pesanan.pesanan_id')
                            ->leftJoin('produk AS pr', 'pr.id', '=', 'dp.produk_id')
                            ->leftJoin('attachment AS at', 'at.produk_id', '=', 'pr.id')
                            ->join('pembayaran AS pem', 'pem.pesanan_id', '=', 'pesanan.pesanan_id')
                            ->where('us.id', $id)
                            ->where('dp.status', '>=', 4)
                            ->groupBy('pesanan.pesanan_id')
                            ->orderBy('pesanan.pesanan_id', 'DESC')
                            ->select('pesanan.pesanan_id AS no_pesanan', 'pr.id AS produk_id', 'nama_produk', 'harga', 'nama_foto', 'dp.status AS status_pengiriman', 'dp.jumlah AS jumlah_pesanan', 'pem.status AS status_pembayaran')
                            ->paginate(4);
        return view('frontend.user.pembelian', compact('pembelian', 'p', 'selesai', 'sp', 'laporan', 'lp'));
    }

    public function konfirmasi($pesanan, $produk){
        $pId    = \decrypt($pesanan);
        $prId   = \decrypt($produk);

        $dp     = Detail_pesanan::where('pesanan_id', $pId)->where('produk_id', $prId);
        $dp->update(['status' => 4]);

        $produk = Users::join('toko As t', 't.user_id', '=', 'users.id')
                    ->join('produk AS pr', 'pr.toko_id', '=', 't.id')
                    ->join('detail_pesanan AS dp', 'dp.produk_id', '=', 'pr.id')
                    ->where('pesanan_id', $pId)
                    ->where('produk_id', $prId);

        $produk->update(['uang' => DB::raw("uang + ((jumlah * harga) + ongkos)")]);

        return redirect('/pembelian');
    }

    public function komentar(Request $request, $pesanan, $produk){
        $pId    = \decrypt($pesanan);
        $prId   = \decrypt($produk);

        $komen  = new Komentar();
        $komen->pesanan_id  = $pId;
        $komen->produk_id   = $prId;
        $komen->isi_komentar= $request->input('isi');
        $komen->rating      = $request->input('rating');

        $komen->save();

        return redirect('/pembelian');
    }
}
