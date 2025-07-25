<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Complain;
use App\Models\Line_token;

class Customer_ComplainController extends Controller
{
public function __construct()
    {
        // $this->middleware('auth',  ['only' => ['index']]);
        $this->middleware('auth')->except(['create','store']);
    }

public function index(Request $request)
    {
        $start_date = $request->start_date ?: date('Y-m-d', strtotime('first day of this month'));
        $end_date   = $request->end_date   ?: date('Y-m-d');
        
        $complain=Complain::whereBetween('created_at',[$start_date." 00:00:00", $end_date." 23:59:59"])
                            ->orderby('created_at','desc')->get(); 
        return view('customer_complain.index',compact('complain','start_date','end_date'));        
    }

public function create() 
    {
        return view('customer_complain.create'); 
    } 

public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'name' => 'required',
            'detail' => 'required',
            'call_back' => 'required'             
        ]);               
      
        $complain = new Complain;   
        $complain->type = $request->type;
        $complain->detail = $request->detail;
        $complain->call_back = $request->call_back;
        $complain->name = $request->name;
        $complain->phone = $request->phone;
        $complain->email = $request->email;
        $complain->save();

//เปิดแจ้งเตือน Telegram---------------------
    $message = "ความคิดเห็น/เสนอแนะ" ."\n"
        ."ประเภท : " .$complain->type ."\n"  
        ."รายละเอียด : " .$complain->detail ."\n" 
        ."ให้ติดต่อกลับ : " .$complain->call_back ."\n";      
  
    $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
    $chat_ids = ["-4729376994","-4605577318"]; //Test_Notify_Group2,RM-รพ.หัวตะพาน
  
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

    return redirect()->route('customer_complain.create')->with('success', 'ส่งข้อมูลเรียบร้อยแล้ว');
}

public function show($id)
    {
        //
    }

public function edit($id)
    {
        //
    }

 public function update(Request $request, $id)
    {
        //
    }

public function destroy($id)
    {
        //
    }
}
