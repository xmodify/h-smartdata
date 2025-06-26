<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use PDF;

class Service_XrayController extends Controller
{
  //Check Login
public function __construct()
{
      $this->middleware('auth');
}

//Create index
public function index()
{     
      return view('service_xray.index');            
}

//Create CT Scan
public function ct(Request $request)
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;} 

  $ct_sum = DB::connection('hosxp')->select('
    SELECT COUNT(DISTINCT hn) AS hn,COUNT(DISTINCT vn) AS vn,COUNT(DISTINCT an) AS an,
    SUM(price_bill) AS price_bill,SUM(price_hosxp) AS price_hosxp,SUM(price_ct) AS price_ct
    FROM (SELECT o.vn,o.hn,o.an,p.hipdata_code,SUM(o.qty)*nd.price AS price_bill,
		SUM(o.sum_price) AS price_hosxp,SUM(o.qty)*nd.unitcost AS price_ct
    FROM opitemrece o
		LEFT JOIN visit_pttype vp ON vp.vn=o.vn
		LEFT JOIN ipt_pttype ip ON ip.an=o.an
    LEFT JOIN pttype p ON p.pttype=o.pttype	
		LEFT JOIN nondrugitems nd ON nd.icode = o.icode
    WHERE	o.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND o.icode IN (SELECT icode FROM xray_items WHERE xray_items_group = 3)
    GROUP BY o.hn,o.vn,o.an,o.icode) AS a');  
  foreach($ct_sum as $row){
    $sum_hn = $row->hn;
    $sum_vn = $row->vn;
    $sum_an = $row->an;
    $sum_price_bill = $row->price_bill;
    $sum_price_hosxp = $row->price_hosxp;
    $sum_price_ct = $row->price_ct;
  }

  $ct_list = DB::connection('hosxp')->select('
    SELECT IF((o.an IS NULL OR o.an =""),"OPD","IPD") AS depart,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,
    pt.cid,o.hn,o.an,p.hipdata_code,p.`name` AS pttype,IFNULL(vp.hospmain,ip.hospmain) AS hospmain,o.rxdate,o.rxtime,
		TIME(last_modified) AS updatetime,GROUP_CONCAT(DISTINCT s.`name`) AS item_name,SUM(o.qty)*nd.price AS price_bill,
		SUM(o.sum_price) AS price_claim,SUM(o.qty)*nd.unitcost AS price_ct
    FROM opitemrece o
    LEFT JOIN patient pt ON pt.hn=o.hn
		LEFT JOIN visit_pttype vp ON vp.vn=o.vn
		LEFT JOIN ipt_pttype ip ON ip.an=o.an
    LEFT JOIN pttype p ON p.pttype=o.pttype		
    LEFT JOIN s_drugitems s ON s.icode = o.icode	
    LEFT JOIN nondrugitems nd ON nd.icode = o.icode
    WHERE	o.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND o.icode IN (SELECT icode FROM xray_items WHERE xray_items_group = 3)
    GROUP BY o.hn,o.vn,o.an,o.icode
    ORDER BY o.pttype,o.hn,o.rxdate,o.rxtime');  

  $request->session()->put('ct_list',$ct_list);
  $request->session()->put('start_date',$start_date);
  $request->session()->put('end_date',$end_date);
  $request->session()->save(); 
        
  return view('service_xray.ct',compact('start_date','end_date','ct_sum','ct_list'));            
}

public function ct_excel()
{
    $ct_list = Session::get('ct_list');
    $start_date = Session::get('start_date');
    $end_date = Session::get('end_date');

  return view('service_xray.ct_excel',compact('start_date','end_date','ct_list'));
}

}
