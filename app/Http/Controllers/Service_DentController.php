<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service_DentController extends Controller
{
//Check Login
      public function __construct()
      {
          $this->middleware('auth');
      }

//Create index
public function index()
{
      return view('service_dent.index');            
}

//Create count
public function count(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $dent_month = DB::connection('hosxp')->select('
            SELECT CASE WHEN MONTH(vstdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(vstdate)+543,2))
            WHEN MONTH(vstdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(vstdate)+543,2))
            WHEN MONTH(vstdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(vstdate)+543,2))
            WHEN MONTH(vstdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(vstdate)+543,2))
            WHEN MONTH(vstdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(vstdate)+543,2))
            WHEN MONTH(vstdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(vstdate)+543,2))
            WHEN MONTH(vstdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(vstdate)+543,2))
            WHEN MONTH(vstdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(vstdate)+543,2))
            WHEN MONTH(vstdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(vstdate)+543,2))
            WHEN MONTH(vstdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(vstdate)+543,2))
            WHEN MONTH(vstdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(vstdate)+543,2))
            WHEN MONTH(vstdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(vstdate)+543,2))
            END AS "month",COUNT(DISTINCT hn) as hn , COUNT(vn) as visit,SUM(sum_price) AS sum_price,
            SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
            SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN sum_price ELSE 0 END) AS "sum_price_ucs",
            SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN ("14","H1") THEN 1 ELSE 0 END) AS "ofc",
            SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN ("14","H1") THEN sum_price ELSE 0 END) AS "sum_price_ofc",
            SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
            SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN sum_price ELSE 0 END) AS "sum_price_sss",
            SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
            SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN sum_price ELSE 0 END) AS "sum_price_lgo",
            SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
            SUM(CASE WHEN hipdata_code like "NR%" THEN sum_price ELSE 0 END) AS "sum_price_fss",
            SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",  
            SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN sum_price ELSE 0 END) AS "sum_price_stp",  
            SUM(CASE WHEN (paidst IN ("01","03") OR hipdata_code IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay",
            SUM(CASE WHEN (paidst IN ("01","03") OR hipdata_code IN ("A1","A9")) THEN sum_price ELSE 0 END) AS "sum_price_pay"
            FROM (SELECT o.vn,o.hn,o.vstdate,vp.pttype,p.hipdata_code,p.paidst,SUM(o1.sum_price) AS sum_price
            FROM ovst o 
            INNER JOIN dtmain d ON d.vn=o.vn
            LEFT JOIN opitemrece o1 ON o1.vn=o.vn AND o1.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_type_id IN ("02","12"))
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p ON p.pttype=vp.pttype
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY o.vn ) AS a						
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate),MONTH(vstdate)');
      $dent_m = array_column($dent_month,'month');
      $dent_visit_m = array_column($dent_month,'visit');
      $dent_hn_m = array_column($dent_month,'hn');

      $dent_year = DB::connection('hosxp')->select('select 
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            COUNT(DISTINCT vn) as visit,COUNT(DISTINCT hn) as hn
            FROM dtmain                        
            WHERE vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            GROUP BY year_bud
            ORDER BY year_bud');
      $dent_y = array_column($dent_year,'year_bud');
      $dent_visit_y = array_column($dent_year,'visit');
      $dent_hn_y = array_column($dent_year,'hn');

      return view('service_dent.count',compact('budget_year_select','budget_year','dent_month','dent_m','dent_visit_m','dent_hn_m',
                  'dent_y','dent_visit_y','dent_hn_y'));            
}

}
