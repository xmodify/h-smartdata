<?php

namespace App\Http\Controllers\Hnplus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HnplusController extends Controller
{
    public function index()
    {
        return view('hnplus.dashboard');
    }
}
