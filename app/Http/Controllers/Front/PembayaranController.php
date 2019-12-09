<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Models\Pembayaran;
use App\Models\Pesanan;

class PembayaranController extends Controller
{
    public function index(Request $request){
        // dd('Nomer Pesanan => '.strtotime(now()).session('id'));
        if(!session('cart')){
            return redirect('/');
        }else{
            $user   = Users::find(session('id'));
        
            return view('frontend.cart.pembayaran', compact('user'));
        }
    }

    public function data(){
        $cart   = session()->get('cart');
        $status = 404;

        if(isset($cart)){
            $status = 200;
        }

        return response()->json([
            'status'    => $status,
            'cart'      => $cart
        ]);
    }
}
