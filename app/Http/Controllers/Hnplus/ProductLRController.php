<?php

namespace App\Http\Controllers\Hnplus;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;  
use App\Models\Nurse_productivity_lr;

class ProductLRController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->only(['lr_report','lr_product_delete']); 
    }

//lr_product--------------------------------------------------------------------------------------------------------------------------
    public function lr_report(Request $request)
    {
        $start_date = $request->start_date ?: date('Y-m-d', strtotime("first day of this month"));
        $end_date = $request->end_date ?: date('Y-m-d');  
        
        $product=Nurse_productivity_lr::whereBetween('report_date',[$start_date, $end_date])
            ->orderBy('report_date', 'desc')->get(); 
        $product_summary=DB::select('
            SELECT CASE WHEN shift_time = "เวรเช้า" THEN "1" WHEN shift_time = "เวรบ่าย" THEN "2"
            WHEN shift_time = "เวรดึก" THEN "3" END AS "id",shift_time,COUNT(shift_time) AS shift_time_sum,
            (SUM(patient_all)+SUM(opd_normal)+SUM(opd_high)) AS patient_all,SUM(opd_normal) AS opd_normal,SUM(opd_high) AS opd_high,
            SUM(convalescent) AS convalescent,SUM(moderate_ill) AS moderate_ill,SUM(semi_critical_ill) AS semi_critical_ill,
            SUM(critical_ill) AS critical_ill,SUM(patient_hr) AS patient_hr,SUM(nurse_oncall) AS nurse_oncall,
            SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime,SUM(nurse_hr) AS nurse_hr,
            ((SUM(patient_hr)*100)/SUM(nurse_hr)) AS productivity,(SUM(patient_hr)/(SUM(patient_all)+SUM(opd_normal)+SUM(opd_high))) AS hhpuos,
            ((SUM(patient_all)+SUM(opd_normal)+SUM(opd_high))*(SUM(patient_hr)/(SUM(patient_all)+SUM(opd_normal)+SUM(opd_high)))
            *(1.4/7))/COUNT(shift_time) AS nurse_shift_time
            FROM nurse_productivity_lrs
            WHERE report_date BETWEEN ? AND ?
            GROUP BY shift_time ORDER BY id',[$start_date,$end_date]);

        // เตรียมข้อมูลสำหรับกราฟ
        $product_asc=Nurse_productivity_lr::whereBetween('report_date',[$start_date, $end_date])
            ->orderBy('report_date', 'asc')->get(); 
        $grouped = $product_asc->groupBy('report_date');
        $report_date = [];
        $night = [];
        $morning = [];
        $afternoon = [];
        foreach ($grouped as $date => $rows) {
            $report_date[] = DateThai($date);
            // ค้นหาค่า productivity ของแต่ละเวร
            $night[]     = optional($rows->firstWhere('shift_time', 'เวรดึก'))->productivity ?? 0;
            $morning[]   = optional($rows->firstWhere('shift_time', 'เวรเช้า'))->productivity ?? 0;
            $afternoon[] = optional($rows->firstWhere('shift_time', 'เวรบ่าย'))->productivity ?? 0;
        }

        $username=Auth::user()->username;
        $del_product=DB::table('users_access')->where('username',$username)->where('del_product','Y')->value("username"); 
        
        return view('hnplus.product.lr_report',compact('product_summary','product','start_date',
                'end_date','del_product','report_date','night','morning','afternoon'));        
    }

//product_delete----------------------------------------------------------------------------------------------------------------
    public function lr_product_delete($id)
    {
        $product=Nurse_productivity_lr::find($id)->delete();
        return redirect()->route('hnplus.product.lr_report')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

//แจ้งเตือนสถานะการณ์สรุปเวรดึก รัน 08.00 น.---------------------------------------------------------------------------------------------
    public function lr_night_notify()
    {
        $notify = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT i.an),0) AS patient_all,
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 1 THEN 1 ELSE 0 END),0) AS "convalescent",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 2 THEN 1 ELSE 0 END),0) AS "moderate_ill",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 3 THEN 1 ELSE 0 END),0) AS "semi_critical_ill",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 4 THEN 1 ELSE 0 END),0) AS "critical_ill",
            IFNULL(SUM(CASE WHEN (i.dch_severe_type_id IS NULL OR i.dch_severe_type_id ="")
            THEN 1 ELSE 0 END),0) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("02") AND i.confirm_discharge = "N"'); 
        $notify_opd_normal = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_normal FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority = "0"
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "00:00:00" AND "07:59:59"'); 
        $notify_opd_high = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_high FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority IN ("1","2")
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "00:00:00" AND "07:59:59"');    
            
        foreach ($notify_opd_normal as $row){
            $opd_normal= $row->opd_normal;    
        }
        foreach ($notify_opd_high as $row){
                $opd_high= $row->opd_high;   
        }
        foreach ($notify as $row) {
            $convalescent= $row->convalescent;
            $moderate_ill=$row->moderate_ill;
            $semi_critical_ill=$row->semi_critical_ill;
            $critical_ill=$row->critical_ill;
            $severe_type_null=$row->severe_type_null;
            $url = url('hnplus/product/lr_night');
        }
                
    //แจ้งเตือน Telegram

        $message = "🤰งานห้องคลอด LR" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 00.00-08.00 น. 🌙เวรดึก" ."\n" 
            ."🧍‍♀️ผู้ป่วยนอกในเวร " ."\n"
            ." -ความเร่งด่วนปกติ " .$opd_normal ." ราย" ."\n"   
            ." -ความเร่งด่วนมาก " .$opd_high ." ราย" ."\n"     
            ."🤰ผู้ป่วยสูติกรรมในเวร " ."\n"    
            ." -Convalescent " .$convalescent ." ราย" ."\n"
            ." -Moderate ill " .$moderate_ill ." ราย" ."\n"
            ." -Semi critical ill " .$semi_critical_ill ." ราย" ."\n" 
            ." -Critical ill " .$critical_ill ." ราย" ."\n" ."\n"
            ."บันทึก Productivity " ."\n"
            . $url ."\n";


        // ✅ ส่งข้อความ Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_lr')->value('value'));

        foreach ($chat_ids as $chat_id) {
                $url = "https://api.telegram.org/bot$token/sendMessage";
                $data = [
                    'chat_id' => $chat_id,
                    'text'    => $message
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);
                sleep(1);
        }

        return response()->json(['success' => 'success'], 200);    
    }

//lr_night------------------------------------------------------------------------------------------------------------------------
    public function lr_night()
    {
        $shift = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT i.an),0) AS patient_all,
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "1%" THEN 1 ELSE 0 END),0) AS "convalescent",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "2%" THEN 1 ELSE 0 END),0) AS "moderate_ill",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "3%" THEN 1 ELSE 0 END),0) AS "semi_critical_ill",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "4%" THEN 1 ELSE 0 END),0) AS "critical_ill",
            IFNULL(SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code ="")
            THEN 1 ELSE 0 END),0) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("02") AND i.confirm_discharge = "N"'); 
        $shift_opd_normal = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_normal FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority = "0"
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "00:00:00" AND "07:59:59"'); 
        $shift_opd_high = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_high FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority IN ("1","2")
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "00:00:00" AND "07:59:59"'); 

        return view('hnplus.product.lr_night',compact('shift','shift_opd_normal','shift_opd_high'));            
    }

//lr_night_save--------------------------------------------------------------------------------------------------------------------
    public function lr_night_save(Request $request)
    {
    // ✅ ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall'   => 'required|numeric',
            'nurse_partime'  => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder'       => 'required|string',
        ]);

        // ✅ เตรียมค่าที่ใช้ซ้ำ
        $opd_normal        = $request->opd_normal;
        $opd_high          = $request->opd_high;
        $convalescent      = $request->convalescent;
        $moderate_ill      = $request->moderate_ill;
        $semi_critical_ill = $request->semi_critical_ill;
        $critical_ill      = $request->critical_ill;

        // ✅ รวมผู้ป่วยทั้งหมด (IPD + OPD)
        $patient_all = max(1, $request->patient_all + $opd_normal + $opd_high);

        // ✅ รวมอัตรากำลังพยาบาล
        $nurse_total    = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        $nurse_total_hr = max(1, $nurse_total * 7); // ป้องกันหาร 0

        // ✅ คำนวณค่าทางสถิติ (ตามสูตรเดิม)
        $patient_hr = ($convalescent * 0.45)
            + ($moderate_ill * 1.17)
            + ($semi_critical_ill * 1.71)
            + ($critical_ill * 1.99)
            + ($opd_normal * 0.5)
            + ($opd_high * 1.4);

        $nurse_hr       = $nurse_total * 7;
        $productivity   = ($patient_hr * 100) / $nurse_total_hr;
        $hhpuos         = $patient_hr / $patient_all;
        $nurse_shift_time = $patient_all * $hhpuos * (1.4 / 7);

        // ✅ บันทึกข้อมูลลงฐานข้อมูล
        $productivity_lr = Nurse_productivity_lr::updateOrCreate(
            // 🔑 เงื่อนไขตรวจว่ามีข้อมูลเดิมไหม
            [
                'report_date' => $request->report_date,
                'shift_time'  => $request->shift_time,
            ],
            // ✏️ ข้อมูลสำหรับ update / create
            [
                'opd_normal'        => $opd_normal,
                'opd_high'          => $opd_high,
                'patient_all'       => $patient_all,
                'convalescent'      => $convalescent,
                'moderate_ill'      => $moderate_ill,
                'semi_critical_ill' => $semi_critical_ill,
                'critical_ill'      => $critical_ill,
                'patient_hr'        => $patient_hr,
                'nurse_oncall'      => $request->nurse_oncall,
                'nurse_partime'     => $request->nurse_partime,
                'nurse_fulltime'    => $request->nurse_fulltime,
                'nurse_hr'          => $nurse_hr,
                'productivity'      => $productivity,
                'hhpuos'            => $hhpuos,
                'nurse_shift_time'  => $nurse_shift_time,
                'recorder'          => $request->recorder,
                'note'              => $request->note,
            ]
        );

        // ✅ เตรียมข้อความแจ้ง Telegram
        $message = "🤰งานห้องคลอด LR" ."\n"
            ."วันที่ " . DateThai(date('Y-m-d')) ."\n"
            ."เวลา 00.00–08.00 น. 🌙เวรดึก" ."\n\n"
            ."🧍‍♀️ผู้ป่วยนอกในเวร:" ."\n"
            ." - ความเร่งด่วนปกติ: {$opd_normal} ราย" ."\n"
            ." - ความเร่งด่วนมาก: {$opd_high} ราย" ."\n\n"
            ."🤰ผู้ป่วยสูติกรรมในเวร:" ."\n"
            ." - Convalescent: {$convalescent} ราย" ."\n"
            ." - Moderate ill: {$moderate_ill} ราย" ."\n"
            ." - Semi critical ill: {$semi_critical_ill} ราย" ."\n"
            ." - Critical ill: {$critical_ill} ราย" ."\n\n"
            ."👩‍⚕️ อัตรากำลัง:" ."\n"
            ." - Oncall: {$request->nurse_oncall}" ."\n"
            ." - เสริม: {$request->nurse_partime}" ."\n"
            ." - ปกติ: {$request->nurse_fulltime}" ."\n\n"
            ."🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) ."\n"
            ."🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) ."\n"
            ."📊 Productivity: " . number_format($productivity, 2) ."\n"
            ."🧮 HHPUOS: " . number_format($hhpuos, 2) ."\n\n"
            ."✍️ ผู้บันทึก: {$request->recorder}";

        // ✅ ส่งข้อความ Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_lr_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text'    => $message,
            ]);
            usleep(500000); // พัก 0.5 วินาที
        }

        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรดึกเรียบร้อยแล้ว');
    }

//แจ้งเตือนสถานะการณ์สรุปเวรเช้า รัน 16.00 น.---------------------------------------------------------------------------------------------
    public function lr_morning_notify()
    {
        $notify = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT i.an),0) AS patient_all,
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 1 THEN 1 ELSE 0 END),0) AS "convalescent",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 2 THEN 1 ELSE 0 END),0) AS "moderate_ill",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 3 THEN 1 ELSE 0 END),0) AS "semi_critical_ill",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 4 THEN 1 ELSE 0 END),0) AS "critical_ill",
            IFNULL(SUM(CASE WHEN (i.dch_severe_type_id IS NULL OR i.dch_severe_type_id ="")
            THEN 1 ELSE 0 END),0) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("02") AND i.confirm_discharge = "N"'); 
        $notify_opd_normal = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_normal FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority = "0"
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "08:00:00" AND "15:59:59"'); 
        $notify_opd_high = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_high FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority IN ("1","2")
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "08:00:00" AND "15:59:59"');    
            
        foreach ($notify_opd_normal as $row){
            $opd_normal= $row->opd_normal;    
        }
        foreach ($notify_opd_high as $row){
                $opd_high= $row->opd_high;   
        }
        foreach ($notify as $row) {
            $convalescent= $row->convalescent;
            $moderate_ill=$row->moderate_ill;
            $semi_critical_ill=$row->semi_critical_ill;
            $critical_ill=$row->critical_ill;
            $severe_type_null=$row->severe_type_null;
            $url = url('hnplus/product/lr_morning');
        }
                
    //แจ้งเตือน Telegram

        $message = "🤰งานห้องคลอด LR" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 08.00-16.00 น. 🌅เวรเช้า" ."\n" 
            ."🧍‍♀️ผู้ป่วยนอกในเวร " ."\n"
            ." -ความเร่งด่วนปกติ " .$opd_normal ." ราย" ."\n"   
            ." -ความเร่งด่วนมาก " .$opd_high ." ราย" ."\n"     
            ."🤰ผู้ป่วยสูติกรรมในเวร " ."\n"    
            ." -Convalescent " .$convalescent ." ราย" ."\n"
            ." -Moderate ill " .$moderate_ill ." ราย" ."\n"
            ." -Semi critical ill " .$semi_critical_ill ." ราย" ."\n" 
            ." -Critical ill " .$critical_ill ." ราย" ."\n" ."\n"
            ."บันทึก Productivity " ."\n"
            . $url ."\n";


        // ✅ ส่งข้อความ Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_lr')->value('value'));

        foreach ($chat_ids as $chat_id) {
                $url = "https://api.telegram.org/bot$token/sendMessage";
                $data = [
                    'chat_id' => $chat_id,
                    'text'    => $message
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);
                sleep(1);
        }

        return response()->json(['success' => 'success'], 200);    
    }

//lr_morning-------------------------------------------------------------------------------------------------------------
    public function lr_morning()
    {
        $shift = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT i.an),0) AS patient_all,
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "1%" THEN 1 ELSE 0 END),0) AS "convalescent",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "2%" THEN 1 ELSE 0 END),0) AS "moderate_ill",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "3%" THEN 1 ELSE 0 END),0) AS "semi_critical_ill",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "4%" THEN 1 ELSE 0 END),0) AS "critical_ill",
            IFNULL(SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code ="")
            THEN 1 ELSE 0 END),0) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("02") AND i.confirm_discharge = "N"'); 
        $shift_opd_normal = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_normal FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority = "0"
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "08:00:00" AND "15:59:59"'); 
        $shift_opd_high = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_high FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority IN ("1","2")
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "08:00:00" AND "15:59:59"'); 

        return view('hnplus.product.lr_morning',compact('shift','shift_opd_normal','shift_opd_high'));            
    }

//lr_morning_save------------------------------------------------------------------------------------------------------------------
    public function lr_morning_save(Request $request)
    {
    // ✅ ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall'   => 'required|numeric',
            'nurse_partime'  => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder'       => 'required|string',
        ]);

        // ✅ เตรียมค่าที่ใช้ซ้ำ
        $opd_normal        = $request->opd_normal;
        $opd_high          = $request->opd_high;
        $convalescent      = $request->convalescent;
        $moderate_ill      = $request->moderate_ill;
        $semi_critical_ill = $request->semi_critical_ill;
        $critical_ill      = $request->critical_ill;

        // ✅ รวมผู้ป่วยทั้งหมด (IPD + OPD)
        $patient_all = max(1, $request->patient_all + $opd_normal + $opd_high);

        // ✅ รวมอัตรากำลังพยาบาล
        $nurse_total    = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        $nurse_total_hr = max(1, $nurse_total * 7); // ป้องกันหาร 0

        // ✅ คำนวณค่าทางสถิติ (ตามสูตรเดิม)
        $patient_hr = ($convalescent * 0.45)
            + ($moderate_ill * 1.17)
            + ($semi_critical_ill * 1.71)
            + ($critical_ill * 1.99)
            + ($opd_normal * 0.5)
            + ($opd_high * 1.4);

        $nurse_hr       = $nurse_total * 7;
        $productivity   = ($patient_hr * 100) / $nurse_total_hr;
        $hhpuos         = $patient_hr / $patient_all;
        $nurse_shift_time = $patient_all * $hhpuos * (1.4 / 7);

        // ✅ บันทึกข้อมูลลงฐานข้อมูล
        $productivity_lr = Nurse_productivity_lr::updateOrCreate(
            // 🔑 เงื่อนไขตรวจว่ามีข้อมูลเดิมไหม
            [
                'report_date' => $request->report_date,
                'shift_time'  => $request->shift_time,
            ],
            // ✏️ ข้อมูลสำหรับ update / create
            [
                'opd_normal'        => $opd_normal,
                'opd_high'          => $opd_high,
                'patient_all'       => $patient_all,
                'convalescent'      => $convalescent,
                'moderate_ill'      => $moderate_ill,
                'semi_critical_ill' => $semi_critical_ill,
                'critical_ill'      => $critical_ill,
                'patient_hr'        => $patient_hr,
                'nurse_oncall'      => $request->nurse_oncall,
                'nurse_partime'     => $request->nurse_partime,
                'nurse_fulltime'    => $request->nurse_fulltime,
                'nurse_hr'          => $nurse_hr,
                'productivity'      => $productivity,
                'hhpuos'            => $hhpuos,
                'nurse_shift_time'  => $nurse_shift_time,
                'recorder'          => $request->recorder,
                'note'              => $request->note,
            ]
        );

        // ✅ เตรียมข้อความแจ้ง Telegram
        $message = "🤰งานห้องคลอด LR" ."\n"
            ."วันที่ " . DateThai(date('Y-m-d')) ."\n"
            ."เวลา 08.00–16.00 น. 🌅เวรเช้า" ."\n\n"
            ."🧍‍♀️ผู้ป่วยนอกในเวร:" ."\n"
            ." - ความเร่งด่วนปกติ: {$opd_normal} ราย" ."\n"
            ." - ความเร่งด่วนมาก: {$opd_high} ราย" ."\n\n"
            ."🤰ผู้ป่วยสูติกรรมในเวร:" ."\n"
            ." - Convalescent: {$convalescent} ราย" ."\n"
            ." - Moderate ill: {$moderate_ill} ราย" ."\n"
            ." - Semi critical ill: {$semi_critical_ill} ราย" ."\n"
            ." - Critical ill: {$critical_ill} ราย" ."\n\n"
            ."👩‍⚕️ อัตรากำลัง:" ."\n"
            ." - Oncall: {$request->nurse_oncall}" ."\n"
            ." - เสริม: {$request->nurse_partime}" ."\n"
            ." - ปกติ: {$request->nurse_fulltime}" ."\n\n"
            ."🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) ."\n"
            ."🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) ."\n"
            ."📊 Productivity: " . number_format($productivity, 2) ."\n"
            ."🧮 HHPUOS: " . number_format($hhpuos, 2) ."\n\n"
            ."✍️ ผู้บันทึก: {$request->recorder}";

        // ✅ ส่งข้อความ Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_lr_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text'    => $message,
            ]);
            usleep(500000); // พัก 0.5 วินาที
        }

        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
    }

//แจ้งเตือนสถานะการณ์สรุปเวรบ่าย รัน 00.01 น.---------------------------------------------------------------------------------------------
    public function lr_afternoon_notify()
    {
        $notify = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT i.an),0) AS patient_all,
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 1 THEN 1 ELSE 0 END),0) AS "convalescent",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 2 THEN 1 ELSE 0 END),0) AS "moderate_ill",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 3 THEN 1 ELSE 0 END),0) AS "semi_critical_ill",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 4 THEN 1 ELSE 0 END),0) AS "critical_ill",
            IFNULL(SUM(CASE WHEN (i.dch_severe_type_id IS NULL OR i.dch_severe_type_id ="")
            THEN 1 ELSE 0 END),0) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("02") AND i.confirm_discharge = "N"'); 
        $notify_opd_normal = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_normal FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority = "0"
            AND o2.vstdate = date(DATE_ADD(now(), INTERVAL -1 DAY )) AND o2.vsttime BETWEEN "16:00:00" AND "23:59:59"'); 
        $notify_opd_high = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_high FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority IN ("1","2")
            AND o2.vstdate = date(DATE_ADD(now(), INTERVAL -1 DAY )) AND o2.vsttime BETWEEN "16:00:00" AND "23:59:59"');    
            
        foreach ($notify_opd_normal as $row){
            $opd_normal= $row->opd_normal;    
        }
        foreach ($notify_opd_high as $row){
                $opd_high= $row->opd_high;   
        }
        foreach ($notify as $row) {
            $convalescent= $row->convalescent;
            $moderate_ill=$row->moderate_ill;
            $semi_critical_ill=$row->semi_critical_ill;
            $critical_ill=$row->critical_ill;
            $severe_type_null=$row->severe_type_null;
            $url = url('hnplus/product/lr_morning');
        }
                
    //แจ้งเตือน Telegram

        $message = "🤰งานห้องคลอด LR" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d", strtotime("-1 day"))) ."\n"  
            ."เวลา 16.00-24.00 น. 🌇เวรบ่าย" ."\n" 
            ."🧍‍♀️ผู้ป่วยนอกในเวร " ."\n"
            ." -ความเร่งด่วนปกติ " .$opd_normal ." ราย" ."\n"   
            ." -ความเร่งด่วนมาก " .$opd_high ." ราย" ."\n"     
            ."🤰ผู้ป่วยสูติกรรมในเวร " ."\n"    
            ." -Convalescent " .$convalescent ." ราย" ."\n"
            ." -Moderate ill " .$moderate_ill ." ราย" ."\n"
            ." -Semi critical ill " .$semi_critical_ill ." ราย" ."\n" 
            ." -Critical ill " .$critical_ill ." ราย" ."\n" ."\n"
            ."บันทึก Productivity " ."\n"
            . $url ."\n";


        // ✅ ส่งข้อความ Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_lr')->value('value'));

        foreach ($chat_ids as $chat_id) {
                $url = "https://api.telegram.org/bot$token/sendMessage";
                $data = [
                    'chat_id' => $chat_id,
                    'text'    => $message
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);
                sleep(1);
        }

        return response()->json(['success' => 'success'], 200);    
    }

//lr_afternoon------------------------------------------------------------------------------------------------------------
    public function lr_afternoon()
    {
        $shift = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT i.an),0) AS patient_all,
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "1%" THEN 1 ELSE 0 END),0) AS "convalescent",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "2%" THEN 1 ELSE 0 END),0) AS "moderate_ill",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "3%" THEN 1 ELSE 0 END),0) AS "semi_critical_ill",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "4%" THEN 1 ELSE 0 END),0) AS "critical_ill",
            IFNULL(SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code ="")
            THEN 1 ELSE 0 END),0) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("02") AND i.confirm_discharge = "N"'); 
        $shift_opd_normal = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_normal FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority = "0"
            AND o2.vstdate = date(DATE_ADD(now(), INTERVAL -1 DAY )) AND o2.vsttime BETWEEN "16:00:00" AND "23:59:59"'); 
        $shift_opd_high = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_high FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority IN ("1","2")
            AND o2.vstdate = date(DATE_ADD(now(), INTERVAL -1 DAY )) AND o2.vsttime BETWEEN "16:00:00" AND "23:59:59"'); 

        return view('hnplus.product.lr_afternoon',compact('shift','shift_opd_normal','shift_opd_high'));            
    }

//lr_afternoon_save---------------------------------------------------------------------------------------------------------------
    public function lr_afternoon_save(Request $request)
       {
    // ✅ ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall'   => 'required|numeric',
            'nurse_partime'  => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder'       => 'required|string',
        ]);

        // ✅ เตรียมค่าที่ใช้ซ้ำ
        $opd_normal        = $request->opd_normal;
        $opd_high          = $request->opd_high;
        $convalescent      = $request->convalescent;
        $moderate_ill      = $request->moderate_ill;
        $semi_critical_ill = $request->semi_critical_ill;
        $critical_ill      = $request->critical_ill;

        // ✅ รวมผู้ป่วยทั้งหมด (IPD + OPD)
        $patient_all = max(1, $request->patient_all + $opd_normal + $opd_high);

        // ✅ รวมอัตรากำลังพยาบาล
        $nurse_total    = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        $nurse_total_hr = max(1, $nurse_total * 7); // ป้องกันหาร 0

        // ✅ คำนวณค่าทางสถิติ (ตามสูตรเดิม)
        $patient_hr = ($convalescent * 0.45)
            + ($moderate_ill * 1.17)
            + ($semi_critical_ill * 1.71)
            + ($critical_ill * 1.99)
            + ($opd_normal * 0.5)
            + ($opd_high * 1.4);

        $nurse_hr       = $nurse_total * 7;
        $productivity   = ($patient_hr * 100) / $nurse_total_hr;
        $hhpuos         = $patient_hr / $patient_all;
        $nurse_shift_time = $patient_all * $hhpuos * (1.4 / 7);

        // ✅ บันทึกข้อมูลลงฐานข้อมูล
        $productivity_lr = Nurse_productivity_lr::updateOrCreate(
            // 🔑 เงื่อนไขตรวจว่ามีข้อมูลเดิมไหม
            [
                'report_date' => $request->report_date,
                'shift_time'  => $request->shift_time,
            ],
            // ✏️ ข้อมูลสำหรับ update / create
            [
                'opd_normal'        => $opd_normal,
                'opd_high'          => $opd_high,
                'patient_all'       => $patient_all,
                'convalescent'      => $convalescent,
                'moderate_ill'      => $moderate_ill,
                'semi_critical_ill' => $semi_critical_ill,
                'critical_ill'      => $critical_ill,
                'patient_hr'        => $patient_hr,
                'nurse_oncall'      => $request->nurse_oncall,
                'nurse_partime'     => $request->nurse_partime,
                'nurse_fulltime'    => $request->nurse_fulltime,
                'nurse_hr'          => $nurse_hr,
                'productivity'      => $productivity,
                'hhpuos'            => $hhpuos,
                'nurse_shift_time'  => $nurse_shift_time,
                'recorder'          => $request->recorder,
                'note'              => $request->note,
            ]
        );

        // ✅ เตรียมข้อความแจ้ง Telegram
        $message = "🤰งานห้องคลอด LR" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d", strtotime("-1 day"))) ."\n"  
            ."เวลา 16.00–24.00 น. 🌇เวรบ่าย" ."\n\n"
            ."🧍‍♀️ผู้ป่วยนอกในเวร:" ."\n"
            ." - ความเร่งด่วนปกติ: {$opd_normal} ราย" ."\n"
            ." - ความเร่งด่วนมาก: {$opd_high} ราย" ."\n\n"
            ."🤰ผู้ป่วยสูติกรรมในเวร:" ."\n"
            ." - Convalescent: {$convalescent} ราย" ."\n"
            ." - Moderate ill: {$moderate_ill} ราย" ."\n"
            ." - Semi critical ill: {$semi_critical_ill} ราย" ."\n"
            ." - Critical ill: {$critical_ill} ราย" ."\n\n"
            ."👩‍⚕️ อัตรากำลัง:" ."\n"
            ." - Oncall: {$request->nurse_oncall}" ."\n"
            ." - เสริม: {$request->nurse_partime}" ."\n"
            ." - ปกติ: {$request->nurse_fulltime}" ."\n\n"
            ."🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) ."\n"
            ."🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) ."\n"
            ."📊 Productivity: " . number_format($productivity, 2) ."\n"
            ."🧮 HHPUOS: " . number_format($hhpuos, 2) ."\n\n"
            ."✍️ ผู้บันทึก: {$request->recorder}";

        // ✅ ส่งข้อความ Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_lr_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text'    => $message,
            ]);
            usleep(500000); // พัก 0.5 วินาที
        }

        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรบ่ายเรียบร้อยแล้ว');
    }

}
