<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service_OperationController extends Controller
{
  //Check Login
public function __construct()
{
      $this->middleware('auth');
}

//Create index
public function index()
{     
      return view('service_operation.index');            
}

//Create count
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

    $count_month = DB::connection('hosxp')->select('
        SELECT CASE WHEN MONTH(request_operation_date)="10" THEN CONCAT("ต.ค. ",RIGHT(YEAR(request_operation_date)+543,2))
        WHEN MONTH(request_operation_date)="11" THEN CONCAT("พ.ย. ",RIGHT(YEAR(request_operation_date)+543,2))
        WHEN MONTH(request_operation_date)="12" THEN CONCAT("ธ.ค. ",RIGHT(YEAR(request_operation_date)+543,2))
        WHEN MONTH(request_operation_date)="1" THEN CONCAT("ม.ค. ",RIGHT(YEAR(request_operation_date)+543,2))
        WHEN MONTH(request_operation_date)="2" THEN CONCAT("ก.พ. ",RIGHT(YEAR(request_operation_date)+543,2))
        WHEN MONTH(request_operation_date)="3" THEN CONCAT("มี.ค. ",RIGHT(YEAR(request_operation_date)+543,2))
        WHEN MONTH(request_operation_date)="4" THEN CONCAT("เม.ย. ",RIGHT(YEAR(request_operation_date)+543,2))
        WHEN MONTH(request_operation_date)="5" THEN CONCAT("พ.ค. ",RIGHT(YEAR(request_operation_date)+543,2))
        WHEN MONTH(request_operation_date)="6" THEN CONCAT("มิ.ย. ",RIGHT(YEAR(request_operation_date)+543,2))
        WHEN MONTH(request_operation_date)="7" THEN CONCAT("ก.ค. ",RIGHT(YEAR(request_operation_date)+543,2))
        WHEN MONTH(request_operation_date)="8" THEN CONCAT("ส.ค. ",RIGHT(YEAR(request_operation_date)+543,2))
        WHEN MONTH(request_operation_date)="9" THEN CONCAT("ก.ย. ",RIGHT(YEAR(request_operation_date)+543,2))
        END AS "month",SUM(CASE WHEN patient_department = "OPD" THEN 1 ELSE 0 END) AS "opd",
        SUM(CASE WHEN patient_department = "IPD" THEN 1 ELSE 0 END) AS "ipd"
        FROM operation_list 
        WHERE request_operation_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY MONTH(request_operation_date)
        ORDER BY YEAR(request_operation_date) , MONTH(request_operation_date)');
    $count_m = array_column($count_month,'month');
    $count_opd_m = array_column($count_month,'opd');
    $count_ipd_m = array_column($count_month,'ipd');

    $count_year = DB::connection('hosxp')->select('
        SELECT IF(MONTH(request_operation_date)>9,YEAR(request_operation_date)+1,YEAR(request_operation_date)) + 543 AS year_bud,
        SUM(CASE WHEN patient_department = "OPD" THEN 1 ELSE 0 END) AS "opd",
        SUM(CASE WHEN patient_department = "IPD" THEN 1 ELSE 0 END) AS "ipd"
        FROM operation_list 
        WHERE request_operation_date BETWEEN "'.$start_date_y.'" AND "'.$end_date.'"
        GROUP BY year_bud
        ORDER BY year_bud');
    $count_y = array_column($count_year,'year_bud');
    $count_opd_y = array_column($count_year,'opd');
    $count_ipd_y = array_column($count_year,'ipd');

    $operation_name_top = DB::connection('hosxp')->select('
        SELECT oi.`name` AS operation_name,COUNT(od.operation_item_id) AS total
        FROM operation_list o 
        INNER JOIN operation_detail od ON od.operation_id=o.operation_id
        LEFT JOIN operation_item oi ON oi.operation_item_id=od.operation_item_id
        WHERE o.operation_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY od.operation_item_id ORDER BY total DESC');
    $operation_name = array_column($operation_name_top,'operation_name');
    $operation_name_total = array_column($operation_name_top,'total');

    $request_doctor = DB::connection('hosxp')->select('
        SELECT d.`name` AS request_doctor,COUNT(DISTINCT o.operation_id) AS total
        FROM operation_list o 
        INNER JOIN doctor d ON d.`code`=o.request_doctor AND d.position_id = "1"
        WHERE request_operation_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY o.request_doctor ORDER BY total DESC');
    $request_doctor_name = array_column($request_doctor,'request_doctor');
    $request_doctor_total = array_column($request_doctor,'total');

    return view('service_operation.count',compact('budget_year_select','budget_year','count_m','count_opd_m','count_ipd_m',
        'count_y','count_opd_y','count_ipd_y','operation_name','operation_name_total','request_doctor_name','request_doctor_total'));            
}

}
