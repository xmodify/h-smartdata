<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use PDF;

class Service_LabController extends Controller
{
  //Check Login
public function __construct()
{
      $this->middleware('auth');
}

//Create index
public function index()
{     
      return view('service_lab.index');            
}
#########################################################################################################
//Create มูลค่าการตรวจทางห้องปฏิบัติการ 20 อันดับ
public function value_top(Request $request)
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;} 

  $value_top = DB::connection('hosxp')->select('
    SELECT icode,dname,SUM(qty) AS qty,SUM(cost*qty) AS sum_cost,SUM(sum_price) AS sum_price,
    SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN sum_price ELSE 0 END) AS ucs_price,
    SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN ("14","H1") THEN sum_price ELSE 0 END) AS ofc_price,
    SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN sum_price ELSE 0 END) AS sss_price,
    SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN sum_price ELSE 0 END) AS lgo_price,
    SUM(CASE WHEN (hipdata_code like "NR%" OR hipdata_code IN ("ST","STP","A1","A9") OR paidst IN ("01","03")) THEN sum_price ELSE 0 END) AS other_price
    FROM (SELECT o.vn,o.an,n.icode,n.`name` AS dname,o.qty,o.cost,o.unitprice,o.sum_price,o.pttype,p.hipdata_code,p.paidst
    FROM opitemrece o 
    LEFT JOIN nondrugitems n ON n.icode = o.icode
    LEFT JOIN pttype p ON p.pttype=o.pttype
    WHERE o.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND n.income = "07" AND (o.an IS NULL OR o.an ="") GROUP BY o.vn,o.an,o.icode ) AS a 
    WHERE qty <> "0" GROUP BY icode ORDER BY sum_price DESC limit 30');  


  $value_top_ipd = DB::connection('hosxp')->select('
    SELECT icode,dname,SUM(qty) AS qty,SUM(cost*qty) AS sum_cost,SUM(sum_price) AS sum_price,
    SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN sum_price ELSE 0 END) AS ucs_price,
    SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN ("14","H1") THEN sum_price ELSE 0 END) AS ofc_price,
    SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN sum_price ELSE 0 END) AS sss_price,
    SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN sum_price ELSE 0 END) AS lgo_price,
    SUM(CASE WHEN (hipdata_code like "NR%" OR hipdata_code IN ("ST","STP","A1","A9") OR paidst IN ("01","03")) THEN sum_price ELSE 0 END) AS other_price
    FROM (SELECT o.vn,o.an,n.icode,n.`name` AS dname,o.qty,o.cost,o.unitprice,o.sum_price,o.pttype,p.hipdata_code,p.paidst
    FROM opitemrece o 
    LEFT JOIN nondrugitems n ON n.icode = o.icode
    LEFT JOIN pttype p ON p.pttype=o.pttype
    WHERE o.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND n.income = "07" AND (o.vn IS NULL OR o.vn ="") GROUP BY o.vn,o.an,o.icode ) AS a 
    WHERE qty <> "0" GROUP BY icode ORDER BY sum_price DESC limit 30');  

  $request->session()->put('value_top',$value_top);
  $request->session()->put('value_top_ipd',$value_top_ipd);
  $request->session()->put('start_date',$start_date);
  $request->session()->put('end_date',$end_date);
  $request->session()->save(); 
        
  return view('service_lab.value_top',compact('start_date','end_date','value_top','value_top_ipd'));            
}

public function value_top_excel()
{
    $value_top = Session::get('value_top');
    $value_top_ipd = Session::get('value_top_ipd');
    $start_date = Session::get('start_date');
    $end_date = Session::get('end_date');

  return view('service_lab.value_top_excel',compact('start_date','end_date','value_top','value_top_ipd'));
}

}
