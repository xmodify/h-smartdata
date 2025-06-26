<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service_ReferController extends Controller
{
//Check Login
public function __construct()
{
      $this->middleware('auth');
}

//Create index
public function index()
{
      return view('service_refer.index');            
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
    
      $refer_month = DB::connection('hosxp')->select('select 
                  CASE WHEN MONTH(refer_date)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(refer_date)+543,2))
                  WHEN MONTH(refer_date)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(refer_date)+543,2))
                  WHEN MONTH(refer_date)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(refer_date)+543,2))
                  WHEN MONTH(refer_date)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(refer_date)+543,2))
                  WHEN MONTH(refer_date)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(refer_date)+543,2))
                  WHEN MONTH(refer_date)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(refer_date)+543,2))
                  WHEN MONTH(refer_date)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(refer_date)+543,2))
                  WHEN MONTH(refer_date)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(refer_date)+543,2))
                  WHEN MONTH(refer_date)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(refer_date)+543,2))
                  WHEN MONTH(refer_date)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(refer_date)+543,2))
                  WHEN MONTH(refer_date)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(refer_date)+543,2))
                  WHEN MONTH(refer_date)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(refer_date)+543,2))
                  END AS "month",
                  SUM(CASE WHEN department = "OPD" THEN 1 ELSE 0 END) AS "opd",
                  SUM(CASE WHEN department = "IPD" THEN 1 ELSE 0 END) AS "ipd",
                  SUM(CASE WHEN with_ambulance = "Y" THEN 1 ELSE 0 END) AS "ambulance",
                  SUM(CASE WHEN refer_hospcode = "10703" THEN 1 ELSE 0 END) AS "r_10703",
                  SUM(CASE WHEN refer_hospcode = "10669" THEN 1 ELSE 0 END) AS "r_10669",
                  SUM(CASE WHEN refer_hospcode = "12269" THEN 1 ELSE 0 END) AS "r_12269",
                  SUM(CASE WHEN refer_hospcode NOT IN ("10669","10703","12269") THEN 1 ELSE 0 END) AS "r_outher"
                  from referout
                  WHERE refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                  GROUP BY MONTH(refer_date)
                  ORDER BY YEAR(refer_date) , MONTH(refer_date)');
      $refer_m = array_column($refer_month,'month');
      $refer_opd_m = array_column($refer_month,'opd');
      $refer_ipd_m = array_column($refer_month,'ipd'); 
      $refer_ambu_m = array_column($refer_month,'ambulance'); 
      $refer_r_10703_m = array_column($refer_month,'r_10703'); 
      $refer_r_10669_m = array_column($refer_month,'r_10669'); 
      $refer_r_12269_m = array_column($refer_month,'r_12269'); 
      $refer_r_outher_m = array_column($refer_month,'r_outher'); 

      $refer_year = DB::connection('hosxp')->select('select 
                  IF(MONTH(refer_date)>9,YEAR(refer_date)+1,YEAR(refer_date)) + 543 AS year_bud,  
                  SUM(CASE WHEN department = "OPD" THEN 1 ELSE 0 END) AS "opd",
                  SUM(CASE WHEN department = "IPD" THEN 1 ELSE 0 END) AS "ipd",
                  SUM(CASE WHEN with_ambulance = "Y" THEN 1 ELSE 0 END) AS "ambulance",
                  SUM(CASE WHEN refer_hospcode = "10703" THEN 1 ELSE 0 END) AS "r_10703",
                  SUM(CASE WHEN refer_hospcode = "10669" THEN 1 ELSE 0 END) AS "r_10669",
                  SUM(CASE WHEN refer_hospcode = "12269" THEN 1 ELSE 0 END) AS "r_12269",
                  SUM(CASE WHEN refer_hospcode NOT IN ("10669","10703","12269") THEN 1 ELSE 0 END) AS "r_outher"
                  FROM referout
                  WHERE refer_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
                  GROUP BY year_bud
                  ORDER BY year_bud');
      $refer_y = array_column($refer_year,'year_bud');
      $refer_opd_y = array_column($refer_year,'opd');
      $refer_ipd_y = array_column($refer_year,'ipd'); 
      $refer_ambu_y = array_column($refer_year,'ambulance'); 
      $refer_r_10703_y = array_column($refer_year,'r_10703'); 
      $refer_r_10669_y = array_column($refer_year,'r_10669'); 
      $refer_r_12269_y = array_column($refer_year,'r_12269'); 
      $refer_r_outher_y = array_column($refer_year,'r_outher'); 

      $refer_list_opd = DB::connection('hosxp')->select('select
            o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,pmh.cc_persist_disease AS pmh,
            GROUP_CONCAT(c1.`name`) AS "clinic",r.refer_point,o.vstdate,o.vsttime,v.pdx,r.refer_date,
            r.refer_time,r.pre_diagnosis,r.pdx AS pdx_refer,h.`name` AS refer_hos,r.with_ambulance
            FROM referout r 
            LEFT JOIN ovst o ON o.vn=r.vn
            LEFT JOIN vn_stat v ON v.vn=r.vn 
            LEFT JOIN clinicmember c ON c.hn=r.hn
            LEFT JOIN clinic c1 ON c1.clinic=c.clinic
            LEFT JOIN opd_ill_history pmh ON pmh.hn=r.hn
            LEFT JOIN patient p ON p.hn=r.hn 
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND r.department = "OPD"
            GROUP BY r.vn
            ORDER BY r.refer_point,r.refer_date');

      $refer_list_ipd = DB::connection('hosxp')->select('select
            i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,pmh.cc_persist_disease AS pmh,
            GROUP_CONCAT(c1.`name`) AS "clinic",r.refer_point,i.regdate,i.regtime,a.pdx,r.refer_date,
            r.refer_time,r.pre_diagnosis,r.pdx AS pdx_refer,h.`name` AS refer_hos,r.with_ambulance
            FROM referout r 
            LEFT JOIN ipt i ON i.an=r.vn
            LEFT JOIN an_stat a ON a.an=r.vn
            LEFT JOIN clinicmember c ON c.hn=r.hn
            LEFT JOIN clinic c1 ON c1.clinic=c.clinic
            LEFT JOIN opd_ill_history pmh ON pmh.hn=r.hn 
            LEFT JOIN patient p ON p.hn=r.hn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND r.department = "IPD"
            GROUP BY r.vn
            ORDER BY r.refer_point,r.refer_date');

      return view('service_refer.count',compact('budget_year_select','budget_year','refer_m','refer_opd_m','refer_ipd_m',
            'refer_ambu_m','refer_r_10703_m','refer_r_10669_m','refer_r_12269_m','refer_r_outher_m','refer_y','refer_opd_y',
            'refer_ipd_y','refer_ambu_y','refer_r_10703_y','refer_r_10669_y','refer_r_12269_y','refer_r_outher_y',
            'refer_list_opd','refer_list_ipd'));            
}

//Create diag
public function diag(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $refer_diag_month = DB::connection('hosxp')->select('
            select 
            CASE WHEN MONTH(refer_date)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(refer_date)+543,2))
            WHEN MONTH(refer_date)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(refer_date)+543,2))
            WHEN MONTH(refer_date)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(refer_date)+543,2))
            WHEN MONTH(refer_date)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(refer_date)+543,2))
            WHEN MONTH(refer_date)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(refer_date)+543,2))
            WHEN MONTH(refer_date)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(refer_date)+543,2))
            WHEN MONTH(refer_date)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(refer_date)+543,2))
            WHEN MONTH(refer_date)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(refer_date)+543,2))
            WHEN MONTH(refer_date)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(refer_date)+543,2))
            WHEN MONTH(refer_date)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(refer_date)+543,2))
            WHEN MONTH(refer_date)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(refer_date)+543,2))
            WHEN MONTH(refer_date)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(refer_date)+543,2))
            END AS "month",
            SUM(CASE WHEN r.pdx BETWEEN "I210"AND "I219" THEN 1 ELSE 0 END) AS "mi",
            SUM(CASE WHEN r.pdx = "I64" THEN 1 ELSE 0 END) AS "stroke",
            SUM(CASE WHEN r.pdx BETWEEN "S00"AND "S09" THEN 1 ELSE 0 END) AS "head_ingury",
            SUM(CASE WHEN r.pdx IN ("K352","K353","K358","K315","k650") THEN 1 ELSE 0 END) AS "acute_abd",
            SUM(CASE WHEN r.pdx IN ("A419","R572") THEN 1 ELSE 0 END) AS "sepsis",
            SUM(CASE WHEN r.pdx IN ("J128","J159","J188","J189") THEN 1 ELSE 0 END) AS "pneumonia"                  
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,
            IF(r.pdx = "" OR r.pdx IS NULL,v.pdx,r.pdx) AS "pdx"
            FROM referout r
            LEFT JOIN vn_stat v ON v.vn=r.vn
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY r.vn) r              
            GROUP BY MONTH(r.refer_date)
            ORDER BY YEAR(r.refer_date) , MONTH(r.refer_date)');
      $refer_diag_m = array_column($refer_diag_month,'month');
      $refer_diag_mi_m = array_column($refer_diag_month,'mi');
      $refer_diag_stroke_m = array_column($refer_diag_month,'stroke'); 
      $refer_diag_head_ingury_m = array_column($refer_diag_month,'head_ingury');
      $refer_diag_acute_abd_m = array_column($refer_diag_month,'acute_abd'); 
      $refer_diag_sepsis_m = array_column($refer_diag_month,'sepsis'); 
      $refer_diag_pneumonia_m = array_column($refer_diag_month,'pneumonia'); 
      $refer_diag_pneumonia_tube_m = array_column($refer_diag_month,'pneumonia_tube'); 

      $refer_diag_year = DB::connection('hosxp')->select('select 
            IF(MONTH(r.refer_date)>9,YEAR(r.refer_date)+1,YEAR(r.refer_date)) + 543 AS year_bud,  
            SUM(CASE WHEN r.pdx BETWEEN "I210"AND "I219" THEN 1 ELSE 0 END) AS "mi",
            SUM(CASE WHEN r.pdx = "I64" THEN 1 ELSE 0 END) AS "stroke",
            SUM(CASE WHEN r.pdx BETWEEN "S00"AND "S09" THEN 1 ELSE 0 END) AS "head_ingury",
            SUM(CASE WHEN r.pdx IN ("K352","K353","K358","K315","k650") THEN 1 ELSE 0 END) AS "acute_abd",
            SUM(CASE WHEN r.pdx IN ("A419","R651","R572") THEN 1 ELSE 0 END) AS "sepsis",
            SUM(CASE WHEN r.pdx IN ("J128","J159","J188","J189") THEN 1 ELSE 0 END) AS "pneumonia"                  
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,
            IF(r.pdx = "" OR r.pdx IS NULL,v.pdx,r.pdx) AS "pdx"
            FROM referout r
            LEFT JOIN vn_stat v ON v.vn=r.vn
            WHERE r.refer_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            GROUP BY r.vn) r 
            GROUP BY year_bud
            ORDER BY year_bud');
      $refer_diag_y = array_column($refer_diag_year,'year_bud');
      $refer_diag_mi_y = array_column($refer_diag_year,'mi');
      $refer_diag_stroke_y = array_column($refer_diag_year,'stroke'); 
      $refer_diag_head_ingury_y = array_column($refer_diag_year,'head_ingury');
      $refer_diag_acute_abd_y = array_column($refer_diag_year,'acute_abd'); 
      $refer_diag_sepsis_y = array_column($refer_diag_year,'sepsis'); 
      $refer_diag_pneumonia_y = array_column($refer_diag_year,'pneumonia'); 
      $refer_diag_pneumonia_tube_y = array_column($refer_diag_year,'pneumonia_tube'); 

      return view('service_refer.diag',compact('budget_year_select','budget_year',
      'refer_diag_m','refer_diag_mi_m','refer_diag_stroke_m','refer_diag_head_ingury_m',
      'refer_diag_acute_abd_m','refer_diag_sepsis_m','refer_diag_pneumonia_m','refer_diag_y',
      'refer_diag_mi_y','refer_diag_stroke_y','refer_diag_head_ingury_y','refer_diag_acute_abd_y',
      'refer_diag_sepsis_y','refer_diag_pneumonia_y','refer_diag_pneumonia_tube_m','refer_diag_pneumonia_tube_y'));            
}

//Create diag_top
public function diag_top(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_top_opd = DB::connection('hosxp')->select('select 
            CONCAT("[",pdx,"] " ,name) AS name,count(*) AS sum
            FROM (SELECT r.hn,r.department,r.refer_point,r.pdx,i.`name`
            FROM referout r 
            LEFT JOIN icd101 i ON i.code=r.pdx										
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (r.pdx <>"" AND r.pdx IS NOT NULL)
            ORDER BY r.department,r.refer_date) AS a
            WHERE a.department = "OPD" AND a.refer_point = "OPD"
            GROUP BY pdx  
            ORDER BY sum desc limit 20');
      $diag_top_opd_name = array_column($diag_top_opd,'name');
      $diag_top_opd_sum = array_column($diag_top_opd,'sum');

      $diag_top_er = DB::connection('hosxp')->select('select 
            CONCAT("[",pdx,"] " ,name) AS name,count(*) AS sum
            FROM (SELECT r.hn,r.department,r.refer_point,r.pdx,i.`name`
            FROM referout r 
            LEFT JOIN icd101 i ON i.code=r.pdx										
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (r.pdx <>"" AND r.pdx IS NOT NULL)
            ORDER BY r.department,r.refer_date) AS a
            WHERE a.department = "OPD" AND a.refer_point = "ER"
            GROUP BY pdx  
            ORDER BY sum desc limit 20');
      $diag_top_er_name = array_column($diag_top_er,'name');
      $diag_top_er_sum = array_column($diag_top_er,'sum');

      $diag_top_ipd = DB::connection('hosxp')->select('select 
            CONCAT("[",pdx,"] " ,name) AS name,count(*) AS sum
            FROM (SELECT r.hn,r.department,r.refer_point,r.pdx,i.`name`
            FROM referout r 
            LEFT JOIN icd101 i ON i.code=r.pdx										
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (r.pdx <>"" AND r.pdx IS NOT NULL)
            ORDER BY r.department,r.refer_date) AS a
            WHERE a.department = "IPD"
            GROUP BY pdx  
            ORDER BY sum desc limit 20');
      $diag_top_ipd_name = array_column($diag_top_ipd,'name');
      $diag_top_ipd_sum = array_column($diag_top_ipd,'sum');

      return view('service_refer.diag_top',compact('budget_year_select','budget_year','diag_top_opd_name','diag_top_opd_sum',
      'diag_top_er_name','diag_top_er_sum','diag_top_ipd_name','diag_top_ipd_sum'));         
}

//Create after_admit4
public function after_admit4(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
      
      $after_admit4 = DB::connection('hosxp')->select('select 
            i.an,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.regdate,i.regtime, 
            i.dchdate,i.dchtime,r.refer_date,r.refer_time,
            a.pdx AS admit_pdx,r.pdx AS refer_pdx,h.`name` AS refer_hos,a.admit_hour    
            FROM ipt i    
            LEFT JOIN patient p ON p.hn=i.hn   
            LEFT JOIN an_stat a ON i.an=a.an
            LEFT JOIN referout r ON i.an=r.vn 
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE i.dchtype=04 AND a.admit_hour <=4   
            AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            GROUP BY i.an ');  

      $after_admit4_month = DB::connection('hosxp')->select('select 
            CASE WHEN MONTH(a.dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(a.dchdate)+543,2))
            END AS "month",COUNT(DISTINCT a.an) AS total
            FROM (SELECT i.an,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.regdate,i.regtime, 
            i.dchdate,i.dchtime,r.refer_date,r.refer_time,
            a.pdx AS admit_pdx,r.pdx AS refer_pdx,h.`name` AS refer_hos,a.admit_hour    
            FROM ipt i    
            LEFT JOIN patient p ON p.hn=i.hn   
            LEFT JOIN an_stat a ON i.an=a.an
            LEFT JOIN referout r ON i.an=r.vn 
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE i.dchtype=04 AND a.admit_hour <=4   
            AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY i.an ) AS a
            GROUP BY MONTH(a.dchdate)
            ORDER BY YEAR(a.dchdate),MONTH(a.dchdate)');
      $after_admit4_m = array_column($after_admit4_month,'month');
      $after_admit4_total_m = array_column($after_admit4_month,'total'); 

      $after_admit4_year = DB::connection('hosxp')->select('
            SELECT
            IF(MONTH(a.dchdate)>9,YEAR(a.dchdate)+1,YEAR(a.dchdate)) + 543 AS year_bud,COUNT(DISTINCT a.an) AS total
            FROM (SELECT i.an,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.regdate,i.regtime, 
            i.dchdate,i.dchtime,r.refer_date,r.refer_time,
            a.pdx AS admit_pdx,r.pdx AS refer_pdx,h.`name` AS refer_hos,a.admit_hour    
            FROM ipt i    
            LEFT JOIN patient p ON p.hn=i.hn   
            LEFT JOIN an_stat a ON i.an=a.an
            LEFT JOIN referout r ON i.an=r.vn 
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE i.dchtype=04 AND a.admit_hour <=4   
            AND i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" 
            GROUP BY i.an ) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $after_admit4_y = array_column($after_admit4_year,'year_bud');
      $after_admit4_total_y = array_column($after_admit4_year,'total');   

      return view('service_refer.after_admit4',compact('budget_year_select','budget_year','after_admit4',
                  'after_admit4_m','after_admit4_total_m','after_admit4_y','after_admit4_total_y'));  

}

//Create after_admit24
public function after_admit24(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
      
      $after_admit24 = DB::connection('hosxp')->select('select 
            i.an,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.regdate,i.regtime, 
            i.dchdate,i.dchtime,r.refer_date,r.refer_time,
            a.pdx AS admit_pdx,r.pdx AS refer_pdx,h.`name` AS refer_hos,a.admit_hour    
            FROM ipt i    
            LEFT JOIN patient p ON p.hn=i.hn   
            LEFT JOIN an_stat a ON i.an=a.an
            LEFT JOIN referout r ON i.an=r.vn 
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE i.dchtype=04 AND a.admit_hour <=24   
            AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            GROUP BY i.an ');  

      $after_admit24_month = DB::connection('hosxp')->select('select 
            CASE WHEN MONTH(a.dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(a.dchdate)+543,2))
            WHEN MONTH(a.dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(a.dchdate)+543,2))
            END AS "month",COUNT(DISTINCT a.an) AS total
            FROM (SELECT i.an,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.regdate,i.regtime, 
            i.dchdate,i.dchtime,r.refer_date,r.refer_time,
            a.pdx AS admit_pdx,r.pdx AS refer_pdx,h.`name` AS refer_hos,a.admit_hour    
            FROM ipt i    
            LEFT JOIN patient p ON p.hn=i.hn   
            LEFT JOIN an_stat a ON i.an=a.an
            LEFT JOIN referout r ON i.an=r.vn 
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE i.dchtype=04 AND a.admit_hour <=24   
            AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY i.an ) AS a
            GROUP BY MONTH(a.dchdate)
            ORDER BY YEAR(a.dchdate),MONTH(a.dchdate)');
      $after_admit24_m = array_column($after_admit24_month,'month');
      $after_admit24_total_m = array_column($after_admit24_month,'total'); 

      $after_admit24_year = DB::connection('hosxp')->select('
            SELECT
            IF(MONTH(a.dchdate)>9,YEAR(a.dchdate)+1,YEAR(a.dchdate)) + 543 AS year_bud,COUNT(DISTINCT a.an) AS total
            FROM (SELECT i.an,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.regdate,i.regtime, 
            i.dchdate,i.dchtime,r.refer_date,r.refer_time,
            a.pdx AS admit_pdx,r.pdx AS refer_pdx,h.`name` AS refer_hos,a.admit_hour    
            FROM ipt i    
            LEFT JOIN patient p ON p.hn=i.hn   
            LEFT JOIN an_stat a ON i.an=a.an
            LEFT JOIN referout r ON i.an=r.vn 
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE i.dchtype=04 AND a.admit_hour <=24   
            AND i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" 
            GROUP BY i.an ) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $after_admit24_y = array_column($after_admit24_year,'year_bud');
      $after_admit24_total_y = array_column($after_admit24_year,'total');   

      return view('service_refer.after_admit24',compact('budget_year_select','budget_year','after_admit24',
                  'after_admit24_m','after_admit24_total_m','after_admit24_y','after_admit24_total_y'));  

}

//Create not_complete
public function not_complete(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}       
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
      
      $not_complete = DB::connection('hosxp')->select('
            SELECT 
            r.refer_date,r.hn,a.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname
            ,r.department,r.refer_point,IFNULL(v.pdx,a.pdx) AS pdx,r.pdx AS pdx_refer
            ,h.`name` AS refer_hos
            FROM referout r 
            LEFT JOIN patient p ON p.hn=r.hn
            LEFT JOIN vn_stat v ON v.vn=r.vn
            LEFT JOIN an_stat a ON a.an=r.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (r.pdx ="" OR r.pdx IS NULL OR r.refer_hospcode ="" OR r.refer_hospcode IS NULL
            OR r.refer_point ="" OR r.refer_point IS NULL)
            ORDER BY r.vn');
              
      return view('service_refer.not_complete',compact('budget_year_select','budget_year','not_complete'));  

}

}
