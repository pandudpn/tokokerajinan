<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use App\Models\Produk;

class ProductController extends Controller
{
    public function index(){
        return view('frontend.produk.filter');
    }

    public function details($slug, $id){
        if($slug == null || $id == null){
            return redirect('/');
        }
        return view('frontend.produk.details');
    }

    public function tambah(){
        if(!session('login')){
            return redirect('/login');
        }else{
            return view('frontend.produk.form');
        }
    }

    public function ubah($id, $user){
        if(!session('login')){
            return redirect('/login');
        }else{
            return view('frontend.produk.ubah');
        }
    }

    public function cart(Request $request){
        if(!Session::get('login')){
            return response()->json([
                'status' => 406,
                'pesan' => redirect('/login')
            ]);
        }else{
            $qty    = $request->input('qty');
            $id     = $request->input('id');

            $produk = Produk::join('attachment AS at', 'at.produk_id', '=', 'produk.id')
                            ->join('toko AS t', 't.id', '=', 'produk.toko_id')
                            ->select('produk.*', 'nama_foto', 'provinsi', 'kota', 'nama_toko')
                            ->where('produk.id', $id)->first();

            $cart   = session()->get('cart');

            if(!$cart){
                $cart   = [
                    $id => [
                        'nama'  => $produk->nama_produk,
                        'qty'   => $qty,
                        'harga' => $produk->harga,
                        'foto'  => $produk->nama_foto,
                        'toko'  => $produk->toko_id,
                        'prov'  => $produk->provinsi,
                        'berat' => $produk->berat,
                        'kota'  => $produk->kota,
                        'ongkos'=> 0
                    ]
                ];

                session()->put('cart', $cart);

                return response()->json([
                    'status'=> 201,
                    'pesan' => 'Berhasil menambahkan produk <b><i>'.$produk->nama_produk.'</i></b> ke dalam keranjang belanja.',
                    'data'  => $cart[$id]
                ]);
            }

            if(isset($cart[$id])){
                $cart[$id]['qty']   += $qty;

                session()->put('cart', $cart);

                return response()->json([
                    'status'=> 201,
                    'pesan' => 'Jumlah pembelian produk <b><i>'.$produk->nama_produk.'</i></b> menjadi '.$cart[$id]["qty"],
                    'data'  => $cart[$id]
                ]);
            }

            $cart[$id]  = [
                'nama'  => $produk->nama_produk,
                'qty'   => $qty,
                'harga' => $produk->harga,
                'foto'  => $produk->nama_foto,
                'toko'  => $produk->toko_id,
                'prov'  => $produk->provinsi,
                'berat' => $produk->berat,
                'kota'  => $produk->kota,
                'ongkos'=> 0
            ];

            session()->put('cart', $cart);

            return response()->json([
                'status'=> 201,
                'pesan' => 'Berhasil menambahkan produk <b><i>'.$produk->nama_produk.'</i></b> ke dalam keranjang belanja.',
                'data'  => $cart[$id]
            ]);
        }
    }
}
