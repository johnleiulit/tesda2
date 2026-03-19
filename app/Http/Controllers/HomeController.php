<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $twspAnnouncement = \App\Models\TwspAnnouncement::getActive();
        
        return view('welcome', compact('twspAnnouncement'));
    }
}
