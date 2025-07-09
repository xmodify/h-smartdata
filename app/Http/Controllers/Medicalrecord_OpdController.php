<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Nhso_Endpoint;
use App\Models\Nhso_Endpoint_Indiv;

class Medicalrecord_OpdController extends Controller
{
//Check Login
public function __construct()
{
      $this->middleware('auth');
}
//Create index
public function index()
{
      return view('medicalrecord_opd.index');          
}
//Create non_Authen
public function non_authen(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
      
    $ucs=DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,p.cid,p.mobile_phone_number,p.hometel,
        p1.`name` AS pttype,k.department,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y
        FROM ovst o
        LEFT JOIN vn_stat v ON v.vn=o.vn
        LEFT JOIN patient p ON p.hn=o.hn
        LEFT JOIN visit_pttype v1 ON v1.vn=o.vn
        LEFT JOIN pttype p1 ON p1.pttype=v1.pttype
        LEFT JOIN kskdepartment k ON k.depcode=o.main_dep
        WHERE p1.hipdata_code ="UCS" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND (v1.auth_code IS NULL OR v1.auth_code ="")           
        GROUP BY o.vn 
        ORDER BY o.vsttime');  

    $non_ucs=DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,p.cid,p.mobile_phone_number,p.hometel,
        p1.`name` AS pttype,k.department,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y
        FROM ovst o
        LEFT JOIN vn_stat v ON v.vn=o.vn
        LEFT JOIN patient p ON p.hn=o.hn
        LEFT JOIN visit_pttype v1 ON v1.vn=o.vn
        LEFT JOIN pttype p1 ON p1.pttype=v1.pttype
        LEFT JOIN kskdepartment k ON k.depcode=o.main_dep
        WHERE p1.hipdata_code <>"UCS" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND (v1.auth_code IS NULL OR v1.auth_code ="")           
        GROUP BY o.vn 
        ORDER BY o.vsttime');  
      
      return view('medicalrecord_opd.non_authen',compact('start_date','end_date','ucs','non_ucs'));        
}

//Create non_hospmain
public function non_hospmain(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
      
    $ucs=DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,p.cid,p.mobile_phone_number,p.hometel,v1.auth_code,
        p1.`name` AS pttype,k.department,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y
        FROM ovst o
        LEFT JOIN vn_stat v ON v.vn=o.vn
        LEFT JOIN patient p ON p.hn=o.hn
        LEFT JOIN visit_pttype v1 ON v1.vn=o.vn
        LEFT JOIN pttype p1 ON p1.pttype=v1.pttype
        LEFT JOIN kskdepartment k ON k.depcode=o.main_dep
        WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND (p1.hipdata_code = "UCS" OR p1.hipdata_code ="SSS")
        AND (v1.hospmain="" OR v1.hospmain IS NULL)         
        GROUP BY o.vn 
        ORDER BY o.vsttime'); 
      
      return view('medicalrecord_opd.non_hospmain',compact('start_date','end_date','ucs'));        
}

//Create nhso_authen
public function nhso_authen(Request $request)
{ 
    $date_now = date('Y-m-d'); 
    $cid     = "1411000087764";
    $headers = array();
    $headers[] = "Accept: application/json";
    $headers[] = "Authorization: Bearer 7a4dba2d-a1f4-4638-883e-bf09adf0990e"; 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://authenucws.nhso.go.th/authencodestatus/api/check-authen-status?personalId=$cid&serviceDate=$date_now");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $contents = $response;
    $result = json_decode($contents, true);
dd($result);
    return response($result);
}      

//Create nhso_endpoint
public function nhso_endpoint(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}
      
    $nhso_endpoint=DB::select('
        SELECT * FROM nhso_endpoint 
        WHERE DATE(claimDate) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
         '); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);  
    $request->session()->save();

    return view('medicalrecord_opd.nhso_endpoint',compact('start_date','end_date','nhso_endpoint'));        
}

//Create nhso_endpoint_pull
public function nhso_endpoint_pull(Request $request)
{   
    $vstdate = $request->input('vstdate') ?? now()->format('Y-m-d'); 
    $hosxp = DB::connection('hosxp')->select('
        SELECT o.vn, o.hn, pt.cid, vp.auth_code
        FROM ovst o
        INNER JOIN visit_pttype vp ON vp.vn = o.vn 
        LEFT JOIN patient pt ON pt.hn = o.hn
        WHERE o.vstdate = ?
        AND vp.auth_code NOT LIKE "EP%" 
        AND vp.auth_code <> "" AND vp.auth_code IS NOT NULL', [$vstdate]);  

    $cids = array_column($hosxp, 'cid');      
    $token = DB::table('main_setting')
        ->where('name', 'token_authen_kiosk_nhso')
        ->value('value');
 
    foreach ($cids as $cid) {
        $response = Http::timeout(10)  // สูงสุดรอ 10 วิ ต่อ 1 request
            ->withToken($token)
            ->acceptJson()
            ->get('https://authenucws.nhso.go.th/authencodestatus/api/check-authen-status', [
                'personalId' => $cid,
                'serviceDate' => $vstdate
            ]);

        if ($response->failed()) {
            \Log::warning("ดึงข้อมูลไม่สำเร็จสำหรับ CID: $cid", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            continue;
        }

        $result = $response->json();

        if (!isset($result['firstName']) || empty($result['serviceHistories'])) {
            continue;
        }

        $firstName = $result['firstName'];
        $lastName  = $result['lastName'];
        $mainInscl = $result['mainInscl']['id'] ?? null;
        $mainInsclName = $result['mainInscl']['name'] ?? null;
        $subInscl = $result['subInscl']['id'] ?? null;
        $subInsclName = $result['subInscl']['name'] ?? null;

        foreach ($result['serviceHistories'] as $row) {
            $serviceDateTime = $row['serviceDateTime'] ?? null;
            $sourceChannel = $row['sourceChannel'] ?? '';
            $claimCode = $row['claimCode'] ?? null;
            $claimType = $row['service']['code'] ?? null;

            if (!$claimCode) continue;

            $exists = Nhso_Endpoint_Indiv::where('cid', $cid)
                ->where('claimCode', $claimCode)
                ->exists();

            if ($exists) {
                Nhso_Endpoint_Indiv::where('cid', $cid)
                    ->where('claimCode', $claimCode)
                    ->update([
                        'claimType' => $claimType
                    ]);
            } elseif ($sourceChannel == 'ENDPOINT' || $claimType == 'PG0140001') {
                Nhso_Endpoint_Indiv::create([
                    'cid' => $cid,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'mainInscl' => $mainInscl,
                    'mainInsclName' => $mainInsclName,
                    'subInscl' => $subInscl,
                    'subInsclName' => $subInsclName,
                    'serviceDateTime' => $serviceDateTime,
                    'sourceChannel' => $sourceChannel,
                    'claimCode' => $claimCode,
                    'claimType' => $claimType,
                ]);
            }
        }
    }
 
    return response()->json(['success' => true, 'message' => 'ดึงข้อมูลจาก สปสช สำเร็จ' ]);
}

//Create nhso_endpoint_pull_indiv
// public function nhso_endpoint_pull_indiv(Request $request,$vstdate,$cid)
// {
//     $date_now = date('Y-m-d');  
//     $token = DB::table('main_setting')
//         ->where('name', 'token_authen_kiosk_nhso')
//         ->value('value');
//     $headers = array();
//     $headers[] = "Accept: application/json"; 
//     $headers[] = "Authorization: Bearer $token";
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, "https://authenucws.nhso.go.th/authencodestatus/api/check-authen-status?personalId=$cid&serviceDate=$vstdate");
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
//     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//     $response = curl_exec($ch);
//     $contents = $response;
//     $result = json_decode($contents, true);  

//     $firstName = $result['firstName'];
//     $lastName  = $result['lastName'];
//     $mainInscl  = $result['mainInscl']['id'];
//     $mainInsclName  = $result['mainInscl']['name'];
//     $subInscl  = $result['subInscl']['id'];
//     $subInsclName  = $result['subInscl']['name'];

//     $service= $result['serviceHistories'];   

//     foreach($service as $row){
//         $serviceDateTime=$row['serviceDateTime'];
//         // $sourceChannel=$row['sourceChannel'];
//         if (isset($row['sourceChannel'])) {$sourceChannel = $row['sourceChannel'];} else {$sourceChannel = '';}
//         $claimCode=$row['claimCode'];
//         $claimType=$row['service']['code'];

//         $check_indiv = Nhso_Endpoint_Indiv::where('cid',$cid)
//             ->Where('claimCode',$claimCode)
//             ->count();
//         if ( $check_indiv > 0) {
//             Nhso_Endpoint_Indiv::where('cid',$cid)
//             ->Where('claimCode',$claimCode)
//             ->update([
//                 'claimCode'     => $claimCode,
//                 'claimType'  => $claimType
//             ]);   
//         }
//         else if ( $sourceChannel=='ENDPOINT'|| $claimType == "PG0140001"){
//             $endpoint_indiv = new Nhso_Endpoint_Indiv;  
//             $endpoint_indiv->cid=$cid;
//             $endpoint_indiv->firstName=$firstName;
//             $endpoint_indiv->lastName=$lastName;
//             $endpoint_indiv->mainInscl=$mainInscl;
//             $endpoint_indiv->mainInsclName=$mainInsclName;
//             $endpoint_indiv->subInscl=$subInscl;
//             $endpoint_indiv->subInsclName=$subInsclName;
//             $endpoint_indiv->serviceDateTime=$serviceDateTime;
//             $endpoint_indiv->sourceChannel=$sourceChannel;
//             $endpoint_indiv->claimCode=$claimCode;
//             $endpoint_indiv->claimType=$claimType;
//             $endpoint_indiv->save();   
//             }            
//     }
//     return back();
// }

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

        $indiv = Nhso_Endpoint_Indiv::firstOrNew([
            'cid' => $cid,
            'claimCode' => $claimCode,
        ]);

        // ถ้าเป็นรายการใหม่ หรือแก้ไขได้ตามเงื่อนไข
        if (!$indiv->exists || $sourceChannel == 'ENDPOINT' || $claimType == 'PG0140001') {
            $indiv->firstName = $firstName;
            $indiv->lastName = $lastName;
            $indiv->mainInscl = $mainInscl;
            $indiv->mainInsclName = $mainInsclName;
            $indiv->subInscl = $subInscl;
            $indiv->subInsclName = $subInsclName;
            $indiv->serviceDateTime = $serviceDateTime;
            $indiv->sourceChannel = $sourceChannel;
            $indiv->claimType = $claimType;
            $indiv->save();
        }
    }

//    return back();
   return response()->json(['success' => true]);
   
}


}

