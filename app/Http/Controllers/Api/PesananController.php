<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Detail_pesanan;
use App\Models\Pembayaran;
use App\Models\Produk;
use Veritrans_Config;
use Veritrans_Snap;
use Veritrans_Notification;
use Veritrans_Transaction;
use DB;

class PesananController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
 
        // Set midtrans configuration
        Veritrans_Config::$serverKey = config('services.midtrans.serverKey');
        Veritrans_Config::$isProduction = config('services.midtrans.isProduction');
        Veritrans_Config::$isSanitized = config('services.midtrans.isSanitized');
        Veritrans_Config::$is3ds = config('services.midtrans.is3ds');
    }

    public function insertPesanan(Request $request){
        $cart   = session('cart');

        \DB::transaction(function() use($cart) {
            $pesanan    = Pesanan::create([
                'pesanan_id'        => strtotime(now()).$this->request->user_id,
                'user_id'   => $this->request->user_id
            ]);
            // dd($pesanan->pesanan_id);
            $o      = 0;
            $total  = 0;
            $untung = 0;
            $data   = array();
            foreach($cart AS $key => $val){
                $jumlah             = intval($val['qty']);
                $ds = new Detail_pesanan();
                $ds->pesanan_id     = $pesanan->pesanan_id;
                $ds->produk_id      = $key;
                $ds->jumlah         = intval($val['qty']);
                $ds->status         = 1;
                $ds->origin         = $val['kota'];
                $ds->destination    = $val['kota_tujuan'];
                $ds->weight         = $val['berat'];
                $ds->courier        = $val['kurir'];
                $ds->ongkos         = $val['ongkos'];
                $ds->alamat         = $this->request->alamat;

                Produk::where('id', $key)->update(['stok' => DB::raw("stok - $jumlah")]);

                $ds->save();

                $subtotal           = (intval($val['qty'] * intval($val['harga'])) + intval($val['ongkos']));
                $total              += $subtotal;
                $untung             += intval($val['qty']);

                $data[]             = [
                    'id'    => $key,
                    'price' => (intval($val['harga']) + 200),
                    'quantity' => intval($val['qty']),
                    'name'  => $val['nama']
                ];

                $data[]             = [
                    'id'    => 'ongkos_'.$o,
                    'price' => intval($val['ongkos']),
                    'quantity' => 1,
                    'name'  => 'Ongkos'
                ];

                if($ds->save()){
                    $o++;
                }
            }
            $totalUntung    = (intval($untung) * 200) + intval($total);

            $payload    = [
                'transaction_details' => [
                    'order_id'  => $pesanan->pesanan_id,
                    'gross_amount' => $totalUntung
                ],
                'customer_details'  => [
                    'first_name'    => $this->request->nama,
                    'email'         => $this->request->email,
                    'phone'         => $this->request->notelp,
                    'address'       => $this->request->alamat
                ],
                'item_details'  => $data
            ];

            $snapToken  = Veritrans_Snap::getSnapToken($payload);

            // $pesanan->total = $total;
            // $pesanan->snap_token = $snapToken;
            // $pesanan->save();
            $pembayaran = Pembayaran::create([
                'pesanan_id'    => $pesanan->pesanan_id,
                'total'         => $total,
                'snap_token'    => $snapToken
            ]);

            $this->response['snap_token']   = $snapToken;
            $this->response['pesanan']      = \encrypt($pesanan->pesanan_id);
        });

        return response()->json($this->response);
    }

    public function notificationHandler(Request $request)
    {
        $notif = new Veritrans_Notification();
        \DB::transaction(function() use($notif) {
 
            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $orderId = $notif->order_id;
            $time   = $notif->transaction_time;
            $fraud = $notif->fraud_status;
            $pembayaran = Pembayaran::where('pesanan_id', $orderId)->first();
            // $pesanan = Pesanan::findOrFail($orderId);
    
            if ($transaction == 'capture') {
                // For credit card transaction, we need to check whether transaction is challenge by FDS or not
                if ($type == 'credit_card') {
                    if($fraud == 'challenge') {
                        // TODO set payment status in merchant's database to 'Challenge by FDS'
                        // TODO merchant should decide whether this transaction is authorized or not in MAP
                        // $pesanan->addUpdate("Transaction order_id: " . $orderId ." is challenged by FDS");
                        $pembayaran->setPending();
                        Detail_pesanan::where('pesanan_id', $orderId)->update(['status' => 1]);
                    } else {
                        // TODO set payment status in merchant's database to 'Success'
                        // $pembayaran->addUpdate("Transaction order_id: " . $orderId ." successfully captured using " . $type);
                        $pembayaran->setSuccess();
                        Detail_pesanan::where('pesanan_id', $orderId)->update(['status' => 2]);
                    }
                }
            } elseif ($transaction == 'settlement') {
                // TODO set payment status in merchant's database to 'Settlement'
                // $pembayaran->addUpdate("Transaction order_id: " . $orderId ." successfully transfered using " . $type);
                $pembayaran->setSuccess();
                Detail_pesanan::where('pesanan_id', $orderId)->update(['status' => 2]);
            } elseif($transaction == 'pending'){
                // TODO set payment status in merchant's database to 'Pending'
                // $pembayaran->addUpdate("Waiting customer to finish transaction order_id: " . $orderId . " using " . $type);
                $pembayaran->setPending();
                Detail_pesanan::where('pesanan_id', $orderId)->update(['status' => 1]);
            } elseif ($transaction == 'deny') {
                // TODO set payment status in merchant's database to 'Failed'
                // $pembayaran->addUpdate("Payment using " . $type . " for transaction order_id: " . $orderId . " is Failed.");
                $pembayaran->setFailed();
                Detail_pesanan::where('pesanan_id', $orderId)->update(['status' => 5]);
                Produk::join('detail_pesanan AS dp', 'dp.produk_id', '=', 'produk.id')->where('pesanan_id', $orderId)->update(['stok' => DB::raw("stok + jumlah")]);
            } elseif ($transaction == 'expire') {
                // TODO set payment status in merchant's database to 'expire'
                // $pembayaran->addUpdate("Payment using " . $type . " for transaction order_id: " . $orderId . " is expired.");
                $pembayaran->setExpired();
                Detail_pesanan::where('pesanan_id', $orderId)->update(['status' => 5]);
                Produk::join('detail_pesanan AS dp', 'dp.produk_id', '=', 'produk.id')->where('pesanan_id', $orderId)->update(['stok' => DB::raw("stok + jumlah")]);
            } elseif ($transaction == 'cancel') {
                // TODO set payment status in merchant's database to 'Failed'
                // $pembayaran->addUpdate("Payment using " . $type . " for transaction order_id: " . $orderId . " is canceled.");
                $pembayaran->setFailed();
                Detail_pesanan::where('pesanan_id', $orderId)->update(['status' => 5]);
                Produk::join('detail_pesanan AS dp', 'dp.produk_id', '=', 'produk.id')->where('pesanan_id', $orderId)->update(['stok' => DB::raw("stok + jumlah")]);
            }
        });
        return;
    }

    public function status($id){
        session()->forget('cart');
        $pId    = \decrypt($id);

        $pesanan= Pesanan::where('pesanan.pesanan_id', $pId)->where('status', 'pending')->join('pembayaran AS p', 'p.pesanan_id', '=', 'pesanan.pesanan_id')
                        ->first();
                        // dd(\encrypt(52));
        if($pesanan){
            if($pesanan->user_id == session('id')){
                return view('frontend.cart.status_pembayaran', compact('pesanan'));
            }else{
                return redirect('/');
            }
        }else{
            return redirect('/pembelian');
        }
    }
}
