<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Nhso_Endpoint;
use App\Models\Nhso_Endpoint_Indiv;

class Dashboard_DigitalhealthController extends Controller 
{
//Check Login
    public function __construct()
    {
        $this->middleware('auth')->except(['nhso_endpoint_pull_daily','digitalhealth','opd_mornitor','ipd_mornitor']);
    }

//hso_endpoint---------------------------------------------------------------------------------------
    public function nhso_endpoint(Request $request)
    {
        $start_date = $request->start_date ?: Session::get('start_date') ?: date('Y-m-d');
        $end_date = $request->end_date ?: Session::get('end_date') ?: date('Y-m-d');
        // อัปเดตค่าเก็บใน Session เผื่อครั้งถัดไป
        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        $sql=DB::select('
            SELECT * FROM nhso_endpoint_indiv WHERE vstdate BETWEEN ? AND ?'
            ,[$start_date,$end_date]);

        return view('dashboard.nhso_endpoint',compact('start_date','end_date','sql'));        
    }

//nhso_endpoint_pull----------------------------------------------------------------------------------------------
    public function nhso_endpoint_pull(Request $request)
    {   
        set_time_limit(600);
        $vstdate = $request->input('vstdate') ?? now()->format('Y-m-d'); 
        $hosxp = DB::connection('hosxp')->select('
            SELECT o.vn, o.hn, pt.cid, vp.auth_code
            FROM ovst o
            INNER JOIN visit_pttype vp ON vp.vn = o.vn 
            LEFT JOIN patient pt ON pt.hn = o.hn
            WHERE o.vstdate = ?
            AND vp.auth_code NOT LIKE "EP%" 
            AND pt.cid NOT IN (SELECT cid FROM htp_report.nhso_endpoint_indiv WHERE vstdate = ? AND cid IS NOT NULL)'
            , [$vstdate,$vstdate]);  

        $cids = array_column($hosxp, 'cid');      
        $token = DB::table('main_setting')
            ->where('name', 'token_authen_kiosk_nhso')
            ->value('value'); 
    
        // วนทีละก้อน (chunk) ก้อนละ 10 CID
        foreach (array_chunk($cids, 10) as $chunk) {
            foreach ($chunk as $cid) {
                try {
                    $response = Http::timeout(5) // สูงสุดรอ 5 วิ ต่อ 1 request
                        ->withToken($token)
                        ->acceptJson()
                        ->get('https://authenucws.nhso.go.th/authencodestatus/api/check-authen-status', [
                            'personalId'  => $cid,
                            'serviceDate' => $vstdate,
                        ]);

                    if ($response->failed()) {
                        \Log::warning("ดึงข้อมูลไม่สำเร็จสำหรับ CID: {$cid}", [
                            'status' => $response->status(),
                            'body'   => $response->body(),
                        ]);
                        continue;
                    }

                    $result = $response->json();

                    // กันกรณี response รูปแบบไม่ครบ
                    if (!is_array($result) || !isset($result['firstName']) || empty($result['serviceHistories'])) {
                        continue;
                    }

                    $firstName     = $result['firstName'] ?? null;
                    $lastName      = $result['lastName'] ?? null;
                    $mainInscl     = $result['mainInscl']['id']   ?? null;
                    $mainInsclName = $result['mainInscl']['name'] ?? null;
                    $subInscl      = $result['subInscl']['id']    ?? null;
                    $subInsclName  = $result['subInscl']['name']  ?? null;

                    foreach ($result['serviceHistories'] as $row) {
                        if (!is_array($row)) continue;

                        $serviceDateTime = $row['serviceDateTime'] ?? null;
                        $sourceChannel   = $row['sourceChannel']   ?? '';
                        $claimCode       = $row['claimCode']       ?? null;
                        $claimType       = $row['service']['code'] ?? null;

                        if (!$claimCode) continue;

                        $exists = Nhso_Endpoint_Indiv::where('cid', $cid)
                            ->where('claimCode', $claimCode)
                            ->exists();

                        if ($exists) {
                            // อัปเดตเฉพาะ claimType ตามลอจิกเดิม
                            Nhso_Endpoint_Indiv::where('cid', $cid)
                                ->where('claimCode', $claimCode)
                                ->update([
                                    'claimType' => $claimType,
                                ]);
                        } elseif ($sourceChannel === 'ENDPOINT' || $claimType === 'PG0140001') {
                            Nhso_Endpoint_Indiv::create([
                                'cid'            => $cid,
                                'firstName'      => $firstName,
                                'lastName'       => $lastName,
                                'mainInscl'      => $mainInscl,
                                'mainInsclName'  => $mainInsclName,
                                'subInscl'       => $subInscl,
                                'subInsclName'   => $subInsclName,
                                'serviceDateTime'=> $serviceDateTime,
                                'vstdate'        => $serviceDateTime ? date('Y-m-d', strtotime($serviceDateTime)) : $vstdate,
                                'sourceChannel'  => $sourceChannel,
                                'claimCode'      => $claimCode,
                                'claimType'      => $claimType,
                            ]);
                        }
                    }
                } catch (\Throwable $e) {
                    \Log::error("ข้อผิดพลาดระหว่างเรียก สปสช. สำหรับ CID: {$cid}", [
                        'exception' => $e->getMessage(),
                    ]);
                    continue;
                }
            }

            // หน่วงเล็กน้อยระหว่างแต่ละก้อน เพื่อกัน rate limit/ภาระระบบปลายทาง
            usleep(300000); // 0.3 วินาที (ปรับตามเหมาะสม: 300ms–1s)
        }
    
        return response()->json(['success' => true, 'message' => 'ดึงข้อมูลจาก สปสช สำเร็จ' ]);
    }
//nhso_endpoint_pull_indiv----------------------------------------------------------------------------------------------
    public function nhso_endpoint_pull_indiv(Request $request, $vstdate, $cid)
    {
        $token = DB::table('main_setting')
            ->where('name', 'token_authen_kiosk_nhso')
            ->value('value');

        // ตรวจสอบ token
        if (!$token) {
            return response()->json(['error' => 'Token not found'], 500);
        }

        // ส่ง request ไปยัง NHSO API
        $response = Http::withToken($token)
            ->acceptJson()
            ->get("https://authenucws.nhso.go.th/authencodestatus/api/check-authen-status", [
                'personalId' => $cid,
                'serviceDate' => $vstdate
            ]);

        if ($response->failed()) {
            return response()->json(['error' => 'NHSO API request failed', 'message' => $response->body()], 500);
        }

        $result = $response->json();

        if (!isset($result['firstName']) || !isset($result['serviceHistories'])) {
            return response()->json(['error' => 'Invalid data from NHSO API'], 500);
        }

        $firstName = $result['firstName'];
        $lastName = $result['lastName'];
        $mainInscl = $result['mainInscl']['id'] ?? '';
        $mainInsclName = $result['mainInscl']['name'] ?? '';
        $subInscl = $result['subInscl']['id'] ?? '';
        $subInsclName = $result['subInscl']['name'] ?? '';

        $services = $result['serviceHistories'];

        foreach ($services as $row) {
            $serviceDateTime = $row['serviceDateTime'] ?? null;
            $sourceChannel = $row['sourceChannel'] ?? '';
            $claimCode = $row['claimCode'] ?? null;
            $claimType = $row['service']['code'] ?? null;

            if (!$claimCode || !$claimType) {
                continue; // ข้ามรายการที่ข้อมูลไม่ครบ
            }
             if (!($sourceChannel === 'ENDPOINT' || $claimType === 'PG0140001')) {
                continue;
            }

            $indiv = Nhso_Endpoint_Indiv::firstOrNew([
                'cid' => $cid,
                'claimCode' => $claimCode,
            ]);

            // ถ้าเป็นรายการใหม่ หรือแก้ไขได้ตามเงื่อนไข
            if (!$indiv->exists || $sourceChannel === 'ENDPOINT' || $claimType === 'PG0140001') {
                $indiv->firstName = $firstName;
                $indiv->lastName = $lastName;
                $indiv->mainInscl = $mainInscl;
                $indiv->mainInsclName = $mainInsclName;
                $indiv->subInscl = $subInscl;
                $indiv->subInsclName = $subInsclName;
                $indiv->serviceDateTime = $serviceDateTime;
                $indiv->vstdate = date('Y-m-d', strtotime($serviceDateTime));
                $indiv->sourceChannel = $sourceChannel;
                $indiv->claimType = $claimType;
                $indiv->save();
            }
        }

    //    return back();
    return response()->json(['success' => true]);
    
    }
//Create Digitalheal--------------------------------------------------------------------------------------------------------
public function digitalhealth(Request $request )
{
    ini_set('max_execution_time', 300); // เพิ่มเป็น 5 นาที

    $budget_year_select = DB::connection('backoffice')->table('budget_year')
        ->select('LEAVE_YEAR_ID', 'LEAVE_YEAR_NAME')
        ->orderByDesc('LEAVE_YEAR_ID')
        ->limit(7)
        ->get();
    $budget_year_last = DB::connection('backoffice')->table('budget_year')
        ->whereDate('DATE_BEGIN', '<=', now())
        ->whereDate('DATE_END', '>=', now())
        ->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year ?: $budget_year_last;
    $year = DB::connection('backoffice')->table('budget_year')
        ->where('LEAVE_YEAR_ID', $budget_year)
        ->first(['DATE_BEGIN', 'DATE_END']);
    $start_date = $year->DATE_BEGIN ?? null;
    $end_date   = $year->DATE_END ?? null;
    
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
        WHERE a.confirm_date BETWEEN ? AND ?
        GROUP BY MONTH(confirm_date)
        ORDER BY YEAR(confirm_date),MONTH(confirm_date)',[$start_date,$end_date]);

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
        WHERE vstdate BETWEEN ? AND ?
        AND (an ="" OR an IS NULL)          
        GROUP BY MONTH(vstdate)  
        ORDER BY YEAR(vstdate) , MONTH(vstdate)',[$start_date,$end_date]);

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
        WHERE o.vstdate BETWEEN ? AND ?
        AND p.hipdata_code = "UCS" AND (o.an ="" OR o.an IS NULL) 
        GROUP BY MONTH(o.vstdate)
        ORDER BY YEAR(o.vstdate),MONTH(o.vstdate)',[$start_date,$end_date]);

    return view('dashboard.digitalhealth',compact('budget_year_select','budget_year',
    'moph_appointment','telehealth','opd_ucs'));
} 

public function opd_mornitor(Request $request )
{
    $monitor=DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS total,IFNULL(SUM(CASE WHEN endpoint<>"" THEN 1 ELSE 0 END),0) AS "endpoint",
        IFNULL(SUM(CASE WHEN hipdata_code="OFC" THEN 1 ELSE 0 END),0) AS "ofc",
        IFNULL(SUM(CASE WHEN hipdata_code="OFC" AND edc_approve_list_text <> "" THEN 1 ELSE 0 END),0) AS "ofc_edc",
        IFNULL(SUM(CASE WHEN auth_code="" AND cid NOT LIKE "0%" AND pttype NOT IN ("10","11","12","13") THEN 1 ELSE 0 END),0) AS "non_authen",
        IFNULL(SUM(CASE WHEN hipdata_code IN ("UCS","SSS","STP") AND hospmain="" THEN 1 ELSE 0 END),0) AS "non_hospmain",
        IFNULL(SUM(CASE WHEN tb="Y" AND paidst = "02" AND income-paid_money <> 0 THEN 1 ELSE 0 END),0) AS "tb",
        IFNULL(SUM(CASE WHEN tb="Y" AND paidst = "02" AND income-paid_money <> 0 AND endpoint ="Y" THEN 1 ELSE 0 END),0) AS "tb_endpoint",
        IFNULL(SUM(CASE WHEN hipdata_code="UCS" AND hospmain NOT IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE in_province ="Y")
            AND income-paid_money <> 0 THEN 1 ELSE 0 END),0) AS "op_anywhere",
        IFNULL(SUM(CASE WHEN hipdata_code="UCS" AND hospmain NOT IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE in_province ="Y")
            AND income-paid_money <> 0 AND endpoint ="Y" THEN 1 ELSE 0 END),0) AS "op_anywhere_endpoint",
        IFNULL(SUM(CASE WHEN hipdata_code="UCS" AND kidney<>"" THEN 1 ELSE 0 END),0) AS "kidney",
        IFNULL(SUM(CASE WHEN hipdata_code="UCS" AND kidney<>"" AND endpoint ="Y" THEN 1 ELSE 0 END),0) AS "kidney_endpoint",
        IFNULL(SUM(CASE WHEN hipdata_code="UCS" AND hospmain IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE in_province ="Y") 
            AND uc_cr<>"" THEN 1 ELSE 0 END),0) AS "ucop_cr",
        IFNULL(SUM(CASE WHEN hipdata_code="UCS" AND hospmain IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE in_province ="Y") 
            AND uc_cr<>"" AND endpoint ="Y" THEN 1 ELSE 0 END),0) AS "ucop_cr_endpoint",
        IFNULL(SUM(CASE WHEN hipdata_code = "UCS" AND hospmain IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE in_province ="Y") 
            AND herb<>"" THEN 1 ELSE 0 END),0) AS "uc_herb",
        IFNULL(SUM(CASE WHEN hipdata_code = "UCS" AND hospmain IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE in_province ="Y") 
            AND herb<>"" AND endpoint ="Y" THEN 1 ELSE 0 END),0) AS "uc_herb_endpoint",
        IFNULL(SUM(CASE WHEN ppfs<>"" THEN 1 ELSE 0 END),0) AS "ppfs",
        IFNULL(SUM(CASE WHEN ppfs<>"" AND endpoint ="Y" THEN 1 ELSE 0 END),0) AS "ppfs_endpoint",
        IFNULL(SUM(CASE WHEN hipdata_code = "UCS" AND healthmed<>"" THEN 1 ELSE 0 END),0) AS "healthmed",
        IFNULL(SUM(CASE WHEN hipdata_code = "UCS" AND healthmed<>"" AND endpoint ="Y" THEN 1 ELSE 0 END),0) AS "healthmed_endpoint"
        FROM(SELECT o.vn,pt.cid,pt.nationality,vp.auth_code,p.pttype,p.paidst,p.hipdata_code,vp.hospmain,os.edc_approve_list_text,
        IF((x.vn <>"" OR l.vn<>""),"Y",NULL) AS tb,kidney.vn AS kidney,uc_cr.vn AS uc_cr,herb.vn AS herb,ppfs.vn AS ppfs,
        healthmed.vn AS healthmed,IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,v.income,v.paid_money
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN ovst_seq os ON os.vn=o.vn
        LEFT JOIN vn_stat v ON v.vn=o.vn
        LEFT JOIN xray_report x ON x.vn=o.vn AND x.xray_items_code IN ("10","46","70","71")
        LEFT JOIN (SELECT vn,lab_items_code FROM lab_head lh LEFT JOIN lab_order lo ON lo.lab_order_number=lh.lab_order_number 
        WHERE lo.lab_items_code IN ("167","169")) l ON l.vn=o.vn
        LEFT JOIN opitemrece kidney ON kidney.vn=o.vn AND kidney.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE kidney = "Y")
        LEFT JOIN opitemrece ppfs ON ppfs.vn=o.vn AND ppfs.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE ppfs = "Y")
        LEFT JOIN opitemrece uc_cr ON uc_cr.vn=o.vn AND uc_cr.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE uc_cr = "Y")
        LEFT JOIN opitemrece herb ON herb.vn=o.vn AND herb.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE herb32 = "Y")
        LEFT JOIN health_med_service healthmed ON healthmed.vn=o.vn
        LEFT JOIN htp_report.nhso_endpoint_indiv ep ON ep.cid=pt.cid AND ep.vstdate=o.vstdate AND ep.claimCode LIKE "EP%"
        WHERE o.vstdate = DATE(NOW()) AND o.an IS NULL GROUP BY o.vn) AS a');

    foreach ($monitor as $row){
        $total = $row->total;
        $endpoint =$row->endpoint;
        $ofc = $row->ofc;
        $ofc_edc = $row->ofc_edc;        
        $non_authen = $row->non_authen;
        $non_hospmain =$row->non_hospmain;
        $tb =$row->tb;
        $tb_endpoint =$row->tb_endpoint;
        $op_anywhere =$row->op_anywhere;
        $op_anywhere_endpoint =$row->op_anywhere_endpoint;
        $kidney =$row->kidney;
        $kidney_endpoint =$row->kidney_endpoint;
        $ucop_cr =$row->ucop_cr;
        $ucop_cr_endpoint =$row->ucop_cr_endpoint;
        $ppfs =$row->ppfs;
        $ppfs_endpoint =$row->ppfs_endpoint;
        $healthmed =$row->healthmed;
        $healthmed_endpoint =$row->healthmed_endpoint;
        $uc_herb =$row->uc_herb;
        $uc_herb_endpoint =$row->uc_herb_endpoint;
    }

    $admit_homeward = DB::connection('hosxp')->select('
        SELECT COUNT(DISTINCT o.an) AS homeward,COUNT(ep.claimCode) AS homeward_auth
        FROM ovst o INNER JOIN ipt i ON i.an = o.an 
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN htp_report.nhso_endpoint_indiv ep ON ep.cid=pt.cid AND ep.vstdate=o.vstdate AND ep.claimType = "PG0140001"
        WHERE o.vstdate = DATE(NOW())
        AND i.ward IN (SELECT ward FROM htp_report.lookup_ward WHERE ward_homeward = "Y")');
    foreach($admit_homeward as $row){
        $homeward = $row->homeward;
        $homeward_auth = $row->homeward_auth;
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

    return view('dashboard.opd_mornitor',compact('total','ofc','ofc_edc','endpoint','non_authen','non_hospmain','op_anywhere',
            'op_anywhere_endpoint','tb','tb_endpoint','kidney','kidney_endpoint','ucop_cr','ucop_cr_endpoint','ppfs',
            'ppfs_endpoint','homeward','homeward_auth','healthmed','healthmed_endpoint','uc_herb','uc_herb_endpoint',
            'vstdate','op_visit','opd_n','opd_m','opd_a','er_n','er_m','er_a','physic_n','physic_m','physic_a',
            'health_med_n','health_med_m','health_med_a','dent_n','dent_m','dent_a','kidney_hos_n','kidney_hos_m','kidney_hos_a',
            'kidney_os_n','kidney_os_m','kidney_os_a','anc_n','anc_m','anc_a','admit_n','admit_m','admit_a','refer_n','refer_m',
            'refer_a','operation_n','operation_m','operation_a'));}

#####################################################################################################################################
public function opd_mornitor_ofc(Request $request )
{
    ini_set('max_execution_time', 300); // เพิ่มเป็น 5 นาที

    $start_date = $request->start_date ?: date('Y-m-d');
    $end_date = $request->end_date ?: date('Y-m-d');

    $sql=DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,
        pt.cid,pt.mobile_phone_number,p.`name` AS pttype,vp.hospmain,v.income-v.paid_money AS debtor,
        v.pdx,IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,
        IFNULL(vp.Claim_Code,os.edc_approve_list_text) AS edc,IF(ppfs.vn <>"","Y",NULL) AS ppfs
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
		LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN ovst_seq os ON os.vn = o.vn
        LEFT JOIN vn_stat v ON v.vn = o.vn
		LEFT JOIN opitemrece ppfs ON ppfs.vn=o.vn AND ppfs.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE ppfs = "Y")
        LEFT JOIN htp_report.nhso_endpoint_indiv ep ON ep.cid=v.cid AND ep.vstdate=o.vstdate AND ep.claimCode LIKE "EP%"
        WHERE o.vstdate  BETWEEN ? AND ? AND p.hipdata_code = "OFC"
        GROUP BY o.vn ORDER BY o.vstdate,o.vsttime',[$start_date,$end_date]);

    return view('dashboard.opd_mornitor_ofc',compact('start_date','end_date','sql'));
}
//----------------------------------------------------------------------------------------------------------------------------------
public function opd_mornitor_non_authen(Request $request )
{
    ini_set('max_execution_time', 300); // เพิ่มเป็น 5 นาที

    $start_date = $request->start_date ?: date('Y-m-d');
    $end_date = $request->end_date ?: date('Y-m-d');

    $sql=DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,p.cid,p.mobile_phone_number,p.hometel,p1.`name` AS pttype,
        vp.hospmain,k.department,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y
        FROM ovst o
        LEFT JOIN vn_stat v ON v.vn=o.vn
        LEFT JOIN patient p ON p.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
        LEFT JOIN kskdepartment k ON k.depcode=o.main_dep
        WHERE o.vstdate BETWEEN ? AND ?
        AND vp.pttype NOT IN ("10","11","12","13")
        AND p.cid NOT LIKE "0%" AND vp.auth_code =""       
        GROUP BY o.vn ORDER BY o.vsttime',[$start_date,$end_date]);

    return view('dashboard.opd_mornitor_non_authen',compact('start_date','end_date','sql'));
}

public function opd_mornitor_non_hospmain(Request $request )
{
    ini_set('max_execution_time', 300); // เพิ่มเป็น 5 นาที

    $start_date = $request->start_date ?: date('Y-m-d');
    $end_date = $request->end_date ?: date('Y-m-d');

    $sql=DB::connection('hosxp')->select('
        SELECT IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,
        o.vstdate,o.vsttime,o.oqueue,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,
        pt.cid,pt.mobile_phone_number,p.`name` AS pttype,vp.hospmain        
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        WHERE o.vstdate BETWEEN ? AND ?
        AND p.hipdata_code IN ("UCS","SSS","STP") AND (vp.hospmain="" OR vp.hospmain IS NULL)
        GROUP BY o.vn ORDER BY o.vstdate,o.vsttime',[$start_date,$end_date]);

    return view('dashboard.opd_mornitor_non_hospmain',compact('start_date','end_date','sql'));
}

public function opd_mornitor_tb(Request $request )
{
    ini_set('max_execution_time', 300); // เพิ่มเป็น 5 นาที

    $start_date = $request->start_date ?: date('Y-m-d');
    $end_date = $request->end_date ?: date('Y-m-d');
    
    $sql=DB::connection('hosxp')->select('
        SELECT IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,o.vstdate,o.vsttime,
        o.oqueue,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,pt.cid,pt.mobile_phone_number,
        p.`name` AS pttype,vp.hospmain,v.income-v.paid_money AS debtor,k.department        
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN xray_report x ON x.vn=o.vn
        LEFT JOIN lab_head lh ON lh.vn=o.vn
		LEFT JOIN lab_order lo ON lo.lab_order_number=lh.lab_order_number
	    LEFT JOIN vn_stat v ON v.vn=o.vn	
        LEFT JOIN kskdepartment k ON k.depcode = o.cur_dep
        LEFT JOIN htp_report.nhso_endpoint_indiv ep ON ep.cid=pt.cid AND ep.vstdate=o.vstdate AND ep.claimCode LIKE "EP%"
        WHERE (o.an ="" OR o.an IS NULL) AND v.income-v.paid_money <> 0 AND o.vstdate  BETWEEN ? AND ?
        AND (x.xray_items_code IN ("10","46","70","71") OR lo.lab_items_code IN ("167","169")) AND p.paidst = "02"
        GROUP BY o.vn ORDER BY ep.claimCode DESC,o.vstdate,o.vsttime',[$start_date,$end_date]);

    return view('dashboard.opd_mornitor_tb',compact('start_date','end_date','sql'));
}

public function opd_mornitor_opanywhere(Request $request )
{
    ini_set('max_execution_time', 300); // เพิ่มเป็น 5 นาที

    $start_date = $request->start_date ?: date('Y-m-d');
    $end_date = $request->end_date ?: date('Y-m-d');

    $sql=DB::connection('hosxp')->select('
        SELECT IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,o.vstdate,o.vsttime,
        o.oqueue,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,pt.cid,pt.mobile_phone_number,
        p.`name` AS pttype,vp.hospmain,v.income-v.paid_money AS debtor,k.department 
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN kskdepartment k ON k.depcode = o.cur_dep
        LEFT JOIN vn_stat v ON v.vn = o.vn
		LEFT JOIN htp_report.nhso_endpoint_indiv ep ON ep.cid=pt.cid AND ep.vstdate=o.vstdate AND ep.claimCode LIKE "EP%"
        WHERE (o.an ="" OR o.an IS NULL) AND v.income-v.paid_money <> 0 AND o.vstdate BETWEEN ? AND ?
        AND p.hipdata_code = "UCS" AND vp.hospmain NOT IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE in_province ="Y")        
        GROUP BY o.vn ORDER BY ep.claimCode DESC,o.vstdate,o.vsttime',[$start_date,$end_date]);

    return view('dashboard.opd_mornitor_opanywhere',compact('start_date','end_date','sql'));
}

public function opd_mornitor_kidney(Request $request )
{
    ini_set('max_execution_time', 300); // เพิ่มเป็น 5 นาที

    $start_date = $request->start_date ?: date('Y-m-d');
    $end_date = $request->end_date ?: date('Y-m-d');

    $sql=DB::connection('hosxp')->select('
        SELECT IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,o.vstdate,o.vsttime,
        o.oqueue,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,pt.cid,pt.mobile_phone_number,
        p.`name` AS pttype,vp.hospmain,v.income-v.paid_money AS debtor,k.department 
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN kskdepartment k ON k.depcode = o.cur_dep				
        LEFT JOIN vn_stat v ON v.vn = o.vn
		LEFT JOIN opitemrece kidney ON kidney.vn=o.vn AND kidney.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE kidney = "Y")
        LEFT JOIN htp_report.nhso_endpoint_indiv ep ON ep.cid=pt.cid AND ep.vstdate=o.vstdate AND ep.claimCode LIKE "EP%"
        WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN ? AND ?
        AND p.hipdata_code = "UCS" AND kidney.vn <>""
        GROUP BY o.vn ORDER BY ep.claimCode DESC,o.vstdate,o.vsttime',[$start_date,$end_date]);

    return view('dashboard.opd_mornitor_kidney',compact('start_date','end_date','sql'));
}

public function opd_mornitor_ucop_cr(Request $request )
{
    ini_set('max_execution_time', 300); // เพิ่มเป็น 5 นาที

    $start_date = $request->start_date ?: date('Y-m-d');
    $end_date = $request->end_date ?: date('Y-m-d');

    $sql=DB::connection('hosxp')->select('
        SELECT IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,o.vstdate,o.vsttime,
        o.oqueue,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,pt.cid,pt.mobile_phone_number,
        p.`name` AS pttype,vp.hospmain,v.income-v.paid_money AS debtor,k.department 
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN kskdepartment k ON k.depcode = o.cur_dep				
        LEFT JOIN vn_stat v ON v.vn = o.vn
		LEFT JOIN opitemrece uc_cr ON uc_cr.vn=o.vn AND uc_cr.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE uc_cr = "Y")
        LEFT JOIN htp_report.nhso_endpoint_indiv ep ON ep.cid=pt.cid AND ep.vstdate=o.vstdate AND ep.claimCode LIKE "EP%"
        WHERE (o.an ="" OR o.an IS NULL) AND uc_cr.vn <>"" AND o.vstdate BETWEEN ? AND ?
        AND p.hipdata_code = "UCS" AND vp.hospmain IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE in_province ="Y") 
        GROUP BY o.vn ORDER BY ep.claimCode DESC,o.vstdate,o.vsttime',[$start_date,$end_date]);

    return view('dashboard.opd_mornitor_ucop_cr',compact('start_date','end_date','sql'));
}

public function opd_mornitor_ppfs(Request $request )
{
    ini_set('max_execution_time', 300); // เพิ่มเป็น 5 นาที

    $start_date = $request->start_date ?: date('Y-m-d');
    $end_date = $request->end_date ?: date('Y-m-d');
    
    $sql=DB::connection('hosxp')->select('
        SELECT IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,o.vstdate,o.vsttime,
        o.oqueue,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,pt.cid,pt.mobile_phone_number,
        p.`name` AS pttype,vp.hospmain,v.income-v.paid_money AS debtor,k.department 
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN kskdepartment k ON k.depcode = o.cur_dep				
        LEFT JOIN vn_stat v ON v.vn = o.vn
		LEFT JOIN opitemrece ppfs ON ppfs.vn=o.vn AND ppfs.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE ppfs = "Y")
        LEFT JOIN htp_report.nhso_endpoint_indiv ep ON ep.cid=pt.cid AND ep.vstdate=o.vstdate AND ep.claimCode LIKE "EP%"
        WHERE (o.an ="" OR o.an IS NULL) AND ppfs.vn <>"" AND o.vstdate BETWEEN ? AND ?         
        GROUP BY o.vn ORDER BY ep.claimCode DESC,o.vstdate,o.vsttime',[$start_date,$end_date]);
    return view('dashboard.opd_mornitor_ppfs',compact('start_date','end_date','sql'));
}

public function opd_mornitor_ucherb(Request $request )
{
    ini_set('max_execution_time', 300); // เพิ่มเป็น 5 นาที

    $start_date = $request->start_date ?: date('Y-m-d');
    $end_date = $request->end_date ?: date('Y-m-d');
    
    $sql=DB::connection('hosxp')->select('
        SELECT IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,o.vstdate,o.vsttime,
        o.oqueue,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,pt.cid,pt.mobile_phone_number,
        p.`name` AS pttype,vp.hospmain,v.income-v.paid_money AS debtor,k.department 
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN kskdepartment k ON k.depcode = o.cur_dep				
        LEFT JOIN vn_stat v ON v.vn = o.vn
		LEFT JOIN opitemrece herb ON herb.vn=o.vn AND herb.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE herb32 = "Y")
        LEFT JOIN htp_report.nhso_endpoint_indiv ep ON ep.cid=pt.cid AND ep.vstdate=o.vstdate AND ep.claimCode LIKE "EP%"
        WHERE (o.an ="" OR o.an IS NULL) AND herb.vn <>"" AND o.vstdate BETWEEN ? AND ?
        AND p.hipdata_code = "UCS" AND vp.hospmain IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE in_province ="Y")          
        GROUP BY o.vn ORDER BY ep.claimCode DESC,o.vstdate,o.vsttime',[$start_date,$end_date]);

    return view('dashboard.opd_mornitor_ucherb',compact('start_date','end_date','sql'));
}

public function opd_mornitor_homeward(Request $request )
{
    ini_set('max_execution_time', 300); // เพิ่มเป็น 5 นาที

    $start_date = $request->start_date ?: date('Y-m-d');
    $end_date = $request->end_date ?: date('Y-m-d');

    $sql=DB::connection('hosxp')->select('
        SELECT IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,o.vstdate,o.vsttime,
        o.oqueue,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,pt.cid,pt.mobile_phone_number,
        p.`name` AS pttype,vp.hospmain,k.department 
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN kskdepartment k ON k.depcode = o.cur_dep
		LEFT JOIN ipt i ON i.an=o.an AND i.ward IN (SELECT ward FROM htp_report.lookup_ward WHERE ward_homeward="Y")
        LEFT JOIN htp_report.nhso_endpoint_indiv ep ON ep.cid=pt.cid AND ep.vstdate=o.vstdate AND ep.claimType = "PG0140001" 
        WHERE o.an <>"" AND o.vstdate BETWEEN ? AND ?
		GROUP BY o.vn ORDER BY o.vsttime',[$start_date,$end_date]);

    return view('dashboard.opd_mornitor_homeward',compact('start_date','end_date','sql'));
}

public function opd_mornitor_healthmed(Request $request )
{
    ini_set('max_execution_time', 300); // เพิ่มเป็น 5 นาที

    $start_date = $request->start_date ?: date('Y-m-d');
    $end_date = $request->end_date ?: date('Y-m-d');

    $sql=DB::connection('hosxp')->select('
        SELECT IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,o.vstdate,o.vsttime,
        o.oqueue,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,pt.cid,pt.mobile_phone_number,
        p.`name` AS pttype,vp.hospmain,v.income-v.paid_money AS debtor,k.department ,
			GROUP_CONCAT(DISTINCT healthmed.health_med_operation) AS operation
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN kskdepartment k ON k.depcode = o.cur_dep				
        LEFT JOIN vn_stat v ON v.vn = o.vn
        LEFT JOIN (SELECT h.vn,CONCAT(h2.health_med_operation_item_name," [",h2.icd10tm,"]") AS health_med_operation 
            FROM health_med_service h
            LEFT JOIN health_med_service_operation h1 ON h1.health_med_service_id=h.health_med_service_id
            LEFT JOIN health_med_operation_item h2 ON h2.health_med_operation_item_id=h1.health_med_operation_item_id
            WHERE h.service_date BETWEEN ? AND ?
            GROUP BY h1.health_med_service_id,h1.health_med_operation_item_id) healthmed ON healthmed.vn=o.vn
        LEFT JOIN htp_report.nhso_endpoint_indiv ep ON ep.cid=pt.cid AND ep.vstdate=o.vstdate AND ep.claimCode LIKE "EP%"
        WHERE (o.an ="" OR o.an IS NULL) AND healthmed.vn <>"" AND o.vstdate BETWEEN ? AND ?
        AND p.hipdata_code = "UCS" AND vp.hospmain IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE in_province ="Y")          
        GROUP BY o.vn ORDER BY ep.claimCode DESC,o.vstdate,o.vsttime',[$start_date,$end_date,$start_date,$end_date]);

    return view('dashboard.opd_mornitor_healthmed',compact('start_date','end_date','sql'));
}

###############################################################################################################################
public function ipd_mornitor(Request $request )
{
    $budget_year_last = DB::connection('backoffice')->table('budget_year')
        ->whereDate('DATE_BEGIN', '<=', date('Y-m-d'))
        ->whereDate('DATE_END', '>=', date('Y-m-d'))
        ->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year ?: $budget_year_last;
    $start_date = DB::connection('backoffice')->table('budget_year')
        ->where('LEAVE_YEAR_ID', $budget_year)
        ->value('DATE_BEGIN');
   
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
        SELECT SUM(CASE WHEN (a.diag_text_list ="" OR a.diag_text_list IS NULL ) THEN 1 ELSE 0 END) AS non_diagtext,
        SUM(CASE WHEN (id.icd10 ="" OR id.icd10 IS NULL OR a.pdx = "" OR a.pdx IS NULL) THEN 1 ELSE 0 END) AS non_icd10
        FROM ipt i
        LEFT JOIN iptdiag id ON id.an = i.an AND id.diagtype = 1
		LEFT JOIN an_stat a ON a.an=i.an
        WHERE i.dchdate >= "'.$start_date.'" AND  i.ward NOT IN (SELECT ward FROM htp_report.lookup_ward WHERE ward_homeward = "Y") 
        AND (a.diag_text_list ="" OR a.diag_text_list IS NULL 				
		OR id.icd10 ="" OR id.icd10 IS NULL
		OR a.pdx = "" OR a.pdx IS NULL)');         

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
        WHERE i.dchdate BETWEEN ? AND DATE(NOW())
        AND a.pdx NOT IN ("Z290","Z208")
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)',[$start_date]);
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
        WHERE i.dchdate BETWEEN ? AND DATE(NOW())
        AND a.pdx NOT IN ("Z290","Z208") AND i.ward NOT IN (SELECT ward FROM htp_report.lookup_ward WHERE ward_homeward = "Y")
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)',[$start_date]);

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
        WHERE i.dchdate BETWEEN ? AND DATE(NOW())
        AND a.pdx NOT IN ("Z290","Z208") AND i.ward IN (SELECT ward FROM htp_report.lookup_ward WHERE ward_homeward = "Y")
        GROUP BY MONTH(i.dchdate)
        ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)',[$start_date]);


    return view('dashboard.ipd_mornitor',compact('total','ipd','vip','lr','homeward','non_diagtext','non_icd10','not_transfer',
            'wait_paid_money','sum_wait_paid_money','sql4','month','bed_occupancy','ip_normal','ip_homeward'));
}

}
