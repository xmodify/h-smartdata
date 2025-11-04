<?php

namespace App\Http\Controllers\MophNotify;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ServiceController extends Controller
{
    public function service_night()
    {   
    //1. ดึงข้อมูลสรุปบริการ
        $service = DB::connection('hosxp')->selectOne("
            SELECT CURDATE() AS vstdate,
            COUNT(DISTINCT o.vn) AS total_visit,
            COUNT(DISTINCT CASE WHEN o.main_dep = '002' THEN o.vn END) AS opd,
            COUNT(DISTINCT CASE WHEN o.main_dep = '032' THEN o.vn END) AS ari,
            COUNT(DISTINCT CASE WHEN o.main_dep = '011' THEN o.vn END) AS ncd,
            COUNT(DISTINCT CASE WHEN o.main_dep = '033' THEN o.vn END) AS kidney_hos,
            COUNT(DISTINCT CASE WHEN o.main_dep = '024' THEN o.vn END) AS kidney_os,
            COUNT(DISTINCT er.vn) AS er,
            COUNT(DISTINCT ps.vn) AS physic,
            COUNT(DISTINCT hm.vn) AS health_med,
            COUNT(DISTINCT dt.vn) AS dental,
            COUNT(DISTINCT anc.vn) AS anc,
            COUNT(DISTINCT ipt1.an) AS admit,
            COUNT(DISTINCT ipt2.an) AS discharge,
            COUNT(DISTINCT ro.vn) AS referout,
            COUNT(DISTINCT ri.vn) AS referin,
            ipd.ipd_all,
            ipd.ipd_normal,
            ipd.ipd_vip,
            ipd.ipd_labor,
            ipd.homeward,
            bed.bed_qty,
            ROUND((ipd.ipd_all / bed.bed_qty) * 100, 2) AS occ_ipd_all_rate,
            ROUND((ipd.ipd_normal / bed.bed_normal) * 100, 2) AS occ_ipd_normal_rate,
            ROUND((ipd.ipd_vip / bed.bed_vip) * 100, 2) AS occ_ipd_vip_rate
            FROM ovst o
            LEFT JOIN opdscreen os ON os.vn = o.vn 
            LEFT JOIN er_regist er ON er.vn = o.vn
            LEFT JOIN physic_list ps ON ps.vn = o.vn
            LEFT JOIN health_med_service hm ON hm.vn = o.vn
            LEFT JOIN dtmain dt ON dt.vn = o.vn
            LEFT JOIN person_anc_service anc ON anc.vn = o.vn
            LEFT JOIN referout ro ON ro.vn = o.vn
            LEFT JOIN referin ri ON ri.vn = o.vn
            LEFT JOIN ipt ipt1 ON ipt1.regdate = CURDATE() AND ipt1.regtime BETWEEN '00:00:01' AND '07:59:59'
            LEFT JOIN ipt ipt2 ON ipt2.dchdate = CURDATE() AND ipt2.dchtime BETWEEN '00:00:01' AND '07:59:59'
            LEFT JOIN (SELECT 
                COUNT(DISTINCT an) AS ipd_all,
                SUM(ward = '01') AS ipd_normal,
                SUM(ward = '02') AS ipd_labor,
                SUM(ward IN ('03','08')) AS ipd_vip,
                SUM(ward = '06') AS homeward
                FROM ipt
                WHERE confirm_discharge = 'N'
                ) ipd ON 1=1
            LEFT JOIN ( SELECT  
                COUNT(DISTINCT b.bedno) AS bed_qty,
                SUM(r.ward = '01') AS bed_normal,
                SUM(r.ward IN ('03','08')) AS bed_vip
                FROM bedno b
                JOIN roomno r ON b.roomno = r.roomno
                WHERE b.export_code IS NOT NULL
                    AND r.ward NOT IN ('06')
                ) bed ON 1=1
            WHERE o.vstdate = CURDATE()
            AND TIME(os.update_datetime) BETWEEN '00:00:01' AND '07:59:59' ");    

    //2. สร้างข้อความสรุป
        $message = "สรุปข้อมูลบริการ " .DateThai(date('Y-m-d')) ."\n"
            ."เวรดึก 🕒 00.00-08.00 น." ."\n\n"
            ."OP " . $service->total_visit ." Visit" ."\n"
            ." - OPD " . $service->opd  ."\n"
            ." - ARI " . $service->ari ."\n"
            ." - NCD " . $service->ncd ."\n"
            ." - ER " . $service->er ."\n"
            ." - ฟอกไต รพ. " . $service->kidney_hos ."\n"
            ." - ฟอกไต เอกชน. " . $service->kidney_os ."\n"            
            ." - กายภาพบำบัด " . $service->physic ."\n"
            ." - แผนไทย " . $service->health_med ."\n"
            ." - ทันตกรรม " . $service->dental ."\n"
            ." - ฝากครรภ์ " . $service->anc ."\n"
            ." - Admit " . $service->admit ."\n"
            ." - Discharge " . $service->discharge ."\n"
            ." - ReferOUT " . $service->referout ."\n"
            ." - ReferIN " . $service->referin ."\n\n"      
           
            . "Admit อยู่ " . $service->ipd_all ." | occ " . $service->occ_ipd_all_rate ." %" ."\n"
            . " - สามัญ " . $service->ipd_normal ." | occ " . $service->occ_ipd_normal_rate ." %" ."\n"
            . " - VIP " . $service->ipd_vip ." | occ " . $service->occ_ipd_vip_rate ." %" ."\n"
            . " - LR " . $service->ipd_labor ." an" . "\n"
            . " - Homeward " . $service->homeward ." an" . "\n";

    //3. ดึงรายการ client จากตาราง moph_notify
        $clients = DB::table('moph_notify')
            ->whereIn('id', [1]) // 👈 เลือกเฉพาะ id ที่ต้องการส่ง
            ->get(['id', 'name', 'client_id', 'secret']);
        $endpoint = "https://morpromt2f.moph.go.th/api/notify/send";
        $results = [];

    //4. ส่งข้อความให้ทุก client
        foreach ($clients as $client) {
            $payload = [
                "messages" => [
                    [
                        "type" => "text",
                        "text" => "🏥{$message}"
                    ]
                ]
            ];
            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'client-key' => $client->client_id,
                    'secret-key' => $client->secret
                ])->post($endpoint, $payload);

                $results[] = [
                    'hospital' => $client->name,
                    'status' => $response->successful() ? '✅ success' : '❌ failed',
                    'http_code' => $response->status(),
                    'response' => $response->json()
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'hospital' => $client->name,
                    'status' => '⚠️ error',
                    'error' => $e->getMessage()
                ];
            }
            sleep(1); // ป้องกัน spam API
        }

    //5. ส่งผลลัพธ์กลับ
        return response()->json([
            'sent_at' => now()->toDateTimeString(),
            'total_clients' => $clients->count(),
            'results' => $results
        ]);
    }

// service_morning ##################################################################################################################
    public function service_morning()
        {   
    //1. ดึงข้อมูลสรุปบริการ
        $service = DB::connection('hosxp')->selectOne("
            SELECT CURDATE() AS vstdate,
            COUNT(DISTINCT o.vn) AS total_visit,
            COUNT(DISTINCT CASE WHEN o.main_dep = '002' THEN o.vn END) AS opd,
            COUNT(DISTINCT CASE WHEN o.main_dep = '032' THEN o.vn END) AS ari,
            COUNT(DISTINCT CASE WHEN o.main_dep = '011' THEN o.vn END) AS ncd,
            COUNT(DISTINCT CASE WHEN o.main_dep = '033' THEN o.vn END) AS kidney_hos,
            COUNT(DISTINCT CASE WHEN o.main_dep = '024' THEN o.vn END) AS kidney_os,
            COUNT(DISTINCT er.vn) AS er,
            COUNT(DISTINCT ps.vn) AS physic,
            COUNT(DISTINCT hm.vn) AS health_med,
            COUNT(DISTINCT dt.vn) AS dental,
            COUNT(DISTINCT anc.vn) AS anc,
            COUNT(DISTINCT ipt1.an) AS admit,
            COUNT(DISTINCT ipt2.an) AS discharge,
            COUNT(DISTINCT ro.vn) AS referout,
            COUNT(DISTINCT ri.vn) AS referin,
            ipd.ipd_all,
            ipd.ipd_normal,
            ipd.ipd_vip,
            ipd.ipd_labor,
            ipd.homeward,
            bed.bed_qty,
            ROUND((ipd.ipd_all / bed.bed_qty) * 100, 2) AS occ_ipd_all_rate,
            ROUND((ipd.ipd_normal / bed.bed_normal) * 100, 2) AS occ_ipd_normal_rate,
            ROUND((ipd.ipd_vip / bed.bed_vip) * 100, 2) AS occ_ipd_vip_rate
            FROM ovst o
            LEFT JOIN opdscreen os ON os.vn = o.vn 
            LEFT JOIN er_regist er ON er.vn = o.vn
            LEFT JOIN physic_list ps ON ps.vn = o.vn
            LEFT JOIN health_med_service hm ON hm.vn = o.vn
            LEFT JOIN dtmain dt ON dt.vn = o.vn
            LEFT JOIN person_anc_service anc ON anc.vn = o.vn
            LEFT JOIN referout ro ON ro.vn = o.vn
            LEFT JOIN referin ri ON ri.vn = o.vn
            LEFT JOIN ipt ipt1 ON ipt1.regdate = CURDATE() AND ipt1.regtime BETWEEN '08:00:01' AND '15:59:59'
            LEFT JOIN ipt ipt2 ON ipt2.dchdate = CURDATE() AND ipt2.dchtime BETWEEN '08:00:01' AND '15:59:59'
            LEFT JOIN (SELECT 
                COUNT(DISTINCT an) AS ipd_all,
                SUM(ward = '01') AS ipd_normal,
                SUM(ward = '02') AS ipd_labor,
                SUM(ward IN ('03','08')) AS ipd_vip,
                SUM(ward = '06') AS homeward
                FROM ipt
                WHERE confirm_discharge = 'N'
                ) ipd ON 1=1
            LEFT JOIN ( SELECT  
                COUNT(DISTINCT b.bedno) AS bed_qty,
                SUM(r.ward = '01') AS bed_normal,
                SUM(r.ward IN ('03','08')) AS bed_vip
                FROM bedno b
                JOIN roomno r ON b.roomno = r.roomno
                WHERE b.export_code IS NOT NULL
                    AND r.ward NOT IN ('06')
                ) bed ON 1=1
            WHERE o.vstdate = CURDATE()
            AND TIME(os.update_datetime) BETWEEN '08:00:01' AND '15:59:59' ");    

    //2. สร้างข้อความสรุป
        $message = "สรุปข้อมูลบริการ " .DateThai(date('Y-m-d')) ."\n"
            ."เวรเช้า 🕒 08.00-16.00 น." ."\n"
             ."OP " . $service->total_visit ." Visit" ."\n"
            ." - OPD " . $service->opd  ."\n"
            ." - ARI " . $service->ari ."\n"
            ." - NCD " . $service->ncd ."\n"
            ." - ER " . $service->er ."\n"
            ." - ฟอกไต รพ. " . $service->kidney_hos ."\n"
            ." - ฟอกไต เอกชน. " . $service->kidney_os ."\n"            
            ." - กายภาพบำบัด " . $service->physic ."\n"
            ." - แผนไทย " . $service->health_med ."\n"
            ." - ทันตกรรม " . $service->dental ."\n"
            ." - ฝากครรภ์ " . $service->anc ."\n"
            ." - Admit " . $service->admit ."\n"
            ." - Discharge " . $service->discharge ."\n"
            ." - ReferOUT " . $service->referout ."\n"
            ." - ReferIN " . $service->referin ."\n\n"      
           
            . "Admit อยู่ " . $service->ipd_all ." | occ " . $service->occ_ipd_all_rate ." %" ."\n"
            . " - สามัญ " . $service->ipd_normal ." | occ " . $service->occ_ipd_normal_rate ." %" ."\n"
            . " - VIP " . $service->ipd_vip ." | occ " . $service->occ_ipd_vip_rate ." %" ."\n"
            . " - LR " . $service->ipd_labor ." an" . "\n"
            . " - Homeward " . $service->homeward ." an" . "\n";

    //3. ดึงรายการ client จากตาราง moph_notify
        $clients = DB::table('moph_notify')
            ->whereIn('id', [1]) // 👈 เลือกเฉพาะ id ที่ต้องการส่ง
            ->get(['id', 'name', 'client_id', 'secret']);
        $endpoint = "https://morpromt2f.moph.go.th/api/notify/send";
        $results = [];

    //4. ส่งข้อความให้ทุก client
        foreach ($clients as $client) {
            $payload = [
                "messages" => [
                    [
                        "type" => "text",
                        "text" => "🏥{$message}"
                    ]
                ]
            ];
            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'client-key' => $client->client_id,
                    'secret-key' => $client->secret
                ])->post($endpoint, $payload);

                $results[] = [
                    'hospital' => $client->name,
                    'status' => $response->successful() ? '✅ success' : '❌ failed',
                    'http_code' => $response->status(),
                    'response' => $response->json()
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'hospital' => $client->name,
                    'status' => '⚠️ error',
                    'error' => $e->getMessage()
                ];
            }
            sleep(1); // ป้องกัน spam API
        }

    //5. ส่งผลลัพธ์กลับ
        return response()->json([
            'sent_at' => now()->toDateTimeString(),
            'total_clients' => $clients->count(),
            'results' => $results
        ]);
    }
// service_afternoon ##################################################################################################################
    public function service_afternoon()
        {   
    //1. ดึงข้อมูลสรุปบริการ
        $service = DB::connection('hosxp')->selectOne("
            SELECT CURDATE() - INTERVAL 1 DAY AS vstdate,
            COUNT(DISTINCT o.vn) AS total_visit,
            COUNT(DISTINCT CASE WHEN o.main_dep = '002' THEN o.vn END) AS opd,
            COUNT(DISTINCT CASE WHEN o.main_dep = '032' THEN o.vn END) AS ari,
            COUNT(DISTINCT CASE WHEN o.main_dep = '011' THEN o.vn END) AS ncd,
            COUNT(DISTINCT CASE WHEN o.main_dep = '033' THEN o.vn END) AS kidney_hos,
            COUNT(DISTINCT CASE WHEN o.main_dep = '024' THEN o.vn END) AS kidney_os,
            COUNT(DISTINCT er.vn) AS er,
            COUNT(DISTINCT ps.vn) AS physic,
            COUNT(DISTINCT hm.vn) AS health_med,
            COUNT(DISTINCT dt.vn) AS dental,
            COUNT(DISTINCT anc.vn) AS anc,
            COUNT(DISTINCT ipt1.an) AS admit,
            COUNT(DISTINCT ipt2.an) AS discharge,
            COUNT(DISTINCT ro.vn) AS referout,
            COUNT(DISTINCT ri.vn) AS referin,
            ipd.ipd_all,
            ipd.ipd_normal,
            ipd.ipd_vip,
            ipd.ipd_labor,
            ipd.homeward,
            bed.bed_qty,
            ROUND((ipd.ipd_all / bed.bed_qty) * 100, 2) AS occ_ipd_all_rate,
            ROUND((ipd.ipd_normal / bed.bed_normal) * 100, 2) AS occ_ipd_normal_rate,
            ROUND((ipd.ipd_vip / bed.bed_vip) * 100, 2) AS occ_ipd_vip_rate
            FROM ovst o
            LEFT JOIN opdscreen os ON os.vn = o.vn 
            LEFT JOIN er_regist er ON er.vn = o.vn
            LEFT JOIN physic_list ps ON ps.vn = o.vn
            LEFT JOIN health_med_service hm ON hm.vn = o.vn
            LEFT JOIN dtmain dt ON dt.vn = o.vn
            LEFT JOIN person_anc_service anc ON anc.vn = o.vn
            LEFT JOIN referout ro ON ro.vn = o.vn
            LEFT JOIN referin ri ON ri.vn = o.vn
            LEFT JOIN ipt ipt1 ON ipt1.regdate = CURDATE() - INTERVAL 1 DAY AND ipt1.regtime BETWEEN '16:00:01' AND '23:59:59'
            LEFT JOIN ipt ipt2 ON ipt2.dchdate = CURDATE() - INTERVAL 1 DAY AND ipt2.dchtime BETWEEN '16:00:01' AND '23:59:59'
            LEFT JOIN (SELECT 
                COUNT(DISTINCT an) AS ipd_all,
                SUM(ward = '01') AS ipd_normal,
                SUM(ward = '02') AS ipd_labor,
                SUM(ward IN ('03','08')) AS ipd_vip,
                SUM(ward = '06') AS homeward
                FROM ipt
                WHERE confirm_discharge = 'N'
                ) ipd ON 1=1
            LEFT JOIN ( SELECT  
                COUNT(DISTINCT b.bedno) AS bed_qty,
                SUM(r.ward = '01') AS bed_normal,
                SUM(r.ward IN ('03','08')) AS bed_vip
                FROM bedno b
                JOIN roomno r ON b.roomno = r.roomno
                WHERE b.export_code IS NOT NULL
                    AND r.ward NOT IN ('06')
                ) bed ON 1=1
            WHERE o.vstdate = CURDATE() - INTERVAL 1 DAY
            AND TIME(os.update_datetime) BETWEEN '16:00:01' AND '23:59:59' ");    

    //2. สร้างข้อความสรุป
        $message = "สรุปข้อมูลบริการ " .DateThai(date("Y-m-d", strtotime("-1 day"))) ."\n"
            ."เวรบ่าย 🕒 16.00-24.00 น." ."\n"
             ."OP " . $service->total_visit ." Visit" ."\n"
            ." - OPD " . $service->opd  ."\n"
            ." - ARI " . $service->ari ."\n"
            ." - NCD " . $service->ncd ."\n"
            ." - ER " . $service->er ."\n"
            ." - ฟอกไต รพ. " . $service->kidney_hos ."\n"
            ." - ฟอกไต เอกชน. " . $service->kidney_os ."\n"            
            ." - กายภาพบำบัด " . $service->physic ."\n"
            ." - แผนไทย " . $service->health_med ."\n"
            ." - ทันตกรรม " . $service->dental ."\n"
            ." - ฝากครรภ์ " . $service->anc ."\n"
            ." - Admit " . $service->admit ."\n"
            ." - Discharge " . $service->discharge ."\n"
            ." - ReferOUT " . $service->referout ."\n"
            ." - ReferIN " . $service->referin ."\n\n"      
           
            . "Admit อยู่ " . $service->ipd_all ." | occ " . $service->occ_ipd_all_rate ." %" ."\n"
            . " - สามัญ " . $service->ipd_normal ." | occ " . $service->occ_ipd_normal_rate ." %" ."\n"
            . " - VIP " . $service->ipd_vip ." | occ " . $service->occ_ipd_vip_rate ." %" ."\n"
            . " - LR " . $service->ipd_labor ." an" . "\n"
            . " - Homeward " . $service->homeward ." an" . "\n";

    //3. ดึงรายการ client จากตาราง moph_notify
        $clients = DB::table('moph_notify')
            ->whereIn('id', [1]) // 👈 เลือกเฉพาะ id ที่ต้องการส่ง
            ->get(['id', 'name', 'client_id', 'secret']);
        $endpoint = "https://morpromt2f.moph.go.th/api/notify/send";
        $results = [];

    //4. ส่งข้อความให้ทุก client
        foreach ($clients as $client) {
            $payload = [
                "messages" => [
                    [
                        "type" => "text",
                        "text" => "🏥{$message}"
                    ]
                ]
            ];
            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'client-key' => $client->client_id,
                    'secret-key' => $client->secret
                ])->post($endpoint, $payload);

                $results[] = [
                    'hospital' => $client->name,
                    'status' => $response->successful() ? '✅ success' : '❌ failed',
                    'http_code' => $response->status(),
                    'response' => $response->json()
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'hospital' => $client->name,
                    'status' => '⚠️ error',
                    'error' => $e->getMessage()
                ];
            }
            sleep(1); // ป้องกัน spam API
        }

    //5. ส่งผลลัพธ์กลับ
        return response()->json([
            'sent_at' => now()->toDateTimeString(),
            'total_clients' => $clients->count(),
            'results' => $results
        ]);
    }

}


