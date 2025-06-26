<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service_PhysicController extends Controller
{
//Check Login
      public function __construct()
      {
          $this->middleware('auth');
      }


//CCreate index
      public function index()
      {                
            return view('service_physic.index');            
      }

//CCreate count
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
    
      $physic_opd_month = DB::connection('hosxp')->select('
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
		INNER JOIN physic_list pt ON pt.vn=o.vn		
		LEFT JOIN opitemrece o1 ON o1.vn=o.vn AND o1.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_type_id IN ("02","20"))		
		LEFT JOIN visit_pttype vp ON vp.vn=o.vn
		LEFT JOIN pttype p ON p.pttype=vp.pttype
		WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY o.vn ) AS a						
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $physic_m = array_column($physic_opd_month,'month');
      $physic_opd_hn_m = array_column($physic_opd_month,'hn');
      $physic_opd_visit_m = array_column($physic_opd_month,'visit');

      $physic_ipd_month = DB::connection('hosxp')->select('
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
            END AS "month",COUNT(DISTINCT an) as visit ,
		SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
            SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN ("14","H1") THEN 1 ELSE 0 END) AS "ofc",
            SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
            SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
            SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
            SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
            SUM(CASE WHEN (paidst IN ("01","03") OR hipdata_code IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"			
            FROM  (SELECT DATE(pt.physic_plan_request_date) AS vstdate,
            pt.an,pt.hn,ip.pttype,p.hipdata_code,p.paidst 
            FROM physic_plan pt
            LEFT JOIN ipt_pttype ip ON ip.an=pt.an
            LEFT JOIN pttype p ON p.pttype=ip.pttype
            WHERE DATE(pt.physic_plan_request_date) BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY an) AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)'); 
      $physic_ipd_visit_m = array_column($physic_ipd_month,'visit');     

      $physic_opd_year = DB::connection('hosxp')->select('select 
                  IF(MONTH(a.vstdate)>9,YEAR(a.vstdate)+1,YEAR(a.vstdate)) + 543 AS year_bud,
                  count(DISTINCT hn) as hn ,count(DISTINCT vn) as visit  
                  FROM
                  (SELECT vn,hn,vstdate FROM physic_main  WHERE vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
                  UNION SELECT p.vn,o.hn,o.vstdate FROM ovst o ,physic_list p
                  WHERE p.vn=o.vn AND o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" ) AS a
                  GROUP BY year_bud');
      $physic_y = array_column($physic_opd_year,'year_bud');
      $physic_opd_hn_y = array_column($physic_opd_year,'hn');
      $physic_opd_visit_y = array_column($physic_opd_year,'visit');

      $physic_ipd_year = DB::connection('hosxp')->select('select 
                  IF(MONTH(a.vstdate)>9,YEAR(a.vstdate)+1,YEAR(a.vstdate)) + 543 AS year_bud,
                  count(DISTINCT hn) as hn, count(DISTINCT an) as visit  
                  FROM
                  (SELECT an,hn,vstdate FROM physic_main_ipd  WHERE vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" 
                  UNION SELECT an,hn,DATE(physic_plan_request_date) AS vstdate FROM physic_plan 
                  WHERE DATE(physic_plan_request_date) BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" GROUP BY an) AS a
                  GROUP BY year_bud');
      $physic_ipd_hn_y = array_column($physic_ipd_year,'hn');  
      $physic_ipd_visit_y = array_column($physic_ipd_year,'visit');      

      return view('service_physic.count',compact('budget_year_select','budget_year','physic_y','physic_opd_hn_y','physic_opd_visit_y','physic_ipd_visit_y',
                  'physic_ipd_hn_y','physic_m','physic_opd_visit_m','physic_opd_hn_m','physic_ipd_visit_m','physic_opd_month','physic_ipd_month'));            
}

//Create top30_diag
public function diag_top30(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_top30 = DB::connection('hosxp')->select('select 
                  CONCAT("[",v.pdx,"] " ,i.name) AS name,count(v.pdx) AS sum , 
                  sum(CASE WHEN v.sex=1 THEN 1 ELSE 0 END) AS male,   
                  sum(CASE WHEN v.sex=2 THEN 1 ELSE 0 END) AS female   
                  FROM vn_stat v   
                  LEFT OUTER JOIN icd101 i on i.code=v.pdx 
                  LEFT OUTER JOIN physic_main p ON p.vn=v.vn
                  LEFT OUTER JOIN physic_list p1 ON p1.vn=v.vn
                  WHERE v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                  AND (p.vn<>"" OR p.vn IS NOT NULL OR p1.vn<>"" OR p1.vn IS NOT NULL)            
                  GROUP BY v.pdx,i.name  
                  ORDER BY sum DESC LIMIT 30');
                  
      return view('service_physic.diag_top30',compact('budget_year_select','budget_year','diag_top30'));          
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
    
      $physic_diag_month = DB::connection('hosxp')->select(' select 
            CASE WHEN MONTH(vstdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(vstdate)+543,2))
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
            END AS "month",
            SUM(CASE WHEN pdx LIKE "M47%"THEN 1 ELSE 0 END) AS "m47",  
            SUM(CASE WHEN pdx LIKE "M48%"THEN 1 ELSE 0 END) AS "m48",
            SUM(CASE WHEN pdx LIKE "M51%"THEN 1 ELSE 0 END) AS "m51", 
            SUM(CASE WHEN pdx LIKE "M54%"THEN 1 ELSE 0 END) AS "m54"                 
            FROM (SELECT  v.vn,v.vstdate,p.vn AS p_vn,p1.vn AS p1_vn,v.pdx
            FROM vn_stat v   
            LEFT OUTER JOIN icd101 i on i.code=v.pdx 
            LEFT OUTER JOIN physic_main p ON p.vn=v.vn
            LEFT OUTER JOIN physic_list p1 ON p1.vn=v.vn
            WHERE v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (p.vn<>"" OR p.vn IS NOT NULL OR p1.vn<>"" OR p1.vn IS NOT NULL) 
            GROUP BY v.vn ,v.pdx) a              
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate),MONTH(vstdate)');
      $physic_diag_m = array_column($physic_diag_month,'month');
      $physic_diag_m47_m = array_column($physic_diag_month,'m47');
      $physic_diag_m48_m = array_column($physic_diag_month,'m48');
      $physic_diag_m51_m = array_column($physic_diag_month,'m51');
      $physic_diag_m54_m = array_column($physic_diag_month,'m54');

      $physic_diag_year = DB::connection('hosxp')->select('select 
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            SUM(CASE WHEN pdx LIKE "M47%"THEN 1 ELSE 0 END) AS "m47",  
            SUM(CASE WHEN pdx LIKE "M48%"THEN 1 ELSE 0 END) AS "m48", 
            SUM(CASE WHEN pdx LIKE "M51%"THEN 1 ELSE 0 END) AS "m51",
            SUM(CASE WHEN pdx LIKE "M54%"THEN 1 ELSE 0 END) AS "m54"          
            FROM (SELECT  v.vn,v.vstdate,p.vn AS p_vn,p1.vn AS p1_vn,v.pdx
            FROM vn_stat v   
            LEFT OUTER JOIN icd101 i on i.code=v.pdx 
            LEFT OUTER JOIN physic_main p ON p.vn=v.vn
            LEFT OUTER JOIN physic_list p1 ON p1.vn=v.vn
            WHERE v.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"   
            AND (p.vn<>"" OR p.vn IS NOT NULL OR p1.vn<>"" OR p1.vn IS NOT NULL) 
            GROUP BY v.vn ,v.pdx) a              
            GROUP BY year_bud
            ORDER BY year_bud');
      $physic_diag_y = array_column($physic_diag_year,'year_bud');
      $physic_diag_m47_y = array_column($physic_diag_year,'m47');
      $physic_diag_m48_y = array_column($physic_diag_year,'m48');
      $physic_diag_m51_y = array_column($physic_diag_year,'m51');
      $physic_diag_m54_y = array_column($physic_diag_year,'m54');

      $physic_diag_month_hn = DB::connection('hosxp')->select(' select 
            CASE WHEN MONTH(vstdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(vstdate)+543,2))
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
            END AS "month",
            SUM(CASE WHEN pdx LIKE "M47%"THEN 1 ELSE 0 END) AS "m47",  
            SUM(CASE WHEN pdx LIKE "M48%"THEN 1 ELSE 0 END) AS "m48",
            SUM(CASE WHEN pdx LIKE "M51%"THEN 1 ELSE 0 END) AS "m51", 
            SUM(CASE WHEN pdx LIKE "M54%"THEN 1 ELSE 0 END) AS "m54"                 
            FROM (SELECT v.vn,v.vstdate,p.vn AS p_vn,p1.vn AS p1_vn,v.pdx
            FROM vn_stat v   
            LEFT OUTER JOIN icd101 i on i.code=v.pdx 
            LEFT OUTER JOIN physic_main p ON p.vn=v.vn
            LEFT OUTER JOIN physic_list p1 ON p1.vn=v.vn
            WHERE v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (p.vn<>"" OR p.vn IS NOT NULL OR p1.vn<>"" OR p1.vn IS NOT NULL) 
            GROUP BY v.hn ,v.pdx) a              
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate),MONTH(vstdate)');
      $physic_diag_m_hn = array_column($physic_diag_month_hn,'month');
      $physic_diag_m47_m_hn = array_column($physic_diag_month_hn,'m47');
      $physic_diag_m48_m_hn = array_column($physic_diag_month_hn,'m48');
      $physic_diag_m51_m_hn = array_column($physic_diag_month_hn,'m51');
      $physic_diag_m54_m_hn = array_column($physic_diag_month_hn,'m54');

      $physic_diag_year_hn = DB::connection('hosxp')->select('select 
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            SUM(CASE WHEN pdx LIKE "M47%"THEN 1 ELSE 0 END) AS "m47",  
            SUM(CASE WHEN pdx LIKE "M48%"THEN 1 ELSE 0 END) AS "m48", 
            SUM(CASE WHEN pdx LIKE "M51%"THEN 1 ELSE 0 END) AS "m51",
            SUM(CASE WHEN pdx LIKE "M54%"THEN 1 ELSE 0 END) AS "m54"          
            FROM (SELECT v.vn,v.vstdate,p.vn AS p_vn,p1.vn AS p1_vn,v.pdx
            FROM vn_stat v   
            LEFT OUTER JOIN icd101 i on i.code=v.pdx 
            LEFT OUTER JOIN physic_main p ON p.vn=v.vn
            LEFT OUTER JOIN physic_list p1 ON p1.vn=v.vn
            WHERE v.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"   
            AND (p.vn<>"" OR p.vn IS NOT NULL OR p1.vn<>"" OR p1.vn IS NOT NULL) 
            GROUP BY v.hn ,v.pdx) a              
            GROUP BY year_bud
            ORDER BY year_bud');
      $physic_diag_y_hn = array_column($physic_diag_year_hn,'year_bud');
      $physic_diag_m47_y_hn = array_column($physic_diag_year_hn,'m47');
      $physic_diag_m48_y_hn = array_column($physic_diag_year_hn,'m48');
      $physic_diag_m51_y_hn = array_column($physic_diag_year_hn,'m51');
      $physic_diag_m54_y_hn = array_column($physic_diag_year_hn,'m54');

      return view('service_physic.diag',compact('budget_year_select','budget_year','physic_diag_m',
            'physic_diag_m47_m','physic_diag_m48_m','physic_diag_m51_m','physic_diag_m54_m','physic_diag_y',
            'physic_diag_m47_y','physic_diag_m48_y','physic_diag_m51_y','physic_diag_m54_y','physic_diag_m_hn',
            'physic_diag_m47_m_hn','physic_diag_m48_m_hn','physic_diag_m51_m_hn','physic_diag_m54_m_hn','physic_diag_y_hn',
            'physic_diag_m47_y_hn','physic_diag_m48_y_hn','physic_diag_m51_y_hn','physic_diag_m54_y_hn'));            
}

//Create physic_appointment
public function physic_appointment(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

    $physic_appointment = DB::connection('hosxp')->select('
            SELECT o.vstdate,o.hn,concat( p.pname, p.fname, "  ", p.lname ) AS ptname,o.note,
            o.nextdate,o.nexttime,o.nexttime_end,c.NAME AS clinic_name,d.NAME AS doctor_name,
            k.department,IF(o.oapp_status_id=2,"มารับบริการแล้ว","ขาดนัด") AS oapp_status,
            p.mobile_phone_number,o.app_cause,o3.NAME AS app_user_name,cast(concat(p.addrpart," หมู่ ",p.moopart,
            IF( length( p.road )> 0, concat( "ถนน", p.road ), "" )," ",t.full_name) AS CHAR ( 200 )) AS addr_name
            FROM oapp o
            LEFT OUTER JOIN patient p ON p.hn = o.hn
            LEFT OUTER JOIN clinic c ON c.clinic = o.clinic
            LEFT OUTER JOIN thaiaddress t ON t.chwpart = p.chwpart
            AND t.amppart = p.amppart AND t.tmbpart = p.tmbpart AND t.codetype = "3"
            LEFT OUTER JOIN doctor d ON d.CODE = o.doctor
            LEFT OUTER JOIN kskdepartment k ON k.depcode = o.depcode
            LEFT OUTER JOIN opduser o3 ON o3.loginname = o.app_user
            WHERE o.nextdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND o.clinic = "016" AND (( o.oapp_status_id < 4 ) OR o.oapp_status_id IS NULL )
            ORDER BY o.nextdate,o.nexttime');

      return view('service_physic.physic_appointment',compact('start_date','end_date','physic_appointment'));
}

}
