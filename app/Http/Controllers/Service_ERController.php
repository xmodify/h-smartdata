<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service_ERController extends Controller
{
//Check Login
      public function __construct()
{
      $this->middleware('auth');
}

//Create index
      public function index()
{
        return view('service_er.index');

}

//Create index
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

      $er_month = DB::connection('hosxp')->select('select
                        CASE WHEN MONTH(vstdate)="10" THEN "ต.ค."
                        WHEN MONTH(vstdate)="11" THEN "พ.ย."
                        WHEN MONTH(vstdate)="12" THEN "ธ.ค."
                        WHEN MONTH(vstdate)="1" THEN "ม.ค."
                        WHEN MONTH(vstdate)="2" THEN "ก.พ."
                        WHEN MONTH(vstdate)="3" THEN "มี.ค."
                        WHEN MONTH(vstdate)="4" THEN "เม.ย."
                        WHEN MONTH(vstdate)="5" THEN "พ.ค."
                        WHEN MONTH(vstdate)="6" THEN "มิ.ย."
                        WHEN MONTH(vstdate)="7" THEN "ก.ค."
                        WHEN MONTH(vstdate)="8" THEN "ส.ค."
                        WHEN MONTH(vstdate)="9" THEN "ก.ย."
                        END AS "month",
                        COUNT(DISTINCT vn) as visit
                        FROM er_regist
                        WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                        GROUP BY MONTH(vstdate)
                        ORDER BY YEAR(vstdate) , MONTH(vstdate)');
      $er_m = array_column($er_month,'month');
      $er_visit_m = array_column($er_month,'visit');

      $er_year = DB::connection('hosxp')->select('
                  SELECT IF(MONTH(o.vstdate)>9,YEAR(o.vstdate)+1,YEAR(o.vstdate)) + 543 AS year_bud,
                  COUNT(DISTINCT o.vn) as visit,COUNT(DISTINCT o.hn) as hn
                  FROM ovst o
			INNER JOIN er_regist e ON e.vn=o.vn
                  WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
                  GROUP BY year_bud ');
      $er_y = array_column($er_year,'year_bud');
      $er_visit_y = array_column($er_year,'visit');

      return view('service_er.count', compact('budget_year_select','budget_year','er_m','er_visit_m','er_y','er_visit_y'));

}

//Create er_type
public function er_type(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

      $er_month = DB::connection('hosxp')->select('select
                        CASE WHEN MONTH(vstdate)="10" THEN "ต.ค."
                        WHEN MONTH(vstdate)="11" THEN "พ.ย."
                        WHEN MONTH(vstdate)="12" THEN "ธ.ค."
                        WHEN MONTH(vstdate)="1" THEN "ม.ค."
                        WHEN MONTH(vstdate)="2" THEN "ก.พ."
                        WHEN MONTH(vstdate)="3" THEN "มี.ค."
                        WHEN MONTH(vstdate)="4" THEN "เม.ย."
                        WHEN MONTH(vstdate)="5" THEN "พ.ค."
                        WHEN MONTH(vstdate)="6" THEN "มิ.ย."
                        WHEN MONTH(vstdate)="7" THEN "ก.ค."
                        WHEN MONTH(vstdate)="8" THEN "ส.ค."
                        WHEN MONTH(vstdate)="9" THEN "ก.ย."
                        END AS "month",
                        SUM(CASE WHEN er_emergency_type ="1" THEN 1 ELSE 0 END) AS "Resuscitate",
                        SUM(CASE WHEN er_emergency_type ="2" THEN 1 ELSE 0 END) AS "Emergency",
                        SUM(CASE WHEN er_emergency_type ="3" THEN 1 ELSE 0 END) AS "Urgency",
                        SUM(CASE WHEN er_emergency_type ="4" THEN 1 ELSE 0 END) AS "Semi_Urgency",
                        SUM(CASE WHEN er_emergency_type ="5" THEN 1 ELSE 0 END) AS "Non_Urgency"
                        FROM er_regist
                        WHERE vstdate  BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                        GROUP BY MONTH(vstdate)
                        ORDER BY YEAR(vstdate),MONTH(vstdate)');
      $er_m = array_column($er_month,'month');
      $er_Resuscitate_m = array_column($er_month,'Resuscitate');
      $er_Emergency_m = array_column($er_month,'Emergency');
      $er_Urgency_m = array_column($er_month,'Urgency');
      $er_Semi_Urgency_m = array_column($er_month,'Semi_Urgency');
      $er_Non_Urgency_m = array_column($er_month,'Non_Urgency');

      $er_year = DB::connection('hosxp')->select('select IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
                         SUM(CASE WHEN er_emergency_type ="1" THEN 1	ELSE 0 END) AS "Resuscitate",
                         SUM(CASE WHEN er_emergency_type ="2" THEN 1	ELSE 0 END) AS "Emergency",
                         SUM(CASE WHEN er_emergency_type ="3" THEN 1	ELSE 0 END) AS "Urgency",
                         SUM(CASE WHEN er_emergency_type ="4" THEN 1	ELSE 0 END) AS "Semi_Urgency",
                         SUM(CASE WHEN er_emergency_type ="5" THEN 1	ELSE 0 END) AS "Non_Urgency"
                         FROM er_regist
                         WHERE vstdate  BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
                         GROUP BY year_bud
                         ORDER BY YEAR(vstdate),MONTH(vstdate)');
      $er_y = array_column($er_year,'year_bud');
      $er_Resuscitate_y = array_column($er_year,'Resuscitate');
      $er_Emergency_y = array_column($er_year,'Emergency');
      $er_Urgency_y = array_column($er_year,'Urgency');
      $er_Semi_Urgency_y = array_column($er_year,'Semi_Urgency');
      $er_Non_Urgency_y = array_column($er_year,'Non_Urgency');

      return view('service_er.er_type',
             compact('budget_year_select','budget_year','er_m','er_Resuscitate_m','er_Emergency_m','er_Urgency_m','er_Semi_Urgency_m','er_Non_Urgency_m',
                     'er_y','er_Resuscitate_y','er_Emergency_y','er_Urgency_y','er_Semi_Urgency_y','er_Non_Urgency_y'));
}

//Create er_oper
public function er_oper(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

      $er_oper_month = DB::connection('hosxp')->select('select
                        CASE WHEN MONTH(vstdate)="10" THEN "ต.ค."
                        WHEN MONTH(vstdate)="11" THEN "พ.ย."
                        WHEN MONTH(vstdate)="12" THEN "ธ.ค."
                        WHEN MONTH(vstdate)="1" THEN "ม.ค."
                        WHEN MONTH(vstdate)="2" THEN "ก.พ."
                        WHEN MONTH(vstdate)="3" THEN "มี.ค."
                        WHEN MONTH(vstdate)="4" THEN "เม.ย."
                        WHEN MONTH(vstdate)="5" THEN "พ.ค."
                        WHEN MONTH(vstdate)="6" THEN "มิ.ย."
                        WHEN MONTH(vstdate)="7" THEN "ก.ค."
                        WHEN MONTH(vstdate)="8" THEN "ส.ค."
                        WHEN MONTH(vstdate)="9" THEN "ก.ย."
                        END AS "month",
                        SUM(CASE WHEN icd9 = "9604" OR er_oper_code = "13" THEN 1 ELSE 0 END) AS "intube",
                        SUM(CASE WHEN icd9 = "9960" OR er_oper_code = "112" THEN 1 ELSE 0 END) AS "cpr"
                        FROM (SELECT o.vn,o.vstdate,d.er_oper_code,d.icd9 FROM ovst o
                        LEFT JOIN doctor_operation d  ON d.vn=o.vn
                        WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                        AND (d.er_oper_code IN ("13","112") OR icd9 IN ("9604","9960"))
                        GROUP BY o.vn,d.er_oper_code,d.icd9) AS a
                        GROUP BY MONTH(vstdate)
                        ORDER BY YEAR(vstdate),MONTH(vstdate)');
      $er_oper_m = array_column($er_oper_month,'month');
      $er_oper_intube_m = array_column($er_oper_month,'intube');
      $er_oper_cpr_m = array_column($er_oper_month,'cpr');

      $er_oper_year = DB::connection('hosxp')->select('select
                  IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
                  SUM(CASE WHEN icd9 = "9604" OR er_oper_code = "13" THEN 1 ELSE 0 END) AS "intube",
                  SUM(CASE WHEN icd9 = "9960" OR er_oper_code = "112" THEN 1 ELSE 0 END) AS "cpr"
                  FROM (SELECT o.vn,o.vstdate,d.er_oper_code,d.icd9 FROM ovst o
                  LEFT JOIN doctor_operation d  ON d.vn=o.vn
                  WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
                  AND (d.er_oper_code IN ("13","112") OR icd9 IN ("9604","9960"))
                  GROUP BY o.vn,d.er_oper_code,d.icd9) AS a
                  GROUP BY year_bud
                  ORDER BY year_bud');
      $er_oper_y = array_column($er_oper_year,'year_bud');
      $er_oper_intube_y = array_column($er_oper_year,'intube');
      $er_oper_cpr_y = array_column($er_oper_year,'cpr');

      $er_oper_list_intube = DB::connection('hosxp')->select('select
                  p.hn,d.vn,o.vstdate,o.vsttime,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
                  v.pdx,d.icd9,e.`name` AS opd_oper,r.refer_date,r.refer_time,r.pdx AS pdx_refer
                  FROM ovst o
                  LEFT JOIN doctor_operation d  ON d.vn=o.vn
                  LEFT JOIN vn_stat v ON v.vn=d.vn
                  LEFT JOIN er_oper_code e ON e.er_oper_code=d.er_oper_code
                  LEFT JOIN patient p ON p.hn=o.hn
                  LEFT JOIN referout r ON r.vn=d.vn
                  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                  AND (d.er_oper_code IN ("13") OR d.icd9 IN ("9604"))
                  GROUP BY d.vn');
      $er_oper_list_cpr = DB::connection('hosxp')->select('select
                  p.hn,d.vn,o.vstdate,o.vsttime,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
                  v.pdx,d.icd9,e.`name` AS opd_oper,r.refer_date,r.refer_time,r.pdx AS pdx_refer
                  FROM ovst o
                  LEFT JOIN doctor_operation d  ON d.vn=o.vn
                  LEFT JOIN vn_stat v ON v.vn=d.vn
                  LEFT JOIN er_oper_code e ON e.er_oper_code=d.er_oper_code
                  LEFT JOIN patient p ON p.hn=o.hn
                  LEFT JOIN referout r ON r.vn=d.vn
                  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                  AND (d.er_oper_code IN ("112") OR d.icd9 IN ("9960"))
                  GROUP BY d.vn');

      return view('service_er.er_oper',compact('budget_year_select','budget_year','er_oper_m','er_oper_intube_m','er_oper_cpr_m',
      'er_oper_y','er_oper_intube_y','er_oper_cpr_y','er_oper_list_intube','er_oper_list_cpr'));
}

//Create ems
public function ems(Request $request)
{
      $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
      $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
      $budget_year = $request->budget_year;
      if($budget_year == '' || $budget_year == null)
      {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
      $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
      $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
      $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

      $ems_month = DB::connection('hosxp')->select('
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
            END AS "month",SUM(CASE WHEN o.ovstist = "08" THEN 1 ELSE 0 END) AS "als",
            SUM(CASE WHEN o.ovstist = "09" THEN 1 ELSE 0 END) AS "ils",
            SUM(CASE WHEN o.ovstist = "10" THEN 1 ELSE 0 END) AS "fr"
            FROM ovst o
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND o.ovstist IN ("08","09","10")
            GROUP BY MONTH(o.vstdate)
            ORDER BY YEAR(o.vstdate) , MONTH(o.vstdate)');
      $ems_m = array_column($ems_month,'month');
      $ems_als_m = array_column($ems_month,'als');
      $ems_ils_m = array_column($ems_month,'ils');
      $ems_fr_m = array_column($ems_month,'fr');

      $ems_year = DB::connection('hosxp')->select('
            SELECT
            IF(MONTH(o.vstdate)>9,YEAR(o.vstdate)+1,YEAR(o.vstdate)) + 543 AS year_bud,
            SUM(CASE WHEN o.ovstist = "08" THEN 1 ELSE 0 END) AS "als",
            SUM(CASE WHEN o.ovstist = "09" THEN 1 ELSE 0 END) AS "ils",
            SUM(CASE WHEN o.ovstist = "10" THEN 1 ELSE 0 END) AS "fr"
            FROM ovst o
            WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND o.ovstist IN ("08","09","10")
            GROUP BY year_bud
            ORDER BY year_bud');
      $ems_y = array_column($ems_year,'year_bud');
      $ems_als_y = array_column($ems_year,'als');
      $ems_ils_y = array_column($ems_year,'ils');
      $ems_fr_y = array_column($ems_year,'fr');

      $ems_diag_als = DB::connection('hosxp')->select('select
            CONCAT("[",pdx,"] " ,name) AS name,count(*) AS sum
            FROM (SELECT v.vn,v.hn,v.vstdate,v.pdx,i.name FROM vn_stat v
            LEFT JOIN icd101 i ON i.code=v.pdx
            LEFT JOIN ovst o ON o.vn=v.vn
            WHERE v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx <>"" OR v.pdx IS NOT NULL)
            AND o.ovstist IN ("08")
            AND v.pdx NOT LIKE "z%" AND v.pdx NOT IN ("u119")) AS a
            GROUP BY pdx
            ORDER BY sum desc limit 20');
      $ems_diag_als_name = array_column($ems_diag_als,'name');
      $ems_diag_als_sum = array_column($ems_diag_als,'sum');

      $ems_diag_ils = DB::connection('hosxp')->select('select
            CONCAT("[",pdx,"] " ,name) AS name,count(*) AS sum
            FROM (SELECT v.vn,v.hn,v.vstdate,v.pdx,i.name FROM vn_stat v
            LEFT JOIN icd101 i ON i.code=v.pdx
            LEFT JOIN ovst o ON o.vn=v.vn
            WHERE v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx <>"" OR v.pdx IS NOT NULL)
            AND o.ovstist IN ("09")
            AND v.pdx NOT LIKE "z%" AND v.pdx NOT IN ("u119")) AS a
            GROUP BY pdx
            ORDER BY sum desc limit 20');
      $ems_diag_ils_name = array_column($ems_diag_ils,'name');
      $ems_diag_ils_sum = array_column($ems_diag_ils,'sum');

      $ems_diag_fr = DB::connection('hosxp')->select('select
            CONCAT("[",pdx,"] " ,name) AS name,count(*) AS sum
            FROM (SELECT v.vn,v.hn,v.vstdate,v.pdx,i.name FROM vn_stat v
            LEFT JOIN icd101 i ON i.code=v.pdx
            LEFT JOIN ovst o ON o.vn=v.vn
            WHERE v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx <>"" OR v.pdx IS NOT NULL)
            AND o.ovstist IN ("10")
            AND v.pdx NOT LIKE "z%" AND v.pdx NOT IN ("u119")) AS a
            GROUP BY pdx
            ORDER BY sum desc limit 20');
      $ems_diag_fr_name = array_column($ems_diag_fr,'name');
      $ems_diag_fr_sum = array_column($ems_diag_fr,'sum');

      $ems_list = DB::connection('hosxp')->select('
            SELECT o.vn,o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
            v.age_y,CONCAT(o.pttype," [",p1.hipdata_code,"]") AS pttype,o1.cc,v.pdx,d.`name` AS dx_doctor,
            CASE WHEN o.ovstist = "08" THEN "ALS" WHEN o.ovstist = "09" THEN "FR" WHEN o.ovstist = "10" THEN "ILS" END AS "ems",
            IF(o.an <>"","Admit",null) AS admit,CONCAT(r.refer_hospcode," [",r.pdx,"]") AS refer,
            CASE WHEN e.er_emergency_type ="1" THEN "Resuscitate" WHEN e.er_emergency_type ="2" THEN "Emergency" 
            WHEN e.er_emergency_type ="3" THEN "Urgency" WHEN e.er_emergency_type ="4" THEN "Semi_Urgency"  
            WHEN (e.er_emergency_type ="5" OR e.er_emergency_type IS NULL) THEN "Non_Urgency"END AS "er_emergency_type"
            FROM ovst o
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN pttype p1 ON p1.pttype=o.pttype
            LEFT JOIN opdscreen o1 ON o1.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            LEFT JOIN er_regist e ON e.vn=o.vn
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND o.ovstist IN ("08","09","10")
            GROUP BY o.vn ');
      
      $ems_als_type= DB::connection('hosxp')->select('
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
            END AS "month",SUM(CASE WHEN er_emergency_type ="1" THEN 1 ELSE 0 END) AS "Resuscitate",
            SUM(CASE WHEN er_emergency_type ="2" THEN 1 ELSE 0 END) AS "Emergency",
            SUM(CASE WHEN er_emergency_type ="3" THEN 1 ELSE 0 END) AS "Urgency",
            SUM(CASE WHEN er_emergency_type ="4" THEN 1 ELSE 0 END) AS "Semi_Urgency",
            SUM(CASE WHEN er_emergency_type ="5" THEN 1 ELSE 0 END) AS "Non_Urgency"
            FROM (SELECT o.vn,o.vstdate,e.er_emergency_type
            FROM ovst o LEFT JOIN er_regist e ON e.vn=o.vn
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND o.ovstist IN ("08") GROUP BY o.vn ) AS a
            GROUP BY MONTH(vstdate) ORDER BY YEAR(vstdate),MONTH(vstdate)');
      $ems_als_month = array_column($ems_als_type,'month');
      $ems_als_Resuscitate = array_column($ems_als_type,'Resuscitate');
      $ems_als_Emergency = array_column($ems_als_type,'Emergency');     
      $ems_als_Urgency = array_column($ems_als_type,'Urgency');
      $ems_als_Semi_Urgency = array_column($ems_als_type,'Semi_Urgency');
      $ems_als_Non_Urgency = array_column($ems_als_type,'Non_Urgency');

      $ems_ils_type= DB::connection('hosxp')->select('
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
            END AS "month",SUM(CASE WHEN er_emergency_type ="1" THEN 1 ELSE 0 END) AS "Resuscitate",
            SUM(CASE WHEN er_emergency_type ="2" THEN 1 ELSE 0 END) AS "Emergency",
            SUM(CASE WHEN er_emergency_type ="3" THEN 1 ELSE 0 END) AS "Urgency",
            SUM(CASE WHEN er_emergency_type ="4" THEN 1 ELSE 0 END) AS "Semi_Urgency",
            SUM(CASE WHEN er_emergency_type ="5" THEN 1 ELSE 0 END) AS "Non_Urgency"
            FROM (SELECT o.vn,o.vstdate,e.er_emergency_type
            FROM ovst o LEFT JOIN er_regist e ON e.vn=o.vn
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND o.ovstist IN ("09") GROUP BY o.vn ) AS a
            GROUP BY MONTH(vstdate) ORDER BY YEAR(vstdate),MONTH(vstdate)');
      $ems_ils_month = array_column($ems_ils_type,'month');
      $ems_ils_Resuscitate = array_column($ems_ils_type,'Resuscitate');
      $ems_ils_Emergency = array_column($ems_ils_type,'Emergency');     
      $ems_ils_Urgency = array_column($ems_ils_type,'Urgency');
      $ems_ils_Semi_Urgency = array_column($ems_ils_type,'Semi_Urgency');
      $ems_ils_Non_Urgency = array_column($ems_ils_type,'Non_Urgency');

      $ems_fr_type= DB::connection('hosxp')->select('
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
            END AS "month",SUM(CASE WHEN er_emergency_type ="1" THEN 1 ELSE 0 END) AS "Resuscitate",
            SUM(CASE WHEN er_emergency_type ="2" THEN 1 ELSE 0 END) AS "Emergency",
            SUM(CASE WHEN er_emergency_type ="3" THEN 1 ELSE 0 END) AS "Urgency",
            SUM(CASE WHEN er_emergency_type ="4" THEN 1 ELSE 0 END) AS "Semi_Urgency",
            SUM(CASE WHEN er_emergency_type ="5" THEN 1 ELSE 0 END) AS "Non_Urgency"
            FROM (SELECT o.vn,o.vstdate,e.er_emergency_type
            FROM ovst o LEFT JOIN er_regist e ON e.vn=o.vn
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND o.ovstist IN ("10") GROUP BY o.vn ) AS a
            GROUP BY MONTH(vstdate) ORDER BY YEAR(vstdate),MONTH(vstdate)');
      $ems_fr_month = array_column($ems_fr_type,'month');
      $ems_fr_Resuscitate = array_column($ems_fr_type,'Resuscitate');
      $ems_fr_Emergency = array_column($ems_fr_type,'Emergency');     
      $ems_fr_Urgency = array_column($ems_fr_type,'Urgency');
      $ems_fr_Semi_Urgency = array_column($ems_fr_type,'Semi_Urgency');
      $ems_fr_Non_Urgency = array_column($ems_fr_type,'Non_Urgency');

      return view('service_er.ems',compact('budget_year_select','budget_year','ems_m','ems_als_m','ems_ils_m',
            'ems_fr_m','ems_y','ems_als_y','ems_ils_y','ems_fr_y','ems_diag_als_name','ems_diag_als_sum','ems_diag_ils_name',
            'ems_diag_ils_sum','ems_diag_fr_name','ems_diag_fr_sum','ems_list','ems_als_month','ems_als_Resuscitate','ems_als_Emergency',
            'ems_als_Urgency','ems_als_Semi_Urgency','ems_als_Non_Urgency','ems_ils_month','ems_ils_Resuscitate','ems_ils_Emergency',
            'ems_ils_Urgency','ems_ils_Semi_Urgency','ems_ils_Non_Urgency','ems_fr_month','ems_fr_Resuscitate','ems_fr_Emergency',
            'ems_fr_Urgency','ems_fr_Semi_Urgency','ems_fr_Non_Urgency'));
}

//Create revisit
public function revisit(Request $request)
{
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
    $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
    $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
    $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

    $revisit_list = DB::connection('hosxp')->select('
            SELECT o.vstdate,CONCAT(v.lastvisit_hour," ช.ม.") AS p_vstdate,o.main_dep_queue AS q,o.hn,c.cc,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname
            ,v.age_y,v.pttype,v.pdx,IF(e.vn<>"","ER","OPD") AS depart,IF(o.an <>"","Admit",null) AS admit,IF(r.vn <>"","Refer",null) AS refer
            FROM ovst o
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN er_regist e ON e.vn=o.vn
            LEFT JOIN opdscreen c ON c.vn=o.vn
            LEFT JOIN ovstdiag o1 ON o1.vn=o.vn
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN patient p ON p.hn=o.hn
            WHERE v.lastvisit_hour <= 48
            AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND v.pdx NOT LIKE "Z%" AND v.old_diagnosis = "Y"
            AND (e.vn <>"" OR o.main_dep = "002")
            AND o1.icd10 NOT IN ("U071","U072","Z290","Z208")
            AND c.cc NOT LIKE "%นัด%" AND c.cc NOT LIKE "%ต่อเนื่อง%" AND c.cc NOT LIKE "%ออกซิเจน%" AND c.cc NOT LIKE "%ออกชิเจน%"
            AND c.cc NOT LIKE "%ยาเดิม%"  AND c.cc NOT LIKE "%ใบความเห็นแพทย์%"  AND c.cc NOT LIKE "%covid%" AND c.cc NOT LIKE "%ยาแทน%"
            AND c.cc NOT LIKE "%ใบส่งตัว%" AND c.cc NOT LIKE "%ใบรับรองแพทย์%"
            GROUP BY o.vn,v.pdx
            ORDER BY v.pdx,o.hn,o.vstdate');

    $revisit_diagtop = DB::connection('hosxp')->select('
            SELECT CONCAT("[",a.pdx,"] ",i.`name`) AS pdx,
            sum(case when a.sex=1 THEN 1 ELSE 0 END) as male,
            sum(case when a.sex=2 THEN 1 ELSE 0 END) as female,
            COUNT(a.pdx) AS total
            FROM (SELECT o.vstdate,CONCAT(v.lastvisit_hour," ช.ม.") AS p_vstdate,o.main_dep_queue AS q,o.hn,c.cc,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,p.sex,v.age_y,v.pttype,v.pdx,IF(e.vn<>"","ER","OPD") AS depart,IF(o.an <>"","Admit",null) AS admit,IF(r.vn <>"","Refer",null) AS refer
            FROM ovst o
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN er_regist e ON e.vn=o.vn
            LEFT JOIN opdscreen c ON c.vn=o.vn
            LEFT JOIN ovstdiag o1 ON o1.vn=o.vn
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN patient p ON p.hn=o.hn
            WHERE v.lastvisit_hour <= 48
            AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND v.pdx NOT LIKE "Z%" AND v.old_diagnosis = "Y"
            AND (e.vn <>"" OR o.main_dep = "002")
            AND o1.icd10 NOT IN ("U071","U072","Z290","Z208")
            AND c.cc NOT LIKE "%นัด%" AND c.cc NOT LIKE "%ต่อเนื่อง%" AND c.cc NOT LIKE "%ออกซิเจน%" AND c.cc NOT LIKE "%ออกชิเจน%"
            AND c.cc NOT LIKE "%ยาเดิม%"  AND c.cc NOT LIKE "%ใบความเห็นแพทย์%"  AND c.cc NOT LIKE "%covid%" AND c.cc NOT LIKE "%ยาแทน%"
            AND c.cc NOT LIKE "%ใบส่งตัว%" AND c.cc NOT LIKE "%ใบรับรองแพทย์%"
            GROUP BY o.vn,v.pdx
            ORDER BY v.pdx,o.hn,o.vstdate ) AS a
            LEFT JOIN icd101 i ON i.`code`=a.pdx
            GROUP BY a.pdx ORDER BY total DESC limit 10');

    $revisit_month = DB::connection('hosxp')->select('
            SELECT
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
            SUM(CASE WHEN depart="ER" THEN 1 ELSE 0 END) AS "er",
            SUM(CASE WHEN depart="OPD" THEN 1 ELSE 0 END) AS "opd"
            FROM
            (SELECT o.vstdate,CONCAT(v.lastvisit_hour," ช.ม.") AS p_vstdate,o.main_dep_queue AS q,o.hn
            ,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.pdx,IF(e.vn<>"","ER","OPD") AS depart
            FROM ovst o
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN er_regist e ON e.vn=o.vn
            LEFT JOIN opdscreen c ON c.vn=o.vn
            LEFT JOIN ovstdiag o1 ON o1.vn=o.vn
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN patient p ON p.hn=o.hn
            WHERE v.lastvisit_hour <= 48
            AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND v.pdx NOT LIKE "Z%" AND v.old_diagnosis = "Y"
            AND (e.vn <>"" OR o.main_dep = "002")
            AND o1.icd10 NOT IN ("U071","U072","Z290","Z208")
            AND c.cc NOT LIKE "%นัด%" AND c.cc NOT LIKE "%ต่อเนื่อง%" AND c.cc NOT LIKE "%ออกซิเจน%" AND c.cc NOT LIKE "%ออกชิเจน%"
            AND c.cc NOT LIKE "%ยาเดิม%"  AND c.cc NOT LIKE "%ใบความเห็นแพทย์%"  AND c.cc NOT LIKE "%covid%" AND c.cc NOT LIKE "%ยาแทน%"
            AND c.cc NOT LIKE "%ใบส่งตัว%" AND c.cc NOT LIKE "%ใบรับรองแพทย์%"
            GROUP BY o.vn,v.pdx
            ORDER BY v.pdx,o.hn,o.vstdate) AS a
            GROUP BY MONTH(vstdate)
            ORDER BY YEAR(vstdate) , MONTH(vstdate) ');
    $revisit_m = array_column($revisit_month,'month');
    $revisit_er_m = array_column($revisit_month,'er');
    $revisit_opd_m = array_column($revisit_month,'opd');

    $revisit_year = DB::connection('hosxp')->select('
            SELECT
            IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud,
            SUM(CASE WHEN depart="ER" THEN 1 ELSE 0 END) AS "er",
            SUM(CASE WHEN depart="OPD" THEN 1 ELSE 0 END) AS "opd"
            FROM (SELECT o.vstdate,CONCAT(v.lastvisit_hour," ช.ม.") AS p_vstdate,o.main_dep_queue AS q,o.hn
            ,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.pdx,IF(e.vn<>"","ER","OPD") AS depart
            FROM ovst o
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN er_regist e ON e.vn=o.vn
            LEFT JOIN opdscreen c ON c.vn=o.vn
            LEFT JOIN ovstdiag o1 ON o1.vn=o.vn
            LEFT JOIN referout r ON r.vn=o.vn
            LEFT JOIN patient p ON p.hn=o.hn
            WHERE v.lastvisit_hour <= 48
            AND o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND v.pdx NOT LIKE "Z%" AND v.old_diagnosis = "Y"
            AND (e.vn <>"" OR o.main_dep = "002")
            AND o1.icd10 NOT IN ("U071","U072","Z290","Z208")
            AND c.cc NOT LIKE "%นัด%" AND c.cc NOT LIKE "%ต่อเนื่อง%" AND c.cc NOT LIKE "%ออกซิเจน%" AND c.cc NOT LIKE "%ออกชิเจน%"
            AND c.cc NOT LIKE "%ยาเดิม%"  AND c.cc NOT LIKE "%ใบความเห็นแพทย์%"  AND c.cc NOT LIKE "%covid%" AND c.cc NOT LIKE "%ยาแทน%"
            AND c.cc NOT LIKE "%ใบส่งตัว%" AND c.cc NOT LIKE "%ใบรับรองแพทย์%"
            GROUP BY o.vn,v.pdx
            ORDER BY v.pdx,o.hn,o.vstdate) AS a
            GROUP BY year_bud
            ORDER BY year_bud ');
    $revisit_y = array_column($revisit_year,'year_bud');
    $revisit_er_y = array_column($revisit_year,'er');
    $revisit_opd_y = array_column($revisit_year,'opd');

    return view('service_er.revisit',compact('budget_year_select','budget_year','revisit_list','revisit_diagtop',
            'revisit_m','revisit_er_m','revisit_opd_m','revisit_y','revisit_er_y','revisit_opd_y'));
}

//Create bps มากกว่า 180
public function bps180up(Request $request)
{
$budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
$budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
$budget_year = $request->budget_year;
if($budget_year == '' || $budget_year == null)
{$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
$start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
$start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
$end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

$bps180up_list = DB::connection('hosxp')->select('
      SELECT o.oqueue,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,
      oc.cc,CONCAT(ROUND(oc.bps),"/",ROUND(oc.bpd)) AS bp,oc.bw,oc.height,oc.bmi,oc.temperature,oc.pulse,
      v.pdx,o.diag_text,IF(e.vn<>"","ER",k.department) AS depart,cms.clinic_member_status_name AS clinic,	
      IF(o.an<>"",CONCAT("Admit ",o.an),"") AS "admit",
      IF(r.vn<>"",CONCAT("Refer ",r.refer_hospcode),"") AS "refer"
      FROM ovst o 
      LEFT JOIN opdscreen oc ON oc.vn=o.vn
      LEFT JOIN vn_stat v ON v.vn=o.vn
      LEFT JOIN er_regist e ON e.vn=o.vn
      LEFT JOIN referout r ON r.vn=o.vn
      LEFT JOIN kskdepartment k ON k.depcode=o.main_dep
      LEFT JOIN patient p ON p.hn=o.hn
      LEFT JOIN clinicmember cm ON cm.hn=o.hn AND cm.clinic = "002" AND cm.regdate<o.vstdate 
      LEFT JOIN clinic_member_status cms ON cms.clinic_member_status_id=cm.clinic_member_status_id
      WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
      AND oc.bps > "179" GROUP BY o.vn');
$bps180up_month = DB::connection('hosxp')->select('
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
      END AS "month", COUNT(a.vn) AS "all",
      SUM(CASE WHEN a.admit<>"" THEN 1 ELSE 0 END) AS "admit",
      SUM(CASE WHEN a.refer<>"" THEN 1 ELSE 0 END) AS "refer"
      FROM (SELECT o.vn,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
      v.age_y,oc.cc,CONCAT(ROUND(oc.bps),"/",ROUND(oc.bpd)) AS bp,oc.bw,oc.height,oc.bmi,oc.temperature,oc.pulse,
      v.pdx,o.diag_text,IF(e.vn<>"","ER",k.department) AS depart,cms.clinic_member_status_name AS clinic,	
      IF(o.an<>"",CONCAT("Admit ",o.an),"") AS "admit",
      IF(r.vn<>"",CONCAT("Refer ",r.refer_hospcode),"") AS "refer"
      FROM ovst o 
      LEFT JOIN opdscreen oc ON oc.vn=o.vn
      LEFT JOIN vn_stat v ON v.vn=o.vn
      LEFT JOIN er_regist e ON e.vn=o.vn
      LEFT JOIN referout r ON r.vn=o.vn
      LEFT JOIN kskdepartment k ON k.depcode=o.main_dep
      LEFT JOIN patient p ON p.hn=o.hn
      LEFT JOIN clinicmember cm ON cm.hn=o.hn AND cm.clinic = "002" AND cm.regdate<o.vstdate 
      LEFT JOIN clinic_member_status cms ON cms.clinic_member_status_id=cm.clinic_member_status_id
      WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
      AND oc.bps > "179" GROUP BY o.vn) AS a
      GROUP BY MONTH(vstdate)
      ORDER BY YEAR(vstdate) , MONTH(vstdate)');
$bps180up_m = array_column($bps180up_month,'month');
$bps180up_all_m = array_column($bps180up_month,'all');
$bps180up_admit_m = array_column($bps180up_month,'admit');
$bps180up_refer_m = array_column($bps180up_month,'refer');

$bps180up_year = DB::connection('hosxp')->select('
      SELECT IF(MONTH(vstdate)>9,YEAR(vstdate)+1,YEAR(vstdate)) + 543 AS year_bud, COUNT(a.vn) AS "all",
      SUM(CASE WHEN a.admit<>"" THEN 1 ELSE 0 END) AS "admit",
      SUM(CASE WHEN a.refer<>"" THEN 1 ELSE 0 END) AS "refer"
      FROM (SELECT o.vn,o.vstdate,o.vsttime,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
      v.age_y,oc.cc,CONCAT(ROUND(oc.bps),"/",ROUND(oc.bpd)) AS bp,oc.bw,oc.height,oc.bmi,oc.temperature,oc.pulse,
      v.pdx,o.diag_text,IF(e.vn<>"","ER",k.department) AS depart,cms.clinic_member_status_name AS clinic,	
      IF(o.an<>"",CONCAT("Admit ",o.an),"") AS "admit",
      IF(r.vn<>"",CONCAT("Refer ",r.refer_hospcode),"") AS "refer"
      FROM ovst o 
      LEFT JOIN opdscreen oc ON oc.vn=o.vn
      LEFT JOIN vn_stat v ON v.vn=o.vn
      LEFT JOIN er_regist e ON e.vn=o.vn
      LEFT JOIN referout r ON r.vn=o.vn
      LEFT JOIN kskdepartment k ON k.depcode=o.main_dep
      LEFT JOIN patient p ON p.hn=o.hn
      LEFT JOIN clinicmember cm ON cm.hn=o.hn AND cm.clinic = "002" AND cm.regdate<o.vstdate 
      LEFT JOIN clinic_member_status cms ON cms.clinic_member_status_id=cm.clinic_member_status_id
      WHERE o.vstdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
      AND oc.bps > "179" GROUP BY o.vn) AS a
      GROUP BY year_bud
      ORDER BY year_bud');
$bps180up_y = array_column($bps180up_year,'year_bud');
$bps180up_all_y = array_column($bps180up_year,'all');
$bps180up_admit_y = array_column($bps180up_year,'admit');
$bps180up_refer_y = array_column($bps180up_year,'refer');

return view('service_er.bps180up', compact('budget_year_select','budget_year','bps180up_list','bps180up_m',
      'bps180up_all_m','bps180up_admit_m','bps180up_refer_m','bps180up_y','bps180up_all_y','bps180up_admit_y',
      'bps180up_refer_y'));
}
#############################################################################################################
//Create ตรวจโดยพยาบาลนอกเวลา 19.00-08.00
public function nurse_diag(Request $request)
{
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      if($start_date == '' || $end_date == null)
      {$start_date = date('Y-m-d', strtotime("last day"));}else{$start_date =$request->start_date;}
      if($end_date == '' || $end_date == null)
      {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

      $nurse_diag = DB::connection('hosxp')->select('
            SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,et.`name` AS emergency_type,
            time(e.enter_er_time) AS time,oc.cc,d.`name` AS nurse_diag,v.pdx,e.er_list,d1.`name` AS er_doctor
            FROM ovst o
            LEFT JOIN er_regist e ON e.vn=o.vn
            LEFT JOIN er_emergency_type et ON et.er_emergency_type=e.er_emergency_type
            LEFT JOIN opdscreen oc ON oc.vn=o.vn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN doctor d ON d.`code`=v.dx_doctor
            LEFT JOIN doctor d1 ON d1.`code`=e.er_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND o.vsttime BETWEEN "00:00:00" AND "07:59:59" AND  v.pdx NOT LIKE "Z%" 
            AND d.position_id = 5 AND e.vn IS NOT NULL '); 

      return view('service_er.nurse_diag',compact('nurse_diag','start_date','end_date'));
}
#############################################################################################################
//Create รายชื่อผู้ป่วยรอ Admit ที่ ER เกิน 2 ชั่วโมง
public function waitingtime_admit(Request $request)
{
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      if($start_date == '' || $end_date == null)
      {$start_date = date('Y-m-d', strtotime("last day"));}else{$start_date =$request->start_date;}
      if($end_date == '' || $end_date == null)
      {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

      $waitingtime_admit = DB::connection('hosxp')->select('
            SELECT * FROM (SELECT o.vstdate,o.oqueue,o.hn,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,o.an,
            TIME(e.enter_er_time) AS er_time,et.`name` AS emergency_type,i.regtime AS admit_time,d.`name` AS er_doctor,
            LEFT(SEC_TO_TIME(AVG((time_to_sec(TIME(i.regtime))-time_to_sec(TIME(e.enter_er_time))) )),8) AS time_wait_admit
            FROM ovst o
            INNER JOIN er_regist e ON e.vn=o.vn
            LEFT JOIN er_emergency_type et ON et.er_emergency_type=e.er_emergency_type
            LEFT JOIN ipt i ON i.an=o.an
            LEFT JOIN patient pt ON pt.hn=o.hn
            LEFT JOIN doctor d ON d.`code`=e.er_doctor
            WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (o.an IS NOT NULL AND o.vn <>"") GROUP BY o.vn ) AS a
            WHERE time_wait_admit >= "02:00:00"
            ORDER BY time_wait_admit DESC'); 

      return view('service_er.waitingtime_admit',compact('waitingtime_admit','start_date','end_date'));
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
    
      $diag_504 = DB::connection('hosxp')->select('
            SELECT CONCAT(a.name1," [",a.id,"]") AS name,
            IFNULL(d.male,0) AS male,IFNULL(d.female,0) AS female,IFNULL(d.amount,0) AS sum  
            FROM rpt_504_name a 
            LEFT JOIN (SELECT b.id,
            SUM(CASE WHEN v.sex=1 THEN 1 ELSE 0 END) AS male,   
            SUM(CASE WHEN v.sex=2 THEN 1 ELSE 0 END) AS female ,
            COUNT(b.id) AS amount 
            FROM rpt_504_code b,vn_stat v ,er_regist e
            WHERE v.pdx BETWEEN b.code1 AND b.code2 AND e.vn=v.vn
		AND v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY b.id) d on d.id=a.id 
            ORDER BY sum DESC ');
     
      return view('service_er.diag_504',compact('budget_year_select','budget_year','diag_504'));          
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
    
      $diag_506 = DB::connection('hosxp')->select('
            SELECT n.name as name , 
            SUM(CASE WHEN p.sex=1 THEN 1 ELSE 0 END) as male,   
            SUM(CASE WHEN p.sex=2 THEN 1 ELSE 0 END) as female,  
            COUNT(DISTINCT s.vn) as sum
            FROM surveil_member s   
		INNER JOIN er_regist e ON e.vn=s.vn
            LEFT JOIN patient p on p.hn=s.hn  
            LEFT JOIN name506 n on n.code=s.code506 
            WHERE s.report_date between "'.$start_date.'" AND "'.$end_date.'" 
            GROUP BY s.code506 ORDER BY sum DESC ');     

      return view('service_er.diag_506',compact('budget_year_select','budget_year','diag_506'));          
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
            SELECT CONCAT("[",v.pdx,"] " ,i.name) as name,count(v.pdx) as sum , 
            SUM(CASE WHEN v.sex=1 THEN 1 ELSE 0 END) as male,   
            SUM(CASE WHEN v.sex=2 THEN 1 ELSE 0 END) as female,
            SUM(v.inc03) as inc_lab,
		SUM(v.inc12) as inc_drug   
            FROM vn_stat v  
            INNER JOIN er_regist e ON e.vn=v.vn
            LEFT JOIN  icd101 i on i.code=v.pdx 
            WHERE v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"      
            AND v.pdx<>"" AND v.pdx IS NOT NULL AND v.pdx NOT LIKE "Z%" AND v.pdx NOT IN ("U119")
            GROUP BY v.pdx,i.name  
            ORDER BY sum DESC LIMIT 30');

      $diag_top30_z = DB::connection('hosxp')->select('      
            SELECT CONCAT("[",v.pdx,"] " ,i.name) as name,count(v.pdx) as sum , 
            SUM(CASE WHEN v.sex=1 THEN 1 ELSE 0 END) as male,   
            SUM(CASE WHEN v.sex=2 THEN 1 ELSE 0 END) as female,
            SUM(v.inc03) as inc_lab,
		SUM(v.inc12) as inc_drug   
            FROM vn_stat v  
            INNER JOIN er_regist e ON e.vn=v.vn
            LEFT JOIN  icd101 i on i.code=v.pdx 
            WHERE v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"      
            AND v.pdx<>"" AND v.pdx IS NOT NULL AND (v.pdx like "z%" or v.pdx IN ("u119"))
            GROUP BY v.pdx,i.name  
            ORDER BY sum DESC LIMIT 30');

      return view('service_er.diag_top30',compact('budget_year_select','budget_year','diag_top30','diag_top30_z'));          
}

}
