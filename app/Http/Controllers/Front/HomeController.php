<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banner;

class HomeController extends Controller
{
    public function index(){
        $banner = Banner::join('event AS e', 'e.id', '=', 'banner.event_id')
                        ->where('status', 2)
                        ->orderBy('e.created_at', 'DESC')
                        ->select('banner.foto', 'e.id', 'slug_event AS slug')
                        ->limit(5)->get();
        return view('frontend.index', compact('banner'));
    }
}
