<?php

namespace App\Http\Controllers\Hrims;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HrimsController extends Controller
{
    public function index()
    {
        return view('hrims.dashboard');
    }
}
