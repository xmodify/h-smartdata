<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Skpcard;

class SkpcardController extends Controller
{
      //Check Login
public function __construct()
    {
        $this->middleware('auth');
    }
    
//Create index
public function index(Request $request )
    {    
        $search  =  $request->search; 
        $skpcard =  Skpcard::select('id','cid','name','birthday','address','phone','buy_date','ex_date','price',                           
                        DB::raw('CONCAT(timestampdiff(year,birthday,curdate()),SPACE(1),"ปี") AS age'),
                        DB::raw('CASE WHEN (DATE(now())> ex_date) THEN "หมดอายุ" ELSE "ใช้ได้ปกติ" END AS "status"'))
                    ->where(function ($query) use ($search){
                        $query->where('name','like','%'.$search.'%');
                        $query->orwhere('cid','like','%'.$search.'%');
                        })
                    ->orderBy('id','desc')
                    ->paginate(10);      
 
        $count =    Skpcard::select(                           
                        DB::raw('COUNT(id) AS total'),
                        DB::raw('SUM(CASE WHEN (DATE(now())>DATE_ADD(buy_date,INTERVAL 1 YEAR)) THEN 1 ELSE 0 END) AS exprie'),
                        DB::raw('COUNT(id)-SUM(CASE WHEN (DATE(now())>DATE_ADD(buy_date,INTERVAL 1 YEAR)) THEN 1 ELSE 0 END) AS normal'),
                        )->get(); 

        $sum = Skpcard::select(                           
                        DB::raw('IF(MONTH(buy_date)>9,YEAR(buy_date)+1,YEAR(buy_date)) + 543 AS year_bud'),
                        DB::raw('sum(case when month(buy_date)=10 then 1 else 0 end) as month_10'),
                        DB::raw('sum(case when month(buy_date)=11 then 1 else 0 end) as month_11'),
                        DB::raw('sum(case when month(buy_date)=12 then 1 else 0 end) as month_12'),
                        DB::raw('sum(case when month(buy_date)=1 then 1 else 0 end) as month_1'),
                        DB::raw('sum(case when month(buy_date)=2 then 1 else 0 end) as month_2'),
                        DB::raw('sum(case when month(buy_date)=3 then 1 else 0 end) as month_3'),
                        DB::raw('sum(case when month(buy_date)=4 then 1 else 0 end) as month_4'),
                        DB::raw('sum(case when month(buy_date)=5 then 1 else 0 end) as month_5'),
                        DB::raw('sum(case when month(buy_date)=6 then 1 else 0 end) as month_6'),
                        DB::raw('sum(case when month(buy_date)=7 then 1 else 0 end) as month_7'),
                        DB::raw('sum(case when month(buy_date)=8 then 1 else 0 end) as month_8'),
                        DB::raw('sum(case when month(buy_date)=9 then 1 else 0 end) as month_9'),
                        DB::raw('sum(case when month(buy_date)=10 then price else 0 end) as price_10'),
                        DB::raw('sum(case when month(buy_date)=11 then price else 0 end) as price_11'),
                        DB::raw('sum(case when month(buy_date)=12 then price else 0 end) as price_12'),
                        DB::raw('sum(case when month(buy_date)=1 then price else 0 end) as price_1'),
                        DB::raw('sum(case when month(buy_date)=2 then price else 0 end) as price_2'),
                        DB::raw('sum(case when month(buy_date)=3 then price else 0 end) as price_3'),
                        DB::raw('sum(case when month(buy_date)=4 then price else 0 end) as price_4'),
                        DB::raw('sum(case when month(buy_date)=5 then price else 0 end) as price_5'),
                        DB::raw('sum(case when month(buy_date)=6 then price else 0 end) as price_6'),
                        DB::raw('sum(case when month(buy_date)=7 then price else 0 end) as price_7'),
                        DB::raw('sum(case when month(buy_date)=8 then price else 0 end) as price_8'),
                        DB::raw('sum(case when month(buy_date)=9 then price else 0 end) as price_9'),
                        DB::raw('count(cid) as total'),
                        DB::raw('SUM(price) as price'))
                        ->groupby('year_bud')
                        ->orderBy('year_bud','desc')
                        ->get();  
          

        return view('skpcard.index', compact('skpcard','count','sum'));
    }

//Create Resource
public function create()
    {
        $count = Skpcard::count(); 
        return view('skpcard.create',compact('count'));
    }
    
//Store Resource   
public function store(Request $request)
    {
        $request->validate([
            'cid' => 'required',
            'name' => 'required',            
            'address' => 'required',
            'phone' => 'required',
            'buy_date' => 'required',
            'price' => 'required',
            'rcpt' => 'required'
        ]);               
      
        $buy_date=$request->buy_date;
        $ex_date=date('Y-m-d',strtotime('1 year',strtotime($buy_date)));
        $skpcard = new Skpcard;   
        $skpcard->cid = $request->cid;
        $skpcard->name = $request->name;
        $skpcard->birthday = $request->birthday;
        $skpcard->address = $request->address;
        $skpcard->phone = $request->phone;
        $skpcard->buy_date = $request->buy_date;
        $skpcard->ex_date = $ex_date;
        $skpcard->price = $request->price;
        $skpcard->rcpt = $request->rcpt;
        $skpcard->save();


        //แจ้งเตือน line    
        $line_token_db = DB::select('select line_token FROM line_tokens WHERE line_token_id IN ("2")'); 
        $line_token_array = array_column($line_token_db,'line_token'); 

        $header = "บัตรใหม่";      
        $message = $header.
            "\n"."ชื่อ-สกุล : " . $skpcard->name .
            "\n"."เลขบัตรประชาชน : " . $skpcard->cid .
            "\n"."เบอร์โทรศัพท์ : " . $skpcard->phone .
            "\n"."วันที่ซื้อบัตร : " .  $skpcard->buy_date . 
            "\n"."จำนวนเงิน : " . $skpcard->price .  
            "\n"."เลขที่ใบเสร็จ : " . $skpcard->rcpt .     
            "\n"; 

        function notify_message($message,$token){           
            if($token !== '' && $token !== null){
            $chOne = curl_init();
            curl_setopt( $chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify");
            curl_setopt( $chOne, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt( $chOne, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt( $chOne, CURLOPT_POST, 1);
            curl_setopt( $chOne, CURLOPT_POSTFIELDS, $message);
            curl_setopt( $chOne, CURLOPT_POSTFIELDS, "message=$message");
            curl_setopt( $chOne, CURLOPT_FOLLOWLOCATION, 1);
            $headers = array( 'Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer '.$token.'', );
            curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers);
            curl_setopt( $chOne, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec( $chOne );
            if(curl_error($chOne)) { echo 'error:' . curl_error($chOne); }
            else { $result_ = json_decode($result, true);
            echo "status : ".$result_['status']; echo "message : ". $result_['message']; }
            curl_close( $chOne ); }
        } 
            foreach($line_token_array as $token){
                    notify_message($message,$token);
            }  

        return redirect()->route('skpcard.index')->with('success', 'เพิ่มข้อมูลบัตรสังฆประชาร่วมใจเรียบร้อย');
    }

//Edit Resource     
public function edit($id)
    {
        $skp=Skpcard::where('id',$id)->get(); 
        return view('skpcard.edit',compact('skp'));
    }

//Update Update 
public function update(Request $request , $id)
    {
        $request->validate([
            'cid' => 'required',
            'name' => 'required',            
            'address' => 'required',
            'phone' => 'required',
            'buy_date' => 'required',
            'price' => 'required',
            'rcpt' => 'required'
        ]);               
      
        $buy_date=$request->buy_date;
        $ex_date=date('Y-m-d',strtotime('1 year',strtotime($buy_date)));
        $skpcard = Skpcard::find($id);
        $skpcard->cid = $request->cid;
        $skpcard->name = $request->name;
        $skpcard->birthday = $request->birthday;
        $skpcard->address = $request->address;
        $skpcard->phone = $request->phone;
        $skpcard->buy_date = $request->buy_date;
        $skpcard->ex_date = $ex_date;
        $skpcard->price = $request->price;
        $skpcard->rcpt = $request->rcpt;
        $skpcard->save();  

        return redirect()->route('skpcard.index')->with('success', 'แก้ไขข้อมูลบัตรสังฆประชาร่วมใจเรียบร้อย');
    }
}
