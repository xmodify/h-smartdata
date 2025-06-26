<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service_DeathController extends Controller
{
//Check Login
public function __construct()
{
      $this->middleware('auth');
}

//Create index
public function index()
{

      return view('service_death.index');            
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
    
      $death_month = DB::connection('hosxp')->select('select 
            CASE WHEN MONTH(death_date)="10" THEN "ต.ค."
            WHEN MONTH(death_date)="11" THEN "พ.ย."
            WHEN MONTH(death_date)="12" THEN "ธ.ค."
            WHEN MONTH(death_date)="1" THEN "ม.ค."
            WHEN MONTH(death_date)="2" THEN "ก.พ."
            WHEN MONTH(death_date)="3" THEN "มี.ค."
            WHEN MONTH(death_date)="4" THEN "เม.ย."
            WHEN MONTH(death_date)="5" THEN "พ.ค."
            WHEN MONTH(death_date)="6" THEN "มิ.ย."
            WHEN MONTH(death_date)="7" THEN "ก.ค."
            WHEN MONTH(death_date)="8" THEN "ส.ค."
            WHEN MONTH(death_date)="9" THEN "ก.ย."
            END AS "month",
            COUNT(DISTINCT hn) AS "total",
            SUM(CASE WHEN death_place = "1" THEN 1 ELSE 0 END) AS "in",
            SUM(CASE WHEN death_place = "2" OR death_place ="" OR death_place IS NULL THEN 1 ELSE 0 END) AS "out"
            FROM death
            WHERE death_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY MONTH(death_date)
            ORDER BY YEAR(death_date) , MONTH(death_date)');
      $death_m = array_column($death_month,'month');
      $death_total_m = array_column($death_month,'total');
      $death_in_m = array_column($death_month,'in'); 
      $death_out_m = array_column($death_month,'out'); 

      $death_year = DB::connection('hosxp')->select('select 
            IF(MONTH(death_date)>9,YEAR(death_date)+1,YEAR(death_date)) + 543 AS year_bud,
            COUNT(DISTINCT hn) AS "total",
            SUM(CASE WHEN death_place = "1" THEN 1 ELSE 0 END) AS "in",
            SUM(CASE WHEN death_place = "2" OR death_place ="" OR death_place IS NULL THEN 1 ELSE 0 END) AS "out"
            FROM death
            WHERE death_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            GROUP BY year_bud
            ORDER BY year_bud');
      $death_y = array_column($death_year,'year_bud');
      $death_total_y = array_column($death_year,'total');
      $death_in_y = array_column($death_year,'in'); 
      $death_out_y = array_column($death_year,'out'); 

      $death_list = DB::connection('hosxp')->select('select 
            pt.hn,d.an,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname ) AS ptname,
            pt.birthday,d.death_date,d.death_time,c1.name1 AS name504,CONCAT("[",i1.`code`,"] ",i1.`name`) AS icdname
            FROM death d
            LEFT OUTER JOIN patient pt ON pt.hn = d.hn
            LEFT OUTER JOIN rpt_504_name c1 ON c1.id = d.death_cause
            LEFT OUTER JOIN icd101 i1 ON i1.CODE = d.death_diag_1 
            WHERE	d.death_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND d.death_place = "1"
            ORDER BY d.death_date');

      return view('service_death.count',compact('budget_year_select','budget_year','death_m','death_total_m','death_in_m','death_out_m',
                  'death_y','death_total_y','death_in_y','death_out_y','death_list'));            
}

//Create diag_504
public function diag_504(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_504 = DB::connection('hosxp')->select('select 
            IF(c1.name1="" OR c1.name1 IS NULL,"ไม่มีรหัสโรค",c1.name1) AS name,
            sum(case when pt.sex=1 THEN 1 ELSE 0 END) as male,   
            sum(case when pt.sex=2 THEN 1 ELSE 0 END) as female,   
            COUNT(DISTINCT d.hn) AS "sum"
            FROM death d
            LEFT OUTER JOIN patient pt ON pt.hn = d.hn
            LEFT OUTER JOIN rpt_504_name c1 ON c1.id = d.death_cause
            LEFT OUTER JOIN icd101 i1 ON i1.CODE = d.death_diag_1 
            WHERE	d.death_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY d.death_cause
            ORDER BY COUNT(DISTINCT d.hn) DESC');

      return view('service_death.diag_504',compact('budget_year_select','budget_year','diag_504'));            
}

//Create diag_icd10
public function diag_icd10(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_icd10 = DB::connection('hosxp')->select('select 
            IF(CONCAT("[",d.death_diag_1,"] ",i1.NAME) ="" OR CONCAT("[",d.death_diag_1,"] ",i1.NAME) IS Null,
            "ไม่บันทึกรหัสโรค",CONCAT("[",d.death_diag_1,"] ",i1.NAME)) AS name,
            sum(case when pt.sex=1 THEN 1 ELSE 0 END) as male,   
            sum(case when pt.sex=2 THEN 1 ELSE 0 END) as female,   
            COUNT(DISTINCT d.hn) AS "sum"
            FROM death d
            LEFT OUTER JOIN patient pt ON pt.hn = d.hn
            LEFT OUTER JOIN rpt_504_name c1 ON c1.id = d.death_cause
            LEFT OUTER JOIN icd101 i1 ON i1.CODE = d.death_diag_1 
            WHERE	d.death_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY d.death_diag_1 
            ORDER BY COUNT(DISTINCT d.hn) DESC');

      return view('service_death.diag_icd10',compact('budget_year_select','budget_year','diag_icd10'));            
}

}
