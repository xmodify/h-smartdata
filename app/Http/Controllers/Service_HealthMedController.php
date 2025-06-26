<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service_HealthMedController extends Controller
{
//Check Login
      public function __construct()
      {
          $this->middleware('auth');
      }

//Create index
      public function index()
      {
           return view('service_healthmed.index');            
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
    
      $healthmed_month = DB::connection('hosxp')->select('
            SELECT CASE WHEN MONTH(service_date)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(service_date)+543,2))
            WHEN MONTH(service_date)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(service_date)+543,2))
            WHEN MONTH(service_date)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(service_date)+543,2))
            WHEN MONTH(service_date)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(service_date)+543,2))
            WHEN MONTH(service_date)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(service_date)+543,2))
            WHEN MONTH(service_date)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(service_date)+543,2))
            WHEN MONTH(service_date)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(service_date)+543,2))
            WHEN MONTH(service_date)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(service_date)+543,2))
            WHEN MONTH(service_date)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(service_date)+543,2))
            WHEN MONTH(service_date)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(service_date)+543,2))
            WHEN MONTH(service_date)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(service_date)+543,2))
            WHEN MONTH(service_date)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(service_date)+543,2))
            END AS "month",COUNT(DISTINCT hn) AS hn,COUNT(DISTINCT vn) AS visit,
            SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
            SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
            SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
            SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
            SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
            SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
            SUM(CASE WHEN (paidst IN ("01","03") OR pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
            FROM (SELECT h.service_date,o.vn,o.hn,o.pttype,p.hipdata_code,p.paidst,p.pcode 
		FROM ovst o
            INNER JOIN health_med_service h ON h.vn=o.vn 
		LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p ON p.pttype=vp.pttype
            LEFT JOIN health_med_service_operation h1 ON h1.health_med_service_id=h.health_med_service_id
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (h1.health_med_operation_item_id <> "" OR h1.health_med_operation_item_id IS NOT NULL)
            GROUP BY o.vn) AS a GROUP BY MONTH(service_date) ORDER BY YEAR(service_date) , MONTH(service_date)');
      $healthmed_m = array_column($healthmed_month,'month');
      $healthmed_visit_m = array_column($healthmed_month,'visit');
      $healthmed_hn_m = array_column($healthmed_month,'hn');

      $healthmed_year = DB::connection('hosxp')->select('
            SELECT IF(MONTH(h.service_date)>9,YEAR(h.service_date)+1,YEAR(h.service_date)) + 543 AS year_bud,
            COUNT(DISTINCT h.vn) as visit,COUNT(DISTINCT h.hn) as hn
            FROM health_med_service h
		LEFT JOIN health_med_service_operation h1 ON h1.health_med_service_id=h.health_med_service_id
            WHERE h.service_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
		AND (h1.health_med_operation_item_id <> "" OR h1.health_med_operation_item_id IS NOT NULL)
            GROUP BY year_bud
            ORDER BY year_bud');
      $healthmed_y = array_column($healthmed_year,'year_bud');
      $healthmed_visit_y = array_column($healthmed_year,'visit');
      $healthmed_hn_y = array_column($healthmed_year,'hn');

      $healthmed_operation = DB::connection('hosxp')->select('
            SELECT h2.health_med_operation_item_name,COUNT(*)  AS total,
            SUM(CASE WHEN p.hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
            SUM(CASE WHEN p.pttype like "O%" OR p.pttype like "B%" OR p.pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
            SUM(CASE WHEN p.hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
            SUM(CASE WHEN p.pttype like "L%" OR p.pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
            SUM(CASE WHEN p.hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
            SUM(CASE WHEN p.hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
            SUM(CASE WHEN (p.paidst IN ("01","03") OR p.pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
            FROM ovst o
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p ON p.pttype=vp.pttype
            LEFT JOIN health_med_service h ON h.vn=o.vn
            LEFT JOIN health_med_service_operation h1 ON h1.health_med_service_id=h.health_med_service_id
            LEFT JOIN health_med_operation_item h2 ON h2.health_med_operation_item_id=h1.health_med_operation_item_id
            LEFT JOIN vn_stat v ON v.vn = o.vn
            WHERE h.service_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (h1.health_med_operation_item_id <> "" OR h1.health_med_operation_item_id IS NOT NULL)
            GROUP BY h1.health_med_operation_item_id ORDER BY total DESC');

      return view('service_healthmed.count',compact('budget_year_select','budget_year','healthmed_m','healthmed_month',
            'healthmed_operation','healthmed_visit_m','healthmed_hn_m','healthmed_y','healthmed_visit_y','healthmed_hn_y'));            
}

//Create acupuncture การฝังเข็ม 
public function acupuncture(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $service_month = DB::connection('hosxp')->select('
            SELECT CASE WHEN MONTH(o.vstdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(o.vstdate)+543,2))
            WHEN MONTH(o.vstdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(o.vstdate)+543,2))
            WHEN MONTH(o.vstdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(o.vstdate)+543,2))
            WHEN MONTH(o.vstdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(o.vstdate)+543,2))
            WHEN MONTH(o.vstdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(o.vstdate)+543,2))
            WHEN MONTH(o.vstdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(o.vstdate)+543,2))
            WHEN MONTH(o.vstdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(o.vstdate)+543,2))
            WHEN MONTH(o.vstdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(o.vstdate)+543,2))
            WHEN MONTH(o.vstdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(o.vstdate)+543,2))
            WHEN MONTH(o.vstdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(o.vstdate)+543,2))
            WHEN MONTH(o.vstdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(o.vstdate)+543,2))
            WHEN MONTH(o.vstdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(o.vstdate)+543,2))
            END AS "month",COUNT(DISTINCT o.vn) AS visit,COUNT(DISTINCT o.hn) AS hn
            FROM ovst o LEFT JOIN opitemrece o1 ON o1.vn = o.vn
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND o1.icode IN ("3003848","3003849","3003851","3003852","3003853","3003856","3003857","3003858","3003859","3003860")
            GROUP BY MONTH(o.vstdate) ORDER BY YEAR(o.vstdate) , MONTH(o.vstdate)');
      $service_m = array_column($service_month,'month');
      $service_visit_m = array_column($service_month,'visit');
      $service_hn_m = array_column($service_month,'hn');

      $service_year = DB::connection('hosxp')->select('
            SELECT IF(MONTH(o.vstdate)>9,YEAR(o.vstdate)+1,YEAR(o.vstdate)) + 543 AS year_bud,
            COUNT(DISTINCT o.vn) AS visit,COUNT(DISTINCT o.hn) AS hn
            FROM ovst o LEFT JOIN opitemrece o1 ON o1.vn = o.vn
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND o1.icode IN ("3003848","3003849","3003851","3003852","3003853","3003856","3003857","3003858","3003859","3003860")
            GROUP BY year_bud ORDER BY year_bud');
      $service_y = array_column($service_year,'year_bud');
      $service_visit_y = array_column($service_year,'visit');
      $service_hn_y = array_column($service_year,'hn');


      return view('service_healthmed.acupuncture',compact('budget_year_select','budget_year','service_m',
            'service_visit_m','service_hn_m','service_y','service_visit_y','service_hn_y'));            
}


}
