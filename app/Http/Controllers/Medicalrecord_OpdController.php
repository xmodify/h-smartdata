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
    $hcode = "10989";
    $start_date = Session::get('start_date');
    $end_date = Session::get('end_date');
    //$cid ="1341800057879";
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date = Session::get('start_date');}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date = Session::get('end_date');}
    
    $url = "https://authenservice.nhso.go.th/authencode/api/authencode-report?hcode=$hcode&claimDateFrom=$start_date&claimDateTo=$end_date&size=1000";
    $cookies  = request()->cookies->all();   
    $response = Http::withBasicAuth('6213043246152', 'h10989')
                ->withCookies($cookies, $url)    
                ->get($url);
    $authen= $response['content'];  
  
    foreach ($authen as $value) {   
        $transId = $value['transId'];   
        $hmain = $value['hmain'];    
        $hname = $value['hname'];              
        $personalId = $value['personalId'];
        //$patientName = $value['patientName'];
        if (isset($value['patientName'])) {$patientName = $value['patientName'];} else {$patientName = '';}
        //$moonanName = $value['moonanName'];
        if (isset($value['moonanName'])) {$moonanName = $value['moonanName'];} else {$moonanName = '';}
        //$tumbonName = $value['tumbonName'];
        if (isset($value['tumbonName'])) {$tumbonName = $value['tumbonName'];} else {$tumbonName = '';}
        //$amphurName = $value['amphurName'];
        if (isset($value['amphurName'])) {$amphurName = $value['amphurName'];} else {$amphurName = '';}
        //$changwatName = $value['changwatName'];
        if (isset($value['changwatName'])) {$changwatName = $value['changwatName'];} else {$changwatName = '';}
        //$mainInscl = $value['mainInscl'];
        if (isset($value['mainInscl'])) {$mainInscl = $value['mainInscl'];} else {$mainInscl = '';}
        //$mainInsclName = $value['mainInsclName'];
        if (isset($value['mainInsclName'])) {$mainInsclName = $value['mainInsclName'];} else {$mainInsclName = '';}
        //$subInscl = $value['subInscl'];
        if (isset($value['subInscl'])) {$subInscl = $value['subInscl'];} else {$subInscl = '';}
        //$subInsclName = $value['subInsclName'];
        if (isset($value['subInsclName'])) {$subInsclName = $value['subInsclName'];} else {$subInsclName = '';}
        //$claimStatus = $value['claimStatus'];   
        if (isset($value['claimStatus'])) {$claimStatus = $value['claimStatus'];} else {$claimStatus = '';}
        //$claimCode = $value['claimCode'];
        if (isset($value['claimCode'])) {$claimCode = $value['claimCode'];} else {$claimCode = '';}
        //$claimType = $value['claimType'];
        if (isset($value['claimType'])) {$claimType = $value['claimType'];} else {$claimType = '';}
        //$claimTypeName = $value['claimTypeName'];
        if (isset($value['claimTypeName'])) {$claimTypeName = $value['claimTypeName'];} else {$claimTypeName = '';}
        //$claimDate = $value['claimDate'];
        if (isset($value['claimDate'])) {$claimDate = $value['claimDate'];} else {$claimDate = '';}
        $createDate = $value['createDate'];
        //$claimDate_old =  explode("T",$value['claimDate']);
        //$claimDate = $claimDate_old[0];
        //$claimTime = $claimDate_old[1]; 
        //$sourceChannel = $value['sourceChannel'];  
        if (isset($value['sourceChannel'])) {$sourceChannel = $value['sourceChannel'];} else {$sourceChannel = '';}
        $claimAuthen = $value['claimAuthen'];  

        $check_authen = Nhso_Endpoint::where('transId',$transId)->count();
        if ( $check_authen > 0 ) {
            Nhso_Endpoint::where('transId',$transId)->update([
                'claimCode'         => $claimCode,
                'claimType'         => $claimType,
                'claimTypeName'     => $claimTypeName,
                'claimStatus'       => $claimStatus
            ]);
        } else if ( $sourceChannel=='ENDPOINT' || $claimType == "PG0140001" ) {
            Nhso_Endpoint::insert([   
                'transId'           => $transId,                 
                'hmain'             => $hmain,
                'hname'             => $hname,
                'personalId'        => $personalId,
                'patientName'       => $patientName,
                'moonanName'        => $moonanName,
                'tumbonName'        => $tumbonName,
                'amphurName'        => $amphurName,
                'changwatName'      => $changwatName,               
                'mainInscl'         => $mainInscl,
                'mainInsclName'     => $mainInsclName,
                'subInscl'          => $subInscl,
                'subInsclName'      => $subInsclName,
                'claimStatus'       => $claimStatus,            
                'claimCode'         => $claimCode,
                'claimType'         => $claimType,
                'claimTypeName'     => $claimTypeName,
                'claimDate'         => $claimDate, 
                'createDate'        => $claimDate,   
                'sourceChannel'     => $sourceChannel,   
                'claimAuthen'       => $claimAuthen                        
            ]);   
        }    
    }   
    return back(); 
}    
//Create nhso_endpoint_pull_indiv
public function nhso_endpoint_pull_indiv(Request $request,$vstdate,$cid)
{
    $date_now = date('Y-m-d');  
    $headers = array();
    $headers[] = "Accept: application/json";
    $headers[] = "Authorization: Bearer 7a4dba2d-a1f4-4638-883e-bf09adf0990e"; 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://authenucws.nhso.go.th/authencodestatus/api/check-authen-status?personalId=$cid&serviceDate=$vstdate");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $contents = $response;
    $result = json_decode($contents, true);  

    $firstName = $result['firstName'];
    $lastName  = $result['lastName'];
    $mainInscl  = $result['mainInscl']['id'];
    $mainInsclName  = $result['mainInscl']['name'];
    $subInscl  = $result['subInscl']['id'];
    $subInsclName  = $result['subInscl']['name'];

    $service= $result['serviceHistories'];   

    foreach($service as $row){
        $serviceDateTime=$row['serviceDateTime'];
        // $sourceChannel=$row['sourceChannel'];
        if (isset($row['sourceChannel'])) {$sourceChannel = $row['sourceChannel'];} else {$sourceChannel = '';}
        $claimCode=$row['claimCode'];
        $claimType=$row['service']['code'];

        $check_indiv = Nhso_Endpoint_Indiv::where('cid',$cid)
            ->Where('claimCode',$claimCode)
            ->count();
        if ( $check_indiv > 0) {
            Nhso_Endpoint_Indiv::where('cid',$cid)
            ->Where('claimCode',$claimCode)
            ->update([
                'claimCode'     => $claimCode,
                'claimType'  => $claimType
            ]);   
        }
        else if ( $sourceChannel=='ENDPOINT'|| $claimType == "PG0140001"){
            $endpoint_indiv = new Nhso_Endpoint_Indiv;  
            $endpoint_indiv->cid=$cid;
            $endpoint_indiv->firstName=$firstName;
            $endpoint_indiv->lastName=$lastName;
            $endpoint_indiv->mainInscl=$mainInscl;
            $endpoint_indiv->mainInsclName=$mainInsclName;
            $endpoint_indiv->subInscl=$subInscl;
            $endpoint_indiv->subInsclName=$subInsclName;
            $endpoint_indiv->serviceDateTime=$serviceDateTime;
            $endpoint_indiv->sourceChannel=$sourceChannel;
            $endpoint_indiv->claimCode=$claimCode;
            $endpoint_indiv->claimType=$claimType;
            $endpoint_indiv->save();   
            }            
    }
    return back();
}
}

