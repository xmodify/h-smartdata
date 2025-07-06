<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Nhso_Endpoint;

class Dashboard_DigitalhealthController extends Controller 
{
//Check Login
public function __construct()
{
    $this->middleware('auth')->except(['nhso_endpoint_pull_daily','digitalhealth','opd_mornitor','ipd_mornitor']);
}

//Create nhso_endpoint_pull_daily
public function nhso_endpoint_pull_daily(Request $request)
{
    $hcode = "10989";
    $date_now = date('Y-m-d'); 
    //$cid ="1341800057879";
    $url = "https://authenservice.nhso.go.th/authencode/api/authencode-report?hcode=$hcode&claimDateFrom=$date_now&claimDateTo=$date_now&size=1000";
    $cookies  = request()->cookies->all(); 
    $response = Http::withBasicAuth('6213043246152', 'h10989')
                ->withCookies($cookies, $url)    
                ->get($url); 
    $authen= $response['content'];     
   
    foreach ($authen as $value) {   
        $transId = $value['transId'];   
        $hmain = $value['hmain'];    
        $hname = $value['hname'];              
        $personalId = $value['personalId'];
        //$patientName = $value['patientName'];
        if (isset($value['patientName'])) {$patientName = $value['patientName'];} else {$patientName = '';}
        //$moonanName = $value['moonanName'];
        if (isset($value['moonanName'])) {$moonanName = $value['moonanName'];} else {$moonanName = '';}
        //$tumbonName = $value['tumbonName'];
        if (isset($value['tumbonName'])) {$tumbonName = $value['tumbonName'];} else {$tumbonName = '';}
        //$amphurName = $value['amphurName'];
        if (isset($value['amphurName'])) {$amphurName = $value['amphurName'];} else {$amphurName = '';}
        //$changwatName = $value['changwatName'];
        if (isset($value['changwatName'])) {$changwatName = $value['changwatName'];} else {$changwatName = '';}
        //$mainInscl = $value['mainInscl'];
        if (isset($value['mainInscl'])) {$mainInscl = $value['mainInscl'];} else {$mainInscl = '';}
        //$mainInsclName = $value['mainInsclName'];
        if (isset($value['mainInsclName'])) {$mainInsclName = $value['mainInsclName'];} else {$mainInsclName = '';}
        //$subInscl = $value['subInscl'];
        if (isset($value['subInscl'])) {$subInscl = $value['subInscl'];} else {$subInscl = '';}
        //$subInsclName = $value['subInsclName'];
        if (isset($value['subInsclName'])) {$subInsclName = $value['subInsclName'];} else {$subInsclName = '';}
        //$claimStatus = $value['claimStatus'];   
        if (isset($value['claimStatus'])) {$claimStatus = $value['claimStatus'];} else {$claimStatus = '';}
        //$claimCode = $value['claimCode'];
        if (isset($value['claimCode'])) {$claimCode = $value['claimCode'];} else {$claimCode = '';}
        //$claimType = $value['claimType'];
        if (isset($value['claimType'])) {$claimType = $value['claimType'];} else {$claimType = '';}
        //$claimTypeName = $value['claimTypeName'];
        if (isset($value['claimTypeName'])) {$claimTypeName = $value['claimTypeName'];} else {$claimTypeName = '';}
        //$claimDate = $value['claimDate'];
        if (isset($value['claimDate'])) {$claimDate = $value['claimDate'];} else {$claimDate = '';}
        $createDate = $value['createDate'];
        //$claimDate_old =  explode("T",$value['claimDate']);
        //$claimDate = $claimDate_old[0];
        //$claimTime = $claimDate_old[1]; 
        //$sourceChannel = $value['sourceChannel'];  
        if (isset($value['sourceChannel'])) {$sourceChannel = $value['sourceChannel'];} else {$sourceChannel = '';}
        $claimAuthen = $value['claimAuthen'];  

        $check_authen = Nhso_Endpoint::where('transId',$transId)->count();
        if ( $check_authen > 0 ) {
            Nhso_Endpoint::where('transId',$transId)->update([
                'claimCode'         => $claimCode,
                'claimType'         => $claimType,
                'claimTypeName'     => $claimTypeName,
                'claimStatus'       => $claimStatus
            ]);
        } else if ( $sourceChannel=='ENDPOINT' || $claimType == "PG0140001") {
            Nhso_Endpoint::insert([   
                'transId'           => $transId,                 
                'hmain'             => $hmain,
                'hname'             => $hname,
                'personalId'        => $personalId,
                'patientName'       => $patientName,
                'moonanName'        => $moonanName,
                'tumbonName'        => $tumbonName,
                'amphurName'        => $amphurName,
                'changwatName'      => $changwatName,               
                'mainInscl'         => $mainInscl,
                'mainInsclName'     => $mainInsclName,
                'subInscl'          => $subInscl,
                'subInsclName'      => $subInsclName,
                'claimStatus'       => $claimStatus,            
                'claimCode'         => $claimCode,
                'claimType'         => $claimType,
                'claimTypeName'     => $claimTypeName,
                'claimDate'         => $claimDate, 
                'createDate'        => $claimDate,   
                'sourceChannel'     => $sourceChannel,   
                'claimAuthen'       => $claimAuthen                        
            ]);   
        }    
    }   
    return back(); 
}    
 
//Create Digitalhealth
public function digitalhealth(Request $request )
{
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}   
    $start_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_BEGIN');
    $end_date = DB::connection('backoffice')->table('budget_year')->where('LEAVE_YEAR_ID',$budget_year)->value('DATE_END');
    
    $moph_appointment= DB::connection('hosxp')->select('
        SELECT CASE WHEN MONTH(confirm_date)="10" THEN CONCAT("ตุลาคม ",YEAR(confirm_date)+543)
        WHEN MONTH(confirm_date)="11" THEN CONCAT("พฤศจิกายน ",YEAR(confirm_date)+543)
        WHEN MONTH(confirm_date)="12" THEN CONCAT("ธันวาคม ",YEAR(confirm_date)+543)
        WHEN MONTH(confirm_date)="1" THEN CONCAT("มกราคม ",YEAR(confirm_date)+543)
        WHEN MONTH(confirm_date)="2" THEN CONCAT("กุมภาพันธ์ ",YEAR(confirm_date)+543)
        WHEN MONTH(confirm_date)="3" THEN CONCAT("มีนาคม ",YEAR(confirm_date)+543)
        WHEN MONTH(confirm_date)="4" THEN CONCAT("เมษายน ",YEAR(confirm_date)+543)
        WHEN MONTH(confirm_date)="5" THEN CONCAT("พฤษภาคม ",YEAR(confirm_date)+543)
        WHEN MONTH(confirm_date)="6" THEN CONCAT("มิถุนายน ",YEAR(confirm_date)+543)
        WHEN MONTH(confirm_date)="7" THEN CONCAT("กรกฎาคม ",YEAR(confirm_date)+543)
        WHEN MONTH(confirm_date)="8" THEN CONCAT("สิงหาคม ",YEAR(confirm_date)+543)
        WHEN MONTH(confirm_date)="9" THEN CONCAT("กันยายน ",YEAR(confirm_date)+543)
        END AS "month",
        SUM(CASE WHEN room_name ="แพทย์แผนไทย" THEN 1 ELSE 0 END) AS "healthmed",
        SUM(CASE WHEN room_name ="ทันตกรรม" THEN 1 ELSE 0 END) AS "dent",
        SUM(CASE WHEN room_name ="กายภาพบำบัด" THEN 1 ELSE 0 END) AS "physic",
        SUM(CASE WHEN room_name ="ฝากครรภ์" THEN 1 ELSE 0 END) AS "anc"
        FROM (SELECT m.person_name,m.cid,DATE(m.confirm_datetime) AS confirm_date,
        m.appointment_date,m.room_name,appointment_type_name,v.pdx
        FROM moph_appointment_list m
        LEFT JOIN vn_stat v ON v.cid = m.cid AND v.vstdate=m.appointment_date
        GROUP BY m.hospital_appointment_slot_id,m.cid) AS a
        WHERE a.confirm_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY MONTH(confirm_date)
        ORDER BY YEAR(confirm_date),MONTH(confirm_date)');

    $telehealth= DB::connection('hosxp')->select('
        SELECT CASE WHEN MONTH(vstdate)="10" THEN CONCAT("ตุลาคม ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="11" THEN CONCAT("พฤศจิกายน ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="12" THEN CONCAT("ธันวาคม ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="1" THEN CONCAT("มกราคม ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="2" THEN CONCAT("กุมภาพันธ์ ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="3" THEN CONCAT("มีนาคม ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="4" THEN CONCAT("เมษายน ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="5" THEN CONCAT("พฤษภาคม ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="6" THEN CONCAT("มิถุนายน ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="7" THEN CONCAT("กรกฎาคม ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="8" THEN CONCAT("สิงหาคม ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="9" THEN CONCAT("กันยายน ",YEAR(vstdate)+543)
        END AS "month",COUNT(DISTINCT vn) AS visit_op ,
        SUM(CASE WHEN ovstist = "12" THEN 1 ELSE 0 END) AS telehealth
        FROM ovst         		
        WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND (an ="" OR an IS NULL)          
        GROUP BY MONTH(vstdate)  
        ORDER BY YEAR(vstdate) , MONTH(vstdate)');

    $opd_ucs= DB::connection('hosxp')->select('
        SELECT CASE WHEN MONTH(vstdate)="10" THEN CONCAT("ตุลาคม ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="11" THEN CONCAT("พฤศจิกายน ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="12" THEN CONCAT("ธันวาคม ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="1" THEN CONCAT("มกราคม ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="2" THEN CONCAT("กุมภาพันธ์ ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="3" THEN CONCAT("มีนาคม ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="4" THEN CONCAT("เมษายน ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="5" THEN CONCAT("พฤษภาคม ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="6" THEN CONCAT("มิถุนายน ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="7" THEN CONCAT("กรกฎาคม ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="8" THEN CONCAT("สิงหาคม ",YEAR(vstdate)+543)
        WHEN MONTH(vstdate)="9" THEN CONCAT("กันยายน ",YEAR(vstdate)+543)
        END AS "month",COUNT(DISTINCT o.vn) AS total,
        SUM(CASE WHEN vp.hospmain = "10989" THEN 1 ELSE 0 END) AS lz,
        SUM(CASE WHEN vp.hospmain <> "10989" AND (r.vn <>"" OR r.vn IS NOT NULL) THEN 1 ELSE 0 END) AS osr,
        SUM(CASE WHEN vp.hospmain <> "10989" AND (r.vn ="" OR r.vn IS NULL) THEN 1 ELSE 0 END) AS osnr
        FROM ovst o
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN referin r ON r.vn=o.vn
        WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND p.hipdata_code = "UCS" AND (o.an ="" OR o.an IS NULL) 
        GROUP BY MONTH(o.vstdate)
        ORDER BY YEAR(o.vstdate),MONTH(o.vstdate)');

    return view('dashboard.digitalhealth',compact('budget_year_select','budget_year',
    'moph_appointment','telehealth','opd_ucs'));
}

public function opd_mornitor(Request $request )
{
    $nhso_monitor=DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS total,IFNULL(SUM(CASE WHEN hipdata_code = "UCS" AND (an ="" OR an IS NULL) THEN 1 ELSE 0 END),0) AS "ucs_all",
			IFNULL(SUM(CASE WHEN endpoint_code LIKE "EP%" THEN 1 ELSE 0 END),0) AS "endpoint",
        IFNULL(SUM(CASE WHEN (auth_code IS NULL OR auth_code ="") AND cid NOT LIKE "0%" AND pttype NOT IN ("10","11","12","13")  
            THEN 1 ELSE 0 END),0) AS "non_authen",
        IFNULL(SUM(CASE WHEN (hipdata_code = "UCS" OR hipdata_code ="SSS")AND (hospmain="" OR hospmain IS NULL)
            THEN 1 ELSE 0 END),0) AS "non_hospmain",
        IFNULL(SUM(CASE WHEN hipdata_code = "UCS" AND hospmain NOT IN ("10703","10985","10986","10987","10988","10989","10990")
            AND (an ="" OR an IS NULL) THEN 1 ELSE 0 END),0) AS "op_anywhere",
        IFNULL(SUM(CASE WHEN hipdata_code = "UCS" AND hospmain NOT IN ("10703","10985","10986","10987","10988","10989","10990") 
            AND endpoint_code LIKE "EP%" AND (an ="" OR an IS NULL) THEN 1 ELSE 0 END),0) AS "op_anywhere_endpoint",
		IFNULL(SUM(CASE WHEN hipdata_code = "UCS" AND hospmain NOT IN ("10703","10985","10986","10987","10988","10989","10990") 
            AND (fdh IS NOT NULL OR fdh <>"") AND (an ="" OR an IS NULL) 
			THEN 1 ELSE 0 END),0) AS "op_anywhere_fdh",
        IFNULL(SUM(CASE WHEN (xray_items_code <>"" OR lab_items_code <>"") AND paidst = "02" AND (an ="" OR an IS NULL) THEN 1 ELSE 0 END),0) AS "cxr",
        IFNULL(SUM(CASE WHEN (xray_items_code <>"" OR lab_items_code <>"") AND paidst = "02" AND (an ="" OR an IS NULL) AND endpoint_code LIKE "EP%"
             THEN 1 ELSE 0 END),0) AS "cxr_endpoint",      
        IFNULL(SUM(CASE WHEN (icode IS NOT NULL OR icode <>"") AND (an ="" OR an IS NULL) AND hipdata_code = "UCS" 
            THEN 1 ELSE 0 END),0) AS "kidney",
        IFNULL(SUM(CASE WHEN (icode IS NOT NULL OR icode <>"") AND (an ="" OR an IS NULL) AND hipdata_code = "UCS"
            AND endpoint_code LIKE "EP%" THEN 1 ELSE 0 END),0) AS "kidney_endpoint",
        IFNULL(SUM(CASE WHEN (icode_cr IS NOT NULL OR icode_cr <>"") AND hospmain IN ("10703","10985","10986","10987","10988","10989","10990")
            AND hipdata_code = "UCS" AND (an ="" OR an IS NULL) THEN 1 ELSE 0 END),0) AS "ucop_cr",
        IFNULL(SUM(CASE WHEN (icode_cr IS NOT NULL OR icode_cr <>"") AND hospmain IN ("10703","10985","10986","10987","10988","10989","10990")
            AND hipdata_code = "UCS" AND endpoint_code LIKE "EP%" AND (an ="" OR an IS NULL) THEN 1 ELSE 0 END),0) AS "ucop_cr_endpoint",
        IFNULL(SUM(CASE WHEN (icode_cr IS NOT NULL OR icode_cr <>"") AND hospmain IN ("10703","10985","10986","10987","10988","10989","10990")
            AND hipdata_code = "UCS" AND (fdh IS NOT NULL OR fdh <>"") AND (an ="" OR an IS NULL) THEN 1 ELSE 0 END),0) AS "ucop_cr_fdh",
        IFNULL(SUM(CASE WHEN (icode_ppfs IS NOT NULL OR icode_ppfs <>"") AND (an ="" OR an IS NULL) THEN 1 ELSE 0 END),0) AS "uc_ppfs",
        IFNULL(SUM(CASE WHEN (icode_ppfs IS NOT NULL OR icode_ppfs <>"") AND (an ="" OR an IS NULL) AND endpoint_code LIKE "EP%" THEN 1 ELSE 0 END),0) AS "uc_ppfs_endpoint",
        IFNULL(SUM(CASE WHEN (icode_ppfs IS NOT NULL OR icode_ppfs <>"") AND (an ="" OR an IS NULL) AND (fdh IS NOT NULL OR fdh <>"") THEN 1 ELSE 0 END),0) AS "uc_ppfs_fdh",
        IFNULL(SUM(CASE WHEN (homeward IS NOT NULL OR homeward <>"") THEN 1 ELSE 0 END),0) AS "homeward",
        IFNULL(SUM(CASE WHEN (homeward IS NOT NULL OR homeward <>"") AND ClaimType= "PG0140001"	THEN 1 ELSE 0 END),0) AS "homeward_auth",
        IFNULL(SUM(CASE WHEN (icode_herb IS NOT NULL OR icode_herb <>"" OR healthmed IS NOT NULL OR healthmed <>"") 
            AND (an ="" OR an IS NULL) AND hipdata_code = "UCS"  AND income-paid_money <> 0 THEN 1 ELSE 0 END),0) AS "healthmed",
        IFNULL(SUM(CASE WHEN (icode_herb IS NOT NULL OR icode_herb <>"" OR healthmed IS NOT NULL OR healthmed <>"") AND hipdata_code = "UCS"
            AND endpoint_code LIKE "EP%" AND (an ="" OR an IS NULL) THEN 1 ELSE 0 END),0) AS "healthmed_endpoint",
        (SELECT COUNT(DISTINCT hn) FROM opitemrece  WHERE rxdate = DATE(NOW())
            AND icode IN (SELECT icode FROM xray_items WHERE xray_items_group = 3)) AS "ct",
		(SELECT SUM(sum_price) FROM opitemrece  WHERE rxdate = DATE(NOW())
            AND icode IN (SELECT icode FROM xray_items WHERE xray_items_group = 3)) AS "ct_price"
        FROM (SELECT o.vn,v.cid,vp.auth_code,IF(vp.auth_code NOT LIKE "EP%",IFNULL(epi.claimCode,ep.claimCode),vp.auth_code) AS endpoint_code,vp.pttype,vp.hospmain,
            p.hipdata_code,IFNULL(ep.sourceChannel,epi.sourceChannel) AS sourceChannel,ep.claimStatus,x.xray_items_code,l.lab_items_code,p.paidst,o1.icode,
            o2.icode AS icode_cr,o3.icode AS icode_ppfs,oe.moph_finance_upload_datetime AS fdh,o.an,i.an AS homeward,IFNULL(ep.claimType,epi.claimType) AS claimType,
            p.pttype_price_group_id,v.pdx,o4.icode AS icode_herb,hm.vn AS healthmed,v.income,v.paid_money
        FROM ovst o
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN xray_report x ON x.vn=o.vn AND x.xray_items_code IN ("10","46","70","71")
        LEFT JOIN (SELECT vn,lab_items_code 
            FROM lab_head lh
            LEFT JOIN lab_order lo ON lo.lab_order_number=lh.lab_order_number 
            WHERE lo.lab_items_code IN ("167","169")) l ON l.vn=o.vn
        LEFT JOIN opitemrece o1 ON o1.vn=o.vn AND o1.icode IN ("3003375","3004035")
		LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN (SELECT icode FROM htp_report.finance_lookup_icode 
			WHERE debtor_code = "1102050101.216" AND icode_type <> "kidney")
		LEFT JOIN opitemrece o3 ON o3.vn=o.vn AND o3.icode IN (SELECT icode FROM htp_report.finance_lookup_icode 
			WHERE debtor_code = "1102050101.209")
		LEFT JOIN opitemrece o4 ON o4.vn=o.vn AND (o4.icode IN (SELECT icode FROM drugitems WHERE nhso_adp_code LIKE "HERB%")
            OR o4.icode IN (SELECT icode FROM drugitems_property_list WHERE drugitems_property_id IN ("12","13")))
        LEFT JOIN vn_stat v ON v.vn=o.vn
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
		LEFT JOIN ipt i ON i.an=o.an AND i.ward = "06" 	
		LEFT JOIN health_med_service hm ON hm.vn=o.vn
        LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimStatus="E"
        LEFT JOIN htp_report.nhso_endpoint_indiv epi ON epi.cid=v.cid AND DATE(epi.serviceDateTime)=o.vstdate
        WHERE o.vstdate = DATE(NOW()) GROUP BY o.vn) AS a');

    foreach ($nhso_monitor as $row){
        $total = $row->total;
        $ucs_all = $row->ucs_all;
        $endpoint =$row->endpoint;
        $non_authen = $row->non_authen;
        $non_hospmain =$row->non_hospmain;
        $op_anywhere =$row->op_anywhere;
        $op_anywhere_endpoint =$row->op_anywhere_endpoint;
        $op_anywhere_fdh =$row->op_anywhere_fdh;
        $cxr =$row->cxr;
        $cxr_endpoint =$row->cxr_endpoint;
        $kidney =$row->kidney;
        $kidney_endpoint =$row->kidney_endpoint;
        $ucop_cr =$row->ucop_cr;
        $ucop_cr_endpoint =$row->ucop_cr_endpoint;
        $ucop_cr_fdh =$row->ucop_cr_fdh;
        $uc_ppfs =$row->uc_ppfs;
        $uc_ppfs_endpoint =$row->uc_ppfs_endpoint;
        $uc_ppfs_fdh =$row->uc_ppfs_fdh;
        $homeward =$row->homeward;
        $homeward_auth =$row->homeward_auth;
        $healthmed =$row->healthmed;
        $healthmed_endpoint =$row->healthmed_endpoint;
        $ct =$row->ct;
        $ct_price =$row->ct_price;
    }

    $op_visit_hour=DB::connection('hosxp')->select('
        SELECT CONCAT(vstdate,"T",CONCAT(LPAD(HOUR(vsttime),2,0),":","00",":","00"),".000Z") AS vstdate,
        CONCAT(LPAD(HOUR(vsttime),2,0),".","00",SPACE(1),"น.") AS htime,
        COUNT(DISTINCT vn) as opd_visit					
        FROM (SELECT o.vn,o.vstdate,o.vsttime,o.main_dep,oq.depcode 
        FROM ovst o LEFT JOIN opd_dep_queue oq ON oq.vn=o.vn
        WHERE o.vstdate = DATE(NOW()) 
        GROUP BY o.hn,oq.depcode ) AS a GROUP BY HOUR(vsttime) ');

    $vstdate = array_column($op_visit_hour,'vstdate');
    $op_visit = array_column($op_visit_hour,'opd_visit');

    $service_n=DB::connection('hosxp')->select('
        SELECT DATE(NOW()) AS vstdate,COUNT(DISTINCT vn) as opd_visit,
        IFNULL(SUM(CASE WHEN main_dep = "002" THEN 1 ELSE 0 END),0) AS opd,
        (SELECT COUNT(DISTINCT vn) FROM er_regist WHERE DATE(enter_er_time) = DATE(NOW()) 
            AND TIME(enter_er_time) BETWEEN "00:00:00" AND "07:59:59") AS er,
		(SELECT COUNT(DISTINCT vn) FROM physic_list WHERE DATE(begin_datetime) = DATE(NOW()) 
            AND TIME(begin_datetime) BETWEEN "00:00:00" AND "07:59:59") AS physic,
		(SELECT COUNT(DISTINCT vn) FROM health_med_service WHERE service_date = DATE(NOW()) 
            AND service_time BETWEEN "00:00:00" AND "07:59:59") AS health_med,
		(SELECT COUNT(DISTINCT vn) FROM dtmain WHERE DATE(begin_time) = DATE(NOW()) 
            AND TIME(begin_time) BETWEEN "00:00:00" AND "07:59:59") AS dent,
        IFNULL(SUM(CASE WHEN main_dep = "033" THEN 1 ELSE 0 END),0) AS kidney_hos,
        IFNULL(SUM(CASE WHEN main_dep = "024" THEN 1 ELSE 0 END),0) AS kidney_os,
        (SELECT COUNT(DISTINCT vn) FROM person_anc_service WHERE anc_service_date = DATE(NOW()) 
            AND anc_service_time BETWEEN "00:00:00" AND "07:59:59") AS anc,
		(SELECT COUNT(DISTINCT an) FROM ipt  WHERE regdate = DATE(NOW())
            AND regtime BETWEEN "00:00:00" AND "07:59:59") AS admit,
        (SELECT COUNT(DISTINCT vn) FROM referout WHERE refer_date = DATE(NOW())
            AND refer_time BETWEEN "00:00:00" AND "07:59:59") AS refer,
        (SELECT COUNT(DISTINCT operation_id) FROM operation_list WHERE request_operation_date = DATE(NOW())
            AND request_operation_time BETWEEN "00:00:00" AND "07:59:59") AS operation
        FROM (SELECT o.vn,o.vstdate,o.vsttime,o.main_dep FROM ovst o
        WHERE o.vstdate = DATE(NOW()) AND o.vsttime BETWEEN "00:00:00" AND "07:59:59" 
        GROUP BY o.hn,o.main_dep ORDER BY o.an DESC ) AS a ');

    foreach ($service_n as $row){
        $opd_n = $row->opd;
        $er_n = $row->er;
        $physic_n =$row->physic;
        $health_med_n = $row->health_med;
        $dent_n =$row->dent;
        $kidney_hos_n =$row->kidney_hos;
        $kidney_os_n =$row->kidney_os;
        $anc_n =$row->anc;
        $admit_n =$row->admit;
        $refer_n =$row->refer;
        $operation_n =$row->operation;       
    }    

    $service_m=DB::connection('hosxp')->select('
        SELECT DATE(NOW()) AS vstdate,COUNT(DISTINCT vn) as opd_visit,
        IFNULL(SUM(CASE WHEN main_dep = "002" THEN 1 ELSE 0 END),0) AS opd,
        (SELECT COUNT(DISTINCT vn) FROM er_regist WHERE DATE(enter_er_time) = DATE(NOW()) 
            AND TIME(enter_er_time) BETWEEN "08:00:00" AND "15:59:59") AS er,
		(SELECT COUNT(DISTINCT vn) FROM physic_list WHERE DATE(begin_datetime) = DATE(NOW()) 
            AND TIME(begin_datetime) BETWEEN "08:00:00" AND "15:59:59") AS physic,
		(SELECT COUNT(DISTINCT vn) FROM health_med_service WHERE service_date = DATE(NOW()) 
            AND service_time BETWEEN "08:00:00" AND "15:59:59") AS health_med,
		(SELECT COUNT(DISTINCT vn) FROM dtmain WHERE DATE(begin_time) = DATE(NOW()) 
            AND TIME(begin_time) BETWEEN "08:00:00" AND "15:59:59") AS dent,
        IFNULL(SUM(CASE WHEN main_dep = "033" THEN 1 ELSE 0 END),0) AS kidney_hos,
        IFNULL(SUM(CASE WHEN main_dep = "024" THEN 1 ELSE 0 END),0) AS kidney_os,
        (SELECT COUNT(DISTINCT vn) FROM person_anc_service WHERE anc_service_date = DATE(NOW()) 
            AND anc_service_time BETWEEN "08:00:00" AND "15:59:59") AS anc,
        (SELECT COUNT(DISTINCT an) FROM ipt  WHERE regdate = DATE(NOW())
            AND regtime BETWEEN "08:00:00" AND "15:59:59" ) AS admit,
        (SELECT COUNT(DISTINCT vn) FROM referout WHERE refer_date = DATE(NOW())
            AND refer_time BETWEEN "08:00:00" AND "15:59:59" ) AS refer,
        (SELECT COUNT(DISTINCT operation_id) FROM operation_list WHERE request_operation_date = DATE(NOW())
            AND request_operation_time BETWEEN "08:00:00" AND "15:59:59" ) AS operation
        FROM (SELECT o.vn,o.vstdate,o.vsttime,o.main_dep FROM ovst o
        WHERE o.vstdate = DATE(NOW()) AND o.vsttime BETWEEN "08:00:00" AND "15:59:59" 
        GROUP BY o.hn,o.main_dep ORDER BY o.an DESC ) AS a ');

    foreach ($service_m as $row){
        $opd_m = $row->opd;
        $er_m = $row->er;
        $physic_m =$row->physic;
        $health_med_m = $row->health_med;
        $dent_m =$row->dent;
        $kidney_hos_m =$row->kidney_hos;
        $kidney_os_m =$row->kidney_os;
        $anc_m =$row->anc;
        $admit_m =$row->admit;
        $refer_m =$row->refer;
        $operation_m =$row->operation;       
    }    

    $service_a=DB::connection('hosxp')->select('
        SELECT DATE(NOW()) AS vstdate,COUNT(DISTINCT vn) as opd_visit,
        IFNULL(SUM(CASE WHEN main_dep = "002" THEN 1 ELSE 0 END),0) AS opd,
        (SELECT COUNT(DISTINCT vn) FROM er_regist WHERE DATE(enter_er_time) = DATE(NOW()) 
            AND TIME(enter_er_time) BETWEEN "16:00:00" AND "23:59:59") AS er,
		(SELECT COUNT(DISTINCT vn) FROM physic_list WHERE DATE(begin_datetime) = DATE(NOW()) 
            AND TIME(begin_datetime) BETWEEN "16:00:00" AND "23:59:59") AS physic,
		(SELECT COUNT(DISTINCT vn) FROM health_med_service WHERE service_date = DATE(NOW()) 
            AND service_time BETWEEN "16:00:00" AND "23:59:59") AS health_med,
		(SELECT COUNT(DISTINCT vn) FROM dtmain WHERE DATE(begin_time) = DATE(NOW()) 
            AND TIME(begin_time) BETWEEN "16:00:00" AND "23:59:59") AS dent,
        IFNULL(SUM(CASE WHEN main_dep = "033" THEN 1 ELSE 0 END),0) AS kidney_hos,
        IFNULL(SUM(CASE WHEN main_dep = "024" THEN 1 ELSE 0 END),0) AS kidney_os,
        (SELECT COUNT(DISTINCT vn) FROM person_anc_service WHERE anc_service_date = DATE(NOW()) 
            AND anc_service_time BETWEEN "16:00:00" AND "23:59:59") AS anc,
        (SELECT COUNT(DISTINCT an) FROM ipt  WHERE regdate = DATE(NOW())
            AND regtime BETWEEN "16:00:00" AND "23:59:59") AS admit,
        (SELECT COUNT(DISTINCT vn) FROM referout WHERE refer_date = DATE(NOW())
            AND refer_time BETWEEN "16:00:00" AND "23:59:59") AS refer,
        (SELECT COUNT(DISTINCT operation_id) FROM operation_list WHERE request_operation_date = DATE(NOW())
            AND request_operation_time BETWEEN "16:00:00" AND "23:59:59") AS operation
        FROM (SELECT o.vn,o.vstdate,o.vsttime,o.main_dep FROM ovst o
        WHERE o.vstdate = DATE(NOW()) AND o.vsttime BETWEEN "16:00:00" AND "23:59:59"
        GROUP BY o.hn,o.main_dep ORDER BY o.an DESC ) AS a ');

    foreach ($service_a as $row){
        $opd_a = $row->opd;
        $er_a = $row->er;
        $physic_a =$row->physic;
        $health_med_a = $row->health_med;
        $dent_a =$row->dent;
        $kidney_hos_a =$row->kidney_hos;
        $kidney_os_a =$row->kidney_os;
        $anc_a =$row->anc;
        $admit_a =$row->admit;
        $refer_a =$row->refer;
        $operation_a =$row->operation;       
    }    

    return view('dashboard.opd_mornitor',compact('total','ucs_all','endpoint','non_authen','non_hospmain','op_anywhere',
            'op_anywhere_endpoint','op_anywhere_fdh','cxr','cxr_endpoint','kidney','kidney_endpoint','ucop_cr','ucop_cr_endpoint',
            'ucop_cr_fdh','uc_ppfs','uc_ppfs_endpoint','uc_ppfs_fdh','homeward','homeward_auth','healthmed','healthmed_endpoint',
            'ct','ct_price','vstdate','op_visit','opd_n','opd_m','opd_a','er_n','er_m','er_a','physic_n','physic_m','physic_a',
            'health_med_n','health_med_m','health_med_a','dent_n','dent_m','dent_a','kidney_hos_n','kidney_hos_m','kidney_hos_a',
            'kidney_os_n','kidney_os_m','kidney_os_a','anc_n','anc_m','anc_a','admit_n','admit_m','admit_a','refer_n','refer_m',
            'refer_a','operation_n','operation_m','operation_a'));}

public function opd_mornitor_non_authen(Request $request )
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

    $sql=DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,p.cid,p.mobile_phone_number,p.hometel,p1.`name` AS pttype,
        vp.hospmain,k.department,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y
        FROM ovst o
        LEFT JOIN vn_stat v ON v.vn=o.vn
        LEFT JOIN patient p ON p.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
        LEFT JOIN kskdepartment k ON k.depcode=o.main_dep
        WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND vp.pttype NOT IN ("10","11","12","13")
        AND p.cid NOT LIKE "0%"
        AND (vp.auth_code IS NULL OR vp.auth_code ="")           
        GROUP BY o.vn ORDER BY o.vsttime');

    return view('dashboard.opd_mornitor_non_authen',compact('start_date','end_date','sql'));
}

public function opd_mornitor_non_hospmain(Request $request )
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

    $sql=DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,
        pt.cid,pt.informtel,p.`name` AS pttype,vp.hospmain,v.income-v.paid_money AS debtor,
        IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,
        IF((ep.sourceChannel IS NOT NULL OR ep.sourceChannel <> ""),"Y",NULL) AS endpoint
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN xray_report x ON x.vn=o.vn
        LEFT JOIN vn_stat v ON v.vn = o.vn
        LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimStatus="E"
        WHERE o.vstdate  BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND (p.hipdata_code = "UCS" OR p.hipdata_code ="SSS") AND (vp.hospmain="" OR vp.hospmain IS NULL)
        GROUP BY o.vn ORDER BY o.vstdate,o.vsttime');

    return view('dashboard.opd_mornitor_non_hospmain',compact('start_date','end_date','sql'));
}

public function opd_mornitor_xray_chest(Request $request )
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
    
    $sql=DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,
        pt.cid,pt.informtel,p.`name` AS pttype,vp.hospmain,v.income-v.paid_money AS debtor,
        IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,k.department,
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%" OR epi.claimCode LIKE "EP%"),"Y",NULL) AS endpoint
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN xray_report x ON x.vn=o.vn
        LEFT JOIN lab_head lh ON lh.vn=o.vn
		LEFT JOIN lab_order lo ON lo.lab_order_number=lh.lab_order_number 
        LEFT JOIN kskdepartment k ON k.depcode = o.cur_dep
        LEFT JOIN vn_stat v ON v.vn = o.vn
        LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimStatus="E"
        LEFT JOIN htp_report.nhso_endpoint_indiv epi ON epi.cid=v.cid AND DATE(epi.serviceDateTime)=o.vstdate
        WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate  BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND (x.xray_items_code IN ("10","46","70","71") OR lo.lab_items_code IN ("167","169")) AND p.paidst = "02"   
        AND v.income-v.paid_money <> 0
        GROUP BY o.vn ORDER BY ep.sourceChannel,o.vstdate,o.vsttime');

    return view('dashboard.opd_mornitor_xray_chest',compact('start_date','end_date','sql'));
}

public function opd_mornitor_opanywhere(Request $request )
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

    $sql=DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,
        pt.cid,pt.informtel,p.`name` AS pttype,vp.hospmain,v.income-v.paid_money AS debtor,
        IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%" OR epi.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,
        vp.confirm_and_locked,vp.request_funds,oe.moph_finance_upload_datetime AS fdh,k.department
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN kskdepartment k ON k.depcode = o.cur_dep
        LEFT JOIN vn_stat v ON v.vn = o.vn
        LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
        LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimStatus="E"
        LEFT JOIN htp_report.nhso_endpoint_indiv epi ON epi.cid=v.cid AND DATE(epi.serviceDateTime)=o.vstdate
        WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND p.hipdata_code = "UCS" AND vp.hospmain NOT IN ("10703","10985","10986","10987","10988","10989","10990") 
        AND v.income-v.paid_money <> 0
        GROUP BY o.vn ORDER BY ep.sourceChannel,o.vstdate,o.vsttime');

    return view('dashboard.opd_mornitor_opanywhere',compact('start_date','end_date','sql'));
}

public function opd_mornitor_kidney(Request $request )
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

    $sql=DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,
        pt.cid,pt.informtel,p.`name` AS pttype,vp.hospmain,v.income-v.paid_money AS debtor,
        IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,k.department,
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%" OR epi.claimCode LIKE "EP%"),"Y",NULL) AS endpoint
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT OUTER JOIN kskdepartment k ON k.depcode = o.cur_dep
		LEFT JOIN opitemrece o1 ON o1.vn=o.vn
        LEFT JOIN vn_stat v ON v.vn = o.vn
        LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimStatus="E"
         LEFT JOIN htp_report.nhso_endpoint_indiv epi ON epi.cid=v.cid AND DATE(epi.serviceDateTime)=o.vstdate
        WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND p.hipdata_code = "UCS" AND o1.icode IN ("3003375","3004035")
        GROUP BY o.vn ORDER BY ep.sourceChannel,o.vstdate,o.vsttime');

    return view('dashboard.opd_mornitor_kidney',compact('start_date','end_date','sql'));
}

public function opd_mornitor_ucop_cr(Request $request )
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

    $sql=DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,o.oqueue,o.vn,o.hn,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
        IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,v.income-v.paid_money AS debtor,
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%" OR epi.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,p1.`name` AS pttype,		
        vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS other,
        IFNULL(SUM(o1.sum_price),0) AS debtor1,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,GROUP_CONCAT(DISTINCT n_proj.nhso_adp_code) AS project,
        vp.confirm_and_locked,vp.request_funds,oe.moph_finance_upload_datetime AS fdh,p.informtel,k.department
        FROM ovst o    
        LEFT JOIN patient p ON p.hn=o.hn
        LEFT JOIN vn_stat v ON v.vn=o.vn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
        LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
            IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
        LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
        LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
        IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.216" AND icode_type <> "kidney") 
        LEFT JOIN s_drugitems s ON s.icode = o1.icode		
        LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
        LEFT JOIN kskdepartment k ON k.depcode = o.cur_dep
        LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimStatus="E"
        LEFT JOIN htp_report.nhso_endpoint_indiv epi ON epi.cid=v.cid AND DATE(epi.serviceDateTime)=o.vstdate
        WHERE p1.hipdata_code = "UCS" AND vp.hospmain IN ("10703","10985","10986","10987","10988","10989","10990")
        AND (o.an IS NULL OR o.an ="") AND o1.vn IS NOT NULL AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

    return view('dashboard.opd_mornitor_ucop_cr',compact('start_date','end_date','sql'));
}

public function opd_mornitor_uc_ppfs(Request $request )
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
    
    $sql=DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,o.oqueue,o.vn,o.hn,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
		IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,p1.`name` AS pttype,	
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%" OR epi.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,v.income-v.paid_money AS debtor,
		vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS other,
		IFNULL(SUM(o1.sum_price),0) AS debtor1,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,GROUP_CONCAT(DISTINCT n_proj.nhso_adp_code) AS project,
		vp.confirm_and_locked,vp.request_funds,oe.moph_finance_upload_datetime AS fdh,p.informtel,k.department
        FROM ovst o    
        LEFT JOIN patient p ON p.hn=o.hn
        LEFT JOIN vn_stat v ON v.vn=o.vn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
        LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
            IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
		LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
        LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
            IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209" ) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode		
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
        LEFT JOIN kskdepartment k ON k.depcode = o.cur_dep
        LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimStatus="E"
        LEFT JOIN htp_report.nhso_endpoint_indiv epi ON epi.cid=v.cid AND DATE(epi.serviceDateTime)=o.vstdate
        WHERE (o.an ="" OR o.an IS NULL) AND o1.vn IS NOT NULL AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"		
        GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

    return view('dashboard.opd_mornitor_uc_ppfs',compact('start_date','end_date','sql'));
}

public function opd_mornitor_homeward(Request $request )
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

    $sql=DB::connection('hosxp')->select('
        SELECT IFNULL(ep.claimCode,epi.claimCode) AS claimCode,o.vstdate,o.vsttime,o.oqueue,o.hn,p.cid,p.mobile_phone_number,
        p.hometel,p1.`name` AS pttype,vp.hospmain,k.department,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y
        FROM ovst o
        LEFT JOIN vn_stat v ON v.vn=o.vn
        LEFT JOIN patient p ON p.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p1 ON p1.pttype=vp.pttype				
        LEFT JOIN kskdepartment k ON k.depcode=o.main_dep
		LEFT JOIN ipt i ON i.an=o.an AND i.ward = "06"
		LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimType = "PG0140001"
        LEFT JOIN htp_report.nhso_endpoint_indiv epi ON epi.cid=v.cid AND DATE(epi.serviceDateTime)=o.vstdate AND epi.claimType = "PG0140001"
        WHERE (i.an IS NOT NULL OR i.an <>"") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		GROUP BY o.vn ORDER BY o.vsttime');

    return view('dashboard.opd_mornitor_homeward',compact('start_date','end_date','sql'));
}

public function opd_mornitor_healthmed(Request $request )
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

    $sql=DB::connection('hosxp')->select('
        SELECT  IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%" OR epi.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,
        o.vstdate,o.vsttime,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,vp.hospmain,
        pt.cid,pt.informtel,p.`name` AS pttype,v.income-v.paid_money AS debtor,GROUP_CONCAT(DISTINCT d.`name`) AS drug ,
		GROUP_CONCAT(DISTINCT hm.health_med_operation) AS operation,k.department				
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN opitemrece o1 ON o1.vn=o.vn  AND (o1.icode IN (SELECT icode FROM drugitems WHERE nhso_adp_code LIKE "HERB%")	
			OR o1.icode IN (SELECT icode FROM drugitems_property_list WHERE drugitems_property_id IN ("12","13")))
		LEFT JOIN drugitems d ON d.icode=o1.icode
        LEFT OUTER JOIN kskdepartment k ON k.depcode = o.cur_dep
        LEFT JOIN vn_stat v ON v.vn = o.vn
		LEFT JOIN (SELECT h.vn,CONCAT(h2.health_med_operation_item_name," [",h2.icd10tm,"]") AS health_med_operation 
			FROM health_med_service h
			LEFT JOIN health_med_service_operation h1 ON h1.health_med_service_id=h.health_med_service_id
			LEFT JOIN health_med_operation_item h2 ON h2.health_med_operation_item_id=h1.health_med_operation_item_id
			WHERE h.service_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
			GROUP BY h1.health_med_service_id,h1.health_med_operation_item_id) hm ON hm.vn=o.vn
        LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimStatus="E"
        LEFT JOIN htp_report.nhso_endpoint_indiv epi ON epi.cid=v.cid AND DATE(epi.serviceDateTime)=o.vstdate
        WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND p.hipdata_code = "UCS"
        AND (o1.icode IN (SELECT icode FROM drugitems WHERE nhso_adp_code LIKE "HERB%")	
            OR o1.icode IN (SELECT icode FROM drugitems_property_list WHERE drugitems_property_id IN ("12","13"))	
            OR hm.vn IS NOT NULL OR hm.vn <>"")
        AND v.income-v.paid_money <> 0
        GROUP BY o.vn ORDER BY ep.sourceChannel,hm.health_med_operation DESC,o.vstdate,o.vsttime');

    return view('dashboard.opd_mornitor_healthmed',compact('start_date','end_date','sql'));
}

###############################################################################################################################
public function ipd_mornitor(Request $request )
{
    $start_date = "2024-10-01";
   
    $sql=DB::connection('hosxp')->select('
        SELECT COUNT(DISTINCT an) AS total,
        IFNULL(SUM(CASE WHEN ward = "01" THEN 1 ELSE 0 END),0) AS "ipd",
        IFNULL(SUM(CASE WHEN ward = "03" THEN 1 ELSE 0 END),0) AS "vip",
        IFNULL(SUM(CASE WHEN ward = "02" THEN 1 ELSE 0 END),0) AS "lr",
        IFNULL(SUM(CASE WHEN ward = "06" THEN 1 ELSE 0 END),0) AS "homeward"
        FROM (SELECT i.an,i.regdate,i.regtime,i.dchdate,i.dchtime,i.ward 
        FROM ipt i WHERE confirm_discharge = "N") AS a');

    foreach ($sql as $row){
        $total = $row->total;
        $ipd =$row->ipd;
        $vip = $row->vip;
        $lr =$row->lr;
        $homeward =$row->homeward;      
    } 

    $sql2 = DB::connection('hosxp')->select('
        SELECT SUM(CASE WHEN (id1.diag_text ="" OR id1.diag_text IS NULL) THEN 1 ELSE 0 END) AS non_diagtext,
        SUM(CASE WHEN (id.icd10 ="" OR id.icd10 IS NULL) AND id1.diag_text <>"" AND id1.diag_text IS NOT NULL THEN 1 ELSE 0 END) AS non_icd10
        FROM ipt i
        LEFT JOIN iptdiag id ON id.an = i.an AND id.diagtype = 1
        LEFT JOIN ipt_doctor_diag id1 ON id1.an = i.an	AND id1.diagtype = 1 
        WHERE i.ward IN ("01","02","03","10") AND i.dchdate >= "2024-12-01" 
        AND (id.icd10 ="" OR id.icd10 IS NULL OR id1.diag_text ="" OR id1.diag_text IS NULL)');         

    foreach ($sql2 as $row){ 
        $non_diagtext=$row->non_diagtext;
        $non_icd10=$row->non_icd10;
    }

    $sql3 = DB::connection('hosxp')->select('
        SELECT SUM(CASE WHEN (finance_transfer = "N" OR opd_wait_money <> "0") THEN 1 ELSE 0 END) AS not_transfer,
        SUM(CASE WHEN wait_paid_money <> "0" THEN 1 ELSE 0 END) AS wait_paid_money,
        SUM(wait_paid_money) AS sum_wait_paid_money
        FROM (SELECT i.hn,i.an,i.finance_transfer,a.opd_wait_money,a.item_money,a.uc_money-a.debt_money AS wait_debt_money,
        a.paid_money,a.rcpt_money,a.paid_money-a.rcpt_money AS wait_paid_money
        FROM ipt i LEFT JOIN an_stat a ON a.an=i.an   
        WHERE i.confirm_discharge = "N"  AND (i.finance_transfer = "N" OR a.opd_wait_money <>"0" 
        OR a.paid_money-a.rcpt_money <>"0" ) GROUP BY i.an 
        ORDER BY a.opd_wait_money DESC,i.ward,wait_paid_money DESC) AS a');         

    foreach ($sql3 as $row){ 
        $not_transfer=$row->not_transfer;
        $wait_paid_money=$row->wait_paid_money;
        $sum_wait_paid_money=$row->sum_wait_paid_money;
    }

    $sql4 = DB::connection('hosxp')->select('
        SELECT CASE WHEN MONTH(i.dchdate)="10" THEN CONCAT("ต.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="11" THEN CONCAT("พ.ย. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="12" THEN CONCAT("ธ.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="1" THEN CONCAT("ม.ค ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="2" THEN CONCAT("ก.พ. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="3" THEN CONCAT("มี.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="4" THEN CONCAT("เม.ย. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="5" THEN CONCAT("พ.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="6" THEN CONCAT("มิ.ย. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="7" THEN CONCAT("ก.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="8" THEN CONCAT("ส.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="9" THEN CONCAT("ก.ย. ",YEAR(i.dchdate)+543)
        END AS "month",COUNT(DISTINCT i.an) AS an ,sum(a.admdate) AS admdate,        
        ROUND((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(i.dchdate))),2) AS "bed_occupancy",
        ROUND(((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate)))*60)/100,2) AS "active_bed",
		ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi,
        ROUND(SUM(i.adjrw),2) AS adjrw ,SUM(a.income-a.rcpt_money)/SUM(i.adjrw) AS "income_rw"  
        FROM an_stat a INNER JOIN ipt i ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND DATE(NOW())
        AND a.pdx NOT IN ("Z290","Z208")
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)');
    $month = array_column($sql4,'month');  
    $bed_occupancy = array_column($sql4,'bed_occupancy');

    $ip_normal = DB::connection('hosxp')->select('
        SELECT CASE WHEN MONTH(i.dchdate)="10" THEN CONCAT("ต.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="11" THEN CONCAT("พ.ย. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="12" THEN CONCAT("ธ.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="1" THEN CONCAT("ม.ค ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="2" THEN CONCAT("ก.พ. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="3" THEN CONCAT("มี.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="4" THEN CONCAT("เม.ย. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="5" THEN CONCAT("พ.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="6" THEN CONCAT("มิ.ย. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="7" THEN CONCAT("ก.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="8" THEN CONCAT("ส.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="9" THEN CONCAT("ก.ย. ",YEAR(i.dchdate)+543)
        END AS "month",COUNT(DISTINCT i.an) AS an ,sum(a.admdate) AS admdate,        
        ROUND((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(i.dchdate))),2) AS "bed_occupancy",
        ROUND(((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate)))*60)/100,2) AS "active_bed",
		ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi,
        ROUND(SUM(i.adjrw),2) AS adjrw ,SUM(a.income-a.rcpt_money)/SUM(i.adjrw) AS "income_rw"  
        FROM an_stat a INNER JOIN ipt i ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND DATE(NOW())
        AND a.pdx NOT IN ("Z290","Z208") AND i.ward NOT IN (SELECT ward FROM htp_report.lookup_ward WHERE ward_homeward = "Y")
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)');

    $ip_homeward = DB::connection('hosxp')->select('
        SELECT CASE WHEN MONTH(i.dchdate)="10" THEN CONCAT("ต.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="11" THEN CONCAT("พ.ย. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="12" THEN CONCAT("ธ.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="1" THEN CONCAT("ม.ค ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="2" THEN CONCAT("ก.พ. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="3" THEN CONCAT("มี.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="4" THEN CONCAT("เม.ย. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="5" THEN CONCAT("พ.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="6" THEN CONCAT("มิ.ย. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="7" THEN CONCAT("ก.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="8" THEN CONCAT("ส.ค. ",YEAR(i.dchdate)+543)
        WHEN MONTH(i.dchdate)="9" THEN CONCAT("ก.ย. ",YEAR(i.dchdate)+543)
        END AS "month",COUNT(DISTINCT i.an) AS an ,sum(a.admdate) AS admdate,        
        ROUND((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(i.dchdate))),2) AS "bed_occupancy",
        ROUND(((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate)))*60)/100,2) AS "active_bed",
		ROUND(SUM(i.adjrw)/COUNT(DISTINCT i.an),2) AS cmi,
        ROUND(SUM(i.adjrw),2) AS adjrw ,SUM(a.income-a.rcpt_money)/SUM(i.adjrw) AS "income_rw"  
        FROM an_stat a INNER JOIN ipt i ON a.an=i.an
        WHERE i.dchdate BETWEEN "'.$start_date.'" AND DATE(NOW())
        AND a.pdx NOT IN ("Z290","Z208") AND i.ward IN (SELECT ward FROM htp_report.lookup_ward WHERE ward_homeward = "Y")
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)');


    return view('dashboard.ipd_mornitor',compact('total','ipd','vip','lr','homeward','non_diagtext','non_icd10','not_transfer',
            'wait_paid_money','sum_wait_paid_money','sql4','month','bed_occupancy','ip_normal','ip_homeward'));
}

}
