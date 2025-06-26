<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service_NCDController extends Controller
{
//Check Login
      public function __construct()
      {
          $this->middleware('auth');
      }

//Create index
public function index()
{
      return view('service_ncd.index');
}

//Create dm_clinic
public function dm_clinic(Request $request)
{
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
    $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
    $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
    $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

    $clinic = DB::connection('hosxp')->select('
            SELECT clinic_member_status_name,COUNT(hn) AS total
            FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
            c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value,
            c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
            y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
            u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
            cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
            FROM clinicmember c
            LEFT OUTER JOIN patient p ON p.hn = c.hn
            LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
            LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
            LEFT OUTER JOIN sex s ON s.CODE = p.sex
            LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
            LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
            LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
            LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
            LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
            LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
            AND ta.amppart = p.amppart
            AND ta.tmbpart = p.tmbpart
            AND ta.codetype = "3"
            WHERE c.clinic = "001"
            GROUP BY c.hn
            ORDER BY c.pt_number,c.regdate) AS a
            GROUP BY clinic_member_status_id
            ORDER BY total DESC');
    $clinic_member_status = array_column($clinic,'clinic_member_status_name');
    $clinic_total = array_column($clinic,'total');

    $newcase_month = DB::connection('hosxp')->select('
            SELECT CASE WHEN MONTH(regdate)="10" THEN "ต.ค."
            WHEN MONTH(regdate)="11" THEN "พ.ย."
            WHEN MONTH(regdate)="12" THEN "ธ.ค."
            WHEN MONTH(regdate)="1" THEN "ม.ค."
            WHEN MONTH(regdate)="2" THEN "ก.พ."
            WHEN MONTH(regdate)="3" THEN "มี.ค."
            WHEN MONTH(regdate)="4" THEN "เม.ย."
            WHEN MONTH(regdate)="5" THEN "พ.ค."
            WHEN MONTH(regdate)="6" THEN "มิ.ย."
            WHEN MONTH(regdate)="7" THEN "ก.ค."
            WHEN MONTH(regdate)="8" THEN "ส.ค."
            WHEN MONTH(regdate)="9" THEN "ก.ย."
            END AS "month",
            COUNT(hn) AS total
            FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
            c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value,
            c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
            y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
            u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
            cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
            FROM clinicmember c
            LEFT OUTER JOIN patient p ON p.hn = c.hn
            LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
            LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
            LEFT OUTER JOIN sex s ON s.CODE = p.sex
            LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
            LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
            LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
            LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
            LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
            LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
            AND ta.amppart = p.amppart
            AND ta.tmbpart = p.tmbpart
            AND ta.codetype = "3"
            WHERE c.clinic = "001" AND c.regdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY c.hn
            ORDER BY c.pt_number,c.regdate) AS a
            GROUP BY MONTH(regdate)
            ORDER BY YEAR(regdate),MONTH(regdate)');
    $newcase_m = array_column($newcase_month,'month');
    $newcase_total_m = array_column($newcase_month,'total');

    $newcase_year = DB::connection('hosxp')->select('
            SELECT IF(MONTH(regdate)>9,YEAR(regdate)+1,YEAR(regdate)) + 543 AS year_bud,
            COUNT(hn) AS total
            FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
            c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value,
            c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
            y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
            u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
            cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
            FROM clinicmember c
            LEFT OUTER JOIN patient p ON p.hn = c.hn
            LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
            LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
            LEFT OUTER JOIN sex s ON s.CODE = p.sex
            LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
            LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
            LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
            LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
            LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
            LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
            AND ta.amppart = p.amppart
            AND ta.tmbpart = p.tmbpart
            AND ta.codetype = "3"
            WHERE c.clinic = "001" AND c.regdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            GROUP BY c.hn
            ORDER BY c.pt_number,c.regdate) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
    $newcase_y = array_column($newcase_year,'year_bud');
    $newcase_total_y = array_column($newcase_year,'total');

    return view('service_ncd.dm_clinic',compact('budget_year_select','budget_year','clinic_member_status','clinic_total',
            'newcase_m','newcase_total_m','newcase_y','newcase_total_y'));
}

//Create dm
public function dm(Request $request)
{
        $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
        $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
        $budget_year = $request->budget_year;
        if($budget_year == '' || $budget_year == null)
        {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
        $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
        $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
        $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

        $month = DB::connection('hosxp')->select('select
                CASE WHEN MONTH(a.vstdate)="10" THEN "ต.ค."
                WHEN MONTH(a.vstdate)="11" THEN "พ.ย."
                WHEN MONTH(a.vstdate)="12" THEN "ธ.ค."
                WHEN MONTH(a.vstdate)="1" THEN "ม.ค."
                WHEN MONTH(a.vstdate)="2" THEN "ก.พ."
                WHEN MONTH(a.vstdate)="3" THEN "มี.ค."
                WHEN MONTH(a.vstdate)="4" THEN "เม.ย."
                WHEN MONTH(a.vstdate)="5" THEN "พ.ค."
                WHEN MONTH(a.vstdate)="6" THEN "มิ.ย."
                WHEN MONTH(a.vstdate)="7" THEN "ก.ค."
                WHEN MONTH(a.vstdate)="8" THEN "ส.ค."
                WHEN MONTH(a.vstdate)="9" THEN "ก.ย."
                END AS "month",COUNT(DISTINCT a.hn) AS "hn",COUNT(DISTINCT a.vn) AS "visit"
                FROM (SELECT o.vstdate,o.vn,o.hn,cm.clinic,v.pdx,v.dx0,v.dx1,v.dx2,v.dx3,v.dx4,v.dx5
                FROM ovst o
                LEFT JOIN vn_stat v ON v.vn=o.vn
                LEFT JOIN clinicmember cm ON cm.hn=o.hn
                LEFT JOIN clinic_visit cv ON cv.vn=o.vn AND cv.clinic="001"
                WHERE cm.clinic = "001" AND v.pdx BETWEEN "E100" AND "E149"
                AND (cv.vn <> "" OR cv.vn IS NOT NULL)
                AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'") AS a
                GROUP BY MONTH(a.vstdate)
                ORDER BY YEAR(a.vstdate) , MONTH(a.vstdate)');
        $m = array_column($month,'month');
        $hn_m = array_column($month,'hn');
        $visit_m = array_column($month,'visit');

        $year = DB::connection('hosxp')->select('select
                IF(MONTH(a.vstdate)>9,YEAR(a.vstdate)+1,YEAR(a.vstdate)) + 543 AS year_bud,
                COUNT(DISTINCT a.hn) AS "hn",COUNT(DISTINCT a.vn) AS "visit"
                FROM (SELECT o.vstdate,o.vn,o.hn,cm.clinic,v.pdx,v.dx0,v.dx1,v.dx2,v.dx3,v.dx4,v.dx5
                FROM ovst o
                LEFT JOIN vn_stat v ON v.vn=o.vn
                LEFT JOIN clinicmember cm ON cm.hn=o.hn
                LEFT JOIN clinic_visit cv ON cv.vn=o.vn AND cv.clinic="001"
                WHERE cm.clinic = "001" AND v.pdx BETWEEN "E100" AND "E149"
                AND (cv.vn <> "" OR cv.vn IS NOT NULL)
                AND o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'") AS a
                GROUP BY year_bud
                ORDER BY year_bud');
        $y = array_column($year,'year_bud');
        $hn_y = array_column($year,'hn');
        $visit_y = array_column($year,'visit');

      return view('service_ncd.dm',compact('budget_year_select','budget_year','m','hn_m','visit_m','y','hn_y','visit_y'));
}

//Create dm_appointment
public function dm_appointment(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

        $appointment = DB::connection('hosxp')->select('
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
                AND o.clinic = "001" AND o.oapp_status_id IN (1,3)
                ORDER BY o.nextdate,o.nexttime');
        $appointment_sum = DB::connection('hosxp')->select('
                SELECT SUM(CASE WHEN oapp_status_id =2 THEN 1 ELSE 0 END) AS "oapp",
                SUM(CASE WHEN oapp_status_id <>2 THEN 1 ELSE 0 END) AS "non_oapp"
                FROM oapp WHERE nextdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND clinic = "001" AND (( oapp_status_id < 4 ) OR oapp_status_id IS NULL)');
        foreach($appointment_sum as $row){
                $oapp_dm = $row->oapp; 
                $non_oapp_dm = $row->non_oapp; 
        }

        return view('service_ncd.dm_appointment',compact('start_date','end_date','oapp_dm','non_oapp_dm','appointment'));
}

//Create dm_nonclinic
public function dm_nonclinic(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

    $dm_nonclinic = DB::connection('hosxp')->select('
            SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            CONCAT(ROUND(o1.bps),"/",ROUND(o1.bpd)) AS bp,o1.cc,v.pdx,v.dx0,v.dx1,v.dx2,v.dx3,v.dx4,v.dx5
            FROM ovst o
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN clinicmember c ON c.hn=o.hn AND c.clinic = "001"
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (c.clinic IS NULL OR c.clinic ="") AND v.pdx BETWEEN "E100" AND "E149"
            GROUP BY o.vn
            ORDER BY o.vstdate,o.vsttime');

      return view('service_ncd.dm_nonclinic',compact('start_date','end_date','dm_nonclinic'));
}

//Create dm_admit
public function dm_admit(Request $request)
{
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
    $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
    $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
    $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

    $admit_month = DB::connection('hosxp')->select('
            SELECT CASE WHEN MONTH(a.regdate)="10" THEN "ต.ค."
            WHEN MONTH(a.regdate)="11" THEN "พ.ย."
            WHEN MONTH(a.regdate)="12" THEN "ธ.ค."
            WHEN MONTH(a.regdate)="1" THEN "ม.ค."
            WHEN MONTH(a.regdate)="2" THEN "ก.พ."
            WHEN MONTH(a.regdate)="3" THEN "มี.ค."
            WHEN MONTH(a.regdate)="4" THEN "เม.ย."
            WHEN MONTH(a.regdate)="5" THEN "พ.ค."
            WHEN MONTH(a.regdate)="6" THEN "มิ.ย."
            WHEN MONTH(a.regdate)="7" THEN "ก.ค."
            WHEN MONTH(a.regdate)="8" THEN "ส.ค."
            WHEN MONTH(a.regdate)="9" THEN "ก.ย."
            END AS "month",
            SUM(CASE WHEN a.icd10 LIKE "E%" THEN 1 ELSE 0 END) AS "dm"
            FROM (SELECT i.regdate,i.regtime,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.prediag,
            i1.icd10,i1.diagtype,i.dchdate,i.dchtime
            FROM ipt i
            LEFT JOIN iptdiag i1 ON i1.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.regdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND i1.icd10 BETWEEN "E100" AND "E149" 
            GROUP BY i.an,i1.icd10 ) AS a
            GROUP BY MONTH(a.regdate)
            ORDER BY YEAR(a.regdate) , MONTH(a.regdate)');
    $admit_m = array_column($admit_month,'month');
    $admit_dm_m = array_column($admit_month,'dm');

    $admit_year = DB::connection('hosxp')->select('
            SELECT IF(MONTH(a.regdate)>9,YEAR(a.regdate)+1,YEAR(a.regdate)) + 543 AS year_bud,
            SUM(CASE WHEN a.icd10 LIKE "E%" THEN 1 ELSE 0 END) AS "dm"
            FROM (SELECT i.regdate,i.regtime,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.prediag,
            i1.icd10,i1.diagtype,i.dchdate,i.dchtime
            FROM ipt i
            LEFT JOIN iptdiag i1 ON i1.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.regdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND i1.icd10 BETWEEN "E100" AND "E149"
            GROUP BY i.an,i1.icd10 ) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
    $admit_y = array_column($admit_year,'year_bud');
    $admit_dm_y = array_column($admit_year,'dm');

    $admit_dm_list = DB::connection('hosxp')->select('
            SELECT i.regdate,i.regtime,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.prediag,
            a.pdx,i1.icd10,i1.diagtype,i.dchdate,i.dchtime,IF(c.clinic IS NULL,"ยังไม่ขึ้นทะเบียน","") AS clinic
            FROM ipt i
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an
            LEFT JOIN clinicmember c ON c.hn=i.hn AND c.clinic = "001" AND c.clinic_member_status_id = "3"
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.regdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND i1.icd10 BETWEEN "E100" AND "E149"
            GROUP BY i.an
            ORDER BY i.regdate ');
   
    return view('service_ncd.dm_admit',compact('budget_year_select','budget_year','admit_m','admit_dm_m',
            'admit_y','admit_dm_y','admit_dm_list'));
}

//Create dm_death
public function dm_death(Request $request)
{
    $dm_death = DB::connection('hosxp')->select('
        SELECT p.hn,LPAD(p1.person_id,6,0) AS pid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS fullname,
        p1.house_regist_type_id,IF(d.death_date <>"","Y","") AS death,p.death AS death_patient,
        p1.death AS death_person,c1.clinic_member_status_name,c.discharge
        FROM patient p 
        INNER JOIN clinicmember c ON c.hn=p.hn
        LEFT JOIN clinic_member_status c1 ON c1.clinic_member_status_id=c.clinic_member_status_id
        LEFT JOIN clinic c2 ON c2.clinic=c.clinic
        LEFT JOIN person p1 ON p1.patient_hn=p.hn  
        LEFT JOIN death d ON d.hn=p.hn
        WHERE c.clinic = "001" AND (d.hn <>"" OR p.death ="Y" OR p1.death="Y" OR c.clinic_member_status_id="2") 
        GROUP BY p.hn ORDER BY house_regist_type_id');

      return view('service_ncd.dm_death',compact('dm_death'));
}

//Create ht_clinic
public function ht_clinic(Request $request)
{
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
    $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
    $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
    $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

    $clinic = DB::connection('hosxp')->select('
            SELECT clinic_member_status_name,COUNT(hn) AS total
            FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
            c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value,
            c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
            y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
            u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
            cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
            FROM clinicmember c
            LEFT OUTER JOIN patient p ON p.hn = c.hn
            LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
            LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
            LEFT OUTER JOIN sex s ON s.CODE = p.sex
            LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
            LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
            LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
            LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
            LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
            LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
            AND ta.amppart = p.amppart
            AND ta.tmbpart = p.tmbpart
            AND ta.codetype = "3"
            WHERE c.clinic = "002"
            GROUP BY c.hn
            ORDER BY c.pt_number,c.regdate) AS a
            GROUP BY clinic_member_status_id
            ORDER BY total DESC');
    $clinic_member_status = array_column($clinic,'clinic_member_status_name');
    $clinic_total = array_column($clinic,'total');

    $newcase_month = DB::connection('hosxp')->select('
            SELECT CASE WHEN MONTH(regdate)="10" THEN "ต.ค."
            WHEN MONTH(regdate)="11" THEN "พ.ย."
            WHEN MONTH(regdate)="12" THEN "ธ.ค."
            WHEN MONTH(regdate)="1" THEN "ม.ค."
            WHEN MONTH(regdate)="2" THEN "ก.พ."
            WHEN MONTH(regdate)="3" THEN "มี.ค."
            WHEN MONTH(regdate)="4" THEN "เม.ย."
            WHEN MONTH(regdate)="5" THEN "พ.ค."
            WHEN MONTH(regdate)="6" THEN "มิ.ย."
            WHEN MONTH(regdate)="7" THEN "ก.ค."
            WHEN MONTH(regdate)="8" THEN "ส.ค."
            WHEN MONTH(regdate)="9" THEN "ก.ย."
            END AS "month",
            COUNT(hn) AS total
            FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
            c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value,
            c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
            y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
            u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
            cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
            FROM clinicmember c
            LEFT OUTER JOIN patient p ON p.hn = c.hn
            LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
            LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
            LEFT OUTER JOIN sex s ON s.CODE = p.sex
            LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
            LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
            LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
            LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
            LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
            LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
            AND ta.amppart = p.amppart
            AND ta.tmbpart = p.tmbpart
            AND ta.codetype = "3"
            WHERE c.clinic = "002" AND c.regdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY c.hn
            ORDER BY c.pt_number,c.regdate) AS a
            GROUP BY MONTH(regdate)
            ORDER BY YEAR(regdate),MONTH(regdate)');
    $newcase_m = array_column($newcase_month,'month');
    $newcase_total_m = array_column($newcase_month,'total');

    $newcase_year = DB::connection('hosxp')->select('
            SELECT IF(MONTH(regdate)>9,YEAR(regdate)+1,YEAR(regdate)) + 543 AS year_bud,
            COUNT(hn) AS total
            FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
            c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value,
            c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
            y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
            u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
            cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
            FROM clinicmember c
            LEFT OUTER JOIN patient p ON p.hn = c.hn
            LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
            LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
            LEFT OUTER JOIN sex s ON s.CODE = p.sex
            LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
            LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
            LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
            LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
            LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
            LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
            AND ta.amppart = p.amppart
            AND ta.tmbpart = p.tmbpart
            AND ta.codetype = "3"
            WHERE c.clinic = "002" AND c.regdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            GROUP BY c.hn
            ORDER BY c.pt_number,c.regdate) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
    $newcase_y = array_column($newcase_year,'year_bud');
    $newcase_total_y = array_column($newcase_year,'total');

    return view('service_ncd.ht_clinic',compact('budget_year_select','budget_year','clinic_member_status','clinic_total',
    'newcase_m','newcase_total_m','newcase_y','newcase_total_y'));
}

//Create ht
public function ht(Request $request)
{
        $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
        $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
        $budget_year = $request->budget_year;
        if($budget_year == '' || $budget_year == null)
        {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
        $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
        $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
        $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

        $month = DB::connection('hosxp')->select('select
                CASE WHEN MONTH(a.vstdate)="10" THEN "ต.ค."
                WHEN MONTH(a.vstdate)="11" THEN "พ.ย."
                WHEN MONTH(a.vstdate)="12" THEN "ธ.ค."
                WHEN MONTH(a.vstdate)="1" THEN "ม.ค."
                WHEN MONTH(a.vstdate)="2" THEN "ก.พ."
                WHEN MONTH(a.vstdate)="3" THEN "มี.ค."
                WHEN MONTH(a.vstdate)="4" THEN "เม.ย."
                WHEN MONTH(a.vstdate)="5" THEN "พ.ค."
                WHEN MONTH(a.vstdate)="6" THEN "มิ.ย."
                WHEN MONTH(a.vstdate)="7" THEN "ก.ค."
                WHEN MONTH(a.vstdate)="8" THEN "ส.ค."
                WHEN MONTH(a.vstdate)="9" THEN "ก.ย."
                END AS "month",COUNT(DISTINCT a.hn) AS "hn",COUNT(DISTINCT a.vn) AS "visit"
                FROM (SELECT o.vstdate,o.vn,o.hn,cm.clinic,v.pdx,v.dx0,v.dx1,v.dx2,v.dx3,v.dx4,v.dx5
                FROM ovst o
                LEFT JOIN vn_stat v ON v.vn=o.vn
                LEFT JOIN clinicmember cm ON cm.hn=o.hn
                LEFT JOIN clinic_visit cv ON cv.vn=o.vn AND cv.clinic="002"
                WHERE cm.clinic = "002" AND v.pdx BETWEEN "I10" AND "I10"
                AND (cv.vn <> "" OR cv.vn IS NOT NULL)
                AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'") AS a
                GROUP BY MONTH(a.vstdate)
                ORDER BY YEAR(a.vstdate) , MONTH(a.vstdate)');
        $m = array_column($month,'month');
        $hn_m = array_column($month,'hn');
        $visit_m = array_column($month,'visit');

        $year = DB::connection('hosxp')->select('select
                IF(MONTH(a.vstdate)>9,YEAR(a.vstdate)+1,YEAR(a.vstdate)) + 543 AS year_bud,
                COUNT(DISTINCT a.hn) AS "hn",COUNT(DISTINCT a.vn) AS "visit"
                FROM (SELECT o.vstdate,o.vn,o.hn,cm.clinic,v.pdx,v.dx0,v.dx1,v.dx2,v.dx3,v.dx4,v.dx5
                FROM ovst o
                LEFT JOIN vn_stat v ON v.vn=o.vn
                LEFT JOIN clinicmember cm ON cm.hn=o.hn
                LEFT JOIN clinic_visit cv ON cv.vn=o.vn AND cv.clinic="002"
                WHERE cm.clinic = "002" AND v.pdx BETWEEN "I10" AND "I10"
                AND (cv.vn <> "" OR cv.vn IS NOT NULL)
                AND o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'") AS a
                GROUP BY year_bud
                ORDER BY year_bud');
        $y = array_column($year,'year_bud');
        $hn_y = array_column($year,'hn');
        $visit_y = array_column($year,'visit');

      return view('service_ncd.ht',compact('budget_year_select','budget_year','m','hn_m','visit_m','y','hn_y','visit_y'));
}

//Create ht_appointment
public function ht_appointment(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;} 

        $appointment = DB::connection('hosxp')->select('
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
                AND o.clinic = "002" AND o.oapp_status_id IN (1,3)
                ORDER BY o.nextdate,o.nexttime');
        $appointment_sum = DB::connection('hosxp')->select('
                SELECT SUM(CASE WHEN oapp_status_id =2 THEN 1 ELSE 0 END) AS "oapp",
                SUM(CASE WHEN oapp_status_id <>2 THEN 1 ELSE 0 END) AS "non_oapp"
                FROM oapp WHERE nextdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND clinic = "002" AND (( oapp_status_id < 4 ) OR oapp_status_id IS NULL)');
        foreach($appointment_sum as $row){
                $oapp_ht = $row->oapp; 
                $non_oapp_ht = $row->non_oapp; 
        }

        return view('service_ncd.ht_appointment',compact('start_date','end_date','oapp_ht','non_oapp_ht','appointment'));
}

//Create ht_nonclinic
public function ht_nonclinic(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

    $ht_nonclinic = DB::connection('hosxp')->select('
            SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            CONCAT(ROUND(o1.bps),"/",ROUND(o1.bpd)) AS bp,o1.cc,v.pdx,v.dx0,v.dx1,v.dx2,v.dx3,v.dx4,v.dx5
            FROM ovst o
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN clinicmember c ON c.hn=o.hn AND c.clinic = "002"
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (c.clinic IS NULL OR c.clinic ="") AND v.pdx = "I10"
            GROUP BY o.vn
            ORDER BY o.vstdate,o.vsttime');


      return view('service_ncd.ht_nonclinic',compact('start_date','end_date','ht_nonclinic'));
}

//Create ht_admit
public function ht_admit(Request $request)
{
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
    $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
    $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
    $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

    $admit_month = DB::connection('hosxp')->select('
            SELECT CASE WHEN MONTH(a.regdate)="10" THEN "ต.ค."
            WHEN MONTH(a.regdate)="11" THEN "พ.ย."
            WHEN MONTH(a.regdate)="12" THEN "ธ.ค."
            WHEN MONTH(a.regdate)="1" THEN "ม.ค."
            WHEN MONTH(a.regdate)="2" THEN "ก.พ."
            WHEN MONTH(a.regdate)="3" THEN "มี.ค."
            WHEN MONTH(a.regdate)="4" THEN "เม.ย."
            WHEN MONTH(a.regdate)="5" THEN "พ.ค."
            WHEN MONTH(a.regdate)="6" THEN "มิ.ย."
            WHEN MONTH(a.regdate)="7" THEN "ก.ค."
            WHEN MONTH(a.regdate)="8" THEN "ส.ค."
            WHEN MONTH(a.regdate)="9" THEN "ก.ย."
            END AS "month",
            SUM(CASE WHEN a.icd10 LIKE "I%" THEN 1 ELSE 0 END) AS "ht"
            FROM (SELECT i.regdate,i.regtime,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.prediag,
            i1.icd10,i1.diagtype,i.dchdate,i.dchtime
            FROM ipt i
            LEFT JOIN iptdiag i1 ON i1.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.regdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND i1.icd10 = "I10"
            GROUP BY i.an,i1.icd10 ) AS a
            GROUP BY MONTH(a.regdate)
            ORDER BY YEAR(a.regdate) , MONTH(a.regdate)');
    $admit_m = array_column($admit_month,'month');
    $admit_ht_m = array_column($admit_month,'ht');

    $admit_year = DB::connection('hosxp')->select('
            SELECT IF(MONTH(a.regdate)>9,YEAR(a.regdate)+1,YEAR(a.regdate)) + 543 AS year_bud,
            SUM(CASE WHEN a.icd10 LIKE "I%" THEN 1 ELSE 0 END) AS "ht"
            FROM (SELECT i.regdate,i.regtime,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.prediag,
            i1.icd10,i1.diagtype,i.dchdate,i.dchtime
            FROM ipt i
            LEFT JOIN iptdiag i1 ON i1.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.regdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND  i1.icd10 = "I10"
            GROUP BY i.an,i1.icd10 ) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
    $admit_y = array_column($admit_year,'year_bud');
    $admit_ht_y = array_column($admit_year,'ht');

    $admit_ht_list = DB::connection('hosxp')->select('
            SELECT i.regdate,i.regtime,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.prediag,
            a.pdx,i1.icd10,i1.diagtype,i.dchdate,i.dchtime,IF(c.clinic IS NULL,"ยังไม่ขึ้นทะเบียน","") AS clinic
            FROM ipt i
            LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN iptdiag i1 ON i1.an=i.an
            LEFT JOIN clinicmember c ON c.hn=i.hn AND c.clinic = "002" AND c.clinic_member_status_id = "3"
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.regdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND i1.icd10 = "I10"
            GROUP BY i.an
            ORDER BY i.regdate ');

    return view('service_ncd.ht_admit',compact('budget_year_select','budget_year','admit_m','admit_ht_m',
        'admit_y','admit_ht_y','admit_ht_list'));
}

//Create ht_death
public function ht_death(Request $request)
{
    $ht_death = DB::connection('hosxp')->select('
        SELECT p.hn,LPAD(p1.person_id,6,0) AS pid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS fullname,
        p1.house_regist_type_id,IF(d.death_date <>"","Y","") AS death,p.death AS death_patient,
        p1.death AS death_person,c1.clinic_member_status_name,c.discharge
        FROM patient p 
        INNER JOIN clinicmember c ON c.hn=p.hn
        LEFT JOIN clinic_member_status c1 ON c1.clinic_member_status_id=c.clinic_member_status_id
        LEFT JOIN clinic c2 ON c2.clinic=c.clinic
        LEFT JOIN person p1 ON p1.patient_hn=p.hn  
        LEFT JOIN death d ON d.hn=p.hn
        WHERE c.clinic = "002" AND (d.hn <>"" OR p.death ="Y" OR p1.death="Y" OR c.clinic_member_status_id="2") 
        GROUP BY p.hn ORDER BY house_regist_type_id');

      return view('service_ncd.ht_death',compact('ht_death'));
}

//Create capd_clinic
public function capd_clinic(Request $request)
{
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
    $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
    $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
    $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

    $clinic = DB::connection('hosxp')->select('
            SELECT clinic_member_status_name,COUNT(hn) AS total
            FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
            c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value,
            c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
            y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
            u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
            cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
            FROM clinicmember c
            LEFT OUTER JOIN patient p ON p.hn = c.hn
            LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
            LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
            LEFT OUTER JOIN sex s ON s.CODE = p.sex
            LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
            LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
            LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
            LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
            LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
            LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
            AND ta.amppart = p.amppart
            AND ta.tmbpart = p.tmbpart
            AND ta.codetype = "3"
            WHERE c.clinic = "014"
            GROUP BY c.hn
            ORDER BY c.pt_number,c.regdate) AS a
            GROUP BY clinic_member_status_id
            ORDER BY total DESC');
    $clinic_member_status = array_column($clinic,'clinic_member_status_name');
    $clinic_total = array_column($clinic,'total');

    $newcase_month = DB::connection('hosxp')->select('
            SELECT CASE WHEN MONTH(regdate)="10" THEN "ต.ค."
            WHEN MONTH(regdate)="11" THEN "พ.ย."
            WHEN MONTH(regdate)="12" THEN "ธ.ค."
            WHEN MONTH(regdate)="1" THEN "ม.ค."
            WHEN MONTH(regdate)="2" THEN "ก.พ."
            WHEN MONTH(regdate)="3" THEN "มี.ค."
            WHEN MONTH(regdate)="4" THEN "เม.ย."
            WHEN MONTH(regdate)="5" THEN "พ.ค."
            WHEN MONTH(regdate)="6" THEN "มิ.ย."
            WHEN MONTH(regdate)="7" THEN "ก.ค."
            WHEN MONTH(regdate)="8" THEN "ส.ค."
            WHEN MONTH(regdate)="9" THEN "ก.ย."
            END AS "month",
            COUNT(hn) AS total
            FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
            c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value,
            c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
            y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
            u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
            cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
            FROM clinicmember c
            LEFT OUTER JOIN patient p ON p.hn = c.hn
            LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
            LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
            LEFT OUTER JOIN sex s ON s.CODE = p.sex
            LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
            LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
            LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
            LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
            LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
            LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
            AND ta.amppart = p.amppart
            AND ta.tmbpart = p.tmbpart
            AND ta.codetype = "3"
            WHERE c.clinic = "014" AND c.regdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY c.hn
            ORDER BY c.pt_number,c.regdate) AS a
            GROUP BY MONTH(regdate)
            ORDER BY YEAR(regdate),MONTH(regdate)');
    $newcase_m = array_column($newcase_month,'month');
    $newcase_total_m = array_column($newcase_month,'total');

    $newcase_year = DB::connection('hosxp')->select('
            SELECT IF(MONTH(regdate)>9,YEAR(regdate)+1,YEAR(regdate)) + 543 AS year_bud,
            COUNT(hn) AS total
            FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
            c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value,
            c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
            y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
            u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
            cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
            FROM clinicmember c
            LEFT OUTER JOIN patient p ON p.hn = c.hn
            LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
            LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
            LEFT OUTER JOIN sex s ON s.CODE = p.sex
            LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
            LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
            LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
            LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
            LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
            LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
            AND ta.amppart = p.amppart
            AND ta.tmbpart = p.tmbpart
            AND ta.codetype = "3"
            WHERE c.clinic = "014" AND c.regdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            GROUP BY c.hn
            ORDER BY c.pt_number,c.regdate) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
    $newcase_y = array_column($newcase_year,'year_bud');
    $newcase_total_y = array_column($newcase_year,'total');

    return view('service_ncd.capd_clinic',compact('budget_year_select','budget_year','clinic_member_status','clinic_total',
            'newcase_m','newcase_total_m','newcase_y','newcase_total_y'));
}

//Create capd
public function capd(Request $request)
{
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
    $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
    $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
    $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

    $month = DB::connection('hosxp')->select('select
            CASE WHEN MONTH(a.vstdate)="10" THEN "ต.ค."
            WHEN MONTH(a.vstdate)="11" THEN "พ.ย."
            WHEN MONTH(a.vstdate)="12" THEN "ธ.ค."
            WHEN MONTH(a.vstdate)="1" THEN "ม.ค."
            WHEN MONTH(a.vstdate)="2" THEN "ก.พ."
            WHEN MONTH(a.vstdate)="3" THEN "มี.ค."
            WHEN MONTH(a.vstdate)="4" THEN "เม.ย."
            WHEN MONTH(a.vstdate)="5" THEN "พ.ค."
            WHEN MONTH(a.vstdate)="6" THEN "มิ.ย."
            WHEN MONTH(a.vstdate)="7" THEN "ก.ค."
            WHEN MONTH(a.vstdate)="8" THEN "ส.ค."
            WHEN MONTH(a.vstdate)="9" THEN "ก.ย."
            END AS "month",
            SUM(CASE WHEN a.capd_visit IS NOT NULL THEN 1 ELSE 0 END) AS "visit"
            FROM (SELECT o.vstdate,o.vn,o.hn,GROUP_CONCAT(cm.clinic) AS clinic,
            cv.clinic AS capd_visit,v.pdx,v.dx0,v.dx1,v.dx2,v.dx3,v.dx4,v.dx5
            FROM ovst o
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN clinicmember cm ON cm.hn=o.hn
            LEFT JOIN clinic_visit cv ON cv.vn=o.vn AND cv.clinic="014"
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND cm.clinic = "014" AND v.pdx = "N185"
            GROUP BY o.vn) AS a
            GROUP BY MONTH(a.vstdate)
            ORDER BY YEAR(a.vstdate) , MONTH(a.vstdate)');
    $m = array_column($month,'month');
    $visit_m = array_column($month,'visit');

    $year = DB::connection('hosxp')->select('select
            IF(MONTH(a.vstdate)>9,YEAR(a.vstdate)+1,YEAR(a.vstdate)) + 543 AS year_bud,
            SUM(CASE WHEN a.capd_visit IS NOT NULL THEN 1 ELSE 0 END) AS "visit"
            FROM (SELECT o.vstdate,o.vn,o.hn,GROUP_CONCAT(cm.clinic) AS clinic,
            cv.clinic AS capd_visit,v.pdx,v.dx0,v.dx1,v.dx2,v.dx3,v.dx4,v.dx5
            FROM ovst o
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN clinicmember cm ON cm.hn=o.hn
            LEFT JOIN clinic_visit cv ON cv.vn=o.vn AND cv.clinic="014"
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND cm.clinic = "014" AND v.pdx = "N185"
            GROUP BY o.vn) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
    $y = array_column($year,'year_bud');
    $visit_y = array_column($year,'visit');

    return view('service_ncd.capd',compact('budget_year_select','budget_year','m',
            'visit_m','y','visit_y'));
}

//Create capd_appointment
public function capd_appointment(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

    $appointment = DB::connection('hosxp')->select('
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
            AND o.clinic = "014" AND (( o.oapp_status_id < 4 ) OR o.oapp_status_id IS NULL )
            ORDER BY o.nextdate,o.nexttime');

      return view('service_ncd.capd_appointment',compact('start_date','end_date','appointment'));
}

//Create capd_nonclinic
public function capd_nonclinic(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

    $nonclinic = DB::connection('hosxp')->select('
            SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            o1.cc,v.pdx,l2.lab_items_name,l1.lab_order_result
            FROM ovst o
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN lab_head l ON l.vn=o.vn
            LEFT JOIN lab_order l1 ON l1.lab_order_number=l.lab_order_number
            LEFT JOIN lab_items l2 ON l2.lab_items_code=l1.lab_items_code
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN clinicmember c ON c.hn=o.hn AND c.clinic = "014"
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (c.clinic IS NULL OR c.clinic ="") AND l1.lab_items_code="793"
            AND l1.lab_order_result IN ("4","5")
            GROUP BY o.vn
            ORDER BY o.vstdate,o.vsttime');

      return view('service_ncd.capd_nonclinic',compact('start_date','end_date','nonclinic'));
}

//Create kidney_clinic
public function kidney_clinic(Request $request)
{
        $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
        $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
        $budget_year = $request->budget_year;
        if($budget_year == '' || $budget_year == null)
        {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
        $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
        $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
        $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

        $clinic = DB::connection('hosxp')->select('
                SELECT clinic_member_status_name,COUNT(hn) AS total
                FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
                c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value,
                c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
                y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
                u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
                cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
                FROM clinicmember c
                LEFT OUTER JOIN patient p ON p.hn = c.hn
                LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
                LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
                LEFT OUTER JOIN sex s ON s.CODE = p.sex
                LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
                LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
                LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
                LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
                LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
                LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
                AND ta.amppart = p.amppart
                AND ta.tmbpart = p.tmbpart
                AND ta.codetype = "3"
                WHERE c.clinic = "013"
                GROUP BY c.hn
                ORDER BY c.pt_number,c.regdate) AS a
                GROUP BY clinic_member_status_id
                ORDER BY total DESC');
        $clinic_member_status = array_column($clinic,'clinic_member_status_name');
        $clinic_total = array_column($clinic,'total');

        $clinic_amppart = DB::connection('hosxp')->select('
                SELECT SUM(CASE WHEN chwpart = "37" AND amppart = "06" THEN 1 ELSE 0 END) AS "in_amppart" ,
                (COUNT(hn)-SUM(CASE WHEN (chwpart = "37" AND amppart = "06") THEN 1 ELSE 0 END)) AS "out_amppart",COUNT(hn) AS total
                FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
                c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value, p.chwpart,p.amppart,
                c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
                y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
                u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
                cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
                FROM clinicmember c
                LEFT OUTER JOIN patient p ON p.hn = c.hn
                LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
                LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
                LEFT OUTER JOIN sex s ON s.CODE = p.sex
                LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
                LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
                LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
                LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
                LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
                LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
                AND ta.amppart = p.amppart
                AND ta.tmbpart = p.tmbpart
                AND ta.codetype = "3"
                WHERE c.clinic = "013" 
                GROUP BY c.hn
                ORDER BY c.pt_number,c.regdate) AS a');
        foreach ($clinic_amppart as $row) {
                $in_amppart=$row->in_amppart;
                $out_amppart=$row->out_amppart;
                $total_amppart=$row->total;
        }

    $newcase_month = DB::connection('hosxp')->select('
            SELECT CASE WHEN MONTH(regdate)="10" THEN "ต.ค."
            WHEN MONTH(regdate)="11" THEN "พ.ย."
            WHEN MONTH(regdate)="12" THEN "ธ.ค."
            WHEN MONTH(regdate)="1" THEN "ม.ค."
            WHEN MONTH(regdate)="2" THEN "ก.พ."
            WHEN MONTH(regdate)="3" THEN "มี.ค."
            WHEN MONTH(regdate)="4" THEN "เม.ย."
            WHEN MONTH(regdate)="5" THEN "พ.ค."
            WHEN MONTH(regdate)="6" THEN "มิ.ย."
            WHEN MONTH(regdate)="7" THEN "ก.ค."
            WHEN MONTH(regdate)="8" THEN "ส.ค."
            WHEN MONTH(regdate)="9" THEN "ก.ย."
            END AS "month",
            COUNT(hn) AS total
            FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
            c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value,
            c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
            y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
            u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
            cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
            FROM clinicmember c
            LEFT OUTER JOIN patient p ON p.hn = c.hn
            LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
            LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
            LEFT OUTER JOIN sex s ON s.CODE = p.sex
            LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
            LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
            LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
            LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
            LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
            LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
            AND ta.amppart = p.amppart
            AND ta.tmbpart = p.tmbpart
            AND ta.codetype = "3"
            WHERE c.clinic = "013" AND c.regdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY c.hn
            ORDER BY c.pt_number,c.regdate) AS a
            GROUP BY MONTH(regdate)
            ORDER BY YEAR(regdate),MONTH(regdate)');
    $newcase_m = array_column($newcase_month,'month');
    $newcase_total_m = array_column($newcase_month,'total');

    $newcase_year = DB::connection('hosxp')->select('
            SELECT IF(MONTH(regdate)>9,YEAR(regdate)+1,YEAR(regdate)) + 543 AS year_bud,
            COUNT(hn) AS total
            FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
            c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value,
            c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
            y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
            u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
            cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
            FROM clinicmember c
            LEFT OUTER JOIN patient p ON p.hn = c.hn
            LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
            LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
            LEFT OUTER JOIN sex s ON s.CODE = p.sex
            LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
            LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
            LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
            LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
            LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
            LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
            AND ta.amppart = p.amppart
            AND ta.tmbpart = p.tmbpart
            AND ta.codetype = "3"
            WHERE c.clinic = "013" AND c.regdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            GROUP BY c.hn
            ORDER BY c.pt_number,c.regdate) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
    $newcase_y = array_column($newcase_year,'year_bud');
    $newcase_total_y = array_column($newcase_year,'total');

    return view('service_ncd.kidney_clinic',compact('budget_year_select','budget_year','clinic_member_status','clinic_total',
            'newcase_m','newcase_total_m','newcase_y','newcase_total_y','in_amppart','out_amppart','total_amppart'));
}

//Create kidney_hos
public function kidney_hos(Request $request)
{
        $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
        $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
        $budget_year = $request->budget_year;
        if($budget_year == '' || $budget_year == null)
        {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
        $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
        $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
        $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

        $kidney = DB::connection('hosxp')->select('select
                CASE WHEN MONTH(rxdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(rxdate)+543,2))
                END AS "month",COUNT(DISTINCT hn) AS hn,ROUND(SUM(CASE WHEN a.dialysis <> "" THEN 1 ELSE 0 END),2) AS visit,
                ROUND(SUM(CASE WHEN a.dialysis <> "" AND a.hipdata_code = "UCS" THEN 1 ELSE 0 END),2) AS visit_ucs,
                ROUND(SUM(CASE WHEN a.dialysis <> "" AND a.hipdata_code = "OFC" THEN 1 ELSE 0 END),2) AS visit_ofc,
                ROUND(SUM(CASE WHEN a.dialysis <> "" AND a.hipdata_code = "LGO" THEN 1 ELSE 0 END),2) AS visit_lgo,
                ROUND(SUM(CASE WHEN a.dialysis <> "" AND a.hipdata_code NOT IN ("LGO","OFC","UCS") THEN 1 ELSE 0 END),2) AS visit_outher,
                ROUND(SUM(a.dialysis),2) AS dialysis,ROUND(SUM(a.lab),2) AS lab,ROUND(SUM(a.drug),2) AS drug,
                ROUND(SUM(CASE WHEN a.hipdata_code = "UCS" THEN a.dialysis ELSE 0 END),2) AS dialysis_ucs,
                ROUND(SUM(CASE WHEN a.hipdata_code = "OFC" THEN a.dialysis ELSE 0 END),2) AS dialysis_ofc,
                ROUND(SUM(CASE WHEN a.hipdata_code = "LGO" THEN a.dialysis ELSE 0 END),2) AS dialysis_lgo,
                ROUND(SUM(CASE WHEN a.hipdata_code NOT IN ("LGO","OFC","UCS") THEN a.dialysis ELSE 0 END),2) AS dialysis_outher,
                ROUND(SUM(CASE WHEN a.hipdata_code = "UCS" THEN a.lab ELSE 0 END),2) AS lab_ucs,
                ROUND(SUM(CASE WHEN a.hipdata_code = "OFC" THEN a.lab ELSE 0 END),2) AS lab_ofc,
                ROUND(SUM(CASE WHEN a.hipdata_code = "LGO" THEN a.lab ELSE 0 END),2) AS lab_lgo,
                ROUND(SUM(CASE WHEN a.hipdata_code NOT IN ("LGO","OFC","UCS") THEN a.lab ELSE 0 END),2) AS lab_outher,
                ROUND(SUM(CASE WHEN a.hipdata_code = "UCS" THEN a.drug ELSE 0 END),2) AS drug_ucs,
                ROUND(SUM(CASE WHEN a.hipdata_code = "OFC" THEN a.drug ELSE 0 END),2) AS drug_ofc,
                ROUND(SUM(CASE WHEN a.hipdata_code = "LGO" THEN a.drug ELSE 0 END),2) AS drug_lgo,
                ROUND(SUM(CASE WHEN a.hipdata_code NOT IN ("LGO","OFC","UCS") THEN a.drug ELSE 0 END),2) AS drug_outher
                FROM (SELECT o.pttype,p.hipdata_code,o.hn,d.vn,d1.an,IFNULL(d.vn,d1.an) AS vnan,c.clinic,c.regdate,
                IFNULL(d.rxdate,d1.rxdate) AS rxdate,o.icode,o.income,o.sum_price,
                CASE WHEN o.icode = "3003375" THEN o.sum_price ELSE 0 END AS dialysis,
                CASE WHEN o.income = "07" THEN o.sum_price ELSE	0 END AS lab,
                CASE WHEN o.income IN ("03","04","17") THEN o.sum_price ELSE 0 END AS drug
                FROM opitemrece o
                LEFT JOIN (SELECT vn,icode,sum_price,rxdate FROM opitemrece WHERE rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                        AND icode = "3003375" ) d ON d.vn=o.vn
                LEFT JOIN (SELECT an,icode,sum_price,rxdate FROM opitemrece WHERE rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                        AND icode = "3003375" ) d1 ON d1.an=o.an
                LEFT JOIN clinicmember c ON c.hn=o.hn AND o.rxdate >= c.regdate AND c.clinic = "013"
                LEFT JOIN pttype p ON p.pttype=o.pttype
                WHERE o.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND (d.vn <>"" OR d1.an <>"") ) AS a WHERE a.clinic IS NOT NULL OR a.clinic<>""
                GROUP BY MONTH(rxdate)
                ORDER BY YEAR(rxdate),MONTH(rxdate)');
        $month = array_column($kidney,'month');
        $hn = array_column($kidney,'hn');
        $visit = array_column($kidney,'visit');
        $visit_ucs = array_column($kidney,'visit_ucs');
        $visit_ofc = array_column($kidney,'visit_ofc');
        $visit_lgo = array_column($kidney,'visit_lgo');
        $visit_outher = array_column($kidney,'visit_outher');
        $dialysis = array_column($kidney,'dialysis');
        $lab = array_column($kidney,'lab');
        $drug = array_column($kidney,'drug');
        $dialysis_ucs = array_column($kidney,'dialysis_ucs');
        $dialysis_ofc = array_column($kidney,'dialysis_ofc');
        $dialysis_lgo = array_column($kidney,'dialysis_lgo');
        $dialysis_outher = array_column($kidney,'dialysis_outher');
        $lab_ucs = array_column($kidney,'lab_ucs'); 
        $lab_ofc = array_column($kidney,'lab_ofc');
        $lab_lgo = array_column($kidney,'lab_lgo');
        $lab_outher = array_column($kidney,'lab_outher');
        $drug_ucs = array_column($kidney,'drug_ucs');
        $drug_ofc = array_column($kidney,'drug_ofc');
        $drug_lgo = array_column($kidney,'drug_lgo');
        $drug_outher = array_column($kidney,'drug_outher');

        return view('service_ncd.kidney_hos',compact('budget_year_select','budget_year','kidney','month','hn','visit','visit_ucs',
                'visit_ofc','visit_lgo','visit_outher','dialysis','lab','drug','dialysis_ucs','dialysis_ofc','dialysis_lgo',
                'dialysis_outher','lab_ucs','lab_ofc',"lab_lgo","lab_outher","drug_ucs","drug_ofc","drug_lgo","drug_outher"));
}

//Create kidney_outsource
public function kidney_outsource(Request $request)
{
        $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
        $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
        $budget_year = $request->budget_year;
        if($budget_year == '' || $budget_year == null)
        {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
        $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
        $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
        $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

        $kidney = DB::connection('hosxp')->select('select
                CASE WHEN MONTH(rxdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(rxdate)+543,2))
                WHEN MONTH(rxdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(rxdate)+543,2))
                END AS "month",COUNT(DISTINCT hn) AS hn,ROUND(SUM(CASE WHEN a.dialysis <> "" THEN 1 ELSE 0 END),2) AS visit,
                ROUND(SUM(CASE WHEN a.dialysis <> "" AND a.hipdata_code = "UCS" THEN 1 ELSE 0 END),2) AS visit_ucs,
                ROUND(SUM(CASE WHEN a.dialysis <> "" AND a.hipdata_code = "OFC" THEN 1 ELSE 0 END),2) AS visit_ofc,
                ROUND(SUM(CASE WHEN a.dialysis <> "" AND a.hipdata_code = "LGO" THEN 1 ELSE 0 END),2) AS visit_lgo,
                ROUND(SUM(CASE WHEN a.dialysis <> "" AND a.hipdata_code NOT IN ("LGO","OFC","UCS") THEN 1 ELSE 0 END),2) AS visit_outher,
                ROUND(SUM(a.dialysis),2) AS dialysis,ROUND(SUM(a.lab),2) AS lab,ROUND(SUM(a.drug),2) AS drug,
                ROUND(SUM(CASE WHEN a.hipdata_code = "UCS" THEN a.dialysis ELSE 0 END),2) AS dialysis_ucs,
                ROUND(SUM(CASE WHEN a.hipdata_code = "OFC" THEN a.dialysis ELSE 0 END),2) AS dialysis_ofc,
                ROUND(SUM(CASE WHEN a.hipdata_code = "LGO" THEN a.dialysis ELSE 0 END),2) AS dialysis_lgo,
                ROUND(SUM(CASE WHEN a.hipdata_code NOT IN ("LGO","OFC","UCS") THEN a.dialysis ELSE 0 END),2) AS dialysis_outher,
                ROUND(SUM(CASE WHEN a.hipdata_code = "UCS" THEN a.lab ELSE 0 END),2) AS lab_ucs,
                ROUND(SUM(CASE WHEN a.hipdata_code = "OFC" THEN a.lab ELSE 0 END),2) AS lab_ofc,
                ROUND(SUM(CASE WHEN a.hipdata_code = "LGO" THEN a.lab ELSE 0 END),2) AS lab_lgo,
                ROUND(SUM(CASE WHEN a.hipdata_code NOT IN ("LGO","OFC","UCS") THEN a.lab ELSE 0 END),2) AS lab_outher,
                ROUND(SUM(CASE WHEN a.hipdata_code = "UCS" THEN a.drug ELSE 0 END),2) AS drug_ucs,
                ROUND(SUM(CASE WHEN a.hipdata_code = "OFC" THEN a.drug ELSE 0 END),2) AS drug_ofc,
                ROUND(SUM(CASE WHEN a.hipdata_code = "LGO" THEN a.drug ELSE 0 END),2) AS drug_lgo,
                ROUND(SUM(CASE WHEN a.hipdata_code NOT IN ("LGO","OFC","UCS") THEN a.drug ELSE 0 END),2) AS drug_outher
                FROM (SELECT o.pttype,p.hipdata_code,o.hn,d.vn,d1.an,IFNULL(d.vn,d1.an) AS vnan,c.clinic,c.regdate,
                IFNULL(d.rxdate,d1.rxdate) AS rxdate,o.icode,o.income,o.sum_price,
                CASE WHEN o.icode = "3003375" THEN o.sum_price ELSE 0 END AS dialysis,
                CASE WHEN o.income = "07" THEN o.sum_price ELSE	0 END AS lab,
                CASE WHEN o.income IN ("03","04","17") THEN o.sum_price ELSE 0 END AS drug
                FROM opitemrece o
                LEFT JOIN (SELECT vn,icode,sum_price,rxdate FROM opitemrece WHERE rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                        AND icode = "3003375" ) d ON d.vn=o.vn
                LEFT JOIN (SELECT an,icode,sum_price,rxdate FROM opitemrece WHERE rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                        AND icode = "3003375" ) d1 ON d1.an=o.an
                LEFT JOIN clinicmember c ON c.hn=o.hn AND o.rxdate >= c.regdate AND c.clinic = "013"
                LEFT JOIN pttype p ON p.pttype=o.pttype
                WHERE o.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND (d.vn <>"" OR d1.an <>"") ) AS a WHERE a.clinic IS NULL OR a.clinic=""
                GROUP BY MONTH(rxdate)
                ORDER BY YEAR(rxdate),MONTH(rxdate)');
        $month = array_column($kidney,'month');
        $hn = array_column($kidney,'hn');
        $visit = array_column($kidney,'visit');
        $visit_ucs = array_column($kidney,'visit_ucs');
        $visit_ofc = array_column($kidney,'visit_ofc');
        $visit_lgo = array_column($kidney,'visit_lgo');
        $visit_outher = array_column($kidney,'visit_outher');
        $dialysis = array_column($kidney,'dialysis');
        $lab = array_column($kidney,'lab');
        $drug = array_column($kidney,'drug');
        $dialysis_ucs = array_column($kidney,'dialysis_ucs');
        $dialysis_ofc = array_column($kidney,'dialysis_ofc');
        $dialysis_lgo = array_column($kidney,'dialysis_lgo');
        $dialysis_outher = array_column($kidney,'dialysis_outher');
        $lab_ucs = array_column($kidney,'lab_ucs'); 
        $lab_ofc = array_column($kidney,'lab_ofc');
        $lab_lgo = array_column($kidney,'lab_lgo');
        $lab_outher = array_column($kidney,'lab_outher');
        $drug_ucs = array_column($kidney,'drug_ucs');
        $drug_ofc = array_column($kidney,'drug_ofc');
        $drug_lgo = array_column($kidney,'drug_lgo');
        $drug_outher = array_column($kidney,'drug_outher');

        return view('service_ncd.kidney_hos',compact('budget_year_select','budget_year','kidney','month','hn','visit','visit_ucs',
                'visit_ofc','visit_lgo','visit_outher','dialysis','lab','drug','dialysis_ucs','dialysis_ofc','dialysis_lgo',
                'dialysis_outher','lab_ucs','lab_ofc',"lab_lgo","lab_outher","drug_ucs","drug_ofc","drug_lgo","drug_outher"));
}

//Create kidney_egfr
public function kidney_egfr(Request $request)
{
        $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
        $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
        $budget_year = $request->budget_year;
        if($budget_year == '' || $budget_year == null)
        {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
        $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
        $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
        $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

        $egfr_list = DB::connection('hosxp')->select('
                SELECT lh.report_date,lh.department,lh.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
                o.cc,IFNULL(v.pdx,a.pdx) AS pdx,li.lab_items_name,lo.lab_order_result,
                CASE WHEN lo.lab_order_result <= 6 THEN "CKD5RRT"
                WHEN lo.lab_order_result <= 15 THEN "CKD5"
                WHEN lo.lab_order_result <=30 THEN "CKD4" END AS "stage",IF(hd.hn <> "","ฟอกแล้ว","") AS hd
                FROM lab_head lh
                LEFT JOIN lab_order lo ON lo.lab_order_number=lh.lab_order_number
                LEFT JOIN lab_items li ON li.lab_items_code=lo.lab_items_code
                LEFT JOIN opdscreen o ON o.vn=lh.vn
                LEFT JOIN vn_stat v ON v.vn=lh.vn
                LEFT JOIN an_stat a ON a.an=lh.vn
                LEFT JOIN patient p ON p.hn=lh.hn
                LEFT JOIN (SELECT o.hn,o.vstdate AS vstdate FROM ovst o
                LEFT JOIN doctor_operation d  ON d.vn=o.vn AND (d.er_oper_code IN ("238","239","240") OR icd9 ="3995")
                LEFT JOIN iptoprt io ON io.an=o.an AND io.icd9 = "3995"
                LEFT JOIN ipt_nurse_oper ino ON ino.an=o.an AND ino.ipt_oper_code="187"
                WHERE (d.er_oper_code <>"" OR d.icd9 <>"" OR io.icd9 <>"" OR ino.ipt_oper_code <>"")
                AND o.vstdate <= "'.$end_date.'"
                GROUP BY o.hn) AS hd ON hd.hn=lh.hn
                WHERE lh.report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND lo.lab_items_code="693" AND lo.lab_order_result <= 30	
                AND lo.lab_order_result <>"" AND lo.lab_order_result <>"ไม่มีข้อมูลส่วนสูง"
                AND lo.lab_order_result <>"-" AND lo.lab_order_result NOT LIKE "*%"
                AND lo.lab_order_result = (SELECT lo1.lab_order_result FROM lab_head lh1
                LEFT JOIN lab_order lo1 ON lo1.lab_order_number=lh1.lab_order_number
                WHERE lh1.report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND lo1.lab_items_code="693" AND lh1.hn=lh.hn ORDER BY ROUND(lo1.lab_order_result,2) limit 1)	
                AND lh.report_date = (SELECT lh1.report_date FROM lab_head lh1
                LEFT JOIN lab_order lo1 ON lo1.lab_order_number=lh1.lab_order_number
                WHERE lh1.report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND lo1.lab_items_code="693" AND lh1.hn=lh.hn ORDER BY ROUND(lo1.lab_order_result,2),lh1.report_date DESC limit 1)	
                ORDER BY ROUND(lo.lab_order_result,2),lh.report_date,lh.report_time');

        $egfr_sum = DB::connection('hosxp')->select('
                SELECT COUNT(hn) AS total,
                SUM(CASE WHEN stage = "CKD5RRT" THEN 1 ELSE 0 END) AS "ckd5rrt", 
                SUM(CASE WHEN stage = "CKD5RRT" AND hd="Y"  THEN 1 ELSE 0 END) AS "ckd5rrt_hd",
                SUM(CASE WHEN stage = "CKD5RRT" AND hd=""  THEN 1 ELSE 0 END) AS "ckd5rrt_hd_n",
                SUM(CASE WHEN stage = "CKD5" THEN 1 ELSE 0 END) AS "ckd5", 
                SUM(CASE WHEN stage = "CKD5" AND hd="Y"  THEN 1 ELSE 0 END) AS "ckd5_hd",
                SUM(CASE WHEN stage = "CKD5" AND hd=""  THEN 1 ELSE 0 END) AS "ckd5_hd_n",
                SUM(CASE WHEN stage = "CKD4" THEN 1 ELSE 0 END) AS "ckd4",
                SUM(CASE WHEN stage = "CKD4" AND hd="Y"  THEN 1 ELSE 0 END) AS "ckd4_hd",
                SUM(CASE WHEN stage = "CKD4" AND hd=""  THEN 1 ELSE 0 END) AS "ckd4_hd_n"
                FROM(SELECT lh.report_date,lh.department,lh.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
                o.cc,IFNULL(v.pdx,a.pdx) AS pdx,li.lab_items_name,lo.lab_order_result,
                CASE WHEN lo.lab_order_result <= 6 THEN "CKD5RRT"
                WHEN lo.lab_order_result <= 15 THEN "CKD5"
                WHEN lo.lab_order_result <=30 THEN "CKD4" END AS "stage",IF(hd.hn <> "","Y","") AS hd
                FROM lab_head lh
                LEFT JOIN lab_order lo ON lo.lab_order_number=lh.lab_order_number
                LEFT JOIN lab_items li ON li.lab_items_code=lo.lab_items_code
                LEFT JOIN opdscreen o ON o.vn=lh.vn
                LEFT JOIN vn_stat v ON v.vn=lh.vn
                LEFT JOIN an_stat a ON a.an=lh.vn
                LEFT JOIN patient p ON p.hn=lh.hn
                LEFT JOIN (SELECT o.hn,o.vstdate AS vstdate FROM ovst o
                LEFT JOIN doctor_operation d  ON d.vn=o.vn AND (d.er_oper_code IN ("238","239","240") OR icd9 ="3995")
                LEFT JOIN iptoprt io ON io.an=o.an AND io.icd9 = "3995"
                LEFT JOIN ipt_nurse_oper ino ON ino.an=o.an AND ino.ipt_oper_code="187"
                WHERE (d.er_oper_code <>"" OR d.icd9 <>"" OR io.icd9 <>"" OR ino.ipt_oper_code <>"")
                AND o.vstdate <= "'.$end_date.'"
                GROUP BY o.hn) AS hd ON hd.hn=lh.hn
                WHERE lh.report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND lo.lab_items_code="693" AND lo.lab_order_result <= 30	
                AND lo.lab_order_result <>"" AND lo.lab_order_result <>"ไม่มีข้อมูลส่วนสูง"
                AND lo.lab_order_result <>"-" AND lo.lab_order_result NOT LIKE "*%"
                AND lo.lab_order_result = (SELECT lo1.lab_order_result FROM lab_head lh1
                LEFT JOIN lab_order lo1 ON lo1.lab_order_number=lh1.lab_order_number
                WHERE lh1.report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND lo1.lab_items_code="693" AND lh1.hn=lh.hn ORDER BY ROUND(lo1.lab_order_result,2) limit 1)	
                AND lh.report_date = (SELECT lh1.report_date FROM lab_head lh1
                LEFT JOIN lab_order lo1 ON lo1.lab_order_number=lh1.lab_order_number
                WHERE lh1.report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND lo1.lab_items_code="693" AND lh1.hn=lh.hn ORDER BY ROUND(lo1.lab_order_result,2),lh1.report_date DESC limit 1)	
                ORDER BY ROUND(lo.lab_order_result,2),lh.report_date,lh.report_time) AS a ');
        foreach ($egfr_sum as $row){
        $ckd5rrt=$row->ckd5rrt;
        $ckd5rrt_hd=$row->ckd5rrt_hd;
        $ckd5rrt_hd_n=$row->ckd5rrt_hd_n;
        $ckd5=$row->ckd5;
        $ckd5_hd=$row->ckd5_hd;
        $ckd5_hd_n=$row->ckd5_hd_n;
        $ckd4=$row->ckd4;
        $ckd4_hd=$row->ckd4_hd;
        $ckd4_hd_n=$row->ckd4_hd_n;
        } 
                
        return view('service_ncd.kidney_egfr',compact('budget_year_select','budget_year','egfr_list','ckd5rrt',
                'ckd5rrt_hd','ckd5rrt_hd_n','ckd5','ckd5_hd','ckd5_hd_n','ckd4','ckd4_hd','ckd4_hd_n'));
}

//Create asthma_clinic
public function asthma_clinic(Request $request)
{
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
    $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
    $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
    $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

    $clinic = DB::connection('hosxp')->select('
            SELECT clinic_member_status_name,COUNT(hn) AS total
            FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
            c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value,
            c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
            y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
            u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
            cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
            FROM clinicmember c
            LEFT OUTER JOIN patient p ON p.hn = c.hn
            LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
            LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
            LEFT OUTER JOIN sex s ON s.CODE = p.sex
            LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
            LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
            LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
            LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
            LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
            LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
            AND ta.amppart = p.amppart
            AND ta.tmbpart = p.tmbpart
            AND ta.codetype = "3"
            WHERE c.clinic = "011"
            GROUP BY c.hn
            ORDER BY c.pt_number,c.regdate) AS a
            GROUP BY clinic_member_status_id
            ORDER BY total DESC');
    $clinic_member_status = array_column($clinic,'clinic_member_status_name');
    $clinic_total = array_column($clinic,'total');

    $newcase_month = DB::connection('hosxp')->select('
            SELECT CASE WHEN MONTH(regdate)="10" THEN "ต.ค."
            WHEN MONTH(regdate)="11" THEN "พ.ย."
            WHEN MONTH(regdate)="12" THEN "ธ.ค."
            WHEN MONTH(regdate)="1" THEN "ม.ค."
            WHEN MONTH(regdate)="2" THEN "ก.พ."
            WHEN MONTH(regdate)="3" THEN "มี.ค."
            WHEN MONTH(regdate)="4" THEN "เม.ย."
            WHEN MONTH(regdate)="5" THEN "พ.ค."
            WHEN MONTH(regdate)="6" THEN "มิ.ย."
            WHEN MONTH(regdate)="7" THEN "ก.ค."
            WHEN MONTH(regdate)="8" THEN "ส.ค."
            WHEN MONTH(regdate)="9" THEN "ก.ย."
            END AS "month",
            COUNT(hn) AS total
            FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
            c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value,
            c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
            y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
            u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
            cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
            FROM clinicmember c
            LEFT OUTER JOIN patient p ON p.hn = c.hn
            LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
            LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
            LEFT OUTER JOIN sex s ON s.CODE = p.sex
            LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
            LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
            LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
            LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
            LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
            LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
            AND ta.amppart = p.amppart
            AND ta.tmbpart = p.tmbpart
            AND ta.codetype = "3"
            WHERE c.clinic = "011" AND c.regdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY c.hn
            ORDER BY c.pt_number,c.regdate) AS a
            GROUP BY MONTH(regdate)
            ORDER BY YEAR(regdate),MONTH(regdate)');
    $newcase_m = array_column($newcase_month,'month');
    $newcase_total_m = array_column($newcase_month,'total');

    $newcase_year = DB::connection('hosxp')->select('
            SELECT IF(MONTH(regdate)>9,YEAR(regdate)+1,YEAR(regdate)) + 543 AS year_bud,
            COUNT(hn) AS total
            FROM (SELECT n.NAME AS clinic_name,p.cid,c.hn,concat( p.pname, p.fname,SPACE(1), p.lname ) AS patient_name,
            c.regdate,lastvisit,c.last_hba1c_date,c.last_hba1c_value,c.last_ua_date,c.last_ua_value,
            c.last_bp_date,CONCAT(c.last_bp_bps_value,"/",c.last_bp_bpd_value) AS last_bp_value,c.last_fbs_date,c.last_fbs_value,
            y.NAME AS pttype_name,s.NAME AS sex_name,d.NAME AS doctor_name,cm.clinic_member_status_name,c.clinic_member_status_id,
            u.NAME AS staff_name,ov1.vstdate AS last_cormobidity_screen_date,concat(ph.hosptype,SPACE(1), ph.NAME ) AS send_pcu_hospital_name,
            cast(concat(p.addrpart, " หมู่ ", p.moopart, " ", ta.full_name) AS CHAR (500)) AS addr_name
            FROM clinicmember c
            LEFT OUTER JOIN patient p ON p.hn = c.hn
            LEFT OUTER JOIN clinic n ON n.clinic = c.clinic
            LEFT OUTER JOIN pttype y ON y.pttype = p.pttype
            LEFT OUTER JOIN sex s ON s.CODE = p.sex
            LEFT OUTER JOIN doctor d ON d.CODE = c.doctor
            LEFT OUTER JOIN clinic_member_status cm ON cm.clinic_member_status_id = c.clinic_member_status_id
            LEFT OUTER JOIN opduser u ON u.loginname = c.modify_staff
            LEFT OUTER JOIN ovst ov1 ON ov1.vn = c.last_cormobidity_screen_vn
            LEFT OUTER JOIN hospcode ph ON ph.hospcode = c.send_to_pcu_hcode
            LEFT OUTER JOIN thaiaddress ta ON ta.chwpart = p.chwpart
            AND ta.amppart = p.amppart
            AND ta.tmbpart = p.tmbpart
            AND ta.codetype = "3"
            WHERE c.clinic = "011" AND c.regdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            GROUP BY c.hn
            ORDER BY c.pt_number,c.regdate) AS a
            GROUP BY year_bud
            ORDER BY year_bud');
    $newcase_y = array_column($newcase_year,'year_bud');
    $newcase_total_y = array_column($newcase_year,'total');

    return view('service_ncd.asthma_clinic',compact('budget_year_select','budget_year','clinic_member_status','clinic_total',
            'newcase_m','newcase_total_m','newcase_y','newcase_total_y'));
}


}
