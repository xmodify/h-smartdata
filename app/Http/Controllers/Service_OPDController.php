<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Service_OPDController extends Controller
{
//Check Login
      public function __construct()
{
      $this->middleware('auth');
}

//Create index
      public function index()
{
      return view('service_opd.index');          
}

//Create count
public function count(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->table('budget_year')->select('LEAVE_YEAR_ID', 'LEAVE_YEAR_NAME')->orderByDesc('LEAVE_YEAR_ID')->limit(7)->get();
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year ?? $budget_year_last;
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID', $budget_year - 4)->value('DATE_BEGIN');
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID', $budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID', $budget_year)->value('DATE_END');
   
      $visit_month = DB::connection('hosxp')->select('
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
            END AS "month",COUNT(vn) AS "visit",COUNT(DISTINCT hn) AS "hn",
            SUM(CASE WHEN diagtype ="OP" THEN 1 ELSE 0 END) AS "visit_op",
            SUM(CASE WHEN diagtype ="PP" THEN 1 ELSE 0 END) AS "visit_pp",SUM(income) AS "income",
            SUM(inc12) AS "inc_drug",SUM(inc03) AS "inc_lab",
            SUM(CASE WHEN hipdata_code IN ("UCS","DIS") AND paidst NOT IN ("01","03") THEN 1 ELSE 0 END) AS "ucs",
            SUM(CASE WHEN hipdata_code IN ("UCS","DIS") AND paidst NOT IN ("01","03") THEN income ELSE 0 END) AS "ucs_income",            
            SUM(CASE WHEN hipdata_code IN ("UCS","DIS") AND paidst NOT IN ("01","03") THEN inc12 ELSE 0 END) AS "ucs_inc_drug",
            SUM(CASE WHEN hipdata_code IN ("UCS","DIS") AND paidst NOT IN ("01","03") THEN inc03 ELSE 0 END) AS "ucs_inc_lab",
            SUM(CASE WHEN hipdata_code IN ("OFC","BKK","BMT") AND paidst NOT IN ("01","03") THEN 1 ELSE 0 END) AS "ofc",
            SUM(CASE WHEN hipdata_code IN ("OFC","BKK","BMT") AND paidst NOT IN ("01","03") THEN income ELSE 0 END) AS "ofc_income",
            SUM(CASE WHEN hipdata_code IN ("OFC","BKK","BMT") AND paidst NOT IN ("01","03") THEN inc12 ELSE 0 END) AS "ofc_inc_drug",
            SUM(CASE WHEN hipdata_code IN ("OFC","BKK","BMT") AND paidst NOT IN ("01","03") THEN inc03 ELSE 0 END) AS "ofc_inc_lab",            
            SUM(CASE WHEN hipdata_code IN ("SSS","SSI") AND paidst NOT IN ("01","03") THEN 1 ELSE 0 END) AS "sss",
            SUM(CASE WHEN hipdata_code IN ("SSS","SSI") AND paidst NOT IN ("01","03") THEN income ELSE 0 END) AS "sss_income",
            SUM(CASE WHEN hipdata_code IN ("SSS","SSI") AND paidst NOT IN ("01","03") THEN inc12 ELSE 0 END) AS "sss_inc_drug",
            SUM(CASE WHEN hipdata_code IN ("SSS","SSI") AND paidst NOT IN ("01","03") THEN inc03 ELSE 0 END) AS "sss_inc_lab",            
            SUM(CASE WHEN hipdata_code IN ("LGO") AND paidst NOT IN ("01","03") THEN 1 ELSE 0 END) AS "lgo",
            SUM(CASE WHEN hipdata_code IN ("LGO") AND paidst NOT IN ("01","03") THEN income ELSE 0 END) AS "lgo_income",
            SUM(CASE WHEN hipdata_code IN ("LGO") AND paidst NOT IN ("01","03") THEN inc12 ELSE 0 END) AS "lgo_inc_drug",
            SUM(CASE WHEN hipdata_code IN ("LGO") AND paidst NOT IN ("01","03") THEN inc03 ELSE 0 END) AS "lgo_inc_lab",            
            SUM(CASE WHEN hipdata_code IN ("NRD","NRH") AND paidst NOT IN ("01","03") THEN 1 ELSE 0 END) AS "fss",
            SUM(CASE WHEN hipdata_code IN ("NRD","NRH") AND paidst NOT IN ("01","03") THEN income ELSE 0 END) AS "fss_income",
            SUM(CASE WHEN hipdata_code IN ("NRD","NRH") AND paidst NOT IN ("01","03") THEN inc12 ELSE 0 END) AS "fss_inc_drug",
            SUM(CASE WHEN hipdata_code IN ("NRD","NRH") AND paidst NOT IN ("01","03") THEN inc03 ELSE 0 END) AS "fss_inc_lab",            
            SUM(CASE WHEN hipdata_code IN ("STP") AND paidst NOT IN ("01","03") THEN 1 ELSE 0 END) AS "stp",   
            SUM(CASE WHEN hipdata_code IN ("STP") AND paidst NOT IN ("01","03") THEN income ELSE 0 END) AS "stp_income",
            SUM(CASE WHEN hipdata_code IN ("STP") AND paidst NOT IN ("01","03") THEN inc12 ELSE 0 END) AS "stp_inc_drug", 
            SUM(CASE WHEN hipdata_code IN ("STP") AND paidst NOT IN ("01","03") THEN inc03 ELSE 0 END) AS "stp_inc_lab",
            SUM(CASE WHEN (paidst IN ("01","03") OR hipdata_code IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay",
            SUM(CASE WHEN (paidst IN ("01","03") OR hipdata_code IN ("A1","A9")) THEN income ELSE 0 END) AS "pay_income",
            SUM(CASE WHEN (paidst IN ("01","03") OR hipdata_code IN ("A1","A9")) THEN inc12 ELSE 0 END) AS "pay_inc_drug",
            SUM(CASE WHEN (paidst IN ("01","03") OR hipdata_code IN ("A1","A9")) THEN inc03 ELSE 0 END) AS "pay_inc_lab"            
            FROM (SELECT v.vstdate,v.vn,v.hn,v.pttype,p.hipdata_code,p.paidst,v.income,v.inc03,v.inc12 ,v.pdx,
            IF(i.icd10 IS NULL,"OP","PP") AS diagtype
            FROM vn_stat v
            LEFT JOIN pttype p ON p.pttype=v.pttype
            LEFT JOIN htp_report.lookup_icd10 i ON i.icd10=v.pdx AND i.pp="Y"
            WHERE v.vstdate BETWEEN ? AND ?) AS a									
            GROUP BY YEAR(vstdate) , MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)',[$start_date,$end_date]);
      $month = array_column($visit_month,'month');
      $visit = array_column($visit_month,'visit');
      $hn = array_column($visit_month,'hn');
      $visit_op = array_column($visit_month,'visit_op');
      $visit_pp = array_column($visit_month,'visit_pp');
      $ucs = array_column($visit_month,'ucs');
      $ucs_inc_lab = array_column($visit_month,'ucs_inc_lab');         
      $ucs_inc_drug = array_column($visit_month,'ucs_inc_drug'); 
      $ofc = array_column($visit_month,'ofc');
      $ofc_inc_lab = array_column($visit_month,'ofc_inc_lab');         
      $ofc_inc_drug = array_column($visit_month,'ofc_inc_drug'); 
      $sss = array_column($visit_month,'sss');
      $sss_inc_lab = array_column($visit_month,'sss_inc_lab');         
      $sss_inc_drug = array_column($visit_month,'sss_inc_drug'); 
      $lgo = array_column($visit_month,'lgo');
      $lgo_inc_lab = array_column($visit_month,'lgo_inc_lab');         
      $lgo_inc_drug = array_column($visit_month,'lgo_inc_drug'); 
      $fss = array_column($visit_month,'fss');
      $fss_inc_lab = array_column($visit_month,'fss_inc_lab');         
      $fss_inc_drug = array_column($visit_month,'fss_inc_drug'); 
      $stp = array_column($visit_month,'stp');
      $stp_inc_lab = array_column($visit_month,'stp_inc_lab');         
      $stp_inc_drug = array_column($visit_month,'stp_inc_drug'); 
      $pay = array_column($visit_month,'pay');
      $pay_inc_lab = array_column($visit_month,'pay_inc_lab');         
      $pay_inc_drug = array_column($visit_month,'pay_inc_drug');   

      return view('service_opd.count',compact('budget_year_select','budget_year','visit_month','month','hn','visit','visit_op','visit_pp','ucs',
            'ucs_inc_lab','ucs_inc_drug','ofc','ofc_inc_lab','ofc_inc_drug','sss','sss_inc_lab','sss_inc_drug','lgo','lgo_inc_lab',
            'lgo_inc_drug','fss','fss_inc_lab','fss_inc_drug','stp','stp_inc_lab','stp_inc_drug','pay','pay_inc_lab','pay_inc_drug',));        
}

//Create count_spclty
public function count_spclty(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->table('budget_year')->select('LEAVE_YEAR_ID', 'LEAVE_YEAR_NAME')->orderByDesc('LEAVE_YEAR_ID')->limit(7)->get();
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year ?? $budget_year_last;
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID', $budget_year - 4)->value('DATE_BEGIN');
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID', $budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID', $budget_year)->value('DATE_END');
    
      $month_surgeon = DB::connection('hosxp')->select('select 
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
            COUNT(DISTINCT hn) AS hn,COUNT(DISTINCT vn) AS visit,
            SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
            SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
            SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
            SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
            SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
            SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
            SUM(CASE WHEN hipdata_code in ("A1","A9") OR pttype like "C%" OR pttype like "E%"  
		    OR pttype like "P%" OR pttype IN ("A1","Z3","G1") THEN 1 ELSE 0 END) AS "pay"
            FROM (SELECT v.vstdate,v.hn,v.vn,v.pttype,p.hipdata_code 
            FROM vn_stat v
            LEFT JOIN pttype p ON p.pttype=v.pttype
            WHERE v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND v.dx_doctor IN (SELECT `code` FROM doctor 
		WHERE doctor_department_id IN ("11","12","13","14","15","16","17","39","40","41"))) AS a									
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $m_surgeon = array_column($month_surgeon,'month');
      $hn_m_surgeon = array_column($month_surgeon,'hn');
      $visit_m_surgeon = array_column($month_surgeon,'visit');    

      return view('service_opd.count_spclty',compact('budget_year_select','budget_year','month_surgeon','m_surgeon','hn_m_surgeon',
            'visit_m_surgeon',));          
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
            concat(a.name1," [",a.id,"]")as name,
            ifnull(d.male,0) as male,ifnull(d.female,0) as female,ifnull(d.amount,0) as sum  
            from rpt_504_name a 
            left join (select b.id,
            sum(case when v.sex=1 THEN 1 ELSE 0 END) as male,   
            sum(case when v.sex=2 THEN 1 ELSE 0 END) as female ,
            count(b.id) as amount 
            from rpt_504_code b,vn_stat v 
            where v.pdx between b.code1 and b.code2  
            and v.vstdate between "'.$start_date.'" AND "'.$end_date.'" 
            group by b.id) d on d.id=a.id 
            order by sum desc ');
     
      return view('service_opd.diag_504',compact('budget_year_select','budget_year','diag_504'));          
}

//Create diag_506
public function diag_506(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_506 = DB::connection('hosxp')->select('select 
            n.name as name , 
            sum(case when p.sex=1 THEN 1 ELSE 0 END) as male,   
            sum(case when p.sex=2 THEN 1 ELSE 0 END) as female,  
            COUNT(DISTINCT s.vn) as sum
            from surveil_member s   
            LEFT JOIN patient p on p.hn=s.hn  
            LEFT JOIN name506 n on n.code=s.code506 
            where s.report_date between "'.$start_date.'" AND "'.$end_date.'" 
            GROUP BY s.code506 ORDER BY sum DESC ');     

      return view('service_opd.diag_506',compact('budget_year_select','budget_year','diag_506'));          
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
            concat("[",v.pdx,"] " ,i.name) as name,count(v.pdx) as sum , 
            sum(case when v.sex=1 THEN 1 ELSE 0 END) as male,   
            sum(case when v.sex=2 THEN 1 ELSE 0 END) as female,
            sum(v.inc03) as inc_lab,
		sum(v.inc12) as inc_drug   
            FROM vn_stat v   
            left outer join icd101 i on i.code=v.pdx 
            where v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            and v.pdx<>"" AND v.pdx is not null and v.pdx not like "z%" and v.pdx NOT IN ("u119")
            group by v.pdx,i.name  
            order by sum desc limit 30');

      $diag_top30_z = DB::connection('hosxp')->select('select 
            concat("[",v.pdx,"] " ,i.name) as name,count(v.pdx) as sum , 
            sum(case when v.sex=1 THEN 1 ELSE 0 END) as male,   
            sum(case when v.sex=2 THEN 1 ELSE 0 END) as female,
            sum(v.inc03) as inc_lab,
		sum(v.inc12) as inc_drug   
            FROM vn_stat v   
            left outer join icd101 i on i.code=v.pdx 
            where v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            and v.pdx<>"" AND v.pdx is not null and (v.pdx like "z%" or v.pdx IN ("u119"))
            group by v.pdx,i.name  
            order by sum desc limit 30');

      return view('service_opd.diag_top30',compact('budget_year_select','budget_year','diag_top30','diag_top30_z'));          
}

//Create waiting_period
public function waiting_period(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $waiting_period_month = DB::connection('hosxp')->select('
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
            END AS "month",LEFT(SEC_TO_TIME(AVG(screen_wait)),8) AS screen_wait,LEFT(SEC_TO_TIME(AVG(screen_success)),8) AS screen_success,
            LEFT(SEC_TO_TIME(AVG(doctor_wait)),8) AS doctor_wait,LEFT(SEC_TO_TIME(AVG(doctor_success)),8) AS doctor_success,
            LEFT(SEC_TO_TIME(AVG(rx_success)),8) AS rx_success,LEFT(SEC_TO_TIME(AVG(success_all)),8) AS success_all  
            FROM (SELECT o.vstdate,o.vsttime,b1.begin_time_screen,b2.end_time_screen,b3.begin_time_doctor,b4.end_time_doctor,b5.end_time_rx,
            (time_to_sec(TIME(b1.begin_time_screen))-time_to_sec(TIME(o.vsttime))) AS screen_wait, 
            (time_to_sec(TIME(b2.end_time_screen))-time_to_sec(TIME(b1.begin_time_screen ))) AS screen_success, 
            (time_to_sec(TIME(b3.begin_time_doctor))-time_to_sec(TIME(b2.end_time_screen ))) AS doctor_wait, 
            (time_to_sec(TIME(b4.end_time_doctor))-time_to_sec(TIME(b3.begin_time_doctor ))) AS doctor_success, 
            (time_to_sec(TIME(b5.end_time_rx))-time_to_sec(TIME(b4.end_time_doctor))) AS rx_success,
            (time_to_sec(TIME(IFNULL(b5.end_time_rx,b4.end_time_doctor)))-time_to_sec(TIME(o.vsttime))) AS success_all  
            FROM ovst o 
            LEFT JOIN ( SELECT s1.vn, Time( s1.service_begin_datetime ) AS begin_time_screen FROM ovst_service_time s1  
            WHERE s1.ovst_service_time_type_code = "OPD-SCREEN" ) b1 ON b1.vn = o.vn 
            LEFT JOIN ( SELECT s1.vn, Time( s1.service_end_datetime ) AS end_time_screen FROM ovst_service_time s1  
            WHERE s1.ovst_service_time_type_code = "OPD-SCREEN" ) b2 ON b2.vn = o.vn 
            LEFT JOIN ( SELECT s1.vn, Time( s1.service_begin_datetime ) AS begin_time_doctor FROM ovst_service_time s1  
            WHERE s1.ovst_service_time_type_code = "OPD-DOCTOR" ) b3 ON b3.vn = o.vn 
            LEFT JOIN ( SELECT s1.vn, Time( s1.service_end_datetime ) AS end_time_doctor FROM ovst_service_time s1  
            WHERE s1.ovst_service_time_type_code = "OPD-DOCTOR" ) b4 ON b4.vn = o.vn 
            LEFT JOIN ( SELECT r1.vn, Time( r1.review_finish_datetime ) AS end_time_rx FROM rx_stat r1  
            WHERE r1.review_finish_datetime IS NOT NULL ) b5 ON b5.vn = o.vn 
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND o.main_dep = "002" AND o.vn NOT IN (SELECT vn FROM er_regist)
            AND b1.begin_time_screen IS NOT NULL  
            AND b3.begin_time_doctor IS NOT NULL  
            GROUP BY o.vn ,o.vstdate  
            ORDER BY o.vstdate,o.vsttime ) AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate),MONTH(vstdate) ');

      $waiting_period_year = DB::connection('hosxp')->select('
            SELECT IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            LEFT(SEC_TO_TIME(AVG(screen_wait)),8) AS screen_wait,LEFT(SEC_TO_TIME(AVG(screen_success)),8) AS screen_success,
            LEFT(SEC_TO_TIME(AVG(doctor_wait)),8) AS doctor_wait,LEFT(SEC_TO_TIME(AVG(doctor_success)),8) AS doctor_success,
            LEFT(SEC_TO_TIME(AVG(rx_success)),8) AS rx_success,LEFT(SEC_TO_TIME(AVG(success_all)),8) AS success_all  
            FROM (SELECT o.vstdate,o.vsttime,b1.begin_time_screen,b2.end_time_screen,b3.begin_time_doctor,b4.end_time_doctor,b5.end_time_rx,
            (time_to_sec(TIME(b1.begin_time_screen))-time_to_sec(TIME(o.vsttime))) AS screen_wait, 
            (time_to_sec(TIME(b2.end_time_screen))-time_to_sec(TIME(b1.begin_time_screen ))) AS screen_success, 
            (time_to_sec(TIME(b3.begin_time_doctor))-time_to_sec(TIME(b2.end_time_screen ))) AS doctor_wait, 
            (time_to_sec(TIME(b4.end_time_doctor))-time_to_sec(TIME(b3.begin_time_doctor ))) AS doctor_success, 
            (time_to_sec(TIME(b5.end_time_rx))-time_to_sec(TIME(b4.end_time_doctor))) AS rx_success,
            (time_to_sec(TIME(IFNULL(b5.end_time_rx,b4.end_time_doctor)))-time_to_sec(TIME(o.vsttime))) AS success_all  
            FROM ovst o 
            LEFT JOIN ( SELECT s1.vn, Time( s1.service_begin_datetime ) AS begin_time_screen FROM ovst_service_time s1  
            WHERE s1.ovst_service_time_type_code = "OPD-SCREEN" ) b1 ON b1.vn = o.vn 
            LEFT JOIN ( SELECT s1.vn, Time( s1.service_end_datetime ) AS end_time_screen FROM ovst_service_time s1  
            WHERE s1.ovst_service_time_type_code = "OPD-SCREEN" ) b2 ON b2.vn = o.vn 
            LEFT JOIN ( SELECT s1.vn, Time( s1.service_begin_datetime ) AS begin_time_doctor FROM ovst_service_time s1  
            WHERE s1.ovst_service_time_type_code = "OPD-DOCTOR" ) b3 ON b3.vn = o.vn 
            LEFT JOIN ( SELECT s1.vn, Time( s1.service_end_datetime ) AS end_time_doctor FROM ovst_service_time s1  
            WHERE s1.ovst_service_time_type_code = "OPD-DOCTOR" ) b4 ON b4.vn = o.vn 
            LEFT JOIN ( SELECT r1.vn, Time( r1.review_finish_datetime ) AS end_time_rx FROM rx_stat r1  
            WHERE r1.review_finish_datetime IS NOT NULL ) b5 ON b5.vn = o.vn 
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND o.main_dep = "002" AND o.vn NOT IN (SELECT vn FROM er_regist)
            AND b1.begin_time_screen IS NOT NULL  
            AND b3.begin_time_doctor IS NOT NULL  
            GROUP BY o.vn ,o.vstdate  
            ORDER BY o.vstdate,o.vsttime ) AS a
            GROUP BY year_bud
            ORDER BY year_bud ');

      return view('service_opd.waiting_period',compact('budget_year_select','budget_year','waiting_period_month','waiting_period_year'));          
}

//Create telehealth
public function telehealth(Request $request )
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $telehealth_list = DB::connection('hosxp')->select('
            SELECT o.vstdate,o.vn,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(vp.pttype," [",p1.hipdata_code,"]") AS pttype,v.pdx,k.department,
            IF(o1.vn<>"","ตามนัด","") AS oapp,k1.department AS oapp_dep,c.`name` AS oapp_clinic,
            d.`name` AS oapp_doctor,d1.`name` AS dx_doctor,vp.auth_code
            FROM ovst o						
            LEFT JOIN oapp o1 ON o1.vn=o.vn
            LEFT JOIN clinic c ON c.clinic=o1.clinic
            LEFT JOIN kskdepartment k ON k.depcode=o.main_dep
            LEFT JOIN kskdepartment k1 ON k1.depcode=o1.depcode
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p1 ON p1.pttype=vp.pttype 
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN doctor d ON d.`code`=o1.doctor
            LEFT JOIN doctor d1 ON d1.`code`=v.dx_doctor						
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND o.ovstist = "12"
            GROUP BY o.vn ORDER BY o.hn,o.vstdate');
      
      $telehealth_month = DB::connection('hosxp')->select('
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
            END AS "month",
            COUNT(DISTINCT vn) AS visit,
            COUNT(DISTINCT hn) AS hn
            FROM (SELECT o.vstdate,o.vn,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(vp.pttype," [",p1.hipdata_code,"]") AS pttype,v.pdx,k.department,
            IF(o1.vn<>"","ตามนัด","") AS oapp,k1.department AS oapp_dep,c.`name` AS oapp_clinic,
            d.`name` AS oapp_doctor,d1.`name` AS dx_doctor,vp.auth_code
            FROM ovst o	
            LEFT JOIN oapp o1 ON o1.vn=o.vn
            LEFT JOIN clinic c ON c.clinic=o1.clinic
            LEFT JOIN kskdepartment k ON k.depcode=o.main_dep
            LEFT JOIN kskdepartment k1 ON k1.depcode=o1.depcode
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p1 ON p1.pttype=vp.pttype 
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN doctor d ON d.`code`=o1.doctor
            LEFT JOIN doctor d1 ON d1.`code`=v.dx_doctor						
            WHERE o.ovstist = "12" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY o.vn ORDER BY o.hn,o.vstdate) AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $telehealth_m = array_column($telehealth_month,'month');
      $telehealth_visit_m = array_column($telehealth_month,'visit');
      $telehealth_hn_m = array_column($telehealth_month,'hn');

      $telehealth_clinic = DB::connection('hosxp')->select('
            SELECT IF(oapp_clinic IS NULL,"ไม่นัดคลินิก",oapp_clinic) AS clinic,
		COUNT(DISTINCT vn) AS visit
            FROM (SELECT o.vstdate,o.vn,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(vp.pttype," [",p1.hipdata_code,"]") AS pttype,v.pdx,k.department,
            IF(o1.vn<>"","ตามนัด","") AS oapp,k1.department AS oapp_dep,c.`name` AS oapp_clinic,
            d.`name` AS oapp_doctor,d1.`name` AS dx_doctor,vp.auth_code
            FROM ovst o	
            LEFT JOIN oapp o1 ON o1.vn=o.vn
            LEFT JOIN clinic c ON c.clinic=o1.clinic
            LEFT JOIN kskdepartment k ON k.depcode=o.main_dep
            LEFT JOIN kskdepartment k1 ON k1.depcode=o1.depcode
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p1 ON p1.pttype=vp.pttype 
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN doctor d ON d.`code`=o1.doctor
            LEFT JOIN doctor d1 ON d1.`code`=v.dx_doctor						
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND o.ovstist = "12"
            GROUP BY o.vn ORDER BY o.hn,o.vstdate) AS a
		GROUP BY oapp_clinic');
      $telehealth_c_clinic = array_column($telehealth_clinic,'clinic');
      $telehealth_visit_clinic = array_column($telehealth_clinic,'visit');    

      return view('service_opd.telehealth',compact('budget_year_select','budget_year','telehealth_list','telehealth_m',
            'telehealth_visit_m','telehealth_hn_m','telehealth_c_clinic','telehealth_visit_clinic'));
}


}
