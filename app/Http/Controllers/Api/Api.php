<?php

namespace App\Http\Controllers\Api;
use GuzzleHttp\Client;

class Api
{
    public function getCURL($endpoint, $tipedata, $data=array(),$type="get")
	{
		$URL = env('RO_URL_API');
		$key = env('RO_API_KEY');
		$client		= new Client();
		$response	= $client->request($type, $URL.$endpoint, [
			'headers' => [
				'key' => $key
			],
			$tipedata => $data
		]);

		$body	= '';
		if($response->getStatusCode() == 200){
			$body	= $response->getBody();
		}

		return json_decode($body);
	}
}
