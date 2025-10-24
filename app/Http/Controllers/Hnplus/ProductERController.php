<?php

namespace App\Http\Controllers\Hnplus;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;  
use App\Models\Nurse_productivity_er;

class ProductERController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->only(['er_report','er_product_delete']); 
    }

//er_product_delete--------------------------------------------------------------------------------------------------------------------------
    public function er_report(Request $request)
    {
        $start_date = $request->start_date ?: date('Y-m-d', strtotime("first day of this month"));
        $end_date = $request->end_date ?: date('Y-m-d');  
        
        $er_product=Nurse_productivity_er::whereBetween('report_date',[$start_date, $end_date])->get(); 
        $er_product_summary=DB::select('
                SELECT CASE WHEN shift_time = "เวรเช้า" THEN "1" WHEN shift_time = "เวรบ่าย" THEN "2"
                WHEN shift_time = "เวรดึก" THEN "3" END AS "id",shift_time,COUNT(shift_time) AS shift_time_sum,
                SUM(patient_all) AS patient_all, SUM(emergent) AS emergent,SUM(urgent) AS urgent,SUM(acute_illness) AS acute_illness,
                SUM(non_acute_illness) AS non_acute_illness,SUM(patient_hr) AS patient_hr,SUM(nurse_oncall) AS nurse_oncall,
                SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime, SUM(nurse_hr) AS nurse_hr,
                ((SUM(patient_hr)*100)/SUM(nurse_hr)) AS productivity,(SUM(patient_hr)/SUM(patient_all)) AS hhpuos,
                (SUM(patient_all)*(SUM(patient_hr)/SUM(patient_all))*(1.4/7))/COUNT(shift_time) AS nurse_shift_time
                FROM nurse_productivity_ers
                WHERE report_date BETWEEN ? AND ?
                GROUP BY shift_time ORDER BY id',[$start_date,$end_date]);
        $day_er_night=DB::select('
                SELECT report_date,ROUND(((SUM(patient_hr)*100)/SUM(nurse_hr)),2) AS productivity
                FROM nurse_productivity_ers
                WHERE report_date BETWEEN ? AND ? AND shift_time = "เวรดึก"
                GROUP BY report_date ORDER BY report_date ',[$start_date,$end_date]);
        $report_date = array_column($day_er_night,'report_date');
        $night = array_column($day_er_night,'productivity');
        $day_er_morning=DB::select('
                SELECT report_date,ROUND(((SUM(patient_hr)*100)/SUM(nurse_hr)),2) AS productivity
                FROM nurse_productivity_ers
                WHERE report_date BETWEEN ? AND ? AND shift_time = "เวรเช้า"
                GROUP BY report_date ORDER BY report_date ',[$start_date,$end_date]);
        $morning = array_column($day_er_morning,'productivity');
        $day_er_afternoon=DB::select('
                SELECT report_date,ROUND(((SUM(patient_hr)*100)/SUM(nurse_hr)),2) AS productivity
                FROM nurse_productivity_ers
                WHERE report_date BETWEEN ? AND ? AND shift_time = "เวรบ่าย"
                GROUP BY report_date ORDER BY report_date ',[$start_date,$end_date]);
        $afternoon = array_column($day_er_afternoon,'productivity');

        $username=Auth::user()->username;
        $del_product=DB::table('users_access')->where('username',$username)->where('del_product','Y')->value("username"); 
        
        return view('hnplus.product.er_report',compact('er_product_summary','er_product','start_date',
                'end_date','del_product','report_date','night','morning','afternoon'));        
    }

//er_product_delete----------------------------------------------------------------------------------------------------------------
    public function er_product_delete($id)
    {
        $er_product=Nurse_productivity_er::find($id)->delete();
        return redirect()->route('hnplus.product.er_report')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

//แจ้งเตือนสถานะการณ์สรุปเวรดึก รัน 08.00 น.---------------------------------------------------------------------------------------------
    public function er_night_notify()
    {
        $service = DB::connection('hosxp')->select("
            SELECT DATE(NOW()) AS vstdate,
                COALESCE(COUNT(DISTINCT vn), 0) AS visit,
                COALESCE(SUM(CASE WHEN er_emergency_type IN ('1','2') THEN 1 ELSE 0 END), 0) AS Emergent,
                COALESCE(SUM(CASE WHEN er_emergency_type = '3' THEN 1 ELSE 0 END), 0) AS Urgent,
                COALESCE(SUM(CASE WHEN er_emergency_type = '4' THEN 1 ELSE 0 END), 0) AS Acute_illness,
                COALESCE(SUM(CASE WHEN er_emergency_type = '5' THEN 1 ELSE 0 END), 0) AS Non_acute_illness
            FROM er_regist
            WHERE DATE(enter_er_time) = DATE(NOW())
            AND TIME(enter_er_time) BETWEEN '00:00:00' AND '07:59:59'");         

        foreach ($service as $row){
            $vstdate=$row->vstdate;
            $visit=$row->visit;
            $Emergent= $row->Emergent;
            $Urgent=$row->Urgent;
            $Acute_illness=$row->Acute_illness;
            $Non_acute_illness=$row->Non_acute_illness;
            $url=url('hnplus/product/er_night'); 
        }  
                
    //แจ้งเตือน Telegram

        $message = "🚨งานอุบัติเหตุ-ฉุกเฉิน" ."\n"
        ."วันที่ " .DateThai($vstdate) ."\n"  
        ."เวลา 00.00-08.00 น. 🌙เวรดึก" ."\n"
        ."ผู้ป่วยในเวร " .$visit ." ราย" ."\n"       
        ." -Emergent " .$Emergent ." ราย" ."\n"
        ." -Urgent " .$Urgent ." ราย" ."\n"
        ." -Acute illness " .$Acute_illness ." ราย" ."\n" 
        ." -Non Acute illness " .$Non_acute_illness ." ราย" ."\n" ."\n"
        ."บันทึก Productivity " ."\n"
        . $url. "\n";

        $token =  DB::table('nurse_setting')->where('name','telegram_token')->value('value'); //Notify_Bot
        $telegram_chat_id =  DB::table('nurse_setting')->where('name','telegram_chat_id_product_er')->value('value'); 
        $chat_ids = explode(',', $telegram_chat_id); //Notify_Group   

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

//er_night------------------------------------------------------------------------------------------------------------------------
    public function er_night()
    {
        $shift = DB::connection('hosxp')->select("
            SELECT DATE(NOW()) AS vstdate,
                COALESCE(COUNT(DISTINCT vn), 0) AS visit,
                COALESCE(SUM(CASE WHEN er_emergency_type IN ('1','2') THEN 1 ELSE 0 END), 0) AS Emergent,
                COALESCE(SUM(CASE WHEN er_emergency_type = '3' THEN 1 ELSE 0 END), 0) AS Urgent,
                COALESCE(SUM(CASE WHEN er_emergency_type = '4' THEN 1 ELSE 0 END), 0) AS Acute_illness,
                COALESCE(SUM(CASE WHEN er_emergency_type = '5' THEN 1 ELSE 0 END), 0) AS Non_acute_illness
            FROM er_regist
            WHERE DATE(enter_er_time) = DATE(NOW())
            AND TIME(enter_er_time) BETWEEN '00:00:00' AND '07:59:59'"); 

        return view('hnplus.product.er_night',compact('shift'));            
    }

//er_night_save--------------------------------------------------------------------------------------------------------------------
    public function er_night_save(Request $request)
    {
        // ✅ ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall'   => 'required|numeric',
            'nurse_partime'  => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder'       => 'required|string',
        ]);

        // ✅ เตรียมค่าที่ใช้ซ้ำ
        $emergent        = $request->emergent;
        $urgent          = $request->urgent;
        $acute_illness   = $request->acute_illness;
        $non_acute       = $request->non_acute_illness;
        $patient_all     = max(1, $request->patient_all); // ป้องกันหาร 0
        $nurse_total     = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        $nurse_total_hr  = max(1, $nurse_total * 7);      // ป้องกันหาร 0

        // ✅ คำนวณค่าทางสถิติ
        $patient_hr = ($emergent * 3.2) + ($urgent * 2.7) + ($acute_illness * 1.4) + ($non_acute * 0.5);
        $nurse_hr   = $nurse_total * 7;
        $productivity = ($patient_hr * 100) / $nurse_total_hr;
        $hhpuos = $patient_hr / $patient_all;
        $nurse_shift_time = $patient_all * $hhpuos * (1.4 / 7);

        // ✅ บันทึกข้อมูลลงฐานข้อมูล
        $productivity_er = Nurse_productivity_er::create([
            'report_date'      => $request->report_date,
            'shift_time'       => $request->shift_time,
            'patient_all'      => $patient_all,
            'emergent'         => $emergent,
            'urgent'           => $urgent,
            'acute_illness'    => $acute_illness,
            'non_acute_illness'=> $non_acute,
            'patient_hr'       => $patient_hr,
            'nurse_oncall'     => $request->nurse_oncall,
            'nurse_partime'    => $request->nurse_partime,
            'nurse_fulltime'   => $request->nurse_fulltime,
            'nurse_hr'         => $nurse_hr,
            'productivity'     => $productivity,
            'hhpuos'           => $hhpuos,
            'nurse_shift_time' => $nurse_shift_time,
            'recorder'         => $request->recorder,
            'note'             => $request->note,
        ]);

        // ✅ เตรียมข้อความแจ้ง Telegram
        $message = "🚨 งานอุบัติเหตุ-ฉุกเฉิน \n"
            ."วันที่ " . DateThai(date('Y-m-d')) . "\n"
            ."เวลา 00.00–08.00 น. 🌙เวรดึก\n"
            ."ผู้ป่วยในเวร: {$patient_all} ราย\n"
            ." - Emergent: {$emergent} ราย\n"
            ." - Urgent: {$urgent} ราย\n"
            ." - Acute illness: {$acute_illness} ราย\n"
            ." - Non Acute illness: {$non_acute} ราย\n"
            ."👩‍⚕️ Oncall: {$request->nurse_oncall}\n"
            ."👩‍⚕️ เสริม: {$request->nurse_partime}\n"
            ."👩‍⚕️ ปกติ: {$request->nurse_fulltime}\n"
            ."🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) . "\n"
            ."🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) . "\n"
            ."📈 Productivity: " . number_format($productivity, 2) . "%\n"
            ."ผู้บันทึก: {$request->recorder}";

        // ✅ ส่งข้อความ Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_er_save')->value('value'));

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
    public function er_morning_notify()
    {
        $service = DB::connection('hosxp')->select("
            SELECT DATE(NOW()) AS vstdate,
                COALESCE(COUNT(DISTINCT vn), 0) AS visit,
                COALESCE(SUM(CASE WHEN er_emergency_type IN ('1','2') THEN 1 ELSE 0 END), 0) AS Emergent,
                COALESCE(SUM(CASE WHEN er_emergency_type = '3' THEN 1 ELSE 0 END), 0) AS Urgent,
                COALESCE(SUM(CASE WHEN er_emergency_type = '4' THEN 1 ELSE 0 END), 0) AS Acute_illness,
                COALESCE(SUM(CASE WHEN er_emergency_type = '5' THEN 1 ELSE 0 END), 0) AS Non_acute_illness
            FROM er_regist
            WHERE DATE(enter_er_time) = DATE(NOW())
            AND TIME(enter_er_time) BETWEEN '08:00:00' AND '15:59:59'");         

        foreach ($service as $row){
            $vstdate=$row->vstdate;
            $visit=$row->visit;
            $Emergent= $row->Emergent;
            $Urgent=$row->Urgent;
            $Acute_illness=$row->Acute_illness;
            $Non_acute_illness=$row->Non_acute_illness;
            $url=url('hnplus/product/er_morning'); 
        }  
                
    //แจ้งเตือน Telegram

        $message = "🚨งานอุบัติเหตุ-ฉุกเฉิน" ."\n"
        ."วันที่ " .DateThai($vstdate) ."\n"  
        ."เวลา 08.00-16.00 น. 🌅เวรเช้า" ."\n"
        ."ผู้ป่วยในเวร " .$visit ." ราย" ."\n"       
        ." -Emergent " .$Emergent ." ราย" ."\n"
        ." -Urgent " .$Urgent ." ราย" ."\n"
        ." -Acute illness " .$Acute_illness ." ราย" ."\n" 
        ." -Non Acute illness " .$Non_acute_illness ." ราย" ."\n" ."\n"
        ."บันทึก Productivity " ."\n"
        . $url. "\n";

        $token =  DB::table('nurse_setting')->where('name','telegram_token')->value('value'); //Notify_Bot
        $telegram_chat_id =  DB::table('nurse_setting')->where('name','telegram_chat_id_product_er')->value('value'); 
        $chat_ids = explode(',', $telegram_chat_id); //Notify_Group   

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

//er_morning-------------------------------------------------------------------------------------------------------------
    public function er_morning()
    {
        $shift = DB::connection('hosxp')->select("
            SELECT DATE(NOW()) AS vstdate,
                COALESCE(COUNT(DISTINCT vn), 0) AS visit,
                COALESCE(SUM(CASE WHEN er_emergency_type IN ('1','2') THEN 1 ELSE 0 END), 0) AS Emergent,
                COALESCE(SUM(CASE WHEN er_emergency_type = '3' THEN 1 ELSE 0 END), 0) AS Urgent,
                COALESCE(SUM(CASE WHEN er_emergency_type = '4' THEN 1 ELSE 0 END), 0) AS Acute_illness,
                COALESCE(SUM(CASE WHEN er_emergency_type = '5' THEN 1 ELSE 0 END), 0) AS Non_acute_illness
            FROM er_regist
            WHERE DATE(enter_er_time) = DATE(NOW())
            AND TIME(enter_er_time) BETWEEN '08:00:00' AND '15:59:59'"); 

        return view('hnplus.product.er_morning',compact('shift'));            
    }

//er_morning_save------------------------------------------------------------------------------------------------------------------
    public function er_morning_save(Request $request)
    {
        // ✅ ตรวจสอบข้อมูลเบื้องต้น
        $request->validate([
            'nurse_oncall'   => 'required|numeric',
            'nurse_partime'  => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder'       => 'required|string',
        ]);

        // ✅ เตรียมค่าที่ใช้ในการคำนวณ
        $emergent      = $request->emergent;
        $urgent        = $request->urgent;
        $acute_illness = $request->acute_illness;
        $non_acute     = $request->non_acute_illness;
        $patient_all   = max(1, $request->patient_all); // ป้องกันหาร 0
        $nurse_total   = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        $nurse_total_hr = max(1, $nurse_total * 7);    // ป้องกันหาร 0

        // ✅ คำนวณค่า Productivity
        $patient_hr = ($emergent * 3.2) + ($urgent * 2.7) + ($acute_illness * 1.4) + ($non_acute * 0.5);
        $nurse_hr = $nurse_total * 7;
        $productivity = ($patient_hr * 100) / $nurse_total_hr;
        $hhpuos = $patient_hr / $patient_all;
        $nurse_shift_time = $patient_all * $hhpuos * (1.4 / 7);

        // ✅ บันทึกข้อมูลลงฐานข้อมูล
        Nurse_productivity_er::create([
            'report_date'      => $request->report_date,
            'shift_time'       => $request->shift_time,
            'patient_all'      => $patient_all,
            'emergent'         => $emergent,
            'urgent'           => $urgent,
            'acute_illness'    => $acute_illness,
            'non_acute_illness'=> $non_acute,
            'patient_hr'       => $patient_hr,
            'nurse_oncall'     => $request->nurse_oncall,
            'nurse_partime'    => $request->nurse_partime,
            'nurse_fulltime'   => $request->nurse_fulltime,
            'nurse_hr'         => $nurse_hr,
            'productivity'     => $productivity,
            'hhpuos'           => $hhpuos,
            'nurse_shift_time' => $nurse_shift_time,
            'recorder'         => $request->recorder,
            'note'             => $request->note,
        ]);

        // ✅ สร้างข้อความแจ้งเตือน Telegram
        $message = "🚨 งานอุบัติเหตุ-ฉุกเฉิน \n"
            ."วันที่ " . DateThai(date('Y-m-d')) . "\n"
            ."เวลา 08.00–16.00 น. 🌅เวรเช้า\n"
            ."ผู้ป่วยในเวร: {$patient_all} ราย\n"
            ." - Emergent: {$emergent} ราย\n"
            ." - Urgent: {$urgent} ราย\n"
            ." - Acute illness: {$acute_illness} ราย\n"
            ." - Non Acute illness: {$non_acute} ราย\n"
            ."👩‍⚕️ Oncall: {$request->nurse_oncall}\n"
            ."👩‍⚕️ เสริม: {$request->nurse_partime}\n"
            ."👩‍⚕️ ปกติ: {$request->nurse_fulltime}\n"
            ."🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) . "\n"
            ."🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) . "\n"
            ."📈 Productivity: " . number_format($productivity, 2) . "%\n"
            ."ผู้บันทึก: {$request->recorder}";

        // ✅ ส่งข้อความผ่าน Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_er_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text'    => $message,
            ]);
            usleep(500000); // พัก 0.5 วินาที (กัน rate limit)
        }

        // ✅ กลับหน้าหลักพร้อมแจ้งผล        
        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
    }

//แจ้งเตือนสถานะการณ์สรุปเวรบ่าย รัน 00.01 น.---------------------------------------------------------------------------------------------
    public function er_afternoon_notify()
    {
        $service = DB::connection('hosxp')->select("
            SELECT date(DATE_ADD(now(), INTERVAL -1 DAY )) AS vstdate,
                COALESCE(COUNT(DISTINCT vn), 0) AS visit,
                COALESCE(SUM(CASE WHEN er_emergency_type IN ('1','2') THEN 1 ELSE 0 END), 0) AS Emergent,
                COALESCE(SUM(CASE WHEN er_emergency_type = '3' THEN 1 ELSE 0 END), 0) AS Urgent,
                COALESCE(SUM(CASE WHEN er_emergency_type = '4' THEN 1 ELSE 0 END), 0) AS Acute_illness,
                COALESCE(SUM(CASE WHEN er_emergency_type = '5' THEN 1 ELSE 0 END), 0) AS Non_acute_illness
            FROM er_regist
            WHERE DATE(enter_er_time) = date(DATE_ADD(now(), INTERVAL -1 DAY ))
            AND TIME(enter_er_time) BETWEEN '16:00:00' AND '23:59:59'");         

        foreach ($service as $row){
            $vstdate=$row->vstdate;
            $visit=$row->visit;
            $Emergent= $row->Emergent;
            $Urgent=$row->Urgent;
            $Acute_illness=$row->Acute_illness;
            $Non_acute_illness=$row->Non_acute_illness;
            $url=url('hnplus/product/er_afternoon'); 
        }  
                
    //แจ้งเตือน Telegram

        $message = "🚨งานอุบัติเหตุ-ฉุกเฉิน" ."\n"
        ."วันที่ " .DateThai($vstdate) ."\n"  
        ."เวลา 16.00-24.00 น. 🌇เวรบ่าย" ."\n"
        ."ผู้ป่วยในเวร " .$visit ." ราย" ."\n"       
        ." -Emergent " .$Emergent ." ราย" ."\n"
        ." -Urgent " .$Urgent ." ราย" ."\n"
        ." -Acute illness " .$Acute_illness ." ราย" ."\n" 
        ." -Non Acute illness " .$Non_acute_illness ." ราย" ."\n" ."\n"
        ."บันทึก Productivity " ."\n"
        . $url. "\n";

        $token =  DB::table('nurse_setting')->where('name','telegram_token')->value('value'); //Notify_Bot
        $telegram_chat_id =  DB::table('nurse_setting')->where('name','telegram_chat_id_product_er')->value('value'); 
        $chat_ids = explode(',', $telegram_chat_id); //Notify_Group   

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

//er_afternoon------------------------------------------------------------------------------------------------------------
    public function er_afternoon()
    {
        $shift = DB::connection('hosxp')->select("
            SELECT date(DATE_ADD(now(), INTERVAL -1 DAY ))  AS vstdate,
                COALESCE(COUNT(DISTINCT vn), 0) AS visit,
                COALESCE(SUM(CASE WHEN er_emergency_type IN ('1','2') THEN 1 ELSE 0 END), 0) AS Emergent,
                COALESCE(SUM(CASE WHEN er_emergency_type = '3' THEN 1 ELSE 0 END), 0) AS Urgent,
                COALESCE(SUM(CASE WHEN er_emergency_type = '4' THEN 1 ELSE 0 END), 0) AS Acute_illness,
                COALESCE(SUM(CASE WHEN er_emergency_type = '5' THEN 1 ELSE 0 END), 0) AS Non_acute_illness
            FROM er_regist
            WHERE DATE(enter_er_time) = date(DATE_ADD(now(), INTERVAL -1 DAY )) 
            AND TIME(enter_er_time) BETWEEN '16:00:00' AND '23:59:59'");

        return view('hnplus.product.er_afternoon',compact('shift'));            
    }

//er_afternoon_save---------------------------------------------------------------------------------------------------------------
    public function er_afternoon_save(Request $request)
        {
        // ✅ ตรวจสอบข้อมูลเบื้องต้น
        $request->validate([
            'nurse_oncall'   => 'required|numeric',
            'nurse_partime'  => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder'       => 'required|string',
        ]);

        // ✅ เตรียมค่าที่ใช้ในการคำนวณ
        $emergent      = $request->emergent;
        $urgent        = $request->urgent;
        $acute_illness = $request->acute_illness;
        $non_acute     = $request->non_acute_illness;
        $patient_all   = max(1, $request->patient_all); // ป้องกันหาร 0
        $nurse_total   = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        $nurse_total_hr = max(1, $nurse_total * 7);    // ป้องกันหาร 0

        // ✅ คำนวณค่า Productivity
        $patient_hr = ($emergent * 3.2) + ($urgent * 2.7) + ($acute_illness * 1.4) + ($non_acute * 0.5);
        $nurse_hr = $nurse_total * 7;
        $productivity = ($patient_hr * 100) / $nurse_total_hr;
        $hhpuos = $patient_hr / $patient_all;
        $nurse_shift_time = $patient_all * $hhpuos * (1.4 / 7);

        // ✅ บันทึกข้อมูลลงฐานข้อมูล
        Nurse_productivity_er::create([
            'report_date'      => $request->report_date,
            'shift_time'       => $request->shift_time,
            'patient_all'      => $patient_all,
            'emergent'         => $emergent,
            'urgent'           => $urgent,
            'acute_illness'    => $acute_illness,
            'non_acute_illness'=> $non_acute,
            'patient_hr'       => $patient_hr,
            'nurse_oncall'     => $request->nurse_oncall,
            'nurse_partime'    => $request->nurse_partime,
            'nurse_fulltime'   => $request->nurse_fulltime,
            'nurse_hr'         => $nurse_hr,
            'productivity'     => $productivity,
            'hhpuos'           => $hhpuos,
            'nurse_shift_time' => $nurse_shift_time,
            'recorder'         => $request->recorder,
            'note'             => $request->note,
        ]);

        // ✅ สร้างข้อความแจ้งเตือน Telegram
        $message = "🚨 งานอุบัติเหตุ-ฉุกเฉิน \n"
            ."วันที่ " . DateThai(date('Y-m-d')) . "\n"
            ."เวลา 16.00–24.00 น. 🌇เวรบ่าย\n"
            ."ผู้ป่วยในเวร: {$patient_all} ราย\n"
            ." - Emergent: {$emergent} ราย\n"
            ." - Urgent: {$urgent} ราย\n"
            ." - Acute illness: {$acute_illness} ราย\n"
            ." - Non Acute illness: {$non_acute} ราย\n"
            ."👩‍⚕️ Oncall: {$request->nurse_oncall}\n"
            ."👩‍⚕️ เสริม: {$request->nurse_partime}\n"
            ."👩‍⚕️ ปกติ: {$request->nurse_fulltime}\n"
            ."🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) . "\n"
            ."🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) . "\n"
            ."📈 Productivity: " . number_format($productivity, 2) . "%\n"
            ."ผู้บันทึก: {$request->recorder}";

        // ✅ ส่งข้อความผ่าน Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_er_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text'    => $message,
            ]);
            usleep(500000); // พัก 0.5 วินาที (กัน rate limit)
        }

        // ✅ กลับหน้าหลักพร้อมแจ้งผล        
        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรบ่ายเรียบร้อยแล้ว');
    }

}
