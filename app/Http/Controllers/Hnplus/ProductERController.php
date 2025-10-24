<?php

namespace App\Http\Controllers\Hnplus;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
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

//แจ้งเตือนสถานะการณ์สรุปเวรดึก รัน 08.00 น.--------------------------------------------------------------------------------------------------------
    public function nurse_productivity_er_night_notify()
    {
        $service = DB::connection('hosxp')->select('
            select DATE(NOW()) AS vstdate,count(DISTINCT vn) as visit,                 
            SUM(CASE WHEN er_emergency_type IN ("1","2") THEN 1	ELSE 0 END) AS "Emergent",
            SUM(CASE WHEN er_emergency_type ="3" THEN 1 ELSE 0 END) AS "Urgent",
            SUM(CASE WHEN er_emergency_type ="4" THEN 1 ELSE 0 END) AS "Acute_illness",
            SUM(CASE WHEN er_emergency_type ="5" THEN 1 ELSE 0 END) AS "Non_acute_illness"
            FROM er_regist  WHERE DATE(enter_er_time) = DATE(NOW()) 
            AND TIME(enter_er_time) BETWEEN "00:00:00" AND "07:59:59"');         

        foreach ($service as $row){
            $vstdate=$row->vstdate;
            $visit=$row->visit;
            $Emergent= $row->Emergent;
            $Urgent=$row->Urgent;
            $Acute_illness=$row->Acute_illness;
            $Non_acute_illness=$row->Non_acute_illness;
            $url=route('nurse_productivity_er_night'); 
        }  
                
    //แจ้งเตือน Telegram

        $message = "Productivity งานอุบัติเหตุ-ฉุกเฉิน" ."\n"
        ."วันที่ " .DateThai($vstdate) ."\n"  
        ."เวลา 00.00-08.00 น. (เวรดึก)" ."\n"
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

//nurse_productivity_er_night------------------------------------------------------------------------------------------------------------------------
    public function nurse_productivity_er_night()
    {
        $shift_night = DB::connection('hosxp')->select('
                SELECT count(DISTINCT vn) as visit,                 
                SUM(CASE WHEN er_emergency_type IN ("1","2") THEN 1 ELSE 0 END) AS "Emergent",
                SUM(CASE WHEN er_emergency_type ="3" THEN 1 ELSE 0 END) AS "Urgent",
                SUM(CASE WHEN er_emergency_type ="4" THEN 1 ELSE 0 END) AS "Acute_illness",
                SUM(CASE WHEN er_emergency_type ="5" THEN 1 ELSE 0 END) AS "Non_acute_illness"           
                FROM er_regist  WHERE DATE(enter_er_time) = DATE(NOW()) 
                AND TIME(enter_er_time) BETWEEN "00:00:00" AND "07:59:59"'); 

        return view('backoffice_hrd.nurse_productivity_er_night',compact('shift_night'));            
    }

//nurse_productivity_er_night_save--------------------------------------------------------------------------------------------------------------------
    public function nurse_productivity_er_night_save(Request $request)
    {
        $request->validate([
                'nurse_oncall' => 'required',
                'nurse_partime' => 'required',  
                'nurse_fulltime' => 'required',    
                'recorder' => 'required'
            ]);  
        $productivity_er = new Nurse_productivity_er;
        $productivity_er->report_date = $request->report_date;   
        $productivity_er->shift_time = $request->shift_time;
        $productivity_er->patient_all = $request->patient_all;
        $productivity_er->emergent = $request->emergent;
        $productivity_er->urgent = $request->urgent;
        $productivity_er->acute_illness = $request->acute_illness;
        $productivity_er->non_acute_illness = $request->non_acute_illness;
        $productivity_er->patient_hr = (($request->emergent*3.2)+($request->urgent*2.7)
                +($request->acute_illness*1.4)+($request->non_acute_illness*0.5));
        $productivity_er->nurse_oncall = $request->nurse_oncall;
        $productivity_er->nurse_partime = $request->nurse_partime;
        $productivity_er->nurse_fulltime = $request->nurse_fulltime;
        $productivity_er->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
        $productivity_er->productivity = ((($request->emergent*3.2)+($request->urgent*2.7)
                +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))*100)
                /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
        $productivity_er->hhpuos = (($request->emergent*3.2)+($request->urgent*2.7)
                +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))/$request->patient_all;
        $productivity_er->nurse_shift_time = $request->patient_all*( (($request->emergent*3.2)+($request->urgent*2.7)
        +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))/$request->patient_all)*(1.4/7);
        $productivity_er->recorder = $request->recorder;
        $productivity_er->note = $request->note;
        $productivity_er->save();

    //เปิดแจ้งเตือน Telegram
        $message = "งานอุบัติเหตุ-ฉุกเฉิน" ."\n"
                ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
                ."เวลา 00.00-08.00 น.(เวรดึก)"."\n"
                ."ผู้ป่วยในเวร " .$productivity_er->patient_all ." ราย" ."\n"       
                ." -Emergent " .$productivity_er->emergent ." ราย" ."\n"
                ." -Urgent " .$productivity_er->urgent ." ราย" ."\n"
                ." -Acute illness " .$productivity_er->acute_illness ." ราย" ."\n" 
                ." -Non Acute illness " .$productivity_er->non_acute_illness ." ราย" ."\n"
                ."อัตรากำลัง Oncall : " .$productivity_er->nurse_oncall ."\n" 
                ."อัตรากำลังเสริม : " .$productivity_er->nurse_partime ."\n"
                ."อัตรากำลังปกติ : " .$productivity_er->nurse_fulltime ."\n"
                ."ชม.การพยาบาล : " .number_format($productivity_er->patient_hr,2) ."\n"  
                ."ชม.การทำงาน : " .number_format($productivity_er->nurse_hr,2) ."\n"  
                ."Productivity ร้อยละ " .number_format($productivity_er->productivity,2) ."\n"  
                ."ผู้บันทึก : " .$productivity_er->recorder ."\n"; 
        
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
    //ปิดแจ้งเตือน Telegram

        return redirect()->route('nurse_productivity_er_night')->with('success', 'ส่งข้อมูลเวรดึกเรียบร้อยแล้ว');
    }

//Create nurse_productivity_er_morning-------------------------------------------------------------------------------------------------------------
    public function nurse_productivity_er_morning()
    {
        $shift_morning = DB::connection('hosxp')->select('
                SELECT DATE(NOW()) AS vstdate,count(DISTINCT vn) as visit,                 
                SUM(CASE WHEN er_emergency_type IN ("1","2") THEN 1 ELSE 0 END) AS "Emergent",
                SUM(CASE WHEN er_emergency_type ="3" THEN 1 ELSE 0 END) AS "Urgent",
                SUM(CASE WHEN er_emergency_type ="4" THEN 1 ELSE 0 END) AS "Acute_illness",
                SUM(CASE WHEN er_emergency_type ="5" THEN 1 ELSE 0 END) AS "Non_acute_illness"
                FROM er_regist  WHERE DATE(enter_er_time) = DATE(NOW()) 
                AND TIME(enter_er_time) BETWEEN "08:00:00" AND "15:59:59"'); 

        return view('backoffice_hrd.nurse_productivity_er_morning',compact('shift_morning'));            
    }

//nurse_productivity_er_morning_save------------------------------------------------------------------------------------------------------------------
    public function nurse_productivity_er_morning_save(Request $request)
    {
        $request->validate([
                'nurse_oncall' => 'required',
                'nurse_partime' => 'required',   
                'nurse_fulltime' => 'required',    
                'recorder' => 'required'
            ]);  
        $productivity_er = new Nurse_productivity_er;   
        $productivity_er->report_date = $request->report_date;   
        $productivity_er->shift_time = $request->shift_time;
        $productivity_er->patient_all = $request->patient_all;
        $productivity_er->emergent = $request->emergent;
        $productivity_er->urgent = $request->urgent;
        $productivity_er->acute_illness = $request->acute_illness;
        $productivity_er->non_acute_illness = $request->non_acute_illness;
        $productivity_er->patient_hr = (($request->emergent*3.2)+($request->urgent*2.7)
                +($request->acute_illness*1.4)+($request->non_acute_illness*0.5));
        $productivity_er->nurse_oncall = $request->nurse_oncall;
        $productivity_er->nurse_partime = $request->nurse_partime;
        $productivity_er->nurse_fulltime = $request->nurse_fulltime;
        $productivity_er->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
        $productivity_er->productivity = ((($request->emergent*3.2)+($request->urgent*2.7)
                +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))*100)
                /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
        $productivity_er->hhpuos = (($request->emergent*3.2)+($request->urgent*2.7)
                +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))/$request->patient_all;
        $productivity_er->nurse_shift_time = $request->patient_all*( (($request->emergent*3.2)+($request->urgent*2.7)
        +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))/$request->patient_all)*(1.4/7);
        $productivity_er->recorder = $request->recorder;
        $productivity_er->note = $request->note;
        $productivity_er->save();    

    //เปิดแจ้งเตือน Telegram
        $message = "งานอุบัติเหตุ-ฉุกเฉิน" ."\n"
                ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
                ."เวลา 08.00-16.00 น.(เวรเช้า)"."\n"
                ."ผู้ป่วยในเวร " .$productivity_er->patient_all ." ราย" ."\n"       
                ." -Emergent " .$productivity_er->emergent ." ราย" ."\n"
                ." -Urgent " .$productivity_er->urgent ." ราย" ."\n"
                ." -Acute illness " .$productivity_er->acute_illness ." ราย" ."\n" 
                ." -Non Acute illness " .$productivity_er->non_acute_illness ." ราย" ."\n"
                ."อัตรากำลัง Oncall : " .$productivity_er->nurse_oncall ."\n" 
                ."อัตรากำลังเสริม : " .$productivity_er->nurse_partime ."\n"
                ."อัตรากำลังปกติ : " .$productivity_er->nurse_fulltime ."\n"
                ."ชม.การพยาบาล : " .number_format($productivity_er->patient_hr,2) ."\n"  
                ."ชม.การทำงาน : " .number_format($productivity_er->nurse_hr,2) ."\n"  
                ."Productivity ร้อยละ " .number_format($productivity_er->productivity,2) ."\n"  
                ."ผู้บันทึก : " .$productivity_er->recorder ."\n"; 

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
    //ปิดแจ้งเตือน Telegram

        return redirect()->route('nurse_productivity_er_morning')->with('success', 'ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
    }

//Create nurse_productivity_er_afternoon------------------------------------------------------------------------------------------------------------
    public function nurse_productivity_er_afternoon()
    {
        $shift_afternoon = DB::connection('hosxp')->select('
                SELECT date(DATE_ADD(now(), INTERVAL -1 DAY )) AS vstdate,count(DISTINCT vn) as visit,                 
                SUM(CASE WHEN er_emergency_type IN ("1","2") THEN 1 ELSE 0 END) AS "Emergent",
                SUM(CASE WHEN er_emergency_type ="3" THEN 1 ELSE 0 END) AS "Urgent",
                SUM(CASE WHEN er_emergency_type ="4" THEN 1 ELSE 0 END) AS "Acute_illness",
                SUM(CASE WHEN er_emergency_type ="5" THEN 1 ELSE 0 END) AS "Non_acute_illness"
                FROM er_regist  WHERE DATE(enter_er_time) = date(DATE_ADD(now(), INTERVAL -1 DAY )) 
                AND TIME(enter_er_time) BETWEEN "16:00:00" AND "23:59:59"'); 

        return view('backoffice_hrd.nurse_productivity_er_afternoon',compact('shift_afternoon'));            
    }

//nurse_productivity_er_afternoon_save---------------------------------------------------------------------------------------------------------------
    public function nurse_productivity_er_afternoon_save(Request $request)
    {
        $request->validate([
                'nurse_oncall' => 'required',
                'nurse_partime' => 'required',  
                'nurse_fulltime' => 'required',    
                'recorder' => 'required'
            ]);  
        $productivity_er = new Nurse_productivity_er; 
        $productivity_er->report_date = $request->report_date;  
        $productivity_er->shift_time = $request->shift_time;
        $productivity_er->patient_all = $request->patient_all;
        $productivity_er->emergent = $request->emergent;
        $productivity_er->urgent = $request->urgent;
        $productivity_er->acute_illness = $request->acute_illness;
        $productivity_er->non_acute_illness = $request->non_acute_illness;
        $productivity_er->patient_hr = (($request->emergent*3.2)+($request->urgent*2.7)
                +($request->acute_illness*1.4)+($request->non_acute_illness*0.5));
        $productivity_er->nurse_oncall = $request->nurse_oncall;
        $productivity_er->nurse_partime = $request->nurse_partime;
        $productivity_er->nurse_fulltime = $request->nurse_fulltime;
        $productivity_er->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
        $productivity_er->productivity = ((($request->emergent*3.2)+($request->urgent*2.7)
                +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))*100)
                /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
        $productivity_er->hhpuos = (($request->emergent*3.2)+($request->urgent*2.7)
                +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))/$request->patient_all;
        $productivity_er->nurse_shift_time = $request->patient_all*( (($request->emergent*3.2)+($request->urgent*2.7)
        +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))/$request->patient_all)*(1.4/7);
        $productivity_er->recorder = $request->recorder;
        $productivity_er->note = $request->note;
        $productivity_er->save();

    //เปิดแจ้งเตือน Telegram
        $message = "งานอุบัติเหตุ-ฉุกเฉิน" ."\n"
                ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
                ."เวลา 16.00-00.00 น.(เวรบ่าย)"."\n"
                ."ผู้ป่วยในเวร " .$productivity_er->patient_all ." ราย" ."\n"       
                ." -Emergent " .$productivity_er->emergent ." ราย" ."\n"
                ." -Urgent " .$productivity_er->urgent ." ราย" ."\n"
                ." -Acute illness " .$productivity_er->acute_illness ." ราย" ."\n" 
                ." -Non Acute illness " .$productivity_er->non_acute_illness ." ราย" ."\n"
                ."อัตรากำลัง Oncall : " .$productivity_er->nurse_oncall ."\n" 
                ."อัตรากำลังเสริม : " .$productivity_er->nurse_partime ."\n"
                ."อัตรากำลังปกติ : " .$productivity_er->nurse_fulltime ."\n"
                ."ชม.การพยาบาล : " .number_format($productivity_er->patient_hr,2) ."\n"  
                ."ชม.การทำงาน : " .number_format($productivity_er->nurse_hr,2) ."\n"  
                ."Productivity ร้อยละ " .number_format($productivity_er->productivity,2) ."\n"  
                ."ผู้บันทึก : " .$productivity_er->recorder ."\n"; 

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
    //ปิดแจ้งเตือน Telegram

        return redirect()->route('nurse_productivity_er_afternoon')->with('success', 'ส่งข้อมูลเวรบ่ายเรียบร้อยแล้ว');
    }

}
