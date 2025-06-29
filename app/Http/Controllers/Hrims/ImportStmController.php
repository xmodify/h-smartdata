<?php

namespace App\Http\Controllers\Hrims;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Finance_stm_ofc;
use App\Models\Finance_stm_ofcexcel;
use App\Models\Finance_stm_ofc_kidney;
use App\Models\Finance_stm_lgo;
use App\Models\Finance_stm_lgoexcel;
use App\Models\Finance_stm_lgo_kidney;
use App\Models\Finance_stm_lgo_kidneyexcel;
use App\Models\Finance_stm_sss_kidney;
use App\Models\Finance_stm_ucs;
use App\Models\Finance_stm_ucsexcel;
use App\Models\Finance_stm_ucs_kidney;
use App\Models\Finance_stm_ucs_kidneyexcel;

class ImportStmController extends Controller
{

//Check Login
public function __construct()
{
    $this->middleware('auth');
}

//Create index
public function index()
{
    return view('hrims.import_stm.index');
}

//Create ofc
public function ofc(Request $request)
{  
    $stm_ofc=DB::select('
        SELECT  stm_filename,COUNT(DISTINCT repno) AS count_repno,COUNT(cid) AS count_cid,
        SUM(adjrw) AS sum_adjrw,SUM(charge) AS sum_charge,SUM(act) AS sum_act,
        SUM(receive_room) AS sum_receive_room,SUM(receive_instument) AS sum_receive_instument,
        SUM(receive_drug) AS sum_receive_drug,SUM(receive_treatment) AS sum_receive_treatment,
        SUM(receive_car) AS sum_receive_car,SUM(receive_waitdch) AS sum_receive_waitdch,
        SUM(receive_other) AS sum_receive_other,SUM(receive_total) AS sum_receive_total
        FROM finance_stm_ofc GROUP BY stm_filename ORDER BY repno');    

    return view('hrims.import_stm.ofc',compact('stm_ofc'));
}

// ofcexcel_save
public function ofc_save(Request $request)
{
    // Set the execution time to 300 seconds (5 minutes)
    set_time_limit(300);

    $this->validate($request, [
        'file' => 'required|file|mimes:xls,xlsx'
    ]);
    $the_file = $request->file('file');
    $file_name = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์

    try{
        $spreadsheet = IOFactory::load($the_file->getRealPath());
        // $sheet        = $spreadsheet->getActiveSheet();
        $sheet        = $spreadsheet->setActiveSheetIndex(0);
        $row_limit    = $sheet->getHighestDataRow();
        $column_limit = $sheet->getHighestDataColumn();
        $row_range    = range( '12', $row_limit );
        // $row_range    = range( "!", $row_limit );
        $column_range = range( 'T', $column_limit );
        $startcount = '12';
        // $row_range_namefile  = range( 9, $sheet->getCell( 'A' . $row )->getValue() );
        $data = array();
        foreach ($row_range as $row ) {

            $adm = $sheet->getCell( 'G' . $row )->getValue();          
            $day = substr($adm, 0, 2);
            $mo = substr($adm, 3, 2);
            $year = substr($adm, 7, 4);
            $admtime = substr($adm, 12, 8);
            $datetimeadm = $year.'-'.$mo.'-'.$day.' '.$admtime;

            $dch = $sheet->getCell( 'H' . $row )->getValue();            
            $dchday = substr($dch, 0, 2);
            $dchmo = substr($dch, 3, 2);
            $dchyear = substr($dch, 7, 4);
            $dchtime = substr($dch, 12, 8);
            $datetimedch = $dchyear.'-'.$dchmo.'-'.$dchday.' '.$dchtime;            

                $data[] = [
                    'repno'             =>$sheet->getCell( 'A' . $row )->getValue(),
                    'no'                =>$sheet->getCell( 'B' . $row )->getValue(),
                    'hn'                =>$sheet->getCell( 'C' . $row )->getValue(),
                    'an'                =>$sheet->getCell( 'D' . $row )->getValue(),
                    'cid'               =>$sheet->getCell( 'E' . $row )->getValue(),
                    'pt_name'           =>$sheet->getCell( 'F' . $row )->getValue(),
                    'datetimeadm'       =>$datetimeadm,
                    'datetimedch'       =>$datetimedch,
                    'projcode'          =>$sheet->getCell( 'I' . $row )->getValue(),
                    'adjrw'             =>$sheet->getCell( 'J' . $row )->getValue(),
                    'charge'            =>$sheet->getCell( 'K' . $row )->getValue(),
                    'act'               =>$sheet->getCell( 'L' . $row )->getValue(),
                    'receive_room'      =>$sheet->getCell( 'M' . $row )->getValue(),
                    'receive_instument' =>$sheet->getCell( 'N' . $row )->getValue(),
                    'receive_drug'      =>$sheet->getCell( 'O' . $row )->getValue(),
                    'receive_treatment' =>$sheet->getCell( 'P' . $row )->getValue(),
                    'receive_car'       =>$sheet->getCell( 'Q' . $row )->getValue(),
                    'receive_waitdch'   =>$sheet->getCell( 'R' . $row )->getValue(),
                    'receive_other'     =>$sheet->getCell( 'S' . $row )->getValue(),
                    'receive_total'     =>$sheet->getCell( 'T' . $row )->getValue(),
                    'stm_filename'      =>$file_name,
                ]; 
            $startcount++;            
        }

        $for_insert = array_chunk($data, 1000);
        foreach ($for_insert as $key => $data_) {
            Finance_stm_ofcexcel::insert($data_);                 
        }

    } 
    
    catch (Exception $e) {
        $error_code = $e->errorInfo[1];
        return back()->withErrors('There was a problem uploading the data!');
    }
// ***************************************************************************************************************************** 
        $stm_ofcexcel=Finance_stm_ofcexcel::whereNotNull('charge')
                    ->Where('charge','<>', 'เรียกเก็บ')->get();
                    
        foreach ($stm_ofcexcel as $key => $value) {

            $check = Finance_stm_ofc::where('repno','=',$value->repno)->where('no','=',$value->no)->count();
                if ($check > 0) {
                    Finance_stm_ofc::where('repno','=',$value->repno)->where('no','=',$value->no)->update([
                        'datetimeadm'           => $value->datetimeadm,
                        'datetimedch'           => $value->datetimedch,
                        'charge'                => $value->charge,
                        'receive_room'          => $value->receive_room,
                        'receive_instument'     => $value->receive_instument,
                        'receive_drug'          => $value->receive_drug,
                        'receive_treatment'     => $value->receive_treatment,
                        'receive_car'           => $value->receive_car,
                        'receive_waitdch'       => $value->receive_waitdch,
                        'receive_other'         => $value->receive_other,
                        'receive_total'         => $value->receive_total,
                        'stm_filename'          => $value->stm_filename
                    ]); 
                } else {
                    $add = new Finance_stm_ofc();
                    $add->repno                 = $value->repno;
                    $add->no                    = $value->no;
                    $add->hn                    = $value->hn;
                    $add->an                    = $value->an;
                    $add->cid                   = $value->cid;
                    $add->pt_name               = $value->pt_name;
                    $add->datetimeadm           = $value->datetimeadm;
                    $add->datetimedch           = $value->datetimedch;
                    $add->projcode              = $value->projcode;
                    $add->adjrw                 = $value->adjrw;
                    $add->charge                = $value->charge;
                    $add->act                   = $value->act;
                    $add->receive_room          = $value->receive_room;
                    $add->receive_instument     = $value->receive_instument;
                    $add->receive_drug          = $value->receive_drug;
                    $add->receive_treatment     = $value->receive_treatment;
                    $add->receive_car           = $value->receive_car;
                    $add->receive_waitdch       = $value->receive_waitdch;
                    $add->receive_other         = $value->receive_other;
                    $add->receive_total         = $value->receive_total;
                    $add->stm_filename          = $value->stm_filename;
                    $add->save(); 
                } 
        }                
        Finance_stm_ofcexcel::truncate(); 
        
    return redirect()->route('hrims.import_stm.ofc')->with('success',$file_name);
}

//Create ofc_detail
public function ofc_detail(Request $request)
{  
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d', strtotime("last day of this month"));}else{$end_date =$request->end_date;}

    $stm_ofc_list=DB::select('
        SELECT IF(SUBSTRING(stm_filename,11) LIKE "O%","OPD","IPD") AS dep,stm_filename,repno,
        hn,an,pt_name,datetimeadm,datetimedch,adjrw,charge,act,receive_room,receive_instument,
        receive_drug,receive_treatment,receive_car,receive_waitdch,receive_other,receive_total
        FROM finance_stm_ofc
        WHERE DATE(datetimeadm) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND SUBSTRING(stm_filename,11) LIKE "O%"
        GROUP BY stm_filename,repno,hn,datetimeadm 
        ORDER BY dep DESC,repno');

    $stm_ofc_list_ip=DB::select('
        SELECT IF(SUBSTRING(stm_filename,11) LIKE "O%","OPD","IPD") AS dep,stm_filename,repno,
        hn,an,pt_name,datetimeadm,datetimedch,adjrw,charge,act,receive_room,receive_instument,
        receive_drug,receive_treatment,receive_car,receive_waitdch,receive_other,receive_total
        FROM finance_stm_ofc 
        WHERE DATE(datetimedch) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND SUBSTRING(stm_filename,11) LIKE "I%"
        GROUP BY stm_filename,repno,hn,datetimeadm 
        ORDER BY dep DESC,repno');

    return view('hrims.import_stm.ofc_detail',compact('start_date','end_date','stm_ofc_list','stm_ofc_list_ip'));
}

//Create ofc_kidney
public function ofc_kidney(Request $request)
{  
    $stm_ofc_kidney=DB::select('
        SELECT stmdoc,station,COUNT(*) AS count_no,	
	    SUM(amount) AS amount FROM finance_stm_ofc_kidney 
        GROUP BY stmdoc,station ORDER BY station ,stmdoc');
    

    return view('hrims.import_stm.ofc_kidney',compact('stm_ofc_kidney'));
}

//Create ofc_kidney XML File
public function ofc_kidney_save(Request $request)
{  
    // Set the execution time to 300 seconds (5 minutes)
    set_time_limit(300);

        $tar_file_ = $request->file; 
        $file_ = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์
        $filename = pathinfo($file_, PATHINFO_FILENAME);
        $extension = pathinfo($file_, PATHINFO_EXTENSION);  
        $xmlString = file_get_contents(($tar_file_));
        $xmlObject = simplexml_load_string($xmlString);
        $json = json_encode($xmlObject); 
        $result = json_decode($json, true); 
    
        // dd($result);

        @$hcode = $result['hcode'];
        @$hname = $result['hname'];
        @$STMdoc = $result['STMdoc'];       
        @$TBills = $result['TBills']['TBill']; 
        $bills_       = @$TBills;  
        
            foreach ($bills_ as $value) {                     
                $hreg = $value['hreg'];
                $station = $value['station'];
                $invno = $value['invno'];
                $hn = $value['hn']; 
                $amount = $value['amount'];
                $paid = $value['paid'];
                $rid = $value['rid']; 
                $HDflag = $value['HDflag']; 
                $dttran = $value['dttran'];                     
                $dttranDate = explode("T",$value['dttran']);
                $dttdate = $dttranDate[0];
                $dtttime = $dttranDate[1];
                $checkc = Finance_stm_ofc_kidney::where('hn', $hn)->where('vstdate', $dttdate)->count();
                if ( $checkc > 0) {
                    Finance_stm_ofc_kidney::where('hn', $hn)->where('vstdate', $dttdate) 
                        ->update([   
                            'invno'            => $invno,
                            'dttran'           => $dttran, 
                            'hn'               => $hn, 
                            'amount'           => $amount, 
                            'paid'             => $paid,
                            'rid'              => $rid, 
                            'HDflag'           => $HDflag,
                            'vstdate'          => $dttdate,
                            'vsttime'          => $dtttime                                
                        ]);

                } else {
                        Finance_stm_ofc_kidney::insert([                            
 
                            'hcode'              => @$hcode, 
                            'hname'              => @$hname,
                            'stmdoc'             => @$STMdoc,
                            'station'            => $station, 
                            'hreg'               => $hreg,
                            'hn'                 => $hn,
                            'invno'              => $invno,
                            'dttran'             => $dttran,
                            'vstdate'            => $dttdate,
                            'vsttime'            => $dtttime,
                            'amount'             => $amount,
                            'paid'               => $paid,
                            'rid'                => $rid,
                            'hdflag'             => $HDflag
                        ]);   
                } 
            }            
      
    return redirect()->route('hrims.import_stm.ofc_kidney')->with('success',@$STMdoc);
}

//Create ofc_kidneydetail
public function ofc_kidneydetail(Request $request)
{  
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d', strtotime("last day of this month"));}else{$end_date =$request->end_date;}

    $stm_ofc_kidney_list=DB::select('
        SELECT hcode,hname,stmdoc,station,hreg,hn,invno,dttran,paid,rid,amount,hdflag
        FROM finance_stm_ofc_kidney  WHERE DATE(dttran) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        ORDER BY station ,stmdoc');

    return view('hrims.import_stm.ofc_kidneydetail',compact('start_date','end_date','stm_ofc_kidney_list'));
}

//Create lgo
public function lgo(Request $request)
{  
    $stm_lgo=DB::select('
        SELECT dep,stm_filename,repno,COUNT(repno) AS count_no,SUM(adjrw) AS adjrw,SUM(payrate) AS payrate,
        SUM(charge_treatment) AS charge_treatment,SUM(compensate_treatment) AS compensate_treatment,
        SUM(case_iplg) AS case_iplg,SUM(case_oplg) AS case_oplg,SUM(case_palg) AS case_palg,
        SUM(case_inslg) AS case_inslg,SUM(case_otlg) AS case_otlg,SUM(case_pp) AS case_pp,SUM(case_drug) AS case_drug
        FROM finance_stm_lgo GROUP BY stm_filename,repno ORDER BY dep DESC,repno');

    return view('hrims.import_stm.lgo',compact('stm_lgo'));
}

// lgo_save
public function lgo_save(Request $request)
{
    // Set the execution time to 300 seconds (5 minutes)
    set_time_limit(300);

    $this->validate($request, [
        'file' => 'required|file|mimes:xls,xlsx'
    ]);
    $the_file = $request->file('file');
    $file_name = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์

    try{
        $spreadsheet = IOFactory::load($the_file->getRealPath());
        $sheet        = $spreadsheet->setActiveSheetIndex(0); //sheet
        $row_limit    = $sheet->getHighestDataRow();
        // $column_limit = $sheet->getHighestDataColumn();
        $row_range    = range( '8', $row_limit );
        $startcount = '8';
        
        $data = array();
        foreach ($row_range as $row ) {

            $adm = $sheet->getCell( 'I' . $row )->getValue(); 
            $day = substr($adm, 0, 2);
            $mo = substr($adm, 3, 2);
            $year = substr($adm, 6, 4);     
            $admtime = substr($adm, 11, 8);  
            $datetimeadm = $year.'-'.$mo.'-'.$day.' '.$admtime;

            $dch = $sheet->getCell( 'J' . $row )->getValue();
            $dchday = substr($dch, 0, 2);
            $dchmo = substr($dch, 3, 2);
            $dchyear = substr($dch, 6, 4);
            $dchtime = substr($dch, 11, 8);
            $datetimedch = $dchyear.'-'.$dchmo.'-'.$dchday.' '.$dchtime;          

                $data[] = [
                    'repno'                 =>$sheet->getCell( 'A' . $row )->getValue(),
                    'no'                    =>$sheet->getCell( 'B' . $row )->getValue(),
                    'tran_id'               =>$sheet->getCell( 'C' . $row )->getValue(),
                    'hn'                    =>$sheet->getCell( 'D' . $row )->getValue(),
                    'an'                    =>$sheet->getCell( 'E' . $row )->getValue(),
                    'cid'                   =>$sheet->getCell( 'F' . $row )->getValue(),
                    'pt_name'               =>$sheet->getCell( 'G' . $row )->getValue(),
                    'dep'                   =>$sheet->getCell( 'H' . $row )->getValue(),
                    'datetimeadm'           =>$datetimeadm,
                    'datetimedch'           =>$datetimedch,
                    'compensate_treatment'  =>$sheet->getCell( 'K' . $row )->getValue(),
                    'compensate_nhso'       =>$sheet->getCell( 'L' . $row )->getValue(),
                    'error_code'            =>$sheet->getCell( 'M' . $row )->getValue(),
                    'fund'                  =>$sheet->getCell( 'N' . $row )->getValue(),
                    'service_type'          =>$sheet->getCell( 'O' . $row )->getValue(),
                    'refer'                 =>$sheet->getCell( 'P' . $row )->getValue(),
                    'have_rights'           =>$sheet->getCell( 'Q' . $row )->getValue(),
                    'use_rights'            =>$sheet->getCell( 'R' . $row )->getValue(),
                    'main_rights'           =>$sheet->getCell( 'S' . $row )->getValue(),
                    'secondary_rights'      =>$sheet->getCell( 'T' . $row )->getValue(),
                    'href'                  =>$sheet->getCell( 'U' . $row )->getValue(),
                    'hcode'                 =>$sheet->getCell( 'V' . $row )->getValue(),
                    'prov1'                 =>$sheet->getCell( 'W' . $row )->getValue(),
                    'hospcode'              =>$sheet->getCell( 'X' . $row )->getValue(),
                    'hospname'              =>$sheet->getCell( 'Y' . $row )->getValue(),
                    'proj'                  =>$sheet->getCell( 'Z' . $row )->getValue(),
                    'pa'                    =>$sheet->getCell( 'AA' . $row )->getValue(),
                    'drg'                   =>$sheet->getCell( 'AB' . $row )->getValue(),
                    'rw'                    =>$sheet->getCell( 'AC' . $row )->getValue(),
                    'charge_treatment'      =>$sheet->getCell( 'AD' . $row )->getValue(),
                    'charge_pp'             =>$sheet->getCell( 'AE' . $row )->getValue(),
                    'withdraw'              =>$sheet->getCell( 'AF' . $row )->getValue(),
                    'non_withdraw'          =>$sheet->getCell( 'AG' . $row )->getValue(),
                    'pay'                   =>$sheet->getCell( 'AH' . $row )->getValue(),
                    'payrate'               =>$sheet->getCell( 'AI' . $row )->getValue(),
                    'delay'                 =>$sheet->getCell( 'AJ' . $row )->getValue(),
                    'delay_percent'         =>$sheet->getCell( 'AK' . $row )->getValue(),
                    'ccuf'                  =>$sheet->getCell( 'AL' . $row )->getValue(),
                    'adjrw'                 =>$sheet->getCell( 'AM' . $row )->getValue(),
                    'act'                   =>$sheet->getCell( 'AN' . $row )->getValue(),
                    'case_iplg'             =>$sheet->getCell( 'AO' . $row )->getValue(),
                    'case_oplg'             =>$sheet->getCell( 'AP' . $row )->getValue(),
                    'case_palg'             =>$sheet->getCell( 'AQ' . $row )->getValue(),
                    'case_inslg'            =>$sheet->getCell( 'AR' . $row )->getValue(),
                    'case_otlg'             =>$sheet->getCell( 'AS' . $row )->getValue(),
                    'case_pp'               =>$sheet->getCell( 'AT' . $row )->getValue(),
                    'case_drug'             =>$sheet->getCell( 'AU' . $row )->getValue(),
                    'deny_iplg'             =>$sheet->getCell( 'AV' . $row )->getValue(),
                    'deny_oplg'             =>$sheet->getCell( 'AW' . $row )->getValue(),
                    'deny_palg'             =>$sheet->getCell( 'AX' . $row )->getValue(),
                    'deny_inslg'            =>$sheet->getCell( 'AY' . $row )->getValue(),
                    'deny_otlg'             =>$sheet->getCell( 'AZ' . $row )->getValue(),
                    'ors'                   =>$sheet->getCell( 'BA' . $row )->getValue(),
                    'va'                    =>$sheet->getCell( 'BB' . $row )->getValue(),
                    'audit_results'         =>$sheet->getCell( 'BC' . $row )->getValue(),
                    'stm_filename'          =>$file_name,
                ]; 
            $startcount++;            
        }

        $for_insert = array_chunk($data, 1000);
        foreach ($for_insert as $key => $data_) {
            Finance_stm_lgoexcel::insert($data_);                 
        }
    }    
    catch (Exception $e) {
        $error_code = $e->errorInfo[1];
        return back()->withErrors('There was a problem uploading the data!');
    }
// ***************************************************************************************************************************** 
        $stm_lgoexcel=Finance_stm_lgoexcel::whereNotNull('charge_treatment')->get();
                    
        foreach ($stm_lgoexcel as $key => $value) {
            $check = Finance_stm_lgo::where('repno','=',$value->repno)->where('no','=',$value->no)->count();
            if ($check > 0) {
                Finance_stm_lgo::where('repno','=',$value->repno)->where('no','=',$value->no)->update([
                        'datetimeadm'                   => $value->datetimeadm,
                        'datetimedch'                   => $value->datetimedch,
                        'compensate_treatment'          => $value->compensate_treatment,
                        'compensate_nhso'               => $value->compensate_nhso,
                        'charge_treatment'              => $value->charge_treatment,
                        'charge_pp'                     => $value->charge_pp,
                        'payrate'                       => $value->payrate,
                        'case_iplg'                     => $value->case_iplg,
                        'case_oplg'                     => $value->case_oplg,
                        'case_palg'                     => $value->case_palg,
                        'case_inslg'                    => $value->case_inslg,
                        'case_otlg'                     => $value->case_otlg,
                        'case_pp'                       => $value->case_pp,
                        'case_drug'                     => $value->case_drug,
                        'stm_filename'                  => $value->stm_filename
                        ]); 
            } else {
                    $add = new Finance_stm_lgo();
                    $add->repno                 = $value->repno;
                    $add->no                    = $value->no;
                    $add->tran_id               = $value->tran_id;
                    $add->hn                    = $value->hn;
                    $add->an                    = $value->an;
                    $add->cid                   = $value->cid;
                    $add->pt_name               = $value->pt_name;
                    $add->dep                   = $value->dep;
                    $add->datetimeadm           = $value->datetimeadm;
                    $add->datetimedch           = $value->datetimedch;
                    $add->compensate_treatment  = $value->compensate_treatment;
                    $add->compensate_nhso       = $value->compensate_nhso;
                    $add->error_code            = $value->error_code;
                    $add->fund                  = $value->fund;
                    $add->service_type          = $value->service_type;
                    $add->refer                 = $value->refer;
                    $add->have_rights           = $value->have_rights;
                    $add->use_rights            = $value->use_rights;
                    $add->main_rights           = $value->main_rights;
                    $add->secondary_rights      = $value->secondary_rights;
                    $add->href                  = $value->href;
                    $add->hcode                 = $value->hcode;
                    $add->prov1                 = $value->prov1;
                    $add->hospcode              = $value->hospcode;
                    $add->hospname              = $value->hospname;
                    $add->proj                  = $value->proj;
                    $add->pa                    = $value->pa;
                    $add->drg                   = $value->drg;
                    $add->rw                    = $value->rw;
                    $add->charge_treatment      = $value->charge_treatment;
                    $add->charge_pp             = $value->charge_pp;
                    $add->withdraw              = $value->withdraw;
                    $add->non_withdraw          = $value->non_withdraw;
                    $add->pay                   = $value->pay;
                    $add->payrate               = $value->payrate;
                    $add->delay                 = $value->delay;
                    $add->delay_percent         = $value->delay_percent;
                    $add->ccuf                  = $value->ccuf;
                    $add->adjrw                 = $value->adjrw;
                    $add->act                   = $value->act;
                    $add->case_iplg             = $value->case_iplg;
                    $add->case_oplg             = $value->case_oplg;
                    $add->case_palg             = $value->case_palg;
                    $add->case_inslg            = $value->case_inslg;
                    $add->case_otlg             = $value->case_otlg;
                    $add->case_pp               = $value->case_pp;
                    $add->case_drug             = $value->case_drug;
                    $add->deny_iplg             = $value->deny_iplg;
                    $add->deny_oplg             = $value->deny_oplg;
                    $add->deny_palg             = $value->deny_palg;
                    $add->deny_inslg            = $value->deny_inslg;
                    $add->deny_otlg             = $value->deny_otlg;
                    $add->ors                   = $value->ors;
                    $add->va                    = $value->va;
                    $add->audit_results            = $value->audit_results;
                    $add->stm_filename          = $value->stm_filename;
                    $add->save(); 
            } 
        }                
            Finance_stm_lgoexcel::truncate(); 
        
    return redirect()->route('hrims.import_stm.lgo')->with('success',$file_name);
}

//Create lgo_detail
public function lgo_detail(Request $request)
{  
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d', strtotime("last day of this month"));}else{$end_date =$request->end_date;}

    $stm_lgo_list=DB::select('
        SELECT dep,stm_filename,repno,hn,an,pt_name,datetimeadm,datetimedch,adjrw,
        payrate,charge_treatment,compensate_treatment,
        case_iplg,case_oplg,case_palg,case_inslg,case_otlg,case_pp,case_drug
        FROM finance_stm_lgo WHERE DATE(datetimeadm) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND dep = "OP"
        GROUP BY stm_filename,repno,hn,datetimeadm ORDER BY dep DESC,repno');

    $stm_lgo_list_ip=DB::select('
        SELECT dep,stm_filename,repno,hn,an,pt_name,datetimeadm,datetimedch,adjrw,
        payrate,charge_treatment,compensate_treatment,
        case_iplg,case_oplg,case_palg,case_inslg,case_otlg,case_pp,case_drug
        FROM finance_stm_lgo WHERE DATE(datetimedch) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND dep = "IP"
        GROUP BY stm_filename,repno,hn,datetimedch ORDER BY dep DESC,repno');

    return view('hrims.import_stm.lgo_detail',compact('start_date','end_date','stm_lgo_list','stm_lgo_list_ip'));
}

//Create lgo_kidney
public function lgo_kidney(Request $request)
{  
    $stm_lgo_kidney=DB::select('
        SELECT stm_filename,repno,COUNT(repno) AS count_no,	
	    SUM(compensate_kidney) AS compensate_kidney 
		FROM finance_stm_lgo_kidney 
        GROUP BY stm_filename,repno 
        ORDER BY stm_filename,repno');

    return view('hrims.import_stm.lgo_kidney',compact('stm_lgo_kidney'));
}

// lgo_kidney_save
public function lgo_kidney_save(Request $request)
{
    // Set the execution time to 300 seconds (5 minutes)
    set_time_limit(300);

    $this->validate($request, [
        'file' => 'required|file|mimes:xls,xlsx'
    ]);
    $the_file = $request->file('file');
    $file_name = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์

    try{
        $spreadsheet = IOFactory::load($the_file->getRealPath());
        $sheet        = $spreadsheet->setActiveSheetIndex(0); //sheet
        $row_limit    = $sheet->getHighestDataRow();
        // $column_limit = $sheet->getHighestDataColumn();
        $row_range    = range( '11', $row_limit );
        $startcount = '11';
        
        $data = array();
        foreach ($row_range as $row ) {

            $adm = $sheet->getCell( 'G' . $row )->getValue(); 
            $day = substr($adm, 0, 2);
            $mo = substr($adm, 3, 2);
            $year = substr($adm, 6, 4);     
            $admtime = substr($adm, 11, 8);  
            $datetimeadm = $year.'-'.$mo.'-'.$day.' '.$admtime;     

            $data[] = [
                'no'                    =>$sheet->getCell( 'A' . $row )->getValue(),
                'repno'                 =>$sheet->getCell( 'B' . $row )->getValue(),
                'hn'                    =>$sheet->getCell( 'C' . $row )->getValue(),
                'cid'                   =>$sheet->getCell( 'D' . $row )->getValue(),
                'pt_name'               =>$sheet->getCell( 'E' . $row )->getValue(),
                'dep'                   =>$sheet->getCell( 'F' . $row )->getValue(),
                'datetimeadm'           =>$datetimeadm,
                'compensate_kidney'     =>$sheet->getCell( 'H' . $row )->getValue(), 
                'note'                  =>$sheet->getCell( 'I' . $row )->getValue(),                    
                'stm_filename'          =>$file_name,
            ]; 
            $startcount++;            
        }

        $for_insert = array_chunk($data, 1000);
        foreach ($for_insert as $key => $data_) {
            Finance_stm_lgo_kidneyexcel::insert($data_);                 
        }
    }    
    catch (Exception $e) {
        $error_code = $e->errorInfo[1];
        return back()->withErrors('There was a problem uploading the data!');
    }
// ***************************************************************************************************************************** 
        $stm_lgo_kidneyexcel=Finance_stm_lgo_kidneyexcel::whereNotNull('compensate_kidney')->get();
                    
        foreach ($stm_lgo_kidneyexcel as $key => $value) {
            $check = Finance_stm_lgo_kidney::where('repno','=',$value->repno)->where('no','=',$value->no)->count();
            if ($check > 0) {
                Finance_stm_lgo_kidney::where('repno','=',$value->repno)->where('no','=',$value->no)->update([
                        'datetimeadm'               => $value->datetimeadm,
                        'compensate_kidney'         => $value->compensate_kidney,
                        'stm_filename'              => $value->stm_filename
                        ]); 
            } else {
                    $add = new Finance_stm_lgo_kidney();
                    $add->no                    = $value->no;
                    $add->repno                 = $value->repno;
                    $add->hn                    = $value->hn;                
                    $add->cid                   = $value->cid;
                    $add->pt_name               = $value->pt_name;
                    $add->dep                   = $value->dep;
                    $add->datetimeadm           = $value->datetimeadm;                   
                    $add->compensate_kidney     = $value->compensate_kidney;
                    $add->note                  = $value->note;                   
                    $add->stm_filename          = $value->stm_filename;
                    $add->save(); 
            } 
        }                
            Finance_stm_lgo_kidneyexcel::truncate(); 
        
    return redirect()->route('hrims.import_stm.lgo_kidney')->with('success',$file_name);
}

//Create lgo_kidneydetail
public function lgo_kidneydetail(Request $request)
{  
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d', strtotime("last day of this month"));}else{$end_date =$request->end_date;}

    $stm_lgo_kidney_list=DB::select('
        SELECT dep,stm_filename,repno,hn,cid,pt_name,
        datetimeadm,compensate_kidney,note 
        FROM finance_stm_lgo_kidney 
        WHERE DATE(datetimeadm) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY stm_filename,repno,cid,datetimeadm 
        ORDER BY dep DESC,repno');

    return view('hrims.import_stm.lgo_kidneydetail',compact('start_date','end_date','stm_lgo_kidney_list'));
}

//Create sss_kidney XML File
public function sss_kidney(Request $request)
{  
    $stm_sss_kidney=DB::select('
        SELECT stmdoc,station,COUNT(*) AS count_no,	
        SUM(amount) AS amount,SUM(epopay) AS epopay,
        SUM(epoadm) AS epoadm	FROM finance_stm_sss_kidney 
        GROUP BY stmdoc,station ORDER BY station ,stmdoc');    

    return view('hrims.import_stm.sss_kidney',compact('stm_sss_kidney'));
}

//Create sss_kidney XML File
public function sss_kidney_save(Request $request)
{  
       // Set the execution time to 300 seconds (5 minutes)
       set_time_limit(300);

        $tar_file_ = $request->file; 
        $file_ = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์
        $filename = pathinfo($file_, PATHINFO_FILENAME);
        $extension = pathinfo($file_, PATHINFO_EXTENSION);  
        $xmlString = file_get_contents(($tar_file_));
        $xmlObject = simplexml_load_string($xmlString);
        $json = json_encode($xmlObject); 
        $result = json_decode($json, true); 
    
        // dd($result);

        @$hcode     = $result['hcode'];
        @$hname     = $result['hname'];
        @$STMdoc    = $result['STMdoc'];    
        @$HDBills   = $result['HDBills']['HDBill'];    
        $bills_     = @$HDBills;       

            foreach ($bills_ as $value) {  
                $name = $value['name']; 
                $cid = $value['pid'];
                $wkno = $value['wkno'];
                $TBill = $value['TBill']; 

                foreach ($TBill as $row) {   

                    $hreg       = $row['hreg']; 
                    $station    = $row['station'];
                    $invno      = $row['invno'];
                    $hn         = $row['hn']; 
                    $amount     = $row['amount'];
                    $paid       = $row['paid'];
                    $rid        = $row['rid']; 
                    $HDflag     = $row['HDflag']; 
                    $dttran     = $row['dttran'];                     
                    $dttranDate = explode("T",$row['dttran']);
                    $dttdate    = $dttranDate[0];
                    $dtttime    = $dttranDate[1];                 

                        if (isset($row['EPOs']['EPOpay'])) {
                            $epopay   = $row['EPOs']['EPOpay'];
                        } else {
                            $epopay   = '';
                        }

                        if (isset($row['EPOs']['EPOadm'])) {
                            $epoadm   = $row['EPOs']['EPOadm'];
                        } else {
                            $epoadm   = '';
                        }
            
                    $checkc = Finance_stm_sss_kidney::where('cid', $cid)->where('vstdate', $dttdate)->count();
                    if ( $checkc > 0) {
                        Finance_stm_sss_kidney::where('cid', $cid)->where('vstdate', $dttdate) 
                            ->update([   
                                'invno'            => $invno,
                                'dttran'           => $dttran, 
                                'hn'               => $hn, 
                                'cid'              => $cid, 
                                'amount'           => $amount, 
                                'epopay'           => $epopay, 
                                'epoadm'           => $epoadm, 
                                'paid'             => $paid,
                                'rid'              => $rid, 
                                'HDflag'           => $HDflag,
                                'vstdate'          => $dttdate,
                                'vsttime'          => $dtttime                                
                            ]);

                    } else {
                            Finance_stm_sss_kidney::insert([                            
    
                                'hcode'              => @$hcode, 
                                'hname'              => @$hname,
                                'stmdoc'             => @$STMdoc,
                                'station'            => $station, 
                                'hreg'               => $hreg,
                                'hn'                 => $hn,
                                'cid'                => $cid,
                                'invno'              => $invno,
                                'dttran'             => $dttran,
                                'vstdate'            => $dttdate,
                                'vsttime'            => $dtttime,
                                'amount'             => $amount,
                                'epopay'             => $epopay,
                                'epoadm'             => $epoadm,
                                'paid'               => $paid,
                                'rid'                => $rid,
                                'hdflag'             => $HDflag
                            ]);        
                    } 
                }
            }            
        
    return redirect()->route('hrims.import_stm.sss_kidney')->with('success',@$STMdoc);
}

//Create sss_kidneydetail
public function sss_kidneydetail(Request $request)
{  
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d', strtotime("last day of this month"));}else{$end_date =$request->end_date;}

    $stm_sss_kidney_list=DB::select('
        SELECT hcode,hname,stmdoc,station,hreg,hn,cid,
        dttran,paid,rid,amount,epopay,epoadm 
        FROM finance_stm_sss_kidney 
        WHERE DATE(dttran) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        ORDER BY station ,stmdoc');

    return view('hrims.import_stm.sss_kidneydetail',compact('start_date','end_date','stm_sss_kidney_list'));
}

//Create ucs
public function ucs(Request $request)
{  
    $stm_ucs=DB::select('
        SELECT IF(SUBSTRING(stm_filename,11) LIKE "O%","OPD","IPD") AS dep,
        stm_filename,COUNT(DISTINCT repno) AS repno,COUNT(cid) AS count_cid,SUM(charge) AS charge,
        SUM(fund_ip_payrate) AS fund_ip_payrate,SUM(receive_total) AS receive_total FROM finance_stm_ucs 
        GROUP BY stm_filename ORDER BY stm_filename DESC');

    return view('hrims.import_stm.ucs',compact('stm_ucs'));
}

// ucs_save
public function ucs_save(Request $request)
{
    // Set the execution time to 300 seconds (5 minutes)
    set_time_limit(300);

    $this->validate($request, [
        'file' => 'required|file|mimes:xls,xlsx'
    ]);
    $the_file = $request->file('file');
    $file_name = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์

    try{
        $spreadsheet = IOFactory::load($the_file->getRealPath());
        $sheet        = $spreadsheet->setActiveSheetIndex(2); //sheet
        $row_limit    = $sheet->getHighestDataRow();
        // $column_limit = $sheet->getHighestDataColumn();
        $row_range    = range( '15', $row_limit );
        $startcount = '15';
        
        $data = array();
        foreach ($row_range as $row ) {

            $adm = $sheet->getCell( 'H' . $row )->getValue(); 
            $day = substr($adm, 0, 2);
            $mo = substr($adm, 3, 2);
            $year = substr($adm, 6, 4);     
            $admtime = substr($adm, 11, 8);  
            $datetimeadm = $year.'-'.$mo.'-'.$day.' '.$admtime;

            $dch = $sheet->getCell( 'I' . $row )->getValue();
            $dchday = substr($dch, 0, 2);
            $dchmo = substr($dch, 3, 2);
            $dchyear = substr($dch, 6, 4);
            $dchtime = substr($dch, 11, 8);
            $datetimedch = $dchyear.'-'.$dchmo.'-'.$dchday.' '.$dchtime;    
            
            $s = $sheet->getCell( 'S' . $row )->getValue();
            $del_s = str_replace(",","",$s);
            $t = $sheet->getCell( 'T' . $row )->getValue();
            $del_t = str_replace(",","",$t);
            $u = $sheet->getCell( 'U' . $row )->getValue();
            $del_u = str_replace(",","",$u);
            $v= $sheet->getCell( 'V' . $row )->getValue();
            $del_v = str_replace(",","",$v);
            $w = $sheet->getCell( 'W' . $row )->getValue();
            $del_w = str_replace(",","",$w);
            $x = $sheet->getCell( 'X' . $row )->getValue();
            $del_x = str_replace(",","",$x);
            $y = $sheet->getCell( 'Y' . $row )->getValue();
            $del_y = str_replace(",","",$y);
            $z = $sheet->getCell( 'Z' . $row )->getValue();
            $del_z = str_replace(",","",$z);
            $aa = $sheet->getCell( 'AA' . $row )->getValue();
            $del_aa = str_replace(",","",$aa);
            $ab = $sheet->getCell( 'AB' . $row )->getValue();
            $del_ab = str_replace(",","",$ab);
            $ac = $sheet->getCell( 'AC' . $row )->getValue();
            $del_ac = str_replace(",","",$ac);
            $ad = $sheet->getCell( 'AD' . $row )->getValue();
            $del_ad = str_replace(",","",$ad);
            $ae = $sheet->getCell( 'AE' . $row )->getValue();
            $del_ae = str_replace(",","",$ae);
            $af = $sheet->getCell( 'AF' . $row )->getValue();
            $del_af = str_replace(",","",$af);
            $ag = $sheet->getCell( 'AG' . $row )->getValue();
            $del_ag = str_replace(",","",$ag);
            $ah = $sheet->getCell( 'AH' . $row )->getValue();
            $del_ah = str_replace(",","",$ah);
            $ai = $sheet->getCell( 'AI' . $row )->getValue();
            $del_ai = str_replace(",","",$ai);
            $aj = $sheet->getCell( 'AJ' . $row )->getValue();
            $del_aj = str_replace(",","",$aj);
            $ak = $sheet->getCell( 'AK' . $row )->getValue();
            $del_ak = str_replace(",","",$ak);
            $al = $sheet->getCell( 'AL' . $row )->getValue();
            $del_al = str_replace(",","",$al);

                $data[] = [
                    'repno'                         =>$sheet->getCell( 'A' . $row )->getValue(),
                    'no'                            =>$sheet->getCell( 'B' . $row )->getValue(),
                    'tran_id'                       =>$sheet->getCell( 'C' . $row )->getValue(),
                    'hn'                            =>$sheet->getCell( 'D' . $row )->getValue(),
                    'an'                            =>$sheet->getCell( 'E' . $row )->getValue(),
                    'cid'                           =>$sheet->getCell( 'F' . $row )->getValue(),
                    'pt_name'                       =>$sheet->getCell( 'G' . $row )->getValue(),                    
                    'datetimeadm'                   =>$datetimeadm,
                    'datetimedch'                   =>$datetimedch,
                    'maininscl'                     =>$sheet->getCell( 'J' . $row )->getValue(),
                    'projcode'                      =>$sheet->getCell( 'K' . $row )->getValue(),
                    'charge'                        =>$sheet->getCell( 'L' . $row )->getValue(),
                    'fund_ip_act'                   =>$sheet->getCell( 'M' . $row )->getValue(),
                    'fund_ip_adjrw'                 =>$sheet->getCell( 'N' . $row )->getValue(),
                    'fund_ip_ps'                    =>$sheet->getCell( 'O' . $row )->getValue(),
                    'fund_ip_ps2'                   =>$sheet->getCell( 'P' . $row )->getValue(),
                    'fund_ip_ccuf'                  =>$sheet->getCell( 'Q' . $row )->getValue(),
                    'fund_ip_adjrw2'                =>$sheet->getCell( 'R' . $row )->getValue(),
                    'fund_ip_payrate'               =>$del_s,
                    'fund_ip_salary'                =>$del_t,
                    'fund_compensate_salary'        =>$del_u,
                    'receive_op'                    =>$del_v,
                    'receive_ip_compensate_cal'     =>$del_w,
                    'receive_ip_compensate_pay'     =>$del_x,
                    'receive_hc_hc'                 =>$del_y,
                    'receive_hc_drug'               =>$del_z,
                    'receive_ae_ae'                 =>$del_aa,
                    'receive_ae_drug'               =>$del_ab,
                    'receive_inst'                  =>$del_ac,
                    'receive_dmis_compensate_cal'   =>$del_ad,
                    'receive_dmis_compensate_pay'   =>$del_ae,
                    'receive_dmis_drug'             =>$del_af,
                    'receive_palliative'            =>$del_ag,
                    'receive_dmishd'                =>$del_ah,
                    'receive_pp'                    =>$del_ai,
                    'receive_fs'                    =>$del_aj,
                    'receive_opbkk'                 =>$del_ak,
                    'receive_total'                 =>$del_al, 
                    'va'                            =>$sheet->getCell( 'AM' . $row )->getValue(), 
                    'covid'                         =>$sheet->getCell( 'AN' . $row )->getValue(), 
                    'resources'                     =>$sheet->getCell( 'AO' . $row )->getValue(), 
                    'stm_filename'                  =>$file_name,
                ]; 
            $startcount++;            
        }

        $for_insert = array_chunk($data, 1000);
        foreach ($for_insert as $key => $data_) {
            Finance_stm_ucsexcel::insert($data_);                 
        }
    }    
    catch (Exception $e) {
        $error_code = $e->errorInfo[1];
        return back()->withErrors('There was a problem uploading the data!');
    }
// ***************************************************************************************************************************** 
        $stm_ucsexcel=Finance_stm_ucsexcel::whereNotNull('charge')->get();
            
        foreach ($stm_ucsexcel as $key => $value) {
            $check = Finance_stm_ucs::where('repno','=',$value->repno)->where('no','=',$value->no)->count();
            if ($check > 0) {
                Finance_stm_ucs::where('repno','=',$value->repno)->where('no','=',$value->no)->update([
                        'datetimeadm'                   => $value->datetimeadm,
                        'datetimedch'                   => $value->datetimedch,
                        'charge'                        => $value->charge,
                        'receive_op'                    => $value->receive_op,
                        'receive_ip_compensate_pay'     => $value->receive_ip_compensate_pay,
                        'receive_hc_hc'                 => $value->receive_hc_hc,
                        'receive_hc_drug'               => $value->receive_hc_drug,
                        'receive_ae_ae'                 => $value->receive_ae_ae,
                        'receive_ae_drug'               => $value->receive_ae_drug,
                        'receive_inst'                  => $value->receive_inst,
                        'receive_dmis_compensate_pay'   => $value->receive_dmis_compensate_pay,
                        'receive_dmis_drug'             => $value->receive_dmis_drug,
                        'receive_palliative'            => $value->receive_palliative,
                        'receive_pp'                    => $value->receive_pp,
                        'receive_fs'                    => $value->receive_fs,                     
                        'receive_total'                 => $value->receive_total,
                        'stm_filename'                  => $value->stm_filename
                        ]); 
            } else {
                    $add = new Finance_stm_ucs();
                    $add->repno                         = $value->repno;
                    $add->no                            = $value->no;
                    $add->tran_id                       = $value->tran_id;
                    $add->hn                            = $value->hn;
                    $add->an                            = $value->an;
                    $add->cid                           = $value->cid;
                    $add->pt_name                       = $value->pt_name;                   
                    $add->datetimeadm                   = $value->datetimeadm;
                    $add->datetimedch                   = $value->datetimedch;
                    $add->maininscl                     = $value->maininscl;
                    $add->projcode                      = $value->projcode;
                    $add->charge                        = $value->charge;
                    $add->fund_ip_act                   = $value->fund_ip_act;
                    $add->fund_ip_adjrw                 = $value->fund_ip_adjrw;
                    $add->fund_ip_ps                    = $value->fund_ip_ps;
                    $add->fund_ip_ps2                   = $value->fund_ip_ps2;
                    $add->fund_ip_ccuf                  = $value->fund_ip_ccuf;
                    $add->fund_ip_adjrw2                = $value->fund_ip_adjrw2;
                    $add->fund_ip_payrate               = $value->fund_ip_payrate;
                    $add->fund_ip_salary                = $value->fund_ip_salary;
                    $add->fund_compensate_salary        = $value->fund_compensate_salary;
                    $add->receive_op                    = $value->receive_op;
                    $add->receive_ip_compensate_cal     = $value->receive_ip_compensate_cal;
                    $add->receive_ip_compensate_pay     = $value->receive_ip_compensate_pay;
                    $add->receive_hc_hc                 = $value->receive_hc_hc;
                    $add->receive_hc_drug               = $value->receive_hc_drug;
                    $add->receive_ae_ae                 = $value->receive_ae_ae;
                    $add->receive_ae_drug               = $value->receive_ae_drug;
                    $add->receive_inst                  = $value->receive_inst;
                    $add->receive_dmis_compensate_cal   = $value->receive_dmis_compensate_cal;
                    $add->receive_dmis_compensate_pay   = $value->receive_dmis_compensate_pay;
                    $add->receive_dmis_drug             = $value->receive_dmis_drug;
                    $add->receive_palliative            = $value->receive_palliative;
                    $add->receive_dmishd                = $value->receive_dmishd;
                    $add->receive_pp                    = $value->receive_pp;
                    $add->receive_fs                    = $value->receive_fs;
                    $add->receive_opbkk                 = $value->receive_opbkk;
                    $add->receive_total                 = $value->receive_total;
                    $add->va                            = $value->va;
                    $add->covid                         = $value->covid;
                    $add->resources                     = $value->resources;
                    $add->stm_filename                  = $value->stm_filename;
                    $add->save(); 
            } 
        }                
            Finance_stm_ucsexcel::truncate(); 
        
    return redirect()->route('hrims.import_stm.ucs')->with('success',$file_name);
}

//Create ucs_detail
public function ucs_detail(Request $request)
{  
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d', strtotime("last day of this month"));}else{$end_date =$request->end_date;}

    $stm_ucs_list=DB::select('
        SELECT IF(SUBSTRING(stm_filename,11) LIKE "O%","OPD","IPD") AS dep,
        stm_filename,repno,hn,an,pt_name,datetimeadm,datetimedch,projcode,fund_ip_adjrw,
        charge,receive_op,receive_ip_compensate_pay,fund_ip_payrate,receive_total,
        receive_hc_hc,receive_hc_drug,receive_ae_ae,receive_ae_drug,receive_inst,
        receive_palliative,receive_pp,receive_fs
        FROM finance_stm_ucs 
		WHERE DATE(datetimeadm) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND SUBSTRING(stm_filename,11) LIKE "O%"
        GROUP BY stm_filename,repno,hn,datetimeadm 
        ORDER BY dep DESC,repno');

    $stm_ucs_list_ip=DB::select('
        SELECT IF(SUBSTRING(stm_filename,11) LIKE "O%","OPD","IPD") AS dep,
        stm_filename,repno,hn,an,pt_name,datetimeadm,datetimedch,projcode,fund_ip_adjrw,
        charge,receive_op,receive_ip_compensate_pay,fund_ip_payrate,receive_total,
        receive_hc_hc,receive_hc_drug,receive_ae_ae,receive_ae_drug,receive_inst,
        receive_palliative,receive_pp,receive_fs
        FROM finance_stm_ucs 
		WHERE DATE(datetimedch) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND SUBSTRING(stm_filename,11) LIKE "I%"
        GROUP BY stm_filename,repno,hn,datetimedch 
        ORDER BY dep DESC,repno');

    return view('hrims.import_stm.ucs_detail',compact('start_date','end_date','stm_ucs_list','stm_ucs_list_ip'));
}

//Create ucs_kidney
public function ucs_kidney(Request $request)
{  
    $stm_ucs_kidney=DB::select('
        SELECT stm_filename,repno,COUNT(cid) AS count_cid,	
        SUM(charge_total) AS charge_total,SUM(receive_total) AS receive_total
        FROM finance_stm_ucs_kidney 
        GROUP BY stm_filename ORDER BY stm_filename');   

    return view('hrims.import_stm.ucs_kidney',compact('stm_ucs_kidney'));
}

// ucs_kidney_save
public function ucs_kidney_save(Request $request)
{
    // Set the execution time to 300 seconds (5 minutes)
    set_time_limit(300);

    $this->validate($request, [
        'file' => 'required|file|mimes:xls,xlsx'
    ]);
    $the_file = $request->file('file');
    $file_name = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์

    try{
        $spreadsheet = IOFactory::load($the_file->getRealPath());
        $sheet        = $spreadsheet->setActiveSheetIndex(0); //sheet
        $row_limit    = $sheet->getHighestDataRow();
        // $column_limit = $sheet->getHighestDataColumn();
        $row_range    = range( '11', $row_limit );
        $startcount = '11';
        
        $data = array();
        foreach ($row_range as $row ) {

            $adm = $sheet->getCell( 'K' . $row )->getValue(); 
            $day = substr($adm, 0, 2);
            $mo = substr($adm, 3, 2);
            $year = substr($adm, 6, 4);     
            $admtime = substr($adm, 11, 8);  
            $datetimeadm = $year.'-'.$mo.'-'.$day.' '.$admtime;     

            $data[] = [
                'no'                    =>$sheet->getCell( 'A' . $row )->getValue(),
                'repno'                 =>$sheet->getCell( 'C' . $row )->getValue(),
                'hn'                    =>$sheet->getCell( 'E' . $row )->getValue(),
                'an'                    =>$sheet->getCell( 'F' . $row )->getValue(),
                'cid'                   =>$sheet->getCell( 'G' . $row )->getValue(),
                'pt_name'               =>$sheet->getCell( 'H' . $row )->getValue(),
                'datetimeadm'           =>$datetimeadm,
                'hd_type'               =>$sheet->getCell( 'L' . $row )->getValue(), 
                'charge_total'          =>$sheet->getCell( 'M' . $row )->getValue(), 
                'receive_total'         =>$sheet->getCell( 'N' . $row )->getValue(),                 
                'note'                  =>$sheet->getCell( 'P' . $row )->getValue(),                    
                'stm_filename'          =>$file_name,
            ]; 
            $startcount++;            
        }

        $for_insert = array_chunk($data, 1000);
        foreach ($for_insert as $key => $data_) {
            Finance_stm_ucs_kidneyexcel::insert($data_);                 
        }
    }    
    catch (Exception $e) {
        $error_code = $e->errorInfo[1];
        return back()->withErrors('There was a problem uploading the data!');
    }
// ***************************************************************************************************************************** 
        $stm_ucs_kidneyexcel=Finance_stm_ucs_kidneyexcel::whereNotNull('charge_total')->get();
                    
        foreach ($stm_ucs_kidneyexcel as $key => $value) {
            $check = Finance_stm_ucs_kidney::where('repno','=',$value->repno)->where('no','=',$value->no)->count();
            if ($check > 0) {
                Finance_stm_ucs_kidney::where('repno','=',$value->repno)->where('no','=',$value->no)->update([
                        'datetimeadm'        => $value->datetimeadm
                        ]); 
            } else {
                    $add = new Finance_stm_ucs_kidney();
                    $add->no                    = $value->no;
                    $add->repno                 = $value->repno;
                    $add->hn                    = $value->hn;   
                    $add->an                    = $value->an;                
                    $add->cid                   = $value->cid;
                    $add->pt_name               = $value->pt_name;                    
                    $add->datetimeadm           = $value->datetimeadm;                   
                    $add->hd_type               = $value->hd_type;
                    $add->charge_total          = $value->charge_total;
                    $add->receive_total         = $value->receive_total;
                    $add->note                  = $value->note;                   
                    $add->stm_filename          = $value->stm_filename;
                    $add->save(); 
            } 
        }                
            Finance_stm_ucs_kidneyexcel::truncate(); 
        
    return redirect()->route('hrims.import_stm.ucs_kidney')->with('success',$file_name);
}

//Create ucs_kidneydetail
public function ucs_kidneydetail(Request $request)
{  
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d', strtotime("last day of this month"));}else{$end_date =$request->end_date;}

    $stm_ucs_kidney_list=DB::select('
        SELECT stm_filename,repno,hn,an,cid,pt_name,datetimeadm,SUM(charge_total) AS charge_total,
        SUM(receive_total) AS receive_total,note 
        FROM finance_stm_ucs_kidney WHERE DATE(datetimeadm) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY repno,cid,datetimeadm ORDER BY cid,datetimeadm');

    return view('hrims.import_stm.ucs_kidneydetail',compact('start_date','end_date','stm_ucs_kidney_list'));
}
}
