<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Models\Pesanan;
use DB;
use Mail;

class UserController extends Controller
{
    public function register(Request $request){
        $email          = $request->input('email');
        
        $us = new Users();
        $us->nama       = $request->input('nama');
        $us->no_telp    = $request->input('no_telp');
        $us->email      = $email;
        $us->username   = $request->input('username');
        $us->password   = bcrypt(sha1($request->input('password')));
        $us->uang       = 0;
        $us->status     = 1;

        $us->save();

        if($us->save()){
            $status = 201;

            $data   = [
                'id'        => encrypt($us->id),
                'email'     => $us->email,
                'nama'      => $us->nama
            ];

            Mail::send('layouts.email', $data, function($message) use ($email){
                $message->to($email)->subject('Konfirmasi Pendaftaran Akun tokokerajinan.id');
                $message->from('no-reply@tokokerajinan.id', 'no-reply');
            });

            if (Mail:: failures()) {
                $pesan  = 'Pendaftaran berhasil dibuat, tetapi email tidak terkirim.';
            }else{
                $pesan  = 'Pendaftaran berhasil dibuat, silahkan cek email untuk melakukan konfirmasi akun.';
            }
        }

        return response()->json([
            'status'    => $status,
            'pesan'     => $pesan
        ]);
    }

    public function confirmation($id){
        $userId = decrypt($id);
        $user   = Users::where('id', $userId)->first();

        if($user){
            if($user->status == 1){
                $status = 200;
                $icon   = '<i class="mdi mdi-check mdi-48px"></i>';
                $pesan  = 'Selamat, akun Anda telah ter-verifikasi. Silahkan berbelanja.';
                Users::where('id', $userId)->update(['status' => 2]);
            }elseif($user->status > 1){
                $status = 406;
                $icon   = '<i class="mdi mdi-alert-circle-outline mdi-48px"></i>';
                $pesan  = 'Maaf, anda sudah pernah melakukan verifikasi. Silahkan login dengan akun anda.';
            }
        }else{
            $status = 404;
            $icon   = '<i class="mdi mdi-close-cirlce-outline mdi-48px"></i>';
            $pesan  = 'Akun tidak ada, silahkan cek email Anda.';
        }

        return response()->json([
            'status'    => $status,
            'icon'      => $icon,
            'pesan'     => $pesan
        ]);
    }

    public function cek_email(Request $request){
        $email  = $request->input('email');
        $user   = Users::where('email', $email)->first();
        
        if($user){
            $status = 406;
            $pesan  = 'Email <b><i>' . $email . '</i></b> sudah ada. Silahkan gunakan email lain';
        }else{
            $status = 200;
            $pesan  = '';
        }

        return response()->json([
            'status'    => $status,
            'pesan'     => $pesan
        ]);
    }

    public function cek_username(Request $request){
        $username   = $request->input('username');
        $user       = Users::where('username', $username)->first();
        
        if($user){
            $status = 406;
            $pesan  = 'Username <b><i>' . $username . '</i></b> sudah ada. Silahkan gunakan username lain';
        }else{
            $status = 200;
            $pesan  = '';
        }

        return response()->json([
            'status'    => $status,
            'pesan'     => $pesan
        ]);
    }

    public function history_pembelian(Request $request){
        $pesan  = Pesanan::leftJoin('users AS a', 'a.id', '=', 'pesanan.user_id')
                        ->leftJoin('detail_pesanan AS b', 'b.pesanan_id', '=', 'pesanan.id')
                        ->leftJoin('produk AS c', 'c.id', '=', 'b.produk_id')
                        ->leftJoin('komentar AS d', function($join){
                            $join->on('d.pesanan_id', '=', 'pesanan.id');
                            $join->on('d.produk_id', '=', 'c.id');
                        })->select(DB::raw('pesanan.id AS no_pesanan, c.id AS id_produk, nama_produk, slug_produk, isi_komentar, jumlah, harga * jumlah AS total_harga, pesanan.created_at AS tanggal_pembelian'))
                        ->orderBy('pesanan.created_at', 'desc')
                        ->where('a.id', 1)->get();
        
        if($pesan){
            $status = 200;
        }else{
            $status = 404;
        }

        return response()->json([
            'status'    => $status,
            'data'      => $pesan
        ]);
    }

}
