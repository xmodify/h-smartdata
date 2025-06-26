<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service_MentalController extends Controller
{
//Check Login
      public function __construct()
      {
          $this->middleware('auth');
      }

//Create index
      public function index()
      {
           return view('service_mental.index');            
      }

//Create mentl_appointment
public function mental_appointment(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

    $mental_appointment = DB::connection('hosxp')->select('
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
            AND o.clinic = "012" AND (( o.oapp_status_id < 4 ) OR o.oapp_status_id IS NULL )
            ORDER BY o.nextdate,o.nexttime');

    $narcotics_appointment = DB::connection('hosxp')->select('
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
            AND o.clinic = "020" AND (( o.oapp_status_id < 4 ) OR o.oapp_status_id IS NULL )
            ORDER BY o.nextdate,o.nexttime');

      return view('service_mental.mental_appointment',compact('start_date','end_date','mental_appointment','narcotics_appointment'));
}

//Create diag_dementia
public function diag_dementia(Request $request)
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
            SELECT o.vn,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" AND o2.diag_no IS NOT NULL
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "F00" AND "F03" OR LEFT(v.dx0,3) BETWEEN "F00" AND "F03"
            OR LEFT(v.dx1,3) BETWEEN "F00" AND "F03" OR LEFT(v.dx2,3) BETWEEN "F00" AND "F03"
            OR LEFT(v.dx3,3) BETWEEN "F00" AND "F03" OR LEFT(v.dx4,3) BETWEEN "F00" AND "F03"
            OR LEFT(v.dx5,3) BETWEEN "F00" AND "F03")
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
            AND (LEFT(v.pdx,3) BETWEEN "F00" AND "F03" OR LEFT(v.dx0,3) BETWEEN "F00" AND "F03"
            OR LEFT(v.dx1,3) BETWEEN "F00" AND "F03" OR LEFT(v.dx2,3) BETWEEN "F00" AND "F03"
            OR LEFT(v.dx3,3) BETWEEN "F00" AND "F03" OR LEFT(v.dx4,3) BETWEEN "F00" AND "F03"
            OR LEFT(v.dx5,3) BETWEEN "F00" AND "F03")) AS a
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
            AND (LEFT(v.pdx,3) BETWEEN "F00" AND "F03" OR LEFT(v.dx0,3) BETWEEN "F00" AND "F03"
            OR LEFT(v.dx1,3) BETWEEN "F00" AND "F03" OR LEFT(v.dx2,3) BETWEEN "F00" AND "F03"
            OR LEFT(v.dx3,3) BETWEEN "F00" AND "F03" OR LEFT(v.dx4,3) BETWEEN "F00" AND "F03"
            OR LEFT(v.dx5,3) BETWEEN "F00" AND "F03")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "F00" AND "F03" OR LEFT(a.dx0,3) BETWEEN "F00" AND "F03"
            OR LEFT(a.dx1,3) BETWEEN "F00" AND "F03" OR LEFT(a.dx2,3) BETWEEN "F00" AND "F03"
            OR LEFT(a.dx3,3) BETWEEN "F00" AND "F03" OR LEFT(a.dx4,3) BETWEEN "F00" AND "F03"
            OR LEFT(a.dx5,3) BETWEEN "F00" AND "F03")
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
            AND (LEFT(a.pdx,3) BETWEEN "F00" AND "F03" OR LEFT(a.dx0,3) BETWEEN "F00" AND "F03"
            OR LEFT(a.dx1,3) BETWEEN "F00" AND "F03" OR LEFT(a.dx2,3) BETWEEN "F00" AND "F03"
            OR LEFT(a.dx3,3) BETWEEN "F00" AND "F03" OR LEFT(a.dx4,3) BETWEEN "F00" AND "F03"
            OR LEFT(a.dx5,3) BETWEEN "F00" AND "F03")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "F00" AND "F03" OR LEFT(a.dx0,3) BETWEEN "F00" AND "F03"
            OR LEFT(a.dx1,3) BETWEEN "F00" AND "F03" OR LEFT(a.dx2,3) BETWEEN "F00" AND "F03"
            OR LEFT(a.dx3,3) BETWEEN "F00" AND "F03" OR LEFT(a.dx4,3) BETWEEN "F00" AND "F03"
            OR LEFT(a.dx5,3) BETWEEN "F00" AND "F03")) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

      return view('service_mental.diag_dementia',compact('budget_year_select','budget_year','diag_m',
            'diag_visit_m','diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd',
            'diag_hn_m_ipd','diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd'));             
}

//Create diag_addict
public function diag_addict(Request $request)
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
            SELECT o.vn,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" AND o2.diag_no IS NOT NULL
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "F11" AND "F19" OR LEFT(v.dx0,3) BETWEEN "F11" AND "F19"
            OR LEFT(v.dx1,3) BETWEEN "F11" AND "F19" OR LEFT(v.dx2,3) BETWEEN "F11" AND "F19"
            OR LEFT(v.dx3,3) BETWEEN "F11" AND "F19" OR LEFT(v.dx4,3) BETWEEN "F11" AND "F19"
            OR LEFT(v.dx5,3) BETWEEN "F11" AND "F19")
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
            AND (LEFT(v.pdx,3) BETWEEN "F11" AND "F19" OR LEFT(v.dx0,3) BETWEEN "F11" AND "F19"
            OR LEFT(v.dx1,3) BETWEEN "F11" AND "F19" OR LEFT(v.dx2,3) BETWEEN "F11" AND "F19"
            OR LEFT(v.dx3,3) BETWEEN "F11" AND "F19" OR LEFT(v.dx4,3) BETWEEN "F11" AND "F19"
            OR LEFT(v.dx5,3) BETWEEN "F11" AND "F19")) AS a
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
            AND (LEFT(v.pdx,3) BETWEEN "F11" AND "F19" OR LEFT(v.dx0,3) BETWEEN "F11" AND "F19"
            OR LEFT(v.dx1,3) BETWEEN "F11" AND "F19" OR LEFT(v.dx2,3) BETWEEN "F11" AND "F19"
            OR LEFT(v.dx3,3) BETWEEN "F11" AND "F19" OR LEFT(v.dx4,3) BETWEEN "F11" AND "F19"
            OR LEFT(v.dx5,3) BETWEEN "F11" AND "F19")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "F11" AND "F19" OR LEFT(a.dx0,3) BETWEEN "F11" AND "F19"
            OR LEFT(a.dx1,3) BETWEEN "F11" AND "F19" OR LEFT(a.dx2,3) BETWEEN "F11" AND "F19"
            OR LEFT(a.dx3,3) BETWEEN "F11" AND "F19" OR LEFT(a.dx4,3) BETWEEN "F11" AND "F19"
            OR LEFT(a.dx5,3) BETWEEN "F11" AND "F19")
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
            AND (LEFT(a.pdx,3) BETWEEN "F11" AND "F19" OR LEFT(a.dx0,3) BETWEEN "F11" AND "F19"
            OR LEFT(a.dx1,3) BETWEEN "F11" AND "F19" OR LEFT(a.dx2,3) BETWEEN "F11" AND "F19"
            OR LEFT(a.dx3,3) BETWEEN "F11" AND "F19" OR LEFT(a.dx4,3) BETWEEN "F11" AND "F19"
            OR LEFT(a.dx5,3) BETWEEN "F11" AND "F19")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "F11" AND "F19" OR LEFT(a.dx0,3) BETWEEN "F11" AND "F19"
            OR LEFT(a.dx1,3) BETWEEN "F11" AND "F19" OR LEFT(a.dx2,3) BETWEEN "F11" AND "F19"
            OR LEFT(a.dx3,3) BETWEEN "F11" AND "F19" OR LEFT(a.dx4,3) BETWEEN "F11" AND "F19"
            OR LEFT(a.dx5,3) BETWEEN "F11" AND "F19")) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

      return view('service_mental.diag_addict',compact('budget_year_select','budget_year','diag_m',
            'diag_visit_m','diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd',
            'diag_hn_m_ipd','diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd'));          
}

//Create diag_addict_alcohol
public function diag_addict_alcohol(Request $request)
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
            SELECT o.vn,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" AND o2.diag_no IS NOT NULL
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx LIKE "F10%" OR v.dx0 LIKE "F10%" OR v.dx1 LIKE "F10%"
            OR v.dx2 LIKE "F10%" OR v.dx3 LIKE "F10%" OR v.dx4 LIKE "F10%"
            OR v.dx5 LIKE "F10%")
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
            AND (v.pdx LIKE "F10%" OR v.dx0 LIKE "F10%" OR v.dx1 LIKE "F10%"
            OR v.dx2 LIKE "F10%" OR v.dx3 LIKE "F10%" OR v.dx4 LIKE "F10%"
            OR v.dx5 LIKE "F10%")) AS a
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
            AND (v.pdx LIKE "F10%" OR v.dx0 LIKE "F10%" OR v.dx1 LIKE "F10%"
            OR v.dx2 LIKE "F10%" OR v.dx3 LIKE "F10%" OR v.dx4 LIKE "F10%"
            OR v.dx5 LIKE "F10%")) AS a
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
            AND (a.pdx LIKE "F10%" OR a.dx0 LIKE "F10%" OR a.dx1 LIKE "F10%"
            OR a.dx2 LIKE "F10%" OR a.dx3 LIKE "F10%" OR a.dx4 LIKE "F10%"
            OR a.dx5 LIKE "F10%")
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
            AND (a.pdx LIKE "F10%" OR a.dx0 LIKE "F10%" OR a.dx1 LIKE "F10%"
            OR a.dx2 LIKE "F10%" OR a.dx3 LIKE "F10%" OR a.dx4 LIKE "F10%"
            OR a.dx5 LIKE "F10%")) AS a
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
            AND (a.pdx LIKE "F10%" OR a.dx0 LIKE "F10%" OR a.dx1 LIKE "F10%"
            OR a.dx2 LIKE "F10%" OR a.dx3 LIKE "F10%" OR a.dx4 LIKE "F10%"
            OR a.dx5 LIKE "F10%")) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

      return view('service_mental.diag_addict_alcohol',compact('budget_year_select','budget_year','diag_m',
            'diag_visit_m','diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd',
            'diag_hn_m_ipd','diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd'));            
}

//Create diag_schizophrenia
public function diag_schizophrenia(Request $request)
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
            SELECT o.vn,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" AND o2.diag_no IS NOT NULL
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "F20" AND "F29" OR LEFT(v.dx0,3) BETWEEN "F20" AND "F29"
            OR LEFT(v.dx1,3) BETWEEN "F20" AND "F29"  OR LEFT(v.dx2,3) BETWEEN "F20" AND "F29"
            OR LEFT(v.dx3,3) BETWEEN "F20" AND "F29"  OR LEFT(v.dx4,3) BETWEEN "F20" AND "F29"
            OR LEFT(v.dx5,3) BETWEEN "F20" AND "F29")
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
            AND (LEFT(v.pdx,3) BETWEEN "F20" AND "F29" OR LEFT(v.dx0,3) BETWEEN "F20" AND "F29"
            OR LEFT(v.dx1,3) BETWEEN "F20" AND "F29"  OR LEFT(v.dx2,3) BETWEEN "F20" AND "F29"
            OR LEFT(v.dx3,3) BETWEEN "F20" AND "F29"  OR LEFT(v.dx4,3) BETWEEN "F20" AND "F29"
            OR LEFT(v.dx5,3) BETWEEN "F20" AND "F29")) AS a
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
            AND (LEFT(v.pdx,3) BETWEEN "F20" AND "F29" OR LEFT(v.dx0,3) BETWEEN "F20" AND "F29"
            OR LEFT(v.dx1,3) BETWEEN "F20" AND "F29"  OR LEFT(v.dx2,3) BETWEEN "F20" AND "F29"
            OR LEFT(v.dx3,3) BETWEEN "F20" AND "F29"  OR LEFT(v.dx4,3) BETWEEN "F20" AND "F29"
            OR LEFT(v.dx5,3) BETWEEN "F20" AND "F29")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "F20" AND "F29" OR LEFT(a.dx0,3) BETWEEN "F20" AND "F29"
            OR LEFT(a.dx1,3) BETWEEN "F20" AND "F29"  OR LEFT(a.dx2,3) BETWEEN "F20" AND "F29"
            OR LEFT(a.dx3,3) BETWEEN "F20" AND "F29"  OR LEFT(a.dx4,3) BETWEEN "F20" AND "F29"
            OR LEFT(a.dx5,3) BETWEEN "F20" AND "F29")
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
            AND (LEFT(a.pdx,3) BETWEEN "F20" AND "F29" OR LEFT(a.dx0,3) BETWEEN "F20" AND "F29"
            OR LEFT(a.dx1,3) BETWEEN "F20" AND "F29"  OR LEFT(a.dx2,3) BETWEEN "F20" AND "F29"
            OR LEFT(a.dx3,3) BETWEEN "F20" AND "F29"  OR LEFT(a.dx4,3) BETWEEN "F20" AND "F29"
            OR LEFT(a.dx5,3) BETWEEN "F20" AND "F29")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "F20" AND "F29" OR LEFT(a.dx0,3) BETWEEN "F20" AND "F29"
            OR LEFT(a.dx1,3) BETWEEN "F20" AND "F29"  OR LEFT(a.dx2,3) BETWEEN "F20" AND "F29"
            OR LEFT(a.dx3,3) BETWEEN "F20" AND "F29"  OR LEFT(a.dx4,3) BETWEEN "F20" AND "F29"
            OR LEFT(a.dx5,3) BETWEEN "F20" AND "F29") ) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

      return view('service_mental.diag_schizophrenia',compact('budget_year_select','budget_year','diag_m',
            'diag_visit_m','diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd',
            'diag_hn_m_ipd','diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd'));            
}

//Create diag_depressive
public function diag_depressive(Request $request)
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
            SELECT o.vn,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            GROUP_CONCAT(ppt.pp_special_type_name) AS pp_special,d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" AND o2.diag_no IS NOT NULL
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            LEFT JOIN pp_special pp ON pp.vn =o.vn
		LEFT JOIN pp_special_type ppt ON ppt.pp_special_type_id=pp.pp_special_type_id 
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "F32" AND "F33" OR LEFT(v.pdx,3) BETWEEN "F38" AND "F39" 
            OR v.pdx LIKE "F341%" OR LEFT(v.dx0,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx0,3) BETWEEN "F38" AND "F39" 
            OR v.dx0 LIKE "F341%" OR LEFT(v.dx1,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx1,3) BETWEEN "F38" AND "F39" 
            OR v.dx1 LIKE "F341%" OR LEFT(v.dx2,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx2,3) BETWEEN "F38" AND "F39" 
            OR v.dx2 LIKE "F341%" OR LEFT(v.dx3,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx3,3) BETWEEN "F38" AND "F39" 
            OR v.dx3 LIKE "F341%" OR LEFT(v.dx4,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx4,3) BETWEEN "F38" AND "F39" 
            OR v.dx4 LIKE "F341%" OR LEFT(v.dx5,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx5,3) BETWEEN "F38" AND "F39" 
            OR v.dx5 LIKE "F341%")
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
            AND (LEFT(v.pdx,3) BETWEEN "F32" AND "F33" OR LEFT(v.pdx,3) BETWEEN "F38" AND "F39" 
            OR v.pdx LIKE "F341%" OR LEFT(v.dx0,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx0,3) BETWEEN "F38" AND "F39" 
            OR v.dx0 LIKE "F341%" OR LEFT(v.dx1,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx1,3) BETWEEN "F38" AND "F39" 
            OR v.dx1 LIKE "F341%" OR LEFT(v.dx2,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx2,3) BETWEEN "F38" AND "F39" 
            OR v.dx2 LIKE "F341%" OR LEFT(v.dx3,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx3,3) BETWEEN "F38" AND "F39" 
            OR v.dx3 LIKE "F341%" OR LEFT(v.dx4,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx4,3) BETWEEN "F38" AND "F39" 
            OR v.dx4 LIKE "F341%" OR LEFT(v.dx5,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx5,3) BETWEEN "F38" AND "F39" 
            OR v.dx5 LIKE "F341%")) AS a
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
            AND (LEFT(v.pdx,3) BETWEEN "F32" AND "F33" OR LEFT(v.pdx,3) BETWEEN "F38" AND "F39" 
            OR v.pdx LIKE "F341%" OR LEFT(v.dx0,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx0,3) BETWEEN "F38" AND "F39" 
            OR v.dx0 LIKE "F341%" OR LEFT(v.dx1,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx1,3) BETWEEN "F38" AND "F39" 
            OR v.dx1 LIKE "F341%" OR LEFT(v.dx2,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx2,3) BETWEEN "F38" AND "F39" 
            OR v.dx2 LIKE "F341%" OR LEFT(v.dx3,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx3,3) BETWEEN "F38" AND "F39" 
            OR v.dx3 LIKE "F341%" OR LEFT(v.dx4,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx4,3) BETWEEN "F38" AND "F39" 
            OR v.dx4 LIKE "F341%" OR LEFT(v.dx5,3) BETWEEN "F32" AND "F33" OR LEFT(v.dx5,3) BETWEEN "F38" AND "F39" 
            OR v.dx5 LIKE "F341%")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "F32" AND "F33" OR LEFT(a.pdx,3) BETWEEN "F38" AND "F39" 
            OR a.pdx LIKE "F341%" OR LEFT(a.dx0,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx0,3) BETWEEN "F38" AND "F39" 
            OR a.dx0 LIKE "F341%" OR LEFT(a.dx1,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx1,3) BETWEEN "F38" AND "F39" 
            OR a.dx1 LIKE "F341%" OR LEFT(a.dx2,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx2,3) BETWEEN "F38" AND "F39" 
            OR a.dx2 LIKE "F341%" OR LEFT(a.dx3,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx3,3) BETWEEN "F38" AND "F39" 
            OR a.dx3 LIKE "F341%" OR LEFT(a.dx4,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx4,3) BETWEEN "F38" AND "F39" 
            OR a.dx4 LIKE "F341%" OR LEFT(a.dx5,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx5,3) BETWEEN "F38" AND "F39" 
            OR a.dx5 LIKE "F341%")
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
            AND (LEFT(a.pdx,3) BETWEEN "F32" AND "F33" OR LEFT(a.pdx,3) BETWEEN "F38" AND "F39" 
            OR a.pdx LIKE "F341%" OR LEFT(a.dx0,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx0,3) BETWEEN "F38" AND "F39" 
            OR a.dx0 LIKE "F341%" OR LEFT(a.dx1,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx1,3) BETWEEN "F38" AND "F39" 
            OR a.dx1 LIKE "F341%" OR LEFT(a.dx2,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx2,3) BETWEEN "F38" AND "F39" 
            OR a.dx2 LIKE "F341%" OR LEFT(a.dx3,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx3,3) BETWEEN "F38" AND "F39" 
            OR a.dx3 LIKE "F341%" OR LEFT(a.dx4,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx4,3) BETWEEN "F38" AND "F39" 
            OR a.dx4 LIKE "F341%" OR LEFT(a.dx5,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx5,3) BETWEEN "F38" AND "F39" 
            OR a.dx5 LIKE "F341%")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "F32" AND "F33" OR LEFT(a.pdx,3) BETWEEN "F38" AND "F39" 
            OR a.pdx LIKE "F341%" OR LEFT(a.dx0,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx0,3) BETWEEN "F38" AND "F39" 
            OR a.dx0 LIKE "F341%" OR LEFT(a.dx1,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx1,3) BETWEEN "F38" AND "F39" 
            OR a.dx1 LIKE "F341%" OR LEFT(a.dx2,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx2,3) BETWEEN "F38" AND "F39" 
            OR a.dx2 LIKE "F341%" OR LEFT(a.dx3,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx3,3) BETWEEN "F38" AND "F39" 
            OR a.dx3 LIKE "F341%" OR LEFT(a.dx4,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx4,3) BETWEEN "F38" AND "F39" 
            OR a.dx4 LIKE "F341%" OR LEFT(a.dx5,3) BETWEEN "F32" AND "F33" OR LEFT(a.dx5,3) BETWEEN "F38" AND "F39" 
            OR a.dx5 LIKE "F341%")) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

      return view('service_mental.diag_depressive',compact('budget_year_select','budget_year','diag_m',
            'diag_visit_m','diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd',
            'diag_hn_m_ipd','diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd'));            
}

//Create diag_anxiety
public function diag_anxiety(Request $request)
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
            SELECT o.vn,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" AND o2.diag_no IS NOT NULL
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "F40" AND "F49" OR LEFT(v.dx0,3) BETWEEN "F40" AND "F49"
            OR LEFT(v.dx1,3) BETWEEN "F40" AND "F49" OR LEFT(v.dx2,3) BETWEEN "F40" AND "F49"
            OR LEFT(v.dx3,3) BETWEEN "F40" AND "F49" OR LEFT(v.dx4,3) BETWEEN "F40" AND "F49"
            OR LEFT(v.dx5,3) BETWEEN "F40" AND "F49")
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
            AND (LEFT(v.pdx,3) BETWEEN "F40" AND "F49" OR LEFT(v.dx0,3) BETWEEN "F40" AND "F49"
            OR LEFT(v.dx1,3) BETWEEN "F40" AND "F49" OR LEFT(v.dx2,3) BETWEEN "F40" AND "F49"
            OR LEFT(v.dx3,3) BETWEEN "F40" AND "F49" OR LEFT(v.dx4,3) BETWEEN "F40" AND "F49"
            OR LEFT(v.dx5,3) BETWEEN "F40" AND "F49")) AS a
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
            AND (LEFT(v.pdx,3) BETWEEN "F40" AND "F49" OR LEFT(v.dx0,3) BETWEEN "F40" AND "F49"
            OR LEFT(v.dx1,3) BETWEEN "F40" AND "F49" OR LEFT(v.dx2,3) BETWEEN "F40" AND "F49"
            OR LEFT(v.dx3,3) BETWEEN "F40" AND "F49" OR LEFT(v.dx4,3) BETWEEN "F40" AND "F49"
            OR LEFT(v.dx5,3) BETWEEN "F40" AND "F49")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "F40" AND "F49" OR LEFT(a.dx0,3) BETWEEN "F40" AND "F49"
            OR LEFT(a.dx1,3) BETWEEN "F40" AND "F49" OR LEFT(a.dx2,3) BETWEEN "F40" AND "F49"
            OR LEFT(a.dx3,3) BETWEEN "F40" AND "F49" OR LEFT(a.dx4,3) BETWEEN "F40" AND "F49"
            OR LEFT(a.dx5,3) BETWEEN "F40" AND "F49")
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
            AND (LEFT(a.pdx,3) BETWEEN "F40" AND "F49" OR LEFT(a.dx0,3) BETWEEN "F40" AND "F49"
            OR LEFT(a.dx1,3) BETWEEN "F40" AND "F49" OR LEFT(a.dx2,3) BETWEEN "F40" AND "F49"
            OR LEFT(a.dx3,3) BETWEEN "F40" AND "F49" OR LEFT(a.dx4,3) BETWEEN "F40" AND "F49"
            OR LEFT(a.dx5,3) BETWEEN "F40" AND "F49")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "F40" AND "F49" OR LEFT(a.dx0,3) BETWEEN "F40" AND "F49"
            OR LEFT(a.dx1,3) BETWEEN "F40" AND "F49" OR LEFT(a.dx2,3) BETWEEN "F40" AND "F49"
            OR LEFT(a.dx3,3) BETWEEN "F40" AND "F49" OR LEFT(a.dx4,3) BETWEEN "F40" AND "F49"
            OR LEFT(a.dx5,3) BETWEEN "F40" AND "F49")) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

      return view('service_mental.diag_anxiety',compact('budget_year_select','budget_year','diag_m',
            'diag_visit_m','diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd',
            'diag_hn_m_ipd','diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd'));            
}

//Create diag_epilepsy
public function diag_epilepsy(Request $request)
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
            SELECT o.vn,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" AND o2.diag_no IS NOT NULL
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "G40" AND "G41" OR LEFT(v.dx0,3) BETWEEN "G40" AND "G41"
            OR LEFT(v.dx1,3) BETWEEN "G40" AND "G41" OR LEFT(v.dx2,3) BETWEEN "G40" AND "G41"
            OR LEFT(v.dx3,3) BETWEEN "G40" AND "G41" OR LEFT(v.dx4,3) BETWEEN "G40" AND "G41"
            OR LEFT(v.dx5,3) BETWEEN "G40" AND "G41")
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
            AND (LEFT(v.pdx,3) BETWEEN "G40" AND "G41" OR LEFT(v.dx0,3) BETWEEN "G40" AND "G41"
            OR LEFT(v.dx1,3) BETWEEN "G40" AND "G41" OR LEFT(v.dx2,3) BETWEEN "G40" AND "G41"
            OR LEFT(v.dx3,3) BETWEEN "G40" AND "G41" OR LEFT(v.dx4,3) BETWEEN "G40" AND "G41"
            OR LEFT(v.dx5,3) BETWEEN "G40" AND "G41")) AS a
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
            AND (LEFT(v.pdx,3) BETWEEN "G40" AND "G41" OR LEFT(v.dx0,3) BETWEEN "G40" AND "G41"
            OR LEFT(v.dx1,3) BETWEEN "G40" AND "G41" OR LEFT(v.dx2,3) BETWEEN "G40" AND "G41"
            OR LEFT(v.dx3,3) BETWEEN "G40" AND "G41" OR LEFT(v.dx4,3) BETWEEN "G40" AND "G41"
            OR LEFT(v.dx5,3) BETWEEN "G40" AND "G41")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "G40" AND "G41" OR LEFT(a.dx0,3) BETWEEN "G40" AND "G41"
            OR LEFT(a.dx1,3) BETWEEN "G40" AND "G41" OR LEFT(a.dx2,3) BETWEEN "G40" AND "G41"
            OR LEFT(a.dx3,3) BETWEEN "G40" AND "G41" OR LEFT(a.dx4,3) BETWEEN "G40" AND "G41"
            OR LEFT(a.dx5,3) BETWEEN "G40" AND "G41")
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
            AND (LEFT(a.pdx,3) BETWEEN "G40" AND "G41" OR LEFT(a.dx0,3) BETWEEN "G40" AND "G41"
            OR LEFT(a.dx1,3) BETWEEN "G40" AND "G41" OR LEFT(a.dx2,3) BETWEEN "G40" AND "G41"
            OR LEFT(a.dx3,3) BETWEEN "G40" AND "G41" OR LEFT(a.dx4,3) BETWEEN "G40" AND "G41"
            OR LEFT(a.dx5,3) BETWEEN "G40" AND "G41")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "G40" AND "G41" OR LEFT(a.dx0,3) BETWEEN "G40" AND "G41"
            OR LEFT(a.dx1,3) BETWEEN "G40" AND "G41" OR LEFT(a.dx2,3) BETWEEN "G40" AND "G41"
            OR LEFT(a.dx3,3) BETWEEN "G40" AND "G41" OR LEFT(a.dx4,3) BETWEEN "G40" AND "G41"
            OR LEFT(a.dx5,3) BETWEEN "G40" AND "G41")) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

      return view('service_mental.diag_epilepsy',compact('budget_year_select','budget_year','diag_m',
            'diag_visit_m','diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd',
            'diag_hn_m_ipd','diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd'));            
}

//Create diag_retardation
public function diag_retardation(Request $request)
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
            SELECT o.vn,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" AND o2.diag_no IS NOT NULL
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "F70" AND "F79" OR LEFT(v.dx0,3) BETWEEN "F70" AND "F79"
            OR LEFT(v.dx1,3) BETWEEN "F70" AND "F79" OR LEFT(v.dx2,3) BETWEEN "F70" AND "F79"
            OR LEFT(v.dx3,3) BETWEEN "F70" AND "F79" OR LEFT(v.dx4,3) BETWEEN "F70" AND "F79"
            OR LEFT(v.dx5,3) BETWEEN "F70" AND "F79")
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
            AND (LEFT(v.pdx,3) BETWEEN "F70" AND "F79" OR LEFT(v.dx0,3) BETWEEN "F70" AND "F79"
            OR LEFT(v.dx1,3) BETWEEN "F70" AND "F79" OR LEFT(v.dx2,3) BETWEEN "F70" AND "F79"
            OR LEFT(v.dx3,3) BETWEEN "F70" AND "F79" OR LEFT(v.dx4,3) BETWEEN "F70" AND "F79"
            OR LEFT(v.dx5,3) BETWEEN "F70" AND "F79")) AS a
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
            AND (LEFT(v.pdx,3) BETWEEN "F70" AND "F79" OR LEFT(v.dx0,3) BETWEEN "F70" AND "F79"
            OR LEFT(v.dx1,3) BETWEEN "F70" AND "F79" OR LEFT(v.dx2,3) BETWEEN "F70" AND "F79"
            OR LEFT(v.dx3,3) BETWEEN "F70" AND "F79" OR LEFT(v.dx4,3) BETWEEN "F70" AND "F79"
            OR LEFT(v.dx5,3) BETWEEN "F70" AND "F79")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "F70" AND "F79" OR LEFT(a.dx0,3) BETWEEN "F70" AND "F79"
            OR LEFT(a.dx1,3) BETWEEN "F70" AND "F79" OR LEFT(a.dx2,3) BETWEEN "F70" AND "F79"
            OR LEFT(a.dx3,3) BETWEEN "F70" AND "F79" OR LEFT(a.dx4,3) BETWEEN "F70" AND "F79"
            OR LEFT(a.dx5,3) BETWEEN "F70" AND "F79") GROUP BY i.an ');

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
            AND (LEFT(a.pdx,3) BETWEEN "F70" AND "F79" OR LEFT(a.dx0,3) BETWEEN "F70" AND "F79"
            OR LEFT(a.dx1,3) BETWEEN "F70" AND "F79" OR LEFT(a.dx2,3) BETWEEN "F70" AND "F79"
            OR LEFT(a.dx3,3) BETWEEN "F70" AND "F79" OR LEFT(a.dx4,3) BETWEEN "F70" AND "F79"
            OR LEFT(a.dx5,3) BETWEEN "F70" AND "F79")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "F70" AND "F79" OR LEFT(a.dx0,3) BETWEEN "F70" AND "F79"
            OR LEFT(a.dx1,3) BETWEEN "F70" AND "F79" OR LEFT(a.dx2,3) BETWEEN "F70" AND "F79"
            OR LEFT(a.dx3,3) BETWEEN "F70" AND "F79" OR LEFT(a.dx4,3) BETWEEN "F70" AND "F79"
            OR LEFT(a.dx5,3) BETWEEN "F70" AND "F79")) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

      return view('service_mental.diag_retardation',compact('budget_year_select','budget_year','diag_m',
            'diag_visit_m','diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd',
            'diag_hn_m_ipd','diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd'));            
}

//Create diag_skills
public function diag_skills(Request $request)
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
            SELECT o.vn,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" AND o2.diag_no IS NOT NULL
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx LIKE "F81%" OR v.dx0 LIKE "F81%" OR v.dx1 LIKE "F81%"
            OR v.dx2 LIKE "F81%" OR v.dx3 LIKE "F81%" OR v.dx4 LIKE "F81%"
            OR v.dx5 LIKE "F81%")
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
            AND (v.pdx LIKE "F81%" OR v.dx0 LIKE "F81%" OR v.dx1 LIKE "F81%"
            OR v.dx2 LIKE "F81%" OR v.dx3 LIKE "F81%" OR v.dx4 LIKE "F81%"
            OR v.dx5 LIKE "F81%")) AS a
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
            AND (v.pdx LIKE "F81%" OR v.dx0 LIKE "F81%" OR v.dx1 LIKE "F81%"
            OR v.dx2 LIKE "F81%" OR v.dx3 LIKE "F81%" OR v.dx4 LIKE "F81%"
            OR v.dx5 LIKE "F81%")) AS a
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
            AND (a.pdx LIKE "F81%" OR a.dx0 LIKE "F81%" OR a.dx1 LIKE "F81%"
            OR a.dx2 LIKE "F81%" OR a.dx3 LIKE "F81%" OR a.dx4 LIKE "F81%"
            OR a.dx5 LIKE "F81%") 
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
            AND (a.pdx LIKE "F81%" OR a.dx0 LIKE "F81%" OR a.dx1 LIKE "F81%"
            OR a.dx2 LIKE "F81%" OR a.dx3 LIKE "F81%" OR a.dx4 LIKE "F81%"
            OR a.dx5 LIKE "F81%")) AS a
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
            AND (a.pdx LIKE "F81%" OR a.dx0 LIKE "F81%" OR a.dx1 LIKE "F81%"
            OR a.dx2 LIKE "F81%" OR a.dx3 LIKE "F81%" OR a.dx4 LIKE "F81%"
            OR a.dx5 LIKE "F81%")) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

      return view('service_mental.diag_skills',compact('budget_year_select','budget_year','diag_m',
            'diag_visit_m','diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd',
            'diag_hn_m_ipd','diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd'));            
}

//Create diag_autism
public function diag_autism(Request $request)
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
            SELECT o.vn,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" AND o2.diag_no IS NOT NULL
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx LIKE "F84%" OR v.dx0 LIKE "F84%" OR v.dx1 LIKE "F84%"
            OR v.dx2 LIKE "F84%" OR v.dx3 LIKE "F84%" OR v.dx4 LIKE "F84%"
            OR v.dx5 LIKE "F84%")
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
            AND (v.pdx LIKE "F84%" OR v.dx0 LIKE "F84%" OR v.dx1 LIKE "F84%"
            OR v.dx2 LIKE "F84%" OR v.dx3 LIKE "F84%" OR v.dx4 LIKE "F84%"
            OR v.dx5 LIKE "F84%")) AS a
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
            AND (v.pdx LIKE "F84%" OR v.dx0 LIKE "F84%" OR v.dx1 LIKE "F84%"
            OR v.dx2 LIKE "F84%" OR v.dx3 LIKE "F84%" OR v.dx4 LIKE "F84%"
            OR v.dx5 LIKE "F84%")) AS a
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
            AND (a.pdx LIKE "F84%" OR a.dx0 LIKE "F84%" OR a.dx1 LIKE "F84%"
            OR a.dx2 LIKE "F84%" OR a.dx3 LIKE "F84%" OR a.dx4 LIKE "F84%"
            OR a.dx5 LIKE "F84%") GROUP BY i.an ');

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
            AND (a.pdx LIKE "F84%" OR a.dx0 LIKE "F84%" OR a.dx1 LIKE "F84%"
            OR a.dx2 LIKE "F84%" OR a.dx3 LIKE "F84%" OR a.dx4 LIKE "F84%"
            OR a.dx5 LIKE "F84%")) AS a
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
            AND (a.pdx LIKE "F84%" OR a.dx0 LIKE "F84%" OR a.dx1 LIKE "F84%"
            OR a.dx2 LIKE "F84%" OR a.dx3 LIKE "F84%" OR a.dx4 LIKE "F84%"
            OR a.dx5 LIKE "F84%")) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

      return view('service_mental.diag_autism',compact('budget_year_select','budget_year','diag_m',
            'diag_visit_m','diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd',
            'diag_hn_m_ipd','diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd'));            
}

//Create diag_behavior
public function diag_behavior(Request $request)
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
            SELECT o.vn,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" AND o2.diag_no IS NOT NULL
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx LIKE "F91%" OR v.dx0 LIKE "F91%"
            OR v.dx1 LIKE "F91%" OR v.dx2 LIKE "F91%"
            OR v.dx3 LIKE "F91%" OR v.dx4 LIKE "F91%"
            OR v.dx5 LIKE "F91%")
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
            AND (v.pdx LIKE "F91%" OR v.dx0 LIKE "F91%"
            OR v.dx1 LIKE "F91%" OR v.dx2 LIKE "F91%"
            OR v.dx3 LIKE "F91%" OR v.dx4 LIKE "F91%"
            OR v.dx5 LIKE "F91%")) AS a
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
            AND (v.pdx LIKE "F91%" OR v.dx0 LIKE "F91%"
            OR v.dx1 LIKE "F91%" OR v.dx2 LIKE "F91%"
            OR v.dx3 LIKE "F91%" OR v.dx4 LIKE "F91%"
            OR v.dx5 LIKE "F91%")) AS a
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
            AND (a.pdx LIKE "F91%" OR a.dx0 LIKE "F91%"
            OR a.dx1 LIKE "F91%" OR a.dx2 LIKE "F91%"
            OR a.dx3 LIKE "F91%" OR a.dx4 LIKE "F91%"
            OR a.dx5 LIKE "F91%") GROUP BY i.an ');

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
            AND (a.pdx LIKE "F91%" OR a.dx0 LIKE "F91%"
            OR a.dx1 LIKE "F91%" OR a.dx2 LIKE "F91%"
            OR a.dx3 LIKE "F91%" OR a.dx4 LIKE "F91%"
            OR a.dx5 LIKE "F91%")) AS a
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
            AND (a.pdx LIKE "F91%" OR a.dx0 LIKE "F91%"
            OR a.dx1 LIKE "F91%" OR a.dx2 LIKE "F91%"
            OR a.dx3 LIKE "F91%" OR a.dx4 LIKE "F91%"
            OR a.dx5 LIKE "F91%")) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

      return view('service_mental.diag_behavior',compact('budget_year_select','budget_year','diag_m',
            'diag_visit_m','diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd',
            'diag_hn_m_ipd','diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd'));            
}

//Create diag_selfharm
public function diag_selfharm(Request $request)
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
            SELECT o.vn,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,GROUP_CONCAT(o2.icd10) AS dx,
            d.`name` AS dx_doctor, CONCAT(h.`name`," [",r.pdx,"]") AS refer
            FROM ovst o 
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN ovstdiag o2 ON o2.vn=o.vn AND o2.diagtype <>"1" AND o2.diag_no IS NOT NULL
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN hospcode h ON h.hospcode=r.refer_hospcode
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (LEFT(v.pdx,3) BETWEEN "X60" AND "X84" 
            OR LEFT(v.dx0,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx1,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx2,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx3,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx4,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx5,3) BETWEEN "X60" AND "X84")
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
            AND (LEFT(v.pdx,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx0,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx1,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx2,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx3,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx4,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx5,3) BETWEEN "X60" AND "X84")) AS a
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
            AND (LEFT(v.pdx,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx0,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx1,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx2,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx3,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx4,3) BETWEEN "X60" AND "X84"
            OR LEFT(v.dx5,3) BETWEEN "X60" AND "X84")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "X60" AND "X84" 
            OR LEFT(a.dx0,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx1,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx2,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx3,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx4,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx5,3) BETWEEN "X60" AND "X84")
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
            AND (LEFT(a.pdx,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx0,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx1,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx2,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx3,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx4,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx5,3) BETWEEN "X60" AND "X84")) AS a
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
            AND (LEFT(a.pdx,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx0,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx1,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx2,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx3,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx4,3) BETWEEN "X60" AND "X84"
            OR LEFT(a.dx5,3) BETWEEN "X60" AND "X84")) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
      $diag_y_ipd = array_column($diag_year_ipd,'year_bud');
      $diag_an_y_ipd = array_column($diag_year_ipd,'an');
      $diag_hn_y_ipd = array_column($diag_year_ipd,'hn');

      return view('service_mental.diag_selfharm',compact('budget_year_select','budget_year','diag_m',
            'diag_visit_m','diag_hn_m','diag_y','diag_visit_y','diag_hn_y','diag_list','diag_m_ipd','diag_an_m_ipd',
            'diag_hn_m_ipd','diag_y_ipd','diag_an_y_ipd','diag_hn_y_ipd','diag_list_ipd'));            
}

}
