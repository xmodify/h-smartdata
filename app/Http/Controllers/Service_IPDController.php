<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service_IPDController extends Controller
{
//Check Login
      public function __construct()
      {
          $this->middleware('auth');
      }


//CCreate index
public function index()
{
      return view('service_ipd.index');
}

//Create count
public function count(Request $request)
{
 
$budget_year_select = DB::table('budget_year')
        ->select('LEAVE_YEAR_ID', 'LEAVE_YEAR_NAME')
        ->orderByDesc('LEAVE_YEAR_ID')
        ->limit(7)
        ->get();
$budget_year_now = DB::table('budget_year')
        ->whereDate('DATE_END', '>=', date('Y-m-d'))
        ->whereDate('DATE_BEGIN', '<=', date('Y-m-d'))
        ->value('LEAVE_YEAR_ID');       
$budget_year = $request->budget_year ?: $budget_year_now;
$year_data = DB::table('budget_year')
        ->whereIn('LEAVE_YEAR_ID', [$budget_year, $budget_year - 4])
        ->pluck('DATE_BEGIN', 'LEAVE_YEAR_ID');       
$start_date   = $year_data[$budget_year] ?? null;
$end_date = DB::table('budget_year')
        ->where('LEAVE_YEAR_ID', $budget_year)
        ->value('DATE_END');
$start_date_y = $year_data[$budget_year - 4] ?? DB::table('budget_year')->orderBy('LEAVE_YEAR_ID')->value('DATE_BEGIN');

$ipd_month = DB::connection('hosxp')->select('select
        CASE WHEN MONTH(i.dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        END AS "month",
        COUNT(DISTINCT i.an) AS an ,sum(a.admdate) AS admdate,
        ROUND((SUM(a.admdate) * 100) / (60 * CASE WHEN YEAR(a.dchdate) = YEAR(CURDATE()) AND MONTH(a.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(a.dchdate))END), 2) AS bed_occupancy,
        ROUND((SUM(a.admdate) / CASE WHEN YEAR(a.dchdate) = YEAR(CURDATE()) AND MONTH(a.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(a.dchdate)) END), 2) AS active_bed, 
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        SUM(a.income-a.rcpt_money)/SUM(i.adjrw) AS "income_rw",
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi,
        SUM(CASE WHEN i.regtime BETWEEN "08:00:00" AND "15:59:59" THEN 1 ELSE 0 END) AS "visit_i",
        SUM(CASE WHEN i.regtime BETWEEN "16:00:00" AND "23:59:59" THEN 1 ELSE 0 END) AS "visit_o",
        SUM(CASE WHEN i.regtime BETWEEN "00:00:00" AND "07:59:59" THEN 1 ELSE 0 END) AS "visit_s"
        FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        WHERE i.dchdate BETWEEN ? AND ?
        AND a.pdx NOT IN ("Z290","Z208")
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)',[$start_date,$end_date]);

$ipd_month_sum = DB::connection('hosxp')->select('select
        COUNT(DISTINCT i.an) AS an,sum(a.admdate) AS admdate,
        ROUND((SUM(a.admdate) * 100) / (60 * (DATEDIFF(LEAST(?, CURDATE()), ?) + 1)),2) AS bed_occupancy,
        ROUND(SUM(a.admdate) / (DATEDIFF(LEAST(?, CURDATE()), ?) + 1),2) AS active_bed,
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        SUM(a.income-a.rcpt_money)/SUM(i.adjrw) AS "income_rw",
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM an_stat a
        INNER JOIN ipt i ON a.an=i.an
        WHERE i.dchdate BETWEEN ? AND ?
        AND a.pdx NOT IN ("Z290","Z208")',[$end_date,$start_date,$end_date,$start_date,$start_date,$end_date]);

$ipd_month_normal = DB::connection('hosxp')->select('select
        CASE WHEN MONTH(i.dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        END AS "month",
        COUNT(DISTINCT i.an) AS an ,sum(a.admdate) AS admdate,
        ROUND((SUM(a.admdate) * 100) / (60 * CASE WHEN YEAR(a.dchdate) = YEAR(CURDATE()) AND MONTH(a.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(a.dchdate))END), 2) AS bed_occupancy,
        ROUND((SUM(a.admdate) / CASE WHEN YEAR(a.dchdate) = YEAR(CURDATE()) AND MONTH(a.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(a.dchdate)) END), 2) AS active_bed, 
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        SUM(a.income-a.rcpt_money)/SUM(i.adjrw) AS "income_rw",
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi,
        SUM(CASE WHEN i.regtime BETWEEN "08:00:00" AND "15:59:59" THEN 1 ELSE 0 END) AS "visit_i",
        SUM(CASE WHEN i.regtime BETWEEN "16:00:00" AND "23:59:59" THEN 1 ELSE 0 END) AS "visit_o",
        SUM(CASE WHEN i.regtime BETWEEN "00:00:00" AND "07:59:59" THEN 1 ELSE 0 END) AS "visit_s"
        FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        WHERE i.dchdate BETWEEN ? AND ?
        AND a.pdx NOT IN ("Z290","Z208") AND a.ward <> "06"
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)',[$start_date,$end_date]);

$ipd_month_normal_sum = DB::connection('hosxp')->select('select
        COUNT(DISTINCT i.an) AS an,sum(a.admdate) AS admdate,
        ROUND((SUM(a.admdate) * 100) / (60 * (DATEDIFF(LEAST(?, CURDATE()), ?) + 1)),2) AS bed_occupancy,
        ROUND(SUM(a.admdate) / (DATEDIFF(LEAST(?, CURDATE()), ?) + 1),2) AS active_bed,
        ROUND(SUM(i.adjrw),2) AS adjrw ,SUM(a.income-a.rcpt_money)/SUM(i.adjrw) AS "income_rw",
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM an_stat a
        INNER JOIN ipt i ON a.an=i.an
        WHERE i.dchdate BETWEEN ? AND ?
        AND a.pdx NOT IN ("Z290","Z208")  AND a.ward <> "06" ',[$end_date,$start_date,$end_date,$start_date,$start_date,$end_date]);

$ipd_month_homeward = DB::connection('hosxp')->select('select
        CASE WHEN MONTH(i.dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        END AS "month",
        COUNT(DISTINCT i.an) AS an ,sum(a.admdate) AS admdate,
        ROUND((SUM(a.admdate) * 100) / (60 * CASE WHEN YEAR(a.dchdate) = YEAR(CURDATE()) AND MONTH(a.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(a.dchdate))END), 2) AS bed_occupancy,
        ROUND((SUM(a.admdate) / CASE WHEN YEAR(a.dchdate) = YEAR(CURDATE()) AND MONTH(a.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(a.dchdate)) END), 2) AS active_bed, 
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        SUM(a.income-a.rcpt_money)/SUM(i.adjrw) AS "income_rw",
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi,
        SUM(CASE WHEN i.regtime BETWEEN "08:00:00" AND "15:59:59" THEN 1 ELSE 0 END) AS "visit_i",
        SUM(CASE WHEN i.regtime BETWEEN "16:00:00" AND "23:59:59" THEN 1 ELSE 0 END) AS "visit_o",
        SUM(CASE WHEN i.regtime BETWEEN "00:00:00" AND "07:59:59" THEN 1 ELSE 0 END) AS "visit_s"
        FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        WHERE i.dchdate BETWEEN ? AND ?
        AND a.pdx NOT IN ("Z290","Z208") AND a.ward = "06"
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)',[$start_date,$end_date]);

$ipd_month_homeward_sum = DB::connection('hosxp')->select('select
        COUNT(DISTINCT i.an) AS an,sum(a.admdate) AS admdate,
        ROUND((SUM(a.admdate) * 100) / (60 * (DATEDIFF(LEAST(?, CURDATE()), ?) + 1)),2) AS bed_occupancy,
        ROUND(SUM(a.admdate) / (DATEDIFF(LEAST(?, CURDATE()), ?) + 1),2) AS active_bed,
        ROUND(SUM(i.adjrw),2) AS adjrw ,SUM(a.income-a.rcpt_money)/SUM(i.adjrw) AS "income_rw",
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM an_stat a
        INNER JOIN ipt i ON a.an=i.an
        WHERE i.dchdate BETWEEN ? AND ?
        AND a.pdx NOT IN ("Z290","Z208")  AND a.ward = "06" ',[$end_date,$start_date,$end_date,$start_date,$start_date,$end_date]);

$ipd_m = array_column($ipd_month,'month');
$ipd_an_m = array_column($ipd_month,'an');
$ipd_admdate_m = array_column($ipd_month,'admdate');
$ipd_bed_occupancy_m = array_column($ipd_month,'bed_occupancy');
$ipd_active_bed_m = array_column($ipd_month,'active_bed');
$ipd_adjrw_m = array_column($ipd_month,'adjrw');
$ipd_cmi_m = array_column($ipd_month,'cmi');
$ipd_visit_i = array_column($ipd_month,'visit_i');
$ipd_visit_o = array_column($ipd_month,'visit_o');
$ipd_visit_s = array_column($ipd_month,'visit_s');

$ipd_year = DB::connection('hosxp')->select('select
        IF(MONTH(a.dchdate)>9,YEAR(a.dchdate)+1,YEAR(a.dchdate)) + 543 AS year_bud,
        COUNT(DISTINCT i.an) AS visit,sum(a.admdate) AS admdate,
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM an_stat a
        INNER JOIN ipt i ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
        AND a.pdx NOT IN ("Z290","Z208")
        GROUP BY year_bud');
$ipd_y = array_column($ipd_year,'year_bud');
$ipd_visit_y = array_column($ipd_year,'visit');
$ipd_admdate_y = array_column($ipd_year,'admdate');
$ipd_adjrw_y = array_column($ipd_year,'adjrw');
$ipd_cmi_y = array_column($ipd_year,'cmi');

$ipd_month_pttype = DB::connection('hosxp')->select('select          
        COUNT(an) AS an, CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(dchdate)+543,2))
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
        END AS "month", SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
        SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
        SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
        SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
        SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
        SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
        SUM(CASE WHEN hipdata_code in ("A1","A9") OR pttype like "C%" OR pttype like "E%"  
        OR pttype like "P%" OR pttype IN ("A1","Z3","G1") THEN 1 ELSE 0 END) AS "pay"
        FROM (SELECT a.dchdate,a.an,a.pttype,p.hipdata_code FROM an_stat a
        LEFT JOIN pttype p ON p.pttype=a.pttype
        WHERE a.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx NOT IN ("Z290","Z208")
        GROUP BY a.an) AS a									
        GROUP BY MONTH(dchdate)
        ORDER BY YEAR(dchdate) , MONTH(dchdate)');

$ipd_spclty = DB::connection('hosxp')->select('
        SELECT s.`name`,COUNT(i.an) AS an,COUNT(DISTINCT i.hn) AS hn
        FROM ipt i 
        INNER JOIN spclty s ON s.spclty=i.spclty
        WHERE i.regdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY i.spclty
        ORDER BY an DESC');
$ipd_spclty_name = array_column($ipd_spclty,'name');
$ipd_spclty_an = array_column($ipd_spclty,'an');
$ipd_spclty_hn = array_column($ipd_spclty,'hn');

return view('service_ipd.count',compact('budget_year_select','budget_year','ipd_month_pttype','ipd_month','ipd_m','ipd_an_m','ipd_admdate_m',
        'ipd_bed_occupancy_m','ipd_y','ipd_visit_y','ipd_admdate_y','ipd_month_sum','ipd_active_bed_m','ipd_adjrw_m','ipd_cmi_m',
        'ipd_adjrw_y','ipd_cmi_y','ipd_visit_i','ipd_visit_o','ipd_visit_s','ipd_spclty_name','ipd_spclty_an','ipd_spclty_hn',
        'ipd_month_normal','ipd_month_normal_sum','ipd_month_homeward','ipd_month_homeward_sum'));

}
//Create count_spclty
public function count_spclty(Request $request)
{

$budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
$budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
$budget_year = $request->budget_year;
if($budget_year == '' || $budget_year == null)
{$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
$start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
$start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
$end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

$ipd = DB::connection('hosxp')->select('select
        CASE WHEN MONTH(i.dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        END AS "month",
        COUNT(DISTINCT i.an) AS an ,sum(a.admdate) AS admdate,
        ROUND((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate))),2) AS "bed_occupancy",
        ROUND(((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate)))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx NOT IN ("Z290","Z208")
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)');

$ipd_sum = DB::connection('hosxp')->select('select
        COUNT(DISTINCT i.an) AS an,sum(a.admdate) AS admdate,
        ROUND((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'")),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'"))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM an_stat a
        INNER JOIN ipt i ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx NOT IN ("Z290","Z208")');


$ipd_kidney = DB::connection('hosxp')->select('select
        CASE WHEN MONTH(i.dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        END AS "month",
        COUNT(DISTINCT i.an) AS an ,sum(a.admdate) AS admdate,
        ROUND((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate))),2) AS "bed_occupancy",
        ROUND(((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate)))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx IN ("N170","N171","N172","N178","N179","N181","N182","N183","N184","N185","N189","N19")
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)');

$ipd_sum_kidney = DB::connection('hosxp')->select('select
        COUNT(DISTINCT i.an) AS an,sum(a.admdate) AS admdate,
        ROUND((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'")),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'"))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM an_stat a
        INNER JOIN ipt i ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx IN ("N170","N171","N172","N178","N179","N181","N182","N183","N184","N185","N189","N19")');

$ipd_obs = DB::connection('hosxp')->select('select
        CASE WHEN MONTH(i.dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        END AS "month",
        COUNT(DISTINCT i.an) AS an ,sum(a.admdate) AS admdate,
        ROUND((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate))),2) AS "bed_occupancy",
        ROUND(((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate)))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx IN ("D250","D251","D252","D259","N700","N701","N702","N709","N710","N711","N719",
        "N72","N730","N731","N732","N733","N734","N735","N736","N738","N739","N750","N751","N758",
        "N759","N760","N761","N762","N763","N764","N765","N766","N768","N770","N771","N778","N800",
        "N801","N802","N803","N804","N805","N806","N807","N808","N809","N8810","N811","N812","N813",
        "N814","N815","N816","N817","N818","N819","N820","N821","N822","N823","N824","N825","N828",
        "N829","N830","N831","N832","N833","N834","N835","N836","N837","N838","N839","N840","N841",
        "N842","N843","N844","N849","N850","N851","N852","N853","N854","N856","N857","N858","N859",
        "N86","N870","N871","N872","N873","N879","N880","N881","N882","N883","N884","N889","N890", 
        "N891","N892","N893","N894","N895","N896","N897","N898","N899","N901","N902","N903","N904",
        "N905","N906","N907","N908","N909","N900","N910","N911","N912","N913","N914","N915","N920",
        "N921","N922","N923","N924","N925","N926","N9300","N9301","N938","N939","N940","N941","N942",
        "N943","N944","N945","N946","N948","N949","N950","N951","N952","N953","N958","N959","N96",
        "N970","N971","N972","N973","N974","N975","N978","N979","N980","N981","N982","N983","N988",
        "N989","N990","N991","N992","N993","N994","N995","N998","N999","Z302")
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)');

$ipd_sum_obs = DB::connection('hosxp')->select('select
        COUNT(DISTINCT i.an) AS an,sum(a.admdate) AS admdate,
        ROUND((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'")),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'"))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM an_stat a
        INNER JOIN ipt i ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx IN ("D250","D251","D252","D259","N700","N701","N702","N709","N710","N711","N719",
        "N72","N730","N731","N732","N733","N734","N735","N736","N738","N739","N750","N751","N758",
        "N759","N760","N761","N762","N763","N764","N765","N766","N768","N770","N771","N778","N800",
        "N801","N802","N803","N804","N805","N806","N807","N808","N809","N8810","N811","N812","N813",
        "N814","N815","N816","N817","N818","N819","N820","N821","N822","N823","N824","N825","N828",
        "N829","N830","N831","N832","N833","N834","N835","N836","N837","N838","N839","N840","N841",
        "N842","N843","N844","N849","N850","N851","N852","N853","N854","N856","N857","N858","N859",
        "N86","N870","N871","N872","N873","N879","N880","N881","N882","N883","N884","N889","N890", 
        "N891","N892","N893","N894","N895","N896","N897","N898","N899","N901","N902","N903","N904",
        "N905","N906","N907","N908","N909","N900","N910","N911","N912","N913","N914","N915","N920",
        "N921","N922","N923","N924","N925","N926","N9300","N9301","N938","N939","N940","N941","N942",
        "N943","N944","N945","N946","N948","N949","N950","N951","N952","N953","N958","N959","N96",
        "N970","N971","N972","N973","N974","N975","N978","N979","N980","N981","N982","N983","N988",
        "N989","N990","N991","N992","N993","N994","N995","N998","N999","Z302")'); 

$ipd_dka = DB::connection('hosxp')->select('select
        CASE WHEN MONTH(i.dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        END AS "month",
        COUNT(DISTINCT i.an) AS an ,sum(a.admdate) AS admdate,
        ROUND((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate))),2) AS "bed_occupancy",
        ROUND(((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate)))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx IN ("E101","E111","E121","E131","E141")
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)');

$ipd_sum_dka = DB::connection('hosxp')->select('select
        COUNT(DISTINCT i.an) AS an,sum(a.admdate) AS admdate,
        ROUND((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'")),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'"))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM an_stat a
        INNER JOIN ipt i ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx IN ("E101","E111","E121","E131","E141")'); 

$ipd_hhs = DB::connection('hosxp')->select('select
        CASE WHEN MONTH(i.dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        END AS "month",
        COUNT(DISTINCT i.an) AS an ,sum(a.admdate) AS admdate,
        ROUND((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate))),2) AS "bed_occupancy",
        ROUND(((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate)))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx IN ("E100","E110","E120","E130","E140")
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)');

$ipd_sum_hhs = DB::connection('hosxp')->select('select
        COUNT(DISTINCT i.an) AS an,sum(a.admdate) AS admdate,
        ROUND((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'")),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'"))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM an_stat a
        INNER JOIN ipt i ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx IN ("E100","E110","E120","E130","E140")'); 

$ipd_septicshock = DB::connection('hosxp')->select('select
        CASE WHEN MONTH(i.dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        END AS "month",
        COUNT(DISTINCT i.an) AS an ,sum(a.admdate) AS admdate,
        ROUND((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate))),2) AS "bed_occupancy",
        ROUND(((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate)))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx IN ("R572")
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)');

$ipd_sum_septicshock = DB::connection('hosxp')->select('select
        COUNT(DISTINCT i.an) AS an,sum(a.admdate) AS admdate,
        ROUND((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'")),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'"))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM an_stat a
        INNER JOIN ipt i ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx IN ("R572")'); 

$ipd_pneumonia = DB::connection('hosxp')->select('select
        CASE WHEN MONTH(i.dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        END AS "month",
        COUNT(DISTINCT i.an) AS an ,sum(a.admdate) AS admdate,
        ROUND((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate))),2) AS "bed_occupancy",
        ROUND(((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate)))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx IN ("J120","J121","122","J123","J128","J129","J13","J14","J150","J151","J152",
        "J153","J154","J155","J156","J157","J158","159","J160","J168","J180","181","J182","J188","J189")        
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)');

$ipd_sum_pneumonia = DB::connection('hosxp')->select('select
        COUNT(DISTINCT i.an) AS an,sum(a.admdate) AS admdate,
        ROUND((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'")),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'"))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM an_stat a
        INNER JOIN ipt i ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx IN ("J120","J121","122","J123","J128","J129","J13","J14","J150","J151","J152",
        "J153","J154","J155","J156","J157","J158","159","J160","J168","J180","181","J182","J188","J189")'); 

$ipd_sur = DB::connection('hosxp')->select('select
        CASE WHEN MONTH(i.dchdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(i.dchdate)+543,2))
        WHEN MONTH(i.dchdate)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(i.dchdate)+543,2))
        END AS "month",
        COUNT(DISTINCT i.an) AS an ,sum(a.admdate) AS admdate,
        ROUND((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate))),2) AS "bed_occupancy",
        ROUND(((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate)))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx IN ("H110","H111","H112","H113","H114","H118","H119","H250","H251","H252","H258","H259",
        "H260","H261","H262","H263","H264","H400","H401","H402","H403","H404","H405","H406","H408","H409")
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)');

$ipd_sum_sur = DB::connection('hosxp')->select('select
        COUNT(DISTINCT i.an) AS an,sum(a.admdate) AS admdate,
        ROUND((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'")),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*DATEDIFF(IF("'.$end_date.'" >= DATE(NOW()),DATE(NOW()),"'.$end_date.'"),"'.$start_date.'"))*60)/100,2) AS "active_bed",
        ROUND(SUM(i.adjrw),2) AS adjrw ,
        ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi
        FROM an_stat a
        INNER JOIN ipt i ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx IN ("H110","H111","H112","H113","H114","H118","H119","H250","H251","H252","H258","H259",
        "H260","H261","H262","H263","H264","H400","H401","H402","H403","H404","H405","H406","H408","H409")');

return view('service_ipd.count_spclty',compact('budget_year_select','budget_year','ipd','ipd_sum','ipd_kidney','ipd_sum_kidney',
        'ipd_obs','ipd_sum_obs','ipd_dka','ipd_sum_dka','ipd_hhs','ipd_sum_hhs','ipd_septicshock','ipd_sum_septicshock',
        'ipd_pneumonia','ipd_sum_pneumonia','ipd_sur','ipd_sum_sur'));

}

//Create diag_505
public function diag_505(Request $request)
{

$budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
$budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
$budget_year = $request->budget_year;
if($budget_year == '' || $budget_year == null)
{$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
$start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
$end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

$diag_505 = DB::connection('hosxp')->select('select
        concat(a.name2," [",a.id,"]")as name,
        ifnull(d.male,0) as male,ifnull(d.female,0) as female,ifnull(d.amount,0) as sum
        from rpt_505_name a
        left join (select b.id,
        sum(case when v.sex=1 THEN 1 ELSE 0 END) as male,
        sum(case when v.sex=2 THEN 1 ELSE 0 END) as female ,
        count(b.id) as amount
        from rpt_505_code b ,an_stat v
        where v.pdx between b.code1 and b.code2
        and v.dchdate between "'.$start_date.'" AND "'.$end_date.'"
        AND v.pdx NOT IN ("Z290","Z208")
        group by b.id) d on d.id=a.id
        order by sum desc');

return view('service_ipd.diag_505',compact('budget_year_select','budget_year','diag_505'));

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
        concat("[",v.pdx,"] " ,i.name) as name,count(v.pdx) as sum,
        sum(case when v.sex=1 THEN 1 ELSE 0 END) as male,
        sum(case when v.sex=2 THEN 1 ELSE 0 END) as female,
        sum(v.inc03) as inc_lab,
	sum(v.inc12) as inc_drug
        FROM an_stat v
        left outer join icd101 i on i.code=v.pdx
        where v.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        and v.pdx<>"" and v.pdx is not null and v.pdx not like "z%"
        AND v.pdx NOT IN ("Z290","Z208")
        group by v.pdx,i.name
        order by sum desc limit 30');

return view('service_ipd.diag_top30',compact('budget_year_select','budget_year','diag_top30'));

}

//Create ipd_oper
public function ipd_oper(Request $request)
{

$budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
$budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
$budget_year = $request->budget_year;
if($budget_year == '' || $budget_year == null)
{$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
$start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
$start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
$end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

$ipd_oper_month = DB::connection('hosxp')->select('select
        CASE WHEN MONTH(DATE(dchdate))="10" THEN "ต.ค."
        WHEN MONTH(DATE(dchdate))="11" THEN "พ.ย."
        WHEN MONTH(DATE(dchdate))="12" THEN "ธ.ค."
        WHEN MONTH(DATE(dchdate))="1" THEN "ม.ค."
        WHEN MONTH(DATE(dchdate))="2" THEN "ก.พ."
        WHEN MONTH(DATE(dchdate))="3" THEN "มี.ค."
        WHEN MONTH(DATE(dchdate))="4" THEN "เม.ย."
        WHEN MONTH(DATE(dchdate))="5" THEN "พ.ค."
        WHEN MONTH(DATE(dchdate))="6" THEN "มิ.ย."
        WHEN MONTH(DATE(dchdate))="7" THEN "ก.ค."
        WHEN MONTH(DATE(dchdate))="8" THEN "ส.ค."
        WHEN MONTH(DATE(dchdate))="9" THEN "ก.ย."
        END AS "month",
        SUM(CASE WHEN icd9 like "%9604%" OR ipt_oper_code like "%27%" THEN 1 ELSE 0 END) AS "intube",
        SUM(CASE WHEN icd9 like "%9960%" OR ipt_oper_code like "%13%" THEN 1 ELSE 0 END) AS "cpr"
        FROM (SELECT i.hn,i.an,i.regdate,i.regtime,i.dchdate,i.dchtime,
        CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,GROUP_CONCAT(i2.icd9) AS icd9,
        GROUP_CONCAT(i3.ipt_oper_code) AS ipt_oper_code,GROUP_CONCAT(i4.`name`) AS ipt_oper,
        r.refer_date,r.refer_time
        FROM ipt i
        LEFT JOIN (SELECT an,icd9 FROM iptoprt
        WHERE icd9 IN ("9960","9604") GROUP BY an,icd9) i2 ON i2.an=i.an
        LEFT JOIN (SELECT an,ipt_oper_code FROM ipt_nurse_oper
        WHERE ipt_oper_code IN ("13","27") GROUP BY an,ipt_oper_code) i3 ON i3.an=i.an
        LEFT JOIN patient p ON p.hn=i.hn
        LEFT JOIN referout r ON r.vn=i.an
        LEFT JOIN ipt_oper_code i4 ON i4.ipt_oper_code=i3.ipt_oper_code
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND (i2.icd9 IS NOT NULL OR i3.ipt_oper_code IS NOT NULL)
        GROUP BY i.an) AS a
        GROUP BY MONTH(DATE(dchdate))
        ORDER BY YEAR(DATE(dchdate)),MONTH(DATE(dchdate))');

$ipd_oper_m = array_column($ipd_oper_month,'month');
$ipd_oper_intube_m = array_column($ipd_oper_month,'intube');
$ipd_oper_cpr_m = array_column($ipd_oper_month,'cpr');

$ipd_oper_year = DB::connection('hosxp')->select('select
        IF(MONTH(dchdate)>9,YEAR(dchdate)+1,YEAR(dchdate)) + 543 AS year_bud,
        SUM(CASE WHEN icd9 like "%9604%" OR ipt_oper_code like "%27%" THEN 1 ELSE 0 END) AS "intube",
        SUM(CASE WHEN icd9 like "%9960%" OR ipt_oper_code like "%13%" THEN 1 ELSE 0 END) AS "cpr"
        FROM (SELECT i.hn,i.an,i.regdate,i.regtime,i.dchdate,i.dchtime,
        CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,GROUP_CONCAT(i2.icd9) AS icd9,
        GROUP_CONCAT(i3.ipt_oper_code) AS ipt_oper_code,GROUP_CONCAT(i4.`name`) AS ipt_oper,
        r.refer_date,r.refer_time
        FROM ipt i
        LEFT JOIN (SELECT an,icd9 FROM iptoprt
        WHERE icd9 IN ("9960","9604") GROUP BY an,icd9) i2 ON i2.an=i.an
        LEFT JOIN (SELECT an,ipt_oper_code FROM ipt_nurse_oper
        WHERE ipt_oper_code IN ("13","27") GROUP BY an,ipt_oper_code) i3 ON i3.an=i.an
        LEFT JOIN patient p ON p.hn=i.hn
        LEFT JOIN referout r ON r.vn=i.an
        LEFT JOIN ipt_oper_code i4 ON i4.ipt_oper_code=i3.ipt_oper_code
        WHERE i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
        AND (i2.icd9 IS NOT NULL OR i3.ipt_oper_code IS NOT NULL)
        GROUP BY i.an) AS a
        GROUP BY year_bud
        ORDER BY year_bud');
$ipd_oper_y = array_column($ipd_oper_year,'year_bud');
$ipd_oper_intube_y = array_column($ipd_oper_year,'intube');
$ipd_oper_cpr_y = array_column($ipd_oper_year,'cpr');

$ipd_oper_list_intube = DB::connection('hosxp')->select('select
        i.hn,i.an,i.regdate,i.regtime,i.dchdate,i.dchtime,
        CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,i2.icd9,
        i3.ipt_oper_code,i4.`name` AS ipt_oper,r.refer_date,r.refer_time,r.pdx AS pdx_refer
        FROM ipt i
        LEFT JOIN (SELECT an,icd9 FROM iptoprt
        WHERE icd9 IN ("9604") GROUP BY an) i2 ON i2.an=i.an
        LEFT JOIN (SELECT an,ipt_oper_code FROM ipt_nurse_oper
        WHERE ipt_oper_code IN ("27") GROUP BY an) i3 ON i3.an=i.an
        LEFT JOIN an_stat a ON a.an=i.an
        LEFT JOIN patient p ON p.hn=i.hn
        LEFT JOIN referout r ON r.vn=i.an
        LEFT JOIN ipt_oper_code i4 ON i4.ipt_oper_code=i3.ipt_oper_code
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND (i2.icd9 IS NOT NULL OR i3.ipt_oper_code IS NOT NULL)
        GROUP BY i.an');
$ipd_oper_list_cpr = DB::connection('hosxp')->select('select
        i.hn,i.an,i.regdate,i.regtime,i.dchdate,i.dchtime,
        CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,i2.icd9,
        i3.ipt_oper_code,i4.`name` AS ipt_oper,r.refer_date,r.refer_time,r.pdx AS pdx_refer
        FROM ipt i
        LEFT JOIN (SELECT an,icd9 FROM iptoprt
        WHERE icd9 IN ("9960") GROUP BY an) i2 ON i2.an=i.an
        LEFT JOIN (SELECT an,ipt_oper_code FROM ipt_nurse_oper
        WHERE ipt_oper_code IN ("13") GROUP BY an) i3 ON i3.an=i.an
        LEFT JOIN an_stat a ON a.an=i.an
        LEFT JOIN patient p ON p.hn=i.hn
        LEFT JOIN referout r ON r.vn=i.an
        LEFT JOIN ipt_oper_code i4 ON i4.ipt_oper_code=i3.ipt_oper_code
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND (i2.icd9 IS NOT NULL OR i3.ipt_oper_code IS NOT NULL)
        GROUP BY i.an');

return view('service_ipd.ipd_oper',compact('budget_year_select','budget_year','ipd_oper_m','ipd_oper_intube_m',
        'ipd_oper_cpr_m','ipd_oper_y','ipd_oper_intube_y','ipd_oper_cpr_y','ipd_oper_list_intube','ipd_oper_list_cpr'));

}

//Create readmit28
public function readmit28(Request $request)
{
$budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
$budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
$budget_year = $request->budget_year;
if($budget_year == '' || $budget_year == null)
{$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
$start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
$start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
$end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

$readmit28_month = DB::connection('hosxp')->select('
        SELECT CASE WHEN MONTH(a.regdate_AN_New)="10" THEN "ต.ค."
        WHEN MONTH(a.regdate_AN_New)="11" THEN "พ.ย."
        WHEN MONTH(a.regdate_AN_New)="12" THEN "ธ.ค."
        WHEN MONTH(a.regdate_AN_New)="1" THEN "ม.ค."
        WHEN MONTH(a.regdate_AN_New)="2" THEN "ก.พ."
        WHEN MONTH(a.regdate_AN_New)="3" THEN "มี.ค."
        WHEN MONTH(a.regdate_AN_New)="4" THEN "เม.ย."
        WHEN MONTH(a.regdate_AN_New)="5" THEN "พ.ค."
        WHEN MONTH(a.regdate_AN_New)="6" THEN "มิ.ย."
        WHEN MONTH(a.regdate_AN_New)="7" THEN "ก.ค."
        WHEN MONTH(a.regdate_AN_New)="8" THEN "ส.ค."
        WHEN MONTH(a.regdate_AN_New)="9" THEN "ก.ย."
        END AS "month",
        sum(case when a.sex=1 THEN 1 ELSE 0 END) as male,
        sum(case when a.sex=2 THEN 1 ELSE 0 END) as female,
        count(a.AN_new) as sum
        FROM (SELECT q3.hn,patient.sex,patient.pname,patient.fname,patient.lname,q3.AN_new,q3.regdate_AN_New,q3.dcdate_AN_New,
        q3.AN_old,q3.regdate_AN_Old,q3.dcdate_AN_Old,q3.icd10_1,q3.ReAdmitDate
        FROM patient INNER JOIN (SELECT q1.hn,q1.an AS AN_new,q1.regdate AS regdate_AN_New,q1.dchdate AS dcdate_AN_New,
        q2.an AS AN_old,q2.regdate AS regdate_AN_Old,q2.dchdate AS dcdate_AN_Old,q1.icd10 AS icd10_1,
        TimestampDiff(day,SubString(q2.dchdate FROM 1 FOR 10 ), SubString(q1.regdate FROM 1 FOR 10 )) AS ReAdmitDate
        FROM (SELECT ipt.hn,ipt.an,ipt.regdate,ipt.dchdate,iptdiag.icd10,iptdiag.diagtype
        FROM ipt
        INNER JOIN iptdiag ON ipt.an = iptdiag.an
        WHERE ipt.hn <> "" AND iptdiag.diagtype = "1") AS q1
        CROSS JOIN (SELECT ipt1.hn,ipt1.an,ipt1.regdate,ipt1.dchdate,iptdiag1.icd10,iptdiag1.diagtype
        FROM ipt AS ipt1 INNER JOIN iptdiag AS iptdiag1 ON ipt1.an = iptdiag1.an
        WHERE ipt1.hn <> "" AND iptdiag1.diagtype = "1") AS q2
        WHERE q1.hn = q2.hn AND q1.icd10 = q2.icd10 AND q1.an <> q2.an AND TimestampDiff(day, SubString(q2.dchdate FROM 1 FOR 10 ), SubString(q1.regdate
        FROM 1 FOR 10 )) > 0 AND TimestampDiff(day, SubString(q2.dchdate FROM 1 FOR 10 ), SubString(q1.regdate FROM 1 FOR 10 )) <= 28 AND
        q1.regdate BETWEEN "'.$start_date.'" AND "'.$end_date.'") AS q3 ON q3.hn = patient.hn ORDER BY q3.AN_new) AS a
        GROUP BY MONTH(a.regdate_AN_New)
        ORDER BY YEAR(a.regdate_AN_New) , MONTH(a.regdate_AN_New)');
$readmit28_m = array_column($readmit28_month,'month');
$readmit28_male_m = array_column($readmit28_month,'male');
$readmit28_female_m = array_column($readmit28_month,'female');

$readmit28_year = DB::connection('hosxp')->select('
        SELECT IF(MONTH(a.regdate_AN_New)>9,YEAR(a.regdate_AN_New)+1,YEAR(a.regdate_AN_New)) + 543 AS year_bud,
        sum(case when a.sex=1 THEN 1 ELSE 0 END) as male,
        sum(case when a.sex=2 THEN 1 ELSE 0 END) as female,
        count(a.AN_new) as sum
        FROM (SELECT q3.hn,patient.sex,patient.pname,patient.fname,patient.lname,q3.AN_new,q3.regdate_AN_New,q3.dcdate_AN_New,
        q3.AN_old,q3.regdate_AN_Old,q3.dcdate_AN_Old,q3.icd10_1,q3.ReAdmitDate
        FROM patient INNER JOIN (SELECT q1.hn,q1.an AS AN_new,q1.regdate AS regdate_AN_New,q1.dchdate AS dcdate_AN_New,
        q2.an AS AN_old,q2.regdate AS regdate_AN_Old,q2.dchdate AS dcdate_AN_Old,q1.icd10 AS icd10_1,
        TimestampDiff(day,SubString(q2.dchdate FROM 1 FOR 10 ), SubString(q1.regdate FROM 1 FOR 10 )) AS ReAdmitDate
        FROM (SELECT ipt.hn,ipt.an,ipt.regdate,ipt.dchdate,iptdiag.icd10,iptdiag.diagtype
        FROM ipt
        INNER JOIN iptdiag ON ipt.an = iptdiag.an
        WHERE ipt.hn <> "" AND iptdiag.diagtype = "1") AS q1
        CROSS JOIN (SELECT ipt1.hn,ipt1.an,ipt1.regdate,ipt1.dchdate,iptdiag1.icd10,iptdiag1.diagtype
        FROM ipt AS ipt1 INNER JOIN iptdiag AS iptdiag1 ON ipt1.an = iptdiag1.an
        WHERE ipt1.hn <> "" AND iptdiag1.diagtype = "1") AS q2
        WHERE q1.hn = q2.hn AND q1.icd10 = q2.icd10 AND q1.an <> q2.an AND TimestampDiff(day, SubString(q2.dchdate FROM 1 FOR 10 ), SubString(q1.regdate
        FROM 1 FOR 10 )) > 0 AND TimestampDiff(day, SubString(q2.dchdate FROM 1 FOR 10 ), SubString(q1.regdate FROM 1 FOR 10 )) <= 28 AND
        q1.regdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'") AS q3 ON q3.hn = patient.hn ORDER BY q3.AN_new) AS a
        GROUP BY year_bud
        ORDER BY year_bud');
$readmit28_y = array_column($readmit28_year,'year_bud');
$readmit28_male_y = array_column($readmit28_year,'male');
$readmit28_female_y = array_column($readmit28_year,'female');

$readmit28_top = DB::connection('hosxp')->select('
        SELECT concat("[",a.icd10_1,"] " ,i.name) as name,
        sum(case when a.sex=1 THEN 1 ELSE 0 END) as male,
        sum(case when a.sex=2 THEN 1 ELSE 0 END) as female,
        count(a.AN_new) as sum
        FROM (SELECT q3.hn,patient.sex,patient.pname,patient.fname,patient.lname,q3.AN_new,q3.regdate_AN_New,q3.dcdate_AN_New,
        q3.AN_old,q3.regdate_AN_Old,q3.dcdate_AN_Old,q3.icd10_1,q3.ReAdmitDate
        FROM patient INNER JOIN (SELECT q1.hn,q1.an AS AN_new,q1.regdate AS regdate_AN_New,q1.dchdate AS dcdate_AN_New,
        q2.an AS AN_old,q2.regdate AS regdate_AN_Old,q2.dchdate AS dcdate_AN_Old,q1.icd10 AS icd10_1,
        TimestampDiff(day,SubString(q2.dchdate FROM 1 FOR 10 ), SubString(q1.regdate FROM 1 FOR 10 )) AS ReAdmitDate
        FROM (SELECT ipt.hn,ipt.an,ipt.regdate,ipt.dchdate,iptdiag.icd10,iptdiag.diagtype
        FROM ipt
        INNER JOIN iptdiag ON ipt.an = iptdiag.an
        WHERE ipt.hn <> "" AND iptdiag.diagtype = "1") AS q1
        CROSS JOIN (SELECT ipt1.hn,ipt1.an,ipt1.regdate,ipt1.dchdate,iptdiag1.icd10,iptdiag1.diagtype
        FROM ipt AS ipt1 INNER JOIN iptdiag AS iptdiag1 ON ipt1.an = iptdiag1.an
        WHERE ipt1.hn <> "" AND iptdiag1.diagtype = "1") AS q2
        WHERE q1.hn = q2.hn AND q1.icd10 = q2.icd10 AND q1.an <> q2.an AND TimestampDiff(day, SubString(q2.dchdate FROM 1 FOR 10 ), SubString(q1.regdate
        FROM 1 FOR 10 )) > 0 AND TimestampDiff(day, SubString(q2.dchdate FROM 1 FOR 10 ), SubString(q1.regdate FROM 1 FOR 10 )) <= 28 AND
        q1.regdate BETWEEN "'.$start_date.'" AND "'.$end_date.'") AS q3 ON q3.hn = patient.hn ORDER BY q3.AN_new) AS a
        LEFT JOIN icd101 i on i.code=a.icd10_1
        GROUP BY a.icd10_1
        ORDER BY sum DESC LIMIT 10');

$readmit28_list = DB::connection('hosxp')->select('
        SELECT *
        FROM (SELECT q3.hn,CONCAT(patient.pname,patient.fname,SPACE(1),patient.lname) AS ptname,q3.AN_new,q3.regdate_AN_New,
        q3.dcdate_AN_New,q3.AN_old,q3.regdate_AN_Old,q3.dcdate_AN_Old,q3.icd10_1,q3.ReAdmitDate
        FROM patient INNER JOIN (SELECT q1.hn,q1.an AS AN_new,q1.regdate AS regdate_AN_New,q1.dchdate AS dcdate_AN_New,
        q2.an AS AN_old,q2.regdate AS regdate_AN_Old,q2.dchdate AS dcdate_AN_Old,q1.icd10 AS icd10_1,
        TimestampDiff(day,SubString(q2.dchdate FROM 1 FOR 10 ), SubString(q1.regdate FROM 1 FOR 10 )) AS ReAdmitDate
        FROM (SELECT ipt.hn,ipt.an,ipt.regdate,ipt.dchdate,iptdiag.icd10,iptdiag.diagtype
        FROM ipt
        INNER JOIN iptdiag ON ipt.an = iptdiag.an
        WHERE ipt.hn <> "" AND iptdiag.diagtype = "1") AS q1
        CROSS JOIN (SELECT ipt1.hn,ipt1.an,ipt1.regdate,ipt1.dchdate,iptdiag1.icd10,iptdiag1.diagtype
        FROM ipt AS ipt1 INNER JOIN iptdiag AS iptdiag1 ON ipt1.an = iptdiag1.an
        WHERE ipt1.hn <> "" AND iptdiag1.diagtype = "1") AS q2
        WHERE q1.hn = q2.hn AND q1.icd10 = q2.icd10 AND q1.an <> q2.an AND TimestampDiff(day, SubString(q2.dchdate FROM 1 FOR 10 ), SubString(q1.regdate
        FROM 1 FOR 10 )) > 0 AND TimestampDiff(day, SubString(q2.dchdate FROM 1 FOR 10 ), SubString(q1.regdate FROM 1 FOR 10 )) <= 28 AND
        q1.regdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" ) AS q3 ON q3.hn = patient.hn ORDER BY q3.AN_new) AS a');

return view('service_ipd.readmit28',compact('budget_year_select','budget_year','readmit28_top','readmit28_list','readmit28_m',
        'readmit28_male_m','readmit28_female_m','readmit28_y','readmit28_male_y','readmit28_female_y'));
}

//Create ระดับความรุนแรง
public function severe_type(Request $request)
{
$budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
$budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
$budget_year = $request->budget_year;
if($budget_year == '' || $budget_year == null)
{$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
$start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
$start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
$end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

$severe_type_month = DB::connection('hosxp')->select('
        SELECT CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย.",SPACE(1),YEAR(dchdate)+543)
        END AS "month",
        SUM(CASE WHEN ipt_severe_type_id = 1 THEN 1 ELSE 0 END) AS "1",
        SUM(CASE WHEN ipt_severe_type_id = 2 THEN 1 ELSE 0 END) AS "2",
        SUM(CASE WHEN ipt_severe_type_id = 3 THEN 1 ELSE 0 END) AS "3",
        SUM(CASE WHEN ipt_severe_type_id = 4 THEN 1 ELSE 0 END) AS "4",
        SUM(CASE WHEN ipt_severe_type_id IS NULL THEN 1 ELSE 0 END) AS "null",
        SUM(CASE WHEN dch_severe_type_id = 1 THEN 1 ELSE 0 END) AS "dch_1",
        SUM(CASE WHEN dch_severe_type_id = 2 THEN 1 ELSE 0 END) AS "dch_2",
        SUM(CASE WHEN dch_severe_type_id = 3 THEN 1 ELSE 0 END) AS "dch_3",
        SUM(CASE WHEN dch_severe_type_id = 4 THEN 1 ELSE 0 END) AS "dch_4",
        SUM(CASE WHEN dch_severe_type_id IS NULL THEN 1 ELSE 0 END) AS "dch_null"
        FROM (SELECT i.an,i.regdate,i.dchdate,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.prediag,
        i.pttype,i.provision_dx_icd,i.provision_dx,i.adjrw,i.ipt_admit_type_id,i.ipt_severe_type_id,i.dch_severe_type_id
        FROM ipt i
        LEFT JOIN patient p ON p.hn=i.hn
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'") AS a
        GROUP BY MONTH(dchdate)
        ORDER BY YEAR(dchdate),MONTH(dchdate)');

$severe_type_m = array_column($severe_type_month,'month');
$severe_type_1 = array_column($severe_type_month,'1');
$severe_type_2 = array_column($severe_type_month,'2');
$severe_type_3 = array_column($severe_type_month,'3');
$severe_type_4 = array_column($severe_type_month,'4');
$severe_type_null = array_column($severe_type_month,'null');
$dch_severe_type_1 = array_column($severe_type_month,'dch_1');
$dch_severe_type_2 = array_column($severe_type_month,'dch_2');
$dch_severe_type_3 = array_column($severe_type_month,'dch_3');
$dch_severe_type_4 = array_column($severe_type_month,'dch_4');
$dch_severe_type_null = array_column($severe_type_month,'dch_null');

$severe_type_year = DB::connection('hosxp')->select('
        SELECT IF(MONTH(dchdate)>=10,YEAR(dchdate)+544 ,YEAR(dchdate)+543) AS budget_year,
        SUM(CASE WHEN ipt_severe_type_id = 1 THEN 1 ELSE 0 END) AS "1",
        SUM(CASE WHEN ipt_severe_type_id = 2 THEN 1 ELSE 0 END) AS "2",
        SUM(CASE WHEN ipt_severe_type_id = 3 THEN 1 ELSE 0 END) AS "3",
        SUM(CASE WHEN ipt_severe_type_id = 4 THEN 1 ELSE 0 END) AS "4",
        SUM(CASE WHEN ipt_severe_type_id IS NULL THEN 1 ELSE 0 END) AS "null",
        SUM(CASE WHEN dch_severe_type_id = 1 THEN 1 ELSE 0 END) AS "dch_1",
        SUM(CASE WHEN dch_severe_type_id = 2 THEN 1 ELSE 0 END) AS "dch_2",
        SUM(CASE WHEN dch_severe_type_id = 3 THEN 1 ELSE 0 END) AS "dch_3",
        SUM(CASE WHEN dch_severe_type_id = 4 THEN 1 ELSE 0 END) AS "dch_4",
        SUM(CASE WHEN dch_severe_type_id IS NULL THEN 1 ELSE 0 END) AS "dch_null"
        FROM (SELECT i.an,i.regdate,i.dchdate,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.prediag,
        i.pttype,i.provision_dx_icd,i.provision_dx,i.adjrw,i.ipt_admit_type_id,i.ipt_severe_type_id,i.dch_severe_type_id
        FROM ipt i
        LEFT JOIN patient p ON p.hn=i.hn
        WHERE i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'") AS a
        GROUP BY budget_year');

$severe_type_y = array_column($severe_type_year,'budget_year');
$severe_type_1_y = array_column($severe_type_year,'1');
$severe_type_2_y = array_column($severe_type_year,'2');
$severe_type_3_y = array_column($severe_type_year,'3');
$severe_type_4_y = array_column($severe_type_year,'4');
$severe_type_null_y = array_column($severe_type_year,'null');
$dch_severe_type_1_y = array_column($severe_type_year,'dch_1');
$dch_severe_type_2_y = array_column($severe_type_year,'dch_2');
$dch_severe_type_3_y = array_column($severe_type_year,'dch_3');
$dch_severe_type_4_y = array_column($severe_type_year,'dch_4');
$dch_severe_type_null_y = array_column($severe_type_year,'dch_null');

$severe_type_list_null = DB::connection('hosxp')->select('
        SELECT i.an,i.regdate,i.regtime,i.dchdate,i.dchtime,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
        i.prediag,i.pttype,i.provision_dx_icd,i.provision_dx,a.pdx,i.adjrw,i.ipt_severe_type_id,i.dch_severe_type_id
        FROM ipt i
        LEFT JOIN an_stat a ON a.an=i.an
        LEFT JOIN patient p ON p.hn=i.hn
        WHERE i.dchdate BETWEEN  "'.$start_date.'" AND "'.$end_date.'"
        AND i.ipt_severe_type_id IS NULL');
$dch_severe_type_list_null = DB::connection('hosxp')->select('
        SELECT i.an,i.regdate,i.regtime,i.dchdate,i.dchtime,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
        i.prediag,i.pttype,i.provision_dx_icd,i.provision_dx,a.pdx,i.adjrw,i.ipt_severe_type_id,i.dch_severe_type_id
        FROM ipt i
        LEFT JOIN an_stat a ON a.an=i.an
        LEFT JOIN patient p ON p.hn=i.hn
        WHERE i.dchdate BETWEEN  "'.$start_date.'" AND "'.$end_date.'"
        AND i.dch_severe_type_id IS NULL');

return view('service_ipd.severe_type',compact('budget_year_select','budget_year','severe_type_m','severe_type_1',
        'severe_type_2','severe_type_3','severe_type_4','severe_type_null','dch_severe_type_1','dch_severe_type_2',
        'dch_severe_type_3','dch_severe_type_4','dch_severe_type_null','severe_type_y','severe_type_1_y',
        'severe_type_2_y','severe_type_3_y','severe_type_4_y','severe_type_null_y','dch_severe_type_1_y',
        'dch_severe_type_2_y','dch_severe_type_3_y','dch_severe_type_4_y','dch_severe_type_null_y',
        'severe_type_list_null','dch_severe_type_list_null'));
}

//Create ระดับความรุนแรง ipd
public function severe_type_ipd(Request $request)
{
$budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
$budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
$budget_year = $request->budget_year;
if($budget_year == '' || $budget_year == null)
{$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
$start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
$start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
$end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

$severe_type_month = DB::connection('hosxp')->select('
        SELECT CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย.",SPACE(1),YEAR(dchdate)+543)
        END AS "month",
        SUM(CASE WHEN ipt_severe_type_id = 1 THEN 1 ELSE 0 END) AS "1",
        SUM(CASE WHEN ipt_severe_type_id = 2 THEN 1 ELSE 0 END) AS "2",
        SUM(CASE WHEN ipt_severe_type_id = 3 THEN 1 ELSE 0 END) AS "3",
        SUM(CASE WHEN ipt_severe_type_id = 4 THEN 1 ELSE 0 END) AS "4",
        SUM(CASE WHEN ipt_severe_type_id IS NULL THEN 1 ELSE 0 END) AS "null",
        SUM(CASE WHEN dch_severe_type_id = 1 THEN 1 ELSE 0 END) AS "dch_1",
        SUM(CASE WHEN dch_severe_type_id = 2 THEN 1 ELSE 0 END) AS "dch_2",
        SUM(CASE WHEN dch_severe_type_id = 3 THEN 1 ELSE 0 END) AS "dch_3",
        SUM(CASE WHEN dch_severe_type_id = 4 THEN 1 ELSE 0 END) AS "dch_4",
        SUM(CASE WHEN dch_severe_type_id IS NULL THEN 1 ELSE 0 END) AS "dch_null"
        FROM (SELECT i.an,i.regdate,i.dchdate,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.prediag,
        i.pttype,i.provision_dx_icd,i.provision_dx,i.adjrw,i.ipt_admit_type_id,i.ipt_severe_type_id,i.dch_severe_type_id
        FROM ipt i
        LEFT JOIN patient p ON p.hn=i.hn
        WHERE i.ward IN ("01")
        AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'") AS a
        GROUP BY MONTH(dchdate)
        ORDER BY YEAR(dchdate),MONTH(dchdate)');

$severe_type_m = array_column($severe_type_month,'month');
$severe_type_1 = array_column($severe_type_month,'1');
$severe_type_2 = array_column($severe_type_month,'2');
$severe_type_3 = array_column($severe_type_month,'3');
$severe_type_4 = array_column($severe_type_month,'4');
$severe_type_null = array_column($severe_type_month,'null');
$dch_severe_type_1 = array_column($severe_type_month,'dch_1');
$dch_severe_type_2 = array_column($severe_type_month,'dch_2');
$dch_severe_type_3 = array_column($severe_type_month,'dch_3');
$dch_severe_type_4 = array_column($severe_type_month,'dch_4');
$dch_severe_type_null = array_column($severe_type_month,'dch_null');

$severe_type_year = DB::connection('hosxp')->select('
        SELECT IF(MONTH(dchdate)>=10,YEAR(dchdate)+544 ,YEAR(dchdate)+543) AS budget_year,
        SUM(CASE WHEN ipt_severe_type_id = 1 THEN 1 ELSE 0 END) AS "1",
        SUM(CASE WHEN ipt_severe_type_id = 2 THEN 1 ELSE 0 END) AS "2",
        SUM(CASE WHEN ipt_severe_type_id = 3 THEN 1 ELSE 0 END) AS "3",
        SUM(CASE WHEN ipt_severe_type_id = 4 THEN 1 ELSE 0 END) AS "4",
        SUM(CASE WHEN ipt_severe_type_id IS NULL THEN 1 ELSE 0 END) AS "null",
        SUM(CASE WHEN dch_severe_type_id = 1 THEN 1 ELSE 0 END) AS "dch_1",
        SUM(CASE WHEN dch_severe_type_id = 2 THEN 1 ELSE 0 END) AS "dch_2",
        SUM(CASE WHEN dch_severe_type_id = 3 THEN 1 ELSE 0 END) AS "dch_3",
        SUM(CASE WHEN dch_severe_type_id = 4 THEN 1 ELSE 0 END) AS "dch_4",
        SUM(CASE WHEN dch_severe_type_id IS NULL THEN 1 ELSE 0 END) AS "dch_null"
        FROM (SELECT i.an,i.regdate,i.dchdate,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.prediag,
        i.pttype,i.provision_dx_icd,i.provision_dx,i.adjrw,i.ipt_admit_type_id,i.ipt_severe_type_id,i.dch_severe_type_id
        FROM ipt i
        LEFT JOIN patient p ON p.hn=i.hn
        WHERE i.ward IN ("01")
        AND i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'") AS a
        GROUP BY budget_year');

$severe_type_y = array_column($severe_type_year,'budget_year');
$severe_type_1_y = array_column($severe_type_year,'1');
$severe_type_2_y = array_column($severe_type_year,'2');
$severe_type_3_y = array_column($severe_type_year,'3');
$severe_type_4_y = array_column($severe_type_year,'4');
$severe_type_null_y = array_column($severe_type_year,'null');
$dch_severe_type_1_y = array_column($severe_type_year,'dch_1');
$dch_severe_type_2_y = array_column($severe_type_year,'dch_2');
$dch_severe_type_3_y = array_column($severe_type_year,'dch_3');
$dch_severe_type_4_y = array_column($severe_type_year,'dch_4');
$dch_severe_type_null_y = array_column($severe_type_year,'dch_null');

$severe_type_list_null = DB::connection('hosxp')->select('
        SELECT i.an,i.regdate,i.regtime,i.dchdate,i.dchtime,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
        i.prediag,i.pttype,i.provision_dx_icd,i.provision_dx,a.pdx,i.adjrw,i.ipt_severe_type_id,i.dch_severe_type_id
        FROM ipt i
        LEFT JOIN an_stat a ON a.an=i.an
        LEFT JOIN patient p ON p.hn=i.hn
        WHERE i.ward IN ("01")
        AND i.dchdate BETWEEN  "'.$start_date.'" AND "'.$end_date.'"
        AND i.ipt_severe_type_id IS NULL');
$dch_severe_type_list_null = DB::connection('hosxp')->select('
        SELECT i.an,i.regdate,i.regtime,i.dchdate,i.dchtime,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
        i.prediag,i.pttype,i.provision_dx_icd,i.provision_dx,a.pdx,i.adjrw,i.ipt_severe_type_id,i.dch_severe_type_id
        FROM ipt i
        LEFT JOIN an_stat a ON a.an=i.an
        LEFT JOIN patient p ON p.hn=i.hn
        WHERE i.ward IN ("01")
        AND i.dchdate BETWEEN  "'.$start_date.'" AND "'.$end_date.'"
        AND i.dch_severe_type_id IS NULL');

return view('service_ipd.severe_type_ipd',compact('budget_year_select','budget_year','severe_type_m','severe_type_1',
        'severe_type_2','severe_type_3','severe_type_4','severe_type_null','dch_severe_type_1','dch_severe_type_2',
        'dch_severe_type_3','dch_severe_type_4','dch_severe_type_null','severe_type_y','severe_type_1_y',
        'severe_type_2_y','severe_type_3_y','severe_type_4_y','severe_type_null_y','dch_severe_type_1_y',
        'dch_severe_type_2_y','dch_severe_type_3_y','dch_severe_type_4_y','dch_severe_type_null_y',
        'severe_type_list_null','dch_severe_type_list_null'));
}

//Create ระดับความรุนแรง vip
public function severe_type_vip(Request $request)
{
$budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
$budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
$budget_year = $request->budget_year;
if($budget_year == '' || $budget_year == null)
{$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
$start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
$start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
$end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

$severe_type_month = DB::connection('hosxp')->select('
        SELECT CASE WHEN MONTH(dchdate)="10" THEN CONCAT("ต.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="11" THEN CONCAT("พ.ย.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="12" THEN CONCAT("ธ.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="1" THEN CONCAT("ม.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="2" THEN CONCAT("ก.พ.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="3" THEN CONCAT("มี.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="4" THEN CONCAT("เม.ย.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="5" THEN CONCAT("พ.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="6" THEN CONCAT("มิ.ย.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="7" THEN CONCAT("ก.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="8" THEN CONCAT("ส.ค.",SPACE(1),YEAR(dchdate)+543)
        WHEN MONTH(dchdate)="9" THEN CONCAT("ก.ย.",SPACE(1),YEAR(dchdate)+543)
        END AS "month",
        SUM(CASE WHEN ipt_severe_type_id = 1 THEN 1 ELSE 0 END) AS "1",
        SUM(CASE WHEN ipt_severe_type_id = 2 THEN 1 ELSE 0 END) AS "2",
        SUM(CASE WHEN ipt_severe_type_id = 3 THEN 1 ELSE 0 END) AS "3",
        SUM(CASE WHEN ipt_severe_type_id = 4 THEN 1 ELSE 0 END) AS "4",
        SUM(CASE WHEN ipt_severe_type_id IS NULL THEN 1 ELSE 0 END) AS "null",
        SUM(CASE WHEN dch_severe_type_id = 1 THEN 1 ELSE 0 END) AS "dch_1",
        SUM(CASE WHEN dch_severe_type_id = 2 THEN 1 ELSE 0 END) AS "dch_2",
        SUM(CASE WHEN dch_severe_type_id = 3 THEN 1 ELSE 0 END) AS "dch_3",
        SUM(CASE WHEN dch_severe_type_id = 4 THEN 1 ELSE 0 END) AS "dch_4",
        SUM(CASE WHEN dch_severe_type_id IS NULL THEN 1 ELSE 0 END) AS "dch_null"
        FROM (SELECT i.an,i.regdate,i.dchdate,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.prediag,
        i.pttype,i.provision_dx_icd,i.provision_dx,i.adjrw,i.ipt_admit_type_id,i.ipt_severe_type_id,i.dch_severe_type_id
        FROM ipt i
        LEFT JOIN patient p ON p.hn=i.hn
        WHERE i.ward IN ("03","08")
        AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'") AS a
        GROUP BY MONTH(dchdate)
        ORDER BY YEAR(dchdate),MONTH(dchdate)');

$severe_type_m = array_column($severe_type_month,'month');
$severe_type_1 = array_column($severe_type_month,'1');
$severe_type_2 = array_column($severe_type_month,'2');
$severe_type_3 = array_column($severe_type_month,'3');
$severe_type_4 = array_column($severe_type_month,'4');
$severe_type_null = array_column($severe_type_month,'null');
$dch_severe_type_1 = array_column($severe_type_month,'dch_1');
$dch_severe_type_2 = array_column($severe_type_month,'dch_2');
$dch_severe_type_3 = array_column($severe_type_month,'dch_3');
$dch_severe_type_4 = array_column($severe_type_month,'dch_4');
$dch_severe_type_null = array_column($severe_type_month,'dch_null');

$severe_type_year = DB::connection('hosxp')->select('
        SELECT IF(MONTH(dchdate)>=10,YEAR(dchdate)+544 ,YEAR(dchdate)+543) AS budget_year,
        SUM(CASE WHEN ipt_severe_type_id = 1 THEN 1 ELSE 0 END) AS "1",
        SUM(CASE WHEN ipt_severe_type_id = 2 THEN 1 ELSE 0 END) AS "2",
        SUM(CASE WHEN ipt_severe_type_id = 3 THEN 1 ELSE 0 END) AS "3",
        SUM(CASE WHEN ipt_severe_type_id = 4 THEN 1 ELSE 0 END) AS "4",
        SUM(CASE WHEN ipt_severe_type_id IS NULL THEN 1 ELSE 0 END) AS "null",
        SUM(CASE WHEN dch_severe_type_id = 1 THEN 1 ELSE 0 END) AS "dch_1",
        SUM(CASE WHEN dch_severe_type_id = 2 THEN 1 ELSE 0 END) AS "dch_2",
        SUM(CASE WHEN dch_severe_type_id = 3 THEN 1 ELSE 0 END) AS "dch_3",
        SUM(CASE WHEN dch_severe_type_id = 4 THEN 1 ELSE 0 END) AS "dch_4",
        SUM(CASE WHEN dch_severe_type_id IS NULL THEN 1 ELSE 0 END) AS "dch_null"
        FROM (SELECT i.an,i.regdate,i.dchdate,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,i.prediag,
        i.pttype,i.provision_dx_icd,i.provision_dx,i.adjrw,i.ipt_admit_type_id,i.ipt_severe_type_id,i.dch_severe_type_id
        FROM ipt i
        LEFT JOIN patient p ON p.hn=i.hn
        WHERE i.ward IN ("03","08")
        AND i.dchdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'") AS a
        GROUP BY budget_year');

$severe_type_y = array_column($severe_type_year,'budget_year');
$severe_type_1_y = array_column($severe_type_year,'1');
$severe_type_2_y = array_column($severe_type_year,'2');
$severe_type_3_y = array_column($severe_type_year,'3');
$severe_type_4_y = array_column($severe_type_year,'4');
$severe_type_null_y = array_column($severe_type_year,'null');
$dch_severe_type_1_y = array_column($severe_type_year,'dch_1');
$dch_severe_type_2_y = array_column($severe_type_year,'dch_2');
$dch_severe_type_3_y = array_column($severe_type_year,'dch_3');
$dch_severe_type_4_y = array_column($severe_type_year,'dch_4');
$dch_severe_type_null_y = array_column($severe_type_year,'dch_null');

$severe_type_list_null = DB::connection('hosxp')->select('
        SELECT i.an,i.regdate,i.regtime,i.dchdate,i.dchtime,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
        i.prediag,i.pttype,i.provision_dx_icd,i.provision_dx,a.pdx,i.adjrw,i.ipt_severe_type_id,i.dch_severe_type_id
        FROM ipt i
        LEFT JOIN an_stat a ON a.an=i.an
        LEFT JOIN patient p ON p.hn=i.hn
        WHERE i.ward IN ("03","08")
        AND i.dchdate BETWEEN  "'.$start_date.'" AND "'.$end_date.'"
        AND i.ipt_severe_type_id IS NULL');
$dch_severe_type_list_null = DB::connection('hosxp')->select('
        SELECT i.an,i.regdate,i.regtime,i.dchdate,i.dchtime,i.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
        i.prediag,i.pttype,i.provision_dx_icd,i.provision_dx,a.pdx,i.adjrw,i.ipt_severe_type_id,i.dch_severe_type_id
        FROM ipt i
        LEFT JOIN an_stat a ON a.an=i.an
        LEFT JOIN patient p ON p.hn=i.hn
        WHERE i.ward IN ("03","08")
        AND i.dchdate BETWEEN  "'.$start_date.'" AND "'.$end_date.'"
        AND i.dch_severe_type_id IS NULL');

return view('service_ipd.severe_type_vip',compact('budget_year_select','budget_year','severe_type_m','severe_type_1',
        'severe_type_2','severe_type_3','severe_type_4','severe_type_null','dch_severe_type_1','dch_severe_type_2',
        'dch_severe_type_3','dch_severe_type_4','dch_severe_type_null','severe_type_y','severe_type_1_y',
        'severe_type_2_y','severe_type_3_y','severe_type_4_y','severe_type_null_y','dch_severe_type_1_y',
        'dch_severe_type_2_y','dch_severe_type_3_y','dch_severe_type_4_y','dch_severe_type_null_y',
        'severe_type_list_null','dch_severe_type_list_null'));
}

}
