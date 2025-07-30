<?php

namespace App\Http\Controllers\Hrims;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Session;

class DebtorController extends Controller
{
//Check Login---------------------------------------------------------------------
    public function __construct()
    {
        $this->middleware('auth');
    }
//index---------------------------------------------------------------------------
    public function index()
    {    
        return view('hrims.debtor.index');
    }
}
