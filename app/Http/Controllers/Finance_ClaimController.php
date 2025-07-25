<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Session;

class Finance_ClaimController extends Controller
{
//Check Login
public function __construct()
{
    $this->middleware('auth');
}    

//Create index
public function index()
{
    return view('finance_claim.index');        
}

//################### Claim OFC ###################################################################################
//Create ofc_claim_opd
public function ofc_claim_opd(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("-1 days"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $claim = DB::connection('hosxp')->select('
    SELECT CONCAT(o.vstdate,SPACE(1),o.vsttime) AS vstdate,SUBSTRING(et.`name`,8) AS er,
    TIME(ed.accident_datetime) AS accident_time,o.oqueue,o.hn,
    CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,p1.pttype,v.pdx,
    IFNULL(vp.Claim_Code,os.edc_approve_list_text) AS edc_approve_list_text,
    vp.auth_code,v.income,v.rcpt_money,oe.upload_datetime AS ecliam,
    stm.receive_total,stm.repno,stm.stm_filename
    FROM ovst o 
    LEFT JOIN er_regist e ON e.vn=o.vn 
    LEFT JOIN er_pt_type et ON et.er_pt_type=e.er_pt_type 
    LEFT JOIN er_nursing_detail ed ON ed.vn=e.vn
    LEFT JOIN vn_stat v ON v.vn=o.vn		
    LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN ovst_seq os ON os.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    LEFT JOIN htp_report.finance_stm_ofc stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o2.icode IS NULL OR o2.icode="") AND p1.pttype LIKE "O%"
    AND v.income <>"0" GROUP BY o.vn ORDER BY o.vstdate,o.oqueue ');

  $claim_kidney = DB::connection('hosxp')->select('
    SELECT CONCAT(o.vstdate,SPACE(1),o.vsttime) AS vstdate,o.oqueue,o.hn,
    CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,p1.pttype,v.pdx,
    IFNULL(vp.Claim_Code,os.edc_approve_list_text) AS edc_approve_list_text,
    vp.auth_code,v.income,v.rcpt_money,oe.upload_datetime AS ecliam,
    stm.amount,stm.rid,stm.stmdoc
    FROM ovst o 
    LEFT JOIN vn_stat v ON v.vn=o.vn		
    LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN ovst_seq os ON os.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    LEFT JOIN htp_report.finance_stm_ofc_kidney stm ON stm.hn=o.hn AND DATE(stm.vstdate) = o.vstdate
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o2.icode IS NOT NULL OR o2.icode<>"") AND (p1.pttype LIKE "O%" OR p1.pttype LIKE "H%") 
    AND v.income <>"0" GROUP BY o.vn ORDER BY o.vstdate,o.oqueue ');

  return view('finance_claim.ofc_claim_opd',compact('start_date','end_date','claim','claim_kidney'));
}

//Create ofc_claim_ipd
public function ofc_claim_ipd(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("-1 days"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $claim = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.dchdate,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    a.age_y,ip.pttype,a.diag_text_list,id.icd10,idx.icd9,CONCAT(r.refer_hospcode,"[ucae=",ia.ac_ae,"]") AS refer,
    i.adjrw,a.income,a.rcpt_money,stm.adjrw AS stm_adjrw,stm.charge,stm.receive_room,stm.receive_instument,
    stm.receive_drug,stm.receive_treatment,stm.receive_car,stm.receive_other,stm.receive_total,
    CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_admit_type iat ON iat.ipt_admit_type_id=i.ipt_admit_type_id
    LEFT JOIN ipt_accident ia ON ia.an=i.an
    LEFT JOIN referout r ON r.vn=i.an
    LEFT JOIN iptdiag id ON id.an=a.an AND id.diagtype = 1
    LEFT JOIN iptoprt idx ON idx.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
    LEFT JOIN htp_report.finance_stm_ofc stm ON stm.an=i.an 
    WHERE i.confirm_discharge = "Y" AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND p1.hipdata_code = "OFC" 
    GROUP BY i.an ORDER BY i.ward,i.dchdate ');

  return view('finance_claim.ofc_claim_ipd',compact('start_date','end_date','claim'));
}

//################### Claim BKK ###################################################################################
//Create bkk_claim_opd
public function bkk_claim_opd(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("-1 days"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $claim = DB::connection('hosxp')->select('
    SELECT CONCAT(o.vstdate,SPACE(1),o.vsttime) AS vstdate,SUBSTRING(et.`name`,8) AS er,
    TIME(ed.accident_datetime) AS accident_time,o.oqueue,o.hn,
    CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,p1.pttype,v.pdx,
    IFNULL(vp.Claim_Code,os.edc_approve_list_text) AS edc_approve_list_text,
    vp.auth_code,v.income,v.rcpt_money,oe.upload_datetime AS ecliam,
    stm.receive_total,stm.repno,stm.stm_filename
    FROM ovst o 
    LEFT JOIN er_regist e ON e.vn=o.vn 
    LEFT JOIN er_pt_type et ON et.er_pt_type=e.er_pt_type
    LEFT JOIN er_nursing_detail ed ON ed.vn=e.vn
    LEFT JOIN vn_stat v ON v.vn=o.vn		
    LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN ovst_seq os ON os.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    LEFT JOIN htp_report.finance_stm_ofc stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o2.icode IS NULL OR o2.icode="") AND p1.pttype LIKE "B%"
    AND v.income <>"0" GROUP BY o.vn ORDER BY o.vstdate,o.oqueue ');

  $claim_kidney = DB::connection('hosxp')->select('
    SELECT CONCAT(o.vstdate,SPACE(1),o.vsttime) AS vstdate,SUBSTRING(et.`name`,8) AS er,
    TIME(ed.accident_datetime) AS accident_time,o.oqueue,o.hn,
    CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,p1.pttype,v.pdx,
    IFNULL(vp.Claim_Code,os.edc_approve_list_text) AS edc_approve_list_text,
    vp.auth_code,v.income,v.rcpt_money,oe.upload_datetime AS ecliam,
    stm.receive_total,stm.repno,stm.stm_filename
    FROM ovst o 
    LEFT JOIN er_regist e ON e.vn=o.vn 
    LEFT JOIN er_pt_type et ON et.er_pt_type=e.er_pt_type
    LEFT JOIN er_nursing_detail ed ON ed.vn=e.vn
    LEFT JOIN vn_stat v ON v.vn=o.vn		
    LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN ovst_seq os ON os.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    LEFT JOIN htp_report.finance_stm_ofc stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o2.icode IS NOT NULL OR o2.icode<>"") AND p1.hipdata_code = "BKK"
    AND v.income <>"0" GROUP BY o.vn ORDER BY o.vstdate,o.oqueue ');

  return view('finance_claim.bkk_claim_opd',compact('start_date','end_date','claim','claim_kidney'));
}

//Create bkk_claim_ipd
public function bkk_claim_ipd(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("-1 days"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $claim = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.dchdate,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    a.age_y,ip.pttype,a.diag_text_list,id.icd10,idx.icd9,CONCAT(r.refer_hospcode,"[ucae=",ia.ac_ae,"]") AS refer,
    i.adjrw,a.income,a.rcpt_money,stm.adjrw AS stm_adjrw,stm.charge,stm.receive_room,stm.receive_instument,
    stm.receive_drug,stm.receive_treatment,stm.receive_car,stm.receive_other,stm.receive_total,
    CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_admit_type iat ON iat.ipt_admit_type_id=i.ipt_admit_type_id
    LEFT JOIN ipt_accident ia ON ia.an=i.an
    LEFT JOIN referout r ON r.vn=i.an
    LEFT JOIN iptdiag id ON id.an=a.an AND id.diagtype = 1
    LEFT JOIN iptoprt idx ON idx.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
    LEFT JOIN htp_report.finance_stm_ofc stm ON stm.an=i.an 
    WHERE i.confirm_discharge = "Y" AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND p1.hipdata_code = "BKK" 
    GROUP BY i.an ORDER BY i.ward,i.dchdate ');

  return view('finance_claim.bkk_claim_ipd',compact('start_date','end_date','claim'));
}

//################### Claim BMT ###################################################################################
//Create bmt_claim_opd 
public function bmt_claim_opd(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("-1 days"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $claim = DB::connection('hosxp')->select('
    SELECT CONCAT(o.vstdate,SPACE(1),o.vsttime) AS vstdate,SUBSTRING(et.`name`,8) AS er,
    TIME(ed.accident_datetime) AS accident_time,o.oqueue,o.hn,
    CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,p1.pttype,v.pdx,
    IFNULL(vp.Claim_Code,os.edc_approve_list_text) AS edc_approve_list_text,
    vp.auth_code,v.income,v.rcpt_money,oe.upload_datetime AS ecliam,
    stm.receive_total,stm.repno,stm.stm_filename
    FROM ovst o 
    LEFT JOIN er_regist e ON e.vn=o.vn 
    LEFT JOIN er_pt_type et ON et.er_pt_type=e.er_pt_type
    LEFT JOIN er_nursing_detail ed ON ed.vn=e.vn
    LEFT JOIN vn_stat v ON v.vn=o.vn		
    LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN ovst_seq os ON os.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    LEFT JOIN htp_report.finance_stm_ofc stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o2.icode IS NULL OR o2.icode="") AND p1.hipdata_code = "BMT"
    AND v.income <>"0" GROUP BY o.vn ORDER BY o.vstdate,o.oqueue ');

  $claim_kidney = DB::connection('hosxp')->select('
    SELECT CONCAT(o.vstdate,SPACE(1),o.vsttime) AS vstdate,SUBSTRING(et.`name`,8) AS er,
    TIME(ed.accident_datetime) AS accident_time,o.oqueue,o.hn,
    CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,p1.pttype,v.pdx,
    IFNULL(vp.Claim_Code,os.edc_approve_list_text) AS edc_approve_list_text,
    vp.auth_code,v.income,v.rcpt_money,oe.upload_datetime AS ecliam,
    stm.receive_total,stm.repno,stm.stm_filename
    FROM ovst o 
    LEFT JOIN er_regist e ON e.vn=o.vn 
    LEFT JOIN er_pt_type et ON et.er_pt_type=e.er_pt_type
    LEFT JOIN er_nursing_detail ed ON ed.vn=e.vn
    LEFT JOIN vn_stat v ON v.vn=o.vn		
    LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN ovst_seq os ON os.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    LEFT JOIN htp_report.finance_stm_ofc stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o2.icode IS NOT NULL OR o2.icode<>"") AND p1.hipdata_code = "BMT"
    AND v.income <>"0" GROUP BY o.vn ORDER BY o.vstdate,o.oqueue ');

  return view('finance_claim.bmt_claim_opd',compact('start_date','end_date','claim','claim_kidney'));
}

//Create bmt_claim_ipd
public function bmt_claim_ipd(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("-1 days"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $claim = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.dchdate,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    a.age_y,ip.pttype,a.diag_text_list,id.icd10,idx.icd9,CONCAT(r.refer_hospcode,"[ucae=",ia.ac_ae,"]") AS refer,
    i.adjrw,a.income,a.rcpt_money,stm.adjrw AS stm_adjrw,stm.charge,stm.receive_room,stm.receive_instument,
    stm.receive_drug,stm.receive_treatment,stm.receive_car,stm.receive_other,stm.receive_total,
    CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_admit_type iat ON iat.ipt_admit_type_id=i.ipt_admit_type_id
    LEFT JOIN ipt_accident ia ON ia.an=i.an
    LEFT JOIN referout r ON r.vn=i.an
    LEFT JOIN iptdiag id ON id.an=a.an AND id.diagtype = 1
    LEFT JOIN iptoprt idx ON idx.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
    LEFT JOIN htp_report.finance_stm_ofc stm ON stm.an=i.an 
    WHERE i.confirm_discharge = "Y" AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND p1.hipdata_code = "BMT" 
    GROUP BY i.an ORDER BY i.ward,i.dchdate ');

  return view('finance_claim.bmt_claim_ipd',compact('start_date','end_date','claim'));
}

//################### Claim LGO ###################################################################################
//Create lgo_claim_opd
public function lgo_claim_opd(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("-1 days"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $claim = DB::connection('hosxp')->select('
    SELECT CONCAT(o.vstdate,SPACE(1),o.vsttime) AS vstdate,SUBSTRING(et.`name`,8) AS er,
    TIME(ed.accident_datetime) AS accident_time,o.oqueue,o.hn,
    CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,p1.pttype,v.pdx,
    vp.auth_code,v.income,v.rcpt_money,oe.upload_datetime AS ecliam,
    SUM(stm.compensate_treatment) AS compensate_treatment,stm.repno,stm.stm_filename
    FROM ovst o 
    LEFT JOIN er_regist e ON e.vn=o.vn
    LEFT JOIN er_pt_type et ON et.er_pt_type=e.er_pt_type
    LEFT JOIN er_nursing_detail ed ON ed.vn=e.vn
    LEFT JOIN vn_stat v ON v.vn=o.vn		
    LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    LEFT JOIN htp_report.finance_stm_lgo stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o2.icode IS NULL OR o2.icode="") AND p1.pttype LIKE "L%" 
    AND v.income <>"0" GROUP BY o.vn ORDER BY o.vstdate,o.oqueue ');

  $claim_kidney = DB::connection('hosxp')->select('
    SELECT CONCAT(o.vstdate,SPACE(1),o.vsttime) AS vstdate,SUBSTRING(et.`name`,8) AS er,
    TIME(ed.accident_datetime) AS accident_time,o.oqueue,o.hn,
    CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,p1.pttype,v.pdx,
    vp.auth_code,oe.upload_datetime AS ecliam,v.income,o2.sum_price AS price_kidney,
		v.income-o2.sum_price AS price_outher,v.rcpt_money,stm1.compensate_kidney,stm.compensate_treatment,
		CONCAT(IF((stm1.repno IS NULL OR stm1.repno=""),"",stm1.repno),",",IF((stm.repno IS NULL OR stm.repno=""),"",stm.repno)) AS repno,
		CONCAT(IF((stm1.stm_filename IS NULL OR stm1.stm_filename=""),"",stm1.stm_filename),",",IF((stm.stm_filename IS NULL OR stm.stm_filename=""),"",stm.stm_filename)) AS stm_filename		
    FROM ovst o 
    LEFT JOIN er_regist e ON e.vn=o.vn
    LEFT JOIN er_pt_type et ON et.er_pt_type=e.er_pt_type
    LEFT JOIN er_nursing_detail ed ON ed.vn=e.vn
    LEFT JOIN vn_stat v ON v.vn=o.vn		
    LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain		
    LEFT JOIN htp_report.finance_stm_lgo stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    LEFT JOIN htp_report.finance_stm_lgo_kidney stm1 ON stm1.hn=o.hn AND stm1.datetimeadm = o.vstdate 
		WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o2.icode IS NOT NULL OR o2.icode<>"") AND p1.pttype LIKE "L%" 
    AND v.income <>"0" GROUP BY o.vn ORDER BY o.vstdate,o.oqueue ');

  return view('finance_claim.lgo_claim_opd',compact('start_date','end_date','claim','claim_kidney'));
}

//Create lgo_claim_ipd
public function lgo_claim_ipd(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("-1 days"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $claim = DB::connection('hosxp')->select('
    SELECT w.`name` AS ward,i.regdate,i.dchdate,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    a.age_y,ip.pttype,a.diag_text_list,id.icd10,idx.icd9,CONCAT(r.refer_hospcode,"[ucae=",ia.ac_ae,"]") AS refer,
    i.adjrw,a.income,a.rcpt_money,stm.rw AS stm_adjrw,SUM(stm.payrate) AS payrate,SUM(stm.charge_treatment) AS charge_treatment,
		SUM(stm.case_iplg) AS case_iplg,SUM(stm.case_inslg) AS case_inslg,SUM(stm.case_otlg) AS case_otlg,SUM(stm.case_pp) AS case_pp,
		SUM(stm.case_drug) AS case_drug,SUM(stm.compensate_treatment) AS compensate_treatment,GROUP_CONCAT(DISTINCT stm.repno) AS repno
    FROM ipt i 
    LEFT JOIN ward w ON w.ward=i.ward
    LEFT JOIN an_stat a ON a.an=i.an
    LEFT JOIN ipt_admit_type iat ON iat.ipt_admit_type_id=i.ipt_admit_type_id
    LEFT JOIN ipt_accident ia ON ia.an=i.an
    LEFT JOIN referout r ON r.vn=i.an
    LEFT JOIN iptdiag id ON id.an=a.an AND id.diagtype = 1
    LEFT JOIN iptoprt idx ON idx.an=i.an
    LEFT JOIN ipt_pttype ip ON ip.an=i.an
    LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
    LEFT JOIN patient p ON p.hn=i.hn
    LEFT JOIN htp_report.finance_stm_lgo stm ON stm.an=i.an 
    WHERE i.confirm_discharge = "Y" AND i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND p1.hipdata_code = "LGO" 
    GROUP BY i.an ORDER BY i.ward,i.dchdate ');

  return view('finance_claim.lgo_claim_ipd',compact('start_date','end_date','claim'));
}

//################### Claim SSS ###################################################################################
//Create sss_claim_kidney ฟอกไต
public function sss_claim_kidney(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("-1 days"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $claim_kidney = DB::connection('hosxp')->select('
    SELECT CONCAT(o.vstdate,SPACE(1),o.vsttime) AS vstdate,o.oqueue,o.hn,
    CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,p1.pttype,v.pdx,
    vp.auth_code,v.income,v.rcpt_money,o2.sum_price AS price_kidney,o3.sum_price AS price_epo,
    stm.amount,stm.epopay,stm.epoadm,stm.rid,stm.stmdoc
    FROM ovst o 
    LEFT JOIN vn_stat v ON v.vn=o.vn		
    LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
		LEFT JOIN opitemrece o3 ON o3.vn=o.vn AND o3.icode IN (SELECT icode FROM drugitems_property_list WHERE drugitems_property_id = 8)
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    LEFT JOIN htp_report.finance_stm_sss_kidney stm ON stm.cid=p.cid AND DATE(stm.vstdate) = o.vstdate
    WHERE (o.an IS NULL OR o.an ="") AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o2.icode IS NOT NULL OR o2.icode<>"") AND p1.hipdata_code IN ("SSS","SSI")
    AND v.income <>"0" GROUP BY o.vn ORDER BY o.vstdate,o.oqueue ');

  return view('finance_claim.sss_claim_kidney',compact('start_date','end_date','claim_kidney'));
}

//################### Claim UCS ###################################################################################
//Create ucs_claim_ipd
public function ucs_claim_ipd(Request $request )
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

    $claim_fdh = DB::connection('hosxp')->select('
      SELECT w.`name` AS ward,i.regdate,i.dchdate,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
      a.age_y,CONCAT(ip.pttype," [",ip.hospmain,"]") AS pttype,a.diag_text_list,id.icd10,idx.icd9,
      IF((ip.auth_code IS NOT NULL OR ip.auth_code <> ""),"Y",NULL) AS auth_code,CONCAT(r.refer_hospcode,"[ucae=",ia.ac_ae,"]") AS refer,
      i.adjrw,a.income,a.rcpt_money,i.data_exp_date AS fdh,rep_eclaim_detail_nhso AS rep_nhso,rep_eclaim_detail_error_code AS rep_error,
      stm.receive_inst,stm.receive_ae_ae,stm.fund_ip_adjrw,stm.fund_ip_payrate,stm.receive_ip_compensate_pay,
      stm.receive_total,CONCAT(stm.repno,"-N.",stm.no) AS repno
      FROM ipt i 
      LEFT JOIN ward w ON w.ward=i.ward
      LEFT JOIN an_stat a ON a.an=i.an
      LEFT JOIN ipt_admit_type iat ON iat.ipt_admit_type_id=i.ipt_admit_type_id
      LEFT JOIN ipt_accident ia ON ia.an=i.an
      LEFT JOIN referout r ON r.vn=i.an
      LEFT JOIN iptdiag id ON id.an=a.an AND id.diagtype = 1
      LEFT JOIN iptoprt idx ON idx.an=i.an
      LEFT JOIN ipt_pttype ip ON ip.an=i.an
      LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
      LEFT JOIN rep_eclaim_detail rep ON rep.vn=i.vn
      LEFT JOIN patient p ON p.hn=i.hn
      LEFT JOIN htp_report.finance_stm_ucs stm ON stm.an=i.an 
      WHERE i.confirm_discharge = "Y" AND i.dchdate BETWEEN  "'.$start_date.'" AND "'.$end_date.'"
      AND p1.hipdata_code = "UCS" AND i.data_ok = "Y" 
      GROUP BY i.an ORDER BY i.ward,i.dchdate');

    $claim = DB::connection('hosxp')->select('
      SELECT w.`name` AS ward,i.regdate,i.dchdate,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
      a.age_y,CONCAT(ip.pttype," [",ip.hospmain,"]") AS pttype,a.diag_text_list,id.icd10,idx.icd9,
      IF((ip.auth_code IS NOT NULL OR ip.auth_code <> ""),"Y",NULL) AS auth_code,CONCAT(r.refer_hospcode,"[ucae=",ia.ac_ae,"]") AS refer,
      i.adjrw,a.income,a.rcpt_money,i.data_exp_date AS fdh,rep_eclaim_detail_nhso AS rep_nhso,rep_eclaim_detail_error_code AS rep_error,
      stm.receive_inst,stm.receive_ae_ae,stm.fund_ip_adjrw,stm.fund_ip_payrate,stm.receive_ip_compensate_pay,
      stm.receive_total,CONCAT(stm.repno,"-N.",stm.no) AS repno
      FROM ipt i 
      LEFT JOIN ward w ON w.ward=i.ward
      LEFT JOIN an_stat a ON a.an=i.an
      LEFT JOIN ipt_admit_type iat ON iat.ipt_admit_type_id=i.ipt_admit_type_id
      LEFT JOIN ipt_accident ia ON ia.an=i.an
      LEFT JOIN referout r ON r.vn=i.an
      LEFT JOIN iptdiag id ON id.an=a.an AND id.diagtype = 1
      LEFT JOIN iptoprt idx ON idx.an=i.an
      LEFT JOIN ipt_pttype ip ON ip.an=i.an
      LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
      LEFT JOIN rep_eclaim_detail rep ON rep.vn=i.vn
      LEFT JOIN patient p ON p.hn=i.hn
      LEFT JOIN htp_report.finance_stm_ucs stm ON stm.an=i.an 
      WHERE i.confirm_discharge = "Y" AND i.dchdate BETWEEN  "'.$start_date.'" AND "'.$end_date.'"
      AND p1.hipdata_code = "UCS" AND (i.data_ok <> "Y" OR i.data_ok IS NULL OR i.data_ok ="")
      GROUP BY i.an ORDER BY i.ward,i.dchdate');

    return view('finance_claim.ucs_claim_ipd',compact('start_date','end_date','claim_fdh','claim'));
}
//Create ucs_claim_walkin
public function ucs_claim_walkin(Request $request )
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

    $eclaim_fdh = DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
        v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,IF((e.vn IS NULL OR e.vn =""),"OPD","ER") AS dep,
        e1.`name` AS er_emergency_type,v.pdx,o1.cc,r.refer_hospcode AS refer,vp.request_funds,
        IF((o2.vn IS NOT NULL OR o2.vn <> ""),"Y",NULL) AS kidney,GROUP_CONCAT(DISTINCT n.nhso_adp_code) AS project,
        v.income,v.rcpt_money,o.finance_lock,IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,		
        oe.upload_datetime AS eclaim,oe.moph_finance_upload_datetime AS fdh,rep_eclaim_detail_nhso AS rep_nhso,
        rep_eclaim_detail_agency AS rep_agency,rep_eclaim_detail_error_code AS rep_error,rep.rep_eclaim_detail_rep_no AS rep_no
        FROM ovst o
        LEFT JOIN er_regist e ON e.vn=o.vn 
        LEFT JOIN referout r ON r.vn=o.vn
        LEFT JOIN opdscreen o1 ON o1.vn=o.vn
        LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
        LEFT JOIN opitemrece o3 ON o3.vn=o.vn AND o3.icode IN ("3003895","3003897","3003898")
        LEFT JOIN nondrugitems n ON n.icode=o3.icode
        LEFT JOIN er_emergency_type e1 ON e1.er_emergency_type=e.er_emergency_type
        LEFT JOIN vn_stat v ON v.vn=o.vn	
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
        LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
        LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
        LEFT JOIN patient p ON p.hn=o.hn
        WHERE v.income-v.rcpt_money<>"0" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND p1.hipdata_code = "UCS" AND vp.hospmain <>"10989" AND (o.an ="" OR o.an IS NULL)      
        AND o.vn NOT IN (SELECT e.vn FROM er_regist e , visit_pttype v WHERE e.vn=v.vn
        AND v.hospmain NOT IN ("10703","10985","10986","10987","10988","10989","10990") 
        AND e.er_emergency_type IN ("1","2") AND e.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'")
        AND vp.confirm_and_locked ="Y"
        GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

    $eclaim = DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
        v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,IF((e.vn IS NULL OR e.vn =""),"OPD","ER") AS dep,
        e1.`name` AS er_emergency_type,v.pdx,o1.cc,r.refer_hospcode AS refer,vp.request_funds,
        IF((o2.vn IS NOT NULL OR o2.vn <> ""),"Y",NULL) AS kidney,GROUP_CONCAT(DISTINCT n.nhso_adp_code) AS project,
        v.income,v.rcpt_money,o.finance_lock,IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,		
        oe.upload_datetime AS eclaim,oe.moph_finance_upload_datetime AS fdh,rep_eclaim_detail_nhso AS rep_nhso,
        rep_eclaim_detail_agency AS rep_agency,rep_eclaim_detail_error_code AS rep_error,rep.rep_eclaim_detail_rep_no AS rep_no
        FROM ovst o
        LEFT JOIN er_regist e ON e.vn=o.vn 
        LEFT JOIN referout r ON r.vn=o.vn
        LEFT JOIN opdscreen o1 ON o1.vn=o.vn
        LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
        LEFT JOIN opitemrece o3 ON o3.vn=o.vn AND o3.icode IN ("3003895","3003897","3003898")
        LEFT JOIN nondrugitems n ON n.icode=o3.icode
        LEFT JOIN er_emergency_type e1 ON e1.er_emergency_type=e.er_emergency_type
        LEFT JOIN vn_stat v ON v.vn=o.vn	
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
        LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
        LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
        LEFT JOIN patient p ON p.hn=o.hn
        WHERE v.income-v.rcpt_money<>"0" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND p1.hipdata_code = "UCS" AND vp.hospmain <>"10989" AND (o.an ="" OR o.an IS NULL)      
        AND o.vn NOT IN (SELECT e.vn FROM er_regist e , visit_pttype v WHERE e.vn=v.vn
        AND v.hospmain NOT IN ("10703","10985","10986","10987","10988","10989","10990") 
        AND e.er_emergency_type IN ("1","2") AND e.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'")
        AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
        GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

    $eclaim_ae_fdh = DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
        v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,IF((e.vn IS NULL OR e.vn =""),"OPD","ER") AS dep,
        e1.`name` AS er_emergency_type,v.pdx,o1.cc,r.refer_hospcode AS refer,vp.request_funds,
        IF((o2.vn IS NOT NULL OR o2.vn <> ""),"Y",NULL) AS kidney,GROUP_CONCAT(DISTINCT n.nhso_adp_code) AS project,
        v.income,v.rcpt_money,IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,		
        oe.upload_datetime AS eclaim,oe.moph_finance_upload_datetime AS fdh,rep_eclaim_detail_nhso AS rep_nhso,
        rep_eclaim_detail_agency AS rep_agency,rep_eclaim_detail_error_code AS rep_error,rep.rep_eclaim_detail_rep_no AS rep_no
        FROM ovst o
        LEFT JOIN er_regist e ON e.vn=o.vn 
        LEFT JOIN referout r ON r.vn=o.vn
        LEFT JOIN opdscreen o1 ON o1.vn=o.vn
        LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
        LEFT JOIN opitemrece o3 ON o3.vn=o.vn AND o3.icode IN ("3003895","3003897","3003898")
        LEFT JOIN nondrugitems n ON n.icode=o3.icode
        LEFT JOIN er_emergency_type e1 ON e1.er_emergency_type=e.er_emergency_type
        LEFT JOIN vn_stat v ON v.vn=o.vn	
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
        LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
        LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
        LEFT JOIN patient p ON p.hn=o.hn
        WHERE v.income-v.rcpt_money<>"0" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND p1.hipdata_code = "UCS" AND vp.hospmain <>"10989" AND (o.an ="" OR o.an IS NULL)   
        AND o.vn IN (SELECT e.vn FROM er_regist e , visit_pttype v WHERE e.vn=v.vn
        AND v.hospmain NOT IN ("10703","10985","10986","10987","10988","10989","10990") 
        AND e.er_emergency_type IN ("1","2") AND e.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'")
        AND vp.confirm_and_locked ="Y"
        GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

    $eclaim_ae = DB::connection('hosxp')->select('
        SELECT o.vstdate,o.vsttime,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
        v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,IF((e.vn IS NULL OR e.vn =""),"OPD","ER") AS dep,
        e1.`name` AS er_emergency_type,v.pdx,o1.cc,r.refer_hospcode AS refer,vp.request_funds,
        IF((o2.vn IS NOT NULL OR o2.vn <> ""),"Y",NULL) AS kidney,GROUP_CONCAT(DISTINCT n.nhso_adp_code) AS project,
        v.income,v.rcpt_money,IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,		
        oe.upload_datetime AS eclaim,oe.moph_finance_upload_datetime AS fdh,rep_eclaim_detail_nhso AS rep_nhso,
        rep_eclaim_detail_agency AS rep_agency,rep_eclaim_detail_error_code AS rep_error,rep.rep_eclaim_detail_rep_no AS rep_no
        FROM ovst o
        LEFT JOIN er_regist e ON e.vn=o.vn 
        LEFT JOIN referout r ON r.vn=o.vn
        LEFT JOIN opdscreen o1 ON o1.vn=o.vn
        LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
        LEFT JOIN opitemrece o3 ON o3.vn=o.vn AND o3.icode IN ("3003895","3003897","3003898")
        LEFT JOIN nondrugitems n ON n.icode=o3.icode
        LEFT JOIN er_emergency_type e1 ON e1.er_emergency_type=e.er_emergency_type
        LEFT JOIN vn_stat v ON v.vn=o.vn	
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
        LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
        LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
        LEFT JOIN patient p ON p.hn=o.hn
        WHERE v.income-v.rcpt_money<>"0" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND p1.hipdata_code = "UCS" AND vp.hospmain <>"10989" AND (o.an ="" OR o.an IS NULL)   
        AND o.vn IN (SELECT e.vn FROM er_regist e , visit_pttype v WHERE e.vn=v.vn
        AND v.hospmain NOT IN ("10703","10985","10986","10987","10988","10989","10990") 
        AND e.er_emergency_type IN ("1","2") AND e.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'")
        AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
        GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

    return view('finance_claim.ucs_claim_walkin',compact('start_date','end_date','eclaim_fdh','eclaim','eclaim_ae_fdh','eclaim_ae'));
}

//################### Claim  UC-OP บริการเฉพาะ CR ###################################################################################
//Create ucs_claim_kidney ฟอกไต
public function ucs_claim_kidney(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d', strtotime("-1 days"));}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $claim_kidney = DB::connection('hosxp')->select('
    SELECT IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,
    IF((vp.auth_code LIKE "EP%" OR epi.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,
    CONCAT(o.vstdate,SPACE(1),o.vsttime) AS vstdate,o.oqueue,o.hn,o.an,
    CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,p1.pttype,v.pdx,
    v.income,v.rcpt_money,o2.sum_price AS price_kidney,o3.sum_price AS price_epo,
		SUM(stm.receive_total) AS receive_total ,stm.repno,stm.stm_filename
    FROM ovst o 
    LEFT JOIN vn_stat v ON v.vn=o.vn		
    LEFT JOIN opitemrece o2 ON o2.vn=o.vn AND o2.icode IN ("3003375")
		LEFT JOIN opitemrece o3 ON o3.vn=o.vn AND o3.icode IN (SELECT icode FROM drugitems_property_list WHERE drugitems_property_id = 8)
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN hospcode h ON h.hospcode=vp.hospmain
    LEFT JOIN htp_report.nhso_endpoint_indiv epi ON epi.cid=v.cid AND DATE(epi.serviceDateTime)=o.vstdate
    LEFT JOIN htp_report.finance_stm_ucs_kidney stm ON stm.cid=p.cid AND DATE(stm.datetimeadm) = o.vstdate
    WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o2.icode IS NOT NULL OR o2.icode<>"") AND p1.hipdata_code IN ("UCS")
    AND v.income <>"0" GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_claim_kidney',compact('start_date','end_date','claim_kidney'));
}

//Create ucs_claim_opanywhere
public function ucs_claim_opanywhere(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $search = DB::connection('hosxp')->select('
    SELECT IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,
    IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%" OR epi.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,
    vp.request_funds,vp.confirm_and_locked,o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,
    CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,IF((e.vn IS NULL OR e.vn =""),"OPD","ER") AS dep,n_proj.nhso_adp_code AS project,
    e1.`name` AS er_emergency_type,v.pdx,GROUP_CONCAT(DISTINCT ov.icd10) AS icd9,o1.cc,v.income,SUM(DISTINCT refer.sum_price) AS refer,
		SUM(DISTINCT kidney.sum_price) AS kidney,v.rcpt_money,oe.moph_finance_upload_datetime AS upload_fdh,rep.rep_eclaim_detail_nhso AS rep_nhso,
		rep.rep_eclaim_detail_error_code AS rep_error,stm.receive_total,stm.repno
    FROM ovst o  
		LEFT JOIN opdscreen o1 ON o1.vn=o.vn
		LEFT JOIN er_regist e ON e.vn=o.vn 
    LEFT JOIN er_emergency_type e1 ON e1.er_emergency_type=e.er_emergency_type
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
		LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovstdiag ov ON ov.vn=o.vn AND ov.diagtype = "2"
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
		LEFT JOIN opitemrece refer ON refer.vn=o.vn AND refer.icode 
	    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("S1801","S1802"))
		LEFT JOIN opitemrece kidney ON kidney.vn=o.vn AND kidney.icode IN ("3003375")
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimStatus="E"
    LEFT JOIN htp_report.nhso_endpoint_indiv epi ON epi.cid=v.cid AND DATE(epi.serviceDateTime)=o.vstdate
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE (o.an ="" OR o.an IS NULL) AND p1.hipdata_code = "UCS" AND vp.hospmain NOT IN ("10703","10985","10986","10987","10988","10989","10990")		
    AND v.income <> "0" AND kidney.vn IS NULL AND oe.moph_finance_upload_status IS NULL 
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue'); 

  $claim_fdh = DB::connection('hosxp')->select('
     SELECT IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,
    IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%" OR epi.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,
    vp.request_funds,vp.confirm_and_locked,o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,
    CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,IF((e.vn IS NULL OR e.vn =""),"OPD","ER") AS dep,n_proj.nhso_adp_code AS project,
    e1.`name` AS er_emergency_type,v.pdx,GROUP_CONCAT(DISTINCT ov.icd10) AS icd9,o1.cc,v.income,SUM(DISTINCT refer.sum_price) AS refer,
		SUM(DISTINCT kidney.sum_price) AS kidney,v.rcpt_money,oe.moph_finance_upload_datetime AS upload_fdh,rep.rep_eclaim_detail_nhso AS rep_nhso,
		rep.rep_eclaim_detail_error_code AS rep_error,stm.receive_total,stm.repno
    FROM ovst o  
		LEFT JOIN opdscreen o1 ON o1.vn=o.vn
		LEFT JOIN er_regist e ON e.vn=o.vn 
    LEFT JOIN er_emergency_type e1 ON e1.er_emergency_type=e.er_emergency_type
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
		LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovstdiag ov ON ov.vn=o.vn AND ov.diagtype = "2"
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
		LEFT JOIN opitemrece refer ON refer.vn=o.vn AND refer.icode 
	    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("S1801","S1802"))
		LEFT JOIN opitemrece kidney ON kidney.vn=o.vn AND kidney.icode IN ("3003375")
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimStatus="E"
    LEFT JOIN htp_report.nhso_endpoint_indiv epi ON epi.cid=v.cid AND DATE(epi.serviceDateTime)=o.vstdate
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE (o.an ="" OR o.an IS NULL) AND p1.hipdata_code = "UCS" AND vp.hospmain NOT IN ("10703","10985","10986","10987","10988","10989","10990")		
    AND v.income <> "0" AND kidney.vn IS NULL AND oe.moph_finance_upload_status IS NOT NULL 
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');    

  return view('finance_claim.ucs_claim_opanywhere',compact('start_date','end_date','search','claim_fdh'));
}

//Create ucs_claim_instrument
public function ucs_claim_instrument(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $eclaim_fdh = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,stm.receive_inst,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_type_id = "2")
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_type_id = "2")
    AND (o.an ="" OR o.an IS NULL) AND vp.confirm_and_locked ="Y" 
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $eclaim = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,stm.receive_inst,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_type_id = "2")
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_type_id = "2") AND (o.an ="" OR o.an IS NULL) 
    AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_claim_instrument',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

//Create ucs_claim_telehealth
public function ucs_claim_telehealth(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $eclaim_fdh = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,IF(stm.receive_op>SUM(claim.sum_price),50,stm.receive_op) AS receive_op,
		CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("TELMED"))
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("TELMED"))
    AND (o.an ="" OR o.an IS NULL) AND vp.confirm_and_locked ="Y" 
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $eclaim = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,IF(stm.receive_op>SUM(claim.sum_price),50,stm.receive_op) AS receive_op,
		CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("TELMED"))
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("TELMED")) AND (o.an ="" OR o.an IS NULL)
    AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_claim_telehealth',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

//Create ucs_claim_rider จัดส่งยาทางไปรษณีย์
public function ucs_claim_rider(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $eclaim_fdh = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,IF(stm.receive_op>SUM(claim.sum_price),50,stm.receive_op) AS receive_op,
    CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("DRUGP"))
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("DRUGP"))
    AND (o.an ="" OR o.an IS NULL) AND vp.confirm_and_locked ="Y" 
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $eclaim = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,IF(stm.receive_op>SUM(claim.sum_price),50,stm.receive_op) AS receive_op,
    CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("DRUGP"))
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("DRUGP")) AND (o.an ="" OR o.an IS NULL)
    AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_claim_rider',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

//Create ucs_claim_palliative 
public function ucs_claim_palliative(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $eclaim_fdh = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,stm.receive_palliative,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("Cons01","Eva001","30001"))
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("Cons01","Eva001","30001"))
    AND (o.an ="" OR o.an IS NULL) AND vp.confirm_and_locked ="Y" 
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $eclaim = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,stm.receive_palliative,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("Cons01","Eva001","30001"))
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("Cons01","Eva001","30001"))
    AND (o.an ="" OR o.an IS NULL) AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_claim_palliative',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

//Create บริการในกลุ่ม T1DM/GDM/PDM 
public function ucs_claim_t1dm_gdm_pdm(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $eclaim_fdh = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT od1.icd10) AS dx,
		GROUP_CONCAT(DISTINCT od2.icd10) AS icd9,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,
    IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,stm.receive_palliative,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN ovstdiag od1 ON od1.vn=o.vn AND od1.diagtype NOT IN ("1","2")
		LEFT JOIN ovstdiag od2 ON od2.vn=o.vn AND od2.diagtype = "2"
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("80001","80002","80003","80004","80005","80006","80007","80008","80015",
			"80024","80025","80026","80027","80028"))
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("80001","80002","80003","80004","80005","80006",
			"80007","80008","80015","80024","80025","80026","80027","80028"))
    AND (o.an ="" OR o.an IS NULL) AND vp.confirm_and_locked ="Y" 
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $eclaim = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,
    CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT od1.icd10) AS dx,
		GROUP_CONCAT(DISTINCT od2.icd10) AS icd9,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,v.income,
		SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code
		,oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,rep_eclaim_detail_error_code AS rep_error,
		stm.receive_palliative,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
		LEFT JOIN ovstdiag od ON od.vn=o.vn
		LEFT JOIN ovstdiag od1 ON od1.vn=o.vn AND od1.diagtype NOT IN ("1","2")
		LEFT JOIN ovstdiag od2 ON od2.vn=o.vn AND od2.diagtype = "2"
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("80001","80002","80003","80004","80005","80006","80007","80008","80015",
			"80024","80025","80026","80027","80028"))
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("80001","80002","80003","80004","80005","80006",
			"80007","80008","80015","80024","80025","80026","80027","80028")) OR od.icd10 IN ("Z348","O244","O249","8744"))
    AND (o.an ="" OR o.an IS NULL) AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_claim_t1dm_gdm_pdm',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

//Create ucs_claim_drug_herb ยาสมุนไพร 9 รายการ
public function ucs_claim_drug_herb(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

$eclaim_fdh = DB::connection('hosxp')->select('
  SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
  v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
  v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,
  oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
  rep_eclaim_detail_error_code AS rep_error,stm.receive_hc_hc,CONCAT(stm.repno,"-N.",stm.no) AS repno
  FROM ovst o     
  LEFT JOIN patient p ON p.hn=o.hn
  LEFT JOIN vn_stat v ON v.vn=o.vn
  LEFT JOIN visit_pttype vp ON vp.vn=o.vn
  LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
  LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode IN (SELECT icode FROM drugitems_property_list WHERE drugitems_property_id="13")
  LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
  LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
  LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
  WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
  AND claim.icode IN (SELECT icode FROM drugitems_property_list WHERE drugitems_property_id="13")
  AND (o.an ="" OR o.an IS NULL) AND vp.confirm_and_locked ="Y" 
  GROUP BY o.vn ORDER BY o.vstdate,o.oqueue'); 

$eclaim = DB::connection('hosxp')->select('
  SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
  v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
  v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,
  oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
  rep_eclaim_detail_error_code AS rep_error,stm.receive_hc_hc,CONCAT(stm.repno,"-N.",stm.no) AS repno
  FROM ovst o    
  LEFT JOIN patient p ON p.hn=o.hn
  LEFT JOIN vn_stat v ON v.vn=o.vn
  LEFT JOIN visit_pttype vp ON vp.vn=o.vn
  LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
  LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode IN (SELECT icode FROM drugitems_property_list WHERE drugitems_property_id="13")
  LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
  LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
  LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
  WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
  AND claim.icode IN (SELECT icode FROM drugitems_property_list WHERE drugitems_property_id="13")
  AND (o.an ="" OR o.an IS NULL) AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
  GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_claim_drug_herb',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

//Create ucs_claim_drug_herb ยาสมุนไพร 32 รายการ
public function ucs_claim_drug_herb32(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $search = DB::connection('hosxp')->select('
    SELECT IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,
    IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%" OR epi.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,
    vp.request_funds,vp.confirm_and_locked,n_proj.nhso_adp_code AS project,o.vstdate,o.oqueue,o.hn,
    CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,
    v.pdx,GROUP_CONCAT(DISTINCT ov.icd10) AS icd9,v.income,SUM(claim.sum_price) AS sum_price,
    GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,oe.upload_datetime AS eclaim,oe.moph_finance_upload_datetime AS fdh,
    rep.rep_eclaim_detail_nhso AS rep_nhso,rep.rep_eclaim_detail_error_code AS rep_error,stm.receive_hc_hc,stm.repno
    FROM ovst o  
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
		LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovstdiag ov ON ov.vn=o.vn AND ov.diagtype = "2"
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode IN (SELECT icode FROM drugitems_property_list WHERE drugitems_property_id="12")
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimStatus="E"
    LEFT JOIN htp_report.nhso_endpoint_indiv epi ON epi.cid=v.cid AND DATE(epi.serviceDateTime)=o.vstdate
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND (o.an ="" OR o.an IS NULL)		 
		AND claim.icode IN (SELECT icode FROM drugitems_property_list WHERE drugitems_property_id="12")
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND oe.moph_finance_upload_status IS NULL AND oe.upload_datetime IS NULL
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue'); 

  $claim_fdh = DB::connection('hosxp')->select('
    SELECT IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,
    IF((ep.sourceChannel IS NOT NULL OR ep.sourceChannel <> ""),"Y",NULL) AS endpoint,vp.request_funds,vp.confirm_and_locked,
    n_proj.nhso_adp_code AS project,o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT ov.icd10) AS icd9,
    v.income,SUM(claim.sum_price) AS sum_price,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    oe.upload_datetime AS eclaim,oe.moph_finance_upload_datetime AS fdh,rep.rep_eclaim_detail_nhso AS rep_nhso,
    rep.rep_eclaim_detail_error_code AS rep_error,stm.receive_hc_hc,stm.repno
    FROM ovst o  
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
		LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN ovstdiag ov ON ov.vn=o.vn AND ov.diagtype = "2"
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode IN (SELECT icode FROM drugitems_property_list WHERE drugitems_property_id="12")
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimStatus="E"
    LEFT JOIN htp_report.nhso_endpoint_indiv epi ON epi.cid=v.cid AND DATE(epi.serviceDateTime)=o.vstdate
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND (o.an ="" OR o.an IS NULL)		 
		AND claim.icode IN (SELECT icode FROM drugitems_property_list WHERE drugitems_property_id="12")
		AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND oe.moph_finance_upload_status IS NOT NULL
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');  

  return view('finance_claim.ucs_claim_drug_herb32',compact('start_date','end_date','search','claim_fdh'));
}

//Create ucs_claim_drug_morphine
public function ucs_claim_drug_morphine(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $eclaim_fdh = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,stm.receive_hc_drug,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN ("1000201","1000202","1000203","1500052","1510002","1510021","1530020")
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN ("1000201","1000202","1000203","1500052","1510002","1510021","1530020")
    AND (o.an ="" OR o.an IS NULL)  AND vp.confirm_and_locked ="Y" 
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $eclaim = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,stm.receive_hc_drug,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN ("1000201","1000202","1000203","1500052","1510002","1510021","1530020")
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN ("1000201","1000202","1000203","1500052","1510002","1510021","1530020") AND (o.an ="" OR o.an IS NULL)  
    AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_claim_drug_morphine',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

//Create ucs_claim_drug_clopidogrel
public function ucs_claim_drug_clopidogrel(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $eclaim_fdh = DB::connection('hosxp')->select('
  SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
  v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
  v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,
  oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
  rep_eclaim_detail_error_code AS rep_error,stm.receive_hc_drug,CONCAT(stm.repno,"-N.",stm.no) AS repno
  FROM ovst o    
  LEFT JOIN patient p ON p.hn=o.hn
  LEFT JOIN vn_stat v ON v.vn=o.vn
  LEFT JOIN visit_pttype vp ON vp.vn=o.vn
  LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
  LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode IN ("1520019")
  LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
  LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
  LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
  WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
  AND claim.icode IN ("1520019") AND (o.an ="" OR o.an IS NULL)  AND vp.confirm_and_locked ="Y" 
  GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

$eclaim = DB::connection('hosxp')->select('
  SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
  v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
  v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,
  oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
  rep_eclaim_detail_error_code AS rep_error,stm.receive_hc_drug,CONCAT(stm.repno,"-N.",stm.no) AS repno
  FROM ovst o    
  LEFT JOIN patient p ON p.hn=o.hn
  LEFT JOIN vn_stat v ON v.vn=o.vn
  LEFT JOIN visit_pttype vp ON vp.vn=o.vn
  LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
  LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode IN ("1520019")
  LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
  LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
  LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
  WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
  AND claim.icode IN ("1520019") AND (o.an ="" OR o.an IS NULL)  
  AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
  GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_claim_drug_clopidogrel',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

//Create ucs_claim_drug_sk
public function ucs_claim_drug_sk(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $eclaim_fdh = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,IFNULL(n_proj.nhso_adp_code,stm.projcode) AS project,vp.auth_code,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,stm.receive_dmis_drug,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode IN ("1580011")
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN ("1580011") AND (o.an ="" OR o.an IS NULL)  
    AND vp.confirm_and_locked ="Y" 
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $eclaim = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,IFNULL(n_proj.nhso_adp_code,stm.projcode) AS project,vp.auth_code,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,stm.receive_dmis_drug,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode IN ("1580011")
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "UCS" AND vp.hospmain ="10989" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN ("1580011") AND (o.an ="" OR o.an IS NULL)  
    AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_claim_drug_sk',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

//################# Claim PP FeeSchedule #########################################################################
public function ucs_ppfs_2(Request $request ) 
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $search = DB::connection('hosxp')->select('
    SELECT IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,a.anc_service_number,
    IF((ep.sourceChannel IS NOT NULL OR ep.sourceChannel <> ""),"Y",NULL) AS endpoint,vp.request_funds,vp.confirm_and_locked,
    n_proj.nhso_adp_code AS project,o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT ov.icd10) AS icd9,
    v.income,SUM(claim.sum_price) AS sum_price,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    oe.upload_datetime AS eclaim,oe.moph_finance_upload_datetime AS fdh
    FROM ovst o    
    LEFT JOIN person_anc_service a ON a.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN ovstdiag ov ON ov.vn=o.vn AND ov.icd10 IN ("8878","2330011","2387010","2277310","2277320","2287310","2287320")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("30008","30009","30010","30011","30012","30013","52612"))
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimStatus="E"
    WHERE (o.an ="" OR o.an IS NULL) AND vp.pttype <> "10" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("30008","30009","30010","30011","30012","30013","52612")) OR
    v.pdx IN ("Z340","Z348","Z350","Z359") OR a.vn IS NOT NULL OR a.vn <>"" OR ov.icd10 IN ("8878","2330011","2387010","2277310","2277320","2287310","2287320"))
    AND oe.moph_finance_upload_status IS NULL AND oe.upload_datetime IS NULL
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $claim_fdh = DB::connection('hosxp')->select('
    SELECT IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,a.anc_service_number,
    IF((ep.sourceChannel IS NOT NULL OR ep.sourceChannel <> ""),"Y",NULL) AS endpoint,vp.request_funds,vp.confirm_and_locked,
    n_proj.nhso_adp_code AS project,o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT ov.icd10) AS icd9,
    v.income,SUM(claim.sum_price) AS sum_price,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    oe.upload_datetime AS eclaim,oe.moph_finance_upload_datetime AS fdh,rep.rep_eclaim_detail_nhso AS rep_nhso,
    rep.rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,stm.repno
    FROM ovst o    
    LEFT JOIN person_anc_service a ON a.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN ovstdiag ov ON ov.vn=o.vn AND ov.icd10 IN ("8878","2330011","2387010","2277310","2277320","2287310","2287320")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("30008","30009","30010","30011","30012","30013","52612"))
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimStatus="E"
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE (o.an ="" OR o.an IS NULL) AND vp.pttype <> "10" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("30008","30009","30010","30011","30012","30013","52612")) OR
    v.pdx IN ("Z340","Z348","Z350","Z359") OR a.vn IS NOT NULL OR a.vn <>"" OR ov.icd10 IN ("8878","2330011","2387010","2277310","2277320","2287310","2287320"))
    AND oe.moph_finance_upload_status IS NOT NULL
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $claim_eclaim = DB::connection('hosxp')->select('
    SELECT IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,a.anc_service_number,
    IF((ep.sourceChannel IS NOT NULL OR ep.sourceChannel <> ""),"Y",NULL) AS endpoint,vp.request_funds,vp.confirm_and_locked,
    n_proj.nhso_adp_code AS project,o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT ov.icd10) AS icd9,
    v.income,SUM(claim.sum_price) AS sum_price,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    oe.upload_datetime AS eclaim,oe.moph_finance_upload_datetime AS fdh,rep.rep_eclaim_detail_nhso AS rep_nhso,
    rep.rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,stm.repno
    FROM ovst o    
    LEFT JOIN person_anc_service a ON a.vn=o.vn
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN ovstdiag ov ON ov.vn=o.vn AND ov.icd10 IN ("8878","2330011","2387010","2277310","2277320","2287310","2287320")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("30008","30009","30010","30011","30012","30013","52612"))
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN htp_report.nhso_endpoint ep ON ep.personalId=v.cid AND DATE(ep.claimDate)=o.vstdate AND ep.claimStatus="E"
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE (o.an ="" OR o.an IS NULL) AND vp.pttype <> "10" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("30008","30009","30010","30011","30012","30013","52612")) OR
    v.pdx IN ("Z340","Z348","Z350","Z359") OR a.vn IS NOT NULL OR a.vn <>"" OR ov.icd10 IN ("8878","2330011","2387010","2277310","2277320","2287310","2287320"))
    AND oe.upload_datetime IS NOT NULL
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_ppfs_2',compact('start_date','end_date','search','claim_fdh','claim_eclaim'));
}

public function ucs_ppfs_7(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $eclaim_fdh = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,
    IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,
    oe.upload_datetime AS eclaim,oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("30015","30016")
			UNION SELECT icode FROM drugitems WHERE icode IN ("1600050"))
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("30015","30016")
		UNION SELECT icode FROM drugitems WHERE icode IN ("1600050")) 
    AND vp.confirm_and_locked ="Y" 
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $eclaim = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,
    IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,oe.upload_datetime AS eclaim,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("30015","30016")
			UNION SELECT icode FROM drugitems WHERE icode IN ("1600050"))
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("30015","30016")
		UNION SELECT icode FROM drugitems WHERE icode IN ("1600050")) OR v.pdx IN ("Z390","Z391","Z392"))
    AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
    AND vp.pttype <> "10"
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_ppfs_7',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

public function ucs_ppfs_8(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

$eclaim_fdh = DB::connection('hosxp')->select('
  SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
  v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT ov.icd10) AS icd10_claim,
	lo.lab_items_name_ref,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,v.income,SUM(claim.sum_price) AS sum_price,
	n_proj.nhso_adp_code AS project,IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,
	oe.upload_datetime AS eclaim,oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
  rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,stm.repno
  FROM ovst o    
  LEFT JOIN patient p ON p.hn=o.hn
  LEFT JOIN vn_stat v ON v.vn=o.vn
	LEFT JOIN ovstdiag ov ON ov.vn=o.vn AND ov.icd10 IN ("Z320","Z321")
  LEFT JOIN visit_pttype vp ON vp.vn=o.vn
  LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
  LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("30014"))
  LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
	LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
	LEFT JOIN lab_head lh ON lh.vn=o.vn
	LEFT JOIN lab_order lo ON lo.lab_order_number=lh.lab_order_number AND lo.lab_items_code="444"
  LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
  WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
  AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("30014")) 
  AND vp.confirm_and_locked ="Y" 
  GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

$eclaim = DB::connection('hosxp')->select('
  SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
  v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT ov.icd10) AS icd10_claim,
	lo.lab_items_name_ref,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,v.income,SUM(claim.sum_price) AS sum_price,
	n_proj.nhso_adp_code AS project,IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,
	oe.upload_datetime AS eclaim,oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
  rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,stm.repno
  FROM ovst o    
  LEFT JOIN patient p ON p.hn=o.hn
  LEFT JOIN vn_stat v ON v.vn=o.vn
	LEFT JOIN ovstdiag ov ON ov.vn=o.vn AND ov.icd10 IN ("Z320","Z321")
  LEFT JOIN visit_pttype vp ON vp.vn=o.vn
  LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
  LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("30014"))
  LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
	LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
	LEFT JOIN lab_head lh ON lh.vn=o.vn
	LEFT JOIN lab_order lo ON lo.lab_order_number=lh.lab_order_number AND lo.lab_items_code="444"
  LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
  WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
  AND (claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("30014","31101")) OR v.pdx IN ("Z320","Z321") OR lo.lab_items_code="444")
  AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
  AND vp.pttype <> "10"
  GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_ppfs_8',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

public function ucs_ppfs_9(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

$eclaim_fdh = DB::connection('hosxp')->select('
  SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
  v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
  v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,
  IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,oe.upload_datetime AS eclaim,
  oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
  rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,CONCAT(stm.repno,"-N.",stm.no) AS repno
  FROM ovst o    
  LEFT JOIN patient p ON p.hn=o.hn
  LEFT JOIN vn_stat v ON v.vn=o.vn
  LEFT JOIN visit_pttype vp ON vp.vn=o.vn
  LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
  LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("FP003_3","FP003_4","FP001","FP002")
		UNION SELECT icode FROM drugitems WHERE nhso_adp_code IN ("FP003_1","FP003_2"))
  LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
  LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
  LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
  WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
  AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("FP003_3","FP003_4","FP001","FP002")
	UNION SELECT icode FROM drugitems WHERE nhso_adp_code IN ("FP003_1","FP003_2")) 
  AND vp.confirm_and_locked ="Y" 
  GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

$eclaim = DB::connection('hosxp')->select('
  SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
  v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
  v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,
  IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,oe.upload_datetime AS eclaim,
  oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
  rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,CONCAT(stm.repno,"-N.",stm.no) AS repno
  FROM ovst o    
  LEFT JOIN patient p ON p.hn=o.hn
  LEFT JOIN vn_stat v ON v.vn=o.vn
  LEFT JOIN visit_pttype vp ON vp.vn=o.vn
  LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
  LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("FP003_3","FP003_4","FP001","FP002")
		UNION SELECT icode FROM drugitems WHERE nhso_adp_code IN ("FP003_1","FP003_2"))
  LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
  LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
  LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
  WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
  AND (claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("FP003_3","FP003_4","FP001","FP002")
	UNION SELECT icode FROM drugitems WHERE nhso_adp_code IN ("FP003_1","FP003_2")) OR v.pdx IN ("Z304","Z392","G431","Z308"))
  AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
  AND vp.pttype <> "10"
  GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_ppfs_9',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

public function ucs_ppfs_14(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

$eclaim_fdh = DB::connection('hosxp')->select('
  SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,
  CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT ov.icd10) AS icd10_claim,
	GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,v.income,SUM(DISTINCT claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,
	vp.auth_code,oe.upload_datetime AS eclaim,oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
  rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,stm.repno
  FROM ovst o    
  LEFT JOIN patient p ON p.hn=o.hn
  LEFT JOIN vn_stat v ON v.vn=o.vn
	LEFT JOIN ovstdiag ov ON ov.vn=o.vn AND ov.icd10 IN ("Z131","Z133","Z136")
  LEFT JOIN visit_pttype vp ON vp.vn=o.vn
  LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
  LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("12003","12004"))
  LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
  LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
  LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
  WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"	
  AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("12003","12004")) 
  AND v.age_y BETWEEN "35" AND "70" AND vp.confirm_and_locked ="Y" 
  GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

$eclaim = DB::connection('hosxp')->select('
 SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,
  CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT ov.icd10) AS icd10_claim,
	GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,v.income,SUM(DISTINCT claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,
	vp.auth_code,oe.upload_datetime AS eclaim,oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
  rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,stm.repno
  FROM ovst o    
  LEFT JOIN patient p ON p.hn=o.hn
  LEFT JOIN vn_stat v ON v.vn=o.vn
	LEFT JOIN ovstdiag ov ON ov.vn=o.vn AND ov.icd10 IN ("Z131","Z133","Z136")
  LEFT JOIN visit_pttype vp ON vp.vn=o.vn
  LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
  LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("12003","12004"))
  LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
  LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
  LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
  WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"	
  AND (claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("12003","12004")) OR ov.icd10 IN ("Z131","Z133","Z136"))
  AND v.age_y BETWEEN "35" AND "70" AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
  AND vp.pttype <> "10"
  GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

$request->session()->put('start_date',$start_date);
$request->session()->put('end_date',$end_date);
$request->session()->save();

  return view('finance_claim.ucs_ppfs_14',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

public function ucs_ppfs_14_excel(Request $request)
{
  $start_date = Session::get('start_date');
  $end_date = Session::get('end_date');
  $eclaim_fdh = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,
  CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT ov.icd10) AS icd10_claim,
	GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,v.income,SUM(DISTINCT claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,
	vp.auth_code,oe.upload_datetime AS eclaim,oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
  rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,stm.repno
  FROM ovst o    
  LEFT JOIN patient p ON p.hn=o.hn
  LEFT JOIN vn_stat v ON v.vn=o.vn
	LEFT JOIN ovstdiag ov ON ov.vn=o.vn AND ov.icd10 IN ("Z131","Z133","Z136")
  LEFT JOIN visit_pttype vp ON vp.vn=o.vn
  LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
  LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("12003","12004"))
  LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
  LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
  LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
  WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"	
  AND (claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("12003","12004")) OR ov.icd10 IN ("Z131","Z133","Z136"))
  AND v.age_y BETWEEN "35" AND "70" AND vp.pttype <> "10"
  GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_ppfs_14_excel',compact('start_date','end_date','eclaim_fdh'));
}

public function ucs_ppfs_17(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $eclaim_fdh = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,
    CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT ov.icd10) AS icd10_claim,
		n_lab.`name` AS lab,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,v.income,SUM(claim.sum_price) AS sum_price,
		n_proj.nhso_adp_code AS project,oe.upload_datetime AS eclaim,IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,
		oe.moph_finance_upload_datetime AS fdh,vp.confirm_and_locked,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
		rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,stm.repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
		LEFT JOIN ovstdiag ov ON ov.vn=o.vn AND ov.icd10 IN ("Z138")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code ="13001")
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
		LEFT JOIN opitemrece lab ON lab.vn = o.vn AND lab.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code ="30101")
		LEFT JOIN nondrugitems n_lab ON n_lab.icode=lab.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("13001"))
    AND vp.confirm_and_locked ="Y"
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $eclaim = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,
    CONCAT(vp.pttype," [",IFNULL(vp.hospmain,""),"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT ov.icd10) AS icd10_claim,
		n_lab.`name` AS lab,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,v.income,SUM(claim.sum_price) AS sum_price,
		n_proj.nhso_adp_code AS project,oe.upload_datetime AS eclaim,IF((vp.auth_code <>"" OR vp.auth_code IS NOT NULL),"Y","") AS auth_code,
		oe.moph_finance_upload_datetime AS fdh,vp.confirm_and_locked,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
		rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,stm.repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
		LEFT JOIN ovstdiag ov ON ov.vn=o.vn AND ov.icd10 IN ("Z138")
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code ="13001")
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
		LEFT JOIN opitemrece lab ON lab.vn = o.vn AND lab.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code ="30101")
		LEFT JOIN nondrugitems n_lab ON n_lab.icode=lab.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("13001")) OR ov.icd10 = "Z138" 
		OR lab.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code ="30101"))
		AND v.age_y BETWEEN "13" AND "24" AND v.sex = "2" AND v.pdx NOT IN ("D500","D501","D508","D509","D550","D551","D552","D553","D558","D559",
		"D560","D561","D562","D563","D564","D568","D569","D570","D571","D5752","D573","D578","D580","D581","D582","D588","D589","D590","D591",
		"D592","D593","D594","D595","D596","D598","D599","D600","D601","D608","D609","D610","D611","D612","D613","D618","D619","D62","D640",
		"D641","D642","D643","D644","D648","D649")
    AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
    AND vp.pttype <> "10"
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_ppfs_17',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

public function ucs_ppfs_18(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $eclaim_fdh = DB::connection('hosxp')->select('
  SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
  v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
  v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,oe.upload_datetime AS eclaim,
  oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
  rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,CONCAT(stm.repno,"-N.",stm.no) AS repno
  FROM ovst o    
  LEFT JOIN patient p ON p.hn=o.hn
  LEFT JOIN vn_stat v ON v.vn=o.vn
  LEFT JOIN visit_pttype vp ON vp.vn=o.vn
  LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
  LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("14001"))
  LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
  LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
  LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
  AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("14001"))
  AND (o.an ="" OR o.an IS NULL) AND vp.confirm_and_locked ="Y" 
  GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

$eclaim = DB::connection('hosxp')->select('
  SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
  v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
  v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,oe.upload_datetime AS eclaim,
  oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
  rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,CONCAT(stm.repno,"-N.",stm.no) AS repno
  FROM ovst o    
  LEFT JOIN patient p ON p.hn=o.hn
  LEFT JOIN vn_stat v ON v.vn=o.vn
  LEFT JOIN visit_pttype vp ON vp.vn=o.vn
  LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
  LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("14001"))
  LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
  LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
  LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
  AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("14001"))
  AND (o.an ="" OR o.an IS NULL) AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
  AND vp.pttype <> "10"
  GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_ppfs_18',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

public function ucs_ppfs_20(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $eclaim_fdh = DB::connection('hosxp')->select('
  SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
  v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
  v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,oe.upload_datetime AS eclaim,
  oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
  rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,CONCAT(stm.repno,"-N.",stm.no) AS repno
  FROM ovst o    
  LEFT JOIN patient p ON p.hn=o.hn
  LEFT JOIN vn_stat v ON v.vn=o.vn
  LEFT JOIN visit_pttype vp ON vp.vn=o.vn
  LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
  LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("15001"))
  LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
  LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
  LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
  AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("15001"))
  AND (o.an ="" OR o.an IS NULL) AND vp.confirm_and_locked ="Y" 
  GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

$eclaim = DB::connection('hosxp')->select('
  SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
  v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
  v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,oe.upload_datetime AS eclaim,
  oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
  rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,CONCAT(stm.repno,"-N.",stm.no) AS repno
  FROM ovst o    
  LEFT JOIN patient p ON p.hn=o.hn
  LEFT JOIN vn_stat v ON v.vn=o.vn
  LEFT JOIN visit_pttype vp ON vp.vn=o.vn
  LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
  LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("15001"))
  LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
  LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
    IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
  LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
  LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
  AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("15001"))
  AND (o.an ="" OR o.an IS NULL) AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
  AND vp.pttype <> "10"
  GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_ppfs_20',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

public function ucs_ppfs_21(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $eclaim_fdh = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,oe.upload_datetime AS eclaim,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("90005"))
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("90005"))
    AND (o.an ="" OR o.an IS NULL) AND vp.confirm_and_locked ="Y" 
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $eclaim = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
    v.age_y,CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,GROUP_CONCAT(DISTINCT n_claim.`name`) AS nondrug,
    v.income,SUM(claim.sum_price) AS sum_price,n_proj.nhso_adp_code AS project,vp.auth_code,oe.upload_datetime AS eclaim,
    oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
    rep_eclaim_detail_error_code AS rep_error,stm.receive_pp,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece claim ON claim.vn = o.vn AND claim.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("90005"))
    LEFT JOIN s_drugitems n_claim ON n_claim.icode=claim.icode
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND claim.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("90005"))
    AND (o.an ="" OR o.an IS NULL) AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
    AND vp.pttype <> "10"
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.ucs_ppfs_21',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

//################### Claim STP ###################################################################################
//Create stp_claim_opd
public function stp_claim_opd(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $eclaim_fdh = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,
    CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,vp.auth_code,n_proj.nhso_adp_code AS project,
		v.income,v.rcpt_money,oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
		rep_eclaim_detail_error_code AS rep_error,stm.receive_total,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "STP" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o.an ="" OR o.an IS NULL) AND vp.confirm_and_locked ="Y" 
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  $eclaim = DB::connection('hosxp')->select('
    SELECT o.vstdate,o.oqueue,o.hn,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,v.age_y,
    CONCAT(vp.pttype," [",vp.hospmain,"]") AS pttype,v.pdx,vp.auth_code,n_proj.nhso_adp_code AS project,
		v.income,v.rcpt_money,oe.moph_finance_upload_datetime AS fdh,vp.request_funds,rep_eclaim_detail_nhso AS rep_nhso,
		rep_eclaim_detail_error_code AS rep_error,stm.receive_total,CONCAT(stm.repno,"-N.",stm.no) AS repno
    FROM ovst o    
    LEFT JOIN patient p ON p.hn=o.hn
    LEFT JOIN vn_stat v ON v.vn=o.vn
    LEFT JOIN visit_pttype vp ON vp.vn=o.vn
    LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
    LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
      IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
    LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
    LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
    LEFT JOIN htp_report.finance_stm_ucs stm ON stm.hn=o.hn AND DATE(stm.datetimeadm) = o.vstdate AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
    WHERE p1.hipdata_code = "STP" AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
    AND (o.an ="" OR o.an IS NULL) AND (vp.confirm_and_locked IS NULL OR vp.confirm_and_locked ="" OR vp.confirm_and_locked <>"Y")
    GROUP BY o.vn ORDER BY o.vstdate,o.oqueue');

  return view('finance_claim.stp_claim_opd',compact('start_date','end_date','eclaim_fdh','eclaim'));
}

//Create stp_claim_ipd
public function stp_claim_ipd(Request $request )
{
  $start_date = $request->start_date;
  $end_date = $request->end_date;
  if($start_date == '' || $end_date == null)
  {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
  if($end_date == '' || $end_date == null)
  {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

  $eclaim_fdh = DB::connection('hosxp')->select('
  SELECT w.`name` AS ward,i.regdate,i.dchdate,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
  a.age_y,CONCAT(ip.pttype," [",ip.hospmain,"]") AS pttype,a.diag_text_list,id.icd10,idx.icd9,
  IF((ip.auth_code IS NOT NULL OR ip.auth_code <> ""),"Y",NULL) AS auth_code,CONCAT(r.refer_hospcode,"[ucae=",ia.ac_ae,"]") AS refer,
  i.adjrw,a.income,a.rcpt_money,i.data_exp_date AS fdh,rep_eclaim_detail_nhso AS rep_nhso,rep_eclaim_detail_error_code AS rep_error,
  stm.receive_inst,stm.receive_ae_ae,stm.fund_ip_adjrw,stm.fund_ip_payrate,stm.receive_ip_compensate_pay,
  stm.receive_total,CONCAT(stm.repno,"-N.",stm.no) AS repno
  FROM ipt i 
  LEFT JOIN ward w ON w.ward=i.ward
  LEFT JOIN an_stat a ON a.an=i.an
  LEFT JOIN ipt_admit_type iat ON iat.ipt_admit_type_id=i.ipt_admit_type_id
  LEFT JOIN ipt_accident ia ON ia.an=i.an
  LEFT JOIN referout r ON r.vn=i.an
  LEFT JOIN iptdiag id ON id.an=a.an AND id.diagtype = 1
  LEFT JOIN iptoprt idx ON idx.an=i.an
  LEFT JOIN ipt_pttype ip ON ip.an=i.an
  LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=i.vn
  LEFT JOIN patient p ON p.hn=i.hn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.an=i.an 
  WHERE i.confirm_discharge = "Y" AND i.dchdate BETWEEN  "'.$start_date.'" AND "'.$end_date.'"
  AND p1.hipdata_code = "STP" AND i.data_ok = "Y" 
  GROUP BY i.an ORDER BY i.ward,i.dchdate');

$eclaim = DB::connection('hosxp')->select('
  SELECT w.`name` AS ward,i.regdate,i.dchdate,i.hn,i.an,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,
  a.age_y,CONCAT(ip.pttype," [",ip.hospmain,"]") AS pttype,a.diag_text_list,id.icd10,idx.icd9,
  IF((ip.auth_code IS NOT NULL OR ip.auth_code <> ""),"Y",NULL) AS auth_code,CONCAT(r.refer_hospcode,"[ucae=",ia.ac_ae,"]") AS refer,
  i.adjrw,a.income,a.rcpt_money,i.data_exp_date AS fdh,rep_eclaim_detail_nhso AS rep_nhso,rep_eclaim_detail_error_code AS rep_error,
  stm.receive_inst,stm.receive_ae_ae,stm.fund_ip_adjrw,stm.fund_ip_payrate,stm.receive_ip_compensate_pay,
  stm.receive_total,CONCAT(stm.repno,"-N.",stm.no) AS repno
  FROM ipt i 
  LEFT JOIN ward w ON w.ward=i.ward
  LEFT JOIN an_stat a ON a.an=i.an
  LEFT JOIN ipt_admit_type iat ON iat.ipt_admit_type_id=i.ipt_admit_type_id
  LEFT JOIN ipt_accident ia ON ia.an=i.an
  LEFT JOIN referout r ON r.vn=i.an
  LEFT JOIN iptdiag id ON id.an=a.an AND id.diagtype = 1
  LEFT JOIN iptoprt idx ON idx.an=i.an
  LEFT JOIN ipt_pttype ip ON ip.an=i.an
  LEFT JOIN pttype p1 ON p1.pttype=ip.pttype
  LEFT JOIN rep_eclaim_detail rep ON rep.vn=i.vn
  LEFT JOIN patient p ON p.hn=i.hn
  LEFT JOIN htp_report.finance_stm_ucs stm ON stm.an=i.an 
  WHERE i.confirm_discharge = "Y" AND i.dchdate BETWEEN  "'.$start_date.'" AND "'.$end_date.'"
  AND p1.hipdata_code = "STP" AND (i.data_ok <> "Y" OR i.data_ok IS NULL OR i.data_ok ="")
  GROUP BY i.an ORDER BY i.ward,i.dchdate');

  return view('finance_claim.stp_claim_ipd',compact('start_date','end_date','eclaim_fdh','eclaim'));
}
}
