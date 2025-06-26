<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Form_check_asset;
use App\Models\Form_check_nurse;

class Form_CheckController extends Controller
{
    public function __construct()
{
        $this->middleware('auth')->only(['index','check_asset_report','check_nurse_report']);
}
     
//Create index
public function index()
{
      return view('form.index');            
}

//check_asset_report

public function check_asset_report(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
    
    $check_asset_er=Form_check_asset::where('depart','=','er')
        ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
        ->orderby('created_at','desc')->get(); 
    $check_asset_lr=Form_check_asset::where('depart','=','lr')
    ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
        ->orderby('created_at','desc')->get(); 
    $check_asset_or=Form_check_asset::where('depart','=','or')
        ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
        ->orderby('created_at','desc')->get(); 
    $check_asset_hd=Form_check_asset::where('depart','=','hd')
        ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
        ->orderby('created_at','desc')->get();  
    $check_asset_ipd=Form_check_asset::where('depart','=','ipd')
        ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
        ->orderby('created_at','desc')->get();  
    $check_asset_vip=Form_check_asset::where('depart','=','vip')
        ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
        ->orderby('created_at','desc')->get(); 

    $check_asset_sum = DB::select('
        SELECT CASE WHEN depart="er" THEN "1. อุบัติเหตุ-ฉุกเฉิน ER" WHEN depart="lr" THEN "2. ห้องคลอด LR"
        WHEN depart="or" THEN "3. ห้องผ่าตัด OR" WHEN depart="hd" THEN "4. ฟอกไต HD รพ."
        WHEN depart="ipd" THEN "5. ผู้ป่วยในสามัญ" WHEN depart="vip" THEN "6. ผู้ป่วยใน VIP" END AS depart,
        SUM(CASE WHEN asset1="พร้อมใช้" THEN 1 ELSE 0 END) AS asset1_ready,
        SUM(CASE WHEN asset1="ชำรุด" THEN 1 ELSE 0 END) AS asset1_repair,
        SUM(CASE WHEN asset1="ไม่เพียงพอ" THEN 1 ELSE 0 END) AS asset1_enough,
        SUM(CASE WHEN asset1="ไม่มี" THEN 1 ELSE 0 END) AS asset1_have,
        SUM(CASE WHEN asset2="พร้อมใช้" THEN 1 ELSE 0 END) AS asset2_ready,
        SUM(CASE WHEN asset2="ชำรุด" THEN 1 ELSE 0 END) AS asset2_repair,
        SUM(CASE WHEN asset2="ไม่เพียงพอ" THEN 1 ELSE 0 END) AS asset2_enough,
        SUM(CASE WHEN asset2="ไม่มี" THEN 1 ELSE 0 END) AS asset2_have,
        SUM(CASE WHEN asset3="พร้อมใช้" THEN 1 ELSE 0 END) AS asset3_ready,
        SUM(CASE WHEN asset3="ชำรุด" THEN 1 ELSE 0 END) AS asset3_repair,
        SUM(CASE WHEN asset3="ไม่เพียงพอ" THEN 1 ELSE 0 END) AS asset3_enough,
        SUM(CASE WHEN asset3="ไม่มี" THEN 1 ELSE 0 END) AS asset3_have,
        SUM(CASE WHEN asset4="พร้อมใช้" THEN 1 ELSE 0 END) AS asset4_ready,
        SUM(CASE WHEN asset4="ชำรุด" THEN 1 ELSE 0 END) AS asset4_repair,
        SUM(CASE WHEN asset4="ไม่เพียงพอ" THEN 1 ELSE 0 END) AS asset4_enough,
        SUM(CASE WHEN asset4="ไม่มี" THEN 1 ELSE 0 END) AS asset4_have,
        SUM(CASE WHEN asset5="พร้อมใช้" THEN 1 ELSE 0 END) AS asset5_ready,
        SUM(CASE WHEN asset5="ชำรุด" THEN 1 ELSE 0 END) AS asset5_repair,
        SUM(CASE WHEN asset5="ไม่เพียงพอ" THEN 1 ELSE 0 END) AS asset5_enough,
        SUM(CASE WHEN asset5="ไม่มี" THEN 1 ELSE 0 END) AS asset5_have,
        SUM(CASE WHEN asset6="พร้อมใช้" THEN 1 ELSE 0 END) AS asset6_ready,
        SUM(CASE WHEN asset6="ชำรุด" THEN 1 ELSE 0 END) AS asset6_repair,
        SUM(CASE WHEN asset6="ไม่เพียงพอ" THEN 1 ELSE 0 END) AS asset6_enough,
        SUM(CASE WHEN asset6="ไม่มี" THEN 1 ELSE 0 END) AS asset6_have,
        SUM(CASE WHEN asset7="พร้อมใช้" THEN 1 ELSE 0 END) AS asset7_ready,
        SUM(CASE WHEN asset7="ชำรุด" THEN 1 ELSE 0 END) AS asset7_repair,
        SUM(CASE WHEN asset7="ไม่เพียงพอ" THEN 1 ELSE 0 END) AS asset7_enough,
        SUM(CASE WHEN asset7="ไม่มี" THEN 1 ELSE 0 END) AS asset7_have,
        SUM(CASE WHEN asset8="พร้อมใช้" THEN 1 ELSE 0 END) AS asset8_ready,
        SUM(CASE WHEN asset8="ชำรุด" THEN 1 ELSE 0 END) AS asset8_repair,
        SUM(CASE WHEN asset8="ไม่เพียงพอ" THEN 1 ELSE 0 END) AS asset8_enough,
        SUM(CASE WHEN asset8="ไม่มี" THEN 1 ELSE 0 END) AS asset8_have,
        SUM(CASE WHEN asset9="พร้อมใช้" THEN 1 ELSE 0 END) AS asset9_ready,
        SUM(CASE WHEN asset9="ชำรุด" THEN 1 ELSE 0 END) AS asset9_repair,
        SUM(CASE WHEN asset9="ไม่เพียงพอ" THEN 1 ELSE 0 END) AS asset9_enough,
        SUM(CASE WHEN asset9="ไม่มี" THEN 1 ELSE 0 END) AS asset9_have,
        SUM(CASE WHEN asset10="พร้อมใช้" THEN 1 ELSE 0 END) AS asset10_ready,
        SUM(CASE WHEN asset10="ชำรุด" THEN 1 ELSE 0 END) AS asset10_repair,
        SUM(CASE WHEN asset10="ไม่เพียงพอ" THEN 1 ELSE 0 END) AS asset10_enough,
        SUM(CASE WHEN asset10="ไม่มี" THEN 1 ELSE 0 END) AS asset10_have,
        SUM(CASE WHEN asset11="พร้อมใช้" THEN 1 ELSE 0 END) AS asset11_ready,
        SUM(CASE WHEN asset11="ชำรุด" THEN 1 ELSE 0 END) AS asset11_repair,
        SUM(CASE WHEN asset11="ไม่เพียงพอ" THEN 1 ELSE 0 END) AS asset11_enough,
        SUM(CASE WHEN asset11="ไม่มี" THEN 1 ELSE 0 END) AS asset11_have,
        SUM(CASE WHEN asset12="พร้อมใช้" THEN 1 ELSE 0 END) AS asset12_ready,
        SUM(CASE WHEN asset12="ชำรุด" THEN 1 ELSE 0 END) AS asset12_repair,
        SUM(CASE WHEN asset12="ไม่เพียงพอ" THEN 1 ELSE 0 END) AS asset12_enough,
        SUM(CASE WHEN asset12="ไม่มี" THEN 1 ELSE 0 END) AS asset12_have,
        SUM(CASE WHEN asset13="พร้อมใช้" THEN 1 ELSE 0 END) AS asset13_ready,
        SUM(CASE WHEN asset13="ชำรุด" THEN 1 ELSE 0 END) AS asset13_repair,
        SUM(CASE WHEN asset13="ไม่เพียงพอ" THEN 1 ELSE 0 END) AS asset13_enough,
        SUM(CASE WHEN asset13="ไม่มี" THEN 1 ELSE 0 END) AS asset13_have,COUNT(*) AS total
        FROM form_check_assets
        WHERE DATE(created_at) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY depart ORDER BY depart'); 

    return view('form.check_asset_report',compact('check_asset_er','check_asset_lr','check_asset_or',
        'check_asset_hd','check_asset_ipd','check_asset_vip','start_date','end_date','check_asset_sum'));        
}

//check_asset_create
public function check_asset_create($depart)
{
    return view('form.check_asset_create',compact('depart')); 
}

//check_asset_save
public function check_asset_save(Request $request)
{
    $request->validate([
        'depart' => 'required',
        'hr_check' => 'required', 
        'asset1' => 'required',
        'asset2' => 'required',
        'asset3' => 'required',
        'asset4' => 'required',
        'asset5' => 'required',
        'asset6' => 'required',
        'asset7' => 'required',
        'asset8' => 'required',
        'asset9' => 'required',
        'asset10' => 'required',
        'asset11' => 'required',
        'asset12' => 'required',
        'asset13' => 'required'
    ]);               
    
    $check_asset = new Form_check_asset;   
    $check_asset->depart = $request->depart;
    $check_asset->hr_check = $request->hr_check;
    $check_asset->asset1 = $request->asset1;
    $check_asset->asset2 = $request->asset2;
    $check_asset->asset3 = $request->asset3;
    $check_asset->asset4 = $request->asset4;
    $check_asset->asset5 = $request->asset5;
    $check_asset->asset6 = $request->asset6;
    $check_asset->asset7 = $request->asset7;
    $check_asset->asset8 = $request->asset8;
    $check_asset->asset9 = $request->asset9;
    $check_asset->asset10 = $request->asset10;
    $check_asset->asset11 = $request->asset11;
    $check_asset->asset12 = $request->asset12;
    $check_asset->asset13 = $request->asset13;
    $check_asset->outher = $request->outher;
    $check_asset->created_at = date('Y-m-d H:i:s'); 
    $check_asset->save();

//เปิดแจ้งเตือน Telegram

    $message = "เครื่องมือแพทย์และอุปกรณ์ฉุกเฉิน".
        "\n"."หน่วยงาน : " .$check_asset->depart .                 
        "\n"."ผู้ตรวจสอบ : " . $check_asset->hr_check.
        "\n"."Defibrillator : " . $check_asset->asset1.  
        "\n"."Laryngoscope : " . $check_asset->asset2.
        "\n"."Ambu bag : " . $check_asset->asset3.  
        "\n"."ETT+อุปกรณ์Away : " . $check_asset->asset4.  
        "\n"."Oxygen+อุปกรณ์ : " . $check_asset->asset5.  
        "\n"."Auto CPR : " . $check_asset->asset6.  
        "\n"."กล่องยาฉุกเฉิน : " . $check_asset->asset7.  
        "\n"."Ventilator : " . $check_asset->asset8.  
        "\n"."Ekg 12 lead : " . $check_asset->asset9. 
        "\n"."Suction+อุปกรณ์ : " . $check_asset->asset10.    
        "\n"."เครื่องดมยา : " . $check_asset->asset11. 
        "\n"."เครื่อง NST : " . $check_asset->asset12. 
        "\n"."Radiant Warmer : " . $check_asset->asset13. 
        "\n"."หมายเหตุ : " . $check_asset->outher.        
        "\n";             
    
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,Test_Notify_Group2
  
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

    return redirect()->route('check_asset_create',$check_asset->depart)->with('success', 'ส่งข้อมูลเรียบร้อยแล้ว'); 
}

//check_nurse_report

public function check_nurse_report(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
    
    $check_nurse_er=Form_check_nurse::where('depart','=','er')
        ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
        ->orderby('created_at','desc')->get(); 
    $check_nurse_opd=Form_check_nurse::where('depart','=','opd')
        ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
        ->orderby('created_at','desc')->get(); 
    $check_nurse_ipd=Form_check_nurse::where('depart','=','ipd')
        ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
        ->orderby('created_at','desc')->get();  
    $check_nurse_vip=Form_check_nurse::where('depart','=','vip')
        ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
        ->orderby('created_at','desc')->get(); 
    $check_nurse_hd=Form_check_nurse::where('depart','=','hd')
        ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
        ->orderby('created_at','desc')->get();     
    $check_nurse_hd_outsource=Form_check_nurse::where('depart','=','hd_outsource')
        ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
        ->orderby('created_at','desc')->get();  
    $check_nurse_lr=Form_check_nurse::where('depart','=','lr')
        ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
        ->orderby('created_at','desc')->get();                

    return view('form.check_nurse_report',compact('check_nurse_er','check_nurse_opd','check_nurse_ipd',
        'check_nurse_vip','check_nurse_hd','check_nurse_hd_outsource','check_nurse_lr','start_date','end_date'));        
}

//check_nurse_create
public function check_nurse_create($depart)
{
    return view('form.check_nurse_create',compact('depart')); 
}

//check_nurse_save
public function check_nurse_save(Request $request)
{
    $request->validate([
        'depart' => 'required', 
        'risk' => 'required',
        'correct' => 'required',
        'complain' => 'required',
        'supervisor' => 'required',       
    ]);               
    
    $check_nurse = new Form_check_nurse;  
    $check_nurse->depart = $request->depart;
    $check_nurse->risk = $request->risk;
    $check_nurse->correct = $request->correct;
    $check_nurse->complain = $request->complain;
    $check_nurse->note = $request->note;
    $check_nurse->supervisor = $request->supervisor;  
    $check_nurse->save();
    

//เปิดแจ้งเตือน Telegram

    $message = "บันทึกเวรตรวจการ".
        "\n"."วันที่ : ".  DatetimeThai(date('Y-m-d H:i:s')).                 
        "\n"."หน่วยงาน : ". $check_nurse->depart.
        "\n"."ความเสี่ยง/เหตุการณ์ในเวร : ". $check_nurse->risk.  
        "\n"."การแก้ไขจัดการ : ". $check_nurse->correct.
        "\n"."นิเทศ/แนะนำในขณะตรวจเวร : ". $check_nurse->complain.  
        "\n"."หมายเหตุ : ". $check_nurse->note. 
        "\n"."พยาบาลเวรตรวจการ : ". $check_nurse->supervisor.         
        "\n";            

    $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
    $chat_ids = ["-4716437484","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,Test_Notify_Group2

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

    return redirect()->route('check_nurse_create',$check_nurse->depart)->with('success', 'ส่งข้อมูลเรียบร้อยแล้ว');
}
   
}
