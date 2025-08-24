<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Medicalrecord_DiagController extends Controller
{
//Check Login
public function __construct()
{
      $this->middleware('auth');
}

//Create index
public function index()
{

      return view('medicalrecord_diag.index');            
}

//Create alcohol_withdrawal
public function alcohol_withdrawal(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_list = DB::connection('hosxp')->select('
            SELECT o.vn,o3.name AS ovstist,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o             
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" 
            LEFT JOIN ovstist o3 ON o3.ovstist=o.ovstist
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("F103","F1030","F1031","F104","F1040","F1041") 
            OR v.dx0 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR v.dx1 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR v.dx2 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR v.dx3 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR v.dx4 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR v.dx5 IN ("F103","F1030","F1031","F104","F1040","F1041"))
            GROUP BY o.vn ');

      $diag_month = DB::connection('hosxp')->select('select 
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
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT vn) AS "visit"
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("F103","F1030","F1031","F104","F1040","F1041") 
            OR v.dx0 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR v.dx1 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR v.dx2 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR v.dx3 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR v.dx4 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR v.dx5 IN ("F103","F1030","F1031","F104","F1040","F1041"))) AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $diag_m = array_column($diag_month,'month');
      $diag_visit_m = array_column($diag_month,'visit');
      $diag_hn_m = array_column($diag_month,'hn');

      $diag_year = DB::connection('hosxp')->select('select 
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            COUNT(DISTINCT vn) as visit,COUNT(DISTINCT hn) as hn
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND (v.pdx IN ("F103","F1030","F1031","F104","F1040","F1041") 
            OR v.dx0 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR v.dx1 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR v.dx2 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR v.dx3 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR v.dx4 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR v.dx5 IN ("F103","F1030","F1031","F104","F1040","F1041"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y = array_column($diag_year,'year_bud');
      $diag_visit_y = array_column($diag_year,'visit');
      $diag_hn_y = array_column($diag_year,'hn');

      $diag_list_ipd = DB::connection('hosxp')->select('
            SELECT i.an,i.hn,i.regdate,i.regtime,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            a.age_y,CONCAT(i.pttype," [",p1.hipdata_code,"]") AS pttype,i.prediag,a.pdx,GROUP_CONCAT(i1.icd10) AS dx,
            d.`name` AS dx_doctor,CONCAT(h.`name`," [",r.pdx,"]") AS refer,i.dchdate,i.dchtime 
            FROM ipt i
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an AND i1.diagtype <>"1" 
            LEFT JOIN referout r ON r.vn=i.an
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=i.hn
            LEFT JOIN pttype p1 ON p1.pttype=i.pttype
            LEFT JOIN doctor d ON d.`code`=a.dx_doctor
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            AND (a.pdx IN ("F103","F1030","F1031","F104","F1040","F1041") 
            OR a.dx0 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR a.dx1 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR a.dx2 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR a.dx3 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR a.dx4 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR a.dx5 IN ("F103","F1030","F1031","F104","F1040","F1041"))
            GROUP BY i.an ');

      $diag_month_ipd = DB::connection('hosxp')->select('select 
            CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(dchdate)+543,2))
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT an) AS "an"
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("F103","F1030","F1031","F104","F1040","F1041") 
            OR a.dx0 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR a.dx1 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR a.dx2 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR a.dx3 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR a.dx4 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR a.dx5 IN ("F103","F1030","F1031","F104","F1040","F1041"))) AS a
            GROUP BY MONTH(dchdate)
            ORDER BY YEAR(dchdate) , MONTH(dchdate)');
      $diag_m_ipd = array_column($diag_month_ipd,'month');
      $diag_an_m_ipd = array_column($diag_month_ipd,'an');
      $diag_hn_m_ipd = array_column($diag_month_ipd,'hn');

      $diag_year_ipd = DB::connection('hosxp')->select('select 
            IF(MONTH(dchdate)>9,YEAR(dchdate)+1,YEAR(dchdate)) + 543 AS year_bud,
            COUNT(DISTINCT an) as an,COUNT(DISTINCT hn) as hn
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("F103","F1030","F1031","F104","F1040","F1041") 
            OR a.dx0 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR a.dx1 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR a.dx2 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR a.dx3 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR a.dx4 IN ("F103","F1030","F1031","F104","F1040","F1041")
            OR a.dx5 IN ("F103","F1030","F1031","F104","F1040","F1041"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

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
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("F103","F1030","F1031","F104","F1040","F1041") 
            GROUP BY MONTH(refer_date)
            ORDER BY YEAR(refer_date) , MONTH(refer_date)');
      $refer_m = array_column($refer_month,'month');
      $refer_opd_m = array_column($refer_month,'opd'); 
      $refer_ipd_m = array_column($refer_month,'ipd');     

      $refer_year = DB::connection('hosxp')->select('select 
            IF(MONTH(refer_date)>9,YEAR(refer_date)+1,YEAR(refer_date)) + 543 AS year_bud,
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("F103","F1030","F1031","F104","F1040","F1041") 
            GROUP BY year_bud
            ORDER BY year_bud');
      $refer_y = array_column($refer_year,'year_bud');
      $refer_opd_y = array_column($refer_year,'opd');
      $refer_ipd_y = array_column($refer_year,'ipd'); 

      $refer_list = DB::connection('hosxp')->select('select
            o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,pmh.cc_persist_disease AS pmh,
            GROUP_CONCAT(c1.`name`) AS "clinic",r.department,r.refer_point,o.vstdate,o.vsttime,
            IFNULL(v.pdx,a.pdx) AS pdx,r.refer_date,r.refer_time,r.pre_diagnosis,r.pdx AS pdx_refer,h.`name` AS refer_hos
            FROM referout r 
            LEFT JOIN ovst o ON o.vn=r.vn
            LEFT JOIN vn_stat v ON v.vn=r.vn 
            LEFT JOIN an_stat a ON a.an=r.vn
            LEFT JOIN clinicmember c ON c.hn=r.hn
            LEFT JOIN clinic c1 ON c1.clinic=c.clinic
            LEFT JOIN opd_ill_history pmh ON pmh.hn=r.hn
            LEFT JOIN patient p ON p.hn=r.hn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		 
            AND (r.pdx IN ("F103","F1030","F1031","F104","F1040","F1041") 
            OR v.pdx IN ("F103","F1030","F1031","F104","F1040","F1041") 
            OR a.pdx IN ("F103","F1030","F1031","F104","F1040","F1041")) 
            GROUP BY r.vn							
            ORDER BY r.refer_point,r.refer_date');

      return view('medicalrecord_diag.alcohol_withdrawal',compact('budget_year_select','budget_year','diag_m','diag_visit_m',
            'diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd','diag_hn_m_ipd',
            'diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd','refer_m','refer_opd_m','refer_ipd_m',
            'refer_y','refer_opd_y','refer_ipd_y','refer_list'));            
}

//Create asthma
public function asthma(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_list = DB::connection('hosxp')->select('
            SELECT o.vn,o3.name AS ovstist,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" 
            LEFT JOIN ovstist o3 ON o3.ovstist=o.ovstist
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("J450","J451","J452","J458","J459")
            OR v.dx0 IN ("J450","J451","J452","J458","J459")
            OR v.dx1 IN ("J450","J451","J452","J458","J459")
            OR v.dx2 IN ("J450","J451","J452","J458","J459")
            OR v.dx3 IN ("J450","J451","J452","J458","J459")
            OR v.dx4 IN ("J450","J451","J452","J458","J459")
            OR v.dx5 IN ("J450","J451","J452","J458","J459"))
            GROUP BY o.vn ');

      $diag_month = DB::connection('hosxp')->select('select 
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
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT vn) AS "visit"
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("J450","J451","J452","J458","J459")
            OR v.dx0 IN ("J450","J451","J452","J458","J459")
            OR v.dx1 IN ("J450","J451","J452","J458","J459")
            OR v.dx2 IN ("J450","J451","J452","J458","J459")
            OR v.dx3 IN ("J450","J451","J452","J458","J459")
            OR v.dx4 IN ("J450","J451","J452","J458","J459")
            OR v.dx5 IN ("J450","J451","J452","J458","J459"))) AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $diag_m = array_column($diag_month,'month');
      $diag_visit_m = array_column($diag_month,'visit');
      $diag_hn_m = array_column($diag_month,'hn');

      $diag_year = DB::connection('hosxp')->select('select 
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            COUNT(DISTINCT vn) as visit,COUNT(DISTINCT hn) as hn
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND (v.pdx IN ("J450","J451","J452","J458","J459")
            OR v.dx0 IN ("J450","J451","J452","J458","J459")
            OR v.dx1 IN ("J450","J451","J452","J458","J459")
            OR v.dx2 IN ("J450","J451","J452","J458","J459")
            OR v.dx3 IN ("J450","J451","J452","J458","J459")
            OR v.dx4 IN ("J450","J451","J452","J458","J459")
            OR v.dx5 IN ("J450","J451","J452","J458","J459"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y = array_column($diag_year,'year_bud');
      $diag_visit_y = array_column($diag_year,'visit');
      $diag_hn_y = array_column($diag_year,'hn');

      $diag_list_ipd = DB::connection('hosxp')->select('
            SELECT i.an,i.hn,i.regdate,i.regtime,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            a.age_y,CONCAT(i.pttype," [",p1.hipdata_code,"]") AS pttype,i.prediag,a.pdx,GROUP_CONCAT(i1.icd10) AS dx,
            d.`name` AS dx_doctor,CONCAT(h.`name`," [",r.pdx,"]") AS refer,i.dchdate,i.dchtime 
            FROM ipt i
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an AND i1.diagtype <>"1" 
            LEFT JOIN referout r ON r.vn=i.an
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=i.hn
            LEFT JOIN pttype p1 ON p1.pttype=i.pttype
            LEFT JOIN doctor d ON d.`code`=a.dx_doctor
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            AND (a.pdx IN ("J450","J451","J452","J458","J459")
            OR a.dx0 IN ("J450","J451","J452","J458","J459")
            OR a.dx1 IN ("J450","J451","J452","J458","J459")
            OR a.dx2 IN ("J450","J451","J452","J458","J459")
            OR a.dx3 IN ("J450","J451","J452","J458","J459")
            OR a.dx4 IN ("J450","J451","J452","J458","J459")
            OR a.dx5 IN ("J450","J451","J452","J458","J459"))
            GROUP BY i.an ');

      $diag_month_ipd = DB::connection('hosxp')->select('select 
            CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(dchdate)+543,2))
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT an) AS "an"
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("J450","J451","J452","J458","J459")
            OR a.dx0 IN ("J450","J451","J452","J458","J459")
            OR a.dx1 IN ("J450","J451","J452","J458","J459")
            OR a.dx2 IN ("J450","J451","J452","J458","J459")
            OR a.dx3 IN ("J450","J451","J452","J458","J459")
            OR a.dx4 IN ("J450","J451","J452","J458","J459")
            OR a.dx5 IN ("J450","J451","J452","J458","J459"))) AS a
            GROUP BY MONTH(dchdate)
            ORDER BY YEAR(dchdate) , MONTH(dchdate)');
      $diag_m_ipd = array_column($diag_month_ipd,'month');
      $diag_an_m_ipd = array_column($diag_month_ipd,'an');
      $diag_hn_m_ipd = array_column($diag_month_ipd,'hn');

      $diag_year_ipd = DB::connection('hosxp')->select('select 
            IF(MONTH(dchdate)>9,YEAR(dchdate)+1,YEAR(dchdate)) + 543 AS year_bud,
            COUNT(DISTINCT an) as an,COUNT(DISTINCT hn) as hn
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("J450","J451","J452","J458","J459")
            OR a.dx0 IN ("J450","J451","J452","J458","J459")
            OR a.dx1 IN ("J450","J451","J452","J458","J459")
            OR a.dx2 IN ("J450","J451","J452","J458","J459")
            OR a.dx3 IN ("J450","J451","J452","J458","J459")
            OR a.dx4 IN ("J450","J451","J452","J458","J459")
            OR a.dx5 IN ("J450","J451","J452","J458","J459"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

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
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("J450","J451","J452","J458","J459") 
            GROUP BY MONTH(refer_date)
            ORDER BY YEAR(refer_date) , MONTH(refer_date)');
      $refer_m = array_column($refer_month,'month');
      $refer_opd_m = array_column($refer_month,'opd'); 
      $refer_ipd_m = array_column($refer_month,'ipd');     

      $refer_year = DB::connection('hosxp')->select('select 
            IF(MONTH(refer_date)>9,YEAR(refer_date)+1,YEAR(refer_date)) + 543 AS year_bud,
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("J450","J451","J452","J458","J459")
            GROUP BY year_bud
            ORDER BY year_bud');
      $refer_y = array_column($refer_year,'year_bud');
      $refer_opd_y = array_column($refer_year,'opd');
      $refer_ipd_y = array_column($refer_year,'ipd'); 

      $refer_list = DB::connection('hosxp')->select('select
            o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,pmh.cc_persist_disease AS pmh,
            GROUP_CONCAT(c1.`name`) AS "clinic",r.department,r.refer_point,o.vstdate,o.vsttime,
            IFNULL(v.pdx,a.pdx) AS pdx,r.refer_date,r.refer_time,r.pre_diagnosis,r.pdx AS pdx_refer,h.`name` AS refer_hos
            FROM referout r 
            LEFT JOIN ovst o ON o.vn=r.vn
            LEFT JOIN vn_stat v ON v.vn=r.vn 
            LEFT JOIN an_stat a ON a.an=r.vn
            LEFT JOIN clinicmember c ON c.hn=r.hn
            LEFT JOIN clinic c1 ON c1.clinic=c.clinic
            LEFT JOIN opd_ill_history pmh ON pmh.hn=r.hn
            LEFT JOIN patient p ON p.hn=r.hn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		 
            AND (r.pdx IN ("J450","J451","J452","J458","J459")
            OR v.pdx IN ("J450","J451","J452","J458","J459")
            OR a.pdx IN ("J450","J451","J452","J458","J459")) 
            GROUP BY r.vn							
            ORDER BY r.refer_point,r.refer_date');

      return view('medicalrecord_diag.asthma',compact('budget_year_select','budget_year','diag_m','diag_visit_m',
            'diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd','diag_hn_m_ipd',
            'diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd','refer_m','refer_opd_m','refer_ipd_m',
            'refer_y','refer_opd_y','refer_ipd_y','refer_list'));            
}

//Create copd
public function copd(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_list = DB::connection('hosxp')->select('
            SELECT o.vn,o3.name AS ovstist,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" 
            LEFT JOIN ovstist o3 ON o3.ovstist=o.ovstist
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("J44","J440","J441","J448","J449")
            OR v.dx0 IN ("J44","J440","J441","J448","J449")
            OR v.dx1 IN ("J44","J440","J441","J448","J449")
            OR v.dx2 IN ("J44","J440","J441","J448","J449")
            OR v.dx3 IN ("J44","J440","J441","J448","J449")
            OR v.dx4 IN ("J44","J440","J441","J448","J449")
            OR v.dx5 IN ("J44","J440","J441","J448","J449"))
            GROUP BY o.vn ');

      $diag_month = DB::connection('hosxp')->select('select 
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
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT vn) AS "visit"
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("J44","J440","J441","J448","J449")
            OR v.dx0 IN ("J44","J440","J441","J448","J449")
            OR v.dx1 IN ("J44","J440","J441","J448","J449")
            OR v.dx2 IN ("J44","J440","J441","J448","J449")
            OR v.dx3 IN ("J44","J440","J441","J448","J449")
            OR v.dx4 IN ("J44","J440","J441","J448","J449")
            OR v.dx5 IN ("J44","J440","J441","J448","J449"))) AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $diag_m = array_column($diag_month,'month');
      $diag_visit_m = array_column($diag_month,'visit');
      $diag_hn_m = array_column($diag_month,'hn');

      $diag_year = DB::connection('hosxp')->select('select 
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            COUNT(DISTINCT vn) as visit,COUNT(DISTINCT hn) as hn
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND (v.pdx IN ("J44","J440","J441","J448","J449")
            OR v.dx0 IN ("J44","J440","J441","J448","J449")
            OR v.dx1 IN ("J44","J440","J441","J448","J449")
            OR v.dx2 IN ("J44","J440","J441","J448","J449")
            OR v.dx3 IN ("J44","J440","J441","J448","J449")
            OR v.dx4 IN ("J44","J440","J441","J448","J449")
            OR v.dx5 IN ("J44","J440","J441","J448","J449"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y = array_column($diag_year,'year_bud');
      $diag_visit_y = array_column($diag_year,'visit');
      $diag_hn_y = array_column($diag_year,'hn');

      $diag_list_ipd = DB::connection('hosxp')->select('
            SELECT i.an,i.hn,i.regdate,i.regtime,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            a.age_y,CONCAT(i.pttype," [",p1.hipdata_code,"]") AS pttype,i.prediag,a.pdx,GROUP_CONCAT(i1.icd10) AS dx,
            d.`name` AS dx_doctor,CONCAT(h.`name`," [",r.pdx,"]") AS refer,i.dchdate,i.dchtime 
            FROM ipt i
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an AND i1.diagtype <>"1" 
            LEFT JOIN referout r ON r.vn=i.an
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=i.hn
            LEFT JOIN pttype p1 ON p1.pttype=i.pttype
            LEFT JOIN doctor d ON d.`code`=a.dx_doctor
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            AND (a.pdx IN ("J44","J440","J441","J448","J449")
            OR a.dx0 IN ("J44","J440","J441","J448","J449")
            OR a.dx1 IN ("J44","J440","J441","J448","J449")
            OR a.dx2 IN ("J44","J440","J441","J448","J449")
            OR a.dx3 IN ("J44","J440","J441","J448","J449")
            OR a.dx4 IN ("J44","J440","J441","J448","J449")
            OR a.dx5 IN ("J44","J440","J441","J448","J449"))
            GROUP BY i.an ');

      $diag_month_ipd = DB::connection('hosxp')->select('select 
            CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(dchdate)+543,2))
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT an) AS "an"
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("J44","J440","J441","J448","J449")
            OR a.dx0 IN ("J44","J440","J441","J448","J449")
            OR a.dx1 IN ("J44","J440","J441","J448","J449")
            OR a.dx2 IN ("J44","J440","J441","J448","J449")
            OR a.dx3 IN ("J44","J440","J441","J448","J449")
            OR a.dx4 IN ("J44","J440","J441","J448","J449")
            OR a.dx5 IN ("J44","J440","J441","J448","J449"))) AS a
            GROUP BY MONTH(dchdate)
            ORDER BY YEAR(dchdate) , MONTH(dchdate)');
      $diag_m_ipd = array_column($diag_month_ipd,'month');
      $diag_an_m_ipd = array_column($diag_month_ipd,'an');
      $diag_hn_m_ipd = array_column($diag_month_ipd,'hn');

      $diag_year_ipd = DB::connection('hosxp')->select('select 
            IF(MONTH(dchdate)>9,YEAR(dchdate)+1,YEAR(dchdate)) + 543 AS year_bud,
            COUNT(DISTINCT an) as an,COUNT(DISTINCT hn) as hn
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("J44","J440","J441","J448","J449")
            OR a.dx0 IN ("J44","J440","J441","J448","J449")
            OR a.dx1 IN ("J44","J440","J441","J448","J449")
            OR a.dx2 IN ("J44","J440","J441","J448","J449")
            OR a.dx3 IN ("J44","J440","J441","J448","J449")
            OR a.dx4 IN ("J44","J440","J441","J448","J449")
            OR a.dx5 IN ("J44","J440","J441","J448","J449"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

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
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("J44","J440","J441","J448","J449")
            GROUP BY MONTH(refer_date)
            ORDER BY YEAR(refer_date) , MONTH(refer_date)');
      $refer_m = array_column($refer_month,'month');
      $refer_opd_m = array_column($refer_month,'opd'); 
      $refer_ipd_m = array_column($refer_month,'ipd');     

      $refer_year = DB::connection('hosxp')->select('select 
            IF(MONTH(refer_date)>9,YEAR(refer_date)+1,YEAR(refer_date)) + 543 AS year_bud,
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("J44","J440","J441","J448","J449")
            GROUP BY year_bud
            ORDER BY year_bud');
      $refer_y = array_column($refer_year,'year_bud');
      $refer_opd_y = array_column($refer_year,'opd');
      $refer_ipd_y = array_column($refer_year,'ipd'); 

      $refer_list = DB::connection('hosxp')->select('select
            o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,pmh.cc_persist_disease AS pmh,
            GROUP_CONCAT(c1.`name`) AS "clinic",r.department,r.refer_point,o.vstdate,o.vsttime,
            IFNULL(v.pdx,a.pdx) AS pdx,r.refer_date,r.refer_time,r.pre_diagnosis,r.pdx AS pdx_refer,h.`name` AS refer_hos
            FROM referout r 
            LEFT JOIN ovst o ON o.vn=r.vn
            LEFT JOIN vn_stat v ON v.vn=r.vn 
            LEFT JOIN an_stat a ON a.an=r.vn
            LEFT JOIN clinicmember c ON c.hn=r.hn
            LEFT JOIN clinic c1 ON c1.clinic=c.clinic
            LEFT JOIN opd_ill_history pmh ON pmh.hn=r.hn
            LEFT JOIN patient p ON p.hn=r.hn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		 
            AND (r.pdx IN ("J44","J440","J441","J448","J449")
            OR v.pdx IN ("J44","J440","J441","J448","J449")
            OR a.pdx IN ("J44","J440","J441","J448","J449")) 
            GROUP BY r.vn							
            ORDER BY r.refer_point,r.refer_date');

      return view('medicalrecord_diag.copd',compact('budget_year_select','budget_year','diag_m','diag_visit_m',
            'diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd','diag_hn_m_ipd',
            'diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd','refer_m','refer_opd_m','refer_ipd_m',
            'refer_y','refer_opd_y','refer_ipd_y','refer_list'));            
}

//Create mi
public function mi(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_list = DB::connection('hosxp')->select('
            SELECT o.vn,o4.name AS ovstist,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer,IF(o3.icode <>"",CONCAT("Streptokinase [",o3.qty,"]"),"") AS drug
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" 
            LEFT JOIN opitemrece o3 ON o3.vn=o.vn AND o3.icode = "1580011"
            LEFT JOIN ovstist o4 ON o4.ovstist=o.ovstist
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx0 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx1 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx2 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx3 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx4 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx5 IN ("I21","I210","I211","I212","I213","I214","I219"))
            GROUP BY o.vn ');

      $diag_month = DB::connection('hosxp')->select('select 
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
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT vn) AS "visit"
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx0 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx1 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx2 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx3 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx4 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx5 IN ("I21","I210","I211","I212","I213","I214","I219"))) AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $diag_m = array_column($diag_month,'month');
      $diag_visit_m = array_column($diag_month,'visit');
      $diag_hn_m = array_column($diag_month,'hn');

      $diag_year = DB::connection('hosxp')->select('select 
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            COUNT(DISTINCT vn) as visit,COUNT(DISTINCT hn) as hn
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND (v.pdx IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx0 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx1 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx2 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx3 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx4 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.dx5 IN ("I21","I210","I211","I212","I213","I214","I219"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y = array_column($diag_year,'year_bud');
      $diag_visit_y = array_column($diag_year,'visit');
      $diag_hn_y = array_column($diag_year,'hn');

      $diag_list_ipd = DB::connection('hosxp')->select('
            SELECT i.an,i.hn,i.regdate,i.regtime,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            a.age_y,CONCAT(i.pttype," [",p1.hipdata_code,"]") AS pttype,i.prediag,a.pdx,GROUP_CONCAT(i1.icd10) AS dx,
            d.`name` AS dx_doctor,CONCAT(h.`name`," [",r.pdx,"]") AS refer,i.dchdate,i.dchtime ,
            IF(o.icode <>"",CONCAT("Streptokinase [",o.qty,"]"),"") AS drug
            FROM ipt i
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an AND i1.diagtype <>"1"
            LEFT JOIN opitemrece o ON o.an=i.an AND o.icode = "1580011" 
            LEFT JOIN referout r ON r.vn=i.an
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=i.hn
            LEFT JOIN pttype p1 ON p1.pttype=i.pttype
            LEFT JOIN doctor d ON d.`code`=a.dx_doctor
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            AND (a.pdx IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx0 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx1 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx2 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx3 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx4 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx5 IN ("I21","I210","I211","I212","I213","I214","I219"))
            GROUP BY i.an ');

      $diag_month_ipd = DB::connection('hosxp')->select('select 
            CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(dchdate)+543,2))
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT an) AS "an"
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx0 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx1 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx2 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx3 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx4 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx5 IN ("I21","I210","I211","I212","I213","I214","I219"))) AS a
            GROUP BY MONTH(dchdate)
            ORDER BY YEAR(dchdate) , MONTH(dchdate)');
      $diag_m_ipd = array_column($diag_month_ipd,'month');
      $diag_an_m_ipd = array_column($diag_month_ipd,'an');
      $diag_hn_m_ipd = array_column($diag_month_ipd,'hn');

      $diag_year_ipd = DB::connection('hosxp')->select('select 
            IF(MONTH(dchdate)>9,YEAR(dchdate)+1,YEAR(dchdate)) + 543 AS year_bud,
            COUNT(DISTINCT an) as an,COUNT(DISTINCT hn) as hn
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx0 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx1 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx2 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx3 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx4 IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.dx5 IN ("I21","I210","I211","I212","I213","I214","I219"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

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
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("I21","I210","I211","I212","I213","I214","I219")
            GROUP BY MONTH(refer_date)
            ORDER BY YEAR(refer_date) , MONTH(refer_date)');
      $refer_m = array_column($refer_month,'month');
      $refer_opd_m = array_column($refer_month,'opd'); 
      $refer_ipd_m = array_column($refer_month,'ipd');     

      $refer_year = DB::connection('hosxp')->select('select 
            IF(MONTH(refer_date)>9,YEAR(refer_date)+1,YEAR(refer_date)) + 543 AS year_bud,
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("I21","I210","I211","I212","I213","I214","I219")
            GROUP BY year_bud
            ORDER BY year_bud');
      $refer_y = array_column($refer_year,'year_bud');
      $refer_opd_y = array_column($refer_year,'opd');
      $refer_ipd_y = array_column($refer_year,'ipd'); 

      $refer_list = DB::connection('hosxp')->select('select
            o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,pmh.cc_persist_disease AS pmh,
            GROUP_CONCAT(c1.`name`) AS "clinic",r.department,r.refer_point,o.vstdate,o.vsttime,
            IFNULL(v.pdx,a.pdx) AS pdx,r.refer_date,r.refer_time,r.pre_diagnosis,r.pdx AS pdx_refer,
            h.`name` AS refer_hos
            FROM referout r 
            LEFT JOIN ovst o ON o.vn=r.vn
            LEFT JOIN vn_stat v ON v.vn=r.vn 
            LEFT JOIN an_stat a ON a.an=r.vn
            LEFT JOIN clinicmember c ON c.hn=r.hn
            LEFT JOIN clinic c1 ON c1.clinic=c.clinic
            LEFT JOIN opd_ill_history pmh ON pmh.hn=r.hn
            LEFT JOIN patient p ON p.hn=r.hn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		 
            AND (r.pdx IN ("I21","I210","I211","I212","I213","I214","I219")
            OR v.pdx IN ("I21","I210","I211","I212","I213","I214","I219")
            OR a.pdx IN ("I21","I210","I211","I212","I213","I214","I219")) 
            GROUP BY r.vn							
            ORDER BY r.refer_point,r.refer_date');

      return view('medicalrecord_diag.mi',compact('budget_year_select','budget_year','diag_m','diag_visit_m',
            'diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd','diag_hn_m_ipd',
            'diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd','refer_m','refer_opd_m','refer_ipd_m',
            'refer_y','refer_opd_y','refer_ipd_y','refer_list'));            
}

//Create ihd
public function ihd(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_list = DB::connection('hosxp')->select('
            SELECT o.vn,o4.name AS ovstist,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer,IF(o3.icode <>"",CONCAT("Streptokinase [",o3.qty,"]"),"") AS drug
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" 
            LEFT JOIN opitemrece o3 ON o3.vn=o.vn AND o3.icode = "1580011"
            LEFT JOIN ovstist o4 ON o4.ovstist=o.ovstist
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx0 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx1 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx2 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx3 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx4 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx5 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259"))
            GROUP BY o.vn ');

      $diag_month = DB::connection('hosxp')->select('select 
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
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT vn) AS "visit"
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx0 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx1 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx2 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx3 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx4 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx5 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259"))) AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $diag_m = array_column($diag_month,'month');
      $diag_visit_m = array_column($diag_month,'visit');
      $diag_hn_m = array_column($diag_month,'hn');

      $diag_year = DB::connection('hosxp')->select('select 
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            COUNT(DISTINCT vn) as visit,COUNT(DISTINCT hn) as hn
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND (v.pdx IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx0 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx1 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx2 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx3 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx4 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.dx5 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y = array_column($diag_year,'year_bud');
      $diag_visit_y = array_column($diag_year,'visit');
      $diag_hn_y = array_column($diag_year,'hn');

      $diag_list_ipd = DB::connection('hosxp')->select('
            SELECT i.an,i.hn,i.regdate,i.regtime,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            a.age_y,CONCAT(i.pttype," [",p1.hipdata_code,"]") AS pttype,i.prediag,a.pdx,GROUP_CONCAT(i1.icd10) AS dx,
            d.`name` AS dx_doctor,CONCAT(h.`name`," [",r.pdx,"]") AS refer,i.dchdate,i.dchtime ,
            IF(o.icode <>"",CONCAT("Streptokinase [",o.qty,"]"),"") AS drug
            FROM ipt i
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an AND i1.diagtype <>"1"
            LEFT JOIN opitemrece o ON o.an=i.an AND o.icode = "1580011" 
            LEFT JOIN referout r ON r.vn=i.an
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=i.hn
            LEFT JOIN pttype p1 ON p1.pttype=i.pttype
            LEFT JOIN doctor d ON d.`code`=a.dx_doctor
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            AND (a.pdx IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx0 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx1 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx2 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx3 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx4 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx5 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259"))
            GROUP BY i.an ');

      $diag_month_ipd = DB::connection('hosxp')->select('select 
            CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(dchdate)+543,2))
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT an) AS "an"
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx0 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx1 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx2 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx3 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx4 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx5 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259"))) AS a
            GROUP BY MONTH(dchdate)
            ORDER BY YEAR(dchdate) , MONTH(dchdate)');
      $diag_m_ipd = array_column($diag_month_ipd,'month');
      $diag_an_m_ipd = array_column($diag_month_ipd,'an');
      $diag_hn_m_ipd = array_column($diag_month_ipd,'hn');

      $diag_year_ipd = DB::connection('hosxp')->select('select 
            IF(MONTH(dchdate)>9,YEAR(dchdate)+1,YEAR(dchdate)) + 543 AS year_bud,
            COUNT(DISTINCT an) as an,COUNT(DISTINCT hn) as hn
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx0 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx1 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx2 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx3 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx4 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.dx5 IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

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
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            GROUP BY MONTH(refer_date)
            ORDER BY YEAR(refer_date) , MONTH(refer_date)');
      $refer_m = array_column($refer_month,'month');
      $refer_opd_m = array_column($refer_month,'opd'); 
      $refer_ipd_m = array_column($refer_month,'ipd');     

      $refer_year = DB::connection('hosxp')->select('select 
            IF(MONTH(refer_date)>9,YEAR(refer_date)+1,YEAR(refer_date)) + 543 AS year_bud,
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            GROUP BY year_bud
            ORDER BY year_bud');
      $refer_y = array_column($refer_year,'year_bud');
      $refer_opd_y = array_column($refer_year,'opd');
      $refer_ipd_y = array_column($refer_year,'ipd'); 

      $refer_list = DB::connection('hosxp')->select('select
            o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,pmh.cc_persist_disease AS pmh,
            GROUP_CONCAT(c1.`name`) AS "clinic",r.department,r.refer_point,o.vstdate,o.vsttime,
            IFNULL(v.pdx,a.pdx) AS pdx,r.refer_date,r.refer_time,r.pre_diagnosis,r.pdx AS pdx_refer,
            h.`name` AS refer_hos
            FROM referout r 
            LEFT JOIN ovst o ON o.vn=r.vn
            LEFT JOIN vn_stat v ON v.vn=r.vn 
            LEFT JOIN an_stat a ON a.an=r.vn
            LEFT JOIN clinicmember c ON c.hn=r.hn
            LEFT JOIN clinic c1 ON c1.clinic=c.clinic
            LEFT JOIN opd_ill_history pmh ON pmh.hn=r.hn
            LEFT JOIN patient p ON p.hn=r.hn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		 
            AND (r.pdx IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR v.pdx IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")
            OR a.pdx IN ("I20","I200","I201","I208","I209","I24","I240","I241","I248","I249",
                  "I250","I251","I252","I253","I254","I255","I256","I258","I259")) 
            GROUP BY r.vn							
            ORDER BY r.refer_point,r.refer_date');

      return view('medicalrecord_diag.ihd',compact('budget_year_select','budget_year','diag_m','diag_visit_m',
            'diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd','diag_hn_m_ipd',
            'diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd','refer_m','refer_opd_m','refer_ipd_m',
            'refer_y','refer_opd_y','refer_ipd_y','refer_list'));            
}

//palliative_care----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
public function palliative_care(Request $request)
      {
            $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
            $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
            $budget_year = $request->budget_year ?? $budget_year_last;
            $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
            $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
            $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
      
            $diag_list = DB::connection('hosxp')->select('
                  SELECT o.vn,o3.name AS ovstist,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
                  v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(DISTINCT o2.icd10) AS dx,
                  o4.sum_price AS h2o,d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
                  FROM ovst o 
                  LEFT JOIN opdscreen o1 ON o1.vn=o.vn                  
                  LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" 
                  LEFT JOIN ovstist o3 ON o3.ovstist=o.ovstist
		      LEFT JOIN opitemrece o4 ON o4.vn=o.vn AND o4.icode = 3001176
                  LEFT JOIN referout r ON r.vn=o.vn
                  LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
                  LEFT JOIN patient p ON p.hn=o.hn
                  LEFT JOIN pttype p1 ON p1.pttype=o.pttype
			LEFT JOIN vn_stat v ON v.vn=o.vn	
                  LEFT JOIN doctor d ON d.`code`=v.dx_doctor																	
                  WHERE o.vstdate BETWEEN ? AND ?                  
			AND (v.pdx IN ("Z515")
                  OR v.dx0 IN ("Z515")
                  OR v.dx1 IN ("Z515")
                  OR v.dx2 IN ("Z515")
                  OR v.dx3 IN ("Z515")
                  OR v.dx4 IN ("Z515")
                  OR v.dx5 IN ("Z515"))
                  GROUP BY o.vn ',[$start_date,$end_date]);

            $diag_month = DB::connection('hosxp')->select('select 
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
                  END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT vn) AS "visit"
                  FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
                  LEFT JOIN vn_stat v ON v.vn=o.vn									
                  WHERE o.vstdate BETWEEN ? AND ?
                  AND (v.pdx IN ("Z515")
                  OR v.dx0 IN ("Z515")
                  OR v.dx1 IN ("Z515")
                  OR v.dx2 IN ("Z515")
                  OR v.dx3 IN ("Z515")
                  OR v.dx4 IN ("Z515")
                  OR v.dx5 IN ("Z515"))) AS a
                  GROUP BY MONTH(vstdate)
                  ORDER BY YEAR(vstdate) , MONTH(vstdate)',[$start_date,$end_date]);
            $diag_m = array_column($diag_month,'month');
            $diag_visit_m = array_column($diag_month,'visit');
            $diag_hn_m = array_column($diag_month,'hn');

            $diag_year = DB::connection('hosxp')->select('select 
                  IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
                  COUNT(DISTINCT vn) as visit,COUNT(DISTINCT hn) as hn
                  FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
                  LEFT JOIN vn_stat v ON v.vn=o.vn									
                  WHERE o.vstdate BETWEEN ? AND ?
                  AND (v.pdx IN ("Z515")
                  OR v.dx0 IN ("Z515")
                  OR v.dx1 IN ("Z515")
                  OR v.dx2 IN ("Z515")
                  OR v.dx3 IN ("Z515")
                  OR v.dx4 IN ("Z515")
                  OR v.dx5 IN ("Z515"))) AS a
                  GROUP BY year_bud
                  ORDER BY year_bud',[$start_date_y,$end_date]);
            $diag_y = array_column($diag_year,'year_bud');
            $diag_visit_y = array_column($diag_year,'visit');
            $diag_hn_y = array_column($diag_year,'hn');

            $diag_list_ipd = DB::connection('hosxp')->select('
                  SELECT i.an,i.hn,i.regdate,i.regtime,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
                  a.age_y,CONCAT(i.pttype," [",p1.hipdata_code,"]") AS pttype,i.prediag,a.pdx,GROUP_CONCAT(i1.icd10) AS dx,
                  d.`name` AS dx_doctor,CONCAT(h.`name`," [",r.pdx,"]") AS refer,i.dchdate,i.dchtime 
                  FROM ipt i
                  LEFT JOIN an_stat a ON a.an=i.an
                  LEFT JOIN iptdiag i1 ON i1.an=i.an AND i1.diagtype <>"1" 
                  LEFT JOIN referout r ON r.vn=i.an
                  LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
                  LEFT JOIN patient p ON p.hn=i.hn
                  LEFT JOIN pttype p1 ON p1.pttype=i.pttype
                  LEFT JOIN doctor d ON d.`code`=a.dx_doctor
                  WHERE i.dchdate BETWEEN ? AND ?  
                  AND (a.pdx IN ("Z515")
                  OR a.dx0 IN ("Z515")
                  OR a.dx1 IN ("Z515")
                  OR a.dx2 IN ("Z515")
                  OR a.dx3 IN ("Z515")
                  OR a.dx4 IN ("Z515")
                  OR a.dx5 IN ("Z515"))
                  GROUP BY i.an ',[$start_date,$end_date]);

            $diag_month_ipd = DB::connection('hosxp')->select('select 
                  CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(dchdate)+543,2))
                  WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(dchdate)+543,2))
                  WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(dchdate)+543,2))
                  WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(dchdate)+543,2))
                  WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(dchdate)+543,2))
                  WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(dchdate)+543,2))
                  WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(dchdate)+543,2))
                  WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(dchdate)+543,2))
                  WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(dchdate)+543,2))
                  WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(dchdate)+543,2))
                  WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(dchdate)+543,2))
                  WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(dchdate)+543,2))
                  END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT an) AS "an"
                  FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
                  LEFT JOIN an_stat a ON a.an=i.an									
                  WHERE i.dchdate BETWEEN ? AND ?
                  AND (a.pdx IN ("Z515")
                  OR a.dx0 IN ("Z515")
                  OR a.dx1 IN ("Z515")
                  OR a.dx2 IN ("Z515")
                  OR a.dx3 IN ("Z515")
                  OR a.dx4 IN ("Z515")
                  OR a.dx5 IN ("Z515"))) AS a
                  GROUP BY MONTH(dchdate)
                  ORDER BY YEAR(dchdate) , MONTH(dchdate)',[$start_date,$end_date]);
            $diag_m_ipd = array_column($diag_month_ipd,'month');
            $diag_an_m_ipd = array_column($diag_month_ipd,'an');
            $diag_hn_m_ipd = array_column($diag_month_ipd,'hn');

            $diag_year_ipd = DB::connection('hosxp')->select('select 
                  IF(MONTH(dchdate)>9,YEAR(dchdate)+1,YEAR(dchdate)) + 543 AS year_bud,
                  COUNT(DISTINCT an) as an,COUNT(DISTINCT hn) as hn
                  FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
                  LEFT JOIN an_stat a ON a.an=i.an									
                  WHERE i.dchdate BETWEEN ? AND ?
                  AND (a.pdx IN ("Z515")
                  OR a.dx0 IN ("Z515")
                  OR a.dx1 IN ("Z515")
                  OR a.dx2 IN ("Z515")
                  OR a.dx3 IN ("Z515")
                  OR a.dx4 IN ("Z515")
                  OR a.dx5 IN ("Z515"))) AS a
                  GROUP BY year_bud
                  ORDER BY year_bud',[$start_date_y,$end_date]);
            $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
            $diag_an_y_ipd = array_column($diag_year_ipd,'an');
            $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

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
                  SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
                  SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
                  FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
                  IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
                  FROM referout r 
                  LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
                  LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
                  WHERE r.refer_date BETWEEN ? AND ?		
                  GROUP BY r.vn) AS a
                  WHERE pdx IN ("Z515")
                  GROUP BY MONTH(refer_date)
                  ORDER BY YEAR(refer_date) , MONTH(refer_date)',[$start_date,$end_date]);
            $refer_m = array_column($refer_month,'month');
            $refer_opd_m = array_column($refer_month,'opd'); 
            $refer_ipd_m = array_column($refer_month,'ipd');     

            $refer_year = DB::connection('hosxp')->select('select 
                  IF(MONTH(refer_date)>9,YEAR(refer_date)+1,YEAR(refer_date)) + 543 AS year_bud,
                  SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
                  SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
                  FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
                  IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
                  FROM referout r 
                  LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
                  LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
                  WHERE r.refer_date BETWEEN ? AND ?		
                  GROUP BY r.vn) AS a
                  WHERE pdx IN ("Z515")
                  GROUP BY year_bud
                  ORDER BY year_bud',[$start_date_y,$end_date]);
            $refer_y = array_column($refer_year,'year_bud');
            $refer_opd_y = array_column($refer_year,'opd');
            $refer_ipd_y = array_column($refer_year,'ipd'); 

            $refer_list = DB::connection('hosxp')->select('select
                  o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,pmh.cc_persist_disease AS pmh,
                  GROUP_CONCAT(c1.`name`) AS "clinic",r.department,r.refer_point,o.vstdate,o.vsttime,
                  IFNULL(v.pdx,a.pdx) AS pdx,r.refer_date,r.refer_time,r.pre_diagnosis,r.pdx AS pdx_refer,h.`name` AS refer_hos
                  FROM referout r 
                  LEFT JOIN ovst o ON o.vn=r.vn
                  LEFT JOIN vn_stat v ON v.vn=r.vn 
                  LEFT JOIN an_stat a ON a.an=r.vn
                  LEFT JOIN clinicmember c ON c.hn=r.hn
                  LEFT JOIN clinic c1 ON c1.clinic=c.clinic
                  LEFT JOIN opd_ill_history pmh ON pmh.hn=r.hn
                  LEFT JOIN patient p ON p.hn=r.hn
                  LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
                  WHERE r.refer_date BETWEEN ? AND ?		 
                  AND (r.pdx IN ("Z515")
                  OR v.pdx IN ("Z515")
                  OR a.pdx IN ("Z515")) 
                  GROUP BY r.vn							
                  ORDER BY r.refer_point,r.refer_date',[$start_date,$end_date]);

            return view('medicalrecord_diag.palliative_care',compact('budget_year_select','budget_year','diag_m','diag_visit_m',
                  'diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd','diag_hn_m_ipd',
                  'diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd','refer_m','refer_opd_m','refer_ipd_m',
                  'refer_y','refer_opd_y','refer_ipd_y','refer_list'));            
      }

//Create pneumonia
public function pneumonia(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_list = DB::connection('hosxp')->select('
            SELECT o.vn,o3.name AS ovstist,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" 
            LEFT JOIN ovstist o3 ON o3.ovstist=o.ovstist
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("J128","J159","J188","J189") 
            OR v.dx0 IN ("J128","J159","J188","J189") 
            OR v.dx1 IN ("J128","J159","J188","J189") 
            OR v.dx2 IN ("J128","J159","J188","J189") 
            OR v.dx3 IN ("J128","J159","J188","J189") 
            OR v.dx4 IN ("J128","J159","J188","J189") 
            OR v.dx5 IN ("J128","J159","J188","J189"))
            GROUP BY o.vn ');

      $diag_month = DB::connection('hosxp')->select('select 
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
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT vn) AS "visit"
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("J128","J159","J188","J189")
            OR v.dx0 IN ("J128","J159","J188","J189")
            OR v.dx1 IN ("J128","J159","J188","J189")
            OR v.dx2 IN ("J128","J159","J188","J189")
            OR v.dx3 IN ("J128","J159","J188","J189")
            OR v.dx4 IN ("J128","J159","J188","J189")
            OR v.dx5 IN ("J128","J159","J188","J189"))) AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $diag_m = array_column($diag_month,'month');
      $diag_visit_m = array_column($diag_month,'visit');
      $diag_hn_m = array_column($diag_month,'hn');

      $diag_year = DB::connection('hosxp')->select('select 
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            COUNT(DISTINCT vn) as visit,COUNT(DISTINCT hn) as hn
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND (v.pdx IN ("J128","J159","J188","J189") 
            OR v.dx0 IN ("J128","J159","J188","J189") 
            OR v.dx1 IN ("J128","J159","J188","J189") 
            OR v.dx2 IN ("J128","J159","J188","J189") 
            OR v.dx3 IN ("J128","J159","J188","J189") 
            OR v.dx4 IN ("J128","J159","J188","J189") 
            OR v.dx5 IN ("J128","J159","J188","J189"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y = array_column($diag_year,'year_bud');
      $diag_visit_y = array_column($diag_year,'visit');
      $diag_hn_y = array_column($diag_year,'hn');

      $diag_list_ipd = DB::connection('hosxp')->select('
            SELECT i.an,i.hn,i.regdate,i.regtime,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            a.age_y,CONCAT(i.pttype," [",p1.hipdata_code,"]") AS pttype,i.prediag,a.pdx,GROUP_CONCAT(i1.icd10) AS dx,
            d.`name` AS dx_doctor,CONCAT(h.`name`," [",r.pdx,"]") AS refer,i.dchdate,i.dchtime 
            FROM ipt i
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an AND i1.diagtype <>"1" 
            LEFT JOIN referout r ON r.vn=i.an
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=i.hn
            LEFT JOIN pttype p1 ON p1.pttype=i.pttype
            LEFT JOIN doctor d ON d.`code`=a.dx_doctor
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            AND (a.pdx IN ("J128","J159","J188","J189") 
            OR a.dx0 IN ("J128","J159","J188","J189") 
            OR a.dx1 IN ("J128","J159","J188","J189") 
            OR a.dx2 IN ("J128","J159","J188","J189") 
            OR a.dx3 IN ("J128","J159","J188","J189") 
            OR a.dx4 IN ("J128","J159","J188","J189") 
            OR a.dx5 IN ("J128","J159","J188","J189"))
            GROUP BY i.an ');

      $diag_month_ipd = DB::connection('hosxp')->select('select 
            CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(dchdate)+543,2))
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT an) AS "an"
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("J128","J159","J188","J189") 
            OR a.dx0 IN ("J128","J159","J188","J189") 
            OR a.dx1 IN ("J128","J159","J188","J189") 
            OR a.dx2 IN ("J128","J159","J188","J189") 
            OR a.dx3 IN ("J128","J159","J188","J189") 
            OR a.dx4 IN ("J128","J159","J188","J189") 
            OR a.dx5 IN ("J128","J159","J188","J189"))) AS a
            GROUP BY MONTH(dchdate)
            ORDER BY YEAR(dchdate) , MONTH(dchdate)');
      $diag_m_ipd = array_column($diag_month_ipd,'month');
      $diag_an_m_ipd = array_column($diag_month_ipd,'an');
      $diag_hn_m_ipd = array_column($diag_month_ipd,'hn');

      $diag_year_ipd = DB::connection('hosxp')->select('select 
            IF(MONTH(dchdate)>9,YEAR(dchdate)+1,YEAR(dchdate)) + 543 AS year_bud,
            COUNT(DISTINCT an) as an,COUNT(DISTINCT hn) as hn
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("J128","J159","J188","J189") 
            OR a.dx0 IN ("J128","J159","J188","J189") 
            OR a.dx1 IN ("J128","J159","J188","J189") 
            OR a.dx2 IN ("J128","J159","J188","J189") 
            OR a.dx3 IN ("J128","J159","J188","J189") 
            OR a.dx4 IN ("J128","J159","J188","J189") 
            OR a.dx5 IN ("J128","J159","J188","J189"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

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
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("J128","J159","J188","J189") 
            GROUP BY MONTH(refer_date)
            ORDER BY YEAR(refer_date) , MONTH(refer_date)');
      $refer_m = array_column($refer_month,'month');
      $refer_opd_m = array_column($refer_month,'opd'); 
      $refer_ipd_m = array_column($refer_month,'ipd');     

      $refer_year = DB::connection('hosxp')->select('select 
            IF(MONTH(refer_date)>9,YEAR(refer_date)+1,YEAR(refer_date)) + 543 AS year_bud,
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("J128","J159","J188","J189") 
            GROUP BY year_bud
            ORDER BY year_bud');
      $refer_y = array_column($refer_year,'year_bud');
      $refer_opd_y = array_column($refer_year,'opd');
      $refer_ipd_y = array_column($refer_year,'ipd'); 

      $refer_list = DB::connection('hosxp')->select('select
            o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,pmh.cc_persist_disease AS pmh,
            GROUP_CONCAT(c1.`name`) AS "clinic",r.department,r.refer_point,o.vstdate,o.vsttime,
            IFNULL(v.pdx,a.pdx) AS pdx,r.refer_date,r.refer_time,r.pre_diagnosis,r.pdx AS pdx_refer,h.`name` AS refer_hos
            FROM referout r 
            LEFT JOIN ovst o ON o.vn=r.vn
            LEFT JOIN vn_stat v ON v.vn=r.vn 
            LEFT JOIN an_stat a ON a.an=r.vn
            LEFT JOIN clinicmember c ON c.hn=r.hn
            LEFT JOIN clinic c1 ON c1.clinic=c.clinic
            LEFT JOIN opd_ill_history pmh ON pmh.hn=r.hn
            LEFT JOIN patient p ON p.hn=r.hn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		 
            AND (r.pdx IN ("J128","J159","J188","J189") 
            OR v.pdx IN ("J128","J159","J188","J189") 
            OR a.pdx IN ("J128","J159","J188","J189")) 
            GROUP BY r.vn							
            ORDER BY r.refer_point,r.refer_date');

      return view('medicalrecord_diag.pneumonia',compact('budget_year_select','budget_year','diag_m','diag_visit_m',
            'diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd','diag_hn_m_ipd',
            'diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd','refer_m','refer_opd_m','refer_ipd_m',
            'refer_y','refer_opd_y','refer_ipd_y','refer_list'));            
}

//Create sepsis
public function sepsis(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_list = DB::connection('hosxp')->select('
            SELECT o.vn,o3.name AS ovstist,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" 
            LEFT JOIN ovstist o3 ON o3.ovstist=o.ovstist
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("A419","R651","R572") 
            OR v.dx0 IN ("A419","R651","R572") 
            OR v.dx1 IN ("A419","R651","R572")  
            OR v.dx2 IN ("A419","R651","R572")  
            OR v.dx3 IN ("A419","R651","R572") 
            OR v.dx4 IN ("A419","R651","R572") 
            OR v.dx5 IN ("A419","R651","R572"))
            GROUP BY o.vn ');

      $diag_month = DB::connection('hosxp')->select('select 
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
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT vn) AS "visit"
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("A419","R651","R572")
            OR v.dx0 IN ("A419","R651","R572")
            OR v.dx1 IN ("A419","R651","R572")
            OR v.dx2 IN ("A419","R651","R572")
            OR v.dx3 IN ("A419","R651","R572")
            OR v.dx4 IN ("A419","R651","R572")
            OR v.dx5 IN ("A419","R651","R572"))) AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $diag_m = array_column($diag_month,'month');
      $diag_visit_m = array_column($diag_month,'visit');
      $diag_hn_m = array_column($diag_month,'hn');

      $diag_year = DB::connection('hosxp')->select('select 
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            COUNT(DISTINCT vn) as visit,COUNT(DISTINCT hn) as hn
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND (v.pdx IN ("A419","R651","R572")
            OR v.dx0 IN ("A419","R651","R572")
            OR v.dx1 IN ("A419","R651","R572")
            OR v.dx2 IN ("A419","R651","R572")
            OR v.dx3 IN ("A419","R651","R572")
            OR v.dx4 IN ("A419","R651","R572")
            OR v.dx5 IN ("A419","R651","R572"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y = array_column($diag_year,'year_bud');
      $diag_visit_y = array_column($diag_year,'visit');
      $diag_hn_y = array_column($diag_year,'hn');

      $diag_list_ipd = DB::connection('hosxp')->select('
            SELECT i.an,i.hn,i.regdate,i.regtime,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            a.age_y,CONCAT(i.pttype," [",p1.hipdata_code,"]") AS pttype,i.prediag,a.pdx,GROUP_CONCAT(i1.icd10) AS dx,
            d.`name` AS dx_doctor,CONCAT(h.`name`," [",r.pdx,"]") AS refer,i.dchdate,i.dchtime 
            FROM ipt i
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an AND i1.diagtype <>"1" 
            LEFT JOIN referout r ON r.vn=i.an
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=i.hn
            LEFT JOIN pttype p1 ON p1.pttype=i.pttype
            LEFT JOIN doctor d ON d.`code`=a.dx_doctor
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            AND (a.pdx IN ("A419","R651","R572")
            OR a.dx0 IN ("A419","R651","R572")
            OR a.dx1 IN ("A419","R651","R572")
            OR a.dx2 IN ("A419","R651","R572")
            OR a.dx3 IN ("A419","R651","R572")
            OR a.dx4 IN ("A419","R651","R572")
            OR a.dx5 IN ("A419","R651","R572"))
            GROUP BY i.an ');

      $diag_month_ipd = DB::connection('hosxp')->select('select 
            CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(dchdate)+543,2))
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT an) AS "an"
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("A419","R651","R572")
            OR a.dx0 IN ("A419","R651","R572")
            OR a.dx1 IN ("A419","R651","R572")
            OR a.dx2 IN ("A419","R651","R572") 
            OR a.dx3 IN ("A419","R651","R572")
            OR a.dx4 IN ("A419","R651","R572")
            OR a.dx5 IN ("A419","R651","R572"))) AS a
            GROUP BY MONTH(dchdate)
            ORDER BY YEAR(dchdate) , MONTH(dchdate)');
      $diag_m_ipd = array_column($diag_month_ipd,'month');
      $diag_an_m_ipd = array_column($diag_month_ipd,'an');
      $diag_hn_m_ipd = array_column($diag_month_ipd,'hn');

      $diag_year_ipd = DB::connection('hosxp')->select('select 
            IF(MONTH(dchdate)>9,YEAR(dchdate)+1,YEAR(dchdate)) + 543 AS year_bud,
            COUNT(DISTINCT an) as an,COUNT(DISTINCT hn) as hn
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("A419","R651","R572")
            OR a.dx0 IN ("A419","R651","R572")
            OR a.dx1 IN ("A419","R651","R572")
            OR a.dx2 IN ("A419","R651","R572")
            OR a.dx3 IN ("A419","R651","R572")
            OR a.dx4 IN ("A419","R651","R572")
            OR a.dx5 IN ("A419","R651","R572"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

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
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("A419","R651","R572")
            GROUP BY MONTH(refer_date)
            ORDER BY YEAR(refer_date) , MONTH(refer_date)');
      $refer_m = array_column($refer_month,'month');
      $refer_opd_m = array_column($refer_month,'opd'); 
      $refer_ipd_m = array_column($refer_month,'ipd');     

      $refer_year = DB::connection('hosxp')->select('select 
            IF(MONTH(refer_date)>9,YEAR(refer_date)+1,YEAR(refer_date)) + 543 AS year_bud,
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("A419","R651","R572")
            GROUP BY year_bud
            ORDER BY year_bud');
      $refer_y = array_column($refer_year,'year_bud');
      $refer_opd_y = array_column($refer_year,'opd');
      $refer_ipd_y = array_column($refer_year,'ipd'); 

      $refer_list = DB::connection('hosxp')->select('select
            o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,pmh.cc_persist_disease AS pmh,
            GROUP_CONCAT(c1.`name`) AS "clinic",r.department,r.refer_point,o.vstdate,o.vsttime,
            IFNULL(v.pdx,a.pdx) AS pdx,r.refer_date,r.refer_time,r.pre_diagnosis,r.pdx AS pdx_refer,h.`name` AS refer_hos
            FROM referout r 
            LEFT JOIN ovst o ON o.vn=r.vn
            LEFT JOIN vn_stat v ON v.vn=r.vn 
            LEFT JOIN an_stat a ON a.an=r.vn
            LEFT JOIN clinicmember c ON c.hn=r.hn
            LEFT JOIN clinic c1 ON c1.clinic=c.clinic
            LEFT JOIN opd_ill_history pmh ON pmh.hn=r.hn
            LEFT JOIN patient p ON p.hn=r.hn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		 
            AND (r.pdx IN ("A419","R651","R572")
            OR v.pdx IN ("A419","R651","R572")
            OR a.pdx IN ("A419","R651","R572")) 
            GROUP BY r.vn							
            ORDER BY r.refer_point,r.refer_date');

      return view('medicalrecord_diag.sepsis',compact('budget_year_select','budget_year','diag_m','diag_visit_m',
            'diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd','diag_hn_m_ipd',
            'diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd','refer_m','refer_opd_m','refer_ipd_m',
            'refer_y','refer_opd_y','refer_ipd_y','refer_list'));            
}

//Create septic_shock
public function septic_shock(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_list = DB::connection('hosxp')->select('
            SELECT o.vn,o3.name AS ovstist,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" 
            LEFT JOIN ovstist o3 ON o3.ovstist=o.ovstist
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("R572")
            OR v.dx0 IN ("R572")
            OR v.dx1 IN ("R572") 
            OR v.dx2 IN ("R572")  
            OR v.dx3 IN ("R572")
            OR v.dx4 IN ("R572") 
            OR v.dx5 IN ("R572"))
            GROUP BY o.vn ');

      $diag_month = DB::connection('hosxp')->select('select 
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
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT vn) AS "visit"
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("R572")
            OR v.dx0 IN ("R572")
            OR v.dx1 IN ("R572")
            OR v.dx2 IN ("R572")
            OR v.dx3 IN ("R572")
            OR v.dx4 IN ("R572")
            OR v.dx5 IN ("R572"))) AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $diag_m = array_column($diag_month,'month');
      $diag_visit_m = array_column($diag_month,'visit');
      $diag_hn_m = array_column($diag_month,'hn');

      $diag_year = DB::connection('hosxp')->select('select 
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            COUNT(DISTINCT vn) as visit,COUNT(DISTINCT hn) as hn
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND (v.pdx IN ("R572")
            OR v.dx0 IN ("R572")
            OR v.dx1 IN ("R572")
            OR v.dx2 IN ("R572")
            OR v.dx3 IN ("R572")
            OR v.dx4 IN ("R572")
            OR v.dx5 IN ("R572"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y = array_column($diag_year,'year_bud');
      $diag_visit_y = array_column($diag_year,'visit');
      $diag_hn_y = array_column($diag_year,'hn');

      $diag_list_ipd = DB::connection('hosxp')->select('
            SELECT i.an,i.hn,i.regdate,i.regtime,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            a.age_y,CONCAT(i.pttype," [",p1.hipdata_code,"]") AS pttype,i.prediag,a.pdx,GROUP_CONCAT(i1.icd10) AS dx,
            d.`name` AS dx_doctor,CONCAT(h.`name`," [",r.pdx,"]") AS refer,i.dchdate,i.dchtime 
            FROM ipt i
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an AND i1.diagtype <>"1" 
            LEFT JOIN referout r ON r.vn=i.an
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=i.hn
            LEFT JOIN pttype p1 ON p1.pttype=i.pttype
            LEFT JOIN doctor d ON d.`code`=a.dx_doctor
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            AND (a.pdx IN ("R572")
            OR a.dx0 IN ("R572")
            OR a.dx1 IN ("R572")
            OR a.dx2 IN ("R572")
            OR a.dx3 IN ("R572")
            OR a.dx4 IN ("R572")
            OR a.dx5 IN ("R572"))
            GROUP BY i.an ');

      $diag_month_ipd = DB::connection('hosxp')->select('select 
            CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(dchdate)+543,2))
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT an) AS "an"
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("R572")
            OR a.dx0 IN ("R572")
            OR a.dx1 IN ("R572")
            OR a.dx2 IN ("R572") 
            OR a.dx3 IN ("R572")
            OR a.dx4 IN ("R572")
            OR a.dx5 IN ("R572"))) AS a
            GROUP BY MONTH(dchdate)
            ORDER BY YEAR(dchdate) , MONTH(dchdate)');
      $diag_m_ipd = array_column($diag_month_ipd,'month');
      $diag_an_m_ipd = array_column($diag_month_ipd,'an');
      $diag_hn_m_ipd = array_column($diag_month_ipd,'hn');

      $diag_year_ipd = DB::connection('hosxp')->select('select 
            IF(MONTH(dchdate)>9,YEAR(dchdate)+1,YEAR(dchdate)) + 543 AS year_bud,
            COUNT(DISTINCT an) as an,COUNT(DISTINCT hn) as hn
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("R572")
            OR a.dx0 IN ("R572")
            OR a.dx1 IN ("R572")
            OR a.dx2 IN ("R572")
            OR a.dx3 IN ("R572")
            OR a.dx4 IN ("R572")
            OR a.dx5 IN ("R572"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

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
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("R572")
            GROUP BY MONTH(refer_date)
            ORDER BY YEAR(refer_date) , MONTH(refer_date)');
      $refer_m = array_column($refer_month,'month');
      $refer_opd_m = array_column($refer_month,'opd'); 
      $refer_ipd_m = array_column($refer_month,'ipd');     

      $refer_year = DB::connection('hosxp')->select('select 
            IF(MONTH(refer_date)>9,YEAR(refer_date)+1,YEAR(refer_date)) + 543 AS year_bud,
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("R572")
            GROUP BY year_bud
            ORDER BY year_bud');
      $refer_y = array_column($refer_year,'year_bud');
      $refer_opd_y = array_column($refer_year,'opd');
      $refer_ipd_y = array_column($refer_year,'ipd'); 

      $refer_list = DB::connection('hosxp')->select('select
            o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,pmh.cc_persist_disease AS pmh,
            GROUP_CONCAT(c1.`name`) AS "clinic",r.department,r.refer_point,o.vstdate,o.vsttime,
            IFNULL(v.pdx,a.pdx) AS pdx,r.refer_date,r.refer_time,r.pre_diagnosis,r.pdx AS pdx_refer,h.`name` AS refer_hos
            FROM referout r 
            LEFT JOIN ovst o ON o.vn=r.vn
            LEFT JOIN vn_stat v ON v.vn=r.vn 
            LEFT JOIN an_stat a ON a.an=r.vn
            LEFT JOIN clinicmember c ON c.hn=r.hn
            LEFT JOIN clinic c1 ON c1.clinic=c.clinic
            LEFT JOIN opd_ill_history pmh ON pmh.hn=r.hn
            LEFT JOIN patient p ON p.hn=r.hn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		 
            AND (r.pdx IN ("R572")
            OR v.pdx IN ("R572")
            OR a.pdx IN ("R572")) 
            GROUP BY r.vn							
            ORDER BY r.refer_point,r.refer_date');

      return view('medicalrecord_diag.septic_shock',compact('budget_year_select','budget_year','diag_m','diag_visit_m',
            'diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd','diag_hn_m_ipd',
            'diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd','refer_m','refer_opd_m','refer_ipd_m',
            'refer_y','refer_opd_y','refer_ipd_y','refer_list'));            
}

//Create stroke
public function stroke(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_list = DB::connection('hosxp')->select('
            SELECT o.vn,o3.name AS ovstist,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" 
            LEFT JOIN ovstist o3 ON o3.ovstist=o.ovstist
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("I64")
            OR v.dx0 IN ("I64")
            OR v.dx1 IN ("I64")
            OR v.dx2 IN ("I64")
            OR v.dx3 IN ("I64")
            OR v.dx4 IN ("I64")
            OR v.dx5 IN ("I64"))
            GROUP BY o.vn ');

      $diag_month = DB::connection('hosxp')->select('select 
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
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT vn) AS "visit"
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx IN ("I64")
            OR v.dx0 IN ("I64")
            OR v.dx1 IN ("I64")
            OR v.dx2 IN ("I64")
            OR v.dx3 IN ("I64")
            OR v.dx4 IN ("I64")
            OR v.dx5 IN ("I64"))) AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $diag_m = array_column($diag_month,'month');
      $diag_visit_m = array_column($diag_month,'visit');
      $diag_hn_m = array_column($diag_month,'hn');

      $diag_year = DB::connection('hosxp')->select('select 
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            COUNT(DISTINCT vn) as visit,COUNT(DISTINCT hn) as hn
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND (v.pdx IN ("I64")
            OR v.dx0 IN ("I64")
            OR v.dx1 IN ("I64")
            OR v.dx2 IN ("I64")
            OR v.dx3 IN ("I64")
            OR v.dx4 IN ("I64")
            OR v.dx5 IN ("I64"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y = array_column($diag_year,'year_bud');
      $diag_visit_y = array_column($diag_year,'visit');
      $diag_hn_y = array_column($diag_year,'hn');

      $diag_list_ipd = DB::connection('hosxp')->select('
            SELECT i.an,i.hn,i.regdate,i.regtime,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            a.age_y,CONCAT(i.pttype," [",p1.hipdata_code,"]") AS pttype,i.prediag,a.pdx,GROUP_CONCAT(i1.icd10) AS dx,
            d.`name` AS dx_doctor,CONCAT(h.`name`," [",r.pdx,"]") AS refer,i.dchdate,i.dchtime 
            FROM ipt i
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an AND i1.diagtype <>"1" 
            LEFT JOIN referout r ON r.vn=i.an
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=i.hn
            LEFT JOIN pttype p1 ON p1.pttype=i.pttype
            LEFT JOIN doctor d ON d.`code`=a.dx_doctor
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            AND (a.pdx IN ("I64")
            OR a.dx0 IN ("I64")
            OR a.dx1 IN ("I64")
            OR a.dx2 IN ("I64")
            OR a.dx3 IN ("I64")
            OR a.dx4 IN ("I64")
            OR a.dx5 IN ("I64"))
            GROUP BY i.an ');

      $diag_month_ipd = DB::connection('hosxp')->select('select 
            CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(dchdate)+543,2))
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT an) AS "an"
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("I64")
            OR a.dx0 IN ("I64")
            OR a.dx1 IN ("I64")
            OR a.dx2 IN ("I64")
            OR a.dx3 IN ("I64")
            OR a.dx4 IN ("I64")
            OR a.dx5 IN ("I64"))) AS a
            GROUP BY MONTH(dchdate)
            ORDER BY YEAR(dchdate) , MONTH(dchdate)');
      $diag_m_ipd = array_column($diag_month_ipd,'month');
      $diag_an_m_ipd = array_column($diag_month_ipd,'an');
      $diag_hn_m_ipd = array_column($diag_month_ipd,'hn');

      $diag_year_ipd = DB::connection('hosxp')->select('select 
            IF(MONTH(dchdate)>9,YEAR(dchdate)+1,YEAR(dchdate)) + 543 AS year_bud,
            COUNT(DISTINCT an) as an,COUNT(DISTINCT hn) as hn
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" 
            AND (a.pdx IN ("I64")
            OR a.dx0 IN ("I64")
            OR a.dx1 IN ("I64")
            OR a.dx2 IN ("I64")
            OR a.dx3 IN ("I64")
            OR a.dx4 IN ("I64")
            OR a.dx5 IN ("I64"))) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

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
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("I64")
            GROUP BY MONTH(refer_date)
            ORDER BY YEAR(refer_date) , MONTH(refer_date)');
      $refer_m = array_column($refer_month,'month');
      $refer_opd_m = array_column($refer_month,'opd'); 
      $refer_ipd_m = array_column($refer_month,'ipd');     

      $refer_year = DB::connection('hosxp')->select('select 
            IF(MONTH(refer_date)>9,YEAR(refer_date)+1,YEAR(refer_date)) + 543 AS year_bud,
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE pdx IN ("I64")
            GROUP BY year_bud
            ORDER BY year_bud');
      $refer_y = array_column($refer_year,'year_bud');
      $refer_opd_y = array_column($refer_year,'opd');
      $refer_ipd_y = array_column($refer_year,'ipd'); 

      $refer_list = DB::connection('hosxp')->select('select
            o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,pmh.cc_persist_disease AS pmh,
            GROUP_CONCAT(c1.`name`) AS "clinic",r.department,r.refer_point,o.vstdate,o.vsttime,
            IFNULL(v.pdx,a.pdx) AS pdx,r.refer_date,r.refer_time,r.pre_diagnosis,r.pdx AS pdx_refer,h.`name` AS refer_hos
            FROM referout r 
            LEFT JOIN ovst o ON o.vn=r.vn
            LEFT JOIN vn_stat v ON v.vn=r.vn 
            LEFT JOIN an_stat a ON a.an=r.vn
            LEFT JOIN clinicmember c ON c.hn=r.hn
            LEFT JOIN clinic c1 ON c1.clinic=c.clinic
            LEFT JOIN opd_ill_history pmh ON pmh.hn=r.hn
            LEFT JOIN patient p ON p.hn=r.hn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		 
            AND (r.pdx IN ("I64")
            OR v.pdx IN ("I64")
            OR a.pdx IN ("I64")) 
            GROUP BY r.vn							
            ORDER BY r.refer_point,r.refer_date');

      return view('medicalrecord_diag.stroke',compact('budget_year_select','budget_year','diag_m','diag_visit_m',
            'diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd','diag_hn_m_ipd',
            'diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd','refer_m','refer_opd_m','refer_ipd_m',
            'refer_y','refer_opd_y','refer_ipd_y','refer_list'));            
}

//Create Head_Injury
public function Head_Injury(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_list = DB::connection('hosxp')->select('
            SELECT o.vn,o3.name AS ovstist,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" 
            LEFT JOIN ovstist o3 ON o3.ovstist=o.ovstist
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "S00" AND "S09" 
            OR LEFT(v.dx0,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx1,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx2,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx3,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx4,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx5,3) BETWEEN "S00" AND "S09")
            GROUP BY o.vn ');

      $diag_month = DB::connection('hosxp')->select('select 
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
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT vn) AS "visit"
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx0,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx1,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx2,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx3,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx4,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx5,3) BETWEEN "S00" AND "S09")) AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $diag_m = array_column($diag_month,'month');
      $diag_visit_m = array_column($diag_month,'visit');
      $diag_hn_m = array_column($diag_month,'hn');

      $diag_year = DB::connection('hosxp')->select('select 
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            COUNT(DISTINCT vn) as visit,COUNT(DISTINCT hn) as hn
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx0,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx1,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx2,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx3,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx4,3) BETWEEN "S00" AND "S09"
            OR LEFT(v.dx5,3) BETWEEN "S00" AND "S09")) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y = array_column($diag_year,'year_bud');
      $diag_visit_y = array_column($diag_year,'visit');
      $diag_hn_y = array_column($diag_year,'hn');

      $diag_list_ipd = DB::connection('hosxp')->select('
            SELECT i.an,i.hn,i.regdate,i.regtime,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            a.age_y,CONCAT(i.pttype," [",p1.hipdata_code,"]") AS pttype,i.prediag,a.pdx,GROUP_CONCAT(i1.icd10) AS dx,
            d.`name` AS dx_doctor,CONCAT(h.`name`," [",r.pdx,"]") AS refer,i.dchdate,i.dchtime 
            FROM ipt i
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an AND i1.diagtype <>"1" 
            LEFT JOIN referout r ON r.vn=i.an
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=i.hn
            LEFT JOIN pttype p1 ON p1.pttype=i.pttype
            LEFT JOIN doctor d ON d.`code`=a.dx_doctor
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            AND (LEFT(a.pdx,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx0,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx1,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx2,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx3,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx4,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx5,3) BETWEEN "S00" AND "S09")
            GROUP BY i.an ');

      $diag_month_ipd = DB::connection('hosxp')->select('select 
            CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(dchdate)+543,2))
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT an) AS "an"
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
            AND (LEFT(a.pdx,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx0,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx1,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx2,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx3,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx4,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx5,3) BETWEEN "S00" AND "S09")) AS a
            GROUP BY MONTH(dchdate)
            ORDER BY YEAR(dchdate) , MONTH(dchdate)');
      $diag_m_ipd = array_column($diag_month_ipd,'month');
      $diag_an_m_ipd = array_column($diag_month_ipd,'an');
      $diag_hn_m_ipd = array_column($diag_month_ipd,'hn');

      $diag_year_ipd = DB::connection('hosxp')->select('select 
            IF(MONTH(dchdate)>9,YEAR(dchdate)+1,YEAR(dchdate)) + 543 AS year_bud,
            COUNT(DISTINCT an) as an,COUNT(DISTINCT hn) as hn
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" 
            AND (LEFT(a.pdx,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx0,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx1,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx2,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx3,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx4,3) BETWEEN "S00" AND "S09"
            OR LEFT(a.dx5,3) BETWEEN "S00" AND "S09")) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

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
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE LEFT(pdx,3) BETWEEN "S00" AND "S09"
            GROUP BY MONTH(refer_date)
            ORDER BY YEAR(refer_date) , MONTH(refer_date)');
      $refer_m = array_column($refer_month,'month');
      $refer_opd_m = array_column($refer_month,'opd'); 
      $refer_ipd_m = array_column($refer_month,'ipd');     

      $refer_year = DB::connection('hosxp')->select('select 
            IF(MONTH(refer_date)>9,YEAR(refer_date)+1,YEAR(refer_date)) + 543 AS year_bud,
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE LEFT(pdx,3) BETWEEN "S00" AND "S09"
            GROUP BY year_bud
            ORDER BY year_bud');
      $refer_y = array_column($refer_year,'year_bud');
      $refer_opd_y = array_column($refer_year,'opd');
      $refer_ipd_y = array_column($refer_year,'ipd'); 

      $refer_list = DB::connection('hosxp')->select('select
            o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,pmh.cc_persist_disease AS pmh,
            GROUP_CONCAT(c1.`name`) AS "clinic",r.department,r.refer_point,o.vstdate,o.vsttime,
            IFNULL(v.pdx,a.pdx) AS pdx,r.refer_date,r.refer_time,r.pre_diagnosis,r.pdx AS pdx_refer,h.`name` AS refer_hos
            FROM referout r 
            LEFT JOIN ovst o ON o.vn=r.vn
            LEFT JOIN vn_stat v ON v.vn=r.vn 
            LEFT JOIN an_stat a ON a.an=r.vn
            LEFT JOIN clinicmember c ON c.hn=r.hn
            LEFT JOIN clinic c1 ON c1.clinic=c.clinic
            LEFT JOIN opd_ill_history pmh ON pmh.hn=r.hn
            LEFT JOIN patient p ON p.hn=r.hn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		 
            AND (LEFT(r.pdx,3) BETWEEN "S00" AND "S09" OR LEFT(v.pdx,3) BETWEEN "S00" AND "S09" 
            OR LEFT(a.pdx,3) BETWEEN "S00" AND "S09") 
            GROUP BY r.vn							
            ORDER BY r.refer_point,r.refer_date');

      return view('medicalrecord_diag.head_injury',compact('budget_year_select','budget_year','diag_m','diag_visit_m',
            'diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd','diag_hn_m_ipd',
            'diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd','refer_m','refer_opd_m','refer_ipd_m',
            'refer_y','refer_opd_y','refer_ipd_y','refer_list'));            
}

//Create fracture (กระดูกแตกหัก)
public function fracture(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_list = DB::connection('hosxp')->select('
            SELECT o.vn,o3.name AS ovstist,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" 
            LEFT JOIN ovstist o3 ON o3.ovstist=o.ovstist
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "S72" AND "S72" 
            OR LEFT(v.dx0,3) BETWEEN "S72" AND "S72" 
            OR LEFT(v.dx1,3) BETWEEN "S72" AND "S72" 
            OR LEFT(v.dx2,3) BETWEEN "S72" AND "S72" 
            OR LEFT(v.dx3,3) BETWEEN "S72" AND "S72" 
            OR LEFT(v.dx4,3) BETWEEN "S72" AND "S72" 
            OR LEFT(v.dx5,3) BETWEEN "S72" AND "S72")
            GROUP BY o.vn ');

      $diag_month = DB::connection('hosxp')->select('select 
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
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT vn) AS "visit"
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "S72" AND "S72" 
            OR LEFT(v.dx0,3) BETWEEN "S72" AND "S72"
            OR LEFT(v.dx1,3) BETWEEN "S72" AND "S72"
            OR LEFT(v.dx2,3) BETWEEN "S72" AND "S72"
            OR LEFT(v.dx3,3) BETWEEN "S72" AND "S72"
            OR LEFT(v.dx4,3) BETWEEN "S72" AND "S72"
            OR LEFT(v.dx5,3) BETWEEN "S72" AND "S72")) AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $diag_m = array_column($diag_month,'month');
      $diag_visit_m = array_column($diag_month,'visit');
      $diag_hn_m = array_column($diag_month,'hn');

      $diag_year = DB::connection('hosxp')->select('select 
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            COUNT(DISTINCT vn) as visit,COUNT(DISTINCT hn) as hn
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn									
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "S72" AND "S72"
            OR LEFT(v.dx0,3) BETWEEN "S72" AND "S72"
            OR LEFT(v.dx1,3) BETWEEN "S72" AND "S72"
            OR LEFT(v.dx2,3) BETWEEN "S72" AND "S72"
            OR LEFT(v.dx3,3) BETWEEN "S72" AND "S72"
            OR LEFT(v.dx4,3) BETWEEN "S72" AND "S72"
            OR LEFT(v.dx5,3) BETWEEN "S72" AND "S72")) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y = array_column($diag_year,'year_bud');
      $diag_visit_y = array_column($diag_year,'visit');
      $diag_hn_y = array_column($diag_year,'hn');

      $diag_list_ipd = DB::connection('hosxp')->select('
            SELECT i.an,i.hn,i.regdate,i.regtime,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            a.age_y,CONCAT(i.pttype," [",p1.hipdata_code,"]") AS pttype,i.prediag,a.pdx,GROUP_CONCAT(i1.icd10) AS dx,
            d.`name` AS dx_doctor,CONCAT(h.`name`," [",r.pdx,"]") AS refer,i.dchdate,i.dchtime 
            FROM ipt i
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an AND i1.diagtype <>"1" 
            LEFT JOIN referout r ON r.vn=i.an
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=i.hn
            LEFT JOIN pttype p1 ON p1.pttype=i.pttype
            LEFT JOIN doctor d ON d.`code`=a.dx_doctor
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            AND (LEFT(a.pdx,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx0,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx1,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx2,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx3,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx4,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx5,3) BETWEEN "S72" AND "S72")
            GROUP BY i.an ');

      $diag_month_ipd = DB::connection('hosxp')->select('select 
            CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(dchdate)+543,2))
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT an) AS "an"
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
            AND (LEFT(a.pdx,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx0,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx1,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx2,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx3,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx4,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx5,3) BETWEEN "S72" AND "S72")) AS a
            GROUP BY MONTH(dchdate)
            ORDER BY YEAR(dchdate) , MONTH(dchdate)');
      $diag_m_ipd = array_column($diag_month_ipd,'month');
      $diag_an_m_ipd = array_column($diag_month_ipd,'an');
      $diag_hn_m_ipd = array_column($diag_month_ipd,'hn');

      $diag_year_ipd = DB::connection('hosxp')->select('select 
            IF(MONTH(dchdate)>9,YEAR(dchdate)+1,YEAR(dchdate)) + 543 AS year_bud,
            COUNT(DISTINCT an) as an,COUNT(DISTINCT hn) as hn
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an									
            WHERE i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" 
            AND (LEFT(a.pdx,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx0,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx1,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx2,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx3,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx4,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.dx5,3) BETWEEN "S72" AND "S72")) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

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
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE LEFT(pdx,3) BETWEEN "S72" AND "S72"
            GROUP BY MONTH(refer_date)
            ORDER BY YEAR(refer_date) , MONTH(refer_date)');
      $refer_m = array_column($refer_month,'month');
      $refer_opd_m = array_column($refer_month,'opd'); 
      $refer_ipd_m = array_column($refer_month,'ipd');     

      $refer_year = DB::connection('hosxp')->select('select 
            IF(MONTH(refer_date)>9,YEAR(refer_date)+1,YEAR(refer_date)) + 543 AS year_bud,
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE LEFT(pdx,3) BETWEEN "S72" AND "S72"
            GROUP BY year_bud
            ORDER BY year_bud');
      $refer_y = array_column($refer_year,'year_bud');
      $refer_opd_y = array_column($refer_year,'opd');
      $refer_ipd_y = array_column($refer_year,'ipd'); 

      $refer_list = DB::connection('hosxp')->select('select
            o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,pmh.cc_persist_disease AS pmh,
            GROUP_CONCAT(c1.`name`) AS "clinic",r.department,r.refer_point,o.vstdate,o.vsttime,
            IFNULL(v.pdx,a.pdx) AS pdx,r.refer_date,r.refer_time,r.pre_diagnosis,r.pdx AS pdx_refer,h.`name` AS refer_hos
            FROM referout r 
            LEFT JOIN ovst o ON o.vn=r.vn
            LEFT JOIN vn_stat v ON v.vn=r.vn 
            LEFT JOIN an_stat a ON a.an=r.vn
            LEFT JOIN clinicmember c ON c.hn=r.hn
            LEFT JOIN clinic c1 ON c1.clinic=c.clinic
            LEFT JOIN opd_ill_history pmh ON pmh.hn=r.hn
            LEFT JOIN patient p ON p.hn=r.hn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		 
            AND (LEFT(r.pdx,3) BETWEEN "S72" AND "S72" OR LEFT(v.pdx,3) BETWEEN "S72" AND "S72"
            OR LEFT(a.pdx,3) BETWEEN "S72" AND "S72") 
            GROUP BY r.vn							
            ORDER BY r.refer_point,r.refer_date');

      return view('medicalrecord_diag.fracture',compact('budget_year_select','budget_year','diag_m','diag_visit_m',
            'diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd','diag_hn_m_ipd',
            'diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd','refer_m','refer_opd_m','refer_ipd_m',
            'refer_y','refer_opd_y','refer_ipd_y','refer_list'));            
}
//Create trauma
public function trauma(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $diag_list = DB::connection('hosxp')->select('
            SELECT o.vn,o3.name AS ovstist,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" 
            LEFT JOIN ovstist o3 ON o3.ovstist=o.ovstist
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "S02" AND "S92" 
            OR LEFT(v.dx0,3) BETWEEN "S02" AND "S92"  
            OR LEFT(v.dx1,3) BETWEEN "S02" AND "S92" 
            OR LEFT(v.dx2,3) BETWEEN "S02" AND "S92" 
            OR LEFT(v.dx3,3) BETWEEN "S02" AND "S92" 
            OR LEFT(v.dx4,3) BETWEEN "S02" AND "S92" 
            OR LEFT(v.dx5,3) BETWEEN "S02" AND "S92" )
		AND o2.icd10 = "9918"
            GROUP BY o.vn ');

      $diag_month = DB::connection('hosxp')->select('select 
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
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT vn) AS "visit"
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn	
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" 								
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "S02" AND "S92" 
            OR LEFT(v.dx0,3) BETWEEN "S02" AND "S92"  
            OR LEFT(v.dx1,3) BETWEEN "S02" AND "S92" 
            OR LEFT(v.dx2,3) BETWEEN "S02" AND "S92" 
            OR LEFT(v.dx3,3) BETWEEN "S02" AND "S92" 
            OR LEFT(v.dx4,3) BETWEEN "S02" AND "S92" 
            OR LEFT(v.dx5,3) BETWEEN "S02" AND "S92")
		AND o2.icd10 = "9918") AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $diag_m = array_column($diag_month,'month');
      $diag_visit_m = array_column($diag_month,'visit');
      $diag_hn_m = array_column($diag_month,'hn');

      $diag_year = DB::connection('hosxp')->select('select 
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            COUNT(DISTINCT vn) as visit,COUNT(DISTINCT hn) as hn
            FROM (SELECT o.vn,o.hn,o.oqueue,o.vstdate,o.vsttime FROM ovst o 								
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" 									
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "S02" AND "S92" 
            OR LEFT(v.dx0,3) BETWEEN "S02" AND "S92"  
            OR LEFT(v.dx1,3) BETWEEN "S02" AND "S92" 
            OR LEFT(v.dx2,3) BETWEEN "S02" AND "S92" 
            OR LEFT(v.dx3,3) BETWEEN "S02" AND "S92" 
            OR LEFT(v.dx4,3) BETWEEN "S02" AND "S92" 
            OR LEFT(v.dx5,3) BETWEEN "S02" AND "S92" )
		AND o2.icd10 = "9918") AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y = array_column($diag_year,'year_bud');
      $diag_visit_y = array_column($diag_year,'visit');
      $diag_hn_y = array_column($diag_year,'hn');

      $diag_list_ipd = DB::connection('hosxp')->select('
            SELECT i.an,i.hn,i.regdate,i.regtime,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            a.age_y,CONCAT(i.pttype," [",p1.hipdata_code,"]") AS pttype,i.prediag,a.pdx,GROUP_CONCAT(i1.icd10) AS dx,
            d.`name` AS dx_doctor,CONCAT(h.`name`," [",r.pdx,"]") AS refer,i.dchdate,i.dchtime 
            FROM ipt i
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an AND i1.diagtype <>"1" 
            LEFT JOIN referout r ON r.vn=i.an
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=i.hn
            LEFT JOIN pttype p1 ON p1.pttype=i.pttype
            LEFT JOIN doctor d ON d.`code`=a.dx_doctor
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
            AND (LEFT(a.pdx,3) BETWEEN "S02" AND "S92" 
            OR LEFT(a.dx0,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.dx1,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.dx2,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.dx3,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.dx4,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.dx5,3) BETWEEN "S02" AND "S92")
            AND i1.icd10 = "9918"
            GROUP BY i.an ');

      $diag_month_ipd = DB::connection('hosxp')->select('select 
            CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(dchdate)+543,2))
            WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(dchdate)+543,2))
            END AS "month", COUNT(DISTINCT hn) AS "hn",COUNT(DISTINCT an) AS "an"
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an AND i1.diagtype <>"1"									
            WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
            AND (LEFT(a.pdx,3) BETWEEN "S02" AND "S92" 
            OR LEFT(a.dx0,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.dx1,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.dx2,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.dx3,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.dx4,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.dx5,3) BETWEEN "S02" AND "S92")
            AND i1.icd10 = "9918") AS a
            GROUP BY MONTH(dchdate)
            ORDER BY YEAR(dchdate) , MONTH(dchdate)');
      $diag_m_ipd = array_column($diag_month_ipd,'month');
      $diag_an_m_ipd = array_column($diag_month_ipd,'an');
      $diag_hn_m_ipd = array_column($diag_month_ipd,'hn');

      $diag_year_ipd = DB::connection('hosxp')->select('select 
            IF(MONTH(dchdate)>9,YEAR(dchdate)+1,YEAR(dchdate)) + 543 AS year_bud,
            COUNT(DISTINCT an) as an,COUNT(DISTINCT hn) as hn
            FROM (SELECT i.an,i.hn,i.regdate,i.regtime,i.dchdate,i.dchtime FROM ipt i								
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an AND i1.diagtype <>"1"									
            WHERE i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'" 
            AND (LEFT(a.pdx,3) BETWEEN "S02" AND "S92" 
            OR LEFT(a.dx0,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.dx1,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.dx2,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.dx3,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.dx4,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.dx5,3) BETWEEN "S02" AND "S92")
            AND i1.icd10 = "9918") AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

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
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE LEFT(pdx,3) BETWEEN "S02" AND "S92"
            GROUP BY MONTH(refer_date)
            ORDER BY YEAR(refer_date) , MONTH(refer_date)');
      $refer_m = array_column($refer_month,'month');
      $refer_opd_m = array_column($refer_month,'opd'); 
      $refer_ipd_m = array_column($refer_month,'ipd');     

      $refer_year = DB::connection('hosxp')->select('select 
            IF(MONTH(refer_date)>9,YEAR(refer_date)+1,YEAR(refer_date)) + 543 AS year_bud,
            SUM(CASE WHEN department="OPD" THEN 1 ELSE 0 END) AS "opd",
            SUM(CASE WHEN department="IPD" THEN 1 ELSE 0 END) AS "ipd"
            FROM (SELECT r.vn,r.refer_date,r.refer_point,r.department,r.pdx AS pdx_refer,o.icd10 AS pdx_opd,i.icd10 AS pdx_ipd,
            IF(r.pdx = "" OR r.pdx IS NULL,IF(o.icd10 = "" OR o.icd10 IS NULL,i.icd10,r.pdx),r.pdx) AS "pdx"
            FROM referout r 
            LEFT JOIN ovstdiag o ON o.vn=r.vn AND o.diagtype = 1
            LEFT JOIN iptdiag i ON i.an=r.vn AND i.diagtype = 1
            WHERE r.refer_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"		
            GROUP BY r.vn) AS a
            WHERE LEFT(pdx,3) BETWEEN "S02" AND "S92"
            GROUP BY year_bud
            ORDER BY year_bud');
      $refer_y = array_column($refer_year,'year_bud');
      $refer_opd_y = array_column($refer_year,'opd');
      $refer_ipd_y = array_column($refer_year,'ipd'); 

      $refer_list = DB::connection('hosxp')->select('select
            o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,pmh.cc_persist_disease AS pmh,
            GROUP_CONCAT(c1.`name`) AS "clinic",r.department,r.refer_point,o.vstdate,o.vsttime,
            IFNULL(v.pdx,a.pdx) AS pdx,r.refer_date,r.refer_time,r.pre_diagnosis,r.pdx AS pdx_refer,h.`name` AS refer_hos
            FROM referout r 
            LEFT JOIN ovst o ON o.vn=r.vn
            LEFT JOIN vn_stat v ON v.vn=r.vn 
            LEFT JOIN an_stat a ON a.an=r.vn
            LEFT JOIN clinicmember c ON c.hn=r.hn
            LEFT JOIN clinic c1 ON c1.clinic=c.clinic
            LEFT JOIN opd_ill_history pmh ON pmh.hn=r.hn
            LEFT JOIN patient p ON p.hn=r.hn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            WHERE r.refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"		 
            AND (LEFT(r.pdx,3) BETWEEN "S02" AND "S92" OR LEFT(v.pdx,3) BETWEEN "S02" AND "S92"
            OR LEFT(a.pdx,3) BETWEEN "S02" AND "S92") 
            GROUP BY r.vn							
            ORDER BY r.refer_point,r.refer_date');

      return view('medicalrecord_diag.trauma',compact('budget_year_select','budget_year','diag_m','diag_visit_m',
            'diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd','diag_hn_m_ipd',
            'diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd','refer_m','refer_opd_m','refer_ipd_m',
            'refer_y','refer_opd_y','refer_ipd_y','refer_list'));            
}

}
