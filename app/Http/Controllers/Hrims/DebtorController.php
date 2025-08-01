<?php

namespace App\Http\Controllers\Hrims;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Session;
use App\Models\Finance_debtor_1102050101_103;
use App\Models\Finance_debtor_1102050101_109;
use App\Models\Finance_debtor_1102050101_201;
use App\Models\Finance_debtor_1102050101_202;
use App\Models\Finance_debtor_1102050101_203;
use App\Models\Finance_debtor_1102050101_209;
use App\Models\Finance_debtor_1102050101_216;
use App\Models\Finance_debtor_1102050101_217;
use App\Models\Finance_debtor_1102050101_301;
use App\Models\Finance_debtor_1102050101_302;
use App\Models\Finance_debtor_1102050101_303;
use App\Models\Finance_debtor_1102050101_304;
use App\Models\Finance_debtor_1102050101_307;
use App\Models\Finance_debtor_1102050101_308;
use App\Models\Finance_debtor_1102050101_309;
use App\Models\Finance_debtor_1102050101_310;
use App\Models\Finance_debtor_1102050101_401;
use App\Models\Finance_debtor_1102050101_402;
use App\Models\Finance_debtor_1102050101_501;
use App\Models\Finance_debtor_1102050101_502;
use App\Models\Finance_debtor_1102050101_703;
use App\Models\Finance_debtor_1102050101_704;
use App\Models\Finance_debtor_1102050102_106;
use App\Models\Finance_debtor_1102050102_106_tracking;
use App\Models\Finance_debtor_1102050102_107;
use App\Models\Finance_debtor_1102050102_107_tracking;
use App\Models\Finance_debtor_1102050102_108;
use App\Models\Finance_debtor_1102050102_109;
use App\Models\Finance_debtor_1102050102_602;
use App\Models\Finance_debtor_1102050102_603;
use App\Models\Finance_debtor_1102050102_801;
use App\Models\Finance_debtor_1102050102_802;
use App\Models\Finance_debtor_1102050102_803;
use App\Models\Finance_debtor_1102050102_804;

class DebtorController extends Controller
{
//Check Login---------------------------------------------------------------------
    public function __construct()
    {
        $this->middleware('auth');
    }
//index---------------------------------------------------------------------------
    public function index()
    {    
        return view('hrims.debtor.index');
    }
//_check_income---------------------------------------------------------------------------------------------------------------------------------------------------------------------------- 
    public function _check_income(Request $request )
    {
        $start_date = $request->start_date ?: date('Y-m-d', strtotime("first day of this month"));
        $end_date = $request->end_date ?: date('Y-m-d', strtotime("last day of this month"));   

        $check_income = DB::connection('hosxp')->select('
            SELECT (SELECT SUM(income) FROM vn_stat WHERE vstdate BETWEEN ? AND ?) AS vn_stat,
            (SELECT SUM(paid_money) FROM vn_stat WHERE vstdate BETWEEN ? AND ?) AS vn_stat_paid,
            (SELECT SUM(sum_price) FROM opitemrece WHERE vstdate BETWEEN ? AND ? AND (an IS NULL OR an ="")) AS opitemrece,
            (SELECT SUM(sum_price) FROM opitemrece WHERE vstdate BETWEEN ? AND ? AND (an IS NULL OR an ="") AND paidst IN ("01","03")) AS opitemrece_paid,
            IF((SELECT SUM(income) FROM vn_stat WHERE vstdate BETWEEN ? AND ?)<>(SELECT SUM(sum_price) FROM opitemrece WHERE vstdate BETWEEN ? AND ? AND (an IS NULL OR an =""))
            ,"Resync VN","Success") AS status_check',[$start_date,$end_date,$start_date,$end_date,$start_date,$end_date,$start_date,$end_date,$start_date,$end_date,$start_date,$end_date]);

        $check_income_ipd = DB::connection('hosxp')->select('
            SELECT (SELECT SUM(income) FROM an_stat WHERE dchdate BETWEEN ? AND ?) AS an_stat,
            (SELECT SUM(paid_money) FROM an_stat WHERE dchdate BETWEEN ? AND ?) AS an_stat_paid,
            (SELECT SUM(sum_price) FROM opitemrece o ,ipt i WHERE o.an = i.an AND i.dchdate BETWEEN ? AND ?) AS opitemrece,
            (SELECT SUM(sum_price) FROM opitemrece o ,ipt i WHERE o.an = i.an AND i.dchdate BETWEEN ? AND ? AND paidst IN ("01","03")) AS opitemrece_paid,
            IF((SELECT SUM(income) FROM an_stat WHERE dchdate BETWEEN ? AND ?)<>(SELECT  SUM(sum_price) FROM opitemrece o ,ipt i WHERE o.an = i.an AND i.dchdate BETWEEN ? AND ?)
            ,"Resync AN","Success") AS status_check',[$start_date,$end_date,$start_date,$end_date,$start_date,$end_date,$start_date,$end_date,$start_date,$end_date,$start_date,$end_date]);  

        return view('hrims.debtor._check_income',compact('start_date','end_date','check_income','check_income_ipd'));
    }
//_summary-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function _summary(Request $request )
        {
        $start_date = $request->start_date ?: Session::get('start_date');
        $end_date = $request->end_date ?: Session::get('end_date');

        $_1102050101_103 = DB::select('
            SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
            FROM finance_debtor_1102050101_103 
            WHERE vstdate BETWEEN ? AND ? ',[$start_date,$end_date]);
        $_1102050101_109 = DB::select('
            SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
            FROM finance_debtor_1102050101_109 
            WHERE vstdate BETWEEN ? AND ? ',[$start_date,$end_date]);
        $_1102050101_201 = DB::select('
            SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
            FROM finance_debtor_1102050101_201 
            WHERE vstdate BETWEEN ? AND ? ',[$start_date,$end_date]);
        $_1102050101_203 = DB::select('
            SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
            FROM finance_debtor_1102050101_203
            WHERE vstdate BETWEEN ? AND ? ',[$start_date,$end_date]);
        $_1102050101_209 = DB::select('
            SELECT COUNT(DISTINCT d.vn) AS anvn,SUM(d.debtor) AS debtor,IFNULL(SUM(s.receive_pp),0) AS receive
            FROM finance_debtor_1102050101_209 d 
            LEFT JOIN finance_stm_ucs s ON s.cid=d.cid AND s.vstdate = d.vstdate 
            AND LEFT(s.vsttime,5) =LEFT(d.vsttime,5) 
            WHERE d.vstdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050101_216 = DB::select('
            SELECT COUNT(DISTINCT d.vn) AS anvn,SUM(d.debtor) AS debtor,SUM(IFNULL(s.receive_total,sk.receive_total)) AS receive
            FROM finance_debtor_1102050101_216 d 
            LEFT JOIN finance_stm_ucs s ON s.cid=d.cid AND s.vstdate = d.vstdate 
            AND LEFT(s.vsttime,5) =LEFT(d.vsttime,5) 
            LEFT JOIN (SELECT cid,datetimeadm AS vstdate,sum(receive_total) AS receive_total FROM finance_stm_ucs_kidney GROUP BY cid,datetimeadm) sk ON sk.cid=d.cid AND sk.vstdate = d.vstdate 
            WHERE d.vstdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050101_301 = DB::select('
            SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
            FROM finance_debtor_1102050101_301 
            WHERE vstdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050101_303 = DB::select('
            SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
            FROM finance_debtor_1102050101_303 
            WHERE vstdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050101_307 = DB::select('
            SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
            FROM finance_debtor_1102050101_307 
            WHERE vstdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050101_309 = DB::select('
            SELECT COUNT(DISTINCT vn) AS anvn, SUM(debtor) AS debtor,IFNULL(SUM(s.amount+s.epopay+s.epoadm),0) AS receive
            FROM finance_debtor_1102050101_309 d 
            LEFT JOIN finance_stm_sss_kidney s ON s.cid=d.cid AND s.vstdate = d.vstdate 
            WHERE d.vstdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050101_401 = DB::select('
            SELECT COUNT(DISTINCT d.vn) AS anvn,SUM(d.debtor) AS debtor,SUM(IFNULL(s.receive_total,0)+IFNULL(sk.amount,0)) AS receive
            FROM finance_debtor_1102050101_401 d 
            LEFT JOIN finance_stm_ofc s ON s.hn=d.hn AND s.vstdate = d.vstdate	AND LEFT(s.vsttime,5) =LEFT(d.vsttime,5)
            LEFT JOIN finance_stm_ofc_kidney sk ON sk.hn=d.hn AND sk.vstdate = d.vstdate AND d.kidney IS NOT NULL 
            WHERE d.vstdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050101_501 = DB::select('
            SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
            FROM finance_debtor_1102050101_501 
            WHERE vstdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050101_703 = DB::select('
            SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
            FROM finance_debtor_1102050101_703 
            WHERE vstdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050102_106 = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT d.vn) AS anvn,SUM(debtor) AS debtor,SUM(IFNULL(d.receive,r.bill_amount)) AS receive
            FROM htp_report.finance_debtor_1102050102_106 d 
            LEFT JOIN rcpt_print r ON r.vn = d.vn AND r.`status` ="OK" AND r.department="OPD" 
            WHERE d.vstdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050102_108 = DB::select('
            SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
            FROM finance_debtor_1102050102_108 
            WHERE vstdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050102_602 = DB::select('
            SELECT COUNT(DISTINCT vn) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive),0) AS receive
            FROM finance_debtor_1102050102_602 
            WHERE vstdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050102_801 = DB::select('
            SELECT COUNT(DISTINCT d.vn) AS anvn,SUM(d.debtor) AS debtor,SUM(IFNULL(s.compensate_treatment,0)+IFNULL(sk.compensate_kidney,0)) AS receive
            FROM finance_debtor_1102050102_801 d   
                LEFT JOIN finance_stm_lgo s ON s.hn=d.hn AND s.vstdate = d.vstdate AND LEFT(s.vsttime,5) =LEFT(d.vsttime,5)
            LEFT JOIN finance_stm_lgo_kidney sk ON sk.hn=d.hn AND sk.datetimeadm = d.vstdate AND d.kidney IS NOT NULL
            WHERE d.vstdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050102_803 = DB::select('
            SELECT COUNT(DISTINCT d.vn) AS anvn,SUM(d.debtor) AS debtor,SUM(IFNULL(s.receive_total,0)+IFNULL(sk.amount,0)) AS receive
            FROM finance_debtor_1102050102_803 d   
                LEFT JOIN finance_stm_ofc s ON s.hn=d.hn AND s.vstdate = d.vstdate AND LEFT(s.vsttime,5) =LEFT(d.vsttime,5)
            LEFT JOIN finance_stm_ofc_kidney sk ON sk.hn=d.hn AND sk.vstdate = d.vstdate AND d.kidney IS NOT NULL
            WHERE d.vstdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050101_202 = DB::select('
            SELECT COUNT(an) AS anvn,SUM(debtor) AS debtor,IFNULL(SUM(receive_ip_compensate_pay),0) AS receive
            FROM (SELECT d.an,d.debtor,stm.receive_ip_compensate_pay FROM finance_debtor_1102050101_202 d
            LEFT JOIN finance_stm_ucs stm ON stm.an=d.an    
            WHERE d.dchdate BETWEEN ? AND ? GROUP BY d.an) AS a',[$start_date,$end_date]);
        $_1102050101_217 = DB::select('
            SELECT COUNT(DISTINCT an) AS anvn,SUM(debtor) AS debtor,SUM(receive) AS receive
            FROM (SELECT d.dchdate,d.an,d.debtor,(s.receive_total-s.receive_ip_compensate_pay)+IFNULL(SUM(sk.receive_total),0) AS receive
            FROM finance_debtor_1102050101_217 d
            LEFT JOIN finance_stm_ucs s ON s.an=d.an
            LEFT JOIN finance_stm_ucs_kidney sk ON sk.cid=d.cid AND sk.datetimeadm BETWEEN d.regdate AND d.dchdate
            WHERE d.dchdate BETWEEN ? AND ? GROUP BY d.an) AS a ',[$start_date,$end_date]);
        $_1102050101_302 = DB::select('
            SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive) AS receive
            FROM finance_debtor_1102050101_302    
            WHERE dchdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050101_304 = DB::select('
            SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive) AS receive
            FROM finance_debtor_1102050101_304    
            WHERE dchdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050101_308 = DB::select('
            SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive) AS receive
            FROM finance_debtor_1102050101_308    
            WHERE dchdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050101_310 = DB::select('
            SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive) AS receive
            FROM finance_debtor_1102050101_310    
            WHERE dchdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050101_402 = DB::select('
            SELECT COUNT(DISTINCT an) AS anvn,SUM(debtor) AS debtor,SUM(receive_total) AS receive
            FROM (SELECT d.*,s.receive_total+IFNULL(SUM(sk.amount),0) AS receive_total 
            FROM finance_debtor_1102050101_402 d    
            LEFT JOIN finance_stm_ofc s ON s.an=d.an
            LEFT JOIN finance_stm_ofc_kidney sk ON sk.hn=d.hn AND sk.vstdate BETWEEN d.regdate AND d.dchdate
            WHERE d.dchdate BETWEEN ? AND ? GROUP BY d.an) AS a ' ,[$start_date,$end_date]);
        $_1102050101_502 = DB::select('
            SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive) AS receive
            FROM finance_debtor_1102050101_502    
            WHERE dchdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050101_704 = DB::select('
            SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive) AS receive
            FROM finance_debtor_1102050101_704    
            WHERE dchdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050102_107 = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT d.vn) AS anvn,SUM(debtor) AS debtor,SUM(IFNULL(d.receive,r.bill_amount)) AS receive
            FROM htp_report.finance_debtor_1102050102_107 d
            LEFT JOIN rcpt_print r ON r.vn = d.vn AND r.`status` ="OK" AND r.department="OPD"
            WHERE d.dchdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050102_109 = DB::select('
            SELECT COUNT(DISTINCT an) AS anvn,SUM(debtor) AS debtor,SUM(receive) AS receive
            FROM finance_debtor_1102050102_109   
            WHERE dchdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050102_603 = DB::select('
            SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive) AS receive
            FROM finance_debtor_1102050102_603  
            WHERE dchdate BETWEEN ? AND ?',[$start_date,$end_date]);
        $_1102050102_802 = DB::select('
            SELECT COUNT(DISTINCT an) AS anvn,SUM(debtor) AS debtor,SUM(receive_total) AS receive
            FROM (SELECT d.*,s.compensate_treatment+IFNULL(SUM(sk.compensate_kidney),0) AS receive_total 
            FROM finance_debtor_1102050102_802 d    
            LEFT JOIN finance_stm_lgo s ON s.an=d.an
            LEFT JOIN finance_stm_lgo_kidney sk ON sk.cid=d.cid AND sk.datetimeadm BETWEEN d.regdate AND d.dchdate
            WHERE d.dchdate BETWEEN ? AND ? GROUP BY d.an ) AS a' ,[$start_date,$end_date]);
        $_1102050102_804 = DB::select('
            SELECT COUNT(DISTINCT an) AS anvn, SUM(debtor) AS debtor,SUM(receive_total) AS receive
                FROM (SELECT d.*,s.receive_total
            FROM finance_debtor_1102050102_804 d    
            LEFT JOIN finance_stm_ofc s ON s.an=d.an  
            WHERE d.dchdate BETWEEN ? AND ? GROUP BY d.an) AS a ' ,[$start_date,$end_date]);

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

        return view('hrims.debtor._summary',compact('start_date','end_date','_1102050101_103','_1102050101_109','_1102050101_201','_1102050101_203',
            '_1102050101_209','_1102050101_216','_1102050101_301','_1102050101_303','_1102050101_307','_1102050101_309','_1102050101_401',
            '_1102050101_501','_1102050101_703','_1102050102_106','_1102050102_108','_1102050102_602','_1102050102_801','_1102050102_803',
            '_1102050101_202','_1102050101_217','_1102050101_302','_1102050101_304','_1102050101_308','_1102050101_310','_1102050101_402',
            '_1102050101_502','_1102050101_704','_1102050102_107','_1102050102_109','_1102050102_603','_1102050102_802','_1102050102_804'));
    }
//_summary_pdf--------------------------------------------------------------------------------------------------------------------------------------------------
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

        $pdf = PDF::loadView('hrims.debtor._summary_pdf',compact('start_date','end_date','_1102050101_103','_1102050101_109','_1102050101_201',
        '_1102050101_203','_1102050101_209','_1102050101_216','_1102050101_301','_1102050101_303','_1102050101_307','_1102050101_309',
        '_1102050101_401','_1102050101_501','_1102050101_703','_1102050102_106','_1102050102_108','_1102050102_602','_1102050102_801',
        '_1102050102_803','_1102050101_202','_1102050101_217','_1102050101_302','_1102050101_304','_1102050101_308','_1102050101_310',
        '_1102050101_402','_1102050101_502','_1102050101_704','_1102050102_107','_1102050102_109','_1102050102_603','_1102050102_802','_1102050102_804'))
                    ->setPaper('A4', 'landscape');
        return @$pdf->stream();
    }
##############################################################################################################################################################
//_1102050101_103--------------------------------------------------------------------------------------------------------------
    public function _1102050101_103(Request $request )
    {
        $start_date = $request->start_date ?: Session::get('start_date');
        $end_date = $request->end_date ?: Session::get('end_date');
        $search  =  $request->search ?: Session::get('search'); 
        $pttype_checkup = DB::table('main_setting')->where('name', 'pttype_checkup')->value('value');
       

        $debtor =  Finance_debtor_1102050101_103::whereBetween('vstdate', [$start_date,$end_date])
                    ->where(function ($query) use ($search){
                        $query->where('ptname','like','%'.$search.'%');
                        $query->orwhere('hn','like','%'.$search.'%');
                        })
                    ->orderBy('vstdate')->get();

        $debtor_search = DB::connection('hosxp')->select('
            SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
                o.vsttime,p1.`name` AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
                v.income-v.rcpt_money AS debtor ,"ยืนยันลูกหนี้" AS status  
            FROM ovst o    
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p1 ON p1.pttype=vp.pttype    
            WHERE (o.an IS NULL OR o.an ="") 
                AND o.vstdate BETWEEN ? AND ?
                AND v.income-v.rcpt_money <>"0" 
                AND vp.pttype IN ('.$pttype_checkup.')
                AND o.vn NOT IN (SELECT vn FROM htp_report.finance_debtor_1102050101_103) 
            GROUP BY o.vn ORDER BY o.vstdate,o.oqueue',[$start_date,$end_date]); 

        $request->session()->put('start_date',$start_date);
        $request->session()->put('end_date',$end_date);
        $request->session()->put('search',$search);
        $request->session()->put('debtor',$debtor);
        $request->session()->save();

        return view('hrims.debtor.1102050101_103',compact('start_date','end_date','search','debtor','debtor_search'));
    }
//_1102050101_103_confirm-------------------------------------------------------------------------------------------------------
    public function _1102050101_103_confirm(Request $request )
    {
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');        
        $pttype_checkup = DB::table('main_setting')->where('name', 'pttype_checkup')->value('value'); 
        $request->validate([
        'checkbox' => 'required|array',
        ], [
            'checkbox.required' => 'กรุณาเลือกรายการที่ต้องการยืนยันลูกหนี้'
        ]);
        $checkbox = $request->input('checkbox'); // รับ array
        $checkbox_string = implode(",", $checkbox); // แปลงเป็น string สำหรับ SQL IN
       
        $debtor = DB::connection('hosxp')->select('
            SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
                o.vsttime,p1.`name` AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
                v.income-v.rcpt_money AS debtor ,"ยืนยันลูกหนี้" AS status   
            FROM ovst o    
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p1 ON p1.pttype=vp.pttype    
            WHERE (o.an IS NULL OR o.an ="") 
                AND o.vstdate BETWEEN ? AND ?
                AND v.income-v.rcpt_money <>"0" 
                AND vp.pttype IN ('.$pttype_checkup.')
                AND o.vn IN ('.$checkbox_string.') 
            GROUP BY o.vn ORDER BY o.vstdate,o.oqueue',[$start_date,$end_date]); 
        
        foreach ($debtor as $row) {
            Finance_debtor_1102050101_103::insert([
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
                'debtor'          => $row->debtor,  
                'status'          => $row->status,          
            ]);            
        }

        if (empty($checkbox) || !is_array($checkbox)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรายการที่จะยืนยัน'
            ]);
        }

        return redirect()->back()->with('success', 'ยืนยันลูกหนี้สำเร็จ');
    }
//_1102050101_103_delete-------------------------------------------------------------------------------------------------------
    public function _1102050101_103_delete(Request $request )
    {
        $checkbox = $request->input('checkbox_d');        

        $deleted = Finance_debtor_1102050101_103::whereIn('vn', $checkbox)
            ->whereNull('debtor_lock')
            ->delete();

        if (empty($checkbox) || !is_array($checkbox)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรายการที่จะลบ'
            ]);
        }

        return redirect()->back()->with('success', 'ลบลูกหนี้เรียบร้อย ');
    }
//_1102050101_103_update-------------------------------------------------------------------------------------------------------
    public function _1102050101_103_update(Request $request, $vn)
    {
        $item = Finance_debtor_1102050101_103::findOrFail($vn);
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
//1102050101_103_daily_pdf-------------------------------------------------------------------------------------------------------
    public function _1102050101_103_daily_pdf(Request $request)
    {
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');
        $debtor = DB::select('
            SELECT vstdate,COUNT(DISTINCT vn) AS anvn,
            SUM(debtor) AS debtor,SUM(receive) AS receive
            FROM finance_debtor_1102050101_103  
            WHERE vstdate BETWEEN ? AND ?
            GROUP BY vstdate ORDER BY vstdate',[$start_date,$end_date]);

        $pdf = PDF::loadView('hrims.debtor.1102050101_103_daily_pdf', compact('start_date','end_date','debtor'))
                    ->setPaper('A4', 'portrait');
        return @$pdf->stream();
    }
 //1102050101_103_indiv_excel-------------------------------------------------------------------------------------------------------   
    public function _1102050101_103_indiv_excel(Request $request)
        {
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');
        $debtor = Session::get('debtor');
        
        return view('hrims.debtor.1102050101_103_indiv_excel',compact('start_date','end_date','debtor'));
    }
##############################################################################################################################################################
//_1102050101_109--------------------------------------------------------------------------------------------------------------
    public function _1102050101_109(Request $request )
    {
        $start_date = $request->start_date ?: Session::get('start_date');
        $end_date = $request->end_date ?: Session::get('end_date');
        $search  =  $request->search ?: Session::get('search');              

        $debtor =  Finance_debtor_1102050101_109::whereBetween('vstdate', [$start_date,$end_date])
                    ->where(function ($query) use ($search){
                        $query->where('ptname','like','%'.$search.'%');
                        $query->orwhere('hn','like','%'.$search.'%');
                        })
                    ->orderBy('vstdate')->get();

        $debtor_search = DB::connection('hosxp')->select('
            SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
                o.vsttime,p1.`name` AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
                COALESCE(ems.claim_price, 0) AS debtor ,GROUP_CONCAT(DISTINCT sd.`name`) AS claim_list,"ยืนยันลูกหนี้" AS status  
            FROM ovst o    
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
			INNER JOIN opitemrece o1 ON o1.vn=o.vn
			INNER JOIN htp_report.lookup_icode li ON o1.icode = li.icode AND li.ems = "Y"	
			LEFT JOIN s_drugitems sd ON sd.icode=o1.icode
			LEFT JOIN (SELECT op.vn, SUM(op.sum_price) AS claim_price	FROM opitemrece op
				INNER JOIN htp_report.lookup_icode li ON op.icode = li.icode
				WHERE op.vstdate BETWEEN ? AND ? AND li.ems = "Y" GROUP BY op.vn) ems ON ems.vn=o.vn			
            WHERE (o.an IS NULL OR o.an ="") 
                AND v.income-v.rcpt_money <>"0" 
                AND o.vstdate BETWEEN ? AND ?            
                AND o.vn NOT IN (SELECT vn FROM htp_report.finance_debtor_1102050101_109) 
            GROUP BY o.vn ORDER BY o.vstdate,o.oqueue',[$start_date,$end_date,$start_date,$end_date]); 

        $request->session()->put('start_date',$start_date);
        $request->session()->put('end_date',$end_date);
        $request->session()->put('search',$search);
        $request->session()->put('debtor',$debtor);
        $request->session()->save();

        return view('hrims.debtor.1102050101_109',compact('start_date','end_date','search','debtor','debtor_search'));
    }
//_1102050101_109_confirm-------------------------------------------------------------------------------------------------------
    public function _1102050101_109_confirm(Request $request )
    {
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');        
        $pttype_checkup = DB::table('main_setting')->where('name', 'pttype_checkup')->value('value'); 
        $request->validate([
        'checkbox' => 'required|array',
        ], [
            'checkbox.required' => 'กรุณาเลือกรายการที่ต้องการยืนยันลูกหนี้'
        ]);
        $checkbox = $request->input('checkbox'); // รับ array
        $checkbox_string = implode(",", $checkbox); // แปลงเป็น string สำหรับ SQL IN
       
        $debtor = DB::connection('hosxp')->select('
            SELECT o.vn,o.hn,o.an,p.cid,CONCAT(p.pname,p.fname,SPACE(1),p.lname) AS ptname,o.vstdate,
                o.vsttime,p1.`name` AS pttype,vp.hospmain,p1.hipdata_code,v.pdx,v.income,v.rcpt_money,
                COALESCE(ems.claim_price, 0) AS debtor ,GROUP_CONCAT(DISTINCT sd.`name`) AS claim_list,"ยืนยันลูกหนี้" AS status  
            FROM ovst o    
            LEFT JOIN patient p ON p.hn=o.hn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p1 ON p1.pttype=vp.pttype
			INNER JOIN opitemrece o1 ON o1.vn=o.vn
			INNER JOIN htp_report.lookup_icode li ON o1.icode = li.icode AND li.ems = "Y"	
			LEFT JOIN s_drugitems sd ON sd.icode=o1.icode
			LEFT JOIN (SELECT op.vn, SUM(op.sum_price) AS claim_price	FROM opitemrece op
				INNER JOIN htp_report.lookup_icode li ON op.icode = li.icode
				WHERE op.vstdate BETWEEN ? AND ? AND li.ems = "Y" GROUP BY op.vn) ems ON ems.vn=o.vn			
            WHERE (o.an IS NULL OR o.an ="") 
                AND v.income-v.rcpt_money <>"0" 
                AND o.vstdate BETWEEN ? AND ?            
                AND o.vn IN ('.$checkbox_string.') 
            GROUP BY o.vn ORDER BY o.vstdate,o.oqueue',[$start_date,$end_date,$start_date,$end_date]); 
        
        foreach ($debtor as $row) {
            Finance_debtor_1102050101_109::insert([
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
                'ppfs'            => $row->ppfs,
                'debtor'          => $row->debtor,  
                'status'          => $row->status,          
            ]);            
        }

        if (empty($checkbox) || !is_array($checkbox)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรายการที่จะยืนยัน'
            ]);
        }

        return redirect()->back()->with('success', 'ยืนยันลูกหนี้สำเร็จ');
    }
//_1102050101_109_delete-------------------------------------------------------------------------------------------------------
    public function _1102050101_109_delete(Request $request )
    {
        $checkbox = $request->input('checkbox_d');        

        $deleted = Finance_debtor_1102050101_109::whereIn('vn', $checkbox)
            ->whereNull('debtor_lock')
            ->delete();

        if (empty($checkbox) || !is_array($checkbox)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรายการที่จะลบ'
            ]);
        }

        return redirect()->back()->with('success', 'ลบลูกหนี้เรียบร้อย ');
    }
//_1102050101_109_update-------------------------------------------------------------------------------------------------------
    public function _1102050101_109_update(Request $request, $vn)
    {
        $item = Finance_debtor_1102050101_109::findOrFail($vn);
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
//1102050101_109_daily_pdf-------------------------------------------------------------------------------------------------------
    public function _1102050101_109_daily_pdf(Request $request)
    {
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');
        $debtor = DB::select('
            SELECT vstdate,COUNT(DISTINCT vn) AS anvn,
            SUM(debtor) AS debtor,SUM(receive) AS receive
            FROM finance_debtor_1102050101_109  
            WHERE vstdate BETWEEN ? AND ?
            GROUP BY vstdate ORDER BY vstdate',[$start_date,$end_date]);

        $pdf = PDF::loadView('hrims.debtor.1102050101_109_daily_pdf', compact('start_date','end_date','debtor'))
                    ->setPaper('A4', 'portrait');
        return @$pdf->stream();
    }
 //1102050101_109_indiv_excel-------------------------------------------------------------------------------------------------------   
    public function _1102050101_109_indiv_excel(Request $request)
        {
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');
        $debtor = Session::get('debtor');
        
        return view('hrims.debtor.1102050101_109_indiv_excel',compact('start_date','end_date','debtor'));
    }
##############################################################################################################################################################
//_1102050101_201--------------------------------------------------------------------------------------------------------------
    public function _1102050101_201(Request $request )
    {
        $start_date = $request->start_date ?: Session::get('start_date');
        $end_date = $request->end_date ?: Session::get('end_date');
        $search  =  $request->search ?: Session::get('search');              

        $debtor =  Finance_debtor_1102050101_201::whereBetween('vstdate', [$start_date,$end_date])
                    ->where(function ($query) use ($search){
                        $query->where('ptname','like','%'.$search.'%');
                        $query->orwhere('hn','like','%'.$search.'%');
                        })
                    ->orderBy('vstdate')->get();

        if ($search) {
            $debtor = DB::select('
                SELECT d.vn,d.vstdate,d.vsttime, d.hn,d.ptname,d.hipdata_code,d.pttype,d.hospmain,d.pdx,d.income,  
                    d.rcpt_money,d.other,d.ppfs,d.debtor,d.receive,d.repno,s.receive_pp,
                    IF(s.receive_pp <>"",s.repno,"") AS repno_pp,d.status,d.debtor_lock
                FROM finance_debtor_1102050101_201 d   
                LEFT JOIN finance_stm_ucs s ON s.cid = d.cid AND s.vstdate = d.vstdate AND LEFT(s.vsttime, 5) = LEFT(d.vsttime, 5)
                WHERE (d.ptname LIKE CONCAT("%", ?, "%") OR d.hn LIKE CONCAT("%", ?, "%"))
                AND d.vstdate BETWEEN ? AND ?', [$search, $search, $start_date, $end_date]);
        } else {
            $debtor = DB::select('
                SELECT d.vn,d.vstdate,d.vsttime, d.hn,d.ptname,d.hipdata_code,d.pttype,d.hospmain,d.pdx,d.income,  
                    d.rcpt_money,d.other,d.ppfs,d.debtor,d.receive,d.repno,s.receive_pp,
                    IF(s.receive_pp <>"",s.repno,"") AS repno_pp,d.status,d.debtor_lock
                FROM finance_debtor_1102050101_201 d   
                LEFT JOIN finance_stm_ucs s ON s.cid = d.cid AND s.vstdate = d.vstdate AND LEFT(s.vsttime, 5) = LEFT(d.vsttime, 5)
                WHERE d.vstdate BETWEEN ? AND ?', [$start_date, $end_date]);
        }

        $debtor_search = DB::connection('hosxp')->select('
            SELECT o.vn,o.hn,o.an,pt.cid,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,o.vstdate,
                o.vsttime,p.`name` AS pttype,vp.hospmain,p.hipdata_code,v.pdx,v.income,v.rcpt_money,
                COALESCE(o1.other_price, 0) AS other,COALESCE(o2.ppfs_price, 0) AS ppfs,
                v.income-v.rcpt_money-COALESCE(o1.other_price, 0) AS debtor,GROUP_CONCAT(DISTINCT sd.`name`) AS other_list,
                GROUP_CONCAT(DISTINCT sd2.`name`) AS ppfs_list,"ยืนยันลูกหนี้" AS status  
            FROM ovst o    
            LEFT JOIN patient pt ON pt.hn=o.hn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p ON p.pttype=vp.pttype
			LEFT JOIN (SELECT op.vn, SUM(op.sum_price) AS other_price	FROM opitemrece op
				INNER JOIN htp_report.lookup_icode li ON op.icode = li.icode AND (li.ppfs ="" OR li.ppfs IS NULL)
				WHERE op.vstdate BETWEEN ? AND ? GROUP BY op.vn) o1 ON o1.vn=o.vn
			LEFT JOIN (SELECT op.vn, SUM(op.sum_price) AS ppfs_price	FROM opitemrece op
				INNER JOIN htp_report.lookup_icode li ON op.icode = li.icode AND li.ppfs ="Y" 
				WHERE op.vstdate BETWEEN ? AND ? GROUP BY op.vn) o2 ON o2.vn=o.vn
			LEFT JOIN opitemrece o3 ON o3.vn=o.vn AND o3.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE (ppfs IS NULL OR ppfs =""))	
			LEFT JOIN s_drugitems sd ON sd.icode=o3.icode			
			LEFT JOIN opitemrece o4 ON o4.vn=o.vn AND o4.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE ppfs ="Y")	
			LEFT JOIN s_drugitems sd2 ON sd2.icode=o4.icode	
            WHERE (o.an IS NULL OR o.an ="")
                AND v.income-v.rcpt_money <>"0"		
                AND v.income-v.rcpt_money-COALESCE(o1.other_price, 0) <>"0"					
                AND o.vstdate BETWEEN ? AND ?
                AND p.hipdata_code = "UCS" 					
                AND vp.hospmain IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE hmain_ucs ="Y")
                AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
                AND o.vn NOT IN (SELECT vn FROM htp_report.finance_debtor_1102050101_201) 
            GROUP BY o.vn ORDER BY o.vstdate,o.oqueue',[$start_date,$end_date,$start_date,$end_date,$start_date,$end_date]); 

        $request->session()->put('start_date',$start_date);
        $request->session()->put('end_date',$end_date);
        $request->session()->put('search',$search);
        $request->session()->put('debtor',$debtor);
        $request->session()->save();

        return view('hrims.debtor.1102050101_201',compact('start_date','end_date','search','debtor','debtor_search'));
    }
//_1102050101_201_confirm-------------------------------------------------------------------------------------------------------
    public function _1102050101_201_confirm(Request $request )
    {
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');        
        $pttype_checkup = DB::table('main_setting')->where('name', 'pttype_checkup')->value('value'); 
        $request->validate([
        'checkbox' => 'required|array',
        ], [
            'checkbox.required' => 'กรุณาเลือกรายการที่ต้องการยืนยันลูกหนี้'
        ]);
        $checkbox = $request->input('checkbox'); // รับ array
        $checkbox_string = implode(",", $checkbox); // แปลงเป็น string สำหรับ SQL IN
       
        $debtor = DB::connection('hosxp')->select('
            SELECT o.vn,o.hn,o.an,pt.cid,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,o.vstdate,
            o.vsttime,p.`name` AS pttype,vp.hospmain,p.hipdata_code,v.pdx,v.income,v.rcpt_money,
            COALESCE(o1.other_price, 0) AS other,COALESCE(o2.ppfs_price, 0) AS ppfs,
			v.income-v.rcpt_money-COALESCE(o1.other_price, 0) AS debtor,GROUP_CONCAT(DISTINCT sd.`name`) AS other_list,
			GROUP_CONCAT(DISTINCT sd2.`name`) AS ppfs_list,"ยืนยันลูกหนี้" AS status  
            FROM ovst o    
            LEFT JOIN patient pt ON pt.hn=o.hn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p ON p.pttype=vp.pttype
			LEFT JOIN (SELECT op.vn, SUM(op.sum_price) AS other_price	FROM opitemrece op
				INNER JOIN htp_report.lookup_icode li ON op.icode = li.icode AND (li.ppfs ="" OR li.ppfs IS NULL)
				WHERE op.vstdate BETWEEN ? AND ? GROUP BY op.vn) o1 ON o1.vn=o.vn
			LEFT JOIN (SELECT op.vn, SUM(op.sum_price) AS ppfs_price	FROM opitemrece op
				INNER JOIN htp_report.lookup_icode li ON op.icode = li.icode AND li.ppfs ="Y" 
				WHERE op.vstdate BETWEEN ? AND ? GROUP BY op.vn) o2 ON o2.vn=o.vn
			LEFT JOIN opitemrece o3 ON o3.vn=o.vn AND o3.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE (ppfs IS NULL OR ppfs =""))	
			LEFT JOIN s_drugitems sd ON sd.icode=o3.icode			
			LEFT JOIN opitemrece o4 ON o4.vn=o.vn AND o4.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE ppfs ="Y")	
			LEFT JOIN s_drugitems sd2 ON sd2.icode=o4.icode	
            WHERE (o.an IS NULL OR o.an ="")
                AND v.income-v.rcpt_money <>"0"		
                AND v.income-v.rcpt_money-COALESCE(o1.other_price, 0) <>"0"					
                AND o.vstdate BETWEEN ? AND ?
                AND p.hipdata_code = "UCS" 					
                AND vp.hospmain IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE hmain_ucs ="Y")
                AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
                AND o.vn IN ('.$checkbox_string.') 
            GROUP BY o.vn ORDER BY o.vstdate,o.oqueue',[$start_date,$end_date,$start_date,$end_date,$start_date,$end_date]); 
        
        foreach ($debtor as $row) {
            Finance_debtor_1102050101_201::insert([
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
                'ppfs'            => $row->ppfs,
                'debtor'          => $row->debtor,  
                'status'          => $row->status,          
            ]);            
        }

        if (empty($checkbox) || !is_array($checkbox)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรายการที่จะยืนยัน'
            ]);
        }

        return redirect()->back()->with('success', 'ยืนยันลูกหนี้สำเร็จ');
    }
//_1102050101_201_delete-------------------------------------------------------------------------------------------------------
    public function _1102050101_201_delete(Request $request )
    {
        $checkbox = $request->input('checkbox_d');        

        $deleted = Finance_debtor_1102050101_201::whereIn('vn', $checkbox)
            ->whereNull('debtor_lock')
            ->delete();

        if (empty($checkbox) || !is_array($checkbox)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรายการที่จะลบ'
            ]);
        }

        return redirect()->back()->with('success', 'ลบลูกหนี้เรียบร้อย ');
    }

//1102050101_201_daily_pdf-------------------------------------------------------------------------------------------------------
    public function _1102050101_201_daily_pdf(Request $request)
    {
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');
        $debtor = DB::select('
            SELECT vstdate,COUNT(DISTINCT vn) AS anvn,
            SUM(debtor) AS debtor,SUM(receive) AS receive
            FROM finance_debtor_1102050101_201  
            WHERE vstdate BETWEEN ? AND ?
            GROUP BY vstdate ORDER BY vstdate',[$start_date,$end_date]);

        $pdf = PDF::loadView('hrims.debtor.1102050101_201_daily_pdf', compact('start_date','end_date','debtor'))
                    ->setPaper('A4', 'portrait');
        return @$pdf->stream();
    }
 //1102050101_201_indiv_excel-------------------------------------------------------------------------------------------------------   
    public function _1102050101_201_indiv_excel(Request $request)
        {
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');
        $debtor = Session::get('debtor');
        
        return view('hrims.debtor.1102050101_201_indiv_excel',compact('start_date','end_date','debtor'));
    }
##############################################################################################################################################################
//_1102050101_203--------------------------------------------------------------------------------------------------------------
    public function _1102050101_203(Request $request )
    {
        $start_date = $request->start_date ?: Session::get('start_date');
        $end_date = $request->end_date ?: Session::get('end_date');
        $search  =  $request->search ?: Session::get('search');              

        $debtor =  Finance_debtor_1102050101_203::whereBetween('vstdate', [$start_date,$end_date])
                    ->where(function ($query) use ($search){
                        $query->where('ptname','like','%'.$search.'%');
                        $query->orwhere('hn','like','%'.$search.'%');
                        })
                    ->orderBy('vstdate')->get();
        if ($search) {
            $debtor = DB::select('
                SELECT d.vn,d.vstdate,d.vsttime, d.hn,d.ptname,d.hipdata_code,d.pttype,d.hospmain,d.pdx,d.income,  
                    d.rcpt_money,d.other,d.ppfs,d.debtor,d.charge,d.charge_date,d.charge_no,d.receive,d.receive_date,
					d.receive_no,d.repno,s.receive_pp,s.repno AS repno_pp,d.status,d.debtor_lock
                FROM finance_debtor_1102050101_203 d   
                LEFT JOIN finance_stm_ucs s ON s.cid = d.cid AND s.vstdate = d.vstdate AND LEFT(s.vsttime, 5) = LEFT(d.vsttime, 5)
                WHERE (d.ptname LIKE CONCAT("%", ?, "%") OR d.hn LIKE CONCAT("%", ?, "%"))
                AND d.vstdate BETWEEN ? AND ?', [$search, $search, $start_date, $end_date]);
        } else {
            $debtor = DB::select('
                SELECT d.vn,d.vstdate,d.vsttime, d.hn,d.ptname,d.hipdata_code,d.pttype,d.hospmain,d.pdx,d.income,  
                    d.rcpt_money,d.other,d.ppfs,d.debtor,d.charge,d.charge_date,d.charge_no,d.receive,d.receive_date,
					d.receive_no,d.repno,s.receive_pp,s.repno AS repno_pp,d.status,d.debtor_lock
                FROM finance_debtor_1102050101_203 d   
                LEFT JOIN finance_stm_ucs s ON s.cid = d.cid AND s.vstdate = d.vstdate AND LEFT(s.vsttime, 5) = LEFT(d.vsttime, 5)
                WHERE d.vstdate BETWEEN ? AND ?', [$start_date, $end_date]);
        }

        $debtor_search = DB::connection('hosxp')->select('
            SELECT o.vn,o.hn,o.an,pt.cid,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,o.vstdate,
            o.vsttime,p.`name` AS pttype,vp.hospmain,p.hipdata_code,v.pdx,v.income,v.rcpt_money,
            COALESCE(o1.other_price, 0) AS other,COALESCE(o2.ppfs_price, 0) AS ppfs,
			v.income-v.rcpt_money-COALESCE(o1.other_price, 0) AS debtor,GROUP_CONCAT(DISTINCT sd.`name`) AS other_list,
			GROUP_CONCAT(DISTINCT sd2.`name`) AS ppfs_list,"ยืนยันลูกหนี้" AS status  
            FROM ovst o    
            LEFT JOIN patient pt ON pt.hn=o.hn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p ON p.pttype=vp.pttype
			LEFT JOIN (SELECT op.vn, SUM(op.sum_price) AS other_price	FROM opitemrece op
				INNER JOIN htp_report.lookup_icode li ON op.icode = li.icode AND (li.ppfs ="" OR li.ppfs IS NULL)
				WHERE op.vstdate BETWEEN ? AND ? GROUP BY op.vn) o1 ON o1.vn=o.vn
			LEFT JOIN (SELECT op.vn, SUM(op.sum_price) AS ppfs_price	FROM opitemrece op
				INNER JOIN htp_report.lookup_icode li ON op.icode = li.icode AND li.ppfs ="Y" 
				WHERE op.vstdate BETWEEN ? AND ? GROUP BY op.vn) o2 ON o2.vn=o.vn
			LEFT JOIN opitemrece o3 ON o3.vn=o.vn AND o3.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE (ppfs IS NULL OR ppfs =""))	
			LEFT JOIN s_drugitems sd ON sd.icode=o3.icode			
			LEFT JOIN opitemrece o4 ON o4.vn=o.vn AND o4.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE ppfs ="Y")	
			LEFT JOIN s_drugitems sd2 ON sd2.icode=o4.icode	
            WHERE (o.an IS NULL OR o.an ="")
                AND v.income-v.rcpt_money <>"0"		
                AND v.income-v.rcpt_money-COALESCE(o1.other_price, 0) <>"0"					
                AND o.vstdate BETWEEN ? AND ?
                AND p.hipdata_code = "UCS" 					
                AND vp.hospmain IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE in_province = "Y"	AND (hmain_ucs IS NULL OR hmain_ucs ="")) 
                AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
                AND o.vn NOT IN (SELECT vn FROM htp_report.finance_debtor_1102050101_203) 
            GROUP BY o.vn ORDER BY o.vstdate,o.oqueue',[$start_date,$end_date,$start_date,$end_date,$start_date,$end_date]); 

        $request->session()->put('start_date',$start_date);
        $request->session()->put('end_date',$end_date);
        $request->session()->put('search',$search);
        $request->session()->put('debtor',$debtor);
        $request->session()->save();

        return view('hrims.debtor.1102050101_203',compact('start_date','end_date','search','debtor','debtor_search'));
    }
//_1102050101_203_confirm-------------------------------------------------------------------------------------------------------
    public function _1102050101_203_confirm(Request $request )
    {
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');        
        $pttype_checkup = DB::table('main_setting')->where('name', 'pttype_checkup')->value('value'); 
        $request->validate([
        'checkbox' => 'required|array',
        ], [
            'checkbox.required' => 'กรุณาเลือกรายการที่ต้องการยืนยันลูกหนี้'
        ]);
        $checkbox = $request->input('checkbox'); // รับ array
        $checkbox_string = implode(",", $checkbox); // แปลงเป็น string สำหรับ SQL IN
       
        $debtor = DB::connection('hosxp')->select('
            SELECT o.vn,o.hn,o.an,pt.cid,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,o.vstdate,
            o.vsttime,p.`name` AS pttype,vp.hospmain,p.hipdata_code,v.pdx,v.income,v.rcpt_money,
            COALESCE(o1.other_price, 0) AS other,COALESCE(o2.ppfs_price, 0) AS ppfs,
			v.income-v.rcpt_money-COALESCE(o1.other_price, 0) AS debtor,GROUP_CONCAT(DISTINCT sd.`name`) AS other_list,
			GROUP_CONCAT(DISTINCT sd2.`name`) AS ppfs_list,"ยืนยันลูกหนี้" AS status  
            FROM ovst o    
            LEFT JOIN patient pt ON pt.hn=o.hn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p ON p.pttype=vp.pttype
			LEFT JOIN (SELECT op.vn, SUM(op.sum_price) AS other_price	FROM opitemrece op
				INNER JOIN htp_report.lookup_icode li ON op.icode = li.icode AND (li.ppfs ="" OR li.ppfs IS NULL)
				WHERE op.vstdate BETWEEN ? AND ? GROUP BY op.vn) o1 ON o1.vn=o.vn
			LEFT JOIN (SELECT op.vn, SUM(op.sum_price) AS ppfs_price	FROM opitemrece op
				INNER JOIN htp_report.lookup_icode li ON op.icode = li.icode AND li.ppfs ="Y" 
				WHERE op.vstdate BETWEEN ? AND ? GROUP BY op.vn) o2 ON o2.vn=o.vn
			LEFT JOIN opitemrece o3 ON o3.vn=o.vn AND o3.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE (ppfs IS NULL OR ppfs =""))	
			LEFT JOIN s_drugitems sd ON sd.icode=o3.icode			
			LEFT JOIN opitemrece o4 ON o4.vn=o.vn AND o4.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE ppfs ="Y")	
			LEFT JOIN s_drugitems sd2 ON sd2.icode=o4.icode	
            WHERE (o.an IS NULL OR o.an ="")
                AND v.income-v.rcpt_money <>"0"		
                AND v.income-v.rcpt_money-COALESCE(o1.other_price, 0) <>"0"					
                AND o.vstdate BETWEEN ? AND ?
                AND p.hipdata_code = "UCS" 					
                AND vp.hospmain IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE in_province = "Y"	AND (hmain_ucs IS NULL OR hmain_ucs ="")) 
                AND v.pdx NOT IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
                AND o.vn IN ('.$checkbox_string.') 
            GROUP BY o.vn ORDER BY o.vstdate,o.oqueue',[$start_date,$end_date,$start_date,$end_date,$start_date,$end_date]); 
        
        foreach ($debtor as $row) {
            Finance_debtor_1102050101_203::insert([
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
                'ppfs'            => $row->ppfs,
                'debtor'          => $row->debtor,  
                'status'          => $row->status,          
            ]);            
        }

        if (empty($checkbox) || !is_array($checkbox)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรายการที่จะยืนยัน'
            ]);
        }

        return redirect()->back()->with('success', 'ยืนยันลูกหนี้สำเร็จ');
    }
//_1102050101_203_delete-------------------------------------------------------------------------------------------------------
    public function _1102050101_203_delete(Request $request )
    {
        $checkbox = $request->input('checkbox_d');        

        $deleted = Finance_debtor_1102050101_203::whereIn('vn', $checkbox)
            ->whereNull('debtor_lock')
            ->delete();

        if (empty($checkbox) || !is_array($checkbox)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรายการที่จะลบ'
            ]);
        }

        return redirect()->back()->with('success', 'ลบลูกหนี้เรียบร้อย ');
    }
//_1102050101_203_update-------------------------------------------------------------------------------------------------------
    public function _1102050101_203_update(Request $request, $vn)
    {
        $item = Finance_debtor_1102050101_203::findOrFail($vn);
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
//1102050101_203_daily_pdf-------------------------------------------------------------------------------------------------------
    public function _1102050101_203_daily_pdf(Request $request)
    {
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');
        $debtor = DB::select('
            SELECT vstdate,COUNT(DISTINCT vn) AS anvn,
            SUM(debtor) AS debtor,SUM(receive) AS receive
            FROM finance_debtor_1102050101_203  
            WHERE vstdate BETWEEN ? AND ?
            GROUP BY vstdate ORDER BY vstdate',[$start_date,$end_date]);

        $pdf = PDF::loadView('hrims.debtor.1102050101_203_daily_pdf', compact('start_date','end_date','debtor'))
                    ->setPaper('A4', 'portrait');
        return @$pdf->stream();
    }
 //1102050101_203_indiv_excel-------------------------------------------------------------------------------------------------------   
    public function _1102050101_203_indiv_excel(Request $request)
        {
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');
        $debtor = Session::get('debtor');
        
        return view('hrims.debtor.1102050101_203_indiv_excel',compact('start_date','end_date','debtor'));
    }
##############################################################################################################################################################
//_1102050101_209--------------------------------------------------------------------------------------------------------------
    public function _1102050101_209(Request $request )
    {
        $start_date = $request->start_date ?: Session::get('start_date');
        $end_date = $request->end_date ?: Session::get('end_date');
        $search  =  $request->search ?: Session::get('search'); 
        $pttype_checkup = DB::table('main_setting')->where('name', 'pttype_checkup')->value('value');
        
        if ($search) {
            $debtor = DB::select('
                SELECT d.vn, d.vstdate, d.vsttime, d.hn, d.ptname, d.hipdata_code, d.pttype, d.hospmain,d.pdx, d.income,  
                    d.rcpt_money, d.ppfs, d.pp, d.other, d.debtor,s.receive_pp AS receive, s.repno, d.status, d.debtor_lock
                FROM finance_debtor_1102050101_209 d   
                LEFT JOIN finance_stm_ucs s ON s.cid = d.cid AND s.vstdate = d.vstdate AND LEFT(s.vsttime, 5) = LEFT(d.vsttime, 5)
                WHERE (d.ptname LIKE CONCAT("%", ?, "%") OR d.hn LIKE CONCAT("%", ?, "%"))
                AND d.vstdate BETWEEN ? AND ?', [$search, $search, $start_date, $end_date]);
        } else {
            $debtor = DB::select('
                SELECT d.vn, d.vstdate, d.vsttime, d.hn, d.ptname, d.hipdata_code, d.pttype, d.hospmain, d.pdx,d.income,
                     d.rcpt_money, d.ppfs, d.pp, d.other,d.debtor,s.receive_pp AS receive, s.repno, d.status, d.debtor_lock
                FROM finance_debtor_1102050101_209 d   
                LEFT JOIN finance_stm_ucs s ON s.cid = d.cid AND s.vstdate = d.vstdate AND LEFT(s.vsttime, 5) = LEFT(d.vsttime, 5)
                WHERE d.vstdate BETWEEN ? AND ?', [$start_date, $end_date]);
        }

        $debtor_search = DB::connection('hosxp')->select('
            SELECT o.vn,o.hn,o.an,pt.cid,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,o.vstdate,
            o.vsttime,p.`name` AS pttype,vp.hospmain,p.hipdata_code,v.pdx,v.income,v.rcpt_money,
            COALESCE(o1.other_price, 0) AS other,COALESCE(o2.ppfs_price, 0) AS ppfs,
			v.income-v.rcpt_money-COALESCE(o1.other_price, 0) AS debtor,GROUP_CONCAT(DISTINCT sd.`name`) AS other_list,
			GROUP_CONCAT(DISTINCT sd2.`name`) AS ppfs_list,"ยืนยันลูกหนี้" AS status  
            FROM ovst o    
            LEFT JOIN patient pt ON pt.hn=o.hn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p ON p.pttype=vp.pttype
			LEFT JOIN (SELECT op.vn, SUM(op.sum_price) AS other_price	FROM opitemrece op
				INNER JOIN htp_report.lookup_icode li ON op.icode = li.icode AND (li.ppfs ="" OR li.ppfs IS NULL)
				WHERE op.vstdate BETWEEN ? AND ? GROUP BY op.vn) o1 ON o1.vn=o.vn
			LEFT JOIN (SELECT op.vn, SUM(op.sum_price) AS ppfs_price FROM opitemrece op
				INNER JOIN htp_report.lookup_icode li ON op.icode = li.icode AND li.ppfs ="Y" 
				WHERE op.vstdate BETWEEN ? AND ? GROUP BY op.vn) o2 ON o2.vn=o.vn
			LEFT JOIN opitemrece o3 ON o3.vn=o.vn AND o3.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE (ppfs IS NULL OR ppfs =""))	
			LEFT JOIN s_drugitems sd ON sd.icode=o3.icode			
			LEFT JOIN opitemrece o4 ON o4.vn=o.vn AND o4.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE ppfs ="Y")	
			LEFT JOIN s_drugitems sd2 ON sd2.icode=o4.icode			
            WHERE (o.an IS NULL OR o.an ="")
                AND v.income-v.rcpt_money <> "0" 
                AND v.income-v.rcpt_money-COALESCE(o1.other_price, 0) <> "0" 
                AND o.vstdate BETWEEN ? AND ?
                AND p.hipdata_code NOT IN ("OFC","LGO")	
                AND vp.pttype NOT IN ('.$pttype_checkup.')
                AND v.pdx IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
                AND o.vn NOT IN (SELECT vn FROM htp_report.finance_debtor_1102050101_209) 
            GROUP BY o.vn ORDER BY o.vstdate,o.oqueue',[$start_date,$end_date,$start_date,$end_date,$start_date,$end_date]); 

        $request->session()->put('start_date',$start_date);
        $request->session()->put('end_date',$end_date);
        $request->session()->put('search',$search);
        $request->session()->put('debtor',$debtor);
        $request->session()->save();

        return view('hrims.debtor.1102050101_209',compact('start_date','end_date','search','debtor','debtor_search'));
    }
//_1102050101_209_confirm-------------------------------------------------------------------------------------------------------
    public function _1102050101_209_confirm(Request $request )
    {
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');        
        $pttype_checkup = DB::table('main_setting')->where('name', 'pttype_checkup')->value('value'); 
        $request->validate([
        'checkbox' => 'required|array',
        ], [
            'checkbox.required' => 'กรุณาเลือกรายการที่ต้องการยืนยันลูกหนี้'
        ]);
        $checkbox = $request->input('checkbox'); // รับ array
        $checkbox_string = implode(",", $checkbox); // แปลงเป็น string สำหรับ SQL IN
       
        $debtor = DB::connection('hosxp')->select('
            SELECT o.vn,o.hn,o.an,pt.cid,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,o.vstdate,
            o.vsttime,p.`name` AS pttype,vp.hospmain,p.hipdata_code,v.pdx,v.income,v.rcpt_money,
            COALESCE(o1.other_price, 0) AS other,COALESCE(o2.ppfs_price, 0) AS ppfs,
			v.income-v.rcpt_money-COALESCE(o1.other_price, 0) AS debtor,GROUP_CONCAT(DISTINCT sd.`name`) AS other_list,
			GROUP_CONCAT(DISTINCT sd2.`name`) AS ppfs_list,"ยืนยันลูกหนี้" AS status  
            FROM ovst o    
            LEFT JOIN patient pt ON pt.hn=o.hn
            LEFT JOIN vn_stat v ON v.vn=o.vn
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p ON p.pttype=vp.pttype
			LEFT JOIN (SELECT op.vn, SUM(op.sum_price) AS other_price	FROM opitemrece op
				INNER JOIN htp_report.lookup_icode li ON op.icode = li.icode AND (li.ppfs ="" OR li.ppfs IS NULL)
				WHERE op.vstdate BETWEEN ? AND ? GROUP BY op.vn) o1 ON o1.vn=o.vn
			LEFT JOIN (SELECT op.vn, SUM(op.sum_price) AS ppfs_price	FROM opitemrece op
				INNER JOIN htp_report.lookup_icode li ON op.icode = li.icode AND li.ppfs ="Y" 
				WHERE op.vstdate BETWEEN ? AND ? GROUP BY op.vn) o2 ON o2.vn=o.vn
			LEFT JOIN opitemrece o3 ON o3.vn=o.vn AND o3.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE (ppfs IS NULL OR ppfs =""))	
			LEFT JOIN s_drugitems sd ON sd.icode=o3.icode			
			LEFT JOIN opitemrece o4 ON o4.vn=o.vn AND o4.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE ppfs ="Y")	
			LEFT JOIN s_drugitems sd2 ON sd2.icode=o4.icode		
            WHERE (o.an IS NULL OR o.an ="") 
                AND v.income-v.rcpt_money <> "0"
                AND v.income-v.rcpt_money-COALESCE(o1.other_price, 0) <> "0" 
                AND o.vstdate BETWEEN ? AND ?
                AND p.hipdata_code NOT IN ("OFC","LGO")	
                AND vp.pttype NOT IN ('.$pttype_checkup.')
                AND v.pdx IN (SELECT icd10 FROM htp_report.lookup_icd10 WHERE pp = "Y")
                AND o.vn IN ('.$checkbox_string.') 
            GROUP BY o.vn ORDER BY o.vstdate,o.oqueue',[$start_date,$end_date,$start_date,$end_date,$start_date,$end_date]); 
        
        foreach ($debtor as $row) {
            Finance_debtor_1102050101_209::insert([
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
                'ppfs'            => $row->ppfs,
                'debtor'          => $row->debtor,  
                'status'          => $row->status,          
            ]);            
        }

        if (empty($checkbox) || !is_array($checkbox)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรายการที่จะยืนยัน'
            ]);
        }

        return redirect()->back()->with('success', 'ยืนยันลูกหนี้สำเร็จ');
    }
//_1102050101_209_delete-------------------------------------------------------------------------------------------------------
    public function _1102050101_209_delete(Request $request )
    {
        $checkbox = $request->input('checkbox_d');        

        $deleted = Finance_debtor_1102050101_209::whereIn('vn', $checkbox)
            ->whereNull('debtor_lock')
            ->delete();

        if (empty($checkbox) || !is_array($checkbox)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรายการที่จะลบ'
            ]);
        }

        return redirect()->back()->with('success', 'ลบลูกหนี้เรียบร้อย ');
    }

//1102050101_209_daily_pdf-------------------------------------------------------------------------------------------------------
    public function _1102050101_209_daily_pdf(Request $request)
    {
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');
        $debtor = DB::select('
            SELECT d.vstdate,COUNT(DISTINCT d.vn) AS anvn,
            SUM(d.debtor) AS debtor,SUM(s.receive_pp) AS receive
            FROM finance_debtor_1102050101_209 d   
            LEFT JOIN finance_stm_ucs s ON s.cid=d.cid AND s.vstdate = d.vstdate
            AND LEFT(s.vsttime,5) =LEFT(d.vsttime,5)
            WHERE d.vstdate BETWEEN ? AND ?
            GROUP BY d.vstdate ORDER BY d.vstdate ',[$start_date,$end_date]);

        $pdf = PDF::loadView('hrims.debtor.1102050101_209_daily_pdf', compact('start_date','end_date','debtor'))
                    ->setPaper('A4', 'portrait');
        return @$pdf->stream();
    }
 //1102050101_209_indiv_excel-------------------------------------------------------------------------------------------------------   
    public function _1102050101_209_indiv_excel(Request $request)
        {
        $start_date = Session::get('start_date');
        $end_date = Session::get('end_date');
        $debtor = Session::get('debtor');
        
        return view('hrims.debtor.1102050101_209_indiv_excel',compact('start_date','end_date','debtor'));
    }

}
