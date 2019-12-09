<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Api;
use GuzzleHttp\Client;

class RajaOngkir extends Controller
{
    public function provinsi(Request $request){
        $id = $request->input('id');
        $api = new API;
        $data['id'] = $id;
        $ro = $api->getCURL('/province', 'query', $data);
        
        return response()->json($ro);
    }

    public function city(Request $request){
        $provinsi   = $request->input('provinsi');
        $id         = $request->input('id');
        $api        = new API;
        $data['province']   = $provinsi;
        $data['id'] = $id;

        $ro         = $api->getCURL('/city', 'query', $data);

        return response()->json($ro);
    }

    public function ongkir(Request $request){
        $kota_awal      = $request->input('kota_awal');
        $kota_tujuan    = $request->input('kota_tujuan');
        $berat          = $request->input('berat');
        $kurir          = $request->input('kurir');

        $api            = new API;
        $data   = array();
        $data['origin']     = $kota_awal;
        $data['destination']= $kota_tujuan;
        $data['weight']     = $berat;
        $data['courier']    = $kurir;

        $ro             = $api->getCURL('/cost', 'form_params', $data, 'post');
        // dd($ro->rajaongkir);

        return response()->json($ro);
    }
}
