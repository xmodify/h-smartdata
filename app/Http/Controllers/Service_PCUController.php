<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service_PCUController extends Controller
{
//Check Login
public function __construct()
{
      $this->middleware('auth');
}

//Create index
public function index()
{     
      return view('service_pcu.index');            
}

//หมู่บ้านในเขตรับผิดชอบ
public function pcu1_village (Request $request) 
{     
      $village_select = DB::connection('hosxp')->select('select * from village where village_id <>1'); 
      $village = $request->village;
      if($village = '' || $village == null)
      {$village ='2';}else{$village =$request->village;}   
      $village_name = DB::connection('hosxp')->table('village')->where('village_id',$village)->value('village_name'); 
      $house = DB::connection('hosxp')->select(' 
            SELECT v.village_name,h.house_id,h.census_id,h.address,h.latitude,h.longitude,
            COUNT(p.person_id) AS person_count,d1.NAME AS doctor_name1,d2.NAME AS doctor_name2,
            CONCAT(p2.person_id,"-",p2.pname, p2.fname,SPACE(1), p2.lname ) AS vms_name,
            CONCAT(p3.person_id,"-",p3.vom_name) AS vms_name8
            FROM house h
            LEFT JOIN village v ON v.village_id=h.village_id
            LEFT JOIN person p ON p.house_id = h.house_id
            LEFT JOIN doctor d1 ON d1.CODE = h.doctor_code
            LEFT JOIN doctor d2 ON d2.CODE = h.doctor_code2
            LEFT JOIN person p2 ON p2.person_id = h.vms_person_id 
            LEFT JOIN (SELECT vms.village_id,vms.house_id,vom.person_id,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS vom_name
            FROM village_organization v
            INNER JOIN village_organization_member vom  on v.village_organization_id = vom.village_organization_id
            INNER JOIN village_organization_member_service vms on vom.village_organization_mid = vms.village_organization_mid
            LEFT JOIN person p ON p.person_id=vom.person_id WHERE v.village_organization_type_id=1 
                  GROUP BY vms.village_id,vms.house_id) AS p3 ON p3.village_id=h.village_id AND p3.house_id = h.house_id
            WHERE h.village_id = "'.$village.'" 
            GROUP BY h.village_id,h.house_id,h.address
            ORDER BY h.village_id,h.house_id +0');

      return view('service_pcu.pcu1_village',compact('village_select','village','village_name','house'));            
}
//ข้อมูลคัดกรองเชิงรุก-ประชากรในเขตรับผิดชอบ (EHP)
public function pcu1_vt_ehp (Request $request)
{     
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      if($start_date == '' || $end_date == null)
      {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
      if($end_date == '' || $end_date == null)
      {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
      $village_select = DB::connection('hosxp')->select('select * from village where village_id <>1'); 
      $village = $request->village;
      if($village = '' || $village == null)
      {$village ='2';}else{$village =$request->village;}   
      $village_name = DB::connection('hosxp')->table('village')->where('village_id',$village)->value('village_name'); 
      $ehp = DB::connection('hosxp')->select(' 
            SELECT CONCAT(p3.pname,p3.fname,SPACE(1),p3.lname ) AS officer_name,
            v.village_name,CONCAT(p2.pname,p2.fname,SPACE(1),p2.lname ) AS ptname,
            h.address,DATE(p.person_vt_datetime) AS date_vt,
            MAX(CASE WHEN p.person_vt_type_id ="1" THEN TIME(p.person_vt_datetime) END) AS bp_time,
            MAX(CASE WHEN p.person_vt_type_id ="1" THEN p.person_vt_test_result_text END) AS bp,
            MAX(CASE WHEN p.person_vt_type_id ="2" THEN TIME(p.person_vt_datetime) END) AS temp_time,
            MAX(CASE WHEN p.person_vt_type_id ="2" THEN p.person_vt_test_result_text END) AS temp,
            MAX(CASE WHEN p.person_vt_type_id ="3" THEN TIME(p.person_vt_datetime) END) AS bw_time,
            MAX(CASE WHEN p.person_vt_type_id ="3" THEN p.person_vt_test_result_text END) AS bw,
            MAX(CASE WHEN p.person_vt_type_id ="4" THEN TIME(p.person_vt_datetime) END) AS bgm_time,
            MAX(CASE WHEN p.person_vt_type_id ="4" THEN p.person_vt_test_result_text END) AS bgm
            FROM person_vt_test_result p
            LEFT JOIN person_vt_type p1 ON p1.person_vt_type_id = p.person_vt_type_id
            LEFT JOIN person p2 ON p2.cid = p.cid 
            LEFT JOIN person p3 ON p3.cid = p.officer_cid 
            LEFT JOIN house h ON h.house_id=p2.house_id
            LEFT JOIN village v ON v.village_id=h.village_id
            WHERE DATE(p.person_vt_datetime) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND h.village_id = "'.$village.'" 
            GROUP BY p.cid,DATE(p.person_vt_datetime)
            ORDER BY v.village_id,p.officer_cid,h.address+0,p.person_vt_datetime');

      return view('service_pcu.pcu1_vt_ehp',compact('start_date','end_date','village_select','village','village_name','ehp'));            
}
//การเยี่ยมบ้าน ประชากรในเขตรับผิดชอบ
public function pcu1_home_visit (Request $request)
{     
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
      
      $home_visit_month = DB::connection('hosxp')->select(' 
            SELECT CASE WHEN MONTH(a.visit_date)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(a.visit_date)+543,2))
            WHEN MONTH(a.visit_date)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(a.visit_date)+543,2))
            WHEN MONTH(a.visit_date)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(a.visit_date)+543,2))
            WHEN MONTH(a.visit_date)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(a.visit_date)+543,2))
            WHEN MONTH(a.visit_date)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(a.visit_date)+543,2))
            WHEN MONTH(a.visit_date)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(a.visit_date)+543,2))
            WHEN MONTH(a.visit_date)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(a.visit_date)+543,2))
            WHEN MONTH(a.visit_date)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(a.visit_date)+543,2))
            WHEN MONTH(a.visit_date)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(a.visit_date)+543,2))
            WHEN MONTH(a.visit_date)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(a.visit_date)+543,2))
            WHEN MONTH(a.visit_date)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(a.visit_date)+543,2))
            WHEN MONTH(a.visit_date)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(a.visit_date)+543,2))
            END AS "month",COUNT(DISTINCT a.patient_hn) AS hn,COUNT(DISTINCT a.person_visit_id) AS visit ,
            SUM(CASE WHEN a.village_id =2 THEN 1 ELSE 0 END) AS "moo1",
            SUM(CASE WHEN a.village_id =3 THEN 1 ELSE 0 END) AS "moo2",
            SUM(CASE WHEN a.village_id =4 THEN 1 ELSE 0 END) AS "moo3",
            SUM(CASE WHEN a.village_id =5 THEN 1 ELSE 0 END) AS "moo4",
            SUM(CASE WHEN a.village_id =6 THEN 1 ELSE 0 END) AS "moo5",
            SUM(CASE WHEN a.village_id =7 THEN 1 ELSE 0 END) AS "moo6",
            SUM(CASE WHEN a.village_id =8 THEN 1 ELSE 0 END) AS "moo7"
            FROM(SELECT p.village_id,vl.village_name,h.address,p.cid,p.patient_hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            pv.visit_date,pv.visit_time,pv.visit_staff,pv.visit_note,pv.visit_problem,pv.bw,pv.height,pv.bps,pv.bpd,
            pv.person_visit_id,pv.pulse,pv.rr,pv.temperature,pv.dx1,pv.visit_advice,pt.person_visit_type_name
            FROM person p LEFT OUTER JOIN house h ON h.house_id = p.house_id
            LEFT OUTER JOIN village vl ON vl.village_id = h.village_id
            INNER JOIN person_visit pv ON pv.person_id=p.person_id
            LEFT JOIN person_visit_type pt ON pt.person_visit_type_id=pv.person_visit_type_id
            WHERE pv.visit_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND p.village_id IN ( 2, 3, 4, 5, 6, 7, 8 ) ORDER BY p.village_id ) AS a
            GROUP BY MONTH(a.visit_date)
            ORDER BY YEAR(a.visit_date) , MONTH(a.visit_date)');

      $home_visit_m = array_column($home_visit_month,'month');
      $home_visit_hn_m = array_column($home_visit_month,'hn');
      $home_visit_visit_m = array_column($home_visit_month,'visit');
      $home_visit_moo1 = array_column($home_visit_month,'moo1');
      $home_visit_moo2 = array_column($home_visit_month,'moo2');
      $home_visit_moo3 = array_column($home_visit_month,'moo3');
      $home_visit_moo4 = array_column($home_visit_month,'moo4');
      $home_visit_moo5 = array_column($home_visit_month,'moo5');
      $home_visit_moo6 = array_column($home_visit_month,'moo6');
      $home_visit_moo7 = array_column($home_visit_month,'moo7');

      $home_visit_year = DB::connection('hosxp')->select(' 
            SELECT IF(MONTH(visit_date)>9,YEAR(visit_date)+1,YEAR(visit_date)) + 543 AS year_bud,
            COUNT(DISTINCT a.patient_hn) AS hn,COUNT(DISTINCT a.person_visit_id) AS visit 
            FROM(SELECT vl.village_name,h.address,p.cid,p.patient_hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            pv.visit_date,pv.visit_time,pv.visit_staff,pv.visit_note,pv.visit_problem,pv.bw,pv.height,pv.bps,pv.bpd,
            pv.person_visit_id,pv.pulse,pv.rr,pv.temperature,pv.dx1,pv.visit_advice,pt.person_visit_type_name
            FROM person p
            LEFT OUTER JOIN house h ON h.house_id = p.house_id
            LEFT OUTER JOIN village vl ON vl.village_id = h.village_id
            INNER JOIN person_visit pv ON pv.person_id=p.person_id
            LEFT JOIN person_visit_type pt ON pt.person_visit_type_id=pv.person_visit_type_id
            WHERE pv.visit_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND p.village_id IN ( 2, 3, 4, 5, 6, 7, 8 ) ORDER BY p.village_id ) AS a
            GROUP BY year_bud  ORDER BY year_bud');

      $home_visit_y = array_column($home_visit_year,'year_bud');
      $home_visit_hn_y = array_column($home_visit_year,'hn');
      $home_visit_visit_y = array_column($home_visit_year,'visit');

      
      $home_visit_list = DB::connection('hosxp')->select(' 
            SELECT vl.village_name,h.address,p.cid,p.patient_hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            pv.visit_date,pv.visit_time,pv.visit_staff,pv.visit_note,pv.visit_problem,pv.bw,pv.height,pv.bps,pv.bpd,
            pv.person_visit_id,pv.pulse,pv.rr,pv.temperature,pv.dx1,pv.visit_advice,pt.person_visit_type_name,pv.visit_service
            FROM person p
            LEFT OUTER JOIN house h ON h.house_id = p.house_id
            LEFT OUTER JOIN village vl ON vl.village_id = h.village_id
            INNER JOIN person_visit pv ON pv.person_id=p.person_id
            LEFT JOIN person_visit_type pt ON pt.person_visit_type_id=pv.person_visit_type_id
            WHERE pv.visit_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND p.village_id IN ( 2, 3, 4, 5, 6, 7, 8 ) ORDER BY p.village_id ');

      return view('service_pcu.pcu1_home_visit',compact('budget_year_select','budget_year','home_visit_m','home_visit_list',
            'home_visit_hn_m','home_visit_visit_m','home_visit_y','home_visit_hn_y','home_visit_visit_y','home_visit_moo1',
            'home_visit_moo2','home_visit_moo3','home_visit_moo4','home_visit_moo5','home_visit_moo6','home_visit_moo7'));            
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
    
      $diag_top30 = DB::connection('hosxp')->select('
            SELECT CONCAT("[",v.pdx,"] " ,i.name) AS name,COUNT(v.pdx) as sum, 
            sum(CASE WHEN v.sex=1 THEN 1 ELSE 0 END) as male,   
            sum(CASE WHEN v.sex=2 THEN 1 ELSE 0 END) as female   
            FROM vn_stat v   
            LEFT JOIN icd101 i ON i.code=v.pdx 
		LEFT JOIN person p ON p.patient_hn=v.hn
            WHERE v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"  
            AND v.pdx<>"" AND v.pdx IS NOT NULL AND v.pdx NOT LIKE "z%" AND v.pdx NOT IN ("U119")
		AND p.village_id IN ("2","3","4","5","6","7","8") AND p.death = "N"
            GROUP BY v.pdx,i.name  
            ORDER BY sum DESC limit 30');

      $diag_top30_ipd = DB::connection('hosxp')->select('
            SELECT  CONCAT("[",a.pdx,"] " ,i.name) AS name,count(a.pdx) AS sum,
            sum(CASE WHEN a.sex=1 THEN 1 ELSE 0 END) as male,
            sum(CASE WHEN a.sex=2 THEN 1 ELSE 0 END) as female
            FROM an_stat a
            LEFT JOIN icd101 i ON i.code=a.pdx 
		LEFT JOIN person p ON p.patient_hn=a.hn
            WHERE a.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND a.pdx<>"" AND a.pdx IS NOT NULL AND a.pdx NOT LIKE "z%"
            AND a.pdx NOT IN ("Z290","Z208") 
		AND p.village_id IN ("2","3","4","5","6","7","8") AND p.death = "N"
            GROUP BY a.pdx,i.name
            ORDER BY sum DESC limit 30');

      return view('service_pcu.diag_top30',compact('budget_year_select','budget_year','diag_top30','diag_top30_ipd'));          
}

//Create death
public function death(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
    
      $death_504 = DB::connection('hosxp')->select('
            SELECT IF(c1.name1="" OR c1.name1 IS NULL,"ไม่มีรหัสโรค",c1.name1) AS name,
            SUM(CASE WHEN pt.sex=1 THEN 1 ELSE 0 END) as male,   
            SUM(CASE WHEN pt.sex=2 THEN 1 ELSE 0 END) as female,   
            COUNT(DISTINCT d.hn) AS "sum"
            FROM death d
            LEFT OUTER JOIN person pt ON pt.patient_hn = d.hn
            LEFT OUTER JOIN rpt_504_name c1 ON c1.id = d.death_cause
            LEFT OUTER JOIN icd101 i1 ON i1.CODE = d.death_diag_1 
            WHERE d.death_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND pt.village_id IN ("2","3","4","5","6","7","8") 
            GROUP BY d.death_cause
            ORDER BY COUNT(DISTINCT d.hn) DESC');

      $death_icd10 = DB::connection('hosxp')->select('
            SELECT IF(CONCAT("[",d.death_diag_1,"] ",i1.NAME) ="" OR CONCAT("[",d.death_diag_1,"] ",i1.NAME) IS Null,
            "ไม่บันทึกรหัสโรค",CONCAT("[",d.death_diag_1,"] ",i1.NAME)) AS name,
            SUM(CASE WHEN pt.sex=1 THEN 1 ELSE 0 END) AS male,   
            SUM(CASE WHEN pt.sex=2 THEN 1 ELSE 0 END) AS female,   
            COUNT(DISTINCT d.hn) AS "sum"
            FROM death d
            LEFT OUTER JOIN person pt ON pt.patient_hn = d.hn
            LEFT OUTER JOIN rpt_504_name c1 ON c1.id = d.death_cause
            LEFT OUTER JOIN icd101 i1 ON i1.CODE = d.death_diag_1 
            WHERE	d.death_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND pt.village_id IN ("2","3","4","5","6","7","8") 
            GROUP BY d.death_diag_1 
            ORDER BY COUNT(DISTINCT d.hn) DESC');

      return view('service_pcu.death',compact('budget_year_select','budget_year','death_504','death_icd10'));            
}

}
