<?php

namespace App\Http\Controllers\Hnplus;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Nurse_inspection_shift;

class HnplusController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->only(['index','inspection_report']); 
    }
//-----------------------------------------------------------------------------------------------------------------
    public function index() 
    {
        return view('hnplus.dashboard');
    }
//###########################################################################################################################################
//inspection_report------------------------------------------------------------------------------------------------
    public function inspection_report(Request $request) 
    {

        $start_date = $request->start_date ?: date('Y-m-d', strtotime("first day of this month"));
        $end_date = $request->end_date ?: date('Y-m-d');        
        
        $er=Nurse_inspection_shift::where('depart','=','er')
            ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
            ->orderby('created_at','desc')->get(); 
        $opd=Nurse_inspection_shift::where('depart','=','opd')
            ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
            ->orderby('created_at','desc')->get(); 
        $ipd=Nurse_inspection_shift::where('depart','=','ipd')
            ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
            ->orderby('created_at','desc')->get();  
        $vip=Nurse_inspection_shift::where('depart','=','vip')
            ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
            ->orderby('created_at','desc')->get(); 
        $hd=Nurse_inspection_shift::where('depart','=','hd')
            ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
            ->orderby('created_at','desc')->get();     
        $hd_outsource=Nurse_inspection_shift::where('depart','=','hd_outsource')
            ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
            ->orderby('created_at','desc')->get();  
        $lr=Nurse_inspection_shift::where('depart','=','lr')
            ->whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
            ->orderby('created_at','desc')->get();  

        return view('hnplus.inspection.report',compact('er','opd','ipd','vip','hd','hd_outsource','lr','start_date','end_date'));
    }

//inspection_create------------------------------------------------------------------------------------------------------------------
    public function inspection_create($depart)
    {
        return view('hnplus.inspection.create',compact('depart')); 
    }

//inspection_save---------------------------------------------------------------------------------------------------------------------
    public function inspection_save(Request $request)
    {
        $request->validate([
            'depart' => 'required', 
            'risk' => 'required',
            'correct' => 'required',
            'complain' => 'required',
            'supervisor' => 'required',       
        ]);               
        
        $nurse = new Nurse_inspection_shift;  
        $nurse->depart = $request->depart;
        $nurse->risk = $request->risk;
        $nurse->correct = $request->correct;
        $nurse->complain = $request->complain;
        $nurse->note = $request->note;
        $nurse->supervisor = $request->supervisor;  
        $nurse->save();
        

    //เปิดแจ้งเตือน Telegram

        $message = "บันทึกเวรตรวจการ".
            "\n"."วันที่ : ".  DatetimeThai(date('Y-m-d H:i:s')).                 
            "\n"."หน่วยงาน : ". $nurse->depart.
            "\n"."ความเสี่ยง/เหตุการณ์ในเวร : ". $nurse->risk.  
            "\n"."การแก้ไขจัดการ : ". $nurse->correct.
            "\n"."นิเทศ/แนะนำในขณะตรวจเวร : ". $nurse->complain.  
            "\n"."หมายเหตุ : ". $nurse->note. 
            "\n"."พยาบาลเวรตรวจการ : ". $nurse->supervisor.         
            "\n";            
             
        $token =  DB::table('nurse_setting')->where('name','telegram_token')->value('value'); //Notify_Bot
        $telegram_chat_id =  DB::table('nurse_setting')->where('name','telegram_chat_id_inspection')->value('value'); 
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

        return redirect('hnplus/inspection/create/'.$nurse->depart)  ->with('success', 'ส่งข้อมูลเรียบร้อยแล้ว');
    }

}
