<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Medicalrecord_OpdController extends Controller
{
//Check Login
public function __construct()
{
      $this->middleware('auth');
}
//Create index
    public function index()
    {
        return view('medicalrecord_opd.index');          
    }   

}

