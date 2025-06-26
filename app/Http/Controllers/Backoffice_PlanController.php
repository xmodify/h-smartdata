<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Backoffice_PlanController extends Controller
{
//Check Login
public function __construct()
{
    $this->middleware('auth');
}

//Create index
public function index()
{

    return view('backoffice_plan.index');            
}

//Create service
public function service(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d');}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

    $opd_vn = DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS visit,
        SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
        SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
        SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
        SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
        SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
        SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
        SUM(CASE WHEN (paidst IN ("01","03") OR pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
        FROM (SELECT o.vstdate,o.vn,o.pttype,p.hipdata_code,p.paidst,p.pcode FROM ovst o 
        LEFT JOIN pttype p ON p.pttype=o.pttype  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY o.vn) AS a '); 
    $opd_hn = DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS visit,
        SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
        SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
        SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
        SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
        SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
        SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
        SUM(CASE WHEN (paidst IN ("01","03") OR pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
        FROM (SELECT o.vstdate,o.vn,o.pttype,p.hipdata_code,p.paidst,p.pcode FROM ovst o 
        LEFT JOIN pttype p ON p.pttype=o.pttype  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY o.hn) AS a '); 
    $refer_vn = DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS visit,
        SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
        SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
        SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
        SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
        SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
        SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
        SUM(CASE WHEN (paidst IN ("01","03") OR pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
        FROM (SELECT o.vstdate,o.vn,o.pttype,p.hipdata_code,p.paidst,p.pcode FROM ovst o
        INNER JOIN referout r ON r.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=o.pttype  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY o.vn) AS a ');
    $refer_hn = DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS visit,
        SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
        SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
        SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
        SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
        SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
        SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
        SUM(CASE WHEN (paidst IN ("01","03") OR pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
        FROM (SELECT o.vstdate,o.vn,o.pttype,p.hipdata_code,p.paidst,p.pcode FROM ovst o 
        INNER JOIN referout r ON r.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=o.pttype  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY o.hn) AS a '); 
    $er_vn = DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS visit,
        SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
        SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
        SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
        SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
        SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
        SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
        SUM(CASE WHEN (paidst IN ("01","03") OR pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
        FROM (SELECT o.vstdate,o.vn,o.pttype,p.hipdata_code,p.paidst,p.pcode FROM ovst o
        INNER JOIN er_regist e ON e.vn=o.vn 
        LEFT JOIN pttype p ON p.pttype=o.pttype  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY o.vn) AS a ');
    $er_hn = DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS visit,
        SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
        SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
        SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
        SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
        SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
        SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
        SUM(CASE WHEN (paidst IN ("01","03") OR pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
        FROM (SELECT o.vstdate,o.vn,o.pttype,p.hipdata_code,p.paidst,p.pcode FROM ovst o 
        INNER JOIN er_regist e ON e.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=o.pttype  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY o.hn) AS a '); 
    $dmht_vn = DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS visit,
        SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
        SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
        SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
        SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
        SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
        SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
        SUM(CASE WHEN (paidst IN ("01","03") OR pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
        FROM (SELECT o.vstdate,o.vn,o.pttype,p.hipdata_code,p.paidst,p.pcode FROM ovst o
        INNER JOIN clinic_visit c ON c.vn=o.vn AND c.clinic IN ("001","002")
        LEFT JOIN pttype p ON p.pttype=o.pttype  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY o.vn) AS a ');
    $dmht_hn = DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS visit,
        SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
        SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
        SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
        SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
        SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
        SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
        SUM(CASE WHEN (paidst IN ("01","03") OR pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
        FROM (SELECT o.vstdate,o.vn,o.pttype,p.hipdata_code,p.paidst,p.pcode FROM ovst o 
        INNER JOIN clinic_visit c ON c.vn=o.vn AND c.clinic IN ("001","002")
        LEFT JOIN pttype p ON p.pttype=o.pttype  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY o.hn) AS a '); 
    $physic_vn = DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS visit,
        SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
        SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
        SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
        SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
        SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
        SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
        SUM(CASE WHEN (paidst IN ("01","03") OR pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
        FROM (SELECT o.vstdate,o.vn,o.pttype,p.hipdata_code,p.paidst,p.pcode FROM ovst o
        INNER JOIN physic_list ps ON ps.vn=o.vn 
        LEFT JOIN pttype p ON p.pttype=o.pttype  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY o.vn) AS a ');
    $physic_hn = DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS visit,
        SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
        SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
        SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
        SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
        SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
        SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
        SUM(CASE WHEN (paidst IN ("01","03") OR pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
        FROM (SELECT o.vstdate,o.vn,o.pttype,p.hipdata_code,p.paidst,p.pcode FROM ovst o 
        INNER JOIN physic_list ps ON ps.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=o.pttype  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY o.hn) AS a ');  
    $healthmed_vn = DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS visit,
        SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
        SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
        SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
        SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
        SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
        SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
        SUM(CASE WHEN (paidst IN ("01","03") OR pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
        FROM (SELECT o.vstdate,o.vn,o.pttype,p.hipdata_code,p.paidst,p.pcode 
        FROM ovst o
        INNER JOIN health_med_service h ON h.vn=o.vn 
		LEFT JOIN visit_pttype vp ON vp.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=vp.pttype
        LEFT JOIN health_med_service_operation h1 ON h1.health_med_service_id=h.health_med_service_id
        WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND (h1.health_med_operation_item_id <> "" OR h1.health_med_operation_item_id IS NOT NULL)
        GROUP BY o.vn) AS a ');
    $healthmed_hn = DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS visit,
        SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
        SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
        SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
        SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
        SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
        SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
        SUM(CASE WHEN (paidst IN ("01","03") OR pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
        FROM (SELECT o.vstdate,o.vn,o.pttype,p.hipdata_code,p.paidst,p.pcode FROM ovst o 
        INNER JOIN health_med_service h ON h.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=o.pttype  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY o.hn) AS a '); 
    $dent_vn = DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS visit,
        SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
        SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
        SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
        SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
        SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
        SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
        SUM(CASE WHEN (paidst IN ("01","03") OR pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
        FROM (SELECT o.vstdate,o.vn,o.pttype,p.hipdata_code,p.paidst,p.pcode FROM ovst o
        INNER JOIN dtmain d ON d.vn=o.vn 
        LEFT JOIN pttype p ON p.pttype=o.pttype  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY o.vn) AS a ');
    $dent_hn = DB::connection('hosxp')->select('
        SELECT COUNT(vn) AS visit,
        SUM(CASE WHEN hipdata_code IN ("UCS","DIS") THEN 1 ELSE 0 END) AS "ucs",
        SUM(CASE WHEN pttype like "O%" OR pttype like "B%" OR pttype IN("14","H1") THEN 1 ELSE 0 END) AS "ofc",
        SUM(CASE WHEN hipdata_code in ("SSS","SSI") THEN 1 ELSE 0 END) AS "sss",
        SUM(CASE WHEN pttype like "L%" OR pttype ="H2" THEN 1 ELSE 0 END) AS "lgo",
        SUM(CASE WHEN hipdata_code like "NR%" THEN 1 ELSE 0 END) AS "fss",
        SUM(CASE WHEN hipdata_code IN ("ST","STP") THEN 1 ELSE 0 END) AS "stp",          
        SUM(CASE WHEN (paidst IN ("01","03") OR pcode IN ("A1","A9")) THEN 1 ELSE 0 END) AS "pay"
        FROM (SELECT o.vstdate,o.vn,o.pttype,p.hipdata_code,p.paidst,p.pcode FROM ovst o 
        INNER JOIN dtmain d ON d.vn=o.vn
        LEFT JOIN pttype p ON p.pttype=o.pttype  WHERE o.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY o.hn) AS a '); 
    $ipd = DB::connection('hosxp')->select('
        SELECT COUNT(an) AS an, SUM(admdate) AS admdate,
        ROUND((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1)),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1))*60)/100,2) AS "active_bed",
        ROUND(SUM(adjrw),2) AS adjrw ,ROUND(SUM(adjrw)/COUNT(DISTINCT an),2) AS cmi
        FROM (SELECT i.dchdate,i.an,i.adjrw,a.admdate,i.pttype,p.hipdata_code FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        LEFT JOIN pttype p ON p.pttype=i.pttype
        WHERE a.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx NOT IN ("Z290","Z208")
        GROUP BY i.an) AS a'); 
    $ipd_ucs = DB::connection('hosxp')->select('
        SELECT COUNT(an) AS an, SUM(admdate) AS admdate,
     ROUND((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1)),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1))*60)/100,2) AS "active_bed",
        ROUND(SUM(adjrw),2) AS adjrw ,ROUND(SUM(adjrw)/COUNT(DISTINCT an),2) AS cmi
        FROM (SELECT i.dchdate,i.an,i.adjrw,a.admdate,i.pttype,p.hipdata_code FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        LEFT JOIN pttype p ON p.pttype=i.pttype
        WHERE a.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx NOT IN ("Z290","Z208") AND p.hipdata_code IN ("UCS","DIS")
        GROUP BY i.an) AS a'); 
    $ipd_ofc = DB::connection('hosxp')->select('
        SELECT COUNT(an) AS an, SUM(admdate) AS admdate,
        ROUND((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1)),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1))*60)/100,2) AS "active_bed",
        ROUND(SUM(adjrw),2) AS adjrw ,ROUND(SUM(adjrw)/COUNT(DISTINCT an),2) AS cmi
        FROM (SELECT i.dchdate,i.an,i.adjrw,a.admdate,i.pttype,p.hipdata_code FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        LEFT JOIN pttype p ON p.pttype=i.pttype
        WHERE a.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx NOT IN ("Z290","Z208") AND (p.pttype like "O%" OR p.pttype like "B%" OR p.pttype IN("14","H1"))
        GROUP BY i.an) AS a'); 
    $ipd_sss = DB::connection('hosxp')->select('
        SELECT COUNT(an) AS an, SUM(admdate) AS admdate,
        ROUND((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1)),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1))*60)/100,2) AS "active_bed",
        ROUND(SUM(adjrw),2) AS adjrw ,ROUND(SUM(adjrw)/COUNT(DISTINCT an),2) AS cmi
        FROM (SELECT i.dchdate,i.an,i.adjrw,a.admdate,i.pttype,p.hipdata_code FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        LEFT JOIN pttype p ON p.pttype=i.pttype
        WHERE a.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx NOT IN ("Z290","Z208") AND p.hipdata_code IN ("SSS","SSI")
        GROUP BY i.an) AS a'); 
    $ipd_lgo = DB::connection('hosxp')->select('
        SELECT COUNT(an) AS an, SUM(admdate) AS admdate,
        ROUND((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1)),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1))*60)/100,2) AS "active_bed",
        ROUND(SUM(adjrw),2) AS adjrw ,ROUND(SUM(adjrw)/COUNT(DISTINCT an),2) AS cmi
        FROM (SELECT i.dchdate,i.an,i.adjrw,a.admdate,i.pttype,p.hipdata_code FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        LEFT JOIN pttype p ON p.pttype=i.pttype
        WHERE a.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx NOT IN ("Z290","Z208") AND (p.pttype like "L%" OR p.pttype ="H2")
        GROUP BY i.an) AS a'); 
    $ipd_fss = DB::connection('hosxp')->select('
        SELECT COUNT(an) AS an, SUM(admdate) AS admdate,
        ROUND((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1)),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1))*60)/100,2) AS "active_bed",
        ROUND(SUM(adjrw),2) AS adjrw ,ROUND(SUM(adjrw)/COUNT(DISTINCT an),2) AS cmi
        FROM (SELECT i.dchdate,i.an,i.adjrw,a.admdate,i.pttype,p.hipdata_code FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        LEFT JOIN pttype p ON p.pttype=i.pttype
        WHERE a.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx NOT IN ("Z290","Z208") AND p.hipdata_code like "NR%"
        GROUP BY i.an) AS a'); 
    $ipd_stp = DB::connection('hosxp')->select('
        SELECT COUNT(an) AS an, SUM(admdate) AS admdate,
        ROUND((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1)),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1))*60)/100,2) AS "active_bed",
        ROUND(SUM(adjrw),2) AS adjrw ,ROUND(SUM(adjrw)/COUNT(DISTINCT an),2) AS cmi
        FROM (SELECT i.dchdate,i.an,i.adjrw,a.admdate,i.pttype,p.hipdata_code FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        LEFT JOIN pttype p ON p.pttype=i.pttype
        WHERE a.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx NOT IN ("Z290","Z208") AND p.hipdata_code IN ("ST","STP")
        GROUP BY i.an) AS a'); 
    $ipd_pay = DB::connection('hosxp')->select('
        SELECT COUNT(an) AS an, SUM(admdate) AS admdate,
        ROUND((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1)),2) AS "bed_occupancy",
        ROUND(((SUM(admdate)*100)/(60*(DATEDIFF("'.$end_date.'","'.$start_date.'")+1))*60)/100,2) AS "active_bed",
        ROUND(SUM(adjrw),2) AS adjrw ,ROUND(SUM(adjrw)/COUNT(DISTINCT an),2) AS cmi
        FROM (SELECT i.dchdate,i.an,i.adjrw,a.admdate,i.pttype,p.hipdata_code FROM ipt i
        INNER JOIN an_stat a ON a.an=i.an
        LEFT JOIN pttype p ON p.pttype=i.pttype
        WHERE a.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        AND a.pdx NOT IN ("Z290","Z208") AND (p.paidst IN ("01","03") OR p.pcode IN ("A1","A9"))
        GROUP BY i.an) AS a');  

    return view('backoffice_plan.service',compact('start_date','end_date','opd_vn','opd_hn','refer_vn','refer_hn',
        'er_vn','er_hn','dmht_vn','dmht_hn','physic_vn','physic_hn','healthmed_vn','healthmed_hn','dent_vn',
        'dent_hn','ipd','ipd_ucs','ipd_ofc','ipd_sss','ipd_lgo','ipd_fss','ipd_stp','ipd_pay'));            
}

//Create diag
public function diag(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}
    
    $opddiag_top20 = DB::connection('hosxp')->select('select 
        concat("[",v.pdx,"] " ,i.name) as name,count(v.pdx) as sum , 
        sum(case when v.sex=1 THEN 1 ELSE 0 END) as male,   
        sum(case when v.sex=2 THEN 1 ELSE 0 END) as female   
        FROM vn_stat v   
        left outer join icd101 i on i.code=v.pdx 
        where v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"   
        and v.pdx<>"" AND v.pdx is not null and v.pdx not like "z%" and v.pdx NOT IN ("u119")
        group by v.pdx,i.name  
        order by sum desc limit 20');

    $ipddiag_top20 = DB::connection('hosxp')->select('select
        concat("[",v.pdx,"] " ,i.name) as name,count(v.pdx) as sum,
        sum(case when v.sex=1 THEN 1 ELSE 0 END) as male,
        sum(case when v.sex=2 THEN 1 ELSE 0 END) as female
        FROM an_stat v
        left outer join icd101 i on i.code=v.pdx
        where v.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        and v.pdx<>"" and v.pdx is not null and v.pdx not like "z%"
        AND v.pdx NOT IN ("Z290","Z208")
        group by v.pdx,i.name
        order by sum desc limit 20');

    $diag_504 = DB::connection('hosxp')->select('select 
        concat(a.name1," [",a.id,"]")as name,
        ifnull(d.male,0) as male,ifnull(d.female,0) as female,ifnull(d.amount,0) as sum  
        from rpt_504_name a 
        left join (select b.id,
        sum(case when v.sex=1 THEN 1 ELSE 0 END) as male,   
        sum(case when v.sex=2 THEN 1 ELSE 0 END) as female ,
        count(b.id) as amount 
        from rpt_504_code b,vn_stat v 
        where v.pdx between b.code1 and b.code2  
        and v.vstdate between "'.$start_date.'" AND "'.$end_date.'" 
        group by b.id) d on d.id=a.id 
        order by sum desc ');

    $diag_505 = DB::connection('hosxp')->select('select
        concat(a.name2," [",a.id,"]")as name,
        ifnull(d.male,0) as male,ifnull(d.female,0) as female,ifnull(d.amount,0) as sum
        from rpt_505_name a
        left join (select b.id,
        sum(case when v.sex=1 THEN 1 ELSE 0 END) as male,
        sum(case when v.sex=2 THEN 1 ELSE 0 END) as female ,
        count(b.id) as amount
        from rpt_505_code b ,an_stat v
        where v.pdx between b.code1 and b.code2
        and v.dchdate between "'.$start_date.'" AND "'.$end_date.'"
        AND v.pdx NOT IN ("Z290","Z208")
        group by b.id) d on d.id=a.id
        order by sum desc');

    $diag_506 = DB::connection('hosxp')->select('select 
        n.name as name , 
        sum(case when p.sex=1 THEN 1 ELSE 0 END) as male,   
        sum(case when p.sex=2 THEN 1 ELSE 0 END) as female,  
        COUNT(DISTINCT s.vn) as sum
        from surveil_member s   
        LEFT JOIN patient p on p.hn=s.hn  
        LEFT JOIN name506 n on n.code=s.code506 
        where s.report_date between "'.$start_date.'" AND "'.$end_date.'" 
        GROUP BY s.code506 ORDER BY sum DESC ');    
        
    $deathdiag_504 = DB::connection('hosxp')->select('select 
        IF(c1.name1="" OR c1.name1 IS NULL,"ไม่มีรหัสโรค",c1.name1) AS name,
        sum(case when pt.sex=1 THEN 1 ELSE 0 END) as male,   
        sum(case when pt.sex=2 THEN 1 ELSE 0 END) as female,   
        COUNT(DISTINCT d.hn) AS "sum"
        FROM death d
        LEFT OUTER JOIN patient pt ON pt.hn = d.hn
        LEFT OUTER JOIN rpt_504_name c1 ON c1.id = d.death_cause
        LEFT OUTER JOIN icd101 i1 ON i1.CODE = d.death_diag_1 
        WHERE	d.death_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY d.death_cause
        ORDER BY COUNT(DISTINCT d.hn) DESC');
        
    $deathdiag_icd10 = DB::connection('hosxp')->select('select 
        IF(CONCAT("[",d.death_diag_1,"] ",i1.NAME) ="" OR CONCAT("[",d.death_diag_1,"] ",i1.NAME) IS Null,
        "ไม่บันทึกรหัสโรค",CONCAT("[",d.death_diag_1,"] ",i1.NAME)) AS name,
        sum(case when pt.sex=1 THEN 1 ELSE 0 END) as male,   
        sum(case when pt.sex=2 THEN 1 ELSE 0 END) as female,   
        COUNT(DISTINCT d.hn) AS "sum"
        FROM death d
        LEFT OUTER JOIN patient pt ON pt.hn = d.hn
        LEFT OUTER JOIN rpt_504_name c1 ON c1.id = d.death_cause
        LEFT OUTER JOIN icd101 i1 ON i1.CODE = d.death_diag_1 
        WHERE	d.death_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY d.death_diag_1 
        ORDER BY COUNT(DISTINCT d.hn) DESC');
    
    return view('backoffice_plan.diag',compact('start_date','end_date','opddiag_top20',
        'ipddiag_top20','diag_504','diag_505','diag_506','deathdiag_504','deathdiag_icd10'));            
}

//Create death
public function death(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    if($start_date == '' || $end_date == null)
    {$start_date = date('Y-m-d', strtotime("first day of previous month"));}else{$start_date =$request->start_date;}
    if($end_date == '' || $end_date == null)
    {$end_date = date('Y-m-d', strtotime("last day of previous month"));}else{$end_date =$request->end_date;}
    
    $deathdiag_504 = DB::connection('hosxp')->select('select 
        IF(c1.name1="" OR c1.name1 IS NULL,"ไม่บันทึกรหัสโรค",c1.name1) AS name,
        sum(case when pt.sex=1 THEN 1 ELSE 0 END) as male,   
        sum(case when pt.sex=2 THEN 1 ELSE 0 END) as female,   
        COUNT(DISTINCT d.hn) AS "sum"
        FROM death d
        LEFT OUTER JOIN patient pt ON pt.hn = d.hn
        LEFT OUTER JOIN rpt_504_name c1 ON c1.id = d.death_cause
        LEFT OUTER JOIN icd101 i1 ON i1.CODE = d.death_diag_1 
        WHERE	d.death_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY d.death_cause
        ORDER BY COUNT(DISTINCT d.hn) DESC');
        
    $deathdiag_icd10 = DB::connection('hosxp')->select('select 
        IF(CONCAT("[",d.death_diag_1,"] ",i1.NAME) ="" OR CONCAT("[",d.death_diag_1,"] ",i1.NAME) IS Null,
        "ไม่บันทึกรหัสโรค",CONCAT("[",d.death_diag_1,"] ",i1.NAME)) AS name,
        sum(case when pt.sex=1 THEN 1 ELSE 0 END) as male,   
        sum(case when pt.sex=2 THEN 1 ELSE 0 END) as female,   
        COUNT(DISTINCT d.hn) AS "sum"
        FROM death d
        LEFT OUTER JOIN patient pt ON pt.hn = d.hn
        LEFT OUTER JOIN rpt_504_name c1 ON c1.id = d.death_cause
        LEFT OUTER JOIN icd101 i1 ON i1.CODE = d.death_diag_1 
        WHERE	d.death_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY d.death_diag_1 
        ORDER BY COUNT(DISTINCT d.hn) DESC');
    
    return view('backoffice_plan.death',compact('start_date','end_date','deathdiag_504','deathdiag_icd10'));            
}

//Create plan_project
public function plan_project(Request $request)
{
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;} 
    
    $plan_project_type1 = DB::connection('backoffice')->select('
        SELECT HR_DEPARTMENT_SUB_SUB_NAME,COUNT(PRO_NUMBER) AS project,
        SUM(CASE WHEN PRO_STATUS = "APP" THEN 1 ELSE 0 END) AS status_app,
        SUM(CASE WHEN PLAN_TRACKING_NAME = "สิ้นสุดโครงการ" THEN 1 ELSE 0 END) AS status_track,
        SUM(BUDGET_PICE) AS BUDGET_PICE,SUM(BUDGET_PICE_REAL) AS BUDGET_PICE_REAL,
        SUM(CASE WHEN BUDGET_ID = "1" THEN BUDGET_PICE ELSE 0 END) AS budget_1,
        SUM(CASE WHEN BUDGET_ID = "2" THEN BUDGET_PICE ELSE 0 END) AS budget_2,
        SUM(CASE WHEN BUDGET_ID = "3" THEN BUDGET_PICE ELSE 0 END) AS budget_3,
        SUM(CASE WHEN BUDGET_ID = "4" THEN BUDGET_PICE ELSE 0 END) AS budget_4,
        SUM(CASE WHEN BUDGET_ID = "5" THEN BUDGET_PICE ELSE 0 END) AS budget_5,
        SUM(CASE WHEN BUDGET_ID = "6" THEN BUDGET_PICE ELSE 0 END) AS budget_6,
        SUM(CASE WHEN BUDGET_ID IS NULL THEN BUDGET_PICE ELSE 0 END) AS nonbudget
        FROM(SELECT p.BUDGET_YEAR,p.PRO_NUMBER,p.PRO_TEAM_NAME,hrs.HR_DEPARTMENT_SUB_SUB_NAME,
        p.PRO_STATUS,p.BUDGET_ID,p.BUDGET_PICE,p.BUDGET_PICE_REAL,ptk.PLAN_TRACKING_NAME
        FROM plan_project p
        LEFT JOIN hrd_department_sub_sub hrs ON hrs.DEP_CODE=p.PRO_TEAM_NAME
        LEFT JOIN plan_tracking ptk ON ptk.PLAN_TRACKING_ID=p.PLAN_TRACKING_ID
        WHERE p.PLAN_TYPE_ID="1" AND p.BUDGET_YEAR = "'.$budget_year.'") AS a 
        GROUP BY PRO_TEAM_NAME ORDER BY BUDGET_PICE DESC');  
    $plan_project_type2 = DB::connection('backoffice')->select('
        SELECT HR_DEPARTMENT_SUB_SUB_NAME,COUNT(PRO_NUMBER) AS project,
        SUM(CASE WHEN PRO_STATUS = "APP" THEN 1 ELSE 0 END) AS status_app,
        SUM(CASE WHEN PLAN_TRACKING_NAME = "สิ้นสุดโครงการ" THEN 1 ELSE 0 END) AS status_track,
        SUM(BUDGET_PICE) AS BUDGET_PICE,SUM(BUDGET_PICE_REAL) AS BUDGET_PICE_REAL,
        SUM(CASE WHEN BUDGET_ID = "1" THEN BUDGET_PICE ELSE 0 END) AS budget_1,
        SUM(CASE WHEN BUDGET_ID = "2" THEN BUDGET_PICE ELSE 0 END) AS budget_2,
        SUM(CASE WHEN BUDGET_ID = "3" THEN BUDGET_PICE ELSE 0 END) AS budget_3,
        SUM(CASE WHEN BUDGET_ID = "4" THEN BUDGET_PICE ELSE 0 END) AS budget_4,
        SUM(CASE WHEN BUDGET_ID = "5" THEN BUDGET_PICE ELSE 0 END) AS budget_5,
        SUM(CASE WHEN BUDGET_ID = "6" THEN BUDGET_PICE ELSE 0 END) AS budget_6,
        SUM(CASE WHEN BUDGET_ID IS NULL THEN BUDGET_PICE ELSE 0 END) AS nonbudget
        FROM(SELECT p.BUDGET_YEAR,p.PRO_NUMBER,p.PRO_TEAM_NAME,hrs.HR_DEPARTMENT_SUB_SUB_NAME,
        p.PRO_STATUS,p.BUDGET_ID,p.BUDGET_PICE,p.BUDGET_PICE_REAL,ptk.PLAN_TRACKING_NAME 
        FROM plan_project p
        LEFT JOIN hrd_department_sub_sub hrs ON hrs.DEP_CODE=p.PRO_TEAM_NAME
        LEFT JOIN plan_tracking ptk ON ptk.PLAN_TRACKING_ID=p.PLAN_TRACKING_ID
        WHERE p.PLAN_TYPE_ID="2" AND p.BUDGET_YEAR = "'.$budget_year.'") AS a 
        GROUP BY PRO_TEAM_NAME ORDER BY BUDGET_PICE DESC');  
    $plan_project_detail = DB::connection('backoffice')->select('
        SELECT p.BUDGET_YEAR,p.PRO_NUMBER,p.PRO_TEAM_NAME,hrs.HR_DEPARTMENT_SUB_SUB_NAME,p.PRO_TEAM_HR_ID,p.PRO_TEAM_HR_NAME,p.PRO_NAME,
        pt.PLAN_TYPE_ID,pt.PLAN_TYPE_NAME,ps.STRATEGIC_ID,ps.STRATEGIC_NAME,ptg.TARGET_NAME,pk.KPI_NAME,s.BUDGET_ID,s.BUDGET_NAME,
        p.BUDGET_PICE,p.BUDGET_PICE_REAL,ptk.PLAN_TRACKING_NAME,CASE WHEN p.PRO_STATUS = "APP" THEN "อนุมัติ" WHEN p.PRO_STATUS = "NOTAPP"
        THEN "ไม่อนุมัติ" WHEN p.PRO_STATUS = "WAIT" THEN "รอพิจารณา" END AS "PRO_STATUS"
        FROM plan_project p
        LEFT JOIN plan_type pt ON pt.PLAN_TYPE_ID=p.PLAN_TYPE_ID
        LEFT JOIN plan_strategic ps ON ps.STRATEGIC_ID=p.STRATEGIC_ID
        LEFT JOIN plan_target ptg ON ptg.TARGET_ID=p.TARGET_ID
        LEFT JOIN plan_kpi pk ON pk.KPI_ID=p.KPI_ID
        LEFT JOIN supplies_budget s ON s.BUDGET_ID=p.BUDGET_ID
        LEFT JOIN plan_tracking ptk ON ptk.PLAN_TRACKING_ID=p.PLAN_TRACKING_ID
        LEFT JOIN hrd_department_sub_sub hrs ON hrs.DEP_CODE=p.PRO_TEAM_NAME
        WHERE p.BUDGET_YEAR = "'.$budget_year.'"'); 
        
    return view('backoffice_plan.plan_project',compact('budget_year','budget_year_select','plan_project_type1',
        'plan_project_type2','plan_project_detail'));            
}

}

