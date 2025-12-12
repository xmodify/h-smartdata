<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PDF;

class Service_DrugController extends Controller
{
//Check Login
      public function __construct()

{
      $this->middleware('auth');
}

//Create index
      public function index()
{
      return view('service_drug.index');
}
################################################################################################################
//Create prescription
public function prescription(Request $request)
{
        $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
        $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
        $budget_year = $request->budget_year;
        if($budget_year == '' || $budget_year == null)
        {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
        $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
        $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
        $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

        $prescription_opd = DB::connection('hosxp')->select('
                SELECT CASE WHEN MONTH(rxdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(rxdate)+543,2))
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
                END AS "month",COUNT(DISTINCT vn) AS opd,COUNT(icode) AS drugopd,
                ROUND(sum(qty*cost),2) as sum_cost,ROUND(sum(sum_price),2) as sum_price
                FROM opitemrece WHERE rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND icode LIKE "1%" AND (vn IS NOT NULL OR vn <>"")
                GROUP BY MONTH(rxdate)  ORDER BY YEAR(rxdate) ,MONTH(rxdate)');
        $prescription_ipd = DB::connection('hosxp')->select('
                SELECT CASE WHEN MONTH(rxdate)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(rxdate)+543,2))
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
                END AS "month",COUNT(DISTINCT order_no) AS ipd,COUNT(icode) AS drugipd,
                ROUND(sum(qty*cost),2) as sum_cost,ROUND(sum(sum_price),2) as sum_price
                FROM opitemrece WHERE rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND icode LIKE "1%" AND (an IS NOT NULL OR an <>"")
                GROUP BY MONTH(rxdate)  ORDER BY YEAR(rxdate) ,MONTH(rxdate)');


        return view('service_drug.prescription',compact('budget_year_select','budget_year','prescription_opd','prescription_ipd'));
}
################################################################################################################
//Create value
public function value(Request $request)
{
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
    $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
    $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
    $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

    $value_opd = DB::connection('hosxp')->select('select IF(MONTH(rxdate)>=10,YEAR(rxdate)+544 ,YEAR(rxdate)+543) AS year_bud,
            ROUND(sum(qty*cost),2) as sum_cost,ROUND(sum(sum_price),2) as sum_price
            FROM opitemrece
            WHERE rxdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND icode <> "1630746" AND icode LIKE "1%" AND vn IS NOT NULL
            GROUP BY year_bud
            ORDER BY year_bud');
    $year = array_column($value_opd,'year_bud');
    $value_opd_cost = array_column($value_opd,'sum_cost');
    $value_opd_price = array_column($value_opd,'sum_price');

    $value_ipd = DB::connection('hosxp')->select('select
            IF(MONTH(o.rxdate)>=10,YEAR(o.rxdate)+544 ,YEAR(o.rxdate)+543) AS year_bud,
            ROUND(sum(o.qty*o.cost),2) as sum_cost,ROUND(sum(o.sum_price),2) as sum_price
            FROM opitemrece o
            LEFT JOIN an_stat a ON a.an=o.an
            WHERE o.rxdate BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
            AND o.icode <> "1630746" AND o.icode LIKE "1%" AND o.an IS NOT NULL
            AND a.pdx NOT IN ("Z290","Z208")
            GROUP BY year_bud
            ORDER BY year_bud');
    $value_ipd_cost = array_column($value_ipd,'sum_cost');
    $value_ipd_price = array_column($value_ipd,'sum_price');

    return view('service_drug.value',compact('budget_year_select','budget_year','year','value_opd_cost',
            'value_opd_price','value_ipd_cost','value_ipd_price'));
}
################################################################################################################
//Create value_diag_opd
public function value_diag_opd(Request $request)
{
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
    $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
    $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
    $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

    $value_diag_opd = DB::connection('hosxp')->select('select CONCAT("[",a.pdx,"] " ,a.name) as name,
            count(DISTINCT a.hn) as hn , count(DISTINCT a.vn) as visit , sum(b.sum_cost) AS sum_cost,sum(b.sum_price) AS sum_price
            FROM
            (SELECT v.vstdate,v.hn,v.vn,v.sex,v.pdx,i.`name`
            FROM vn_stat v
            LEFT JOIN icd101 i ON i.code=v.pdx
            WHERE v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND (v.pdx<>"" OR v.pdx IS NOT NULL) and v.pdx NOT LIKE "z%" and v.pdx NOT IN ("u119")) AS a
            LEFT JOIN
            (SELECT vn,sum(qty*cost) as sum_cost,sum(sum_price) as sum_price
            FROM opitemrece
            WHERE rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND icode <>"1630746" AND icode LIKE "1%" AND vn IS NOT NULL
            GROUP BY vn) AS b ON a.vn=b.vn
            GROUP BY a.pdx
            ORDER BY visit desc limit 20');

      return view('service_drug.value_diag_opd',compact('value_diag_opd','budget_year_select','budget_year','start_date','end_date'));
}
########################################################################################################################
//Create value_diag_ipd
public function value_diag_ipd(Request $request)
{
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}
    $start_date_y = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year-4)->value('DATE_BEGIN');
    $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
    $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');

    $value_diag_ipd = DB::connection('hosxp')->select('select CONCAT("[",a.pdx,"] " ,a.name) as name,
            count(DISTINCT a.hn) as hn ,count(DISTINCT a.an) as visit , sum(b.sum_cost) AS sum_cost,sum(b.sum_price) AS sum_price
            FROM
            (SELECT a.dchdate,a.hn,a.an,a.sex,a.pdx,i.`name`
            FROM an_stat a
            LEFT JOIN icd101 i ON i.code=a.pdx
            WHERE a.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND a.ward NOT IN ("06","07")
            AND (a.pdx <> "" AND a.pdx IS NOT NULL) and a.pdx NOT LIKE "z%" and a.pdx NOT IN ("u119")) AS a
            LEFT JOIN
            (SELECT an,sum(qty*cost) as sum_cost,sum(sum_price) as sum_price
            FROM opitemrece
            WHERE rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND icode <>"1630746" AND icode LIKE "1%" AND an IS NOT NULL AND an <>""
            GROUP BY an) AS b ON a.an=b.an
            GROUP BY a.pdx
            ORDER BY visit desc limit 20');

      return view('service_drug.value_diag_ipd',compact('value_diag_ipd','budget_year_select','budget_year','start_date','end_date'));
}
################################################################################################################
//Create มูลค่าการใช้ยาสมุนไพร
        public function herb(Request $request) 
        {
                $start_date = $request->start_date ?: Session::get('start_date') ?: date('Y-m-d');
                $end_date = $request->end_date ?: Session::get('end_date') ?: date('Y-m-d');

                $opd = collect(DB::connection('hosxp')->select('
                        SELECT o.icode,
                        CONCAT(d.`name`,SPACE(1),d.strength) AS `name`,
                        d.generic_name,
                        COUNT(DISTINCT o.hn) AS total_hn ,
                        COUNT(DISTINCT o.vn) AS total_visit ,
                        SUM(qty) AS total_qty,
                        SUM(qty*cost) AS total_cost,
                        SUM(sum_price) AS total_price,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN 1 ELSE 0 END) AS ucs_visit,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty ELSE 0 END) AS ucs_qty,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty * cost ELSE 0 END) AS ucs_cost,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN sum_price ELSE 0 END) AS ucs_price,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN 1 ELSE 0 END) AS ofc_visit,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty ELSE 0 END) AS ofc_qty,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty * cost ELSE 0 END) AS ofc_cost,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN sum_price ELSE 0 END) AS ofc_price,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN 1 ELSE 0 END) AS lgo_visit,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty ELSE 0 END) AS lgo_qty,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty * cost ELSE 0 END) AS lgo_cost,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN sum_price ELSE 0 END) AS lgo_price,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN 1 ELSE 0 END) AS sss_visit,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty ELSE 0 END) AS sss_qty,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty * cost ELSE 0 END) AS sss_cost,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN sum_price ELSE 0 END) AS sss_price,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN 1 ELSE 0 END) AS other_visit,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty ELSE 0 END) AS other_qty,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty * cost ELSE 0 END) AS other_cost,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN sum_price ELSE 0 END) AS other_price
                        FROM opitemrece o                        
                        LEFT JOIN pttype p ON p.pttype=o.pttype
                        LEFT JOIN drugitems d ON d.icode=o.icode
                        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=1
                        LEFT JOIN drugitems_property_list dpl ON dpl.icode= o.icode AND dpl.drugitems_property_id = 1								
                        WHERE o.rxdate BETWEEN ? AND ?
                        AND (o.vn IS NOT NULL OR o.vn <> "")
                        AND (d2.ref_code LIKE "4%" OR dpl.drugitems_property_id="1")
                        GROUP BY o.icode
                        ORDER BY d.`name`',[$start_date, $end_date]));

                $ipd = collect(DB::connection('hosxp')->select('
                        SELECT o.icode,
                        CONCAT(d.`name`,SPACE(1),d.strength) AS `name`,
                        d.generic_name,
                        COUNT(DISTINCT o.hn) AS total_hn ,
                        COUNT(DISTINCT o.an) AS total_an ,
                        SUM(qty) AS total_qty,
                        SUM(qty*cost) AS total_cost,
                        SUM(sum_price) AS total_price,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN 1 ELSE 0 END) AS ucs_an,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty ELSE 0 END) AS ucs_qty,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty * cost ELSE 0 END) AS ucs_cost,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN sum_price ELSE 0 END) AS ucs_price,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN 1 ELSE 0 END) AS ofc_an,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty ELSE 0 END) AS ofc_qty,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty * cost ELSE 0 END) AS ofc_cost,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN sum_price ELSE 0 END) AS ofc_price,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN 1 ELSE 0 END) AS lgo_an,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty ELSE 0 END) AS lgo_qty,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty * cost ELSE 0 END) AS lgo_cost,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN sum_price ELSE 0 END) AS lgo_price,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN 1 ELSE 0 END) AS sss_an,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty ELSE 0 END) AS sss_qty,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty * cost ELSE 0 END) AS sss_cost,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN sum_price ELSE 0 END) AS sss_price,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN 1 ELSE 0 END) AS other_an,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty ELSE 0 END) AS other_qty,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty * cost ELSE 0 END) AS other_cost,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN sum_price ELSE 0 END) AS other_price
                        FROM opitemrece o                        
                        LEFT JOIN pttype p ON p.pttype=o.pttype
                        LEFT JOIN drugitems d ON d.icode=o.icode
                        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=1
                        LEFT JOIN drugitems_property_list dpl ON dpl.icode= o.icode AND dpl.drugitems_property_id = 1								
                        WHERE o.rxdate BETWEEN ? AND ?
                        AND (o.an IS NOT NULL OR o.an <> "")
                        AND (d2.ref_code LIKE "4%" OR dpl.drugitems_property_id="1")	
                        GROUP BY o.icode
                        ORDER BY d.`name`',[$start_date, $end_date]));

        return view('service_drug.herb',compact('opd','ipd','start_date','end_date'));
        }
################################################################################################################
//Create การใช้ยาสมุนไพร 9 รายการ
public function herb9(Request $request) 
        {
                $start_date = $request->start_date ?: Session::get('start_date') ?: date('Y-m-d');
                $end_date = $request->end_date ?: Session::get('end_date') ?: date('Y-m-d');

                $opd = collect(DB::connection('hosxp')->select('
                        SELECT o.icode,
                        CONCAT(d.`name`,SPACE(1),d.strength) AS `name`,
                        d.generic_name,
                        COUNT(DISTINCT o.hn) AS total_hn ,
                        COUNT(DISTINCT o.vn) AS total_visit ,
                        SUM(qty) AS total_qty,
                        SUM(qty*cost) AS total_cost,
                        SUM(sum_price) AS total_price,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN 1 ELSE 0 END) AS ucs_visit,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty ELSE 0 END) AS ucs_qty,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty * cost ELSE 0 END) AS ucs_cost,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN sum_price ELSE 0 END) AS ucs_price,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN 1 ELSE 0 END) AS ofc_visit,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty ELSE 0 END) AS ofc_qty,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty * cost ELSE 0 END) AS ofc_cost,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN sum_price ELSE 0 END) AS ofc_price,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN 1 ELSE 0 END) AS lgo_visit,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty ELSE 0 END) AS lgo_qty,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty * cost ELSE 0 END) AS lgo_cost,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN sum_price ELSE 0 END) AS lgo_price,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN 1 ELSE 0 END) AS sss_visit,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty ELSE 0 END) AS sss_qty,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty * cost ELSE 0 END) AS sss_cost,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN sum_price ELSE 0 END) AS sss_price,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN 1 ELSE 0 END) AS other_visit,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty ELSE 0 END) AS other_qty,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty * cost ELSE 0 END) AS other_cost,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN sum_price ELSE 0 END) AS other_price
                        FROM opitemrece o
                        INNER JOIN drugitems_property_list dpl ON dpl.icode= o.icode 
                                AND dpl.drugitems_property_id = 13
                        LEFT JOIN pttype p ON p.pttype=o.pttype
                        LEFT JOIN drugitems d ON d.icode=o.icode								
                        WHERE o.rxdate BETWEEN ? AND ?
                        AND (o.vn IS NOT NULL OR o.vn <> "")	
                        GROUP BY o.icode
                        ORDER BY d.`name`',[$start_date, $end_date]));

                $ipd = collect(DB::connection('hosxp')->select('
                        SELECT o.icode,
                        CONCAT(d.`name`,SPACE(1),d.strength) AS `name`,
                        d.generic_name,
                        COUNT(DISTINCT o.hn) AS total_hn ,
                        COUNT(DISTINCT o.an) AS total_an ,
                        SUM(qty) AS total_qty,
                        SUM(qty*cost) AS total_cost,
                        SUM(sum_price) AS total_price,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN 1 ELSE 0 END) AS ucs_an,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty ELSE 0 END) AS ucs_qty,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty * cost ELSE 0 END) AS ucs_cost,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN sum_price ELSE 0 END) AS ucs_price,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN 1 ELSE 0 END) AS ofc_an,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty ELSE 0 END) AS ofc_qty,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty * cost ELSE 0 END) AS ofc_cost,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN sum_price ELSE 0 END) AS ofc_price,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN 1 ELSE 0 END) AS lgo_an,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty ELSE 0 END) AS lgo_qty,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty * cost ELSE 0 END) AS lgo_cost,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN sum_price ELSE 0 END) AS lgo_price,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN 1 ELSE 0 END) AS sss_an,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty ELSE 0 END) AS sss_qty,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty * cost ELSE 0 END) AS sss_cost,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN sum_price ELSE 0 END) AS sss_price,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN 1 ELSE 0 END) AS other_an,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty ELSE 0 END) AS other_qty,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty * cost ELSE 0 END) AS other_cost,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN sum_price ELSE 0 END) AS other_price
                        FROM opitemrece o
                        INNER JOIN drugitems_property_list dpl ON dpl.icode= o.icode 
                                AND dpl.drugitems_property_id = 13
                        LEFT JOIN pttype p ON p.pttype=o.pttype
                        LEFT JOIN drugitems d ON d.icode=o.icode								
                        WHERE o.rxdate BETWEEN ? AND ?
                        AND (o.an IS NOT NULL OR o.an <> "")	
                        GROUP BY o.icode
                        ORDER BY d.`name`',[$start_date, $end_date]));

        return view('service_drug.herb9',compact('opd','ipd','start_date','end_date'));
        }
################################################################################################################
//Create การใช้ยาสมุนไพร 32 รายการ
        public function herb32(Request $request) 
        {
                $start_date = $request->start_date ?: Session::get('start_date') ?: date('Y-m-d');
                $end_date = $request->end_date ?: Session::get('end_date') ?: date('Y-m-d');

                $opd = collect(DB::connection('hosxp')->select('
                        SELECT o.icode,
                        CONCAT(d.`name`,SPACE(1),d.strength) AS `name`,
                        d.generic_name,
                        COUNT(DISTINCT o.hn) AS total_hn ,
                        COUNT(DISTINCT o.vn) AS total_visit ,
                        SUM(qty) AS total_qty,
                        SUM(qty*cost) AS total_cost,
                        SUM(sum_price) AS total_price,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN 1 ELSE 0 END) AS ucs_visit,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty ELSE 0 END) AS ucs_qty,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty * cost ELSE 0 END) AS ucs_cost,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN sum_price ELSE 0 END) AS ucs_price,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN 1 ELSE 0 END) AS ofc_visit,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty ELSE 0 END) AS ofc_qty,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty * cost ELSE 0 END) AS ofc_cost,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN sum_price ELSE 0 END) AS ofc_price,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN 1 ELSE 0 END) AS lgo_visit,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty ELSE 0 END) AS lgo_qty,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty * cost ELSE 0 END) AS lgo_cost,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN sum_price ELSE 0 END) AS lgo_price,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN 1 ELSE 0 END) AS sss_visit,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty ELSE 0 END) AS sss_qty,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty * cost ELSE 0 END) AS sss_cost,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN sum_price ELSE 0 END) AS sss_price,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN 1 ELSE 0 END) AS other_visit,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty ELSE 0 END) AS other_qty,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty * cost ELSE 0 END) AS other_cost,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN sum_price ELSE 0 END) AS other_price
                        FROM opitemrece o
                        INNER JOIN drugitems_property_list dpl ON dpl.icode= o.icode 
                                AND dpl.drugitems_property_id = 12
                        LEFT JOIN pttype p ON p.pttype=o.pttype
                        LEFT JOIN drugitems d ON d.icode=o.icode								
                        WHERE o.rxdate BETWEEN ? AND ?
                        AND (o.vn IS NOT NULL OR o.vn <> "")	
                        GROUP BY o.icode
                        ORDER BY d.`name`',[$start_date, $end_date]));

                $ipd = collect(DB::connection('hosxp')->select('
                        SELECT o.icode,
                        CONCAT(d.`name`,SPACE(1),d.strength) AS `name`,
                        d.generic_name,
                        COUNT(DISTINCT o.hn) AS total_hn ,
                        COUNT(DISTINCT o.an) AS total_an ,
                        SUM(qty) AS total_qty,
                        SUM(qty*cost) AS total_cost,
                        SUM(sum_price) AS total_price,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN 1 ELSE 0 END) AS ucs_an,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty ELSE 0 END) AS ucs_qty,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty * cost ELSE 0 END) AS ucs_cost,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN sum_price ELSE 0 END) AS ucs_price,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN 1 ELSE 0 END) AS ofc_an,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty ELSE 0 END) AS ofc_qty,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty * cost ELSE 0 END) AS ofc_cost,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN sum_price ELSE 0 END) AS ofc_price,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN 1 ELSE 0 END) AS lgo_an,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty ELSE 0 END) AS lgo_qty,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty * cost ELSE 0 END) AS lgo_cost,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN sum_price ELSE 0 END) AS lgo_price,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN 1 ELSE 0 END) AS sss_an,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty ELSE 0 END) AS sss_qty,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty * cost ELSE 0 END) AS sss_cost,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN sum_price ELSE 0 END) AS sss_price,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN 1 ELSE 0 END) AS other_an,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty ELSE 0 END) AS other_qty,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty * cost ELSE 0 END) AS other_cost,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN sum_price ELSE 0 END) AS other_price
                        FROM opitemrece o
                        INNER JOIN drugitems_property_list dpl ON dpl.icode= o.icode 
                                AND dpl.drugitems_property_id = 12
                        LEFT JOIN pttype p ON p.pttype=o.pttype
                        LEFT JOIN drugitems d ON d.icode=o.icode								
                        WHERE o.rxdate BETWEEN ? AND ?
                        AND (o.an IS NOT NULL OR o.an <> "")	
                        GROUP BY o.icode
                        ORDER BY d.`name`',[$start_date, $end_date]));

        return view('service_drug.herb32',compact('opd','ipd','start_date','end_date'));
        }

################################################################################################################
//Create esrd
        public function esrd(Request $request) 
        {
                $start_date = $request->start_date ?: Session::get('start_date') ?: date('Y-m-d');
                $end_date = $request->end_date ?: Session::get('end_date') ?: date('Y-m-d');

                $opd = collect(DB::connection('hosxp')->select('
                        SELECT o.icode,
                        CONCAT(d.`name`,SPACE(1),d.strength) AS `name`,
                        d.generic_name,
                        COUNT(DISTINCT o.hn) AS total_hn ,
                        COUNT(DISTINCT o.vn) AS total_visit ,
                        SUM(qty) AS total_qty,
                        SUM(qty*cost) AS total_cost,
                        SUM(sum_price) AS total_price,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN 1 ELSE 0 END) AS ucs_visit,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty ELSE 0 END) AS ucs_qty,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty * cost ELSE 0 END) AS ucs_cost,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN sum_price ELSE 0 END) AS ucs_price,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN 1 ELSE 0 END) AS ofc_visit,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty ELSE 0 END) AS ofc_qty,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty * cost ELSE 0 END) AS ofc_cost,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN sum_price ELSE 0 END) AS ofc_price,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN 1 ELSE 0 END) AS lgo_visit,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty ELSE 0 END) AS lgo_qty,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty * cost ELSE 0 END) AS lgo_cost,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN sum_price ELSE 0 END) AS lgo_price,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN 1 ELSE 0 END) AS sss_visit,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty ELSE 0 END) AS sss_qty,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty * cost ELSE 0 END) AS sss_cost,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN sum_price ELSE 0 END) AS sss_price,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN 1 ELSE 0 END) AS other_visit,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty ELSE 0 END) AS other_qty,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty * cost ELSE 0 END) AS other_cost,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN sum_price ELSE 0 END) AS other_price
                        FROM opitemrece o
                        INNER JOIN drugitems_property_list dpl ON dpl.icode= o.icode 
                                AND dpl.drugitems_property_id = 7
                        LEFT JOIN pttype p ON p.pttype=o.pttype
                        LEFT JOIN drugitems d ON d.icode=o.icode								
                        WHERE o.rxdate BETWEEN ? AND ?
                        AND (o.vn IS NOT NULL OR o.vn <> "")	
                        GROUP BY o.icode
                        ORDER BY d.`name`',[$start_date, $end_date]));

                $ipd = collect(DB::connection('hosxp')->select('
                        SELECT o.icode,
                        CONCAT(d.`name`,SPACE(1),d.strength) AS `name`,
                        d.generic_name,
                        COUNT(DISTINCT o.hn) AS total_hn ,
                        COUNT(DISTINCT o.an) AS total_an ,
                        SUM(qty) AS total_qty,
                        SUM(qty*cost) AS total_cost,
                        SUM(sum_price) AS total_price,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN 1 ELSE 0 END) AS ucs_an,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty ELSE 0 END) AS ucs_qty,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty * cost ELSE 0 END) AS ucs_cost,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN sum_price ELSE 0 END) AS ucs_price,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN 1 ELSE 0 END) AS ofc_an,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty ELSE 0 END) AS ofc_qty,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty * cost ELSE 0 END) AS ofc_cost,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN sum_price ELSE 0 END) AS ofc_price,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN 1 ELSE 0 END) AS lgo_an,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty ELSE 0 END) AS lgo_qty,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty * cost ELSE 0 END) AS lgo_cost,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN sum_price ELSE 0 END) AS lgo_price,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN 1 ELSE 0 END) AS sss_an,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty ELSE 0 END) AS sss_qty,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty * cost ELSE 0 END) AS sss_cost,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN sum_price ELSE 0 END) AS sss_price,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN 1 ELSE 0 END) AS other_an,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty ELSE 0 END) AS other_qty,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty * cost ELSE 0 END) AS other_cost,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN sum_price ELSE 0 END) AS other_price
                        FROM opitemrece o
                        INNER JOIN drugitems_property_list dpl ON dpl.icode= o.icode 
                                AND dpl.drugitems_property_id = 7
                        LEFT JOIN pttype p ON p.pttype=o.pttype
                        LEFT JOIN drugitems d ON d.icode=o.icode								
                        WHERE o.rxdate BETWEEN ? AND ?
                        AND (o.an IS NOT NULL OR o.an <> "")	
                        GROUP BY o.icode
                        ORDER BY d.`name`',[$start_date, $end_date]));

        return view('service_drug.esrd',compact('opd','ipd','start_date','end_date'));
        }
################################################################################################################
//Create hd
        public function hd(Request $request) 
        {
                $start_date = $request->start_date ?: Session::get('start_date') ?: date('Y-m-d');
                $end_date = $request->end_date ?: Session::get('end_date') ?: date('Y-m-d');

                $opd = collect(DB::connection('hosxp')->select('
                        SELECT o.icode,
                        CONCAT(d.`name`,SPACE(1),d.strength) AS `name`,
                        d.generic_name,
                        COUNT(DISTINCT o.hn) AS total_hn ,
                        COUNT(DISTINCT o.vn) AS total_visit ,
                        SUM(qty) AS total_qty,
                        SUM(qty*cost) AS total_cost,
                        SUM(sum_price) AS total_price,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN 1 ELSE 0 END) AS ucs_visit,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty ELSE 0 END) AS ucs_qty,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty * cost ELSE 0 END) AS ucs_cost,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN sum_price ELSE 0 END) AS ucs_price,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN 1 ELSE 0 END) AS ofc_visit,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty ELSE 0 END) AS ofc_qty,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty * cost ELSE 0 END) AS ofc_cost,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN sum_price ELSE 0 END) AS ofc_price,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN 1 ELSE 0 END) AS lgo_visit,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty ELSE 0 END) AS lgo_qty,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty * cost ELSE 0 END) AS lgo_cost,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN sum_price ELSE 0 END) AS lgo_price,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN 1 ELSE 0 END) AS sss_visit,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty ELSE 0 END) AS sss_qty,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty * cost ELSE 0 END) AS sss_cost,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN sum_price ELSE 0 END) AS sss_price,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN 1 ELSE 0 END) AS other_visit,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty ELSE 0 END) AS other_qty,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty * cost ELSE 0 END) AS other_cost,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN sum_price ELSE 0 END) AS other_price
                        FROM opitemrece o
                        INNER JOIN drugitems_property_list dpl ON dpl.icode= o.icode 
                                AND dpl.drugitems_property_id = 8
                        LEFT JOIN pttype p ON p.pttype=o.pttype
                        LEFT JOIN drugitems d ON d.icode=o.icode								
                        WHERE o.rxdate BETWEEN ? AND ?
                        AND (o.vn IS NOT NULL OR o.vn <> "")	
                        GROUP BY o.icode
                        ORDER BY d.`name`',[$start_date, $end_date]));

                $ipd = collect(DB::connection('hosxp')->select('
                        SELECT o.icode,
                        CONCAT(d.`name`,SPACE(1),d.strength) AS `name`,
                        d.generic_name,
                        COUNT(DISTINCT o.hn) AS total_hn ,
                        COUNT(DISTINCT o.an) AS total_an ,
                        SUM(qty) AS total_qty,
                        SUM(qty*cost) AS total_cost,
                        SUM(sum_price) AS total_price,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN 1 ELSE 0 END) AS ucs_an,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty ELSE 0 END) AS ucs_qty,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN qty * cost ELSE 0 END) AS ucs_cost,
                        SUM(CASE WHEN p.hipdata_code = "UCS" THEN sum_price ELSE 0 END) AS ucs_price,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN 1 ELSE 0 END) AS ofc_an,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty ELSE 0 END) AS ofc_qty,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN qty * cost ELSE 0 END) AS ofc_cost,
                        SUM(CASE WHEN p.hipdata_code = "OFC" THEN sum_price ELSE 0 END) AS ofc_price,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN 1 ELSE 0 END) AS lgo_an,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty ELSE 0 END) AS lgo_qty,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN qty * cost ELSE 0 END) AS lgo_cost,
                        SUM(CASE WHEN p.hipdata_code = "LGO" THEN sum_price ELSE 0 END) AS lgo_price,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN 1 ELSE 0 END) AS sss_an,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty ELSE 0 END) AS sss_qty,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN qty * cost ELSE 0 END) AS sss_cost,
                        SUM(CASE WHEN p.hipdata_code IN ("SSS","SSI") THEN sum_price ELSE 0 END) AS sss_price,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN 1 ELSE 0 END) AS other_an,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty ELSE 0 END) AS other_qty,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN qty * cost ELSE 0 END) AS other_cost,
                        SUM(CASE WHEN p.hipdata_code NOT IN ("UCS","OFC","LGO","SSS","SSI") THEN sum_price ELSE 0 END) AS other_price
                        FROM opitemrece o
                        INNER JOIN drugitems_property_list dpl ON dpl.icode= o.icode 
                                AND dpl.drugitems_property_id = 8
                        LEFT JOIN pttype p ON p.pttype=o.pttype
                        LEFT JOIN drugitems d ON d.icode=o.icode								
                        WHERE o.rxdate BETWEEN ? AND ?
                        AND (o.an IS NOT NULL OR o.an <> "")	
                        GROUP BY o.icode
                        ORDER BY d.`name`',[$start_date, $end_date]));

        return view('service_drug.hd',compact('opd','ipd','start_date','end_date'));
        }
################################################################################################################
//Create dmht
public function dmht(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

    $dm_opd = DB::connection('hosxp')->select('select
            p.hipdata_code,o.icode,d.`name`,d.generic_name,d.strength,COUNT(DISTINCT o.hn) AS hn,
            COUNT(DISTINCT o.vn) AS visit,COUNT(DISTINCT o.an) AS an ,SUM(qty) AS qty,SUM(qty*cost) AS cost,SUM(sum_price) AS price
            FROM opitemrece o
            LEFT JOIN pttype p ON p.pttype=o.pttype
            LEFT JOIN drugitems d ON d.icode=o.icode
            LEFT JOIN ovstdiag o1 ON o1.vn=o.vn
            WHERE o.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND o.vn IS NOT NULL AND o1.icd10 BETWEEN "E100" AND  "E149" AND o1.diagtype = "1"
            AND o.icode IN ("1000199","1000200","1000160","1510019","1000258","1510004","1550032","1000189","1610057",
            "1000013","1000120","1000121","1000122","1000123","1510023","1000286","1570010","1000016","1000209",
            "1000312","1000103","1000104","1540019","1000034","1560002","1000195","1000250","1000102","1520023","1500020")
            GROUP BY p.hipdata_code,o.icode
            ORDER BY p.hipdata_code,d.`name`');

    $dm_ipd = DB::connection('hosxp')->select('select
            p.hipdata_code,o.icode,d.`name`,d.generic_name,d.strength,COUNT(DISTINCT o.hn) AS hn,
            COUNT(DISTINCT o.vn) AS visit,COUNT(DISTINCT o.an) AS an ,SUM(qty) AS qty,SUM(qty*cost) AS cost,SUM(sum_price) AS price
            FROM opitemrece o
            LEFT JOIN pttype p ON p.pttype=o.pttype
            LEFT JOIN drugitems d ON d.icode=o.icode
            LEFT JOIN iptdiag i ON i.an=o.an
            WHERE o.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND o.an IS NOT NULL AND i.icd10 BETWEEN "E100" AND "E149" AND i.diagtype = "1"
            AND o.icode IN ("1000199","1000200","1000160","1510019","1000258","1510004","1550032","1000189","1610057",
            "1000013","1000120","1000121","1000122","1000123","1510023","1000286","1570010","1000016","1000209",
            "1000312","1000103","1000104","1540019","1000034","1560002","1000195","1000250","1000102","1520023","1500020")
            GROUP BY p.hipdata_code,o.icode
            ORDER BY p.hipdata_code,d.`name`');

    $ht_opd = DB::connection('hosxp')->select('select
            p.hipdata_code,o.icode,d.`name`,d.generic_name,d.strength,COUNT(DISTINCT o.hn) AS hn,
            COUNT(DISTINCT o.vn) AS visit,COUNT(DISTINCT o.an) AS an ,SUM(qty) AS qty,SUM(qty*cost) AS cost,SUM(sum_price) AS price
            FROM opitemrece o
            LEFT JOIN pttype p ON p.pttype=o.pttype
            LEFT JOIN drugitems d ON d.icode=o.icode
            LEFT JOIN ovstdiag o1 ON o1.vn=o.vn
            WHERE o.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND o.vn IS NOT NULL AND o1.icd10 BETWEEN "i10" AND  "i10" AND o1.diagtype = "1"
            AND o.icode IN ("1000199","1000200","1000160","1510019","1000258","1510004","1550032","1000189","1610057",
            "1000013","1000120","1000121","1000122","1000123","1510023","1000286","1570010","1000016","1000209",
            "1000312","1000103","1000104","1540019","1000034","1560002","1000195","1000250","1000102","1520023","1500020")
            GROUP BY p.hipdata_code,o.icode
            ORDER BY p.hipdata_code,d.`name`');

    $ht_ipd = DB::connection('hosxp')->select('select
            p.hipdata_code,o.icode,d.`name`,d.generic_name,d.strength,COUNT(DISTINCT o.hn) AS hn,
            COUNT(DISTINCT o.vn) AS visit,COUNT(DISTINCT o.an) AS an ,SUM(qty) AS qty,SUM(qty*cost) AS cost,SUM(sum_price) AS price
            FROM opitemrece o
            LEFT JOIN pttype p ON p.pttype=o.pttype
            LEFT JOIN drugitems d ON d.icode=o.icode
            LEFT JOIN iptdiag i ON i.an=o.an
            WHERE o.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND o.an IS NOT NULL AND i.icd10 BETWEEN "i10" AND "i10" AND i.diagtype = "1"
            AND o.icode IN ("1000199","1000200","1000160","1510019","1000258","1510004","1550032","1000189","1610057",
            "1000013","1000120","1000121","1000122","1000123","1510023","1000286","1570010","1000016","1000209",
            "1000312","1000103","1000104","1540019","1000034","1560002","1000195","1000250","1000102","1520023","1500020")
            GROUP BY p.hipdata_code,o.icode
            ORDER BY p.hipdata_code,d.`name`');

    $request->session()->put('dm_opd',$dm_opd);
    $request->session()->put('dm_ipd',$dm_ipd);
    $request->session()->put('ht_opd',$ht_opd);
    $request->session()->put('ht_ipd',$ht_ipd);
    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->save();

    return view('service_drug.dmht',compact('dm_opd','dm_ipd','ht_opd','ht_ipd','start_date','end_date'));
}
//Create esrd_excel
public function dmht_excel()
{
    $dm_opd = Session::get('dm_opd');
    $dm_ipd = Session::get('dm_ipd');
    $ht_opd = Session::get('ht_opd');
    $ht_ipd = Session::get('ht_ipd');
    $start_date = Session::get('start_date');
    $end_date = Session::get('end_date');

    return view('service_drug.dmht_excel',compact('dm_opd','dm_ipd','ht_opd','ht_ipd','start_date','end_date'));
}
################################################################################################################
//Create การประเมินการใช้ยา due
public function due(Request $request)
{
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if($start_date == '' || $end_date == null)
        {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
        if($end_date == '' || $end_date == null)
        {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

        $due_ipd = DB::connection('hosxp')->select('
                SELECT o.vstdate,o.vsttime,o.an,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,
                ROUND(oc.bw) AS bw,CONCAT(d.`name`," ",d.strength) AS drug ,ot.rxdate,ot.rxtime,
                ot.qty,d1.`code` AS drugusage,lh.report_date,lh.report_time,lo.lab_items_name_ref,lo.lab_order_result
                FROM ovst o
                LEFT JOIN patient p ON p.hn=o.hn
                LEFT JOIN vn_stat v ON v.vn=o.vn
                LEFT JOIN opdscreen oc ON oc.vn=o.vn
                LEFT JOIN opitemrece ot ON ot.an=o.an
                LEFT JOIN drugitems d ON d.icode=ot.icode
                LEFT JOIN drugusage d1 ON d1.drugusage=ot.drugusage
                LEFT JOIN lab_head lh ON lh.vn=o.an
                LEFT JOIN lab_order lo ON lo.lab_order_number=lh.lab_order_number             
                WHERE ot.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND ot.icode IN ("1000048","1520046","1610023","1610015","1631004") AND lo.lab_items_code ="4" AND lo.lab_order_result <>""
                GROUP BY o.an,lo.lab_order_number,d1.drugusage
                ORDER BY o.an,lh.report_date,lh.report_time,ot.rxdate,ot.rxtime');

        $request->session()->put('due_ipd',$due_ipd);
        $request->session()->put('start_date',$start_date);
        $request->session()->put('end_date',$end_date);
        $request->session()->save();

      return view('service_drug.due',compact('due_ipd','start_date','end_date'));
}
//Create การประเมินการใช้ยา due_excel
public function due_excel()
{
        $due_ipd = Session::get('due_ipd');
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');

      return view('service_drug.due_excel',compact('due_ipd','start_date','end_date'));
}
################################################################################################################
//Create การประเมินการใช้ยา Metformin
public function Metformin(Request $request)
{
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if($start_date == '' || $end_date == null)
        {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
        if($end_date == '' || $end_date == null)
        {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

        $drug = DB::connection('hosxp')->select('
                SELECT CONCAT(d1.`name`,SPACE(1),d1.strength) AS drug ,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
                v.age_y,o.hn,o1.rxdate,o1.rxtime,d2.`code` AS drugusage,lh.report_date,lh.report_time,
                lo.lab_items_name_ref,lo.lab_order_result
                FROM ovst o
                LEFT JOIN patient p ON p.hn=o.hn
                LEFT JOIN vn_stat v ON v.vn=o.vn
                LEFT JOIN opitemrece o1 ON o1.vn=o.vn
                LEFT JOIN drugitems d1 ON d1.icode=o1.icode
                LEFT JOIN drugusage d2 ON d2.drugusage=o1.drugusage
                LEFT JOIN lab_head lh ON lh.vn=o.vn
                LEFT JOIN lab_order lo ON lo.lab_order_number=lh.lab_order_number 
                WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND o1.icode IN ("1000189","1550032") 
		AND lo.lab_items_code IN ("693") AND lo.lab_order_result <>""
                GROUP BY o.vn,o1.icode
                ORDER BY o1.icode,o.vstdate');

        $drug_ipd = DB::connection('hosxp')->select('
                SELECT CONCAT(d1.`name`,SPACE(1),d1.strength) AS drug ,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
                a.age_y,i.hn,i.an,o1.rxdate,o1.rxtime,d2.`code` AS drugusage,lh.report_date,lh.report_time,
                lo.lab_items_name_ref,lo.lab_order_result
                FROM ipt i
                LEFT JOIN patient p ON p.hn=i.hn
                LEFT JOIN an_stat a ON a.an=i.an
                LEFT JOIN opitemrece o1 ON o1.an=i.an
                LEFT JOIN drugitems d1 ON d1.icode=o1.icode
                LEFT JOIN drugusage d2 ON d2.drugusage=o1.drugusage
                LEFT JOIN lab_head lh ON lh.vn=i.an
                LEFT JOIN lab_order lo ON lo.lab_order_number=lh.lab_order_number 
                WHERE i.regdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND o1.icode IN ("1000189","1550032") 
		AND lo.lab_items_code IN ("693") AND lo.lab_order_result <>""
                GROUP BY i.an,o1.icode,o1.rxdate
                ORDER BY o1.icode,o1.rxdate');

        $request->session()->put('drug',$drug);
        $request->session()->put('drug_ipd',$drug_ipd);
        $request->session()->put('start_date',$start_date);
        $request->session()->put('end_date',$end_date);
        $request->session()->save();

      return view('service_drug.metformin',compact('start_date','end_date','drug','drug_ipd'));
}
public function metformin_excel()
{
        $drug = Session::get('drug');
        $drug_ipd = Session::get('drug_ipd');
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');

      return view('service_drug.metformin_excel',compact('start_date','end_date','drug','drug_ipd'));
}
################################################################################################################
//Create การประเมินการใช้ยา Warfarin
public function warfarin(Request $request)
{
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if($start_date == '' || $end_date == null)
        {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
        if($end_date == '' || $end_date == null)
        {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

        $drug = DB::connection('hosxp')->select('
                SELECT CONCAT(d1.`name`,SPACE(1),d1.strength) AS drug ,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
                v.age_y,o.hn,o1.rxdate,o1.rxtime,d2.`code` AS drugusage,lh.report_date,lh.report_time,
                lo.lab_items_name_ref AS pt,lo.lab_order_result AS pt_result,
		lo2.lab_items_name_ref AS inr,lo2.lab_order_result AS inr_result,
                CASE WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370601") THEN "รพ.สต.หัวตะพาน" 
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370602" AND p.moopart IN ("4","5","6","10","11")) THEN "รพ.สต.โนนหนามแท่ง"   
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370602" AND p.moopart IN ("1","2","3","7","8","9","12")) THEN "รพ.สต.คำพระ"  
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370603" ) THEN "รพ.สต.เค็งใหญ่"  
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370604" ) THEN "รพ.สต.โคกเลาะ"   
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370605" AND p.moopart IN ("2","5","6","7","8","9")) THEN "รพ.สต.ขุมเหล็ก" 
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370605" AND p.moopart IN ("1","3","4","10","11","12")) THEN "รพ.สต.โพนเมืองน้อย" 
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370606"AND p.moopart IN ("1","3","7","10","11","12","13")) THEN "รพ.สต.สร้างถ่อน้อย" 
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370606" AND p.moopart IN ("2","4","5","6","8","9") ) THEN "รพ.สต.นาคู" 
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370607" AND p.moopart IN ("3","6","7","8","9")) THEN "รพ.สต.หนองยอ"  
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370607" AND p.moopart IN ("1","2","4","5","10","11","12")) THEN "รพ.สต.จิกดู่"   
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370608" ) THEN "PCU รัตนวารี" 
                WHEN CONCAT(p.chwpart,p.amppart,p.tmbpart ) NOT IN ("370608","370607","370606","370605","370604","370603","370602","370601")  
                THEN "นอกเขตอำเภอหัวตะพาน" END AS "pcu" 
                FROM ovst o
                LEFT JOIN patient p ON p.hn=o.hn
                LEFT JOIN vn_stat v ON v.vn=o.vn
                LEFT JOIN opitemrece o1 ON o1.vn=o.vn
                LEFT JOIN drugitems d1 ON d1.icode=o1.icode
		LEFT JOIN drugusage d2 ON d2.drugusage=o1.drugusage
                LEFT JOIN lab_head lh ON lh.vn=o.vn
                LEFT JOIN lab_order lo ON lo.lab_order_number=lh.lab_order_number AND lo.lab_items_code ="350" 
		LEFT JOIN lab_head lh2 ON lh2.vn=o.vn
                LEFT JOIN lab_order lo2 ON lo2.lab_order_number=lh2.lab_order_number AND lo2.lab_items_code ="353" 
                WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND o1.icode IN ("1550002","1500035","1500036") 
		AND ((lo.lab_items_code ="350" AND lo.lab_order_result <>"") OR (lo2.lab_items_code ="353" AND lo2.lab_order_result <>""))
                GROUP BY o.vn,o1.icode,lo.lab_items_code
                ORDER BY pcu,o.hn,o.vstdate,o1.icode,lo.lab_items_code');

        $drug_ipd = DB::connection('hosxp')->select('
                SELECT CONCAT(d1.`name`,SPACE(1),d1.strength) AS drug ,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
                a.age_y,i.hn,i.an,o1.rxdate,o1.rxtime,d2.`code` AS drugusage,lh.report_date,lh.report_time,
                lo.lab_items_name_ref AS pt,lo.lab_order_result AS pt_result,
		lo2.lab_items_name_ref AS inr,lo2.lab_order_result AS inr_result,
                CASE WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370601") THEN "รพ.สต.หัวตะพาน" 
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370602" AND p.moopart IN ("4","5","6","10","11")) THEN "รพ.สต.โนนหนามแท่ง"   
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370602" AND p.moopart IN ("1","2","3","7","8","9","12")) THEN "รพ.สต.คำพระ"  
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370603" ) THEN "รพ.สต.เค็งใหญ่"  
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370604" ) THEN "รพ.สต.โคกเลาะ"   
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370605" AND p.moopart IN ("2","5","6","7","8","9")) THEN "รพ.สต.ขุมเหล็ก" 
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370605" AND p.moopart IN ("1","3","4","10","11","12")) THEN "รพ.สต.โพนเมืองน้อย" 
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370606"AND p.moopart IN ("1","3","7","10","11","12","13")) THEN "รพ.สต.สร้างถ่อน้อย" 
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370606" AND p.moopart IN ("2","4","5","6","8","9") ) THEN "รพ.สต.นาคู" 
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370607" AND p.moopart IN ("3","6","7","8","9")) THEN "รพ.สต.หนองยอ"  
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370607" AND p.moopart IN ("1","2","4","5","10","11","12")) THEN "รพ.สต.จิกดู่"   
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370608" ) THEN "PCU รัตนวารี" 
                WHEN CONCAT(p.chwpart,p.amppart,p.tmbpart ) NOT IN ("370608","370607","370606","370605","370604","370603","370602","370601")  
                THEN "นอกเขตอำเภอหัวตะพาน" END AS "pcu" 
                FROM ipt i
                LEFT JOIN patient p ON p.hn=i.hn
                LEFT JOIN an_stat a ON a.an=i.an
                LEFT JOIN opitemrece o1 ON o1.an=i.an
                LEFT JOIN drugitems d1 ON d1.icode=o1.icode
                LEFT JOIN drugusage d2 ON d2.drugusage=o1.drugusage
                LEFT JOIN lab_head lh ON lh.vn=i.an
                LEFT JOIN lab_order lo ON lo.lab_order_number=lh.lab_order_number AND lo.lab_items_code ="350" 
		LEFT JOIN lab_head lh2 ON lh2.vn=i.an
                LEFT JOIN lab_order lo2 ON lo2.lab_order_number=lh2.lab_order_number AND lo2.lab_items_code ="353" 
                WHERE i.regdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND o1.icode IN ("1550002","1500035","1500036") 
		AND ((lo.lab_items_code ="350" AND lo.lab_order_result <>"") OR (lo2.lab_items_code ="353" AND lo2.lab_order_result <>""))
                GROUP BY i.an,o1.icode,o1.rxdate
                ORDER BY pcu,i.an,o1.icode,o1.rxdate');

        $request->session()->put('drug',$drug);
        $request->session()->put('drug_ipd',$drug_ipd);
        $request->session()->put('start_date',$start_date);
        $request->session()->put('end_date',$end_date);
        $request->session()->save();

      return view('service_drug.warfarin',compact('start_date','end_date','drug','drug_ipd'));
}
public function warfarin_excel()
{
        $drug = Session::get('drug');
        $drug_ipd = Session::get('drug_ipd');
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');

      return view('service_drug.warfarin_excel',compact('start_date','end_date','drug','drug_ipd'));
}
###################################################################################################################
//Create การใช้ยาต้านไวรัส 
public function antiviral(Request $request)
{
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if($start_date == '' || $end_date == null)
        {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
        if($end_date == '' || $end_date == null)
        {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

        $drug_opd_sss = DB::connection('hosxp')->select('
                SELECT ot.vn,o.hn,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,
                pt.`name` AS pttype,ot.rxdate,ot.rxtime,CONCAT(d.`name`," ",d.strength) AS drug ,SUM(ot.qty) AS qty
                FROM ovst o
                LEFT JOIN pttype pt ON pt.pttype=o.pttype
                LEFT JOIN patient p ON p.hn=o.hn
                LEFT JOIN vn_stat v ON v.vn=o.vn
                LEFT JOIN opitemrece ot ON ot.vn=o.vn AND (ot.an IS NULL OR ot.an<>"")
                LEFT JOIN drugitems d ON d.icode=ot.icode
                WHERE ot.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND ot.icode IN ("1630768","1630855","1500051","1306001") 		
                AND pt.hipdata_code LIKE "SS%"		
                GROUP BY o.vn,ot.icode
                ORDER BY o.vstdate,o.hn,d.`name`');
                
        $drug_opd_sss_sum = DB::connection('hosxp')->select('
                SELECT CONCAT(d.`name`," ",d.strength) AS drug ,SUM(ot.qty) AS qty
                FROM ovst o
                LEFT JOIN pttype pt ON pt.pttype=o.pttype
                LEFT JOIN opitemrece ot ON ot.vn=o.vn AND (ot.an IS NULL OR ot.an<>"")
                LEFT JOIN drugitems d ON d.icode=ot.icode
                WHERE ot.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND ot.icode IN ("1630768","1630855","1500051","1306001") 		
                AND pt.hipdata_code LIKE "SS%"	
                GROUP BY ot.icode ORDER BY d.`name`');
        $drug_ipd_sss = DB::connection('hosxp')->select('
                SELECT  i.an,i.hn,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.age_y,
                pt.`name` AS pttype,ot.rxdate,ot.rxtime,CONCAT(d.`name`," ",d.strength) AS drug ,SUM(ot.qty) AS qty
                FROM ipt i
                LEFT JOIN pttype pt ON pt.pttype=i.pttype
                LEFT JOIN an_stat a ON a.an=i.an
                LEFT JOIN patient p ON p.hn=i.hn
                LEFT JOIN opitemrece ot ON ot.an=i.an AND (ot.vn IS NULL OR ot.vn<>"")
                LEFT JOIN drugitems d ON d.icode=ot.icode
                WHERE ot.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND ot.icode IN ("1630768","1630855","1500051","1306001") 		
                AND pt.hipdata_code LIKE "SS%"		
                GROUP BY i.an,ot.icode
                ORDER BY i.regdate,i.hn,d.`name`');
        $drug_ipd_sss_sum = DB::connection('hosxp')->select('
                SELECT CONCAT(d.`name`," ",d.strength) AS drug ,SUM(ot.qty) AS qty
                FROM ipt i
                LEFT JOIN pttype pt ON pt.pttype=i.pttype
                LEFT JOIN opitemrece ot ON ot.an=i.an AND (ot.vn IS NULL OR ot.vn<>"")
                LEFT JOIN drugitems d ON d.icode=ot.icode
                WHERE ot.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND ot.icode IN ("1630768","1630855","1500051","1306001") 		
                AND pt.hipdata_code LIKE "SS%"		
                GROUP BY ot.icode ORDER BY d.`name`');

        $request->session()->put('drug_opd_sss',$drug_opd_sss);
        $request->session()->put('drug_opd_sss_sum',$drug_opd_sss_sum);
        $request->session()->put('drug_ipd_sss',$drug_ipd_sss);
        $request->session()->put('drug_ipd_sss_sum',$drug_ipd_sss_sum);
        $request->session()->put('start_date',$start_date);
        $request->session()->put('end_date',$end_date);
        $request->session()->save();

        return view('service_drug.antiviral',compact('drug_opd_sss','drug_ipd_sss','start_date','end_date'));
}

public function antiviral_opd_pdf(Request $request )
  {
    $drug_opd_sss = Session::get('drug_opd_sss');
    $drug_opd_sss_sum = Session::get('drug_opd_sss_sum');
    $start_date = Session::get('start_date');
    $end_date = Session::get('end_date');
    $pdf = PDF::loadView('service_drug.antiviral_opd_pdf', compact('start_date','end_date','drug_opd_sss','drug_opd_sss_sum'))
                ->setPaper('A4', 'portrait');
    return @$pdf->stream();
  }
public function antiviral_ipd_pdf(Request $request )
  {
    $drug_ipd_sss = Session::get('drug_ipd_sss');
    $drug_ipd_sss_sum = Session::get('drug_ipd_sss_sum');
    $start_date = Session::get('start_date');
    $end_date = Session::get('end_date');
    $pdf = PDF::loadView('service_drug.antiviral_ipd_pdf', compact('start_date','end_date','drug_ipd_sss','drug_ipd_sss_sum'))
                ->setPaper('A4', 'portrait');
    return @$pdf->stream();
  }
###################################################################################################################
//Create การใช้ยาที่ er
public function drugtime_s(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("last day"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

    $drugtime_s = DB::connection('hosxp')->select('
        SELECT o.rxdate,o.rxtime,IF(o.an is NULL ,"ER","IPD") as department,d1.`name` AS doctor,o.hn,o.an,o.vn,o.icode,
        CONCAT(d.`name`," ",d.strength) as drug_name,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
        CASE WHEN d.drugaccount ="" THEN "NED" when d.drugaccount <>"" THEN "ED" END AS "acc" ,
        sum(o.qty) AS qty,sum(o.cost*o.qty) AS sum_cost,sum(o.sum_price) AS sum_price       
        FROM opitemrece o INNER JOIN drugitems d ON d.icode=o.icode   
        LEFT OUTER JOIN (SELECT an,rxdate,rxtime,order_no,order_type    
        FROM ipt_order_no WHERE  order_type = "TRx") as i on i.an=o.an 
        LEFT JOIN doctor d1 ON d1.code=o.doctor
        LEFT JOIN patient p ON p.hn=o.hn
        WHERE o.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND  o.rxtime BETWEEN "00:00:00" AND "07:59:59" AND  o.icode LIKE "1%" 
        GROUP BY o.icode ORDER BY o.rxdate,o.rxtime'); 

    $request->session()->put('drugtime_s',$drugtime_s);
    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->save();

      return view('service_drug.drugtime_s',compact('drugtime_s','start_date','end_date'));
}
//Create การใช้ยาเวลา 00.00-08.00excel
public function drugtime_s_excel()
{
    $drugtime_s = Session::get('drugtime_s');
    $start_date = Session::get('start_date');
    $end_date = Session::get('end_date');

      return view('service_drug.drugtime_s_excel',compact('drugtime_s','start_date','end_date'));
}

###################################################################################################################
//ข้อมูลการแพ้ยาแยก รพ.สต.
public function drugallergy(Request $request)
{
        $drugallergy = DB::connection('hosxp')->select('
                SELECT p.cid,p.hn,concat( p.pname, p.fname, " ", p.lname ) AS ptname,o.report_date,p.drugallergy,GROUP_CONCAT(DISTINCT o.symptom) AS symptom ,
                GROUP_CONCAT(DISTINCT o1.seiousness_name) AS seiousness_name,GROUP_CONCAT(DISTINCT o2.result_name) AS result_name ,
		count( o.agent ) AS agent_count,CASE WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370601") THEN "รพ.สต.หัวตะพาน" 
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370602" AND p.moopart IN ("4","5","6","10","11")) THEN "รพ.สต.โนนหนามแท่ง"   
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370602" AND p.moopart IN ("1","2","3","7","8","9","12")) THEN "รพ.สต.คำพระ"  
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370603" ) THEN "รพ.สต.เค็งใหญ่"  
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370604" ) THEN "รพ.สต.โคกเลาะ"   
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370605" AND p.moopart IN ("2","5","6","7","8","9")) THEN "รพ.สต.ขุมเหล็ก" 
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370605" AND p.moopart IN ("1","3","4","10","11","12")) THEN "รพ.สต.โพนเมืองน้อย" 
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370606"AND p.moopart IN ("1","3","7","10","11","12","13")) THEN "รพ.สต.สร้างถ่อน้อย" 
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370606" AND p.moopart IN ("2","4","5","6","8","9") ) THEN "รพ.สต.นาคู" 
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370607" AND p.moopart IN ("3","6","7","8","9")) THEN "รพ.สต.หนองยอ"  
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370607" AND p.moopart IN ("1","2","4","5","10","11","12")) THEN "รพ.สต.จิกดู่"   
                WHEN (CONCAT(p.chwpart,p.amppart,p.tmbpart )="370608" ) THEN "PCU รัตนวารี" 
                WHEN CONCAT(p.chwpart,p.amppart,p.tmbpart ) NOT IN ("370608","370607","370606","370605","370604","370603","370602","370601")  
                THEN "นอกเขตอำเภอหัวตะพาน" END AS "pcu" ,o.*
                FROM patient p, opd_allergy o, allergy_seriousness o1, allergy_result o2 
                WHERE p.hn = o.hn AND o1.seriousness_id=o.seriousness_id
		AND o2.allergy_result_id=o.allergy_result_id
                GROUP BY p.hn,p.pname,p.fname,p.lname,p.drugallergy
                ORDER BY PCU'); 

        $request->session()->put('drugallergy',$drugallergy);
        $request->session()->save();

        return view('service_drug.drugallergy',compact('drugallergy'));
}
public function drugallergy_excel()
{
        $drugallergy = Session::get('drugallergy');

        return view('service_drug.drugallergy_excel',compact('drugallergy'));
}
###################################################################################################################
//มูลค่าการใช้ยา 20 อันดับ
public function value_drug_top(Request $request)
{
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if($start_date == '' || $end_date == null)
        {$start_date = date('Y-m-d', strtotime("last day"));}else{$start_date =$request->start_date;}
        if($end_date == '' || $end_date == null)
        {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

        $value_drug_top = DB::connection('hosxp')->select('
                SELECT icode,CONCAT(dname,SPACE(1),strength," / ",units) AS dname,SUM(qty) AS qty,SUM(cost*qty) AS sum_cost,SUM(sum_price) AS sum_price,
                SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN sum_price ELSE 0 END) AS ucs_price,
                SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN ("14","H1") THEN sum_price ELSE 0 END) AS ofc_price,
                SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN sum_price ELSE 0 END) AS sss_price,
                SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN sum_price ELSE 0 END) AS lgo_price,
                SUM(CASE WHEN (hipdata_code like "NR%" OR hipdata_code IN ("ST","STP","A1","A9") OR paidst IN ("01","03")) THEN sum_price ELSE 0 END) AS other_price
                FROM (SELECT o.vn,o.an,d.icode,d.`name` AS dname,d.strength,d.units,o.qty,o.cost,o.unitprice,o.sum_price,
                IF((d.drugaccount="-" OR d.drugaccount IS NULL OR d.drugaccount =""),"NED","ED") AS account,
                d.drugaccount,o.pttype,p.hipdata_code,p.paidst
                FROM opitemrece o 
                LEFT JOIN drugitems d ON d.icode = o.icode
                LEFT JOIN pttype p ON p.pttype=o.pttype
                WHERE o.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND o.icode LIKE "1%" AND (o.an IS NULL OR o.an ="") GROUP BY o.vn,o.an,o.icode ) AS a 
                WHERE qty <> "0" GROUP BY icode ORDER BY sum_price DESC limit 30'); 

        $value_drug_top_ipd = DB::connection('hosxp')->select('
                SELECT icode,CONCAT(dname,SPACE(1),strength," / ",units) AS dname,SUM(qty) AS qty,SUM(cost*qty) AS sum_cost,SUM(sum_price) AS sum_price,
                SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN sum_price ELSE 0 END) AS ucs_price,
                SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN ("14","H1") THEN sum_price ELSE 0 END) AS ofc_price,
                SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN sum_price ELSE 0 END) AS sss_price,
                SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN sum_price ELSE 0 END) AS lgo_price,
                SUM(CASE WHEN (hipdata_code like "NR%" OR hipdata_code IN ("ST","STP","A1","A9") OR paidst IN ("01","03")) THEN sum_price ELSE 0 END) AS other_price
                FROM (SELECT o.vn,o.an,d.icode,d.`name` AS dname,d.strength,d.units,o.qty,o.cost,o.unitprice,o.sum_price,
                IF((d.drugaccount="-" OR d.drugaccount IS NULL OR d.drugaccount =""),"NED","ED") AS account,
                d.drugaccount,o.pttype,p.hipdata_code,p.paidst
                FROM opitemrece o 
                LEFT JOIN drugitems d ON d.icode = o.icode
                LEFT JOIN pttype p ON p.pttype=o.pttype
                WHERE o.rxdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                AND o.icode LIKE "1%" AND (o.vn IS NULL OR o.vn ="") GROUP BY o.vn,o.an,o.icode ) AS a 
                WHERE qty <> "0" GROUP BY icode ORDER BY sum_price DESC limit 30'); 

        $request->session()->put('value_drug_top',$value_drug_top);
        $request->session()->put('value_drug_top_ipd',$value_drug_top_ipd);
        $request->session()->put('start_date',$start_date);
        $request->session()->put('end_date',$end_date);
        $request->session()->save();

        return view('service_drug.value_drug_top',compact('start_date','end_date','value_drug_top','value_drug_top_ipd'));
}
public function value_drug_top_excel()
{
        $value_drug_top = Session::get('value_drug_top');
        $value_drug_top_ipd = Session::get('value_drug_top_ipd');
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');

        return view('service_drug.value_drug_top_excel',compact('start_date','end_date','value_drug_top','value_drug_top_ipd'));
}
}
