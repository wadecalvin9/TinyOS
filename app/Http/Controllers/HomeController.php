<?php

namespace App\Http\Controllers;
use App\Models\Settings;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function fetch(){

    $settings = Settings::first();
    return view('welcome', compact('settings'));

}
}
