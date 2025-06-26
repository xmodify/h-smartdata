<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use PDF;
use Illuminate\Support\Facades\Response;

class Backoffice_RiskController extends Controller
{
//Check Login
      public function __construct()
      {
          $this->middleware('auth');
      }


//Create index
public function index(Request $request)
{
        $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
        $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
        $budget_year = $request->budget_year;
        if($budget_year == '' || $budget_year == null)
        {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
        $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
        $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
        $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  
        
        $risk_clinic = DB::connection('backoffice')->select('select 
                CASE WHEN MONTH(a.RISKREP_DATESAVE)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(a.RISKREP_DATESAVE)+543,2))
                WHEN MONTH(a.RISKREP_DATESAVE)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(a.RISKREP_DATESAVE)+543,2))
                WHEN MONTH(a.RISKREP_DATESAVE)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(a.RISKREP_DATESAVE)+543,2))
                WHEN MONTH(a.RISKREP_DATESAVE)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(a.RISKREP_DATESAVE)+543,2))
                WHEN MONTH(a.RISKREP_DATESAVE)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(a.RISKREP_DATESAVE)+543,2))
                WHEN MONTH(a.RISKREP_DATESAVE)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(a.RISKREP_DATESAVE)+543,2))
                WHEN MONTH(a.RISKREP_DATESAVE)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(a.RISKREP_DATESAVE)+543,2))
                WHEN MONTH(a.RISKREP_DATESAVE)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(a.RISKREP_DATESAVE)+543,2))
                WHEN MONTH(a.RISKREP_DATESAVE)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(a.RISKREP_DATESAVE)+543,2))
                WHEN MONTH(a.RISKREP_DATESAVE)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(a.RISKREP_DATESAVE)+543,2))
                WHEN MONTH(a.RISKREP_DATESAVE)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(a.RISKREP_DATESAVE)+543,2))
                WHEN MONTH(a.RISKREP_DATESAVE)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(a.RISKREP_DATESAVE)+543,2))
                END AS "month",
                SUM(CASE WHEN a.RISK_REPPROGRAMSUB_DETAIL = "Clinical" THEN 1 ELSE 0 END) AS "clinical",
                SUM(CASE WHEN a.RISK_REPPROGRAMSUB_DETAIL = "General" THEN 1 ELSE 0 END) AS "general",            
                COUNT(DISTINCT a.RISKREP_ID) AS total,
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "A" THEN 1 ELSE 0 END) AS "a",
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "B" THEN 1 ELSE 0 END) AS "b", 	
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "C" THEN 1 ELSE 0 END) AS "c", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "D" THEN 1 ELSE 0 END) AS "d", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "E" THEN 1 ELSE 0 END) AS "e", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "F" THEN 1 ELSE 0 END) AS "f", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "G" THEN 1 ELSE 0 END) AS "g", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "H" THEN 1 ELSE 0 END) AS "h", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "I" THEN 1 ELSE 0 END) AS "i",
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "1" THEN 1 ELSE 0 END) AS "g1", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "2" THEN 1 ELSE 0 END) AS "g2", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "3" THEN 1 ELSE 0 END) AS "g3", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "4" THEN 1 ELSE 0 END) AS "g4", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "5" THEN 1 ELSE 0 END) AS "g5",
                SUM(CASE WHEN (a.RISK_REP_LEVEL_NAME = "" OR a.RISK_REP_LEVEL_NAME IS NULL) THEN 1 ELSE 0 END) AS "null",
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME IN ("A","B","1") THEN 1 ELSE 0 END) AS "near_miss",
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME IN ("C","D","2") THEN 1 ELSE 0 END) AS "low_risk",
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME IN ("E","F","3") THEN 1 ELSE 0 END) AS "moderate_risk",
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME IN ("G","H","4","5") THEN 1 ELSE 0 END) AS "high_risk"
                FROM (SELECT r.RISKREP_ID,r.RISKREP_DETAILRISK,r.RISKREP_LEVEL,
                r.RISKREP_DATESAVE,l.RISK_REP_LEVEL_NAME,
                r.RISK_REPPROGRAM_ID,r.RISK_REPPROGRAMSUB_ID,
                ps.RISK_REPPROGRAMSUB_NAME,ps.RISK_REPPROGRAMSUB_DETAIL
                FROM risk_rep r
                LEFT JOIN risk_rep_program_sub ps ON ps.RISK_REPPROGRAMSUB_ID=r.RISK_REPPROGRAMSUB_ID                
                LEFT JOIN risk_rep_level l ON l.RISK_REP_LEVEL_ID=r.RISKREP_LEVEL 
                WHERE r.RISKREP_DATESAVE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND r.RISKREP_STATUS <> "CANCEL"
                GROUP BY r.RISKREP_ID) AS a
                GROUP BY MONTH(a.RISKREP_DATESAVE)
                ORDER BY YEAR(a.RISKREP_DATESAVE) , MONTH(a.RISKREP_DATESAVE)'); 
        $risk_clinic_m = array_column($risk_clinic,'month');              
        $risk_clinical = array_column($risk_clinic,'clinical');
        $risk_general = array_column($risk_clinic,'general');
        $risk_lavel_near_miss = array_column($risk_clinic,'near_miss'); 
        $risk_lavel_low_risk = array_column($risk_clinic,'low_risk');
        $risk_lavel_moderate_risk = array_column($risk_clinic,'moderate_risk'); 
        $risk_lavel_high_risk = array_column($risk_clinic,'high_risk');   

        $risk_clinic_year = DB::connection('backoffice')->select('select 
                SUM(CASE WHEN a.RISK_REPPROGRAMSUB_DETAIL = "Clinical" THEN 1 ELSE 0 END) AS "clinical",
                SUM(CASE WHEN a.RISK_REPPROGRAMSUB_DETAIL = "General" THEN 1 ELSE 0 END) AS "general",
                SUM(CASE WHEN a.RISK_REPPROGRAMSUB_DETAIL = "" OR a.RISK_REPPROGRAMSUB_DETAIL IS NULL THEN 1 ELSE 0 END) AS "null"
                FROM (SELECT r.RISKREP_ID,r.RISKREP_DETAILRISK,r.RISKREP_LEVEL,
                r.RISKREP_DATESAVE,r.RISK_REPPROGRAM_ID,r.RISK_REPPROGRAMSUB_ID,
                ps.RISK_REPPROGRAMSUB_NAME,ps.RISK_REPPROGRAMSUB_DETAIL
                FROM risk_rep r                
                LEFT JOIN risk_rep_program_sub ps ON ps.RISK_REPPROGRAMSUB_ID=r.RISK_REPPROGRAMSUB_ID 
                WHERE r.RISKREP_DATESAVE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND r.RISKREP_STATUS <> "CANCEL"
                GROUP BY r.RISKREP_ID) AS a');
        foreach ($risk_clinic_year as $row){
                $risk_clinical_y = $row->clinical; 
                $risk_general_y = $row->general;  
                $risk_null_y = $row->null; 
                }

        $risk_program = DB::connection('backoffice')->select('select 
                a.RISK_REPPROGRAM_ID AS id,a.RISK_REPPROGRAM_NAME,                 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "A" THEN 1 ELSE 0 END) AS "a",
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "B" THEN 1 ELSE 0 END) AS "b", 	
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "C" THEN 1 ELSE 0 END) AS "c", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "D" THEN 1 ELSE 0 END) AS "d", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "E" THEN 1 ELSE 0 END) AS "e", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "F" THEN 1 ELSE 0 END) AS "f", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "G" THEN 1 ELSE 0 END) AS "g", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "H" THEN 1 ELSE 0 END) AS "h", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "I" THEN 1 ELSE 0 END) AS "i",
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "1" THEN 1 ELSE 0 END) AS "g1", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "2" THEN 1 ELSE 0 END) AS "g2", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "3" THEN 1 ELSE 0 END) AS "g3", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "4" THEN 1 ELSE 0 END) AS "g4", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "5" THEN 1 ELSE 0 END) AS "g5",
                SUM(CASE WHEN (a.RISK_REP_LEVEL_NAME = "" OR a.RISK_REP_LEVEL_NAME IS NULL) THEN 1 ELSE 0 END) AS "null",
                COUNT(DISTINCT a.RISKREP_ID) AS total
                FROM (SELECT r.RISKREP_ID,r.RISKREP_DETAILRISK,r.RISKREP_LEVEL,r.RISKREP_DATESAVE,l.RISK_REP_LEVEL_NAME,
                IF(p.RISK_REPPROGRAM_ID IS NULL,"0",p.RISK_REPPROGRAM_ID) AS RISK_REPPROGRAM_ID,
                IF((p.RISK_REPPROGRAM_NAME="" OR p.RISK_REPPROGRAM_NAME IS NULL),"Non-Program",
                p.RISK_REPPROGRAM_NAME) AS RISK_REPPROGRAM_NAME         
                FROM risk_rep r
                LEFT JOIN risk_rep_program p ON p.RISK_REPPROGRAM_ID=r.RISK_REPPROGRAM_ID                
                LEFT JOIN risk_rep_level l ON l.RISK_REP_LEVEL_ID=r.RISKREP_LEVEL 
                WHERE r.RISKREP_DATESAVE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND r.RISKREP_STATUS <> "CANCEL"
                GROUP BY r.RISKREP_ID  ) AS a
                GROUP BY a.RISK_REPPROGRAM_NAME
                ORDER BY a.RISK_REPPROGRAM_NAME');

        $risk_matrix = DB::connection('backoffice')->select('select 
                SUM(CASE WHEN a.consequence="1" AND a.likelihood="1" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c1_1",
                SUM(CASE WHEN a.consequence="1" AND a.likelihood="2" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c1_2",
                SUM(CASE WHEN a.consequence="1" AND a.likelihood="3" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c1_3",
                SUM(CASE WHEN a.consequence="1" AND a.likelihood="4" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c1_4",
                SUM(CASE WHEN a.consequence="1" AND a.likelihood="5" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c1_5",
                SUM(CASE WHEN a.consequence="2" AND a.likelihood="1" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c2_1",
                SUM(CASE WHEN a.consequence="2" AND a.likelihood="2" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c2_2",
                SUM(CASE WHEN a.consequence="2" AND a.likelihood="3" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c2_3",
                SUM(CASE WHEN a.consequence="2" AND a.likelihood="4" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c2_4",
                SUM(CASE WHEN a.consequence="2" AND a.likelihood="5" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c2_5",
                SUM(CASE WHEN a.consequence="3" AND a.likelihood="1" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c3_1",
                SUM(CASE WHEN a.consequence="3" AND a.likelihood="2" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c3_2",
                SUM(CASE WHEN a.consequence="3" AND a.likelihood="3" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c3_3",
                SUM(CASE WHEN a.consequence="3" AND a.likelihood="4" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c3_4",
                SUM(CASE WHEN a.consequence="3" AND a.likelihood="5" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c3_5",
                SUM(CASE WHEN a.consequence="4" AND a.likelihood="1" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c4_1",
                SUM(CASE WHEN a.consequence="4" AND a.likelihood="2" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c4_2",
                SUM(CASE WHEN a.consequence="4" AND a.likelihood="3" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c4_3",
                SUM(CASE WHEN a.consequence="4" AND a.likelihood="4" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c4_4",
                SUM(CASE WHEN a.consequence="4" AND a.likelihood="5" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c4_5",
                SUM(CASE WHEN a.consequence="5" AND a.likelihood="1" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c5_1",
                SUM(CASE WHEN a.consequence="5" AND a.likelihood="2" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c5_2",
                SUM(CASE WHEN a.consequence="5" AND a.likelihood="3" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c5_3",
                SUM(CASE WHEN a.consequence="5" AND a.likelihood="4" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c5_4",
                SUM(CASE WHEN a.consequence="5" AND a.likelihood="5" AND a.RISK_REPPROGRAMSUB_DETAIL="clinical" THEN 1 ELSE 0 END) AS "c5_5",						 SUM(CASE WHEN a.consequence="1" AND a.likelihood="1" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g1_1",
                SUM(CASE WHEN a.consequence="1" AND a.likelihood="2" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g1_2",
                SUM(CASE WHEN a.consequence="1" AND a.likelihood="3" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g1_3",
                SUM(CASE WHEN a.consequence="1" AND a.likelihood="4" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g1_4",
                SUM(CASE WHEN a.consequence="1" AND a.likelihood="5" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g1_5",
                SUM(CASE WHEN a.consequence="2" AND a.likelihood="1" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g2_1",
                SUM(CASE WHEN a.consequence="2" AND a.likelihood="2" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g2_2",
                SUM(CASE WHEN a.consequence="2" AND a.likelihood="3" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g2_3",
                SUM(CASE WHEN a.consequence="2" AND a.likelihood="4" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g2_4",
                SUM(CASE WHEN a.consequence="2" AND a.likelihood="5" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g2_5",
                SUM(CASE WHEN a.consequence="3" AND a.likelihood="1" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g3_1",
                SUM(CASE WHEN a.consequence="3" AND a.likelihood="2" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g3_2",
                SUM(CASE WHEN a.consequence="3" AND a.likelihood="3" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g3_3",
                SUM(CASE WHEN a.consequence="3" AND a.likelihood="4" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g3_4",
                SUM(CASE WHEN a.consequence="3" AND a.likelihood="5" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g3_5",
                SUM(CASE WHEN a.consequence="4" AND a.likelihood="1" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g4_1",
                SUM(CASE WHEN a.consequence="4" AND a.likelihood="2" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g4_2",
                SUM(CASE WHEN a.consequence="4" AND a.likelihood="3" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g4_3",
                SUM(CASE WHEN a.consequence="4" AND a.likelihood="4" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g4_4",
                SUM(CASE WHEN a.consequence="4" AND a.likelihood="5" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g4_5",
                SUM(CASE WHEN a.consequence="5" AND a.likelihood="1" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g5_1",
                SUM(CASE WHEN a.consequence="5" AND a.likelihood="2" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g5_2",
                SUM(CASE WHEN a.consequence="5" AND a.likelihood="3" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g5_3",
                SUM(CASE WHEN a.consequence="5" AND a.likelihood="4" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g5_4",
                SUM(CASE WHEN a.consequence="5" AND a.likelihood="5" AND a.RISK_REPPROGRAMSUB_DETAIL="general" THEN 1 ELSE 0 END) AS "g5_5"
                FROM (SELECT r.RISKREP_ID,r.RISKREP_DATESAVE,DATE(NOW()) AS date_now,DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) AS date_count,
                CASE WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE)  BETWEEN 1 AND 30 THEN "5"
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) BETWEEN 31 AND 183 THEN "4"
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) BETWEEN 184 AND 730 THEN "3"
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) BETWEEN 731 AND 1825 THEN "2" 
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) > 1825 THEN "1" END AS "likelihood",
                l.RISK_REP_LEVEL_NAME,CASE WHEN l.RISK_REP_LEVEL_NAME IN ("I","5") THEN "5" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("G","H","4") THEN "4" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("E","F","3") THEN "3" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("B","C","D","2") THEN "2" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("A","1")THEN "1" END AS "consequence",            
                ps.RISK_REPPROGRAMSUB_NAME,ps.RISK_REPPROGRAMSUB_DETAIL
                FROM risk_rep r            
                LEFT JOIN risk_rep_program_sub ps ON ps.RISK_REPPROGRAMSUB_ID=r.RISK_REPPROGRAMSUB_ID            
                LEFT JOIN risk_rep_level l ON l.RISK_REP_LEVEL_ID=r.RISKREP_LEVEL
                WHERE r.RISKREP_STATUS <> "CANCEL"
                GROUP BY r.RISKREP_ID) AS a');
    foreach ($risk_matrix as $row){
             $matrix_c1_1 = $row->c1_1; 
             $matrix_c1_2 = $row->c1_2;  
             $matrix_c1_3 = $row->c1_3;
             $matrix_c1_4 = $row->c1_4; 
             $matrix_c1_5 = $row->c1_5; 
             $matrix_c2_1 = $row->c2_1; 
             $matrix_c2_2 = $row->c2_2;  
             $matrix_c2_3 = $row->c2_3;
             $matrix_c2_4 = $row->c2_4; 
             $matrix_c2_5 = $row->c2_5; 
             $matrix_c3_1 = $row->c3_1; 
             $matrix_c3_2 = $row->c3_2;  
             $matrix_c3_3 = $row->c3_3;
             $matrix_c3_4 = $row->c3_4; 
             $matrix_c3_5 = $row->c3_5; 
             $matrix_c4_1 = $row->c4_1; 
             $matrix_c4_2 = $row->c4_2;  
             $matrix_c4_3 = $row->c4_3;
             $matrix_c4_4 = $row->c4_4; 
             $matrix_c4_5 = $row->c4_5; 
             $matrix_c5_1 = $row->c5_1; 
             $matrix_c5_2 = $row->c5_2;  
             $matrix_c5_3 = $row->c5_3;
             $matrix_c5_4 = $row->c5_4; 
             $matrix_c5_5 = $row->c5_5;
             $matrix_g1_1 = $row->g1_1; 
             $matrix_g1_2 = $row->g1_2;  
             $matrix_g1_3 = $row->g1_3;
             $matrix_g1_4 = $row->g1_4; 
             $matrix_g1_5 = $row->g1_5; 
             $matrix_g2_1 = $row->g2_1; 
             $matrix_g2_2 = $row->g2_2;  
             $matrix_g2_3 = $row->g2_3;
             $matrix_g2_4 = $row->g2_4; 
             $matrix_g2_5 = $row->g2_5; 
             $matrix_g3_1 = $row->g3_1; 
             $matrix_g3_2 = $row->g3_2;  
             $matrix_g3_3 = $row->g3_3;
             $matrix_g3_4 = $row->g3_4; 
             $matrix_g3_5 = $row->g3_5; 
             $matrix_g4_1 = $row->g4_1; 
             $matrix_g4_2 = $row->g4_2;  
             $matrix_g4_3 = $row->g4_3;
             $matrix_g4_4 = $row->g4_4; 
             $matrix_g4_5 = $row->g4_5; 
             $matrix_g5_1 = $row->g5_1; 
             $matrix_g5_2 = $row->g5_2;  
             $matrix_g5_3 = $row->g5_3;
             $matrix_g5_4 = $row->g5_4; 
             $matrix_g5_5 = $row->g5_5; 
             }

             $request->session()->put('start_date',$start_date);
             $request->session()->put('end_date',$end_date);
             $request->session()->put('budget_year',$budget_year);
             $request->session()->save();
    return view('backoffice_risk.index',compact('budget_year_select','budget_year','risk_clinic_m','risk_clinical',
                'risk_general','risk_clinical_y','risk_general_y','risk_null_y','risk_clinic','risk_lavel_near_miss',
                'risk_lavel_low_risk','risk_lavel_moderate_risk','risk_lavel_high_risk','risk_program','matrix_c1_1',
                'matrix_c1_2','matrix_c1_3','matrix_c1_4','matrix_c1_5','matrix_c2_1','matrix_c2_2','matrix_c2_3',
                'matrix_c2_4','matrix_c2_5','matrix_c3_1','matrix_c3_2','matrix_c3_3','matrix_c3_4','matrix_c3_5',
                'matrix_c4_1','matrix_c4_2','matrix_c4_3','matrix_c4_4','matrix_c4_5','matrix_c5_1','matrix_c5_2',
                'matrix_c5_3','matrix_c5_4','matrix_c5_5','matrix_g1_1','matrix_g1_2','matrix_g1_3','matrix_g1_4',
                'matrix_g1_5','matrix_g2_1','matrix_g2_2','matrix_g2_3','matrix_g2_4','matrix_g2_5','matrix_g3_1',
                'matrix_g3_2','matrix_g3_3','matrix_g3_4','matrix_g3_5','matrix_g4_1','matrix_g4_2','matrix_g4_3',
                'matrix_g4_4','matrix_g4_5','matrix_g5_1','matrix_g5_2','matrix_g5_3','matrix_g5_4','matrix_g5_5'));          
}

//Create med_error
public function med_error(Request $request)
{
        $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
        $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
        $budget_year = $request->budget_year;
        if($budget_year == '' || $budget_year == null)
        {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
        $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');      
        $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
        $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');  

        $med_error = DB::connection('hosxp')->select('select 
                CASE WHEN MONTH(a.vstdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(a.vstdate)+543,2))
                WHEN MONTH(a.vstdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(a.vstdate)+543,2))
                WHEN MONTH(a.vstdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(a.vstdate)+543,2))
                WHEN MONTH(a.vstdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(a.vstdate)+543,2))
                WHEN MONTH(a.vstdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(a.vstdate)+543,2))
                WHEN MONTH(a.vstdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(a.vstdate)+543,2))
                WHEN MONTH(a.vstdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(a.vstdate)+543,2))
                WHEN MONTH(a.vstdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(a.vstdate)+543,2))
                WHEN MONTH(a.vstdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(a.vstdate)+543,2))
                WHEN MONTH(a.vstdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(a.vstdate)+543,2))
                WHEN MONTH(a.vstdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(a.vstdate)+543,2))
                WHEN MONTH(a.vstdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(a.vstdate)+543,2))
                END AS "month",
                COUNT(a.med_error_id) AS "total",
                SUM(CASE WHEN a.med_error_process_type_id ="1" AND a.dep_type ="OPD" THEN 1 ELSE 0 END) AS "po_1",
                SUM(CASE WHEN a.med_error_process_type_id ="2" AND a.dep_type ="OPD" THEN 1 ELSE 0 END) AS "po_2",
                SUM(CASE WHEN a.med_error_process_type_id ="3" AND a.dep_type ="OPD" THEN 1 ELSE 0 END) AS "po_3",
                SUM(CASE WHEN a.med_error_process_type_id ="4" AND a.dep_type ="OPD" THEN 1 ELSE 0 END) AS "po_4",
                SUM(CASE WHEN a.med_error_process_type_id ="5" AND a.dep_type ="OPD" THEN 1 ELSE 0 END) AS "po_5",
                SUM(CASE WHEN a.med_error_process_type_id ="1" AND a.dep_type ="IPD" THEN 1 ELSE 0 END) AS "pi_1",
                SUM(CASE WHEN a.med_error_process_type_id ="2" AND a.dep_type ="IPD" THEN 1 ELSE 0 END) AS "pi_2",
                SUM(CASE WHEN a.med_error_process_type_id ="3" AND a.dep_type ="IPD" THEN 1 ELSE 0 END) AS "pi_3",
                SUM(CASE WHEN a.med_error_process_type_id ="4" AND a.dep_type ="IPD" THEN 1 ELSE 0 END) AS "pi_4",
                SUM(CASE WHEN a.med_error_process_type_id ="5" AND a.dep_type ="IPD" THEN 1 ELSE 0 END) AS "pi_5",
                SUM(CASE WHEN a.med_error_risk_type_id ="1" AND a.dep_type ="OPD" THEN 1 ELSE 0 END) AS "o_a",
                SUM(CASE WHEN a.med_error_risk_type_id ="2" AND a.dep_type ="OPD" THEN 1 ELSE 0 END) AS "o_b",
                SUM(CASE WHEN a.med_error_risk_type_id ="3" AND a.dep_type ="OPD" THEN 1 ELSE 0 END) AS "o_c",
                SUM(CASE WHEN a.med_error_risk_type_id ="4" AND a.dep_type ="OPD" THEN 1 ELSE 0 END) AS "o_d",
                SUM(CASE WHEN a.med_error_risk_type_id ="5" AND a.dep_type ="OPD" THEN 1 ELSE 0 END) AS "o_e",
                SUM(CASE WHEN a.med_error_risk_type_id ="6" AND a.dep_type ="OPD" THEN 1 ELSE 0 END) AS "o_f",
                SUM(CASE WHEN a.med_error_risk_type_id ="7" AND a.dep_type ="OPD" THEN 1 ELSE 0 END) AS "o_g",
                SUM(CASE WHEN a.med_error_risk_type_id ="8" AND a.dep_type ="OPD" THEN 1 ELSE 0 END) AS "o_h",
                SUM(CASE WHEN a.med_error_risk_type_id ="9" AND a.dep_type ="OPD" THEN 1 ELSE 0 END) AS "o_i",                
                SUM(CASE WHEN a.dep_type ="OPD" THEN 1 ELSE 0 END) AS "opd",
		SUM(CASE WHEN a.med_error_risk_type_id ="1" AND a.dep_type ="IPD" THEN 1 ELSE 0 END) AS "i_a",
                SUM(CASE WHEN a.med_error_risk_type_id ="2" AND a.dep_type ="IPD" THEN 1 ELSE 0 END) AS "i_b",
                SUM(CASE WHEN a.med_error_risk_type_id ="3" AND a.dep_type ="IPD" THEN 1 ELSE 0 END) AS "i_c",
                SUM(CASE WHEN a.med_error_risk_type_id ="4" AND a.dep_type ="IPD" THEN 1 ELSE 0 END) AS "i_d",
                SUM(CASE WHEN a.med_error_risk_type_id ="5" AND a.dep_type ="IPD" THEN 1 ELSE 0 END) AS "i_e",
                SUM(CASE WHEN a.med_error_risk_type_id ="6" AND a.dep_type ="IPD" THEN 1 ELSE 0 END) AS "i_f",
                SUM(CASE WHEN a.med_error_risk_type_id ="7" AND a.dep_type ="IPD" THEN 1 ELSE 0 END) AS "i_g",
                SUM(CASE WHEN a.med_error_risk_type_id ="8" AND a.dep_type ="IPD" THEN 1 ELSE 0 END) AS "i_h",
                SUM(CASE WHEN a.med_error_risk_type_id ="9" AND a.dep_type ="IPD" THEN 1 ELSE 0 END) AS "i_i",
                SUM(CASE WHEN a.dep_type ="IPD" THEN 1 ELSE 0 END) AS "ipd"
                FROM
                (SELECT m.med_error_id,m.dep_type,m.med_error_process_type_id,m.med_error_risk_type_id,
                DATE(m.update_datetime) AS vstdate,m1.med_error_process_type_name,m2.med_error_risk_type_name
                FROM med_error m	
                LEFT OUTER JOIN med_error_process_type m1 ON m1.med_error_process_type_id = m.med_error_process_type_id
                LEFT OUTER JOIN med_error_risk_type m2 ON m2.med_error_risk_type_id = m.med_error_risk_type_id 
                WHERE DATE(m.update_datetime) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                ORDER BY m.update_datetime) AS a
                GROUP BY MONTH(a.vstdate)
                ORDER BY YEAR(a.vstdate),MONTH(a.vstdate)'); 
        $med_error_m = array_column($med_error,'month');              
        $med_error_opd = array_column($med_error,'opd');
        $med_error_ipd = array_column($med_error,'ipd'); 
        
        $med_error_top=DB::connection('hosxp')->select('select 
                CONCAT(d.`name`,SPACE(1),d.strength) AS drug,COUNT(DISTINCT m.med_error_id) AS total              
                FROM med_error m	
                LEFT JOIN drugitems d ON d.icode=m.icode
                WHERE DATE(m.update_datetime) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND d.icode <>"" AND d.icode IS NOT NULL AND m.dep_type ="OPD"
                GROUP BY m.icode ORDER BY COUNT(DISTINCT m.med_error_id) DESC limit 20');
        $med_error_drug = array_column($med_error_top,'drug');              
        $med_error_total = array_column($med_error_top,'total');

        $med_error_top_ipd=DB::connection('hosxp')->select('select 
                CONCAT(d.`name`,SPACE(1),d.strength) AS drug,COUNT(DISTINCT m.med_error_id) AS total              
                FROM med_error m	
                LEFT JOIN drugitems d ON d.icode=m.icode
                WHERE DATE(m.update_datetime) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND d.icode <>"" AND d.icode IS NOT NULL AND m.dep_type ="IPD"
                GROUP BY m.icode ORDER BY COUNT(DISTINCT m.med_error_id) DESC limit 20');
        $med_error_drug_ipd = array_column($med_error_top_ipd,'drug');              
        $med_error_total_ipd = array_column($med_error_top_ipd,'total');

  return view('backoffice_risk.med_error',compact('budget_year','med_error_m','med_error','med_error_opd','med_error_ipd',
              'budget_year_select','med_error_drug','med_error_total','med_error_drug_ipd','med_error_total_ipd'));
}

//Create risk_dataset
public function risk_nrls_dataset(Request $request)
{
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if($start_date == '' || $end_date == null)
        {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
        if($end_date == '' || $end_date == null)
        {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}
      
        $rr001 = DB::connection('hosxp')->table('an_stat')->selectRaw('lpad(SUM(admdate),6,0) AS "rr001"')
                ->whereBetween('dchdate', [$start_date,$end_date])->get();
        $rr003 = DB::connection('hosxp')->table('ovst')->selectRaw('lpad(COUNT(DISTINCT vn),6,0) AS "rr003"')
                ->whereBetween('vstdate', [$start_date,$end_date])->wherein('visit_type',['I'])->get();  
        $rr004 = DB::connection('hosxp')->table('ovst')->selectRaw('lpad(COUNT(DISTINCT vn),6,0) AS "rr004"')
                ->whereBetween('vstdate', [$start_date,$end_date])->wherein('visit_type',['S','O'])->get(); 
        $rr005 = DB::connection('hosxp')->table('ovst')->selectRaw('lpad(COUNT(DISTINCT hn),6,0) AS "rr005"')
                ->whereBetween('vstdate', [$start_date,$end_date])->wherein('visit_type',['I'])->get(); 
        $rr006 = DB::connection('hosxp')->table('ovst')->selectRaw('lpad(COUNT(DISTINCT hn),6,0) AS "rr006"')
                ->whereBetween('vstdate', [$start_date,$end_date])->wherein('visit_type',['S','O'])->get();                                
        $rr007 = DB::connection('hosxp')->table('er_regist')->selectRaw('lpad(COUNT(DISTINCT vn),6,0) AS "rr007"')
                ->whereBetween('vstdate', [$start_date,$end_date])->where('er_emergency_type','1')->get(); 
        $rr008 = DB::connection('hosxp')->table('er_regist')->selectRaw('lpad(COUNT(DISTINCT vn),6,0) AS "rr008"')
                ->whereBetween('vstdate', [$start_date,$end_date])->where('er_emergency_type','3')->get(); 
        $rr009 = DB::connection('hosxp')->table('er_regist')->selectRaw('lpad(COUNT(DISTINCT vn),6,0) AS "rr009"')
                ->whereBetween('vstdate', [$start_date,$end_date])->where('er_emergency_type','4')->get(); 
        $rr010 = DB::connection('hosxp')->table('er_regist')->selectRaw('lpad(COUNT(DISTINCT vn),6,0) AS "rr010"')
                ->whereBetween('vstdate', [$start_date,$end_date])->where('er_emergency_type','5')->get(); 
        $rr011 = DB::connection('hosxp')->table('referout')->selectRaw('lpad(COUNT(DISTINCT vn),6,0) AS "rr011"')
                ->whereBetween('refer_date', [$start_date,$end_date])->get(); 
        $rr015 = DB::connection('hosxp')->table('ipt')->selectRaw('lpad(COUNT(DISTINCT an),6,0) AS "rr015"')
                ->whereBetween('regdate', [$start_date,$end_date])->where('ipt_type','3')->get();  
        $rr016 = DB::connection('hosxp')->table('ipt')->selectRaw('lpad(COUNT(DISTINCT an),6,0) AS "rr016"')
                ->whereBetween('regdate', [$start_date,$end_date])->where('ipt_type','4')->get();     
        $rr022 = DB::connection('hosxp')->table('opitemrece')->selectRaw('lpad(COUNT(DISTINCT vn),6,0) AS "rr022"')
                ->whereBetween('vstdate', [$start_date,$end_date])->where('icode','like','1%')->whereNotNull('vn')->get(); 
        $rr024 = DB::connection('hosxp')->table('er_regist')->selectRaw('lpad(COUNT(DISTINCT vn),6,0) AS "rr024"')
                ->whereBetween('vstdate', [$start_date,$end_date])->where('er_emergency_type','2')->get(); 
        
        $request->session()->put('start_date',$start_date);
        $request->session()->put('end_date',$end_date);
        $request->session()->put('rr001',$rr001);
        $request->session()->put('rr003',$rr003);
        $request->session()->put('rr004',$rr004);
        $request->session()->put('rr005',$rr005);
        $request->session()->put('rr006',$rr006);
        $request->session()->put('rr007',$rr007);
        $request->session()->put('rr008',$rr008);
        $request->session()->put('rr009',$rr009);
        $request->session()->put('rr010',$rr010);
        $request->session()->put('rr011',$rr011);
        $request->session()->put('rr015',$rr015);
        $request->session()->put('rr016',$rr016);
        $request->session()->put('rr022',$rr022);
        $request->session()->put('rr024',$rr024);
        $request->session()->save();
        
        return view('backoffice_risk.risk_nrls_dataset',compact('start_date','end_date','rr001','rr003','rr004','rr005','rr006',
                'rr007','rr008','rr009','rr010','rr011','rr015','rr016','rr022','rr024'));
}       
//nrls_dataset_export
public function risk_nrls_dataset_export(Request $request)
  {
        $start_date = Session::get('start_date');   
        $date =substr($start_date, 0, 4).substr($start_date, 5, 2);  
        $rr001 = Session::get('rr001');
        $rr003 = Session::get('rr003');
        $rr004 = Session::get('rr004');
        $rr005 = Session::get('rr005');
        $rr006 = Session::get('rr006');
        $rr007 = Session::get('rr007');
        $rr008 = Session::get('rr008');
        $rr009 = Session::get('rr009');
        $rr010 = Session::get('rr010');
        $rr011 = Session::get('rr011');
        $rr015 = Session::get('rr015');
        $rr016 = Session::get('rr016');
        $rr022 = Session::get('rr022');
        $rr024 = Session::get('rr024');

        return view('backoffice_risk.risk_nrls_dataset_export',compact('date','rr001','rr003','rr004','rr005','rr006',
                'rr007','rr008','rr009','rr010','rr011','rr015','rr016','rr022','rr024'));
  }
//nrls
public function risk_nrls(Request $request)
{
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if($start_date == '' || $end_date == null)
        {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
        if($end_date == '' || $end_date == null)
        {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

        $nrls = DB::connection('backoffice')->select('
                SELECT "10989" AS hospital,LPAD(r.RISKREP_ID,10,0) AS risk_id,ri.RISK_REPITEMS_CODE AS datadic1,
                ri.RISK_REPITEMS_NAME AS datadic1_name,ru.INCEDENCE_USEREFFECT_CODE AS effect_code,ru.INCEDENCE_USEREFFECT_NAME AS effect_name,
                IF((r.RISKREP_SEX = "" OR r.RISKREP_SEX IS NULL) ,"O",r.RISKREP_SEX) AS pt_sex,
                IF((r.RISKREP_AGE = "" OR r.RISKREP_AGE IS NULL),"000",LPAD(r.RISKREP_AGE,3,0)) AS person_age,
                rl.RISK_LOCATION_CODE AS datadic4,rl.RISK_LOCATION_NAME AS datadic4_name,
                DATE_FORMAT(r.RISKREP_STARTDATE, "%Y%m%d") AS risk_date,LPAD(r.RISKREP_FATE,5,0) AS datadic5,
                rv.RISK_REP_LEVEL_CODE AS datadic6,rv.RISK_REP_LEVEL_NAME AS datadic6_name,r.RISKREP_DETAILRISK AS risk_detail,
                CASE WHEN ri.RISK_REPITEMS_CODE LIKE "C%" AND rv.RISK_REP_LEVEL_ID <="9" THEN "OK"
                WHEN ri.RISK_REPITEMS_CODE LIKE "C%" AND rv.RISK_REP_LEVEL_ID >"9" THEN "ความรุนแรงไม่ตรงกับรหัสอุบัติการณ์"
                WHEN ri.RISK_REPITEMS_CODE LIKE "G%" AND rv.RISK_REP_LEVEL_ID >"9" THEN "OK"
                WHEN ri.RISK_REPITEMS_CODE LIKE "G%" AND rv.RISK_REP_LEVEL_ID <="9" THEN "ความรุนแรงไม่ตรงกับรหัสอุบัติการณ์" END AS "status_lavel"
                FROM risk_rep r
                LEFT JOIN risk_rep_items ri ON ri.RISK_REPITEMS_ID=r.RISK_REPITEMS_ID
                LEFT JOIN risk_setupincidence_usereffect ru ON ru.INCEDENCE_USEREFFECT_ID=r.RISK_REP_EFFECT
                LEFT JOIN risk_rep_location rl ON rl.RISK_LOCATION_ID=r.RISKREP_LOCAL
                LEFT JOIN risk_rep_level rv ON rv.RISK_REP_LEVEL_ID=r.RISKREP_LEVEL
                WHERE r.RISKREP_STARTDATE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND (ri.RISK_REPITEMS_ID IS NOT NULL OR ri.RISK_REPITEMS_ID <>"") GROUP BY r.RISKREP_ID');

        $request->session()->put('start_date',$start_date);
        $request->session()->put('end_date',$end_date);
        $request->session()->put('nrls',$nrls);
        $request->session()->save();
        
        return view('backoffice_risk.risk_nrls',compact('start_date','end_date','nrls'));
}
//nrls_export
public function risk_nrls_export(Request $request)
  {
        $start_date = Session::get('start_date');  
        $end_date = Session::get('end_date');    
        $date =substr($start_date, 0, 4).substr($start_date, 5, 2);  
        $nrls =  DB::connection('backoffice')->select('
                SELECT CONCAT("10989","|",LPAD(r.RISKREP_ID,10,0),"|",ri.RISK_REPITEMS_CODE,"|",
                ru.INCEDENCE_USEREFFECT_CODE,"|",IF((r.RISKREP_SEX = "" OR r.RISKREP_SEX IS NULL) ,"O",r.RISKREP_SEX),"|",
                IF((r.RISKREP_AGE = "" OR r.RISKREP_AGE IS NULL),"000",LPAD(r.RISKREP_AGE,3,0)),"|",rl.RISK_LOCATION_CODE,"|",
                DATE_FORMAT(r.RISKREP_STARTDATE, "%Y%m%d"),"|",LPAD(r.RISKREP_FATE,5,0),"|",rv.RISK_REP_LEVEL_CODE,"|",
                REPLACE(r.RISKREP_DETAILRISK,","," "),"|") AS nrls
                FROM risk_rep r
                LEFT JOIN risk_rep_items ri ON ri.RISK_REPITEMS_ID=r.RISK_REPITEMS_ID
                LEFT JOIN risk_setupincidence_usereffect ru ON ru.INCEDENCE_USEREFFECT_ID=r.RISK_REP_EFFECT
                LEFT JOIN risk_rep_location rl ON rl.RISK_LOCATION_ID=r.RISKREP_LOCAL
                LEFT JOIN risk_rep_level rv ON rv.RISK_REP_LEVEL_ID=r.RISKREP_LEVEL
                WHERE r.RISKREP_STARTDATE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND (ri.RISK_REPITEMS_ID IS NOT NULL OR ri.RISK_REPITEMS_ID <>"") GROUP BY r.RISKREP_ID');

        return view('backoffice_risk.risk_nrls_export',compact('date','nrls'));
  }
//nrls_edit
public function risk_nrls_edit(Request $request)
{
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if($start_date == '' || $end_date == null)
        {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
        if($end_date == '' || $end_date == null)
        {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

        $nrls = DB::connection('backoffice')->select('
                SELECT "10989" AS hospital,LPAD(r.RISKREP_ID,10,0) AS risk_id,ri.RISK_REPITEMS_CODE AS datadic1,
                ri.RISK_REPITEMS_NAME AS datadic1_name,ru.INCEDENCE_USEREFFECT_CODE AS effect_code,ru.INCEDENCE_USEREFFECT_NAME AS effect_name,
                IF((r.RISKREP_SEX = "" OR r.RISKREP_SEX IS NULL) ,"O",r.RISKREP_SEX) AS pt_sex,
                IF((r.RISKREP_AGE = "" OR r.RISKREP_AGE IS NULL),"000",LPAD(r.RISKREP_AGE,3,0)) AS person_age,
                rl.RISK_LOCATION_CODE AS datadic4,rl.RISK_LOCATION_NAME AS datadic4_name,
                DATE_FORMAT(r.RISKREP_STARTDATE, "%Y%m%d") AS risk_date,DATE_FORMAT(r.RISKREP_INFER_DAYENDPROBLEM, "%Y%m%d") AS risk_date_edit,
                LPAD(r.RISKREP_FATE,5,0) AS datadic5,rv.RISK_REP_LEVEL_CODE AS datadic6,rv.RISK_REP_LEVEL_NAME AS datadic6_name,
                REPLACE(r.RISKREP_DETAILRISK,","," ") AS risk_detail,IF(r.RISKREP_INFER_EDIT IS NULL,"-",r.RISKREP_INFER_EDIT) AS risk_detail_edit,
                r.RISKREP_INFER_IMPROVE,r.RISKREP_INFER_GROUPPROBLEM AS risk_detail_group
                FROM risk_rep r
                LEFT JOIN risk_rep_items ri ON ri.RISK_REPITEMS_ID=r.RISK_REPITEMS_ID
                LEFT JOIN risk_setupincidence_usereffect ru ON ru.INCEDENCE_USEREFFECT_ID=r.RISK_REP_EFFECT
                LEFT JOIN risk_rep_location rl ON rl.RISK_LOCATION_ID=r.RISKREP_LOCAL
                LEFT JOIN risk_rep_level rv ON rv.RISK_REP_LEVEL_ID=r.RISKREP_LEVEL
                WHERE r.RISKREP_STARTDATE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND (ri.RISK_REPITEMS_ID IS NOT NULL OR ri.RISK_REPITEMS_ID <>"") GROUP BY r.RISKREP_ID');

        $request->session()->put('start_date',$start_date);
        $request->session()->put('nrls',$nrls);
        $request->session()->save();
        
        return view('backoffice_risk.risk_nrls_edit',compact('start_date','end_date','nrls'));
}
//nrls_export
public function risk_nrls_editexport(Request $request)
  {
        $start_date = Session::get('start_date');   
        $date =substr($start_date, 0, 4).substr($start_date, 5, 2);  
        $nrls = Session::get('nrls');

        return view('backoffice_risk.risk_nrls_editexport',compact('date','nrls'));
  }
//Create risk_program_detail
public function risk_program_detail(Request $request,$id)
{
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date'); 
        $budget_year = Session::get('budget_year'); 
        $risk_program_detail = DB::connection('backoffice')->select('
                SELECT * FROM(SELECT CONCAT("R",RIGHT(r.budget_year,2),"-",IF(LENGTH(r.RISKREP_ID)=1,CONCAT("000",r.RISKREP_ID),if(LENGTH(r.RISKREP_ID)=2,concat("00",r.RISKREP_ID),if(LENGTH(r.RISKREP_ID)="3",concat("0",r.RISKREP_ID),r.RISKREP_ID)))) AS id,r.RISKREP_DATESAVE,r.RISKREP_STARTDATE,DATE(NOW()) AS date_now,DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) AS date_count,     CASE WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE)  BETWEEN 1 AND 30 THEN "5"
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) BETWEEN 31 AND 183 THEN "4"
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) BETWEEN 184 AND 730 THEN "3"
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) BETWEEN 731 AND 1825 THEN "2" 
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) > 1825 THEN "1" END AS "likelihood",
                l.RISK_REP_LEVEL_NAME,CASE WHEN l.RISK_REP_LEVEL_NAME IN ("I","5") THEN "5" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("G","H","4") THEN "4" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("E","F","3") THEN "3" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("B","C","D","2") THEN "2" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("A","1")THEN "1" END AS "consequence", 
		IF(p.RISK_REPPROGRAM_ID IS NULL,"0",p.RISK_REPPROGRAM_ID) AS RISK_REPPROGRAM_ID,          
                IF(p.RISK_REPPROGRAM_NAME IS NULL,"ไม่ระบุ",p.RISK_REPPROGRAM_NAME) AS RISK_REPPROGRAM_NAME,
		IF(ps.RISK_REPPROGRAMSUB_ID IS NULL,"00",ps.RISK_REPPROGRAMSUB_ID) AS RISK_REPPROGRAMSUB_ID,
		IF(ps.RISK_REPPROGRAMSUB_NAME IS NULL,"ไม่ระบุ",ps.RISK_REPPROGRAMSUB_NAME) AS RISK_REPPROGRAMSUB_NAME,
		IF(pss.RISK_REPPROGRAMSUBSUB_ID IS NULL,"000",pss.RISK_REPPROGRAMSUBSUB_ID) AS RISK_REPPROGRAMSUBSUB_ID, 
		IF(pss.RISK_REPPROGRAMSUBSUB_NAME IS NULL,"ไม่ระบุ",pss.RISK_REPPROGRAMSUBSUB_NAME) AS RISK_REPPROGRAMSUBSUB_NAME,
		ps.RISK_REPPROGRAMSUB_DETAIL AS clinic,r.RISKREP_DETAILRISK,GROUP_CONCAT(rc.RISK_RECHECK_DATE) AS "recheck"
                FROM risk_rep r LEFT JOIN risk_rep_program p ON p.RISK_REPPROGRAM_ID=r.RISK_REPPROGRAM_ID  								
                LEFT JOIN risk_rep_program_sub ps ON ps.RISK_REPPROGRAMSUB_ID=r.RISK_REPPROGRAMSUB_ID  
		LEFT JOIN risk_rep_program_subsub pss ON pss.RISK_REPPROGRAMSUBSUB_ID=r.RISK_REPPROGRAMSUBSUB_ID
                LEFT JOIN risk_rep_level l ON l.RISK_REP_LEVEL_ID=r.RISKREP_LEVEL
                LEFT JOIN risk_recheck rc ON rc.RISK_RECHECK_RISKID=r.RISKREP_ID
                WHERE r.RISKREP_STATUS <> "CANCEL" GROUP BY r.RISKREP_ID) AS a
                WHERE RISKREP_DATESAVE BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND RISK_REPPROGRAM_ID = "'.$id.'"
                ORDER BY RISK_REP_LEVEL_NAME DESC');
        foreach ($risk_program_detail as $row){
                        $RISK_REPPROGRAM_NAME= $row->RISK_REPPROGRAM_NAME;
                }

        return view('backoffice_risk.risk_program_detail',compact('risk_program_detail','budget_year','RISK_REPPROGRAM_NAME'));
}

//Create risk_program_sub_detail
public function risk_program_sub_detail(Request $request,$id)
{
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date'); 
        $budget_year = Session::get('budget_year'); 
        $risk_program_sub_detail = DB::connection('backoffice')->select('
                SELECT * FROM(SELECT CONCAT("R",RIGHT(r.budget_year,2),"-",IF(LENGTH(r.RISKREP_ID)=1,CONCAT("000",r.RISKREP_ID),if(LENGTH(r.RISKREP_ID)=2,concat("00",r.RISKREP_ID),if(LENGTH(r.RISKREP_ID)="3",concat("0",r.RISKREP_ID),r.RISKREP_ID)))) AS id,r.RISKREP_DATESAVE,r.RISKREP_STARTDATE,DATE(NOW()) AS date_now,DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) AS date_count,     CASE WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE)  BETWEEN 1 AND 30 THEN "5"
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) BETWEEN 31 AND 183 THEN "4"
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) BETWEEN 184 AND 730 THEN "3"
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) BETWEEN 731 AND 1825 THEN "2" 
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) > 1825 THEN "1" END AS "likelihood",
                l.RISK_REP_LEVEL_NAME,CASE WHEN l.RISK_REP_LEVEL_NAME IN ("I","5") THEN "5" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("G","H","4") THEN "4" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("E","F","3") THEN "3" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("B","C","D","2") THEN "2" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("A","1")THEN "1" END AS "consequence", 
                IF(p.RISK_REPPROGRAM_ID IS NULL,"0",p.RISK_REPPROGRAM_ID) AS RISK_REPPROGRAM_ID,          
                IF(p.RISK_REPPROGRAM_NAME IS NULL,"ไม่ระบุ",p.RISK_REPPROGRAM_NAME) AS RISK_REPPROGRAM_NAME,
                IF(ps.RISK_REPPROGRAMSUB_ID IS NULL,"00",ps.RISK_REPPROGRAMSUB_ID) AS RISK_REPPROGRAMSUB_ID,
                IF(ps.RISK_REPPROGRAMSUB_NAME IS NULL,"ไม่ระบุ",ps.RISK_REPPROGRAMSUB_NAME) AS RISK_REPPROGRAMSUB_NAME,
                IF(pss.RISK_REPPROGRAMSUBSUB_ID IS NULL,"000",pss.RISK_REPPROGRAMSUBSUB_ID) AS RISK_REPPROGRAMSUBSUB_ID, 
                IF(pss.RISK_REPPROGRAMSUBSUB_NAME IS NULL,"ไม่ระบุ",pss.RISK_REPPROGRAMSUBSUB_NAME) AS RISK_REPPROGRAMSUBSUB_NAME,
                ps.RISK_REPPROGRAMSUB_DETAIL AS clinic,r.RISKREP_DETAILRISK,GROUP_CONCAT(rc.RISK_RECHECK_DATE) AS "recheck"
                FROM risk_rep r LEFT JOIN risk_rep_program p ON p.RISK_REPPROGRAM_ID=r.RISK_REPPROGRAM_ID  								
                LEFT JOIN risk_rep_program_sub ps ON ps.RISK_REPPROGRAMSUB_ID=r.RISK_REPPROGRAMSUB_ID  
                LEFT JOIN risk_rep_program_subsub pss ON pss.RISK_REPPROGRAMSUBSUB_ID=r.RISK_REPPROGRAMSUBSUB_ID
                LEFT JOIN risk_rep_level l ON l.RISK_REP_LEVEL_ID=r.RISKREP_LEVEL
                LEFT JOIN risk_recheck rc ON rc.RISK_RECHECK_RISKID=r.RISKREP_ID
                WHERE r.RISKREP_STATUS <> "CANCEL" GROUP BY r.RISKREP_ID) AS a
                WHERE RISKREP_DATESAVE BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND RISK_REPPROGRAMSUB_ID = "'.$id.'"
                ORDER BY RISK_REP_LEVEL_NAME DESC');
        foreach ($risk_program_sub_detail as $row){
                        $RISK_REPPROGRAMSUB_NAME= $row->RISK_REPPROGRAMSUB_NAME;
                }

        return view('backoffice_risk.risk_program_sub_detail',compact('risk_program_sub_detail','budget_year','RISK_REPPROGRAMSUB_NAME'));
}

//Create risk_program_subsub_detail
public function risk_program_subsub_detail(Request $request,$id,$id2)
{
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date'); 
        $budget_year = Session::get('budget_year'); 
        $risk_program_subsub_detail = DB::connection('backoffice')->select('
                SELECT * FROM(SELECT CONCAT("R",RIGHT(r.budget_year,2),"-",IF(LENGTH(r.RISKREP_ID)=1,CONCAT("000",r.RISKREP_ID),if(LENGTH(r.RISKREP_ID)=2,concat("00",r.RISKREP_ID),if(LENGTH(r.RISKREP_ID)="3",concat("0",r.RISKREP_ID),r.RISKREP_ID)))) AS id,r.RISKREP_DATESAVE,r.RISKREP_STARTDATE,DATE(NOW()) AS date_now,DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) AS date_count,     CASE WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE)  BETWEEN 1 AND 30 THEN "5"
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) BETWEEN 31 AND 183 THEN "4"
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) BETWEEN 184 AND 730 THEN "3"
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) BETWEEN 731 AND 1825 THEN "2" 
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) > 1825 THEN "1" END AS "likelihood",
                l.RISK_REP_LEVEL_NAME,CASE WHEN l.RISK_REP_LEVEL_NAME IN ("I","5") THEN "5" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("G","H","4") THEN "4" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("E","F","3") THEN "3" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("B","C","D","2") THEN "2" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("A","1")THEN "1" END AS "consequence", 
                IF(p.RISK_REPPROGRAM_ID IS NULL,"0",p.RISK_REPPROGRAM_ID) AS RISK_REPPROGRAM_ID,          
                IF(p.RISK_REPPROGRAM_NAME IS NULL,"ไม่ระบุ",p.RISK_REPPROGRAM_NAME) AS RISK_REPPROGRAM_NAME,
                IF(ps.RISK_REPPROGRAMSUB_ID IS NULL,"00",ps.RISK_REPPROGRAMSUB_ID) AS RISK_REPPROGRAMSUB_ID,
                IF(ps.RISK_REPPROGRAMSUB_NAME IS NULL,"ไม่ระบุ",ps.RISK_REPPROGRAMSUB_NAME) AS RISK_REPPROGRAMSUB_NAME,
                IF(pss.RISK_REPPROGRAMSUBSUB_ID IS NULL,"000",pss.RISK_REPPROGRAMSUBSUB_ID) AS RISK_REPPROGRAMSUBSUB_ID, 
                IF(pss.RISK_REPPROGRAMSUBSUB_NAME IS NULL,"ไม่ระบุ",pss.RISK_REPPROGRAMSUBSUB_NAME) AS RISK_REPPROGRAMSUBSUB_NAME,
                ps.RISK_REPPROGRAMSUB_DETAIL AS clinic,r.RISKREP_DETAILRISK,GROUP_CONCAT(rc.RISK_RECHECK_DATE) AS "recheck"
                FROM risk_rep r LEFT JOIN risk_rep_program p ON p.RISK_REPPROGRAM_ID=r.RISK_REPPROGRAM_ID  								
                LEFT JOIN risk_rep_program_sub ps ON ps.RISK_REPPROGRAMSUB_ID=r.RISK_REPPROGRAMSUB_ID  
                LEFT JOIN risk_rep_program_subsub pss ON pss.RISK_REPPROGRAMSUBSUB_ID=r.RISK_REPPROGRAMSUBSUB_ID
                LEFT JOIN risk_rep_level l ON l.RISK_REP_LEVEL_ID=r.RISKREP_LEVEL
                LEFT JOIN risk_recheck rc ON rc.RISK_RECHECK_RISKID=r.RISKREP_ID
                WHERE r.RISKREP_STATUS <> "CANCEL" GROUP BY r.RISKREP_ID) AS a
                WHERE RISKREP_DATESAVE BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND RISK_REPPROGRAMSUB_ID = "'.$id.'"
                AND RISK_REPPROGRAMSUBSUB_ID = "'.$id2.'"
                ORDER BY RISK_REP_LEVEL_NAME DESC'); 
        foreach ($risk_program_subsub_detail as $row){
                        $RISK_REPPROGRAMSUBSUB_NAME= $row->RISK_REPPROGRAMSUBSUB_NAME;
                } 

        return view('backoffice_risk.risk_program_subsub_detail',compact('risk_program_subsub_detail','budget_year','RISK_REPPROGRAMSUBSUB_NAME'));
}

//Create risk_program_sub
public function risk_program_sub(Request $request,$id)
{
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date'); 
        $budget_year = Session::get('budget_year'); 
        $risk_program_sub = DB::connection('backoffice')->select('select							
                a.RISK_REPPROGRAMSUB_ID AS id,a.RISK_REPPROGRAM_ID,a.RISK_REPPROGRAMSUB_NAME,a.RISK_REPPROGRAM_NAME,               
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "A" THEN 1 ELSE 0 END) AS "a",
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "B" THEN 1 ELSE 0 END) AS "b", 	
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "C" THEN 1 ELSE 0 END) AS "c", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "D" THEN 1 ELSE 0 END) AS "d", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "E" THEN 1 ELSE 0 END) AS "e", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "F" THEN 1 ELSE 0 END) AS "f", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "G" THEN 1 ELSE 0 END) AS "g", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "H" THEN 1 ELSE 0 END) AS "h", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "I" THEN 1 ELSE 0 END) AS "i",
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "1" THEN 1 ELSE 0 END) AS "g1", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "2" THEN 1 ELSE 0 END) AS "g2", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "3" THEN 1 ELSE 0 END) AS "g3", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "4" THEN 1 ELSE 0 END) AS "g4", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "5" THEN 1 ELSE 0 END) AS "g5",
                SUM(CASE WHEN (a.RISK_REP_LEVEL_NAME = "" OR a.RISK_REP_LEVEL_NAME IS NULL) THEN 1 ELSE 0 END) AS "null",
                COUNT(DISTINCT a.RISKREP_ID) AS total
                FROM (SELECT r.RISKREP_ID,r.RISKREP_DETAILRISK,r.RISKREP_LEVEL,r.RISKREP_DATESAVE,l.RISK_REP_LEVEL_NAME,
                IF(r.RISK_REPPROGRAM_ID IS NULL,"0",r.RISK_REPPROGRAM_ID) AS RISK_REPPROGRAM_ID,
                IF(r.RISK_REPPROGRAMSUB_ID IS NULL,"00",r.RISK_REPPROGRAMSUB_ID) AS RISK_REPPROGRAMSUB_ID,
                p.RISK_REPPROGRAM_NAME,IF((ps.RISK_REPPROGRAMSUB_NAME="" OR ps.RISK_REPPROGRAMSUB_NAME IS NULL),"Non-Program",
                ps.RISK_REPPROGRAMSUB_NAME) AS RISK_REPPROGRAMSUB_NAME
                FROM risk_rep r  
                LEFT JOIN risk_rep_program p ON p.RISK_REPPROGRAM_ID=r.RISK_REPPROGRAM_ID               
                LEFT JOIN risk_rep_program_sub ps ON ps.RISK_REPPROGRAMSUB_ID=r.RISK_REPPROGRAMSUB_ID
                LEFT JOIN risk_rep_level l ON l.RISK_REP_LEVEL_ID=r.RISKREP_LEVEL 
                WHERE r.RISKREP_DATESAVE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND r.RISKREP_STATUS <> "CANCEL"
                GROUP BY r.RISKREP_ID  ) AS a
                WHERE a.RISK_REPPROGRAM_ID = "'.$id.'"
                GROUP BY a.RISK_REPPROGRAMSUB_NAME
                ORDER BY a.RISK_REPPROGRAMSUB_NAME');
        foreach ($risk_program_sub as $row){
                        $RISK_REPPROGRAM_NAME= $row->RISK_REPPROGRAM_NAME;
                }

        return view('backoffice_risk.risk_program_sub',compact('risk_program_sub','budget_year','RISK_REPPROGRAM_NAME'));
}

//Create risk_program_subsub
public function risk_program_subsub(Request $request,$id)
{
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date'); 
        $budget_year = Session::get('budget_year'); 
        $risk_program_subsub = DB::connection('backoffice')->select('select							
                a.RISK_REPPROGRAMSUB_ID AS id,a.RISK_REPPROGRAMSUBSUB_ID AS id2 ,
                a.RISK_REPPROGRAMSUBSUB_NAME,a.RISK_REPPROGRAMSUB_NAME,                
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "A" THEN 1 ELSE 0 END) AS "a",
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "B" THEN 1 ELSE 0 END) AS "b", 	
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "C" THEN 1 ELSE 0 END) AS "c", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "D" THEN 1 ELSE 0 END) AS "d", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "E" THEN 1 ELSE 0 END) AS "e", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "F" THEN 1 ELSE 0 END) AS "f", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "G" THEN 1 ELSE 0 END) AS "g", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "H" THEN 1 ELSE 0 END) AS "h", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "I" THEN 1 ELSE 0 END) AS "i",
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "1" THEN 1 ELSE 0 END) AS "g1", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "2" THEN 1 ELSE 0 END) AS "g2", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "3" THEN 1 ELSE 0 END) AS "g3", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "4" THEN 1 ELSE 0 END) AS "g4", 
                SUM(CASE WHEN a.RISK_REP_LEVEL_NAME = "5" THEN 1 ELSE 0 END) AS "g5",
                SUM(CASE WHEN (a.RISK_REP_LEVEL_NAME = "" OR a.RISK_REP_LEVEL_NAME IS NULL) THEN 1 ELSE 0 END) AS "null",
                COUNT(DISTINCT a.RISKREP_ID) AS total
                FROM (SELECT r.RISKREP_ID,r.RISKREP_DETAILRISK,r.RISKREP_LEVEL,r.RISKREP_DATESAVE,l.RISK_REP_LEVEL_NAME,
                IF(r.RISK_REPPROGRAMSUB_ID IS NULL,"00",r.RISK_REPPROGRAMSUB_ID) AS RISK_REPPROGRAMSUB_ID,
                IF(r.RISK_REPPROGRAMSUBSUB_ID IS NULL,"000",r.RISK_REPPROGRAMSUBSUB_ID) AS RISK_REPPROGRAMSUBSUB_ID,
                ps.RISK_REPPROGRAMSUB_NAME,IF((pss.RISK_REPPROGRAMSUBSUB_NAME="" OR pss.RISK_REPPROGRAMSUBSUB_NAME IS NULL),"Non-Program",
                pss.RISK_REPPROGRAMSUBSUB_NAME) AS RISK_REPPROGRAMSUBSUB_NAME
                FROM risk_rep r
                LEFT JOIN risk_rep_program_sub ps ON ps.RISK_REPPROGRAMSUB_ID=r.RISK_REPPROGRAMSUB_ID
                LEFT JOIN risk_rep_program_subsub pss ON pss.RISK_REPPROGRAMSUBSUB_ID=r.RISK_REPPROGRAMSUBSUB_ID
                LEFT JOIN risk_rep_level l ON l.RISK_REP_LEVEL_ID=r.RISKREP_LEVEL 
                WHERE r.RISKREP_DATESAVE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND r.RISKREP_STATUS <> "CANCEL"
                GROUP BY r.RISKREP_ID  ) AS a
                WHERE a.RISK_REPPROGRAMSUB_ID =  "'.$id.'"
                GROUP BY a.RISK_REPPROGRAMSUBSUB_NAME
                ORDER BY a.RISK_REPPROGRAMSUBSUB_NAME');
        foreach ($risk_program_subsub as $row){
                $RISK_REPPROGRAMSUB_NAME= $row->RISK_REPPROGRAMSUB_NAME;
                }

        return view('backoffice_risk.risk_program_subsub',compact('risk_program_subsub','budget_year','RISK_REPPROGRAMSUB_NAME'));
}

//Create risk_matrix_detail
public function risk_matrix_detail(Request $request,$clinic,$consequence,$likelihood)
{
        $risk_matrix_detail = DB::connection('backoffice')->select('					
                SELECT * FROM(SELECT CONCAT("R",RIGHT(r.budget_year,2),"-",IF(LENGTH(r.RISKREP_ID)=1,
                CONCAT("000",r.RISKREP_ID),if(LENGTH(r.RISKREP_ID)=2,concat("00",r.RISKREP_ID),if(LENGTH(r.RISKREP_ID)="3",
                concat("0",r.RISKREP_ID),r.RISKREP_ID)))) AS id,r.RISKREP_DATESAVE,r.RISKREP_STARTDATE,DATE(NOW()) AS date_now,
                DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) AS date_count,CASE WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) 
                BETWEEN 1 AND 30 THEN "5" WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) BETWEEN 31 AND 183 THEN "4"
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) BETWEEN 184 AND 730 THEN "3"
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) BETWEEN 731 AND 1825 THEN "2" 
                WHEN DATEDIFF(DATE(NOW()),r.RISKREP_DATESAVE) > 1825 THEN "1" END AS "likelihood",
                l.RISK_REP_LEVEL_NAME,CASE WHEN l.RISK_REP_LEVEL_NAME IN ("I","5") THEN "5" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("G","H","4") THEN "4" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("E","F","3") THEN "3" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("B","C","D","2") THEN "2" 
                WHEN l.RISK_REP_LEVEL_NAME IN ("A","1")THEN "1" END AS "consequence",            
                p.RISK_REPPROGRAM_NAME,ps.RISK_REPPROGRAMSUB_NAME,ps.RISK_REPPROGRAMSUB_DETAIL AS clinic,r.RISKREP_DETAILRISK,
                GROUP_CONCAT(rc.RISK_RECHECK_DATE) AS "recheck"
                FROM risk_rep r LEFT JOIN risk_rep_program p ON p.RISK_REPPROGRAM_ID=r.RISK_REPPROGRAM_ID  								
                LEFT JOIN risk_rep_program_sub ps ON ps.RISK_REPPROGRAMSUB_ID=r.RISK_REPPROGRAMSUB_ID            
                LEFT JOIN risk_rep_level l ON l.RISK_REP_LEVEL_ID=r.RISKREP_LEVEL
                LEFT JOIN risk_recheck rc ON rc.RISK_RECHECK_RISKID=r.RISKREP_ID
                WHERE r.RISKREP_STATUS <> "CANCEL" GROUP BY r.RISKREP_ID) AS a
                WHERE clinic = "'.$clinic.'" AND consequence = "'.$consequence.'" AND likelihood = "'.$likelihood.'" ');

        return view('backoffice_risk.risk_matrix_detail',compact('risk_matrix_detail'));
}

}
