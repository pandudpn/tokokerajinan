<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function index(){
        // dd(session('cart'));
        return view('frontend.cart.cart');
    }

    public function cart(){
        $cart   = 0;
        $status = 404;
        if(session('cart')){
            $status = 200;
            $cart   = session('cart');
        }

        return response()->json([
            'status'    => $status,
            'data'      => $cart
        ]);
    }

    public function update(Request $request){
        $id     = $request->input('id');
        $qty    = $request->input('qty');
        $cart   = session()->get('cart');
        $tipe   = $request->input('tipe');
        $status = 404;

        if(isset($cart[$id])){
            if($tipe == 'tambah'){
                $cart[$id]['qty'] += $qty;
            }elseif($tipe == 'kurang'){
                $cart[$id]['qty'] -= $qty;

                if($cart[$id]['qty'] == 0){
                    unset($cart[$id]);
                }
            }

            session()->put('cart', $cart);
            $status = 200;
        }

        return response()->json([
            'status'    => $status,
            'data'      => $cart
        ]);
    }

    public function updateOngkos(Request $request){
        $id     = $request->input('id');
        $ongkos = $request->input('ongkos');
        $kt     = $request->input('kota_tujuan');
        $kurir  = $request->input('kurir');
        $status = 404;

        $cart   = session()->get('cart');

        if(isset($cart[$id])){
            $cart[$id]['ongkos'] = $ongkos;
            $cart[$id]['kota_tujuan'] = $kt;
            $cart[$id]['kurir']  = $kurir;
            
            session()->put('cart', $cart);
            $status = 200;
        }

        return response()->json([
            'status'    => $status,
            'data'      => $cart
        ]);
    }
}
