<?php

namespace App\Http\Controllers\Hrims;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MishosController extends Controller
{
//Check Login
    public function __construct()
    {
        $this->middleware('auth');
    }
//----------------------------------------------------------------------------------------------------------------------------------------
    public function ucs_ae(Request $request )
    {
        ini_set('max_execution_time', 300); // เพิ่มเป็น 5 นาที

        $year_data = DB::table('budget_year')
            ->orderByDesc('LEAVE_YEAR_ID')
            ->first(['LEAVE_YEAR_ID', 'DATE_BEGIN', 'DATE_END']);
        $budget_year = $year_data->LEAVE_YEAR_ID ?? null;
        $start_date_b = $year_data->DATE_BEGIN ?? null;
        $end_date_b   = $year_data->DATE_END ?? null;
        $start_date = $request->start_date ?: date('Y-m-d');
        $end_date = $request->end_date ?: date('Y-m-d');

        $sum_month=DB::connection('hosxp')->select('
            SELECT CASE WHEN MONTH(vstdate)=10 THEN CONCAT("ต.ค. ", RIGHT(YEAR(vstdate)+543, 2))
                WHEN MONTH(vstdate)=11 THEN CONCAT("พ.ย. ", RIGHT(YEAR(vstdate)+543, 2))
                WHEN MONTH(vstdate)=12 THEN CONCAT("ธ.ค. ", RIGHT(YEAR(vstdate)+543, 2))
                WHEN MONTH(vstdate)=1 THEN CONCAT("ม.ค. ", RIGHT(YEAR(vstdate)+543, 2))
                WHEN MONTH(vstdate)=2 THEN CONCAT("ก.พ. ", RIGHT(YEAR(vstdate)+543, 2))
                WHEN MONTH(vstdate)=3 THEN CONCAT("มี.ค. ", RIGHT(YEAR(vstdate)+543, 2))
                WHEN MONTH(vstdate)=4 THEN CONCAT("เม.ย. ", RIGHT(YEAR(vstdate)+543, 2))
                WHEN MONTH(vstdate)=5 THEN CONCAT("พ.ค. ", RIGHT(YEAR(vstdate)+543, 2))
                WHEN MONTH(vstdate)=6 THEN CONCAT("มิ.ย. ", RIGHT(YEAR(vstdate)+543, 2))
                WHEN MONTH(vstdate)=7 THEN CONCAT("ก.ค. ", RIGHT(YEAR(vstdate)+543, 2))
                WHEN MONTH(vstdate)=8 THEN CONCAT("ส.ค. ", RIGHT(YEAR(vstdate)+543, 2))
                WHEN MONTH(vstdate)=9 THEN CONCAT("ก.ย. ", RIGHT(YEAR(vstdate)+543, 2))
                END AS month,COUNT(vn) AS visit,SUM(IFNULL(claim_price,0)) AS claim_price,SUM(IFNULL(receive_total,0)) AS receive_total
            FROM (SELECT o.vstdate,o.vsttime,o.vn,v.income-v.rcpt_money AS claim_price,stm.receive_total
                FROM ovst o           
                LEFT JOIN patient pt ON pt.hn=o.hn
                LEFT JOIN visit_pttype vp ON vp.vn=o.vn           
                LEFT JOIN pttype p ON p.pttype=vp.pttype          
                LEFT JOIN vn_stat v ON v.vn = o.vn           
                LEFT JOIN opitemrece kidney ON kidney.vn=o.vn AND kidney.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE kidney = "Y")
                LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))
                LEFT JOIN htp_report.finance_stm_ucs stm ON stm.cid=pt.cid AND stm.vstdate = o.vstdate AND LEFT(stm.vsttime,5) =LEFT(o.vsttime,5)
                WHERE (o.an ="" OR o.an IS NULL)
                    AND proj.vn IS NULL
                    AND kidney.vn IS NULL 
                    AND p.hipdata_code = "UCS" 							
                    AND vp.hospmain NOT IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE in_province = "Y")            
                    AND o.vstdate BETWEEN ? AND ?
                GROUP BY o.vn ) AS a
                GROUP BY YEAR(vstdate), MONTH(vstdate)
                ORDER BY YEAR(vstdate), MONTH(vstdate)',[$start_date_b,$end_date_b]);
        $month = array_column($sum_month,'month');  
        $claim_price = array_column($sum_month,'claim_price');
        $receive_total = array_column($sum_month,'receive_total');

        $search=DB::connection('hosxp')->select('
            SELECT o.vstdate,o.vsttime,o.oqueue,pt.cid,pt.hn,CONCAT(pt.pname,pt.fname,SPACE(1),pt.lname) AS ptname,
            p.`name` AS pttype,vp.hospmain,v.pdx,v.income,v.rcpt_money,v.income-v.rcpt_money AS claim_price,
            stm.receive_total,stm.repno
            FROM ovst o
            LEFT JOIN patient pt ON pt.hn=o.hn
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn           
            LEFT JOIN pttype p ON p.pttype=vp.pttype          
            LEFT JOIN vn_stat v ON v.vn = o.vn
            LEFT JOIN ovst_eclaim oe ON oe.vn=o.vn
            LEFT JOIN opitemrece kidney ON kidney.vn=o.vn AND kidney.icode IN (SELECT icode FROM htp_report.lookup_icode WHERE kidney = "Y")
            LEFT JOIN opitemrece proj ON proj.vn=o.vn AND proj.icode IN (SELECT icode FROM nondrugitems WHERE nhso_adp_code IN ("WALKIN","UCEP24"))   
		    LEFT JOIN nondrugitems n_proj ON n_proj.icode=proj.icode								
            LEFT JOIN rep_eclaim_detail rep ON rep.vn=o.vn
            LEFT JOIN htp_report.finance_stm_ucs stm ON stm.cid=pt.cid AND stm.vstdate = o.vstdate AND LEFT(stm.vsttime,5) =LEFT(o.vsttime,5)
            WHERE (o.an ="" OR o.an IS NULL)
			AND proj.vn IS NULL
			AND kidney.vn IS NULL 
			AND p.hipdata_code = "UCS" 							
            AND vp.hospmain NOT IN (SELECT hospcode FROM htp_report.lookup_hospcode WHERE in_province = "Y")            
			AND o.vstdate BETWEEN ? AND ?
            GROUP BY o.vn ORDER BY o.vstdate,o.vsttime',[$start_date,$end_date]);

        return view('hrims.mishos.ucs_ae',compact('start_date','end_date','month','claim_price','receive_total','search'));
    }

}
