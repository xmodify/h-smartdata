<?php

namespace App\Http\Controllers\Hrims;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClaimOpController extends Controller
{
    //Check Login
    public function __construct()
    {
        $this->middleware('auth');
    }
//----------------------------------------------------------------------------------------------------------------------------------------
    public function ucs_incup(Request $request )
    {
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

    ini_set('max_execution_time', 300); // เพิ่มเป็น 5 นาที

    $search=DB::connection('hosxp')->select('
        SELECT IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,
        vp.confirm_and_locked,vp.request_funds,o.vstdate,o.vsttime,o.oqueue,pt.hn,
        CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,p.`name` AS pttype,vp.hospmain,
        os.cc,v.pdx,GROUP_CONCAT(DISTINCT od.icd10) AS icd9,GROUP_CONCAT(DISTINCT IFNULL(n.`name`,d.`name`)) AS claim_list,
        v.income,v.paid_money,COALESCE(o2.claim_price, 0) AS claim_price,GROUP_CONCAT(DISTINCT n_proj.nhso_adp_code) AS project
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN opdscreen os ON os.vn=o.vn
        LEFT JOIN ovstdiag od ON od.vn = o.vn AND od.hn=o.hn AND od.diagtype = "2"
        LEFT JOIN vn_stat v ON v.vn = o.vn
        LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
        LEFT JOIN opitemrece o1 ON o1.vn=o.vn AND o1.icode IN (SELECT icode FROM htp_report.lookup_icode)
        LEFT JOIN nondrugitems n ON n.icode=o1.icode
        LEFT JOIN drugitems d ON d.icode=o1.icode
        LEFT JOIN (SELECT vn,SUM(sum_price) AS claim_price FROM opitemrece
	        WHERE icode IN (SELECT icode FROM htp_report.lookup_icode) GROUP BY vn) o2 ON o2.vn=o.vn
        LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
            IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
        LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
        LEFT JOIN htp_report.nhso_endpoint_indiv ep ON ep.cid=pt.cid AND DATE(ep.serviceDateTime)=o.vstdate AND ep.claimCode LIKE "EP%"
        LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
        LEFT JOIN htp_report.finance_stm_ucs stm ON stm.cid=pt.cid AND DATE(stm.datetimeadm) = o.vstdate	
	        AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
        LEFT JOIN (SELECT cid,datetimeadm AS vstdate,sum(receive_total) AS receive_total,repno
	        FROM htp_report.finance_stm_ucs_kidney GROUP BY cid,datetimeadm) stm_k ON stm_k.cid=pt.cid AND stm_k.vstdate = o.vstdate
        WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND p.hipdata_code = "UCS" AND vp.hospmain IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE hmain_ucs ="Y")
        AND o1.vn IS NOT NULL AND oe.moph_finance_upload_status IS NULL AND rep.vn IS NULL AND stm.cid IS NULL AND stm_k.cid IS NULL
        GROUP BY o.vn ORDER BY o.vstdate,o.vsttime');

    $claim=DB::connection('hosxp')->select('
        SELECT IF((vp.auth_code IS NOT NULL OR vp.auth_code <> ""),"Y",NULL) AS auth_code,
        IF((vp.auth_code LIKE "EP%" OR ep.claimCode LIKE "EP%"),"Y",NULL) AS endpoint,
        vp.confirm_and_locked,vp.request_funds,o.vstdate,o.vsttime,o.oqueue,pt.hn,
        CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,p.`name` AS pttype,vp.hospmain,
        os.cc,v.pdx,GROUP_CONCAT(DISTINCT od.icd10) AS icd9,GROUP_CONCAT(DISTINCT IFNULL(n.`name`,d.`name`)) AS claim_list,
        v.income,v.paid_money,COALESCE(o2.claim_price, 0) AS claim_price,GROUP_CONCAT(DISTINCT n_proj.nhso_adp_code) AS project,
        rep.rep_eclaim_detail_nhso AS rep_nhso,rep.rep_eclaim_detail_error_code AS rep_error,stm.receive_total,stm.repno
        FROM ovst o
        LEFT JOIN patient pt ON pt.hn=o.hn
        LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN opdscreen os ON os.vn=o.vn
        LEFT JOIN ovstdiag od ON od.vn = o.vn AND od.hn=o.hn AND od.diagtype = "2"
        LEFT JOIN vn_stat v ON v.vn = o.vn
        LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn        
        LEFT JOIN opitemrece o1 ON o1.vn=o.vn AND o1.icode IN (SELECT icode FROM htp_report.lookup_icode)
        LEFT JOIN nondrugitems n ON n.icode=o1.icode
        LEFT JOIN drugitems d ON d.icode=o1.icode
        LEFT JOIN (SELECT vn,SUM(sum_price) AS claim_price FROM opitemrece
	        WHERE icode IN (SELECT icode FROM htp_report.lookup_icode) GROUP BY vn) o2 ON o2.vn=o.vn
        LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode 
            IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
        LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode
        LEFT JOIN htp_report.nhso_endpoint_indiv ep ON ep.cid=pt.cid AND DATE(ep.serviceDateTime)=o.vstdate AND ep.claimCode LIKE "EP%"
        LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
        LEFT JOIN htp_report.finance_stm_ucs stm ON stm.cid=pt.cid AND DATE(stm.datetimeadm) = o.vstdate	
	        AND LEFT(TIME(stm.datetimeadm),5) =LEFT(o.vsttime,5)
        LEFT JOIN (SELECT cid,datetimeadm AS vstdate,sum(receive_total) AS receive_total,repno
	        FROM htp_report.finance_stm_ucs_kidney GROUP BY cid,datetimeadm) stm_k ON stm_k.cid=pt.cid AND stm_k.vstdate = o.vstdate
        WHERE (o.an ="" OR o.an IS NULL) AND o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND p.hipdata_code = "UCS" AND vp.hospmain IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE hmain_ucs ="Y")
        AND o1.vn IS NOT NULL AND (oe.moph_finance_upload_status IS NOT NULL OR rep.vn IS NOT NULL OR stm.cid IS NOT NULL OR stm_k.cid IS NOT NULL)
        GROUP BY o.vn ORDER BY o.vstdate,o.vsttime');

        return view('hrims.claim_op.ucs_incup',compact('start_date','end_date','search','claim'));
    }
}
