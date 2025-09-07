<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use App\Models\Debtor_1102050101_103;
use App\Models\Debtor_1102050101_109;
use App\Models\Debtor_1102050101_201;
use App\Models\Debtor_1102050101_202;
use App\Models\Debtor_1102050101_203;
use App\Models\Debtor_1102050101_209;
use App\Models\Debtor_1102050101_216;
use App\Models\Debtor_1102050101_217;
use App\Models\Debtor_1102050101_301;
use App\Models\Debtor_1102050101_302;
use App\Models\Debtor_1102050101_303;
use App\Models\Debtor_1102050101_304;
use App\Models\Debtor_1102050101_307;
use App\Models\Debtor_1102050101_308;
use App\Models\Debtor_1102050101_309;
use App\Models\Debtor_1102050101_310;
use App\Models\Debtor_1102050101_401;
use App\Models\Debtor_1102050101_402;
use App\Models\Debtor_1102050101_501;
use App\Models\Debtor_1102050101_502;
use App\Models\Debtor_1102050101_703;
use App\Models\Debtor_1102050101_704;
use App\Models\Debtor_1102050102_106;
use App\Models\Debtor_1102050102_106_tracking;
use App\Models\Debtor_1102050102_107;
use App\Models\Debtor_1102050102_107_tracking;
use App\Models\Debtor_1102050102_108;
use App\Models\Debtor_1102050102_109;
use App\Models\Debtor_1102050102_602;
use App\Models\Debtor_1102050102_603;
use App\Models\Debtor_1102050102_801;
use App\Models\Debtor_1102050102_802;
use App\Models\Debtor_1102050102_803;
use App\Models\Debtor_1102050102_804;
use PDF;
use Session;

class Finance_DebtorController extends Controller
{
    //Check Login
public function __construct()
{
    $this->middleware('auth');
}

//Create index
public function index()
{
  
  return view('finance_debtor.index');
}

#################### ลูกหนี้ค่ารักษาพยาบาล HTP-Report ##################################################
public function _check_income(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d', strtotime("last day of this month"));}else{$end_date =$request->end_date;}

  $check_income = DB::connection('hosxp')->select('
    SELECT (SELECT SUM(income) FROM vn_stat WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'") AS vn_stat,
    (SELECT SUM(paid_money) FROM vn_stat WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'") AS vn_stat_paid,
    (SELECT SUM(sum_price) FROM opitemrece WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND (an IS NULL OR an ="")) AS opitemrece,
    (SELECT SUM(sum_price) FROM opitemrece WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND (an IS NULL OR an ="") AND paidst IN ("01","03")) AS opitemrece_paid,
    IF((SELECT SUM(income) FROM vn_stat WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'")<>(SELECT SUM(sum_price) FROM opitemrece WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND (an IS NULL OR an =""))
    ,"Resync VN","Success") AS status_check');

  $check_income_ipd = DB::connection('hosxp')->select('
    SELECT (SELECT SUM(income) FROM an_stat WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'") AS an_stat,
    (SELECT SUM(paid_money) FROM an_stat WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'") AS an_stat_paid,
    (SELECT SUM(sum_price) FROM opitemrece o ,ipt i WHERE o.an = i.an AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'") AS opitemrece,
    (SELECT SUM(sum_price) FROM opitemrece o ,ipt i WHERE o.an = i.an AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND paidst IN ("01","03")) AS opitemrece_paid,
    IF((SELECT SUM(income) FROM an_stat WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'")<>(SELECT  SUM(sum_price) FROM opitemrece o ,ipt i WHERE o.an = i.an AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'")
    ,"Resync AN","Success") AS status_check');  

  return view('finance_debtor._check_income',compact('start_date','end_date','check_income','check_income_ipd'));
}

public function _summary(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $_1102050101_103 = DB::select('
    SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
    FROM debtor_1102050101_103 
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_109 = DB::select('
    SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
    FROM debtor_1102050101_109 
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_201 = DB::select('
    SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
    FROM debtor_1102050101_201 
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_203 = DB::select('
    SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
    FROM debtor_1102050101_203
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_209 = DB::select('
    SELECT COUNT(DISTINCT d.vn) AS anvn,SUM(d.debtor) AS debtor,IFNULL(SUM(s.receive_pp),0) AS receive
    FROM debtor_1102050101_209 d 
    LEFT JOIN stm_ucs s ON s.cid=d.cid AND DATE(s.datetimeadm) = d.vstdate 
      AND LEFT(TIME(s.datetimeadm),5) =LEFT(d.vsttime,5) 
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_216 = DB::select('
    SELECT COUNT(DISTINCT d.vn) AS anvn,SUM(d.debtor) AS debtor,SUM(IFNULL(s.receive_total,sk.receive_total)) AS receive
    FROM debtor_1102050101_216 d 
    LEFT JOIN stm_ucs s ON s.cid=d.cid AND DATE(s.datetimeadm) = d.vstdate 
      AND LEFT(TIME(s.datetimeadm),5) =LEFT(d.vsttime,5) 
    LEFT JOIN (SELECT cid,datetimeadm AS vstdate,sum(receive_total) AS receive_total FROM stm_ucs_kidney GROUP BY cid,datetimeadm) sk ON sk.cid=d.cid AND sk.vstdate = d.vstdate 
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_301 = DB::select('
    SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
    FROM debtor_1102050101_301 
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_303 = DB::select('
    SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
    FROM debtor_1102050101_303 
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_307 = DB::select('
    SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
    FROM debtor_1102050101_307 
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_309 = DB::select('
    SELECT COUNT(DISTINCT vn) AS anvn, SUM(debtor) AS debtor,IFNULL(SUM(s.amount+s.epopay+s.epoadm),0) AS receive
    FROM debtor_1102050101_309 d 
    LEFT JOIN stm_sss_kidney s ON s.cid=d.cid AND s.vstdate = d.vstdate 
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_401 = DB::select('
    SELECT COUNT(DISTINCT d.vn) AS anvn,SUM(d.debtor) AS debtor,SUM(IFNULL(s.receive_total,0)+IFNULL(s1.amount,0)) AS receive
    FROM debtor_1102050101_401 d 
    LEFT JOIN stm_ofc s ON s.hn=d.hn AND DATE(s.datetimeadm) = d.vstdate	AND LEFT(TIME(s.datetimeadm),5) =LEFT(d.vsttime,5)
    LEFT JOIN stm_ofc_kidney s1 ON s1.hn=d.hn AND DATE(s1.vstdate) = d.vstdate AND d.kidney IS NOT NULL 
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_501 = DB::select('
    SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
    FROM debtor_1102050101_501 
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_703 = DB::select('
    SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
    FROM debtor_1102050101_703 
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050102_106 = DB::select('
    SELECT COUNT(DISTINCT d.vn) AS anvn,SUM(debtor) AS debtor,SUM(IFNULL(d.receive,r.bill_amount)) AS receive
    FROM debtor_1102050102_106 d 
    LEFT JOIN hosxe.rcpt_print r ON r.vn = d.vn AND r.`status` ="OK" AND r.department="OPD" 
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050102_108 = DB::select('
    SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
    FROM debtor_1102050102_108 
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050102_602 = DB::select('
    SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
    FROM debtor_1102050102_602 
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050102_801 = DB::select('
    SELECT COUNT(DISTINCT d.vn) AS anvn,SUM(d.debtor) AS debtor,SUM(IFNULL(s.compensate_treatment,0)+IFNULL(s1.compensate_kidney,0)) AS receive
    FROM debtor_1102050102_801 d   
		LEFT JOIN stm_lgo s ON s.hn=d.hn AND DATE(s.datetimeadm) = d.vstdate AND LEFT(TIME(s.datetimeadm),5) =LEFT(d.vsttime,5)
    LEFT JOIN stm_lgo_kidney s1 ON s1.hn=d.hn AND DATE(s1.datetimeadm) = d.vstdate AND d.kidney IS NOT NULL
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050102_803 = DB::select('
    SELECT COUNT(DISTINCT d.vn) AS anvn,SUM(d.debtor) AS debtor,SUM(IFNULL(s.receive_total,0)+IFNULL(s1.amount,0)) AS receive
    FROM debtor_1102050102_803 d   
		LEFT JOIN stm_ofc s ON s.hn=d.hn AND DATE(s.datetimeadm) = d.vstdate AND LEFT(TIME(s.datetimeadm),5) =LEFT(d.vsttime,5)
    LEFT JOIN stm_ofc_kidney s1 ON s1.hn=d.hn AND DATE(s1.vstdate) = d.vstdate AND d.kidney IS NOT NULL
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_202 = DB::select('
    SELECT COUNT(DISTINCT an) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive_ip_compensate_pay),0) AS receive
    FROM (SELECT d.an,d.debtor,stm.receive_ip_compensate_pay FROM debtor_1102050101_202 d
    LEFT JOIN stm_ucs stm ON stm.an=d.an    
    WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"	GROUP BY d.an) AS a');
  $_1102050101_217 = DB::select('
    SELECT COUNT(DISTINCT an) AS anvn,SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM (SELECT d.dchdate,d.an,d.debtor,(stm.receive_total-stm.receive_ip_compensate_pay)+IFNULL(SUM(stm1.receive_total),0) AS receive
    FROM debtor_1102050101_217 d
    LEFT JOIN stm_ucs stm ON stm.an=d.an
		LEFT JOIN stm_ucs_kidney stm1 ON stm1.cid=d.cid AND stm1.datetimeadm BETWEEN d.regdate AND d.dchdate
		WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY d.an) AS a ');
  $_1102050101_302 = DB::select('
    SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050101_302    
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_304 = DB::select('
    SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050101_304    
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_308 = DB::select('
    SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050101_308    
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_310 = DB::select('
    SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050101_310    
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_402 = DB::select('
    SELECT COUNT(DISTINCT an) AS anvn,SUM(debtor) AS debtor,SUM(receive_total) AS receive
		FROM (SELECT d.*,s.receive_total+IFNULL(SUM(s1.amount),0) AS receive_total 
    FROM debtor_1102050101_402 d    
    LEFT JOIN htp_report.stm_ofc s ON s.an=d.an
    LEFT JOIN htp_report.stm_ofc_kidney s1 ON s1.hn=d.hn AND s1.vstdate BETWEEN d.regdate AND d.dchdate
    WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY d.an) AS a ');
  $_1102050101_502 = DB::select('
    SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050101_502    
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050101_704 = DB::select('
    SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050101_704    
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050102_107 = DB::select('
    SELECT COUNT(DISTINCT d.vn) AS anvn,SUM(debtor) AS debtor,SUM(IFNULL(d.receive,r.bill_amount)) AS receive
    FROM debtor_1102050102_107 d
    LEFT JOIN hosxe.rcpt_print r ON r.vn = d.vn AND r.`status` ="OK" AND r.department="OPD"
    WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050102_109 = DB::select('
    SELECT COUNT(DISTINCT an) AS anvn,SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050102_109   
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050102_603 = DB::select('
    SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050102_603  
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
  $_1102050102_802 = DB::select('
    SELECT COUNT(DISTINCT an) AS anvn,SUM(debtor) AS debtor,SUM(receive_total) AS receive
		FROM (SELECT d.*,s.compensate_treatment+IFNULL(SUM(s1.compensate_kidney),0) AS receive_total 
    FROM debtor_1102050102_802 d    
    LEFT JOIN htp_report.stm_lgo s ON s.an=d.an
    LEFT JOIN htp_report.stm_lgo_kidney s1 ON s1.cid=d.cid AND DATE(s1.datetimeadm)  BETWEEN d.regdate AND d.dchdate
    WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY d.an ) AS a');
  $_1102050102_804 = DB::select('
    SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive_total) AS receive
		FROM (SELECT d.*,s.receive_total
    FROM debtor_1102050102_804 d    
    LEFT JOIN htp_report.stm_ofc s ON s.an=d.an  
    WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY d.an)	AS a ');

  $request->session()->put('start_date',$start_date);
  $request->session()->put('end_date',$end_date);
  $request->session()->put('_1102050101_103',$_1102050101_103);
  $request->session()->put('_1102050101_109',$_1102050101_109);
  $request->session()->put('_1102050101_201',$_1102050101_201);
  $request->session()->put('_1102050101_203',$_1102050101_203);
  $request->session()->put('_1102050101_209',$_1102050101_209);
  $request->session()->put('_1102050101_216',$_1102050101_216);
  $request->session()->put('_1102050101_301',$_1102050101_301);
  $request->session()->put('_1102050101_303',$_1102050101_303);
  $request->session()->put('_1102050101_307',$_1102050101_307);
  $request->session()->put('_1102050101_309',$_1102050101_309);
  $request->session()->put('_1102050101_401',$_1102050101_401);
  $request->session()->put('_1102050101_501',$_1102050101_501);
  $request->session()->put('_1102050101_703',$_1102050101_703);
  $request->session()->put('_1102050102_106',$_1102050102_106);
  $request->session()->put('_1102050102_108',$_1102050102_108);
  $request->session()->put('_1102050102_602',$_1102050102_602);
  $request->session()->put('_1102050102_801',$_1102050102_801);
  $request->session()->put('_1102050102_803',$_1102050102_803);
  $request->session()->put('_1102050101_202',$_1102050101_202);
  $request->session()->put('_1102050101_217',$_1102050101_217);
  $request->session()->put('_1102050101_302',$_1102050101_302);
  $request->session()->put('_1102050101_304',$_1102050101_304);
  $request->session()->put('_1102050101_308',$_1102050101_308);
  $request->session()->put('_1102050101_310',$_1102050101_310);
  $request->session()->put('_1102050101_402',$_1102050101_402);
  $request->session()->put('_1102050101_502',$_1102050101_502);
  $request->session()->put('_1102050101_704',$_1102050101_704);
  $request->session()->put('_1102050102_107',$_1102050102_107);
  $request->session()->put('_1102050102_109',$_1102050102_109);
  $request->session()->put('_1102050102_603',$_1102050102_603);
  $request->session()->put('_1102050102_802',$_1102050102_802);
  $request->session()->put('_1102050102_804',$_1102050102_804);
  $request->session()->save();

  return view('finance_debtor._summary',compact('start_date','end_date','_1102050101_103','_1102050101_109','_1102050101_201','_1102050101_203',
    '_1102050101_209','_1102050101_216','_1102050101_301','_1102050101_303','_1102050101_307','_1102050101_309','_1102050101_401',
    '_1102050101_501','_1102050101_703','_1102050102_106','_1102050102_108','_1102050102_602','_1102050102_801','_1102050102_803',
    '_1102050101_202','_1102050101_217','_1102050101_302','_1102050101_304','_1102050101_308','_1102050101_310','_1102050101_402',
    '_1102050101_502','_1102050101_704','_1102050102_107','_1102050102_109','_1102050102_603','_1102050102_802','_1102050102_804'));
}

public function _summary_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $_1102050101_103 = Session::get('_1102050101_103');
  $_1102050101_109 = Session::get('_1102050101_109');
  $_1102050101_201 = Session::get('_1102050101_201');
  $_1102050101_203 = Session::get('_1102050101_203');
  $_1102050101_209 = Session::get('_1102050101_209');
  $_1102050101_216 = Session::get('_1102050101_216');
  $_1102050101_301 = Session::get('_1102050101_301');
  $_1102050101_303 = Session::get('_1102050101_303');
  $_1102050101_307 = Session::get('_1102050101_307');
  $_1102050101_309 = Session::get('_1102050101_309');
  $_1102050101_401 = Session::get('_1102050101_401');
  $_1102050101_501 = Session::get('_1102050101_501');
  $_1102050101_703 = Session::get('_1102050101_703');
  $_1102050102_106 = Session::get('_1102050102_106');
  $_1102050102_108 = Session::get('_1102050102_108');
  $_1102050102_602 = Session::get('_1102050102_602');
  $_1102050102_801 = Session::get('_1102050102_801');
  $_1102050102_803 = Session::get('_1102050102_803');
  $_1102050101_202 = Session::get('_1102050101_202');
  $_1102050101_217 = Session::get('_1102050101_217');
  $_1102050101_302 = Session::get('_1102050101_302');
  $_1102050101_304 = Session::get('_1102050101_304');
  $_1102050101_308 = Session::get('_1102050101_308');
  $_1102050101_310 = Session::get('_1102050101_310');
  $_1102050101_402 = Session::get('_1102050101_402');
  $_1102050101_502 = Session::get('_1102050101_502');
  $_1102050101_704 = Session::get('_1102050101_704');
  $_1102050102_107 = Session::get('_1102050102_107');
  $_1102050102_109 = Session::get('_1102050102_109');
  $_1102050102_603 = Session::get('_1102050102_603');
  $_1102050102_802 = Session::get('_1102050102_802');
  $_1102050102_804 = Session::get('_1102050102_804');

  $pdf = PDF::loadView('finance_debtor._summary_pdf',compact('start_date','end_date','_1102050101_103','_1102050101_109','_1102050101_201',
  '_1102050101_203','_1102050101_209','_1102050101_216','_1102050101_301','_1102050101_303','_1102050101_307','_1102050101_309',
  '_1102050101_401','_1102050101_501','_1102050101_703','_1102050102_106','_1102050102_108','_1102050102_602','_1102050102_801',
  '_1102050102_803','_1102050101_202','_1102050101_217','_1102050101_302','_1102050101_304','_1102050101_308','_1102050101_310',
  '_1102050101_402','_1102050101_502','_1102050101_704','_1102050102_107','_1102050102_109','_1102050102_603','_1102050102_802','_1102050102_804'))
              ->setPaper('A4', 'landscape');
  return @$pdf->stream();
}

public function _1102050101_103(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');;}else{$end_date =$request->end_date;}

  $search  =  $request->search; 
  if($search =='' || $search == null )
  {$search = Session::get('search');}else{$search =$request->search;}

  $debtor =  Debtor_1102050101_103::whereBetween('vstdate', [$start_date,$end_date])
              ->where(function ($query) use ($search){
                $query->where('ptname','like','%'.$search.'%');
                $query->orwhere('hn','like','%'.$search.'%');
                })
              ->orderBy('vstdate')->get();  
              
  // $debtor = DB::select('
  //   SELECT * FROM debtor_1102050101_103    
  //   WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.`name` AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,"0" AS other 
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype    
    WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND v.income-v.rcpt_money <>"0" AND vp.pttype IN ("14","27")
    AND (o.an IS NULL OR o.an ="") 
    AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_103) 
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue	'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('search',$search);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_103',compact('start_date','end_date','search','debtor','debtor_search'));
}

public function _1102050101_103_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');
 
  return view('finance_debtor.1102050101_103_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_103_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT vstdate,COUNT(DISTINCT vn) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050101_103  
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY vstdate ORDER BY vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_103_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_103_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.`name` AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
    "0" AS other,v.income-v.rcpt_money AS debtor ,"ยืนยันลูกหนี้" AS status 
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype    
    WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND v.income-v.rcpt_money <>"0" AND vp.pttype IN ("14","27")
    AND (o.an IS NULL OR o.an ="") AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue	');     

  foreach ($debtor as $row) {
      Debtor_1102050101_103::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'other'           => $row->other,
        'debtor'          => $row->debtor,  
        'status'          => $row->status,          
    ]);
  }

  return redirect()->route('_1102050101_103');
}

public function _1102050101_103_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_103::whereIn('vn', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_103');
}

public function _1102050101_103_update(Request $request, $vn)
{
  $item = Debtor_1102050101_103::findOrFail($vn);
  $item->update([
    'charge_date' => $request->input('charge_date'),
    'charge_no' => $request->input('charge_no'),
    'charge' => $request->input('charge'),
    'receive_date' => $request->input('receive_date'),
    'receive_no' => $request->input('receive_no'),
    'receive' => $request->input('receive'),
    'repno' => $request->input('repno'),
    'status' => $request->input('status'),
  ]);

  return  redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย'); 
  //return response()->json(['success' => 'User updated successfully']);
}

###############################################################################
public function _1102050101_109(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $search  =  $request->search; 
  if($search =='' || $search == null )
  {$search = Session::get('search');}else{$search =$request->search;}

  $debtor =  Debtor_1102050101_109::whereBetween('vstdate', [$start_date,$end_date])
              ->where(function ($query) use ($search){
                $query->where('ptname','like','%'.$search.'%');
                $query->orwhere('hn','like','%'.$search.'%');
                })
              ->orderBy('vstdate')->get();  

  // $debtor = DB::select('
  //   SELECT * FROM debtor_1102050101_109    
  //   WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,vp.pttype,vp.hospmain,p1.hipdata_code,IF(o2.an IS NOT NULL,a.income,v.income) AS income,
		v.rcpt_money,v.pdx,IFNULL(n_o1.`name`,n_o2.`name`) AS nondrug,IFNULL(o1.sum_price,o2.sum_price) AS debtor   
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
		LEFT JOIN an_stat a ON a.an=o.an
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.109") 
    LEFT JOIN s_drugitems n_o1 ON n_o1.icode=o1.icode 
		LEFT JOIN opitemrece o2 ON o2.an = o.an AND o2.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.109") 
		LEFT JOIN s_drugitems n_o2 ON n_o2.icode=o2.icode 
    WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND (o1.vn IS NOT NULL OR o2.an IS NOT NULL)
    AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_109)
    GROUP BY o.vn,o.an ORDER BY o.vstdate,o.oqueue'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('search',$search);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_109',compact('start_date','end_date','search','debtor','debtor_search'));
}

public function _1102050101_109_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_109_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_109_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT vstdate,COUNT(DISTINCT vn) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050101_109  
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY vstdate ORDER BY vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_109_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_109_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,o.vsttime,
		vp.pttype,vp.hospmain,p1.hipdata_code,IF(o2.an IS NOT NULL,a.income,v.income) AS income,v.rcpt_money,v.pdx,
		IFNULL(n_o1.`name`,n_o2.`name`) AS nondrug,IFNULL(o1.sum_price,o2.sum_price) AS debtor,"ยืนยันลูกหนี้" AS status  
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
		LEFT JOIN an_stat a ON a.an=o.an
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.109") 
    LEFT JOIN s_drugitems n_o1 ON n_o1.icode=o1.icode 
		LEFT JOIN opitemrece o2 ON o2.an = o.an AND o2.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.109") 
		LEFT JOIN s_drugitems n_o2 ON n_o2.icode=o2.icode 
    WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND (o1.vn IS NOT NULL OR o2.an IS NOT NULL)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn,o.an ORDER BY o.vstdate,o.oqueue');     

  foreach ($debtor as $row) {
      Debtor_1102050101_109::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'debtor'          => $row->debtor,   
        'status'          => $row->status,           
    ]);
  }

  return redirect()->route('_1102050101_109');
}

public function _1102050101_109_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_109::whereIn('vn', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_109');
}

public function _1102050101_109_update(Request $request, $vn)
{
  $item = Debtor_1102050101_109::find($vn);
  $item->charge_date = $request->input('charge_date');
  $item->charge_no = $request->input('charge_no');
  $item->charge = $request->input('charge');
  $item->receive_date = $request->input('receive_date');
  $item->receive_no = $request->input('receive_no');
  $item->receive = $request->input('receive');
  $item->repno = $request->input('repno');  
  $item->status = $request->input('status'); 
  $item->save();

  return redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย');  
}

###############################################################################
public function _1102050101_201(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT * FROM debtor_1102050101_201   
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,vp.pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor 
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" 		
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_201)
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" '); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_201',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050101_201_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_201_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_201_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT vstdate,COUNT(DISTINCT vn) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050101_201  
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY vstdate ORDER BY vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_201_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_201_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,vp.pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" 	
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_201)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" ');     

  foreach ($debtor as $row) {
      Debtor_1102050101_201::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'other'           => $row->other,
        'debtor'          => $row->debtor,     
        'status'          => $row->status,          
    ]);
  }

  return redirect()->route('_1102050101_201');
}

public function _1102050101_201_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_201::whereIn('vn', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_201');
}

###############################################################################
public function _1102050101_203(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $search  =  $request->search; 
  if($search =='' || $search == null )
  {$search = Session::get('search');}else{$search =$request->search;}

  $debtor =  Debtor_1102050101_203::whereBetween('vstdate', [$start_date,$end_date])
              ->where(function ($query) use ($search){
                $query->where('ptname','like','%'.$search.'%');
                $query->orwhere('hn','like','%'.$search.'%');
                })
              ->orderBy('vstdate')->get();  

  // $debtor = DB::select('
  //   SELECT * FROM debtor_1102050101_203   
  //   WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,vp.pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,
    GROUP_CONCAT(DISTINCT n_proj.nhso_adp_code) AS project,vp.confirm_and_locked,vp.request_funds,oe.moph_finance_upload_datetime AS fdh	 
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
	  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain IN ("10703","10985","10986","10987","10988","10990") 		
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_203)
    GROUP BY o.vn ORDER BY o1.sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" '); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('search',$search);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_203',compact('start_date','end_date','search','debtor','debtor_search'));
}

public function _1102050101_203_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_203_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_203_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT vstdate,COUNT(DISTINCT vn) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050101_203  
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY vstdate ORDER BY vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_203_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_203_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,vp.pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain IN ("10703","10985","10986","10987","10988","10990")
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_203)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" ');     

  foreach ($debtor as $row) {
      Debtor_1102050101_203::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'other'           => $row->other,
        'debtor'          => $row->debtor,     
        'status'          => $row->status,          
    ]);
  }

  return redirect()->route('_1102050101_203');
}

public function _1102050101_203_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_203::whereIn('vn', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_203');
}

public function _1102050101_203_update(Request $request, $vn)
{
  $item = Debtor_1102050101_203::find($vn);
  $item->charge_date = $request->input('charge_date');
  $item->charge_no = $request->input('charge_no');
  $item->charge = $request->input('charge');
  $item->receive_date = $request->input('receive_date');
  $item->receive_no = $request->input('receive_no');
  $item->receive = $request->input('receive');
  $item->repno = $request->input('repno');  
  $item->status = $request->input('status'); 
  $item->save();

  return redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย');  
}

###############################################################################
public function _1102050101_209(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT d.vn,d.vstdate,d.vsttime,d.hn,d.ptname,d.hipdata_code,d.pttype,d.hospmain,d.pdx,
    d.income,d.rcpt_money,d.ppfs,d.pp,d.debtor,s.receive_pp AS receive,s.repno,d.debtor_lock
    FROM debtor_1102050101_209 d   
    LEFT JOIN stm_ucs s ON s.cid=d.cid AND DATE(s.datetimeadm) = d.vstdate
      AND LEFT(TIME(s.datetimeadm),5) =LEFT(d.vsttime,5)
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.vsttime,o.oqueue,o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
		vp.pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS ppfs,
		v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS pp,v.income-v.rcpt_money AS debtor,
		GROUP_CONCAT(DISTINCT s.`name`) AS ppfs_list,vp.confirm_and_locked,vp.request_funds,oe.moph_finance_upload_datetime AS fdh
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode		
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE v.income-v.rcpt_money <>"0" AND (o.an IS NULL OR o.an ="")  		
		AND p1.pttype NOT LIKE "O%" AND p1.pttype NOT LIKE "L%" 
    AND p1.pttype NOT IN ("14","17")
    AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_209)
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue'); 

  $debtor_search_nonpp = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.vsttime,o.oqueue,o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    vp.pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS ppfs,
    0 AS pp,IFNULL(SUM(o1.sum_price),0) AS debtor,GROUP_CONCAT(DISTINCT s.`name`) AS ppfs_list,vp.confirm_and_locked,
    vp.request_funds,oe.moph_finance_upload_datetime AS fdh
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
    LEFT JOIN s_drugitems s ON s.icode = o1.icode		
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE v.income-v.rcpt_money <>"0" AND (o.an IS NULL OR o.an ="") AND o1.vn IS NOT NULL 		
    AND p1.pttype NOT LIKE "O%" AND p1.pttype NOT LIKE "L%" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
    AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_209)
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_209',compact('start_date','end_date','debtor','debtor_search','debtor_search_nonpp'));
}

public function _1102050101_209_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_209_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_209_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT d.vstdate,COUNT(DISTINCT d.vn) AS anvn,
    SUM(d.debtor) AS debtor,SUM(s.receive_pp) AS receive
    FROM debtor_1102050101_209 d   
    LEFT JOIN stm_ucs s ON s.cid=d.cid AND DATE(s.datetimeadm) = d.vstdate
      AND LEFT(TIME(s.datetimeadm),5) =LEFT(d.vsttime,5)
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		GROUP BY d.vstdate ORDER BY d.vstdate ');

  $pdf = PDF::loadView('finance_debtor.1102050101_209_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_209_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.vsttime,o.oqueue,o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
		vp.pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS ppfs,
		v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS pp,v.income-v.rcpt_money AS debtor,
		GROUP_CONCAT(DISTINCT s.`name`) AS ppfs_list,vp.confirm_and_locked,vp.request_funds,oe.moph_finance_upload_datetime AS fdh
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode		
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE v.income-v.rcpt_money <>"0" AND (o.an IS NULL OR o.an ="")  		
		AND p1.hipdata_code NOT LIKE "O%" AND p1.hipdata_code NOT LIKE "L%" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_209)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue');     

  foreach ($debtor as $row) {
      Debtor_1102050101_209::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'ppfs'            => $row->ppfs,
        'pp'              => $row->pp,
        'debtor'          => $row->debtor,            
    ]);
  }
  return redirect()->route('_1102050101_209');
}

public function _1102050101_209_confirm_nonpp(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes_nonpp' => 'required|array', 
  ]);
  $checkboxes_nonpp = $request->input('checkboxes_nonpp');
  $checkbox = join(",",$checkboxes_nonpp);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.vsttime,o.oqueue,o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    vp.pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS ppfs,
    0 AS pp,IFNULL(SUM(o1.sum_price),0) AS debtor,GROUP_CONCAT(DISTINCT s.`name`) AS ppfs_list,vp.confirm_and_locked,
    vp.request_funds,oe.moph_finance_upload_datetime AS fdh
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
    LEFT JOIN s_drugitems s ON s.icode = o1.icode		
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE v.income-v.rcpt_money <>"0" AND (o.an IS NULL OR o.an ="") AND o1.vn IS NOT NULL 		
    AND p1.hipdata_code NOT LIKE "O%" AND p1.hipdata_code NOT LIKE "L%" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
    AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_209)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue');     

  foreach ($debtor as $row) {
      Debtor_1102050101_209::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'ppfs'            => $row->ppfs,
        'pp'              => $row->pp,
        'debtor'          => $row->debtor,            
    ]);
  }
  return redirect()->route('_1102050101_209');
}

public function _1102050101_209_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_209::whereIn('vn', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_209');
}

###############################################################################
public function _1102050101_216(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT d.vstdate,d.vsttime,d.hn,d.vn,d.cid,d.ptname,d.hipdata_code,d.pttype,
    d.pdx,d.hospmain,d.income,d.rcpt_money,d.kidney,d.cr,d.anywhere,d.debtor,
    IFNULL(s.receive_total,sk.receive_total) AS receive,IFNULL(s.repno,sk.repno) AS repno,d.debtor_lock
    FROM debtor_1102050101_216 d   
    LEFT JOIN stm_ucs s ON s.cid=d.cid AND DATE(s.datetimeadm) = d.vstdate
      AND LEFT(TIME(s.datetimeadm),5) =LEFT(d.vsttime,5)
    LEFT JOIN (SELECT cid,datetimeadm AS vstdate,sum(receive_total) AS receive_total,repno
      FROM stm_ucs_kidney GROUP BY cid,datetimeadm) sk ON sk.cid=d.cid AND sk.vstdate = d.vstdate
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search_kidney = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.vsttime,o.oqueue,o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,vp.pttype,
		vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS other,
		IFNULL(SUM(o1.sum_price),0) AS debtor,GROUP_CONCAT(DISTINCT s.`name`) AS other_list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.216" AND icode_type = "kidney") 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode		
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE p1.hipdata_code = "UCS" AND (o.an IS NULL OR o.an ="") 		
		AND o1.vn IS NOT NULL AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_216)
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue'); 

  $debtor_search_cr = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.vsttime,o.oqueue,o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,vp.pttype,
		vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS other,
		IFNULL(SUM(o1.sum_price),0) AS debtor,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,GROUP_CONCAT(DISTINCT n_proj.nhso_adp_code) AS project,
		vp.confirm_and_locked,vp.request_funds,oe.moph_finance_upload_datetime AS fdh
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
		LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.216" AND icode_type <> "kidney") 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode		
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain IN ("10703","10985","10986","10987","10988","10989","10990")
    AND (o.an IS NULL OR o.an ="") 		
		AND o1.vn IS NOT NULL AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_216)
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue'); 

  $debtor_search_anywhere = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vstdate,o.vsttime,o.oqueue,o.vn,o.hn,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,vp.pttype,
		vp.hospmain,p1.hipdata_code,v.pdx,e1.`name` AS er_emergency_type,GROUP_CONCAT(DISTINCT n_proj.nhso_adp_code) AS project,
		v.income,v.rcpt_money,v.income-v.rcpt_money AS debtor,SUM(DISTINCT refer.sum_price) AS refer,
		vp.confirm_and_locked,vp.request_funds, oe.moph_finance_upload_datetime AS fdh
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
		LEFT JOIN er_regist e ON e.vn=o.vn 
		LEFT JOIN er_emergency_type e1 ON e1.er_emergency_type=e.er_emergency_type
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.216" AND icode_type = "kidney")
		LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
		LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
		LEFT JOIN opitemrece refer ON refer.vn=o.vn AND refer.icode 
	    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("S1801","S1802"))
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain NOT IN ("10703","10985","10986","10987","10988","10989","10990")
    AND (o.an IS NULL OR o.an ="") 		
		AND o1.vn IS NULL AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_216)
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue) AS a WHERE debtor <> "0" '); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_216',compact('start_date','end_date','debtor','debtor_search_kidney','debtor_search_cr','debtor_search_anywhere'));
}

public function _1102050101_216_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_216_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_216_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT d.vstdate,COUNT(DISTINCT d.vn) AS anvn,
    SUM(d.debtor) AS debtor,SUM(IFNULL(s.receive_total,sk.receive_total)) AS receive
    FROM debtor_1102050101_216 d   
    LEFT JOIN stm_ucs s ON s.cid=d.cid AND DATE(s.datetimeadm) = d.vstdate
      AND LEFT(TIME(s.datetimeadm),5) =LEFT(d.vsttime,5)
    LEFT JOIN (SELECT cid,datetimeadm AS vstdate,sum(receive_total) AS receive_total
      FROM stm_ucs_kidney GROUP BY cid,datetimeadm) sk ON sk.cid=d.cid AND sk.vstdate = d.vstdate
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY d.vstdate ORDER BY d.vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_216_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_216_confirm_kidney(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkbox_kidney' => 'required|array', 
  ]);
  $checkbox_kidney = $request->input('checkbox_kidney');
  $checkbox = join(",",$checkbox_kidney);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.vsttime,o.oqueue,o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,vp.pttype,
		vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS other,
		IFNULL(SUM(o1.sum_price),0) AS debtor,GROUP_CONCAT(DISTINCT s.`name`) AS other_list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.216" AND icode_type = "kidney") 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode		
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE p1.hipdata_code = "UCS" AND (o.an IS NULL OR o.an ="") 		
		AND o1.vn IS NOT NULL AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_216)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue');     

  foreach ($debtor as $row) {    
      Debtor_1102050101_216::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'kidney'          => $row->debtor, 
        'debtor'          => $row->debtor,                 
      ]);  
  }

  return redirect()->route('_1102050101_216');
}

public function _1102050101_216_confirm_cr(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkbox_cr' => 'required|array', 
  ]);
  $checkbox_cr = $request->input('checkbox_cr');
  $checkbox = join(",",$checkbox_cr);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.vsttime,o.oqueue,o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,vp.pttype,
		vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS other,
		IFNULL(SUM(o1.sum_price),0) AS debtor,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,vp.confirm_and_locked,vp.request_funds,
    oe.moph_finance_upload_datetime AS fdh
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.216" AND icode_type <> "kidney") 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode		
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain IN ("10703","10985","10986","10987","10988","10989","10990")
    AND (o.an IS NULL OR o.an ="") 		
		AND o1.vn IS NOT NULL AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_216)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue');     

  foreach ($debtor as $row) {    
      Debtor_1102050101_216::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'cr'              => $row->debtor,  
        'debtor'          => $row->debtor,                
      ]); 
  }

  return redirect()->route('_1102050101_216');
}

public function _1102050101_216_confirm_anywhere(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkbox_anywhere' => 'required|array', 
  ]);
  $checkbox_anywhere = $request->input('checkbox_anywhere');
  $checkbox = join(",",$checkbox_anywhere);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vstdate,o.vsttime,o.oqueue,o.vn,o.an,o.hn,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
		vp.pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,v.income-v.rcpt_money AS debtor,
		GROUP_CONCAT(DISTINCT n_proj.nhso_adp_code) AS project,SUM(DISTINCT refer.sum_price) AS refer,vp.confirm_and_locked,vp.request_funds,
    oe.moph_finance_upload_datetime AS fdh
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.216" AND icode_type = "kidney")
		LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
		LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
		LEFT JOIN opitemrece refer ON refer.vn=o.vn AND refer.icode 
	    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("S1801","S1802"))
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain NOT IN ("10703","10985","10986","10987","10988","10989","10990")
    AND (o.an IS NULL OR o.an ="") 		
		AND (o1.vn IS NULL OR o1.vn = "") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_216)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue) AS a WHERE debtor <> "0" ');     

  foreach ($debtor as $row) {    
      Debtor_1102050101_216::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'anywhere'        => $row->debtor,  
        'debtor'          => $row->debtor,                
      ]);
  }

  return redirect()->route('_1102050101_216');
}

public function _1102050101_216_delete(Request $request )
{
  $request->validate([
    'checkbox_d' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox_d');

    if (!empty($checkbox)) {
      Debtor_1102050101_216::whereIn('vn', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_216');
}

###############################################################################
public function _1102050101_301(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT * FROM debtor_1102050101_301   
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,vp.pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (icode_type = "kidney" OR debtor_code = "1102050101.209")) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.pttype IN ("S1","S2","S3","S4","S5","S7") AND vp.hospmain ="10703" 		
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_301)
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" '); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_301',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050101_301_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_301_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_301_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT vstdate,COUNT(DISTINCT vn) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050101_301  
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY vstdate ORDER BY vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_301_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_301_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,vp.pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (icode_type = "kidney" OR debtor_code = "1102050101.209")) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.pttype IN ("S1","S2","S3","S4","S5","S7") AND vp.hospmain ="10703" 	
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_301)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" ');     

  foreach ($debtor as $row) {
      Debtor_1102050101_301::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'other'           => $row->other,
        'debtor'          => $row->debtor,            
    ]);
  }

  return redirect()->route('_1102050101_301');
}

public function _1102050101_301_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_301::whereIn('vn', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_301');
}

###############################################################################
public function _1102050101_303(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $search  =  $request->search; 
  if($search =='' || $search == null )
  {$search = Session::get('search');}else{$search =$request->search;}

  $debtor =  Debtor_1102050101_303::whereBetween('vstdate', [$start_date,$end_date])
              ->where(function ($query) use ($search){
                $query->where('ptname','like','%'.$search.'%');
                $query->orwhere('hn','like','%'.$search.'%');
                })
              ->orderBy('vstdate')->get();  

  // $debtor = DB::select('
  //   SELECT * FROM debtor_1102050101_303   
  //   WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,vp.pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (icode_type = "kidney" OR debtor_code = "1102050101.209")) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.pttype IN ("S1","S2","S3","S4","S5","S7") AND vp.hospmain <>"10703" 		
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_303)
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" '); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('search',$search);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_303',compact('start_date','end_date','search','debtor','debtor_search'));
}

public function _1102050101_303_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_303_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_303_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT vstdate,COUNT(DISTINCT vn) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050101_303  
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY vstdate ORDER BY vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_303_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_303_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,vp.pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (icode_type = "kidney" OR debtor_code = "1102050101.209")) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.pttype IN ("S1","S2","S3","S4","S5","S7") AND vp.hospmain <>"10703" 	
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_303)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" ');     

  foreach ($debtor as $row) {
      Debtor_1102050101_303::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'other'           => $row->other,
        'debtor'          => $row->debtor,  
        'status'          => $row->status,              
    ]);
  }

  return redirect()->route('_1102050101_303');
}

public function _1102050101_303_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_303::whereIn('vn', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_303');
}

public function _1102050101_303_update(Request $request, $vn)
{
  $item = Debtor_1102050101_303::findOrFail($vn);
  $item->update([
    'charge_date' => $request->input('charge_date'),
    'charge_no' => $request->input('charge_no'),
    'charge' => $request->input('charge'),
    'receive_date' => $request->input('receive_date'),
    'receive_no' => $request->input('receive_no'),
    'receive' => $request->input('receive'),
    'repno' => $request->input('repno'),
    'status' => $request->input('status'),
  ]);
  return  redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย'); 
}

###############################################################################
public function _1102050101_307(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $search  =  $request->search; 
  if($search =='' || $search == null )
  {$search = Session::get('search');}else{$search =$request->search;}

  $debtor =  Debtor_1102050101_307::whereBetween('vstdate', [$start_date,$end_date])
              ->where(function ($query) use ($search){
                $query->where('ptname','like','%'.$search.'%');
                $query->orwhere('hn','like','%'.$search.'%');
                $query->orwhere('an','like','%'.$search.'%');
                })
              ->orderBy('vstdate')->get();  

  // $debtor = DB::select('
  //   SELECT * FROM debtor_1102050101_307   
  //   WHERE (vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
  //   OR dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'")');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (icode_type = "kidney" OR debtor_code = "1102050101.209")) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.pttype IN ("25","31","D1","S6")	
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_307)
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" '); 

  $debtor_search_ip = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,o.vstdate,o.vsttime,i.regdate,i.regtime,i.dchdate,i.dchtime,
    i.vn,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ipt i 
    LEFT JOIN ovst o ON o.an=i.an
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216") 		
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("25","31","D1","S6")
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_307) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate) AS a WHERE debtor <> 0 '); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('search',$search);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_307',compact('start_date','end_date','search','debtor','debtor_search','debtor_search_ip'));
}

public function _1102050101_307_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_307_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_307_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT vstdate,COUNT(DISTINCT vn) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050101_307  
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY vstdate ORDER BY vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_307_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_307_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (icode_type = "kidney" OR debtor_code = "1102050101.209")) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.pttype IN ("25","31","D1","S6")
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_307)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" ');     

  foreach ($debtor as $row) {
      Debtor_1102050101_307::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'other'           => $row->other,
        'debtor'          => $row->debtor,  
        'status'          => $row->status,                 
    ]);
  }

  return redirect()->route('_1102050101_307');
}
public function _1102050101_307_confirm_ip(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes_ip' => 'required|array', 
  ]);
  $checkboxes_ip = $request->input('checkboxes_ip');
  $checkbox = join(",",$checkboxes_ip);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,o.vstdate,o.vsttime,i.regdate,i.regtime,i.dchdate,i.dchtime,
    i.vn,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ipt i 
    LEFT JOIN ovst o ON o.an=i.an
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216") 		
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("25","31","D1","S6")
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_307) 
    AND i.vn IN ('.$checkbox.')
    GROUP BY i.an ORDER BY i.ward,i.dchdate) AS a WHERE debtor <> 0 ');     

  foreach ($debtor as $row) {
      Debtor_1102050101_307::insert([
        'an'              => $row->an,
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'regdate'         => $row->regdate,
        'regtime'         => $row->regtime,
        'dchdate'         => $row->dchdate,
        'dchtime'         => $row->dchtime,    
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'adjrw'           => $row->adjrw,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money, 
        'other'           => $row->other,  
        'debtor'          => $row->debtor,    
        'status'          => $row->status,                 
    ]);
  }

  return redirect()->route('_1102050101_307');
}

public function _1102050101_307_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_307::whereIn('vn', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_307');
}

public function _1102050101_307_update(Request $request, $vn)
{
  $item = Debtor_1102050101_307::find($vn);
  $item->charge_date = $request->input('charge_date');
  $item->charge_no = $request->input('charge_no');
  $item->charge = $request->input('charge');
  $item->receive_date = $request->input('receive_date');
  $item->receive_no = $request->input('receive_no');
  $item->receive = $request->input('receive');
  $item->repno = $request->input('repno');  
  $item->status = $request->input('status'); 
  $item->save();

  return redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย');
   
}

###############################################################################
public function _1102050101_309(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT d.vstdate,d.vsttime,d.vn,d.hn,d.cid,d.ptname,d.hipdata_code,d.pttype,
    d.pdx,d.hospmain,d.income,d.rcpt_money,d.kidney,d.debtor,
    s.amount+s.epopay+s.epoadm AS receive,s.rid AS repno,d.debtor_lock
    FROM debtor_1102050101_309 d   
    LEFT JOIN stm_sss_kidney s ON s.cid=d.cid AND s.vstdate = d.vstdate 
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
		o.vstdate,o.vsttime,vp.pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
		IFNULL(SUM(DISTINCT o1.sum_price),0) AS kidney,IFNULL(SUM(DISTINCT o1.sum_price),0) AS debtor,
		GROUP_CONCAT(DISTINCT s.`name`) AS kidney_list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn AND vp.pttype_number = "1"
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE icode_type = "kidney" ) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.hipdata_code IN ("SSS","SSI") AND o1.vn IS NOT NULL	
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_309)
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue '); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_309',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050101_309_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_309_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_309_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT d.vstdate,COUNT(DISTINCT vn) AS anvn,
    SUM(debtor) AS debtor,SUM(s.amount+s.epopay+s.epoadm) AS receive
    FROM debtor_1102050101_309 d   
    LEFT JOIN stm_sss_kidney s ON s.cid=d.cid AND s.vstdate = d.vstdate 
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY d.vstdate ORDER BY d.vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_309_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_309_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
		o.vstdate,o.vsttime,vp.pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
		IFNULL(SUM(DISTINCT o1.sum_price),0) AS kidney,IFNULL(SUM(DISTINCT o1.sum_price),0) AS debtor,
		GROUP_CONCAT(DISTINCT s.`name`) AS kidney_list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn AND vp.pttype_number = "1"
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE icode_type = "kidney" ) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.hipdata_code IN ("SSS","SSI") AND o1.vn IS NOT NULL	
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_309)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue');     

  foreach ($debtor as $row) {
      Debtor_1102050101_309::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'kidney'          => $row->kidney,
        'debtor'          => $row->debtor,            
    ]);
  }

  return redirect()->route('_1102050101_309');
}

public function _1102050101_309_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_309::whereIn('vn', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_309');
}

###############################################################################
public function _1102050101_401(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT d.vstdate,d.vsttime,d.vn,d.hn,d.cid,d.ptname,d.hipdata_code,d.pttype,d.pdx,d.hospmain,d.income,
    d.rcpt_money,d.ofc,d.kidney,d.ppfs,d.other,d.debtor,IF(IFNULL(s1.rid,s.repno) <>"","กระทบยอดแล้ว",d.status) AS status,d.charge_date,
    d.charge_no,d.charge,d.receive_date,d.receive_no,IFNULL(d.receive,0)+IFNULL(s.receive_total,0)+IFNULL(s1.amount,0) AS receive,     
		IFNULL(s2.receive_pp,0) AS receive_pp,IFNULL(s1.rid,s.repno) AS repno,d.repno AS repno_chk,d.debtor_lock
    FROM debtor_1102050101_401 d   
		LEFT JOIN stm_ofc s ON s.hn=d.hn AND DATE(s.datetimeadm) = d.vstdate
			AND LEFT(TIME(s.datetimeadm),5) =LEFT(d.vsttime,5) AND d.kidney=""   
    LEFT JOIN stm_ofc_kidney s1 ON s1.hn=d.hn AND DATE(s1.vstdate) = d.vstdate AND d.kidney<>""
    LEFT JOIN stm_ucs s2 ON s2.cid=d.cid AND DATE(s2.datetimeadm) = d.vstdate
		  AND LEFT(TIME(s2.datetimeadm),5) =LEFT(d.vsttime,5)
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" ');

  $debtor_search_ofc = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.oqueue,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
		v.income-v.rcpt_money-IFNULL(o1.sum_price,0)-IFNULL(SUM(o2.sum_price),0)-IFNULL(SUM(o3.sum_price),0) AS ofc,
		IFNULL(o1.sum_price,0) AS kidney,IFNULL(SUM(o2.sum_price),0) AS ppfs,IFNULL(SUM(o3.sum_price),0) AS other,
		v.income-v.rcpt_money-IFNULL(SUM(o2.sum_price),0)-IFNULL(o3.sum_price,0) AS debtor,oe.upload_datetime AS ecliam,
    GROUP_CONCAT(DISTINCT s.`name`) AS list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode ="3003375"
		LEFT JOIN opitemrece o2 ON o2.vn = o.vn AND o2.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
		LEFT JOIN opitemrece o3 ON o3.vn = o.vn AND o3.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code IN ("1102050101.109"))
		LEFT JOIN s_drugitems s ON s.icode=o3.icode
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE (p1.pttype LIKE "O%" OR p1.pttype ="H1") AND (o.an IS NULL OR o.an ="") 
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_401)
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue) AS a WHERE a.debtor <> 0');  

  $debtor_search_pp = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.oqueue,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
		v.income-v.rcpt_money-IFNULL(o1.sum_price,0)-IFNULL(SUM(o2.sum_price),0)-IFNULL(SUM(o3.sum_price),0) AS ofc,
		IFNULL(o1.sum_price,0) AS kidney,IFNULL(SUM(o2.sum_price),0) AS ppfs,IFNULL(SUM(o3.sum_price),0) AS other,
		v.income-v.rcpt_money-IFNULL(SUM(o2.sum_price),0)-IFNULL(o3.sum_price,0) AS debtor,oe.upload_datetime AS ecliam,GROUP_CONCAT(DISTINCT s.`name`) AS list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode ="3003375"
		LEFT JOIN opitemrece o2 ON o2.vn = o.vn AND o2.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
		LEFT JOIN opitemrece o3 ON o3.vn = o.vn AND o3.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code IN ("1102050101.109"))
		LEFT JOIN s_drugitems s ON s.icode=o3.icode
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE (p1.pttype LIKE "O%" OR p1.pttype ="H1") AND (o.an IS NULL OR o.an ="") 
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND v.pdx IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_401)
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue) AS a WHERE a.debtor <> 0');  

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_401',compact('start_date','end_date','debtor','debtor_search_ofc','debtor_search_pp'));
}

public function _1102050101_401_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_401_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_401_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT d.vstdate,COUNT(DISTINCT d.vn) AS anvn,SUM(d.debtor) AS debtor,
		SUM(IFNULL(s.receive_total,0)+IFNULL(s1.amount,0)) AS receive
    FROM debtor_1102050101_401 d   
		LEFT JOIN stm_ofc s ON s.hn=d.hn AND DATE(s.datetimeadm) = d.vstdate
			AND LEFT(TIME(s.datetimeadm),5) =LEFT(d.vsttime,5) AND d.kidney ="" 
    LEFT JOIN stm_ofc_kidney s1 ON s1.hn=d.hn AND DATE(s1.vstdate) = d.vstdate AND d.kidney <>"" 
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		GROUP BY d.vstdate ORDER BY d.vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_401_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_401_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkbox_ofc' => 'required|array', 
  ]);
  $checkbox_ofc = $request->input('checkbox_ofc');
  $checkbox = join(",",$checkbox_ofc);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.oqueue,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
		v.income-v.rcpt_money-IFNULL(o1.sum_price,0)-IFNULL(SUM(o2.sum_price),0)-IFNULL(SUM(o3.sum_price),0) AS ofc,
		IFNULL(o1.sum_price,0) AS kidney,IFNULL(SUM(o2.sum_price),0) AS ppfs,IFNULL(SUM(o3.sum_price),0) AS other,
		v.income-v.rcpt_money-IFNULL(SUM(o2.sum_price),0)-IFNULL(o3.sum_price,0) AS debtor,oe.upload_datetime AS ecliam,
    GROUP_CONCAT(DISTINCT s.`name`) AS list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode ="3003375"
		LEFT JOIN opitemrece o2 ON o2.vn = o.vn AND o2.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
		LEFT JOIN opitemrece o3 ON o3.vn = o.vn AND o3.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code IN ("1102050101.109"))
		LEFT JOIN s_drugitems s ON s.icode=o3.icode
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE (p1.pttype LIKE "O%" OR p1.pttype ="H1") AND (o.an IS NULL OR o.an ="") 
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_401)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue) AS a WHERE a.debtor <> 0');     

  foreach ($debtor as $row) {
      Debtor_1102050101_401::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'ofc'             => $row->ofc,
        'kidney'          => $row->kidney,
        'ppfs'            => $row->ppfs,
        'other'           => $row->other,
        'debtor'          => $row->debtor,            
    ]);
    if ($row->ppfs != 0) {
      Debtor_1102050101_209::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'ppfs'            => $row->pp,
        'debtor'          => $row->pp,    
      ]);
    }
  }

  return redirect()->route('_1102050101_401');
}

public function _1102050101_401_confirm_pp(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkbox_pp' => 'required|array', 
  ]);
  $checkbox_pp = $request->input('checkbox_pp');
  $checkbox = join(",",$checkbox_pp);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.oqueue,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
		v.income-v.rcpt_money-IFNULL(o1.sum_price,0)-IFNULL(SUM(o2.sum_price),0)-IFNULL(SUM(o3.sum_price),0) AS ofc,
		IFNULL(o1.sum_price,0) AS kidney,IFNULL(SUM(o2.sum_price),0) AS pp,IFNULL(SUM(o3.sum_price),0) AS other,
		v.income-v.rcpt_money-IFNULL(SUM(o2.sum_price),0)-IFNULL(o3.sum_price,0) AS debtor,oe.upload_datetime AS ecliam,
    GROUP_CONCAT(DISTINCT s.`name`) AS list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode ="3003375"
		LEFT JOIN opitemrece o2 ON o2.vn = o.vn AND o2.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
		LEFT JOIN opitemrece o3 ON o3.vn = o.vn AND o3.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code IN ("1102050101.109"))
		LEFT JOIN s_drugitems s ON s.icode=o3.icode
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE (p1.pttype LIKE "O%" OR p1.pttype ="H1") AND (o.an IS NULL OR o.an ="") 
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND v.pdx IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_401)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue) AS a WHERE a.debtor <> 0');     

  foreach ($debtor as $row) {
      Debtor_1102050101_401::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'ofc'             => $row->ofc,
        'kidney'          => $row->kidney,
        'pp'              => $row->pp,
        'other'           => $row->other,
        'debtor'          => $row->debtor,            
    ]);
    if ($row->pp != 0) {
      Debtor_1102050101_209::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'ppfs'            => $row->pp,
        'debtor'          => $row->pp,    
      ]);
    }
  }
  return redirect()->route('_1102050101_401');

}

public function _1102050101_401_delete(Request $request )
{
  $request->validate([
    'checkbox_d' => 'required|array', 
  ]);
  $checkbox_d = $request->input('checkbox_d');

    if (!empty($checkbox_d)) {
      Debtor_1102050101_401::whereIn('vn', $checkbox_d)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_401');
}

public function _1102050101_401_update(Request $request, $vn)
{
  $item = Debtor_1102050101_401::find($vn);
  $item->charge_date = $request->input('charge_date');
  $item->charge_no = $request->input('charge_no');
  $item->charge = $request->input('charge');
  $item->receive_date = $request->input('receive_date');
  $item->receive_no = $request->input('receive_no');
  $item->receive = $request->input('receive');
  $item->repno = $request->input('repno');  
  $item->status = $request->input('status'); 
  $item->save();

  return redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย');  
}

###############################################################################
public function _1102050101_501(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT * FROM debtor_1102050101_501   
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (icode_type = "kidney" OR debtor_code = "1102050101.109")) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.pttype IN ("11","12","N1","N2","N3","N4","N5")	
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_501)
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" '); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_501',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050101_501_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_501_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_501_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT vstdate,COUNT(DISTINCT vn) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050101_501 
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY vstdate ORDER BY vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_501_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_501_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (icode_type = "kidney" OR debtor_code = "1102050101.109")) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE  p1.pttype IN ("11","12","N1","N2","N3","N4","N5")	
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"  
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_501)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" ');     

  foreach ($debtor as $row) {
    Debtor_1102050101_501::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'other'           => $row->other,
        'debtor'          => $row->debtor,  
        'status'          => $row->status,             
    ]);
  }

  return redirect()->route('_1102050101_501');
}

public function _1102050101_501_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_501::whereIn('vn', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_501');
}

public function _1102050101_501_update(Request $request, $vn)
{
  $item = Debtor_1102050101_501::findOrFail($vn);
  $item->update([
    'charge_date' => $request->input('charge_date'),
    'charge_no' => $request->input('charge_no'),
    'charge' => $request->input('charge'),
    'receive_date' => $request->input('receive_date'),
    'receive_no' => $request->input('receive_no'),
    'receive' => $request->input('receive'),
    'repno' => $request->input('repno'),
    'status' => $request->input('status'),
  ]);
  return  redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย'); 
}

###############################################################################
public function _1102050101_703(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT * FROM debtor_1102050101_703   
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (icode_type = "kidney" OR debtor_code = "1102050101.109")) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.pttype IN ("ST")
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_703)
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" '); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_703',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050101_703_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_703_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_703_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT vstdate,COUNT(DISTINCT vn) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050101_703 
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY vstdate ORDER BY vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_703_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_703_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (icode_type = "kidney" OR debtor_code = "1102050101.109")) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE  p1.pttype IN ("ST")	
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050101_703)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" ');     

  foreach ($debtor as $row) {
      Debtor_1102050101_703::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'other'           => $row->other,
        'debtor'          => $row->debtor,            
    ]);
  }

  return redirect()->route('_1102050101_703');
}

public function _1102050101_703_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_703::whereIn('vn', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_703');
}

###############################################################################
public function _1102050102_106(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT d.vstdate,d.vsttime,d.hn,d.vn,d.ptname,d.mobile_phone_number,d.pttype,d.pdx,d.income,d.paid_money,d.rcpt_money,
    d.debtor,d.debtor_lock,IF(r.bill_amount <>"","กระทบยอดแล้ว",d.status) AS status,d.charge_date,d.charge_no,d.charge,
    d.receive_date,d.receive_no,IFNULL(d.receive,r.bill_amount) AS receive,IFNULL(d.repno,r.rcpno) AS repno,r.bill_amount,
    IF(t.visit IS NULL,0,t.visit) AS visit
    FROM debtor_1102050102_106 d
    LEFT JOIN hosxe.rcpt_print r ON r.vn = d.vn AND r.`status` ="OK" AND r.department="OPD" AND r.bill_date <> d.vstdate
    LEFT JOIN (SELECT vn,COUNT(vn) AS visit FROM debtor_1102050102_106_tracking GROUP BY vn) t ON t.vn=d.vn
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.vsttime,o.oqueue,o.vn,o.an,o.hn,v.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    p.mobile_phone_number,p1.`name` AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.paid_money,
    v.rcpt_money,v.paid_money-v.rcpt_money AS debtor,r.rcpno,p2.arrear_date,p2.amount AS arrear_amount,
    r1.bill_amount AS paid_arrear,r1.rcpno AS rcpno_arrear,fd.deposit_amount,fd1.debit_amount
    FROM ovst o LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN patient_arrear p2 ON p2.vn=o.vn
    LEFT JOIN patient_finance_deposit fd ON fd.anvn = o.vn
    LEFT JOIN patient_finance_debit fd1 ON fd1.anvn = o.vn
    LEFT JOIN rcpt_print r ON r.vn = o.vn AND r.`status` ="OK" AND r.department="OPD" AND r.bill_date=o.vstdate
    LEFT JOIN rcpt_print r1 ON r1.vn = p2.vn AND r1.`status` ="OK" AND r1.department="OPD" 
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.paid_money <>"0" AND v.rcpt_money <> v.paid_money 
    AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050102_106)    
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue '); 

  $debtor_search_iclaim = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.oqueue,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,o.vsttime,
		p1.`name` AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.109" ) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND vp.pttype = "26" AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050102_106)
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0"'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050102_106',compact('start_date','end_date','debtor','debtor_search','debtor_search_iclaim'));
}

public function _1102050102_106_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050102_106_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050102_106_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT d.vstdate,COUNT(DISTINCT d.vn) AS anvn,
    SUM(debtor) AS debtor,SUM(IFNULL(d.receive,r.bill_amount)) AS receive
    FROM debtor_1102050102_106 d
    LEFT JOIN hosxe.rcpt_print r ON r.vn = d.vn AND r.`status` ="OK" AND r.department="OPD"
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY vstdate ORDER BY vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050102_106_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050102_106_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.vsttime,o.oqueue,o.vn,o.an,o.hn,v.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    p.mobile_phone_number,p1.`name` AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.paid_money,
    v.rcpt_money,v.paid_money-v.rcpt_money AS debtor,r.rcpno,p2.arrear_date,p2.amount AS arrear_amount,
    r1.bill_amount AS paid_arrear,r1.rcpno AS rcpno_arrear,fd.deposit_amount,fd1.debit_amount,"ยืนยันลูกหนี้" AS status
    FROM ovst o LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN patient_arrear p2 ON p2.vn=o.vn
    LEFT JOIN patient_finance_deposit fd ON fd.anvn = o.vn
    LEFT JOIN patient_finance_debit fd1 ON fd1.anvn = o.vn
    LEFT JOIN rcpt_print r ON r.vn = o.vn AND r.`status` ="OK" AND r.department="OPD" AND r.bill_date=o.vstdate
    LEFT JOIN rcpt_print r1 ON r1.vn = p2.vn AND r1.`status` ="OK" AND r1.department="OPD" 
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.paid_money <>"0" AND v.rcpt_money <> v.paid_money AND o.vn IN ('.$checkbox.')
    AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050102_106)    
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue ');     

  foreach ($debtor as $row) {
      Finance_debtor_1102050102_106::insert([
        'vn'                  => $row->vn,
        'hn'                  => $row->hn,
        'an'                  => $row->an,
        'cid'                 => $row->cid,
        'ptname'              => $row->ptname,
        'mobile_phone_number' => $row->mobile_phone_number,
        'vstdate'             => $row->vstdate,
        'vsttime'             => $row->vsttime,
        'pttype'              => $row->pttype,    
        'hospmain'            => $row->hospmain,    
        'hipdata_code'        => $row->hipdata_code,   
        'pdx'                 => $row->pdx,  
        'income'              => $row->income,  
        'paid_money'          => $row->paid_money,
        'rcpt_money'          => $row->rcpt_money,
        'debtor'              => $row->debtor,      
        'status'              => $row->status,       
    ]);
  }

  return redirect()->route('_1102050102_106');
}

public function _1102050102_106_confirm_iclaim(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkbox_iclaim' => 'required|array', 
  ]);
  $checkbox_iclaim = $request->input('checkbox_iclaim');
  $checkbox = join(",",$checkbox_iclaim);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.`name` AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,GROUP_CONCAT(s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.109" ) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND vp.pttype = "26" AND o.vn NOT IN (SELECT vn FROM htp_report.finance_debtor_1102050102_106)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0"');     

  foreach ($debtor as $row) {
      Debtor_1102050102_106::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'other'           => $row->other,
        'debtor'          => $row->debtor,     
        'status'          => $row->status,      
    ]);
  }

  return redirect()->route('_1102050102_106');
}


public function _1102050102_106_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050102_106::whereIn('vn', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050102_106');
}

public function _1102050102_106_update(Request $request, $vn)
{
  $item = Debtor_1102050102_106::find($vn);
  $item->charge_date = $request->input('charge_date');
  $item->charge_no = $request->input('charge_no');
  $item->charge = $request->input('charge');
  $item->receive_date = $request->input('receive_date');
  $item->receive_no = $request->input('receive_no');
  $item->receive = $request->input('receive');
  $item->repno = $request->input('repno');  
  $item->status = $request->input('status'); 
  $item->save();

  return redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย');  
}

public function _1102050102_106_tracking(Request $request, $vn )
{
  $debtor = DB::select('
    SELECT * FROM debtor_1102050102_106 WHERE vn = "'.$vn.'"');

  $tracking = DB::select('
    SELECT * FROM debtor_1102050102_106_tracking WHERE vn = "'.$vn.'"');

  return view('finance_debtor.1102050102_106_tracking',compact('debtor','tracking'));
}

public function _1102050102_106_tracking_insert(Request $request)
{
  $item = new Debtor_1102050102_106_tracking;
  $item->vn = $request->input('vn');
  $item->tracking_date = $request->input('tracking_date');
  $item->tracking_type = $request->input('tracking_type');
  $item->tracking_no = $request->input('tracking_no');
  $item->tracking_officer = $request->input('tracking_officer');
  $item->tracking_note = $request->input('tracking_note');  
  $item->save();  

  return redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย'); 
}

public function _1102050102_106_tracking_update(Request $request, $tracking_id)
{
  Debtor_1102050102_106_tracking::where('tracking_id', $tracking_id)
      ->update([
      'tracking_date' => $request->input('tracking_date'),
      'tracking_type' => $request->input('tracking_type'),
      'tracking_no' => $request->input('tracking_no'),
      'tracking_officer' => $request->input('tracking_officer'),
      'tracking_note' => $request->input('tracking_note')
      ]);

  return redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย');  
}

############################################################################################################
public function _1102050102_108(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT * FROM debtor_1102050102_108  
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.paid_money,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (icode_type = "kidney" OR debtor_code = "1102050101.109")) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.hipdata_code IN ("A2","BFC","GOF","PVT","WVO") AND v.paid_money = "0"
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050102_108)
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" '); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050102_108',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050102_108_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050102_108_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050102_108_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT vstdate,COUNT(DISTINCT vn) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050102_108
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY vstdate ORDER BY vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050102_108_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050102_108_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.paid_money,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (icode_type = "kidney" OR debtor_code = "1102050101.109")) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.hipdata_code IN ("A2","BFC","GOF","PVT","WVO") AND v.paid_money = "0"
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050102_108)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY sum_price DESC ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" ');     

  foreach ($debtor as $row) {
      Debtor_1102050102_108::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'other'           => $row->other,
        'debtor'          => $row->debtor,   
        'status'          => $row->status,          
    ]);
  }

  return redirect()->route('_1102050102_108');
}

public function _1102050102_108_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050102_108::whereIn('vn', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050102_108');
}

public function _1102050102_108_update(Request $request, $vn)
{
  $item = Debtor_1102050102_108::findOrFail($vn);
  $item->update([
    'charge_date' => $request->input('charge_date'),
    'charge_no' => $request->input('charge_no'),
    'charge' => $request->input('charge'),
    'receive_date' => $request->input('receive_date'),
    'receive_no' => $request->input('receive_no'),
    'receive' => $request->input('receive'),
    'repno' => $request->input('repno'),
    'status' => $request->input('status'),
  ]);
  return  redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย'); 
}

########################################################################################################
public function _1102050102_602(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date; 
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $search  =  $request->search; 
  if($search =='' || $search == null )
  {$search = Session::get('search');}else{$search =$request->search;}

  $debtor =  Debtor_1102050102_602::whereBetween('vstdate', [$start_date,$end_date])
              ->where(function ($query) use ($search){
                $query->where('ptname','like','%'.$search.'%');
                $query->orwhere('hn','like','%'.$search.'%');
                })
              ->orderBy('vstdate')->get();  

  // $debtor = DB::select('
  // SELECT * FROM debtor_1102050102_602  
  // WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.paid_money,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (icode_type = "kidney" OR debtor_code = "1102050101.109")) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.pttype = "29"
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050102_602)
    GROUP BY o.vn ORDER BY o.hn ,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" '); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('search',$search);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050102_602',compact('start_date','end_date','search','debtor','debtor_search'));
}

public function _1102050102_602_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050102_602_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050102_602_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT vstdate,COUNT(DISTINCT vn) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive
    FROM debtor_1102050102_602
    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY vstdate ORDER BY vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050102_602_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050102_602_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,o.vsttime,p1.name AS pttype,
    vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.paid_money,v.rcpt_money,GROUP_CONCAT(DISTINCT s.`name`) AS other_list,
		IFNULL(SUM(o1.sum_price),0) AS other,v.income-v.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status 
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (icode_type = "kidney" OR debtor_code = "1102050101.109")) 
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE p1.pttype ="29"
		AND (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050102_602)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY o.hn,o.vstdate,o.oqueue) AS a WHERE debtor <> "0" ');     

  foreach ($debtor as $row) {
      Debtor_1102050102_602::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'other'           => $row->other,
        'debtor'          => $row->debtor,      
        'status'          => $row->status,         
    ]);
  }

  return redirect()->route('_1102050102_602');
}

public function _1102050102_602_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050102_602::whereIn('vn', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050102_602');
}

public function _1102050102_602_update(Request $request, $vn)
{
  $item = Debtor_1102050102_602::find($vn);
  $item->charge_date = $request->input('charge_date');
  $item->charge_no = $request->input('charge_no');
  $item->charge = $request->input('charge');
  $item->receive_date = $request->input('receive_date');
  $item->receive_no = $request->input('receive_no');
  $item->receive = $request->input('receive');
  $item->repno = $request->input('repno');  
  $item->status = $request->input('status'); 
  $item->save();

  // session()->flash('success','บันทึกข้อมูลเรียบร้อย');
  // return response()->json(['success' => 'บันทึกข้อมูลเรียบร้']);
  return redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย'); 
}

###############################################################################
public function _1102050102_801(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT d.vstdate,d.vsttime,d.vn,d.hn,d.cid,d.ptname,d.hipdata_code,d.pttype,d.pdx,d.hospmain,d.income,
    d.rcpt_money,d.lgo,d.kidney,d.pp,d.other,d.debtor,IFNULL(s.compensate_treatment,0)+IFNULL(s1.compensate_kidney,0) AS receive,   
		IFNULL(s2.receive_pp,0) AS receive_pp,s.repno,s1.repno AS rid,d.debtor_lock
    FROM debtor_1102050102_801 d   
		LEFT JOIN stm_lgo s ON s.hn=d.hn AND DATE(s.datetimeadm) = d.vstdate 
      AND LEFT(TIME(s.datetimeadm),5) =LEFT(d.vsttime,5) 
    LEFT JOIN stm_lgo_kidney s1 ON s1.hn=d.hn AND DATE(s1.datetimeadm) = d.vstdate AND d.kidney <>""
    LEFT JOIN stm_ucs s2 ON s2.cid=d.cid AND DATE(s2.datetimeadm) = d.vstdate 
      AND LEFT(TIME(s2.datetimeadm),5) =LEFT(d.vsttime,5)
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" ');

  $debtor_search_lgo = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.oqueue,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
		v.income-v.rcpt_money-IFNULL(o1.sum_price,0)-IFNULL(SUM(o2.sum_price),0)-IFNULL(SUM(o3.sum_price),0) AS lgo,
		IFNULL(o1.sum_price,0) AS kidney,IFNULL(SUM(o2.sum_price),0) AS pp,IFNULL(SUM(o3.sum_price),0) AS other,
		v.income-v.rcpt_money-IFNULL(SUM(o2.sum_price),0)-IFNULL(o3.sum_price,0) AS debtor,oe.upload_datetime AS ecliam,
    GROUP_CONCAT(DISTINCT s.`name`) AS list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode ="3003375"
		LEFT JOIN opitemrece o2 ON o2.vn = o.vn AND o2.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
		LEFT JOIN opitemrece o3 ON o3.vn = o.vn AND o3.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code IN ("1102050101.109"))
		LEFT JOIN s_drugitems s ON s.icode=o3.icode
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE p1.pttype LIKE "L%" AND (o.an IS NULL OR o.an ="") 
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050102_801)
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue) AS a WHERE a.debtor <> 0');  

  $debtor_search_pp = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.oqueue,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
		v.income-v.rcpt_money-IFNULL(o1.sum_price,0)-IFNULL(SUM(o2.sum_price),0)-IFNULL(SUM(o3.sum_price),0) AS lgo,
		IFNULL(o1.sum_price,0) AS kidney,IFNULL(SUM(o2.sum_price),0) AS pp,IFNULL(SUM(o3.sum_price),0) AS other,
		v.income-v.rcpt_money-IFNULL(SUM(o2.sum_price),0)-IFNULL(o3.sum_price,0) AS debtor,oe.upload_datetime AS ecliam,
    GROUP_CONCAT(DISTINCT s.`name`) AS list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode ="3003375"
		LEFT JOIN opitemrece o2 ON o2.vn = o.vn AND o2.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
		LEFT JOIN opitemrece o3 ON o3.vn = o.vn AND o3.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code IN ("1102050101.109"))
		LEFT JOIN s_drugitems s ON s.icode=o3.icode
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE p1.pttype LIKE "L%" AND (o.an IS NULL OR o.an ="") 
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND v.pdx IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050102_801)
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue) AS a WHERE a.debtor <> 0'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050102_801',compact('start_date','end_date','debtor','debtor_search_lgo','debtor_search_pp'));
}

public function _1102050102_801_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050102_801_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050102_801_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT d.vstdate,COUNT(DISTINCT d.vn) AS anvn,SUM(d.debtor) AS debtor,
		SUM(IFNULL(s.compensate_treatment,0)+IFNULL(s1.compensate_kidney,0)) AS receive
    FROM debtor_1102050102_801 d   
		LEFT JOIN stm_lgo s ON s.hn=d.hn AND DATE(s.datetimeadm) = d.vstdate AND LEFT(TIME(s.datetimeadm),5) =LEFT(d.vsttime,5)
    LEFT JOIN stm_lgo_kidney s1 ON s1.hn=d.hn AND DATE(s1.datetimeadm) = d.vstdate AND d.kidney IS NOT NULL
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		GROUP BY d.vstdate ORDER BY d.vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050102_801_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050102_801_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkbox_lgo' => 'required|array', 
  ]);
  $checkbox_lgo = $request->input('checkbox_lgo');
  $checkbox = join(",",$checkbox_lgo);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.oqueue,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
		v.income-v.rcpt_money-IFNULL(o1.sum_price,0)-IFNULL(SUM(o2.sum_price),0)-IFNULL(SUM(o3.sum_price),0) AS lgo,
		IFNULL(o1.sum_price,0) AS kidney,IFNULL(SUM(o2.sum_price),0) AS pp,IFNULL(SUM(o3.sum_price),0) AS other,
		v.income-v.rcpt_money-IFNULL(SUM(o2.sum_price),0)-IFNULL(o3.sum_price,0) AS debtor,oe.upload_datetime AS ecliam,
    GROUP_CONCAT(DISTINCT s.`name`) AS list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode ="3003375"
		LEFT JOIN opitemrece o2 ON o2.vn = o.vn AND o2.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
		LEFT JOIN opitemrece o3 ON o3.vn = o.vn AND o3.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code IN ("1102050101.109"))
		LEFT JOIN s_drugitems s ON s.icode=o3.icode
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE p1.pttype LIKE "L%" AND (o.an IS NULL OR o.an ="") 
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050102_801)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue) AS a WHERE a.debtor <> 0 ');     

  foreach ($debtor as $row) {
      Debtor_1102050102_801::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'lgo'             => $row->lgo,
        'kidney'          => $row->kidney,
        'pp'              => $row->pp,
        'other'           => $row->other,
        'debtor'          => $row->debtor,            
    ]);
    if ($row->pp != 0) {
      Debtor_1102050101_209::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'ppfs'            => $row->pp,
        'debtor'          => $row->pp,    
      ]);
    }
  }

  return redirect()->route('_1102050102_801');
}

public function _1102050102_801_confirm_pp(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkbox_pp' => 'required|array', 
  ]);
  $checkbox_pp = $request->input('checkbox_pp');
  $checkbox = join(",",$checkbox_pp);
  
  $debtor = DB::connection('hosxp')->select('
   SELECT * FROM (SELECT o.vn,o.oqueue,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
		v.income-v.rcpt_money-IFNULL(o1.sum_price,0)-IFNULL(SUM(o2.sum_price),0)-IFNULL(SUM(o3.sum_price),0) AS lgo,
		IFNULL(o1.sum_price,0) AS kidney,IFNULL(SUM(o2.sum_price),0) AS pp,IFNULL(SUM(o3.sum_price),0) AS other,
		v.income-v.rcpt_money-IFNULL(SUM(o2.sum_price),0)-IFNULL(o3.sum_price,0) AS debtor,oe.upload_datetime AS ecliam,
    GROUP_CONCAT(DISTINCT s.`name`) AS list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode ="3003375"
		LEFT JOIN opitemrece o2 ON o2.vn = o.vn AND o2.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
		LEFT JOIN opitemrece o3 ON o3.vn = o.vn AND o3.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code IN ("1102050101.109"))
		LEFT JOIN s_drugitems s ON s.icode=o3.icode
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE p1.pttype LIKE "L%" AND (o.an IS NULL OR o.an ="") 
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND v.pdx IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050102_801)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue) AS a WHERE a.debtor <> 0');     

  foreach ($debtor as $row) {
      Debtor_1102050102_801::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'lgo'             => $row->lgo,
        'kidney'          => $row->kidney,
        'pp'              => $row->pp,
        'other'           => $row->other,
        'debtor'          => $row->debtor,            
    ]);
    if ($row->pp != 0) {
      Debtor_1102050101_209::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'ppfs'            => $row->pp,
        'debtor'          => $row->pp,    
      ]);
    }
  }
  return redirect()->route('_1102050102_801');
}

public function _1102050102_801_delete(Request $request )
{
  $request->validate([
    'checkbox_d' => 'required|array', 
  ]);
  $checkbox_d = $request->input('checkbox_d');

    if (!empty($checkbox_d)) {
      Debtor_1102050102_801::whereIn('vn', $checkbox_d)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050102_801');
}

#########################################################################################################
public function _1102050102_803(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT d.vstdate,d.vsttime,d.vn,d.hn,d.cid,d.ptname,d.hipdata_code,d.pttype,d.pdx,d.hospmain,d.income,d.rcpt_money,
    d.ofc,d.kidney,d.pp,d.other,d.debtor,IF(IFNULL(d.repno,IFNULL(s.repno,s1.rid)) <>"","กระทบยอดแล้ว",d.status) AS status,d.charge_date,
    d.charge_no,d.charge,d.receive_date,d.receive_no,IFNULL(d.receive,0)+IFNULL(s.receive_total,0)+IFNULL(s1.amount,0) AS receive,   
		IFNULL(s2.receive_pp,0) AS receive_pp,s.repno,s1.rid,d.repno AS repno_chk,d.debtor_lock
    FROM debtor_1102050102_803 d   
		LEFT JOIN stm_ofc s ON s.hn=d.hn AND DATE(s.datetimeadm) = d.vstdate
			AND LEFT(TIME(s.datetimeadm),5) =LEFT(d.vsttime,5) 
    LEFT JOIN stm_ofc_kidney s1 ON s1.hn=d.hn AND DATE(s1.vstdate) = d.vstdate AND d.kidney IS NOT NULL
    LEFT JOIN stm_ucs s2 ON s2.cid=d.cid AND DATE(s2.datetimeadm) = d.vstdate 
      AND LEFT(TIME(s2.datetimeadm),5) =LEFT(d.vsttime,5)
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search_bkk = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.oqueue,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
		v.income-v.rcpt_money-IFNULL(o1.sum_price,0)-IFNULL(SUM(o2.sum_price),0)-IFNULL(SUM(o3.sum_price),0) AS ofc,
		IFNULL(o1.sum_price,0) AS kidney,IFNULL(SUM(o2.sum_price),0) AS pp,IFNULL(SUM(o3.sum_price),0) AS other,
		v.income-v.rcpt_money-IFNULL(SUM(o2.sum_price),0)-IFNULL(o3.sum_price,0) AS debtor,oe.upload_datetime AS ecliam,
    GROUP_CONCAT(DISTINCT s.`name`) AS list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode ="3003375"
		LEFT JOIN opitemrece o2 ON o2.vn = o.vn AND o2.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
		LEFT JOIN opitemrece o3 ON o3.vn = o.vn AND o3.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code IN ("1102050101.109"))
		LEFT JOIN s_drugitems s ON s.icode=o3.icode
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE p1.hipdata_code = "BKK" AND (o.an IS NULL OR o.an ="") AND v.paid_money="0"
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050102_803)
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue) AS a WHERE a.debtor <> 0');  

  $debtor_search_bmt = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.oqueue,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
		v.income-v.rcpt_money-IFNULL(o1.sum_price,0)-IFNULL(SUM(o2.sum_price),0)-IFNULL(SUM(o3.sum_price),0) AS ofc,
		IFNULL(o1.sum_price,0) AS kidney,IFNULL(SUM(o2.sum_price),0) AS pp,IFNULL(SUM(o3.sum_price),0) AS other,
		v.income-v.rcpt_money-IFNULL(SUM(o2.sum_price),0)-IFNULL(o3.sum_price,0) AS debtor,oe.upload_datetime AS ecliam,
    GROUP_CONCAT(DISTINCT s.`name`) AS list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode ="3003375"
		LEFT JOIN opitemrece o2 ON o2.vn = o.vn AND o2.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
		LEFT JOIN opitemrece o3 ON o3.vn = o.vn AND o3.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code IN ("1102050101.109"))
		LEFT JOIN s_drugitems s ON s.icode=o3.icode
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE p1.hipdata_code = "BMT" AND (o.an IS NULL OR o.an ="") 
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"	
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050102_803)
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue) AS a WHERE a.debtor <> 0'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050102_803',compact('start_date','end_date','debtor','debtor_search_bkk','debtor_search_bmt'));
}

public function _1102050102_803_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050102_803_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050102_803_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT d.vstdate,COUNT(DISTINCT d.vn) AS anvn,SUM(d.debtor) AS debtor,
		SUM(IFNULL(s.receive_total,0)+IFNULL(s1.amount,0)) AS receive
    FROM debtor_1102050102_803 d   
		LEFT JOIN stm_ofc s ON s.hn=d.hn AND DATE(s.datetimeadm) = d.vstdate
			AND LEFT(TIME(s.datetimeadm),5) =LEFT(d.vsttime,5)
    LEFT JOIN stm_ofc_kidney s1 ON s1.hn=d.hn AND DATE(s1.vstdate) = d.vstdate AND d.kidney IS NOT NULL
    WHERE d.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		GROUP BY d.vstdate ORDER BY d.vstdate');

  $pdf = PDF::loadView('finance_debtor.1102050102_803_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050102_803_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkbox_bkk' => 'required|array', 
  ]);
  $checkbox_bkk = $request->input('checkbox_bkk');
  $checkbox = join(",",$checkbox_bkk);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT o.vn,o.oqueue,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
		v.income-v.rcpt_money-IFNULL(o1.sum_price,0)-IFNULL(SUM(o2.sum_price),0)-IFNULL(SUM(o3.sum_price),0) AS ofc,
		IFNULL(o1.sum_price,0) AS kidney,IFNULL(SUM(o2.sum_price),0) AS pp,IFNULL(SUM(o3.sum_price),0) AS other,
		v.income-v.rcpt_money-IFNULL(SUM(o2.sum_price),0)-IFNULL(o3.sum_price,0) AS debtor,oe.upload_datetime AS ecliam,
    GROUP_CONCAT(DISTINCT s.`name`) AS list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode ="3003375"
		LEFT JOIN opitemrece o2 ON o2.vn = o.vn AND o2.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
		LEFT JOIN opitemrece o3 ON o3.vn = o.vn AND o3.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code IN ("1102050101.109"))
		LEFT JOIN s_drugitems s ON s.icode=o3.icode
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE p1.hipdata_code = "BKK" AND (o.an IS NULL OR o.an ="") AND v.paid_money="0"
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050102_803)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue) AS a WHERE a.debtor <> 0 ');     

  foreach ($debtor as $row) {
      Debtor_1102050102_803::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'ofc'             => $row->ofc,
        'kidney'          => $row->kidney,
        'pp'              => $row->pp,
        'other'           => $row->other,
        'debtor'          => $row->debtor,            
    ]);
    if ($row->pp != 0) {
      Debtor_1102050101_209::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'ppfs'            => $row->pp,
        'debtor'          => $row->pp,    
      ]);
    }
  }

  return redirect()->route('_1102050102_803');
}

public function _1102050102_803_confirm_bmt(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkbox_bmt' => 'required|array', 
  ]);
  $checkbox_bmt = $request->input('checkbox_bmt');
  $checkbox = join(",",$checkbox_bmt);
  
  $debtor = DB::connection('hosxp')->select('
   SELECT * FROM (SELECT o.vn,o.oqueue,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
		o.vsttime,p1.name AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
		v.income-v.rcpt_money-IFNULL(o1.sum_price,0)-IFNULL(SUM(o2.sum_price),0)-IFNULL(SUM(o3.sum_price),0) AS ofc,
		IFNULL(o1.sum_price,0) AS kidney,IFNULL(SUM(o2.sum_price),0) AS pp,IFNULL(SUM(o3.sum_price),0) AS other,
		v.income-v.rcpt_money-IFNULL(SUM(o2.sum_price),0)-IFNULL(o3.sum_price,0) AS debtor,oe.upload_datetime AS ecliam,
    GROUP_CONCAT(DISTINCT s.`name`) AS list
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece o1 ON o1.vn = o.vn AND o1.icode ="3003375"
		LEFT JOIN opitemrece o2 ON o2.vn = o.vn AND o2.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.209") 
		LEFT JOIN opitemrece o3 ON o3.vn = o.vn AND o3.icode IN 
			(SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code IN ("1102050101.109"))
		LEFT JOIN s_drugitems s ON s.icode=o3.icode
		LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn	
    WHERE p1.hipdata_code = "BMT" AND (o.an IS NULL OR o.an ="") 
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		AND o.vn NOT IN (SELECT vn FROM htp_report.debtor_1102050102_803)
    AND o.vn IN ('.$checkbox.')
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue) AS a WHERE a.debtor <> 0');     

  foreach ($debtor as $row) {
      Debtor_1102050102_803::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'ofc'             => $row->ofc,
        'kidney'          => $row->kidney,
        'pp'              => $row->pp,
        'other'           => $row->other,
        'debtor'          => $row->debtor,            
    ]);
    if ($row->pp != 0) {
      Debtor_1102050101_209::insert([
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'an'              => $row->an,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'vstdate'         => $row->vstdate,
        'vsttime'         => $row->vsttime,
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money,
        'ppfs'            => $row->pp,
        'debtor'          => $row->pp,    
      ]);
    }
  }
  return redirect()->route('_1102050102_803');
}

public function _1102050102_803_delete(Request $request )
{
  $request->validate([
    'checkbox_d' => 'required|array', 
  ]);
  $checkbox_d = $request->input('checkbox_d');

    if (!empty($checkbox_d)) {
      Debtor_1102050102_803::whereIn('vn', $checkbox_d)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050102_803');
}

public function _1102050102_803_update(Request $request, $vn)
{
  $item = Debtor_1102050102_803::find($vn);
  $item->charge_date = $request->input('charge_date');
  $item->charge_no = $request->input('charge_no');
  $item->charge = $request->input('charge');
  $item->receive_date = $request->input('receive_date');
  $item->receive_no = $request->input('receive_no');
  $item->receive = $request->input('receive');
  $item->repno = $request->input('repno');  
  $item->status = $request->input('status'); 
  $item->save();

  return redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย');  
}

###################################################################################################
public function _1102050101_202(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT d.*,SUM(stm.receive_ip_compensate_pay) AS receive_ip_compensate_pay,
    stm.fund_ip_adjrw,GROUP_CONCAT(DISTINCT stm.fund_ip_payrate) AS fund_ip_payrate,
    GROUP_CONCAT(DISTINCT stm.repno) AS repno
    FROM debtor_1102050101_202 d
    LEFT JOIN stm_ucs stm ON stm.an=d.an
    WHERE d.dchdate BETWEEN ? AND ?
		GROUP BY d.an',[$start_date,$end_date]);

  $debtor_search = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,ip.pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216") 		
    WHERE i.confirm_discharge = "Y" AND p1.hipdata_code = "UCS" 
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_202) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_202',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050101_202_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_202_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_202_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT dchdate AS vstdate,COUNT(DISTINCT an) AS anvn,
    SUM(debtor) AS debtor,SUM(receive_ip_compensate_pay) AS receive_total
    FROM (SELECT d.dchdate,d.an,d.debtor,stm.receive_ip_compensate_pay FROM debtor_1102050101_202 d
    LEFT JOIN stm_ucs stm ON stm.an=d.an    
    WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		GROUP BY d.an) AS a
    GROUP BY dchdate ORDER BY dchdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_202_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_202_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,ip.pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216")
    WHERE i.confirm_discharge = "Y" AND p1.hipdata_code = "UCS" 
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    and i.an in ('.$checkbox.')
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_202) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate');     

  foreach ($debtor as $row) {
      Debtor_1102050101_202::insert([
        'an'              => $row->an,
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'regdate'         => $row->regdate,
        'regtime'         => $row->regtime,
        'dchdate'         => $row->dchdate,
        'dchtime'         => $row->dchtime,    
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'adjrw'           => $row->adjrw,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money, 
        'other'           => $row->other,  
        'debtor'          => $row->debtor,    
    ]);
  }

  return redirect()->route('_1102050101_202');
}

public function _1102050101_202_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_202::whereIn('an', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_202');
}

###############################################################################
public function _1102050101_217(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT d.*,(stm.receive_total-stm.receive_ip_compensate_pay)+IFNULL(SUM(stm1.receive_total),0) AS receive,
		stm.receive_total-stm.receive_ip_compensate_pay AS receive_cr,IFNULL(SUM(stm1.receive_total),0) AS receive_kidney,
		stm.repno,stm1.repno AS repno_kidney		
    FROM debtor_1102050101_217 d
    LEFT JOIN stm_ucs stm ON stm.an=d.an
		LEFT JOIN stm_ucs_kidney stm1 ON stm1.cid=d.cid AND stm1.datetimeadm BETWEEN d.regdate AND d.dchdate
		WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY d.an');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,ip.pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS kidney,
		IFNULL(SUM(o2.sum_price),0) AS cr,GROUP_CONCAT(DISTINCT s.`name`) AS kidney_list,GROUP_CONCAT(DISTINCT s2.`name`) AS cr_list,
		IFNULL(SUM(o1.sum_price),0)+IFNULL(SUM(o2.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.217" AND icode_type = "kidney") 
		LEFT JOIN s_drugitems s ON s.icode=o1.icode			
		LEFT JOIN opitemrece o2 ON o2.an = i.an AND o2.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.217" AND icode_type <> "kidney") 
		LEFT JOIN s_drugitems s2 ON s2.icode=o2.icode		
    WHERE i.confirm_discharge = "Y" AND p1.hipdata_code = "UCS" 
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_217) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate ) AS a WHERE debtor <> 0 '); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_217',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050101_217_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_217_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_217_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT dchdate AS vstdate,COUNT(DISTINCT an) AS anvn,SUM(debtor) AS debtor,SUM(receive) AS receive_total
    FROM (SELECT d.dchdate,d.an,d.debtor,(stm.receive_total-stm.receive_ip_compensate_pay)+IFNULL(SUM(stm1.receive_total),0) AS receive,
		stm.receive_total-stm.receive_ip_compensate_pay AS receive_cr,IFNULL(SUM(stm1.receive_total),0) AS receive_kidney,
		stm.repno,stm1.repno AS repno_kidney		
    FROM debtor_1102050101_217 d
    LEFT JOIN stm_ucs stm ON stm.an=d.an
		LEFT JOIN stm_ucs_kidney stm1 ON stm1.cid=d.cid AND stm1.datetimeadm BETWEEN d.regdate AND d.dchdate
		WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY d.an) AS a 
		GROUP BY dchdate ORDER BY dchdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_217_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_217_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,ip.pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS kidney,
		IFNULL(SUM(o2.sum_price),0) AS cr,GROUP_CONCAT(DISTINCT s.`name`) AS kidney_list,GROUP_CONCAT(DISTINCT s2.`name`) AS cr_list,
		IFNULL(SUM(o1.sum_price),0)+IFNULL(SUM(o2.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
       IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.217" AND icode_type = "kidney") 
		LEFT JOIN s_drugitems s ON s.icode=o1.icode			
		LEFT JOIN opitemrece o2 ON o2.an = i.an AND o2.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.217" AND icode_type <> "kidney") 
		LEFT JOIN s_drugitems s2 ON s2.icode=o2.icode		
    WHERE i.confirm_discharge = "Y" AND p1.hipdata_code = "UCS" 
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_217) 
    AND i.an in ('.$checkbox.')
    GROUP BY i.an ORDER BY i.ward,i.dchdate ) AS a WHERE debtor <> 0 ');     

  foreach ($debtor as $row) {
      Debtor_1102050101_217::insert([
        'an'              => $row->an,
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'regdate'         => $row->regdate,
        'regtime'         => $row->regtime,
        'dchdate'         => $row->dchdate,
        'dchtime'         => $row->dchtime,    
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'adjrw'           => $row->adjrw,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money, 
        'kidney'          => $row->kidney,  
        'cr'              => $row->cr,  
        'debtor'          => $row->debtor,    
    ]);
  }

  return redirect()->route('_1102050101_217');
}

public function _1102050101_217_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_217::whereIn('an', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_217');
}

###############################################################################
public function _1102050101_302(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $search  =  $request->search; 
  if($search =='' || $search == null )
  {$search = Session::get('search');}else{$search =$request->search;}

  $debtor =  Debtor_1102050101_302::whereBetween('dchdate', [$start_date,$end_date])
              ->where(function ($query) use ($search){
                $query->where('ptname','like','%'.$search.'%');
                $query->orwhere('hn','like','%'.$search.'%');
                $query->orwhere('an','like','%'.$search.'%');
                })
              ->orderBy('dchdate')->get();     

  // $debtor = DB::select('
  //   SELECT *
  //   FROM debtor_1102050101_302     
  //   WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,ip.pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216") 		
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("S1","S2","S3","S4","S5","S7") AND ip.hospmain ="10703"
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_302) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('search',$search);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_302',compact('start_date','end_date','search','debtor','debtor_search'));
}

public function _1102050101_302_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_302_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_302_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT dchdate AS vstdate,COUNT(DISTINCT an) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive_total
    FROM debtor_1102050101_302    
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
    GROUP BY dchdate ORDER BY dchdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_302_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_302_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,ip.pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216")
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("S1","S2","S3","S4","S5","S7") AND ip.hospmain = "10703" 
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    and i.an in ('.$checkbox.')
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_302) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate');     

  foreach ($debtor as $row) {
      Debtor_1102050101_302::insert([
        'an'              => $row->an,
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'regdate'         => $row->regdate,
        'regtime'         => $row->regtime,
        'dchdate'         => $row->dchdate,
        'dchtime'         => $row->dchtime,    
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'adjrw'           => $row->adjrw,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money, 
        'other'           => $row->other,  
        'debtor'          => $row->debtor, 
        'status'          => $row->status,     
    ]);
  }

  return redirect()->route('_1102050101_302');
}

public function _1102050101_302_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      debtor_1102050101_302::whereIn('an', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_302');
}

public function _1102050101_302_update(Request $request, $an)
{
  $item = Debtor_1102050101_302::findOrFail($an);
  $item->update([
    'charge_date' => $request->input('charge_date'),
    'charge_no' => $request->input('charge_no'),
    'charge' => $request->input('charge'),
    'receive_date' => $request->input('receive_date'),
    'receive_no' => $request->input('receive_no'),
    'receive' => $request->input('receive'),
    'repno' => $request->input('repno'),
    'status' => $request->input('status'),
  ]);
  return  redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย'); 
}

###############################################################################
public function _1102050101_304(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT *
    FROM debtor_1102050101_304     
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,ip.pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,IFNULL(SUM(o1.sum_price),0) AS income_pttype,a.rcpt_money,
		IFNULL(SUM(o2.sum_price),0) AS other,IFNULL(SUM(o1.sum_price),0)-a.rcpt_money-IFNULL(SUM(o2.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.pttype=ip.pttype 
		LEFT JOIN opitemrece o2 ON o2.an = i.an AND o2.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216") 		
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("S1","S2","S3","S4","S5","S7") AND ip.hospmain <> "10703"
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_304) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_304',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050101_304_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_304_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_304_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT dchdate AS vstdate,COUNT(DISTINCT an) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive_total
    FROM debtor_1102050101_304    
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
    GROUP BY dchdate ORDER BY dchdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_304_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_304_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,ip.pttype,ip.hospmain,p1.hipdata_code,
		i.adjrw,a.income,IFNULL(SUM(o1.sum_price),0) AS income_pttype,a.rcpt_money,IFNULL(SUM(o2.sum_price),0) AS other,
		IFNULL(SUM(o1.sum_price),0)-a.rcpt_money-IFNULL(SUM(o2.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.pttype=ip.pttype 
		LEFT JOIN opitemrece o2 ON o2.an = i.an AND o2.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216") 		
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("S1","S2","S3","S4","S5","S7") AND ip.hospmain <> "10703"
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_304) 
    AND i.an in ('.$checkbox.')
    GROUP BY i.an ORDER BY i.ward,i.dchdate');     

  foreach ($debtor as $row) {
      Debtor_1102050101_304::insert([
        'an'              => $row->an,
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'regdate'         => $row->regdate,
        'regtime'         => $row->regtime,
        'dchdate'         => $row->dchdate,
        'dchtime'         => $row->dchtime,    
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'adjrw'           => $row->adjrw,  
        'income'          => $row->income,  
        'income_pttype'   => $row->income_pttype, 
        'rcpt_money'      => $row->rcpt_money, 
        'other'           => $row->other,  
        'debtor'          => $row->debtor,    
        'status'          => $row->status, 
    ]);
  }

  return redirect()->route('_1102050101_304');
}

public function _1102050101_304_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_304::whereIn('an', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_304');
}

public function _1102050101_304_update(Request $request, $an)
{
  $item = Debtor_1102050101_304::findOrFail($an);
  $item->update([
    'charge_date' => $request->input('charge_date'),
    'charge_no' => $request->input('charge_no'),
    'charge' => $request->input('charge'),
    'receive_date' => $request->input('receive_date'),
    'receive_no' => $request->input('receive_no'),
    'receive' => $request->input('receive'),
    'repno' => $request->input('repno'),
    'status' => $request->input('status'),
  ]);
  return  redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย'); 
}

###############################################################################
public function _1102050101_308(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $search  =  $request->search; 
  if($search =='' || $search == null )
  {$search = Session::get('search');}else{$search =$request->search;}

  $debtor =  Debtor_1102050101_308::whereBetween('dchdate', [$start_date,$end_date])
              ->where(function ($query) use ($search){
                $query->where('ptname','like','%'.$search.'%');
                $query->orwhere('hn','like','%'.$search.'%');
                $query->orwhere('an','like','%'.$search.'%');
                })
              ->orderBy('dchdate')->get();     

  // $debtor = DB::select('
  //   SELECT *
  //   FROM debtor_1102050101_308     
  //   WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,ip.pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,IFNULL(SUM(o1.sum_price),0) AS income_pttype,a.rcpt_money,
		IFNULL(SUM(o2.sum_price),0) AS other,IFNULL(SUM(o1.sum_price),0)-a.rcpt_money-IFNULL(SUM(o2.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.pttype=ip.pttype 
		LEFT JOIN opitemrece o2 ON o2.an = i.an AND o2.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216") 		
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("32")
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_308) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('search',$search);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_308',compact('start_date','end_date','search','debtor','debtor_search'));
}

public function _1102050101_308_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_308_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_308_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT dchdate AS vstdate,COUNT(DISTINCT an) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive_total
    FROM debtor_1102050101_308    
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
    GROUP BY dchdate ORDER BY dchdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_308_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_308_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,ip.pttype,ip.hospmain,p1.hipdata_code,
		i.adjrw,a.income,IFNULL(SUM(o1.sum_price),0) AS income_pttype,a.rcpt_money,IFNULL(SUM(o2.sum_price),0) AS other,
		IFNULL(SUM(o1.sum_price),0)-a.rcpt_money-IFNULL(SUM(o2.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.pttype=ip.pttype 
		LEFT JOIN opitemrece o2 ON o2.an = i.an AND o2.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216") 		
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("32")
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_308) 
    AND i.an in ('.$checkbox.')
    GROUP BY i.an ORDER BY i.ward,i.dchdate');     

  foreach ($debtor as $row) {
      Debtor_1102050101_308::insert([
        'an'              => $row->an,
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'regdate'         => $row->regdate,
        'regtime'         => $row->regtime,
        'dchdate'         => $row->dchdate,
        'dchtime'         => $row->dchtime,    
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'adjrw'           => $row->adjrw,  
        'income'          => $row->income,  
        'income_pttype'   => $row->income_pttype,  
        'rcpt_money'      => $row->rcpt_money, 
        'other'           => $row->other,  
        'debtor'          => $row->debtor,    
        'status'          => $row->status,  
    ]);
  }

  return redirect()->route('_1102050101_308');
}

public function _1102050101_308_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_308::whereIn('an', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_308');
}

public function _1102050101_308_update(Request $request, $an)
{
  $item = Debtor_1102050101_308::findOrFail($an);
  $item->update([
    'charge_date' => $request->input('charge_date'),
    'charge_no' => $request->input('charge_no'),
    'charge' => $request->input('charge'),
    'receive_date' => $request->input('receive_date'),
    'receive_no' => $request->input('receive_no'),
    'receive' => $request->input('receive'),
    'repno' => $request->input('repno'),
    'status' => $request->input('status'),
  ]);
  return  redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย'); 
}

###############################################################################
public function _1102050101_310(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT *
    FROM debtor_1102050101_310     
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,ip.pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS kidney,
		IFNULL(SUM(o1.sum_price),0) AS debtor,GROUP_CONCAT(DISTINCT s.`name`) AS list
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE icode_type = "kidney") 	
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE i.confirm_discharge = "Y" AND p1.hipdata_code IN ("SSS","SSI")
    AND o1.an IS NOT NULL AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_310) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_310',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050101_310_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_310_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_310_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT dchdate AS vstdate,COUNT(DISTINCT an) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive_total
    FROM debtor_1102050101_310    
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
    GROUP BY dchdate ORDER BY dchdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_310_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_310_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,ip.pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS kidney,
		IFNULL(SUM(o1.sum_price),0) AS debtor,GROUP_CONCAT(DISTINCT s.`name`) AS list
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE icode_type = "kidney") 	
		LEFT JOIN s_drugitems s ON s.icode = o1.icode			
    WHERE i.confirm_discharge = "Y" AND p1.hipdata_code IN ("SSS","SSI")
    AND o1.an IS NOT NULL AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_310) 
    AND i.an in ('.$checkbox.')
    GROUP BY i.an ORDER BY i.ward,i.dchdate');     

  foreach ($debtor as $row) {
      Debtor_1102050101_310::insert([
        'an'              => $row->an,
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'regdate'         => $row->regdate,
        'regtime'         => $row->regtime,
        'dchdate'         => $row->dchdate,
        'dchtime'         => $row->dchtime,    
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'adjrw'           => $row->adjrw,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money, 
        'kidney'          => $row->kidney,  
        'debtor'          => $row->debtor,    
    ]);
  }

  return redirect()->route('_1102050101_310');
}

public function _1102050101_310_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_310::whereIn('an', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_310');
}

###############################################################################
public function _1102050101_402(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT d.*,s.receive_total+IFNULL(SUM(s1.amount),0) AS receive_total ,
    SUM(s1.amount) AS receive_kidney, s.receive_total AS receive_eclaim,s.repno
    FROM debtor_1102050101_402 d    
    LEFT JOIN htp_report.stm_ofc s ON s.an=d.an
    LEFT JOIN htp_report.stm_ofc_kidney s1 ON s1.hn=d.hn AND s1.vstdate BETWEEN d.regdate AND d.dchdate
    WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY d.an');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS kidney,
		IFNULL(SUM(o2.sum_price),0) AS other,a.income-a.rcpt_money-IFNULL(SUM(o2.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode = "3003375"
		LEFT JOIN opitemrece o2 ON o2.an = i.an AND o2.icode 
			IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (debtor_code = "1102050101.109"))		
    WHERE i.confirm_discharge = "Y" AND p1.pttype LIKE "O%"
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_402) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate) AS a WHERE debtor <> "0"'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_402',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050101_402_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_402_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_402_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT dchdate AS vstdate,COUNT(DISTINCT an) AS anvn,
    SUM(debtor) AS debtor,SUM(receive_total) AS receive_total
		FROM (SELECT d.*,s.receive_total+IFNULL(SUM(s1.amount),0) AS receive_total 
    FROM debtor_1102050101_402 d    
    LEFT JOIN htp_report.stm_ofc s ON s.an=d.an
    LEFT JOIN htp_report.stm_ofc_kidney s1 ON s1.hn=d.hn AND s1.vstdate BETWEEN d.regdate AND d.dchdate
    WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY d.an) AS a 
		GROUP BY dchdate ORDER BY dchdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_402_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_402_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS kidney,
		IFNULL(SUM(o2.sum_price),0) AS other,a.income-a.rcpt_money-IFNULL(SUM(o2.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode = "3003375"
		LEFT JOIN opitemrece o2 ON o2.an = i.an AND o2.icode 
			IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (debtor_code = "1102050101.109"))		
    WHERE i.confirm_discharge = "Y" AND p1.pttype LIKE "O%"
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_402) 
    AND i.an in ('.$checkbox.')
    GROUP BY i.an ORDER BY i.ward,i.dchdate) AS a WHERE debtor <> "0"');     

  foreach ($debtor as $row) {
      Debtor_1102050101_402::insert([
        'an'              => $row->an,
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'regdate'         => $row->regdate,
        'regtime'         => $row->regtime,
        'dchdate'         => $row->dchdate,
        'dchtime'         => $row->dchtime,    
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'adjrw'           => $row->adjrw,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money, 
        'kidney'          => $row->kidney, 
        'other'           => $row->other,  
        'debtor'          => $row->debtor,    
    ]);
  }

  return redirect()->route('_1102050101_402');
}

public function _1102050101_402_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_402::whereIn('an', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_402');
}

###############################################################################
public function _1102050101_502(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT *
    FROM debtor_1102050101_502     
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,ip.pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216") 		
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("11","N1","N2","N3","N4","N5")	
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_502) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_502',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050101_502_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_502_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_502_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT dchdate AS vstdate,COUNT(DISTINCT an) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive_total
    FROM debtor_1102050101_502    
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
    GROUP BY dchdate ORDER BY dchdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_502_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_502_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,ip.pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216")
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("11","N1","N2","N3","N4","N5")	
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an in ('.$checkbox.')
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_502) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate');     

  foreach ($debtor as $row) {
      Debtor_1102050101_502::insert([
        'an'              => $row->an,
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'regdate'         => $row->regdate,
        'regtime'         => $row->regtime,
        'dchdate'         => $row->dchdate,
        'dchtime'         => $row->dchtime,    
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'adjrw'           => $row->adjrw,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money, 
        'other'           => $row->other,  
        'debtor'          => $row->debtor,  
        'status'          => $row->status,    
    ]);
  }

  return redirect()->route('_1102050101_502');
}

public function _1102050101_502_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_502::whereIn('an', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_502');
}

public function _1102050101_502_update(Request $request, $an)
{
  $item = Debtor_1102050101_502::findOrFail($an);
  $item->update([
    'charge_date' => $request->input('charge_date'),
    'charge_no' => $request->input('charge_no'),
    'charge' => $request->input('charge'),
    'receive_date' => $request->input('receive_date'),
    'receive_no' => $request->input('receive_no'),
    'receive' => $request->input('receive'),
    'repno' => $request->input('repno'),
    'status' => $request->input('status'),
  ]);
  return  redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย'); 
}

###############################################################################
public function _1102050101_704(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT *
    FROM debtor_1102050101_704     
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216") 		
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("ST")
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_704) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050101_704',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050101_704_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050101_704_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050101_704_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT dchdate AS vstdate,COUNT(DISTINCT an) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive_total
    FROM debtor_1102050101_704    
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
    GROUP BY dchdate ORDER BY dchdate');

  $pdf = PDF::loadView('finance_debtor.1102050101_704_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050101_704_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216")
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("ST")	
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"  AND i.an in ('.$checkbox.')
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050101_704) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate');     

  foreach ($debtor as $row) {
      Debtor_1102050101_704::insert([
        'an'              => $row->an,
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'regdate'         => $row->regdate,
        'regtime'         => $row->regtime,
        'dchdate'         => $row->dchdate,
        'dchtime'         => $row->dchtime,    
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'adjrw'           => $row->adjrw,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money, 
        'other'           => $row->other,  
        'debtor'          => $row->debtor,    
    ]);
  }

  return redirect()->route('_1102050101_704');
}

public function _1102050101_704_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050101_704::whereIn('an', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050101_704');
}

###############################################################################
public function _1102050102_107(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
  	SELECT d.regdate,d.regtime,d.dchdate,d.dchtime,d.hn,d.vn,d.an,d.ptname,d.mobile_phone_number,d.pttype,d.pdx,d.income,d.paid_money,d.rcpt_money,
    d.debtor,d.debtor_lock,IF(r.bill_amount <>"","กระทบยอดแล้ว",d.status) AS status,d.charge_date,d.charge_no,d.charge,
    d.receive_date,d.receive_no,IFNULL(d.receive,r.bill_amount) AS receive,IFNULL(d.repno,r.rcpno) AS repno,r.bill_amount,
    IF(t.visit IS NULL,0,t.visit) AS visit
    FROM debtor_1102050102_107 d
    LEFT JOIN hosxe.rcpt_print r ON r.vn = d.an AND r.`status` ="OK" AND r.department="IPD" AND r.bill_date NOT BETWEEN d.regdate AND d.dchdate
    LEFT JOIN (SELECT an,COUNT(an) AS visit FROM debtor_1102050102_107_tracking GROUP BY an) t ON t.an=d.an
    WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" ');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT i.regdate,i.regtime,i.dchdate,i.dchtime,i.hn,i.vn,i.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    p.mobile_phone_number,a.age_y,p1.`name` AS pttype,ip.hospmain,p1.hipdata_code,a.pdx,a.income,
    a.paid_money,a.rcpt_money,(a.paid_money-a.rcpt_money) AS debtor,r.rcpno,p2.arrear_date,p2.amount AS arrear_amount,
    r1.bill_amount AS paid_arrear,r1.rcpno AS rcpno_arrear,fd.deposit_amount,fd1.debit_amount
    FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient_arrear p2 ON p2.an=i.an
    LEFT JOIN patient_finance_deposit fd ON fd.anvn = i.an
    LEFT JOIN patient_finance_debit fd1 ON fd1.anvn = i.an
		LEFT JOIN rcpt_print r ON r.vn = i.an AND r.`status` ="OK" AND r.department="IPD" AND r.bill_date BETWEEN i.regdate AND i.dchdate
		LEFT JOIN rcpt_print r1 ON r1.vn = p2.an AND r1.`status` ="OK" AND r1.department="IPD"
    LEFT JOIN patient p ON p.hn=i.hn
    LEFT JOIN hospcode h ON h.hospcode=ip.hospmain
    WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND a.paid_money <>"0" AND a.rcpt_money <> a.paid_money 
		AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050102_107)
    GROUP BY i.an ORDER BY i.dchdate'); 

  $debtor_search_iclaim = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.109" ) 
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("26")
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.finance_debtor_1102050102_107) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate) AS a WHERE debtor <> "0"'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050102_107',compact('start_date','end_date','debtor','debtor_search','debtor_search_iclaim'));
}

public function _1102050102_107_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050102_107_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050102_107_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT d.dchdate,COUNT(DISTINCT d.vn) AS anvn,
    SUM(debtor) AS debtor,SUM(IFNULL(d.receive,r.bill_amount)) AS receive
    FROM debtor_1102050102_107 d
    LEFT JOIN hosxe.rcpt_print r ON r.vn = d.vn AND r.`status` ="OK" AND r.department="OPD"
    WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY d.dchdate ORDER BY d.dchdate');

  $pdf = PDF::loadView('finance_debtor.1102050102_107_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050102_107_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT i.regdate,i.regtime,i.dchdate,i.dchtime,i.hn,i.vn,i.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    p.mobile_phone_number,a.age_y,p1.`name` AS pttype,ip.hospmain,p1.hipdata_code,a.pdx,a.income,
    a.paid_money,a.rcpt_money,(a.paid_money-a.rcpt_money) AS debtor,r.rcpno,p2.arrear_date,p2.amount AS arrear_amount,
    r1.bill_amount AS paid_arrear,r1.rcpno AS rcpno_arrear,fd.deposit_amount,fd1.debit_amount,"ยืนยันลูกหนี้" AS status
    FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient_arrear p2 ON p2.an=i.an
    LEFT JOIN patient_finance_deposit fd ON fd.anvn = i.an
    LEFT JOIN patient_finance_debit fd1 ON fd1.anvn = i.an
		LEFT JOIN rcpt_print r ON r.vn = i.an AND r.`status` ="OK" AND r.department="IPD" AND r.bill_date BETWEEN i.regdate AND i.dchdate
		LEFT JOIN rcpt_print r1 ON r1.vn = p2.an AND r1.`status` ="OK" AND r1.department="IPD"
    LEFT JOIN patient p ON p.hn=i.hn
    LEFT JOIN hospcode h ON h.hospcode=ip.hospmain
    WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" and i.an in ('.$checkbox.')
    AND a.paid_money <>"0" AND a.rcpt_money <> a.paid_money 
		AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050102_107)
    GROUP BY i.an ORDER BY i.dchdate');     

  foreach ($debtor as $row) {
      Debtor_1102050102_107::insert([
        'vn'                  => $row->vn,
        'hn'                  => $row->hn,
        'an'                  => $row->an,
        'cid'                 => $row->cid,
        'ptname'              => $row->ptname,
        'mobile_phone_number' => $row->mobile_phone_number,
        'regdate'             => $row->regdate,
        'regtime'             => $row->regtime,
        'dchdate'             => $row->dchdate,
        'dchtime'             => $row->dchtime,
        'pttype'              => $row->pttype,    
        'hospmain'            => $row->hospmain,    
        'hipdata_code'        => $row->hipdata_code,   
        'pdx'                 => $row->pdx,  
        'income'              => $row->income,  
        'paid_money'          => $row->paid_money,
        'rcpt_money'          => $row->rcpt_money,
        'debtor'              => $row->debtor,      
        'status'              => $row->status,       
    ]);
  }

  return redirect()->route('_1102050102_107');
}

public function _1102050102_107_confirm_iclaim(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkbox_iclaim' => 'required|array', 
  ]);
  $checkbox_iclaim = $request->input('checkbox_iclaim');
  $checkbox = join(",",$checkbox_iclaim);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		p.mobile_phone_number,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
    ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.paid_money,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code = "1102050101.109" ) 
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("26")
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND i.an in ('.$checkbox.')
    AND i.an NOT IN (SELECT an FROM htp_report.finance_debtor_1102050102_107) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate) AS a WHERE debtor <> "0"');     

  foreach ($debtor as $row) {
      Debtor_1102050102_107::insert([
        'vn'                  => $row->vn,
        'hn'                  => $row->hn,
        'an'                  => $row->an,
        'cid'                 => $row->cid,
        'ptname'              => $row->ptname,
        'mobile_phone_number' => $row->mobile_phone_number,
        'regdate'             => $row->regdate,
        'regtime'             => $row->regtime,
        'dchdate'             => $row->dchdate,
        'dchtime'             => $row->dchtime,
        'pttype'              => $row->pttype,    
        'hospmain'            => $row->hospmain,    
        'hipdata_code'        => $row->hipdata_code,   
        'pdx'                 => $row->pdx,  
        'income'              => $row->income,  
        'paid_money'          => $row->paid_money,
        'rcpt_money'          => $row->rcpt_money,
        'debtor'              => $row->debtor,    
        'status'              => $row->status,         
    ]);
  }

  return redirect()->route('_1102050102_107');
}

public function _1102050102_107_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050102_107::whereIn('an', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050102_107');
}

public function _1102050102_107_update(Request $request, $an)
{
  $item = Debtor_1102050102_107::find($an);
  $item->charge_date = $request->input('charge_date');
  $item->charge_no = $request->input('charge_no');
  $item->charge = $request->input('charge');
  $item->receive_date = $request->input('receive_date');
  $item->receive_no = $request->input('receive_no');
  $item->receive = $request->input('receive');
  $item->repno = $request->input('repno');  
  $item->status = $request->input('status'); 
  $item->save();

  return redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย');
}

public function _1102050102_107_tracking(Request $request, $an )
{
  $debtor = DB::select('
    SELECT * FROM debtor_1102050102_107 WHERE an = "'.$an.'"');

  $tracking = DB::select('
    SELECT * FROM debtor_1102050102_107_tracking WHERE an = "'.$an.'"');

  return view('finance_debtor.1102050102_107_tracking',compact('debtor','tracking'));
}

public function _1102050102_107_tracking_insert(Request $request)
{
  $item = new Debtor_1102050102_107_tracking;
  $item->vn = $request->input('vn');
  $item->an = $request->input('an');
  $item->tracking_date = $request->input('tracking_date');
  $item->tracking_type = $request->input('tracking_type');
  $item->tracking_no = $request->input('tracking_no');
  $item->tracking_officer = $request->input('tracking_officer');
  $item->tracking_note = $request->input('tracking_note');  
  $item->save();  

  return redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย'); 
}

public function _1102050102_107_tracking_update(Request $request, $tracking_id)
{
  Debtor_1102050102_107_tracking::where('tracking_id', $tracking_id)
      ->update([
      'tracking_date' => $request->input('tracking_date'),
      'tracking_type' => $request->input('tracking_type'),
      'tracking_no' => $request->input('tracking_no'),
      'tracking_officer' => $request->input('tracking_officer'),
      'tracking_note' => $request->input('tracking_note')
      ]);

  return redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย');  
}

###############################################################################
public function _1102050102_109(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT *
    FROM debtor_1102050102_109   
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216") 		
    WHERE i.confirm_discharge = "Y" AND p1.hipdata_code IN ("A2","BFC","GOF","PVT","WVO")
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050102_109) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate) AS a WHERE debtor <> "0"'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050102_109',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050102_109_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050102_109_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050102_109_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT dchdate AS vstdate,COUNT(DISTINCT an) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive_total
    FROM debtor_1102050102_109   
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
    GROUP BY dchdate ORDER BY dchdate');

  $pdf = PDF::loadView('finance_debtor.1102050102_109_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050102_109_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216")
    WHERE i.confirm_discharge = "Y" AND p1.hipdata_code IN ("A2","BFC","GOF","PVT","WVO")
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    and i.an in ('.$checkbox.')
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050102_109) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate) AS a WHERE debtor <> "0"');     

  foreach ($debtor as $row) {
      Debtor_1102050102_109::insert([
        'an'              => $row->an,
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'regdate'         => $row->regdate,
        'regtime'         => $row->regtime,
        'dchdate'         => $row->dchdate,
        'dchtime'         => $row->dchtime,    
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'adjrw'           => $row->adjrw,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money, 
        'other'           => $row->other,  
        'debtor'          => $row->debtor, 
        'status'          => $row->status,    
    ]);
  }

  return redirect()->route('_1102050102_109');
}

public function _1102050102_109_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050102_109::whereIn('an', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050102_109');
}

public function _1102050102_109_update(Request $request, $an)
{
  $item = Debtor_1102050102_109::findOrFail($an);
  $item->update([
    'charge_date' => $request->input('charge_date'),
    'charge_no' => $request->input('charge_no'),
    'charge' => $request->input('charge'),
    'receive_date' => $request->input('receive_date'),
    'receive_no' => $request->input('receive_no'),
    'receive' => $request->input('receive'),
    'repno' => $request->input('repno'),
    'status' => $request->input('status'),
  ]);
  return  redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย'); 
}

###############################################################################
public function _1102050102_603(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $search  =  $request->search; 
  if($search =='' || $search == null )
  {$search = Session::get('search');}else{$search =$request->search;}

  $debtor =  Debtor_1102050102_603::whereBetween('dchdate', [$start_date,$end_date])
              ->where(function ($query) use ($search){
                $query->where('ptname','like','%'.$search.'%');
                $query->orwhere('hn','like','%'.$search.'%');
                $query->orwhere('an','like','%'.$search.'%');
                })
              ->orderBy('dchdate')->get();     

  // $debtor = DB::select('
  //   SELECT *
  //   FROM debtor_1102050102_603   
  //   WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216") 		
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("29")
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050102_603) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate) AS a WHERE debtor <> "0"'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('search',$search);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050102_603',compact('start_date','end_date','search','debtor','debtor_search'));
}

public function _1102050102_603_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050102_603_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050102_603_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT dchdate AS vstdate,COUNT(DISTINCT an) AS anvn,
    SUM(debtor) AS debtor,SUM(receive) AS receive_total
    FROM debtor_1102050102_603  
    WHERE dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
    GROUP BY dchdate ORDER BY dchdate');

  $pdf = PDF::loadView('finance_debtor.1102050102_603_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050102_603_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS other,
		a.income-a.rcpt_money-IFNULL(SUM(o1.sum_price),0) AS debtor,"ยืนยันลูกหนี้" AS status
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode 
      IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE debtor_code <> "1102050101.216")
    WHERE i.confirm_discharge = "Y" AND p1.pttype IN ("29")
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    and i.an in ('.$checkbox.')
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050102_603) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate) AS a WHERE debtor <> "0"');     

  foreach ($debtor as $row) {
      Debtor_1102050102_603::insert([
        'an'              => $row->an,
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'regdate'         => $row->regdate,
        'regtime'         => $row->regtime,
        'dchdate'         => $row->dchdate,
        'dchtime'         => $row->dchtime,    
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'adjrw'           => $row->adjrw,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money, 
        'other'           => $row->other,  
        'debtor'          => $row->debtor,    
        'status'          => $row->status,  
    ]);
  }

  return redirect()->route('_1102050102_603');
}

public function _1102050102_603_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050102_603::whereIn('an', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050102_603');
}

public function _1102050102_603_update(Request $request, $an)
{
  $item = Debtor_1102050102_603::find($an);
  $item->charge_date = $request->input('charge_date');
  $item->charge_no = $request->input('charge_no');
  $item->charge = $request->input('charge');
  $item->receive_date = $request->input('receive_date');
  $item->receive_no = $request->input('receive_no');
  $item->receive = $request->input('receive');
  $item->repno = $request->input('repno');  
  $item->status = $request->input('status'); 
  $item->save();

  return redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย');
}

###############################################################################
public function _1102050102_802(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT d.*,IFNULL(SUM(s.compensate_treatment),0)+IFNULL(SUM(s1.compensate_kidney),0) AS receive_total ,
    SUM(s1.compensate_kidney) AS receive_kidney,SUM(s.compensate_treatment) AS receive_eclaim,GROUP_CONCAT(DISTINCT s.repno) AS repno
    FROM debtor_1102050102_802 d    
    LEFT JOIN htp_report.stm_lgo s ON s.an=d.an
    LEFT JOIN htp_report.stm_lgo_kidney s1 ON s1.cid=d.cid AND DATE(s1.datetimeadm)  BETWEEN d.regdate AND d.dchdate
    WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY d.an');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS kidney,
		IFNULL(SUM(o2.sum_price),0) AS other,a.income-a.rcpt_money-IFNULL(SUM(o2.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode = "3003375"
		LEFT JOIN opitemrece o2 ON o2.an = i.an AND o2.icode 
			IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (debtor_code = "1102050101.109"))		
    WHERE i.confirm_discharge = "Y" AND p1.pttype LIKE "L%"
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050102_802) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate) AS a WHERE debtor <> "0"'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050102_802',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050102_802_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050102_802_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050102_802_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT dchdate AS vstdate,COUNT(DISTINCT an) AS anvn,
    SUM(debtor) AS debtor,SUM(receive_total) AS receive_total
		FROM (SELECT d.*,s.compensate_treatment+IFNULL(SUM(s1.compensate_kidney),0) AS receive_total ,
    SUM(s1.compensate_kidney) AS receive_kidney, s.compensate_treatment AS receive_eclaim
    FROM debtor_1102050102_802 d    
    LEFT JOIN htp_report.stm_lgo s ON s.an=d.an
    LEFT JOIN htp_report.stm_lgo_kidney s1 ON s1.cid=d.cid AND DATE(s1.datetimeadm)  BETWEEN d.regdate AND d.dchdate
    WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY d.an ) AS a
		GROUP BY dchdate ORDER BY dchdate');

  $pdf = PDF::loadView('finance_debtor.1102050102_802_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050102_802_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS kidney,
		IFNULL(SUM(o2.sum_price),0) AS other,a.income-a.rcpt_money-IFNULL(SUM(o2.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode = "3003375"
		LEFT JOIN opitemrece o2 ON o2.an = i.an AND o2.icode 
			IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (debtor_code = "1102050101.109"))		
    WHERE i.confirm_discharge = "Y" AND p1.pttype LIKE "L%"
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050102_802) 
    AND i.an in ('.$checkbox.')
    GROUP BY i.an ORDER BY i.ward,i.dchdate) AS a WHERE debtor <> "0"');     

  foreach ($debtor as $row) {
      Debtor_1102050102_802::insert([
        'an'              => $row->an,
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'regdate'         => $row->regdate,
        'regtime'         => $row->regtime,
        'dchdate'         => $row->dchdate,
        'dchtime'         => $row->dchtime,    
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'adjrw'           => $row->adjrw,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money, 
        'kidney'          => $row->kidney, 
        'other'           => $row->other,  
        'debtor'          => $row->debtor,    
    ]);
  }

  return redirect()->route('_1102050102_802');
}

public function _1102050102_802_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050102_802::whereIn('an', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050102_802');
}

###############################################################################
public function _1102050102_804(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = Session::get('start_date');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = Session::get('end_date');}else{$end_date =$request->end_date;}

  $debtor = DB::select('
    SELECT d.*,s.receive_total,s.repno
    FROM debtor_1102050102_804 d    
    LEFT JOIN htp_report.stm_ofc s ON s.an=d.an  
    WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY d.an');

  $debtor_search = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS kidney,
		IFNULL(SUM(o2.sum_price),0) AS other,a.income-a.rcpt_money-IFNULL(SUM(o2.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an 
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode = "3003375"
		LEFT JOIN opitemrece o2 ON o2.an = i.an AND o2.icode 
			IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (debtor_code = "1102050101.109"))		
    WHERE i.confirm_discharge = "Y" AND p1.hipdata_code IN ("BKK","BMT")
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050102_804) 
    GROUP BY i.an ORDER BY i.ward,i.dchdate) AS a WHERE debtor <> "0"'); 

    $request->session()->put('start_date',$start_date);
    $request->session()->put('end_date',$end_date);
    $request->session()->put('debtor',$debtor);
    $request->session()->save();

  return view('finance_debtor.1102050102_804',compact('start_date','end_date','debtor','debtor_search'));
}

public function _1102050102_804_indiv_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');

  return view('finance_debtor.1102050102_804_indiv_excel',compact('start_date','end_date','debtor'));
}

public function _1102050102_804_daily_pdf(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::select('
    SELECT dchdate AS vstdate,COUNT(DISTINCT an) AS anvn,
    SUM(debtor) AS debtor,SUM(receive_total) AS receive_total
		FROM (SELECT d.*,s.receive_total
    FROM debtor_1102050102_804 d    
    LEFT JOIN htp_report.stm_ofc s ON s.an=d.an  
    WHERE d.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY d.an)	AS a 
		GROUP BY dchdate ORDER BY dchdate');

  $pdf = PDF::loadView('finance_debtor.1102050102_804_daily_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}

public function _1102050102_804_confirm(Request $request )
{
  set_time_limit(300);
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $request->validate([
    'checkboxes' => 'required|array', 
  ]);
  $checkboxes = $request->input('checkboxes');
  $checkbox = join(",",$checkboxes);
  
  $debtor = DB::connection('hosxp')->select('
    SELECT * FROM (SELECT w.`name` AS ward,i.regdate,i.regtime,i.dchdate,i.dchtime,i.vn,i.hn,i.an,
		CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,a.pdx,p.cid,a.age_y,p1.name AS pttype,
		ip.hospmain,p1.hipdata_code,i.adjrw,a.income,a.rcpt_money,IFNULL(SUM(o1.sum_price),0) AS kidney,
		IFNULL(SUM(o2.sum_price),0) AS other,a.income-a.rcpt_money-IFNULL(SUM(o2.sum_price),0) AS debtor
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
		LEFT JOIN opitemrece o1 ON o1.an = i.an AND o1.icode = "3003375"
		LEFT JOIN opitemrece o2 ON o2.an = i.an AND o2.icode 
			IN (SELECT icode FROM htp_report.finance_lookup_icode WHERE (debtor_code = "1102050101.109"))		
    WHERE i.confirm_discharge = "Y" AND p1.hipdata_code IN ("BKK","BMT")
    AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.an NOT IN (SELECT an FROM htp_report.debtor_1102050102_804) 
    AND i.an in ('.$checkbox.')
    GROUP BY i.an ORDER BY i.ward,i.dchdate) AS a WHERE debtor <> "0"');     

  foreach ($debtor as $row) {
      Debtor_1102050102_804::insert([
        'an'              => $row->an,
        'vn'              => $row->vn,
        'hn'              => $row->hn,
        'cid'             => $row->cid,
        'ptname'          => $row->ptname,
        'regdate'         => $row->regdate,
        'regtime'         => $row->regtime,
        'dchdate'         => $row->dchdate,
        'dchtime'         => $row->dchtime,    
        'pttype'          => $row->pttype,    
        'hospmain'        => $row->hospmain,    
        'hipdata_code'    => $row->hipdata_code,   
        'pdx'             => $row->pdx,  
        'adjrw'           => $row->adjrw,  
        'income'          => $row->income,  
        'rcpt_money'      => $row->rcpt_money, 
        'kidney'          => $row->kidney, 
        'other'           => $row->other,  
        'debtor'          => $row->debtor,    
    ]);
  }

  return redirect()->route('_1102050102_804');
}

public function _1102050102_804_delete(Request $request )
{
  $request->validate([
    'checkbox' => 'required|array', 
  ]);
  $checkbox = $request->input('checkbox');

    if (!empty($checkbox)) {
      Debtor_1102050102_804::whereIn('an', $checkbox)->whereNull('debtor_lock')->delete();
    }

  return redirect()->route('_1102050102_804');
}

#################### ลูกหนี้ค่ารักษาพยาบาล CCMS ##################################################

public function ccms_check(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}
  $debtor = DB::connection('ccms')->select('
    SELECT o.HN,o.SEQ,o.DATEOPD,o.PTTYPE,o1.HOSPMAIN,o1.HOSPSUB
    FROM op_cht o
    LEFT OUTER JOIN op_ins o1 ON o1.SEQ=o.SEQ  
    WHERE o.DATEOPD BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND o.SUB_ACCOUNT_NO IS NULL GROUP BY o.SEQ ORDER BY o.DATEOPD ');
  $debtor_ipd = DB::connection('ccms')->select('
    SELECT i.HN,i.AN,i.DATEIPD,i.PTTYPE,i1.HOSPMAIN,i1.HOSPSUB 
    FROM ip_cht i 
    LEFT JOIN ip_ins i1 ON i1.AN=i.AN
    WHERE i.DATEIPD BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.SUB_ACCOUNT_NO IS NULL ORDER BY i1.PTTYPE,i1.HOSPMAIN');
  
    return view('finance_debtor.ccms_check',compact('debtor','debtor_ipd','start_date','end_date'));
}

public function ccms_checknondeb(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}
  $debtor = DB::connection('ccms')->select('
    SELECT o.HN,o.SEQ,o.DATEOPD,o.PTTYPE,o1.HOSPMAIN,o1.HOSPSUB
    FROM op_cht o
    LEFT OUTER JOIN op_ins o1 ON o1.SEQ=o.SEQ  
    WHERE o.DATEOPD BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND o.HN NOT IN (SELECT HN FROM account_inc_total WHERE VSTDATE BETWEEN "'.$start_date.'" AND "'.$end_date.'")');
  $debtor_ipd = DB::connection('ccms')->select('
    SELECT i.HN,i.AN,i.DATEIPD,i.PTTYPE,i1.HOSPMAIN,i1.HOSPSUB 
    FROM ip_cht i 
    LEFT JOIN ip_ins i1 ON i1.AN=i.AN
    WHERE i.DATEIPD BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND i.AN NOT IN (SELECT AN FROM account_inc_total WHERE VSTDATE BETWEEN "'.$start_date.'" AND "'.$end_date.'")');
  
    return view('finance_debtor.ccms_checknondeb',compact('debtor','debtor_ipd','start_date','end_date'));
}

public function ccms_all(Request $request )
{
  
  // $budget_year = DB::connection('ccms')->table('mas_acc_health_sub')->groupBy('byear')->orderBy('byear','desc')->limit(1)->value('byear');
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}
  $debtor = DB::connection('ccms')->select('
    SELECT mm.ACC_HEALTH_MAIN_ID AS debtor_id,mm.ACC_HEALTH_MAIN_NAME,COUNT(DISTINCT IFNULL(a.VN,a.AN)) AS visit,
    SUM(a.ClaimAmount) AS claimAmount,SUM(a.INCOME) AS income,SUM(a.INCOME)-SUM(a.ClaimAmount) AS difference
    FROM(SELECT	INVNO,VN,AN,VSTDATE,DCHDATE,CID,PTNAME,PTTYPE,HOSPMAIN,AMOUNT,PAID,ClaimAmount,INCOME,ACCOUNT_NO 
    FROM account_inc_total WHERE ACCOUNT_NO LIKE "1%" AND DCHDATE BETWEEN "'.$start_date.'" AND "'.$end_date.'" ) AS a
    LEFT JOIN (SELECT m.acc_health_main_id,m1.ACC_HEALTH_MAIN_NAME,m.acc_health_sub_id,m.acc_health_sub_name,m.byear
    FROM mas_acc_health_sub m LEFT JOIN mas_acc_health_main m1 ON m1.ACC_HEALTH_MAIN_ID=m.acc_health_main_id
    GROUP BY m.acc_health_main_id) mm ON mm.acc_health_main_id=a.ACCOUNT_NO
    GROUP BY a.ACCOUNT_NO ORDER BY a.ACCOUNT_NO');
  $request->session()->put('start_date',$start_date);
  $request->session()->put('end_date',$end_date);
  $request->session()->put('debtor',$debtor);
  $request->session()->save();

  return view('finance_debtor.ccms_all',compact('debtor','start_date','end_date'));
}

public function ccms_all_pdf(Request $request )
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = Session::get('debtor');
  $pdf = PDF::loadView('finance_debtor.ccms_all_pdf', compact('start_date','end_date','debtor'))
              ->setPaper('A4', 'landscape');
  return @$pdf->stream();
}

public function ccms_all_daily_pdf(Request $request,$debtor_id )
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::connection('ccms')->select('
    SELECT mm.ACC_HEALTH_MAIN_ID AS debtor_id,mm.ACC_HEALTH_MAIN_NAME,a.DCHDATE,COUNT(DISTINCT IFNULL(a.VN,a.AN)) AS visit,
    SUM(a.ClaimAmount) AS claimAmount,SUM(a.INCOME) AS income,SUM(a.INCOME)-SUM(a.ClaimAmount) AS difference
    FROM(SELECT	OID,INVNO,VN,AN,VSTDATE,DCHDATE,CID,PTNAME,PTTYPE,HOSPMAIN,AMOUNT,PAID,ClaimAmount,INCOME,ACCOUNT_NO 
    FROM account_inc_total WHERE ACCOUNT_NO = "'.$debtor_id.'" AND DCHDATE BETWEEN "'.$start_date.'" AND "'.$end_date.'" ) AS a
    LEFT JOIN (SELECT m.acc_health_main_id,m1.ACC_HEALTH_MAIN_NAME,m.acc_health_sub_id,m.acc_health_sub_name,m.byear
    FROM mas_acc_health_sub m LEFT JOIN mas_acc_health_main m1 ON m1.ACC_HEALTH_MAIN_ID=m.acc_health_main_id
    GROUP BY m.acc_health_main_id) mm ON mm.acc_health_main_id=a.ACCOUNT_NO
    GROUP BY a.DCHDATE ORDER BY a.DCHDATE');
  foreach ($debtor as $row){
    $debtor_name = $row->ACC_HEALTH_MAIN_NAME;
  }
  $pdf = PDF::loadView('finance_debtor.ccms_all_daily_pdf', compact('start_date','end_date','debtor','debtor_id','debtor_name'))
              ->setPaper('A4', 'portrait');
  return @$pdf->stream();
}
public function ccms_all_indiv_excel(Request $request,$debtor_id )
{
  $budget_year = Session::get('budget_year');
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $debtor = DB::connection('ccms')->select('
  SELECT a.INVNO,a.VSTDATE,a.DCHDATE,a.HN,a.CID,a.PTNAME,a.PTTYPE,a.HOSPMAIN,a.DIAG_LIST,a.AMOUNT,a.PAID,
  a.CLAIM_DATE,a.STM_ID,IFNULL(a.CLAIM_ID,a.REP) AS REP,a.ClaimAmount,a.INCOME,a.ACCOUNT_NO,a.SUB_ACCOUNT_NO,
  a.SUB_ACCOUNT_NAME,a.DEBTOR_STATUS,mm.ACC_HEALTH_MAIN_NAME
  FROM(SELECT	OID,INVNO,VSTDATE,DCHDATE,HN,CID,PTNAME,PTTYPE,HOSPMAIN,DIAG_LIST,AMOUNT,PAID,CLAIM_DATE,CLAIM_ID,
  ClaimAmount,INCOME,ACCOUNT_NO,SUB_ACCOUNT_NO,SUB_ACCOUNT_NAME,STM_DATE,REP,DEBTOR_STATUS,STM_ID
  FROM account_inc_total WHERE ACCOUNT_NO = "'.$debtor_id.'" AND DCHDATE BETWEEN "'.$start_date.'" AND "'.$end_date.'") AS a
  LEFT JOIN (SELECT m.acc_health_main_id,m1.ACC_HEALTH_MAIN_NAME,m.acc_health_sub_id,m.acc_health_sub_name,m.byear
  FROM mas_acc_health_sub m LEFT JOIN mas_acc_health_main m1 ON m1.ACC_HEALTH_MAIN_ID=m.acc_health_main_id
  GROUP BY m.acc_health_main_id) mm ON mm.acc_health_main_id=a.ACCOUNT_NO
  GROUP BY a.INVNO,a.SUB_ACCOUNT_NO ORDER BY a.DCHDATE,a.HN');
  foreach ($debtor as $row){
    $debtor_name = $row->ACC_HEALTH_MAIN_NAME;
  }

  return view('finance_debtor.ccms_all_indiv_excel',compact('start_date','end_date','debtor','debtor_id','debtor_name'));
}

public function ccms_all_income(Request $request )
{    
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}
  $debtor = DB::connection('ccms')->select('
    SELECT mm.ACC_HEALTH_MAIN_ID AS debtor_id,mm.ACC_HEALTH_MAIN_NAME,COUNT(DISTINCT a.INVNO) AS visit,
    SUM(a.ClaimAmount) AS claimAmount,SUM(a.INC01) AS INC01,SUM(a.INC02) AS INC02,
    SUM(a.INC03)+SUM(a.INC04)+SUM(a.INC17) AS INC03,SUM(a.INC05) AS INC05,SUM(a.INC06) AS INC06,
    SUM(a.INC07) AS INC07,SUM(a.INC08) AS INC08,SUM(a.INC09) AS INC09,SUM(a.INC10) AS INC10,
    SUM(a.INC11) AS INC11,SUM(a.INC12) AS INC12,SUM(a.INC13) AS INC13,SUM(a.INC14) AS INC14,
    SUM(a.INC15) AS INC15,SUM(a.INC16)+SUM(a.INC18)+SUM(a.INC19) AS INC16
    FROM(SELECT	INVNO,VSTDATE,DCHDATE,CID,PTNAME,PTTYPE,HOSPMAIN,AMOUNT,PAID,ClaimAmount,INCOME,ACCOUNT_NO,
    INC01,INC02,INC03,INC04,INC05,INC06,INC07,INC08,INC09,INC10,INC11,INC12,INC13,INC14,INC15,INC16,INC17,INC18,INC19
    FROM account_inc_total WHERE ACCOUNT_NO LIKE "1%" AND DCHDATE BETWEEN "'.$start_date.'" AND "'.$end_date.'" ) AS a
    LEFT JOIN mas_acc_health_main mm ON mm.ACC_HEALTH_MAIN_ID=a.ACCOUNT_NO 
    GROUP BY a.ACCOUNT_NO ORDER BY a.ACCOUNT_NO');

  return view('finance_debtor.ccms_all_income',compact('debtor','start_date','end_date'));
}
public function ccms_1102050101_203(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  $hospmain = $request->hospmain;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}
  if($hospmain == '' || $hospmain == null)
  {$hospmain = '10703';}else{$hospmain =$request->hospmain;}
  $hospmain_name=DB::connection('ccms')->table('HOSPCODE')->where('hospcode',$hospmain)->value('name');
  $debtor = DB::connection('ccms')->select('select
    a.VSTDATE,a.HN,a.CID,a.PTNAME,a.DIAG_LIST,a.PTTYPE,a.TOTAL,"120" AS "REAL"
    FROM account_inc_total a
    WHERE a.VSTDATE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND a.SUB_ACCOUNT_NO = "1102050101.203.01" AND a.HOSPMAIN = "'.$hospmain.'"
    GROUP BY a.VN
    ORDER BY a.VSTDATE,a.HN');
  $request->session()->put('debtor',$debtor);
  $request->session()->put('hospmain_name',$hospmain_name);
  $request->session()->save();

  return view('finance_debtor.ccms_1102050101_203',compact('debtor','start_date','end_date','hospmain_name','hospmain'));
}

#################### ลูกหนี้ค่ารักษาพยาบาล HOSxP ##################################################

public function hosxp_1102050101_202(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

  $debtor = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.dchdate,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    a.age_y,CONCAT(ip.pttype," [",ip.hospmain,"]") AS pttype,ip.auth_code,i.adjrw,a.income,a.paid_money,
    rep_eclaim_detail_nhso AS rep_nhso,rep.rep_eclaim_detail_rep_no AS rep_no
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=i.vn
    LEFT JOIN patient p ON p.hn=i.hn
    WHERE i.confirm_discharge = "Y" AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND p1.hipdata_code = "UCS" 
    GROUP BY i.an ORDER BY i.ward,i.dchdate');

  return view('finance_debtor.hosxp_1102050101_202',compact('start_date','end_date','debtor'));
}
public function hosxp_1102050101_203(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  $hospmain = $request->hospmain;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}
  if($hospmain == '' || $hospmain == null)
  {$hospmain = '10703';}else{$hospmain =$request->hospmain;}
  $hospmain_name=DB::connection('hosxp')->table('hospcode')->where('hospcode',$hospmain)->value('name');
  $debtor = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,p1.`name` AS pttype,p1.pttype AS pttype1,CONCAT(vp.hospmain,SPACE(1),h.`name`) AS hospmain,v.pdx,
    v.income,v.paid_money,v.rcpt_money,IF(v.income>700,700,v.income-v.rcpt_money)  AS debtor
    FROM ovst o
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype  
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND p1.hipdata_code = "UCS" AND vp.hospmain = "'.$hospmain.'"
    AND (v.pdx LIKE "S%" OR v.pdx LIKE "T%")
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');
  $request->session()->put('debtor',$debtor);
  $request->session()->put('hospmain_name',$hospmain_name);
  $request->session()->save();

  return view('finance_debtor.hosxp_1102050101_203',compact('start_date','end_date','debtor','hospmain','hospmain_name'));
}
public function hosxp_1102050101_203_pdf()
{
  $debtor = Session::get('debtor');
  $hospmain_name = Session::get('hospmain_name');
  $pdf = PDF::loadView('finance_debtor.hosxp_1102050101_203_pdf', compact('debtor','hospmain_name'))->setPaper('A4', 'portrait');
  return @$pdf->stream();
}
public function hosxp_1102050101_203_excel()
{
  $debtor = Session::get('debtor');
  $hospmain_name = Session::get('hospmain_name');  
  return  view('finance_debtor.hosxp_1102050101_203_excel',compact('debtor','hospmain_name'));
}

public function hosxp_1102050101_303(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

  $debtor = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,p1.`name` AS pttype,CONCAT(vp.hospmain,SPACE(1),h.`name`) AS hospmain,v.pdx,
    v.income,v.paid_money,v.rcpt_money,vp.auth_code
    FROM ovst o LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND p1.pttype IN ("S1","S2","S3","S4") AND vp.hospmain NOT IN ("10703")
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_debtor.hosxp_1102050101_303',compact('start_date','end_date','debtor'));
}

public function hosxp_1102050101_307(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

  $debtor = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,p1.`name` AS pttype,CONCAT(vp.hospmain,SPACE(1),h.`name`) AS hospmain,v.pdx,
    v.income,v.paid_money,v.rcpt_money,vp.auth_code
    FROM ovst o LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND p1.pttype IN ("S6") 
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');
  $debtor_ipd = DB::connection('hosxp')->select('
    SELECT i.regdate,i.dchdate,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    a.age_y,p1.`name` AS pttype,CONCAT(ip.hospmain,SPACE(1),h.`name`) AS hospmain,a.pdx,
    a.income,a.paid_money,a.rcpt_money
    FROM ipt i 
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
    LEFT JOIN hospcode h ON h.hospcode=ip.hospmain
    WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND p1.pttype IN ("S6") 
    GROUP BY i.an ORDER BY i.an');

  return view('finance_debtor.hosxp_1102050101_307',compact('start_date','end_date','debtor','debtor_ipd'));
}

public function hosxp_1102050101_401(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

  $debtor = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,p1.`name` AS pttype,v.pdx,v.income,v.rcpt_money,os.edc_approve_list_text,
    vp.Claim_Code,vp.auth_code,oe.upload_datetime AS ecliam
    FROM ovst o 
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN ovst_seq os ON os.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o2.icode IS NULL OR o2.icode="") AND p1.hipdata_code = "OFC"
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $debtor_kidney = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,p1.`name` AS pttype,v.pdx,v.income,v.rcpt_money,os.edc_approve_list_text,
    vp.Claim_Code,vp.auth_code,oe.upload_datetime AS ecliam
    FROM ovst o 
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN ovst_seq os ON os.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o2.icode IS NOT NULL OR o2.icode<>"") AND p1.hipdata_code = "OFC"  
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_debtor.hosxp_1102050101_401',compact('start_date','end_date','debtor','debtor_kidney'));
}

public function hosxp_1102050101_801(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

  $debtor = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,p1.`name` AS pttype,v.pdx,v.income,v.rcpt_money,vp.auth_code,oe.upload_datetime AS ecliam
    FROM ovst o 
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o2.icode IS NULL OR o2.icode="") AND p1.hipdata_code = "LGO" 
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $debtor_kidney = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,p1.`name` AS pttype,v.pdx,v.income,v.rcpt_money,vp.auth_code,oe.upload_datetime AS ecliam
    FROM ovst o 
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o2.icode IS NOT NULL OR o2.icode<>"") AND p1.hipdata_code = "LGO"
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_debtor.hosxp_1102050101_801',compact('start_date','end_date','debtor','debtor_kidney'));
}

public function hosxp_1102050102_106(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

  $debtor = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    p.mobile_phone_number,v.age_y,p1.`name` AS pttype,CONCAT(vp.hospmain,SPACE(1),h.`name`) AS hospmain,
    v.pdx,v.income,v.paid_money,v.rcpt_money,r.rcpno,vp.auth_code,p2.arrear_date,p2.amount AS arrear_amount,
    r1.bill_amount AS paid_arrear,r1.rcpno AS rcpno_arrear,fd.deposit_amount,fd1.debit_amount
    FROM ovst o LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN patient_arrear p2 ON p2.vn=o.vn
    LEFT JOIN patient_finance_deposit fd ON fd.anvn = o.vn
    LEFT JOIN patient_finance_debit fd1 ON fd1.anvn = o.vn
    LEFT JOIN rcpt_print r ON r.vn = o.vn AND r.`status` ="OK" AND r.department="OPD" AND r.bill_date=o.vstdate
    LEFT JOIN rcpt_print r1 ON r1.vn = p2.vn AND r1.`status` ="OK" AND r1.department="OPD" 
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.paid_money <>"0" AND v.rcpt_money <> v.paid_money
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $debtor_paid = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    p.mobile_phone_number,v.age_y,p1.`name` AS pttype,CONCAT(vp.hospmain,SPACE(1),h.`name`) AS hospmain,
    v.pdx,v.income,v.paid_money,v.rcpt_money,r.rcpno,vp.auth_code,p2.arrear_date,p2.amount AS arrear_amount,
    r1.bill_amount AS paid_arrear,r1.rcpno AS rcpno_arrear,fd.deposit_amount,fd1.debit_amount
    FROM ovst o LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN patient_arrear p2 ON p2.vn=o.vn
    LEFT JOIN patient_finance_deposit fd ON fd.anvn = o.vn
    LEFT JOIN patient_finance_debit fd1 ON fd1.anvn = o.vn
    LEFT JOIN rcpt_print r ON r.vn = o.vn AND r.`status` ="OK" AND r.department="OPD" AND r.bill_date=o.vstdate
    LEFT JOIN rcpt_print r1 ON r1.vn = p2.vn AND r1.`status` ="OK" AND r1.department="OPD" 
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND v.paid_money <>"0" AND v.rcpt_money = v.paid_money
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_debtor.hosxp_1102050102_106',compact('start_date','end_date','debtor','debtor_paid'));
}

public function hosxp_1102050102_107(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}

  $debtor = DB::connection('hosxp')->select('
    SELECT i.dchdate,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,p.mobile_phone_number,
    a.age_y,p1.`name` AS pttype,CONCAT(ip.hospmain,SPACE(1),h.`name`) AS hospmain,a.pdx,
    a.income,a.paid_money,a.rcpt_money,r.rcpno,p2.arrear_date,p2.amount AS arrear_amount,
    r1.bill_amount AS paid_arrear,r1.rcpno AS rcpno_arrear,fd.deposit_amount,fd1.debit_amount
    FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient_arrear p2 ON p2.an=i.an
    LEFT JOIN patient_finance_deposit fd ON fd.anvn = i.an
    LEFT JOIN patient_finance_debit fd1 ON fd1.anvn = i.an
		LEFT JOIN rcpt_print r ON r.vn = i.an AND r.`status` ="OK" AND r.department="IPD" AND r.bill_date BETWEEN i.regdate AND i.dchdate
		LEFT JOIN rcpt_print r1 ON r1.vn = p2.an AND r1.`status` ="OK" AND r1.department="IPD"
    LEFT JOIN patient p ON p.hn=i.hn
    LEFT JOIN hospcode h ON h.hospcode=ip.hospmain
    WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND a.paid_money <>"0" AND a.rcpt_money <> a.paid_money
    GROUP BY i.an ORDER BY i.dchdate');

  $debtor_paid = DB::connection('hosxp')->select('
    SELECT i.dchdate,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,p.mobile_phone_number,
    a.age_y,p1.`name` AS pttype,CONCAT(ip.hospmain,SPACE(1),h.`name`) AS hospmain,a.pdx,
    a.income,a.paid_money,a.rcpt_money,r.rcpno,p2.arrear_date,p2.amount AS arrear_amount,
    r1.bill_amount AS paid_arrear,r1.rcpno AS rcpno_arrear,fd.deposit_amount,fd1.debit_amount
    FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient_arrear p2 ON p2.an=i.an
    LEFT JOIN patient_finance_deposit fd ON fd.anvn = i.an
    LEFT JOIN patient_finance_debit fd1 ON fd1.anvn = i.an
		LEFT JOIN rcpt_print r ON r.vn = i.vn AND r.`status` ="OK" AND r.department="IPD" AND r.bill_date=i.regdate
		LEFT JOIN rcpt_print r1 ON r1.vn = p2.vn AND r1.`status` ="OK" AND r1.department="IPD"
    LEFT JOIN patient p ON p.hn=i.hn
    LEFT JOIN hospcode h ON h.hospcode=ip.hospmain
    WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND a.paid_money <>"0" AND a.rcpt_money = a.paid_money
    GROUP BY i.an ORDER BY i.dchdate');

  return view('finance_debtor.hosxp_1102050102_107',compact('start_date','end_date','debtor','debtor_paid'));
}

}
