<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Drugcat_nhso;
use App\Models\Drugcat_aipn;
use Session;
use PDF;

class Hosxp_SettingController extends Controller
{
//Check Login
public function __construct()
{
    $this->middleware('auth')->except(['hosxp_backup']);
}
//index
public function index()
{
    return view('hosxp_setting.index');            
}
//หมวดค่ารักษาพยาบาล
public function income()
{
    $income =  DB::connection('hosxp')->select('
        SELECT i.income,i.`name`,n.nhso_adp_type_name,
        CONCAT(d.drg_chrgitem_id,"-",d.drg_chrgitem_name) AS eclaim
        FROM income i
        LEFT OUTER JOIN drg_chrgitem d ON d.drg_chrgitem_id = i.drg_chrgitem_id
        LEFT JOIN htp_report.lookup_nhso_adp_type n ON n.drg_chrgitem=d.drg_chrgitem_id
        ORDER BY i.income');

    return view('hosxp_setting.income',compact('income'));            
}
//รายการค่ารักษาพยาบาล
public function nondrug(Request $request)
{
    $income_select = DB::connection('hosxp')->select('select * from income');          
    $income = $request->income ?: '01';
    $income_name = DB::connection('hosxp')->table('income')->where('income',$income)->value('name'); 
    $nondrug =  DB::connection('hosxp')->select('
        SELECT n.istatus,n.icode,n.`name`,n.income AS income,n.price AS price,n.ipd_price AS ipd_price,
        n.istatus AS istatus,i.NAME AS income_name,n.use_paidst,p3.NAME AS paidst_name,n.lockprint,
        n.no_substock,n.unit,n.inv_map_update,n.billcode,n.billnumber,n.ucef_code,n.nhso_adp_code,
        nd2.nhso_adp_code_name,nd1.nhso_adp_type_name,d.drg_chrgitem_name,nd3.*
        FROM nondrugitems n
        LEFT OUTER JOIN income i ON i.income = n.income
        LEFT OUTER JOIN drg_chrgitem d ON d.drg_chrgitem_id = i.drg_chrgitem_id
        LEFT OUTER JOIN paidst p3 ON p3.paidst = n.paidst
        LEFT OUTER JOIN nhso_adp_type nd1 ON nd1.nhso_adp_type_id = n.nhso_adp_type_id
        LEFT OUTER JOIN nhso_adp_code nd2 ON nd2.nhso_adp_code = n.nhso_adp_code 
        LEFT OUTER JOIN htp_report.lookup_nhso_adp_code nd3 ON nd3.nhso_adp_code = n.nhso_adp_code 
        WHERE n.istatus = "Y" AND n.income = ? ORDER BY n.income,n.NAME',[$income]);

    return view('hosxp_setting.nondrug',compact('nondrug','income_select','income','income_name'));            
}
//ทะเบียน ADP Code (Eclaim)
public function adp_code(Request $request)
{
    $adp_type_select = DB::connection('hosxp')->select('select * from htp_report.lookup_nhso_adp_type');          
    $adp_type = $request->adp_type;
    if($adp_type = '' || $adp_type == null)
    {$adp_type ='10';}else{$adp_type =$request->adp_type;} 
    $adp_type_name = DB::connection('hosxp')->table('htp_report.lookup_nhso_adp_type')->where('nhso_adp_type_id',$adp_type)->value('nhso_adp_type_name'); 
    $adp_code =  DB::connection('hosxp')->select('
        SELECT c.nhso_adp_code,c.nhso_adp_code_name,c.ofc,c.lgo,c.sss,c.ucs,c.ucep,c.fs,c.ppfs,c.moph,
        ct.nhso_adp_type_name,d.drg_chrgitem_name,i.`name` AS income,
        IF((n.nhso_adp_code IS NOT NULL OR n.nhso_adp_code <>""),CONCAT(n.icode,"-",n.`name`,"[",ROUND(n.price,2),"]"),"") AS hosxp
        FROM htp_report.lookup_nhso_adp_code c
        LEFT JOIN htp_report.lookup_nhso_adp_type ct ON ct.nhso_adp_type_id=c.nhso_adp_type_id
        LEFT JOIN drg_chrgitem d ON d.drg_chrgitem_id=ct.drg_chrgitem
        LEFT JOIN nondrugitems n ON n.nhso_adp_code=c.nhso_adp_code
        LEFT JOIN income i ON i.income=n.income 
        WHERE c.nhso_adp_type_id =  "'.$adp_type.'" 
        ORDER BY c.nhso_adp_code ');

    return view('hosxp_setting.adp_code',compact('adp_code','adp_type_select','adp_type','adp_type_name'));            
}
//หัตถการผู้ป่วยนอก (OPD,ER)
public function icd9_opd()
{
    $icd9 =  DB::connection('hosxp')->select('
        SELECT e.er_oper_code,e.`name`,e.price,n.price AS unitprice,CONCAT(i1.`code`,"-",i1.`Name`) AS icd9,
        n.istatus,CONCAT(n.icode,"-",n.`name`) AS item_name,CONCAT(i2.income,"-",i2.`name`) AS income,
        CONCAT(n1.nhso_adp_code,"-",n1.nhso_adp_code_name) AS adp,e.export_proced
        FROM er_oper_code e
        LEFT OUTER JOIN nondrugitems n ON n.icode=e.icode
        LEFT OUTER JOIN nhso_adp_code n1 ON n1.nhso_adp_code=n.nhso_adp_code
        LEFT OUTER JOIN icd9cm1 i1 ON i1.`code` = e.icd9cm
        LEFT OUTER JOIN income i2 ON i2.income = n.income
        WHERE e.active_status ="Y" GROUP BY e.er_oper_code ORDER BY e.`name`');
    $icd9_non_active =  DB::connection('hosxp')->select('
        SELECT e.er_oper_code,e.`name`,e.price,n.price AS unitprice,CONCAT(i1.`code`,"-",i1.`Name`) AS icd9,
        n.istatus,CONCAT(n.icode,"-",n.`name`) AS item_name,CONCAT(i2.income,"-",i2.`name`) AS income,
        CONCAT(n1.nhso_adp_code,"-",n1.nhso_adp_code_name) AS adp,e.export_proced
        FROM er_oper_code e
        LEFT OUTER JOIN nondrugitems n ON n.icode=e.icode
        LEFT OUTER JOIN nhso_adp_code n1 ON n1.nhso_adp_code=n.nhso_adp_code
        LEFT OUTER JOIN icd9cm1 i1 ON i1.`code` = e.icd9cm
        LEFT OUTER JOIN income i2 ON i2.income = n.income
        WHERE e.active_status <>"Y" GROUP BY e.er_oper_code ORDER BY e.`name`');

    return view('hosxp_setting.icd9_opd',compact('icd9','icd9_non_active'));            
}
//หัตถการผู้ป่วยใน
public function icd9_ipd()
{
    $icd9 =  DB::connection('hosxp')->select('
        SELECT i.ipt_oper_code,i.`name`,i.price,n.price AS unitprice,CONCAT(i1.`code`,"-",i1.`Name`) AS icd9,
        n.istatus,CONCAT(n.icode,"-",n.`name`) AS item_name,CONCAT(i2.income,"-",i2.`name`) AS income,
        CONCAT(n1.nhso_adp_code,"-",n1.nhso_adp_code_name) AS adp
        FROM ipt_oper_code i
        LEFT OUTER JOIN nondrugitems n ON n.icode=i.icode
        LEFT OUTER JOIN nhso_adp_code n1 ON n1.nhso_adp_code=n.nhso_adp_code
        LEFT OUTER JOIN icd9cm1 i1 ON i1.`code` = i.icd9cm
        LEFT OUTER JOIN income i2 ON i2.income = n.income
        WHERE i.active_status ="Y" GROUP BY i.ipt_oper_code ORDER BY i2.income,i.`name`');
    $icd9_non_active =  DB::connection('hosxp')->select('
        SELECT i.ipt_oper_code,i.`name`,i.price,n.price AS unitprice,CONCAT(i1.`code`,"-",i1.`Name`) AS icd9,
        n.istatus,CONCAT(n.icode,"-",n.`name`) AS item_name,CONCAT(i2.income,"-",i2.`name`) AS income,
        CONCAT(n1.nhso_adp_code,"-",n1.nhso_adp_code_name) AS adp
        FROM ipt_oper_code i
        LEFT OUTER JOIN nondrugitems n ON n.icode=i.icode
        LEFT OUTER JOIN nhso_adp_code n1 ON n1.nhso_adp_code=n.nhso_adp_code
        LEFT OUTER JOIN icd9cm1 i1 ON i1.`code` = i.icd9cm
        LEFT OUTER JOIN income i2 ON i2.income = n.income
        WHERE i.active_status <>"Y" GROUP BY i.ipt_oper_code ORDER BY i2.income,i.`name`');

    return view('hosxp_setting.icd9_ipd',compact('icd9','icd9_non_active'));            
}
//หัตถการทันตกรรม
public function icd9_dent()
{
    $icd9 =  DB::connection('hosxp')->select('
        SELECT g.NAME AS dttm_group_name,d.code,d.`name` AS dttm_name,d.opd_price1 AS dttm_price,
        n.price AS n_price,CONCAT(n.icode,"-",n.`name`) AS item_name,n.istatus,
        CONCAT(i.income,"-",i.`name`) AS income,CONCAT(i1.`code`,"-",i1.`Name`) AS icd9,
        d.icd10tm_operation_code,d.icd10,d.export_proced,CONCAT(n1.nhso_adp_code,"-",n1.nhso_adp_code_name) AS adp
        FROM dttm d
        LEFT OUTER JOIN dttm_group g ON g.dttm_group_id = d.dttm_group_id
        LEFT OUTER JOIN icd10tm_operation tm ON tm.icd10tm_operation_code = d.icd10tm_operation_code 
        LEFT OUTER JOIN icd9cm1 i1 ON i1.`code` = d.icd9cm
        LEFT OUTER JOIN nondrugitems n ON n.icode = d.icode
        LEFT OUTER JOIN nhso_adp_code n1 ON n1.nhso_adp_code=n.nhso_adp_code
        LEFT OUTER JOIN income i ON i.income = n.income
        WHERE d.active_status = "Y" ORDER BY d.dttm_group_id');
    $icd9_non_active =  DB::connection('hosxp')->select('
        SELECT g.NAME AS dttm_group_name,d.code,d.`name` AS dttm_name,d.opd_price1 AS dttm_price,
        n.price AS n_price,CONCAT(n.icode,"-",n.`name`) AS item_name,n.istatus,
        CONCAT(i.income,"-",i.`name`) AS income,CONCAT(i1.`code`,"-",i1.`Name`) AS icd9,
        d.icd10tm_operation_code,d.icd10,d.export_proced,CONCAT(n1.nhso_adp_code,"-",n1.nhso_adp_code_name) AS adp
        FROM dttm d
        LEFT OUTER JOIN dttm_group g ON g.dttm_group_id = d.dttm_group_id
        LEFT OUTER JOIN icd10tm_operation tm ON tm.icd10tm_operation_code = d.icd10tm_operation_code 
        LEFT OUTER JOIN icd9cm1 i1 ON i1.`code` = d.icd9cm
        LEFT OUTER JOIN nondrugitems n ON n.icode = d.icode
        LEFT OUTER JOIN nhso_adp_code n1 ON n1.nhso_adp_code=n.nhso_adp_code
        LEFT OUTER JOIN income i ON i.income = n.income
        WHERE d.active_status <> "Y" ORDER BY d.dttm_group_id');

    return view('hosxp_setting.icd9_dent',compact('icd9','icd9_non_active'));            
}
#############################################################################################################################
//ตรวจสอบ Drug Catalog

public function drug_cat_nhso_save(Request $request)
{
    // Set the execution time to 300 seconds (5 minutes)
    set_time_limit(300);

    Drugcat_nhso::truncate(); 

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
        $row_range    = range( '2', $row_limit );
        // $row_range    = range( "!", $row_limit );
        $column_range = range( 'Y', $column_limit );
        $startcount = '2';
        // $row_range_namefile  = range( 9, $sheet->getCell( 'A' . $row )->getValue() );
        $data = array();
        foreach ($row_range as $row ) {

            $dc = $sheet->getCell( 'S' . $row )->getValue();          
            $dcday = substr($dc, 0, 2);
            $dcmo = substr($dc, 3, 2);
            $dcyear = substr($dc, 8, 4);  
            $datechange = $dcyear.'-'.$dcmo.'-'.$dcday;  

            $du = $sheet->getCell( 'T' . $row )->getValue();          
            $duday = substr($du, 0, 2);
            $dumo = substr($du, 3, 2);
            $duyear = substr($du, 8, 4);  
            $dateupdate = $duyear.'-'.$dumo.'-'.$duday;    
            
            $de = $sheet->getCell( 'U' . $row )->getValue();          
            $deday = substr($de, 0, 2);
            $demo = substr($de, 3, 2);
            $deyear = substr($de, 8, 4);  
            $dateeffective = $deyear.'-'.$demo.'-'.$deday;      

            $da = $sheet->getCell( 'X' . $row )->getValue();          
            $daday = substr($da, 0, 2);
            $damo = substr($da, 3, 2);
            $dayear = substr($da, 8, 4);  
            $date_approved = $dayear.'-'.$damo.'-'.$daday;    

                $data[] = [
                    'hospdrugcode'      =>$sheet->getCell( 'A' . $row )->getValue(),
                    'productcat'        =>$sheet->getCell( 'B' . $row )->getValue(),
                    'tmtid'             =>$sheet->getCell( 'C' . $row )->getValue(),
                    'specprep'          =>$sheet->getCell( 'D' . $row )->getValue(),
                    'genericname'       =>$sheet->getCell( 'E' . $row )->getValue(),
                    'tradename'         =>$sheet->getCell( 'F' . $row )->getValue(),
                    'dfscode'           =>$sheet->getCell( 'G' . $row )->getValue(),
                    'dosageform'        =>$sheet->getCell( 'H' . $row )->getValue(),
                    'strength'          =>$sheet->getCell( 'I' . $row )->getValue(),
                    'content'           =>$sheet->getCell( 'J' . $row )->getValue(),
                    'unitprice'         =>$sheet->getCell( 'K' . $row )->getValue(),
                    'distributor'       =>$sheet->getCell( 'L' . $row )->getValue(),
                    'manufacturer'      =>$sheet->getCell( 'M' . $row )->getValue(),
                    'ised'              =>$sheet->getCell( 'N' . $row )->getValue(),
                    'ndc24'             =>$sheet->getCell( 'O' . $row )->getValue(),
                    'packsize'          =>$sheet->getCell( 'P' . $row )->getValue(),
                    'packprice'         =>$sheet->getCell( 'Q' . $row )->getValue(),
                    'updateflag'        =>$sheet->getCell( 'R' . $row )->getValue(),
                    'datechange'        =>$datechange,
                    'dateupdate'        =>$dateupdate,
                    'dateeffective'     =>$dateeffective,
                    'ised_approved'     =>$sheet->getCell( 'V' . $row )->getValue(),
                    'ndc24_approved'    =>$sheet->getCell( 'W' . $row )->getValue(),
                    'date_approved'     =>$date_approved,
                    'ised_status'       =>$sheet->getCell( 'Y' . $row )->getValue(),
                    'stm_filename'      =>$file_name,
                ]; 
            $startcount++;            
        }

        $for_insert = array_chunk($data, 1000);
        foreach ($for_insert as $key => $data_) {
            Drugcat_nhso::insert($data_);                 
        }
    }     

    catch (Exception $e) {
        $error_code = $e->errorInfo[1];
        return back()->withErrors('There was a problem uploading the data!');
    }       
        
    return redirect()->route('drug_cat')->with('success',$file_name);
}

public function drug_cat_aipn_save(Request $request)
{
    // Set the execution time to 300 seconds (5 minutes)
    set_time_limit(300);

    Drugcat_aipn::truncate(); 

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
        $row_range    = range( '5', $row_limit );
        // $row_range    = range( "!", $row_limit );
        $column_range = range( 'X', $column_limit );
        $startcount = '5';
        // $row_range_namefile  = range( 9, $sheet->getCell( 'A' . $row )->getValue() );
        $data = array();
        foreach ($row_range as $row ) {

            $dc = $sheet->getCell( 'T' . $row )->getValue();          
            $dcday = substr($dc, 0, 2);
            $dcmo = substr($dc, 3, 2);
            $dcyear = substr($dc, 6, 4);  
            $dcadmtime = substr($dc, 12, 7);
            $DateChange = $dcyear .'-' .$dcmo .'-' .$dcday .' ' .$dcadmtime;  

            $du = $sheet->getCell( 'U' . $row )->getValue();          
            $duday = substr($du, 0, 2);
            $dumo = substr($du, 3, 2);
            $duyear = substr($du, 6, 4);  
            $duadmtime = substr($du, 12, 7);
            $DateUpdate = $duyear .'-' .$dumo .'-' .$duday .' ' .$duadmtime;    
            
            $de = $sheet->getCell( 'V' . $row )->getValue();          
            $deday = substr($de, 0, 2);
            $demo = substr($de, 3, 2);
            $deyear = substr($de, 6, 4);  
            $deadmtime = substr($de, 12, 7);
            $DateEffect = $deyear .'-' .$demo .'-' .$deday .' ' .$deadmtime;    

            $da = $sheet->getCell( 'W' . $row )->getValue();          
            $daday = substr($da, 0, 2);
            $damo = substr($da, 3, 2);
            $dayear = substr($da, 6, 4);  
            $daadmtime = substr($da, 12, 7);
            $datechk = $dayear .'-' .$damo .'-' .$daday .' ' .$daadmtime;    

                $data[] = [
                    'id'                =>$sheet->getCell( 'A' . $row )->getValue(),
                    'Hospdcode'         =>$sheet->getCell( 'B' . $row )->getValue(),
                    'Prodcat'           =>$sheet->getCell( 'C' . $row )->getValue(),
                    'Tmtid'             =>$sheet->getCell( 'D' . $row )->getValue(),
                    'Specprep'          =>$sheet->getCell( 'E' . $row )->getValue(),
                    'Genname'           =>$sheet->getCell( 'F' . $row )->getValue(),
                    'Tradename'         =>$sheet->getCell( 'G' . $row )->getValue(),
                    'Dsfcode'           =>$sheet->getCell( 'H' . $row )->getValue(),
                    'Dosefm'            =>$sheet->getCell( 'I' . $row )->getValue(),
                    'Strength'          =>$sheet->getCell( 'J' . $row )->getValue(),
                    'Content'           =>$sheet->getCell( 'K' . $row )->getValue(),
                    'UnitPrice'         =>$sheet->getCell( 'L' . $row )->getValue(),
                    'Distrb'            =>$sheet->getCell( 'M' . $row )->getValue(),
                    'Manuf'             =>$sheet->getCell( 'N' . $row )->getValue(),
                    'Ised'              =>$sheet->getCell( 'O' . $row )->getValue(),
                    'Ndc24'             =>$sheet->getCell( 'P' . $row )->getValue(),
                    'Packsize'          =>$sheet->getCell( 'Q' . $row )->getValue(),
                    'Packprice'         =>$sheet->getCell( 'R' . $row )->getValue(),
                    'Updateflag'        =>$sheet->getCell( 'S' . $row )->getValue(),
                    'DateChange'        =>$DateChange,
                    'DateUpdate'        =>$DateUpdate,
                    'DateEffect'        =>$DateEffect,
                    'DateChk'           =>$datechk,
                    'Rp'                =>$sheet->getCell( 'X' . $row )->getValue(),
                    'stm_filename'      =>$file_name,
                ]; 
            $startcount++;            
        }

        $for_insert = array_chunk($data, 1000);
        foreach ($for_insert as $key => $data_) {            
            Drugcat_aipn::insert($data_);                 
        }
    }     

    catch (Exception $e) {
        $error_code = $e->errorInfo[1];
        return back()->withErrors('There was a problem uploading the data!');
    }       
        
    return redirect()->route('drug_cat')->with('success',$file_name);
}

public function drug_cat()
{
    $drug =  DB::connection('hosxp')->select('
        SELECT  d.icode,CONCAT(d.`name`,SPACE(1),d.strength) AS dname,d.units,IF(nd.hospdrugcode IS NULL,"N","Y") AS chk_nhso_drugcat,
        IF(ad.hospdcode IS NULL,"N","Y") AS chk_aipn_drugcat,d.unitprice AS price_hos,nd.unitprice AS price_nhso,
        ad.unitprice AS price_aipn,d3.ref_code AS code_tmt_hos,nd.tmtid AS code_tmt_nhso,ad.tmtid AS code_tmt_aipn,
        d2.ref_code AS code_24_hos,nd.ndc24 AS code_24_nhso,ad.ndc24 AS code_24_aipn,i.NAME AS income_name,  
        CASE WHEN (d.drugaccount = "-" OR d.drugaccount = "") THEN"N" WHEN drugaccount <> "" THEN "E" END AS ISED,d.drugaccount,
		IFNULL(d.generic_name,d.`name`) AS GenericName,IFNULL(d.trade_name,s.TradeName) AS TradeName,IFNULL(d.dosageform,s.DosageForm) AS DosageForm     
        FROM drugitems d
        LEFT JOIN income i ON i.income = d.income
        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=1
        LEFT JOIN drugitems_ref_code d3 ON d3.icode=d.icode AND d3.drugitems_ref_code_type_id=3
        LEFT JOIN sks_drugcatalog s ON s.HospDrugCode=d.icode
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_nhso dc WHERE  dc.date_approved = (SELECT MAX(dc1.date_approved) 
		    FROM htp_report.drugcat_nhso dc1 WHERE dc.hospdrugcode=dc1.hospdrugcode AND dc1.updateflag IN ("A","U","E"))) nd ON nd.hospdrugcode=d.icode 
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_aipn dc WHERE  dc.DateUpdate = (SELECT MAX(dc1.DateUpdate) 
			FROM htp_report.drugcat_aipn dc1 WHERE dc.Hospdcode=dc1.Hospdcode AND dc1.Updateflag IN ("A","U","E"))) ad ON ad.hospdcode=d.icode 
        WHERE d.istatus = "Y" AND d.`name` NOT LIKE "*%"  
		ORDER BY d.NAME,d.strength,d.units');

    return view('hosxp_setting.drug_cat',compact('drug'));            
}
//ตรวจสอบ Drug Catalog
public function drug_cat_non_nhso()
{
    $drug =  DB::connection('hosxp')->select('
        SELECT  d.icode,CONCAT(d.`name`,SPACE(1),d.strength) AS dname,d.units,IF(nd.hospdrugcode IS NULL,"N","Y") AS chk_nhso_drugcat,
        IF(ad.hospdcode IS NULL,"N","Y") AS chk_aipn_drugcat,d.unitprice AS price_hos,nd.unitprice AS price_nhso,
        ad.unitprice AS price_aipn,d3.ref_code AS code_tmt_hos,nd.tmtid AS code_tmt_nhso,ad.tmtid AS code_tmt_aipn,
        d2.ref_code AS code_24_hos,nd.ndc24 AS code_24_nhso,ad.ndc24 AS code_24_aipn,i.NAME AS income_name,  
        CASE WHEN (d.drugaccount = "-" OR d.drugaccount = "") THEN"N" WHEN drugaccount <> "" THEN "E" END AS ISED,d.drugaccount,
		IFNULL(d.generic_name,d.`name`) AS GenericName,IFNULL(d.trade_name,s.TradeName) AS TradeName,IFNULL(d.dosageform,s.DosageForm) AS DosageForm     
        FROM drugitems d
        LEFT JOIN income i ON i.income = d.income
        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=1
        LEFT JOIN drugitems_ref_code d3 ON d3.icode=d.icode AND d3.drugitems_ref_code_type_id=3
        LEFT JOIN sks_drugcatalog s ON s.HospDrugCode=d.icode
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_nhso dc WHERE  dc.date_approved = (SELECT MAX(dc1.date_approved) 
		    FROM htp_report.drugcat_nhso dc1 WHERE dc.hospdrugcode=dc1.hospdrugcode AND dc1.updateflag IN ("A","U","E"))) nd ON nd.hospdrugcode=d.icode 
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_aipn dc WHERE  dc.DateUpdate = (SELECT MAX(dc1.DateUpdate) 
			FROM htp_report.drugcat_aipn dc1 WHERE dc.Hospdcode=dc1.Hospdcode AND dc1.Updateflag IN ("A","U","E"))) ad ON ad.hospdcode=d.icode 
        WHERE d.istatus = "Y" AND d.`name` NOT LIKE "*%" AND nd.hospdrugcode IS NULL  
		ORDER BY d.NAME,d.strength,d.units');

    return view('hosxp_setting.drug_cat',compact('drug'));            
}
//ตรวจสอบ Drug Catalog
public function drug_cat_nhso_price_notmatch_hosxp()
{
    $drug =  DB::connection('hosxp')->select('
        SELECT  d.icode,CONCAT(d.`name`,SPACE(1),d.strength) AS dname,d.units,IF(nd.hospdrugcode IS NULL,"N","Y") AS chk_nhso_drugcat,
        IF(ad.hospdcode IS NULL,"N","Y") AS chk_aipn_drugcat,d.unitprice AS price_hos,nd.unitprice AS price_nhso,
        ad.unitprice AS price_aipn,d3.ref_code AS code_tmt_hos,nd.tmtid AS code_tmt_nhso,ad.tmtid AS code_tmt_aipn,
        d2.ref_code AS code_24_hos,nd.ndc24 AS code_24_nhso,ad.ndc24 AS code_24_aipn,i.NAME AS income_name,  
        CASE WHEN (d.drugaccount = "-" OR d.drugaccount = "") THEN"N" WHEN drugaccount <> "" THEN "E" END AS ISED,d.drugaccount,
		IFNULL(d.generic_name,d.`name`) AS GenericName,IFNULL(d.trade_name,s.TradeName) AS TradeName,IFNULL(d.dosageform,s.DosageForm) AS DosageForm     
        FROM drugitems d
        LEFT JOIN income i ON i.income = d.income
        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=1
        LEFT JOIN drugitems_ref_code d3 ON d3.icode=d.icode AND d3.drugitems_ref_code_type_id=3
        LEFT JOIN sks_drugcatalog s ON s.HospDrugCode=d.icode
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_nhso dc WHERE  dc.date_approved = (SELECT MAX(dc1.date_approved) 
		    FROM htp_report.drugcat_nhso dc1 WHERE dc.hospdrugcode=dc1.hospdrugcode AND dc1.updateflag IN ("A","U","E"))) nd ON nd.hospdrugcode=d.icode 
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_aipn dc WHERE  dc.DateUpdate = (SELECT MAX(dc1.DateUpdate) 
			FROM htp_report.drugcat_aipn dc1 WHERE dc.Hospdcode=dc1.Hospdcode AND dc1.Updateflag IN ("A","U","E"))) ad ON ad.hospdcode=d.icode 
        WHERE d.istatus = "Y" AND d.`name` NOT LIKE "*%" AND nd.unitprice <> d.unitprice
		ORDER BY d.NAME,d.strength,d.units');

    return view('hosxp_setting.drug_cat',compact('drug'));            
}
//ตรวจสอบ Drug Catalog
public function drug_cat_nhso_tmt_notmatch_hosxp()
{
    $drug =  DB::connection('hosxp')->select('
        SELECT  d.icode,CONCAT(d.`name`,SPACE(1),d.strength) AS dname,d.units,IF(nd.hospdrugcode IS NULL,"N","Y") AS chk_nhso_drugcat,
        IF(ad.hospdcode IS NULL,"N","Y") AS chk_aipn_drugcat,d.unitprice AS price_hos,nd.unitprice AS price_nhso,
        ad.unitprice AS price_aipn,d3.ref_code AS code_tmt_hos,nd.tmtid AS code_tmt_nhso,ad.tmtid AS code_tmt_aipn,
        d2.ref_code AS code_24_hos,nd.ndc24 AS code_24_nhso,ad.ndc24 AS code_24_aipn,i.NAME AS income_name,  
        CASE WHEN (d.drugaccount = "-" OR d.drugaccount = "") THEN"N" WHEN drugaccount <> "" THEN "E" END AS ISED,d.drugaccount,
		IFNULL(d.generic_name,d.`name`) AS GenericName,IFNULL(d.trade_name,s.TradeName) AS TradeName,IFNULL(d.dosageform,s.DosageForm) AS DosageForm     
        FROM drugitems d
        LEFT JOIN income i ON i.income = d.income
        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=1
        LEFT JOIN drugitems_ref_code d3 ON d3.icode=d.icode AND d3.drugitems_ref_code_type_id=3
        LEFT JOIN sks_drugcatalog s ON s.HospDrugCode=d.icode
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_nhso dc WHERE  dc.date_approved = (SELECT MAX(dc1.date_approved) 
		    FROM htp_report.drugcat_nhso dc1 WHERE dc.hospdrugcode=dc1.hospdrugcode AND dc1.updateflag IN ("A","U","E"))) nd ON nd.hospdrugcode=d.icode 
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_aipn dc WHERE  dc.DateUpdate = (SELECT MAX(dc1.DateUpdate) 
			FROM htp_report.drugcat_aipn dc1 WHERE dc.Hospdcode=dc1.Hospdcode AND dc1.Updateflag IN ("A","U","E"))) ad ON ad.hospdcode=d.icode 
        WHERE d.istatus = "Y" AND d.`name` NOT LIKE "*%" AND nd.tmtid <> d3.ref_code
		ORDER BY d.NAME,d.strength,d.units');

    return view('hosxp_setting.drug_cat',compact('drug'));            
}
//ตรวจสอบ Drug Catalog
public function drug_cat_nhso_code24_notmatch_hosxp()
{
    $drug =  DB::connection('hosxp')->select('
        SELECT  d.icode,CONCAT(d.`name`,SPACE(1),d.strength) AS dname,d.units,IF(nd.hospdrugcode IS NULL,"N","Y") AS chk_nhso_drugcat,
        IF(ad.hospdcode IS NULL,"N","Y") AS chk_aipn_drugcat,d.unitprice AS price_hos,nd.unitprice AS price_nhso,
        ad.unitprice AS price_aipn,d3.ref_code AS code_tmt_hos,nd.tmtid AS code_tmt_nhso,ad.tmtid AS code_tmt_aipn,
        d2.ref_code AS code_24_hos,nd.ndc24 AS code_24_nhso,ad.ndc24 AS code_24_aipn,i.NAME AS income_name,  
        CASE WHEN (d.drugaccount = "-" OR d.drugaccount = "") THEN"N" WHEN drugaccount <> "" THEN "E" END AS ISED,d.drugaccount,
		IFNULL(d.generic_name,d.`name`) AS GenericName,IFNULL(d.trade_name,s.TradeName) AS TradeName,IFNULL(d.dosageform,s.DosageForm) AS DosageForm     
        FROM drugitems d
        LEFT JOIN income i ON i.income = d.income
        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=1
        LEFT JOIN drugitems_ref_code d3 ON d3.icode=d.icode AND d3.drugitems_ref_code_type_id=3
        LEFT JOIN sks_drugcatalog s ON s.HospDrugCode=d.icode
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_nhso dc WHERE  dc.date_approved = (SELECT MAX(dc1.date_approved) 
		    FROM htp_report.drugcat_nhso dc1 WHERE dc.hospdrugcode=dc1.hospdrugcode AND dc1.updateflag IN ("A","U","E"))) nd ON nd.hospdrugcode=d.icode 
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_aipn dc WHERE  dc.DateUpdate = (SELECT MAX(dc1.DateUpdate) 
			FROM htp_report.drugcat_aipn dc1 WHERE dc.Hospdcode=dc1.Hospdcode AND dc1.Updateflag IN ("A","U","E"))) ad ON ad.hospdcode=d.icode 
        WHERE d.istatus = "Y" AND d.`name` NOT LIKE "*%" AND nd.ndc24 <> d2.ref_code
		ORDER BY d.NAME,d.strength,d.units');

    return view('hosxp_setting.drug_cat',compact('drug'));            
}
//ตรวจสอบ Drug Catalog
public function drug_cat_non_aipn()
{
    $drug =  DB::connection('hosxp')->select('
        SELECT  d.icode,CONCAT(d.`name`,SPACE(1),d.strength) AS dname,d.units,IF(nd.hospdrugcode IS NULL,"N","Y") AS chk_nhso_drugcat,
        IF(ad.hospdcode IS NULL,"N","Y") AS chk_aipn_drugcat,d.unitprice AS price_hos,nd.unitprice AS price_nhso,
        ad.unitprice AS price_aipn,d3.ref_code AS code_tmt_hos,nd.tmtid AS code_tmt_nhso,ad.tmtid AS code_tmt_aipn,
        d2.ref_code AS code_24_hos,nd.ndc24 AS code_24_nhso,ad.ndc24 AS code_24_aipn,i.NAME AS income_name,  
        CASE WHEN (d.drugaccount = "-" OR d.drugaccount = "") THEN"N" WHEN drugaccount <> "" THEN "E" END AS ISED,d.drugaccount,
		IFNULL(d.generic_name,d.`name`) AS GenericName,IFNULL(d.trade_name,s.TradeName) AS TradeName,IFNULL(d.dosageform,s.DosageForm) AS DosageForm     
        FROM drugitems d
        LEFT JOIN income i ON i.income = d.income
        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=1
        LEFT JOIN drugitems_ref_code d3 ON d3.icode=d.icode AND d3.drugitems_ref_code_type_id=3
        LEFT JOIN sks_drugcatalog s ON s.HospDrugCode=d.icode
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_nhso dc WHERE  dc.date_approved = (SELECT MAX(dc1.date_approved) 
		    FROM htp_report.drugcat_nhso dc1 WHERE dc.hospdrugcode=dc1.hospdrugcode AND dc1.updateflag IN ("A","U","E"))) nd ON nd.hospdrugcode=d.icode 
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_aipn dc WHERE  dc.DateUpdate = (SELECT MAX(dc1.DateUpdate) 
			FROM htp_report.drugcat_aipn dc1 WHERE dc.Hospdcode=dc1.Hospdcode AND dc1.Updateflag IN ("A","U","E"))) ad ON ad.hospdcode=d.icode 
        WHERE d.istatus = "Y" AND d.`name` NOT LIKE "*%" AND ad.hospdcode IS NULL
		ORDER BY d.NAME,d.strength,d.units');

    return view('hosxp_setting.drug_cat',compact('drug'));            
}
//ตรวจสอบ Drug Catalog
public function drug_cat_aipn_price_notmatch_hosxp()
{
    $drug =  DB::connection('hosxp')->select('
        SELECT  d.icode,CONCAT(d.`name`,SPACE(1),d.strength) AS dname,d.units,IF(nd.hospdrugcode IS NULL,"N","Y") AS chk_nhso_drugcat,
        IF(ad.hospdcode IS NULL,"N","Y") AS chk_aipn_drugcat,d.unitprice AS price_hos,nd.unitprice AS price_nhso,
        ad.unitprice AS price_aipn,d3.ref_code AS code_tmt_hos,nd.tmtid AS code_tmt_nhso,ad.tmtid AS code_tmt_aipn,
        d2.ref_code AS code_24_hos,nd.ndc24 AS code_24_nhso,ad.ndc24 AS code_24_aipn,i.NAME AS income_name,  
        CASE WHEN (d.drugaccount = "-" OR d.drugaccount = "") THEN"N" WHEN drugaccount <> "" THEN "E" END AS ISED,d.drugaccount,
		IFNULL(d.generic_name,d.`name`) AS GenericName,IFNULL(d.trade_name,s.TradeName) AS TradeName,IFNULL(d.dosageform,s.DosageForm) AS DosageForm     
        FROM drugitems d
        LEFT JOIN income i ON i.income = d.income
        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=1
        LEFT JOIN drugitems_ref_code d3 ON d3.icode=d.icode AND d3.drugitems_ref_code_type_id=3
        LEFT JOIN sks_drugcatalog s ON s.HospDrugCode=d.icode
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_nhso dc WHERE  dc.date_approved = (SELECT MAX(dc1.date_approved) 
		    FROM htp_report.drugcat_nhso dc1 WHERE dc.hospdrugcode=dc1.hospdrugcode AND dc1.updateflag IN ("A","U","E"))) nd ON nd.hospdrugcode=d.icode 
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_aipn dc WHERE  dc.DateUpdate = (SELECT MAX(dc1.DateUpdate) 
			FROM htp_report.drugcat_aipn dc1 WHERE dc.Hospdcode=dc1.Hospdcode AND dc1.Updateflag IN ("A","U","E"))) ad ON ad.hospdcode=d.icode 
        WHERE d.istatus = "Y" AND d.`name` NOT LIKE "*%" AND ad.unitprice <> d.unitprice
		ORDER BY d.NAME,d.strength,d.units');

    return view('hosxp_setting.drug_cat',compact('drug'));            
}
//ตรวจสอบ Drug Catalog
public function drug_cat_aipn_tmt_notmatch_hosxp()
{
    $drug =  DB::connection('hosxp')->select('
        SELECT  d.icode,CONCAT(d.`name`,SPACE(1),d.strength) AS dname,d.units,IF(nd.hospdrugcode IS NULL,"N","Y") AS chk_nhso_drugcat,
        IF(ad.hospdcode IS NULL,"N","Y") AS chk_aipn_drugcat,d.unitprice AS price_hos,nd.unitprice AS price_nhso,
        ad.unitprice AS price_aipn,d3.ref_code AS code_tmt_hos,nd.tmtid AS code_tmt_nhso,ad.tmtid AS code_tmt_aipn,
        d2.ref_code AS code_24_hos,nd.ndc24 AS code_24_nhso,ad.ndc24 AS code_24_aipn,i.NAME AS income_name,  
        CASE WHEN (d.drugaccount = "-" OR d.drugaccount = "") THEN"N" WHEN drugaccount <> "" THEN "E" END AS ISED,d.drugaccount,
		IFNULL(d.generic_name,d.`name`) AS GenericName,IFNULL(d.trade_name,s.TradeName) AS TradeName,IFNULL(d.dosageform,s.DosageForm) AS DosageForm     
        FROM drugitems d
        LEFT JOIN income i ON i.income = d.income
        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=1
        LEFT JOIN drugitems_ref_code d3 ON d3.icode=d.icode AND d3.drugitems_ref_code_type_id=3
        LEFT JOIN sks_drugcatalog s ON s.HospDrugCode=d.icode
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_nhso dc WHERE  dc.date_approved = (SELECT MAX(dc1.date_approved) 
		    FROM htp_report.drugcat_nhso dc1 WHERE dc.hospdrugcode=dc1.hospdrugcode AND dc1.updateflag IN ("A","U","E"))) nd ON nd.hospdrugcode=d.icode 
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_aipn dc WHERE  dc.DateUpdate = (SELECT MAX(dc1.DateUpdate) 
			FROM htp_report.drugcat_aipn dc1 WHERE dc.Hospdcode=dc1.Hospdcode AND dc1.Updateflag IN ("A","U","E"))) ad ON ad.hospdcode=d.icode 
        WHERE d.istatus = "Y" AND d.`name` NOT LIKE "*%" AND ad.tmtid <> d3.ref_code
		ORDER BY d.NAME,d.strength,d.units');

    return view('hosxp_setting.drug_cat',compact('drug'));            
}
//ตรวจสอบ Drug Catalog
public function drug_cat_aipn_code24_notmatch_hosxp()
{
    $drug =  DB::connection('hosxp')->select('
        SELECT  d.icode,CONCAT(d.`name`,SPACE(1),d.strength) AS dname,d.units,IF(nd.hospdrugcode IS NULL,"N","Y") AS chk_nhso_drugcat,
        IF(ad.hospdcode IS NULL,"N","Y") AS chk_aipn_drugcat,d.unitprice AS price_hos,nd.unitprice AS price_nhso,
        ad.unitprice AS price_aipn,d3.ref_code AS code_tmt_hos,nd.tmtid AS code_tmt_nhso,ad.tmtid AS code_tmt_aipn,
        d2.ref_code AS code_24_hos,nd.ndc24 AS code_24_nhso,ad.ndc24 AS code_24_aipn,i.NAME AS income_name,  
        CASE WHEN (d.drugaccount = "-" OR d.drugaccount = "") THEN"N" WHEN drugaccount <> "" THEN "E" END AS ISED,d.drugaccount,
		IFNULL(d.generic_name,d.`name`) AS GenericName,IFNULL(d.trade_name,s.TradeName) AS TradeName,IFNULL(d.dosageform,s.DosageForm) AS DosageForm     
        FROM drugitems d
        LEFT JOIN income i ON i.income = d.income
        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=1
        LEFT JOIN drugitems_ref_code d3 ON d3.icode=d.icode AND d3.drugitems_ref_code_type_id=3
        LEFT JOIN sks_drugcatalog s ON s.HospDrugCode=d.icode
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_nhso dc WHERE  dc.date_approved = (SELECT MAX(dc1.date_approved) 
		    FROM htp_report.drugcat_nhso dc1 WHERE dc.hospdrugcode=dc1.hospdrugcode AND dc1.updateflag IN ("A","U","E"))) nd ON nd.hospdrugcode=d.icode 
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_aipn dc WHERE  dc.DateUpdate = (SELECT MAX(dc1.DateUpdate) 
			FROM htp_report.drugcat_aipn dc1 WHERE dc.Hospdcode=dc1.Hospdcode AND dc1.Updateflag IN ("A","U","E"))) ad ON ad.hospdcode=d.icode 
        WHERE d.istatus = "Y" AND d.`name` NOT LIKE "*%" AND ad.ndc24 <> d2.ref_code
		ORDER BY d.NAME,d.strength,d.units');

    return view('hosxp_setting.drug_cat',compact('drug'));            
}
//ตรวจสอบ Drug Catalog
public function drug_cat_aipn_export()
{
    $drug =  DB::connection('hosxp')->select('        
        SELECT IF(ad.hospdcode IS NULL,"N","Y") AS chk_aipn_drugcat,
        d.icode AS HospDrugCode,
        d.sks_product_category_id AS ProductCat,
        d.sks_drug_code AS TMTID,
        "" AS SpecPrep,
        IFNULL(d.generic_name,d.`name`) AS GenericName,
        IFNULL(d.trade_name,s.TradeName) AS TradeName,
        IFNULL(d.sks_dfs_code,NULL) AS DFSCode,
        IFNULL(d.dosageform,s.DosageForm) AS DosageForm,
        IFNULL(d.strength,s.Strength) AS Strength,
        IFNULL(d.dosageform,s.DosageForm) AS Content,
        d.unitprice AS UnitPrice,
        dr.comp AS Distributor,
        CASE WHEN dr.manufacturer IS NULL OR dr.manufacturer = "" THEN tc.manufacturer ELSE dr.manufacturer END AS Manufacture,
        CASE WHEN (d.drugaccount = "-" OR d.drugaccount = "") THEN"N" WHEN drugaccount <> "" THEN "E" END AS ISED,
        d.did AS NDC24,
        CASE WHEN d.provis_medication_unit_code = "" OR d.provis_medication_unit_code IS NULL THEN d.units ELSE p.provis_medication_unit_name END AS Packsize,
        d.unitprice AS Packprice,
        "A" AS UpdateFlag,
        "" AS DateChange,
        "" AS DateUpdate,
        DATE_FORMAT(d.last_update,"%d/%m/%Y") AS DateEffective ,
        NULL AS RP
        FROM drugitems d
        LEFT JOIN tmt_tpu_code tc ON tc.tpu_code = d.sks_drug_code
        LEFT JOIN drugitems_register_unique dr ON dr.std_code = d.did
        LEFT JOIN provis_medication_unit p ON p.provis_medication_unit_code = d.provis_medication_unit_code 
        LEFT JOIN sks_drugcatalog s ON s.HospDrugCode=d.icode
        LEFT JOIN (SELECT dc.* FROM htp_report.drugcat_aipn dc WHERE  dc.DateUpdate = (SELECT MAX(dc1.DateUpdate) 
			FROM htp_report.drugcat_aipn dc1 WHERE dc.Hospdcode=dc1.Hospdcode AND dc1.Updateflag IN ("A","U","E"))) ad ON ad.hospdcode=d.icode 
        WHERE istatus ="Y" AND d.`name` NOT LIKE "*%" 
        ORDER BY ad.hospdcode,d.icode');

    return view('hosxp_setting.drug_cat_aipn_export',compact('drug'));            
}
######################################################################################################################
//ทะเบียนยาทั้งหมด
public function drug_all(Request $request)
{
    $drug =  DB::connection('hosxp')->select('
        SELECT d1.icode,d1.`name`,d1.generic_name,d1.strength,d1.units,tt3.trade_name AS sks_trade_name,d1.unitcost,d1.unitprice,d1.dosageform,i1.NAME AS income_name,
        d1.drugaccount,tt1.gp_name,	tt2.tp_name,d1.sks_drug_code,tt3.trade_name AS sks_trade_name,d1.ttmt_code,d1.therapeutic,GROUP_CONCAT(d2.hinttext) AS hinttext
        FROM drugitems d1
        LEFT JOIN drughint d2 ON d2.hc=d1.hintcode 
        LEFT JOIN tmt_gp_code tt1 ON tt1.gp_code = d1.tmt_gp_code
        LEFT JOIN tmt_tp_code tt2 ON tt2.tp_code = d1.tmt_tp_code
        LEFT JOIN tmt_tpu_code tt3 ON tt3.tpu_code = d1.sks_drug_code
        LEFT JOIN income i1 ON i1.income = d1.income	
        WHERE d1.istatus = "Y"
        GROUP BY d1.icode	
        ORDER BY d1.NAME,d1.strength,d1.units');
    $drug_non_active =  DB::connection('hosxp')->select('
        SELECT d1.icode,d1.`name`,d1.generic_name,d1.strength,d1.units,tt3.trade_name AS sks_trade_name,d1.unitcost,d1.unitprice,d1.dosageform,i1.NAME AS income_name,
        d1.drugaccount,tt1.gp_name,	tt2.tp_name,d1.sks_drug_code,tt3.trade_name AS sks_trade_name,d1.ttmt_code,d1.therapeutic,GROUP_CONCAT(d2.hinttext) AS hinttext
        FROM drugitems d1
        LEFT JOIN drughint d2 ON d2.hc=d1.hintcode 
        LEFT JOIN tmt_gp_code tt1 ON tt1.gp_code = d1.tmt_gp_code
        LEFT JOIN tmt_tp_code tt2 ON tt2.tp_code = d1.tmt_tp_code
        LEFT JOIN tmt_tpu_code tt3 ON tt3.tpu_code = d1.sks_drug_code
        LEFT JOIN income i1 ON i1.income = d1.income	
        WHERE d1.istatus <> "Y"
        GROUP BY d1.icode	
        ORDER BY d1.NAME,d1.strength,d1.units');

    $request->session()->put('drug',$drug);
    $request->session()->save();

    return view('hosxp_setting.drug_all',compact('drug','drug_non_active'));            
}
public function drug_all_excel(Request $request)
{
        $drug = Session::get('drug');

      return view('hosxp_setting.drug_all_excel',compact('drug'));
}
######################################################################################################################
//ทะเบียนยาสมุนไพร
public function drug_herb()
{
    $drug =  DB::connection('hosxp')->select('
        SELECT d.icode,d.generic_name,d.`name` AS dname,d.strength,d.units,d.unitprice,d.unitcost,
        IF((d.drugaccount="-" OR d.drugaccount IS NULL OR d.drugaccount =""),"NED","ED") AS account,
        d.drugaccount,d1.ref_code AS code_24,d2.ref_code AS code_tmt,CONCAT(d.ttmt_code,"-",t.fsn) AS ttmt_code,
		i.NAME AS income_name,IF(d4.icode <>"","Y","") AS herb32,IF(d5.icode <>"","Y","") AS herb9
        FROM drugitems d
        LEFT JOIN ttmt_code t ON t.ttmt_code=d.ttmt_code
        LEFT JOIN income i ON i.income = d.income      
        LEFT JOIN drugitems_ref_code d1 ON d1.icode=d.icode AND d1.drugitems_ref_code_type_id=1
        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=3
		LEFT JOIN drugitems_property_list d3 ON d3.icode=d.icode AND d3.drugitems_property_id=1 
		LEFT JOIN drugitems_property_list d4 ON d4.icode=d.icode AND d4.drugitems_property_id=12
        LEFT JOIN drugitems_property_list d5 ON d5.icode=d.icode AND d5.drugitems_property_id=13
        WHERE d.istatus = "Y" AND (d3.icode <>"" OR d1.ref_code LIKE "4%")
		GROUP BY d.icode ORDER BY d.NAME,d.strength,d.units');
    $drug_non_active =  DB::connection('hosxp')->select('
        SELECT d.icode,d.generic_name,d.`name` AS dname,d.strength,d.units,d.unitprice,d.unitcost,
        IF((d.drugaccount="-" OR d.drugaccount IS NULL OR d.drugaccount =""),"NED","ED") AS account,
        d.drugaccount,d1.ref_code AS code_24,d2.ref_code AS code_tmt,CONCAT(d.ttmt_code,"-",t.fsn) AS ttmt_code,
		i.NAME AS income_name,IF(d4.icode <>"","Y","") AS herb32,IF(d5.icode <>"","Y","") AS herb9
        FROM drugitems d
        LEFT JOIN ttmt_code t ON t.ttmt_code=d.ttmt_code
        LEFT JOIN income i ON i.income = d.income      
        LEFT JOIN drugitems_ref_code d1 ON d1.icode=d.icode AND d1.drugitems_ref_code_type_id=1
        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=3
		LEFT JOIN drugitems_property_list d3 ON d3.icode=d.icode AND d3.drugitems_property_id=1 
		LEFT JOIN drugitems_property_list d4 ON d4.icode=d.icode AND d4.drugitems_property_id=12
        LEFT JOIN drugitems_property_list d5 ON d5.icode=d.icode AND d5.drugitems_property_id=13
        WHERE d.istatus <> "Y" AND (d3.icode <>"" OR d1.ref_code LIKE 	"4%")
		GROUP BY d.icode ORDER BY d.NAME,d.strength,d.units');

    return view('hosxp_setting.drug_herb',compact('drug','drug_non_active'));            
}
######################################################################################################################
//ทะเบียนยาสนับสนุน
public function drug_support()
{
    $drug =  DB::connection('hosxp')->select('
        SELECT d.icode,d.generic_name,d.`name` AS dname,d.strength,d.units,d.unitprice,d.unitcost,
        IF((d.drugaccount="-" OR d.drugaccount IS NULL OR d.drugaccount =""),"NED","ED") AS account,
        d.drugaccount,d2.ref_code AS code_24,d3.ref_code AS code_tmt,i.NAME AS income_name,nc.nhso_adp_code_name
        FROM drugitems d
        LEFT JOIN income i ON i.income = d.income
        LEFT JOIN drugitems_property_list d1 ON d1.icode=d.icode
        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=1
        LEFT JOIN drugitems_ref_code d3 ON d3.icode=d.icode AND d3.drugitems_ref_code_type_id=3
        LEFT JOIN nhso_adp_code nc ON nc.nhso_adp_code = d.nhso_adp_code 
        WHERE d.istatus = "Y" AND d1.drugitems_property_id IN ("2","3","4","5") ORDER BY d.NAME,d.strength,d.units');
    $drug_non_active =  DB::connection('hosxp')->select('
        SELECT d.icode,d.generic_name,d.`name` AS dname,d.strength,d.units,d.unitprice,d.unitcost,
        IF((d.drugaccount="-" OR d.drugaccount IS NULL OR d.drugaccount =""),"NED","ED") AS account,
        d.drugaccount,d2.ref_code AS code_24,d3.ref_code AS code_tmt,i.NAME AS income_name,nc.nhso_adp_code_name
        FROM drugitems d
        LEFT JOIN income i ON i.income = d.income
        LEFT JOIN drugitems_property_list d1 ON d1.icode=d.icode
        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=1
        LEFT JOIN drugitems_ref_code d3 ON d3.icode=d.icode AND d3.drugitems_ref_code_type_id=3
        LEFT JOIN nhso_adp_code nc ON nc.nhso_adp_code = d.nhso_adp_code 
        WHERE d.istatus <> "Y" AND d1.drugitems_property_id IN ("2","3","4","5") ORDER BY d.NAME,d.strength,d.units');

    return view('hosxp_setting.drug_support',compact('drug','drug_non_active'));            
}
######################################################################################################################
//ทะเบียนยานอก รพ.
public function drug_outside()
{
    $drug =  DB::connection('hosxp')->select('
        SELECT d.icode,d.generic_name,d.`name` AS dname,d.strength,d.units,d.unitprice,d.unitcost,
        IF((d.drugaccount="-" OR d.drugaccount IS NULL OR d.drugaccount =""),"NED","ED") AS account,
        d.drugaccount,d2.ref_code AS code_24,d3.ref_code AS code_tmt,i.NAME AS income_name,nc.nhso_adp_code_name
        FROM drugitems d
        LEFT JOIN income i ON i.income = d.income
        LEFT JOIN drugitems_property_list d1 ON d1.icode=d.icode
        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=1
        LEFT JOIN drugitems_ref_code d3 ON d3.icode=d.icode AND d3.drugitems_ref_code_type_id=3
        LEFT JOIN nhso_adp_code nc ON nc.nhso_adp_code = d.nhso_adp_code 
        WHERE d.istatus = "Y" AND d1.drugitems_property_id=6 ORDER BY d.NAME,d.strength,d.units');
    $drug_non_active =  DB::connection('hosxp')->select('
        SELECT d.icode,d.generic_name,d.`name` AS dname,d.strength,d.units,d.unitprice,d.unitcost,
        IF((d.drugaccount="-" OR d.drugaccount IS NULL OR d.drugaccount =""),"NED","ED") AS account,
        d.drugaccount,d2.ref_code AS code_24,d3.ref_code AS code_tmt,i.NAME AS income_name,nc.nhso_adp_code_name
        FROM drugitems d
        LEFT JOIN income i ON i.income = d.income
        LEFT JOIN drugitems_property_list d1 ON d1.icode=d.icode
        LEFT JOIN drugitems_ref_code d2 ON d2.icode=d.icode AND d2.drugitems_ref_code_type_id=1
        LEFT JOIN drugitems_ref_code d3 ON d3.icode=d.icode AND d3.drugitems_ref_code_type_id=3
        LEFT JOIN nhso_adp_code nc ON nc.nhso_adp_code = d.nhso_adp_code 
        WHERE d.istatus <> "Y" AND d1.drugitems_property_id=6 ORDER BY d.NAME,d.strength,d.units');

    return view('hosxp_setting.drug_outside',compact('drug','drug_non_active'));            
}
#############################################################################################################################
//สิทธิการรักษา
public function pttype()
{
    $pttype =  DB::connection('hosxp')->select('
        SELECT p.`name` AS pttype, CONCAT( p.paidst, "-", p2.`name` ) AS paidst,
        p4.pttype_price_group_name,	CONCAT( p.hipdata_code, "-", n.inscl_name ) AS hipdata_code,
        CONCAT( p1.`code`, "-", p1.`name` ) AS pcode,
        CONCAT( p3.`code`, "-", p3.`name`, "-", p3.pttype_std_code ) AS provis_instype,	p.pttype_std_code 
        FROM pttype p LEFT JOIN pcode p1 ON p1.`code` = p.pcode
        LEFT JOIN paidst p2 ON p2.paidst = p.paidst
        LEFT JOIN provis_instype p3 ON p3.`code` = p.nhso_code
        LEFT JOIN pttype_price_group p4 ON p4.pttype_price_group_id = p.pttype_price_group_id
        LEFT JOIN nhso_inscl_code n ON n.inscl_code = hipdata_code
        LEFT JOIN sks_benefit_plan_type s ON s.sks_benefit_plan_type_id = p.sks_benefit_plan_type_id 
        WHERE p.isuse = "Y" ORDER BY p.pttype');
    $pttype_non_use =  DB::connection('hosxp')->select('
        SELECT p.`name` AS pttype, CONCAT( p.paidst, "-", p2.`name` ) AS paidst,
        p4.pttype_price_group_name,	CONCAT( p.hipdata_code, "-", n.inscl_name ) AS hipdata_code,
        CONCAT( p1.`code`, "-", p1.`name` ) AS pcode,
        CONCAT( p3.`code`, "-", p3.`name`, "-", p3.pttype_std_code ) AS provis_instype,	p.pttype_std_code 
        FROM pttype p LEFT JOIN pcode p1 ON p1.`code` = p.pcode
        LEFT JOIN paidst p2 ON p2.paidst = p.paidst
        LEFT JOIN provis_instype p3 ON p3.`code` = p.nhso_code
        LEFT JOIN pttype_price_group p4 ON p4.pttype_price_group_id = p.pttype_price_group_id
        LEFT JOIN nhso_inscl_code n ON n.inscl_code = hipdata_code
        LEFT JOIN sks_benefit_plan_type s ON s.sks_benefit_plan_type_id = p.sks_benefit_plan_type_id 
        WHERE p.isuse <> "Y" ORDER BY p.pttype');

    return view('hosxp_setting.pttype',compact('pttype','pttype_non_use'));            
}
//บุคลากรทางการแพทย์
public function doctor()
{
    $doctor =  DB::connection('hosxp')->select('
        SELECT d.`code`,CONCAT(d.pname,d.fname,SPACE(1),d.lname) AS doctor_name,CONCAT(d.sex,"-",s.`name`) AS sex,o.officer_phone,
        CASE WHEN d.council_code = 01 THEN "01-แพทย์สภา" WHEN d.council_code = 02 THEN "02-สภาการพยาบาล" WHEN d.council_code = 03 THEN "03-สภาเภสัชกรรม"
        WHEN d.council_code = 04 THEN "04-ทันตแพทย์สภา" WHEN d.council_code = 05 THEN "05-สภากายภาพบำบัด" WHEN d.council_code = 06 THEN "02-สภาเทคนิคการแพทย์"
        WHEN d.council_code = 07 THEN "07-สัตวแพทย์สภา" END AS "council_code",d.licenseno,p.`name` AS position_name,d.cid,o.auto_lockout_minute,
        CONCAT(p1.provider_type_code,"-",p1.provider_type_name) AS provider_type,d.force_diagnosis,o.officer_active,o.officer_active,o.auto_lockout,
        GROUP_CONCAT(d2.doctor_position_std_name) AS position_std_name,IF(d3.hospital_department_id="1","รพ.หัวตะพาน","") AS hospital
        FROM doctor d
        LEFT JOIN sex s ON s.`code`=d.sex
        LEFT JOIN doctor_position p ON p.id = d.position_id
        LEFT JOIN provider_type p1 ON p1.provider_type_code=d.provider_type_code
        LEFT JOIN doctor_position_list d1 ON d1.doctor=d.`code`
        LEFT JOIN doctor_position_std d2 ON d2.doctor_position_std_id=d1.position_id
        LEFT JOIN doctor_hospital_list d3 ON d3.doctor_code=d.`code`
        LEFT JOIN officer o ON o.officer_doctor_code=d.`code`
        WHERE d.active = "Y" GROUP BY d.`code` ORDER BY d.provider_type_code');
    $doctor_non_active =  DB::connection('hosxp')->select('
        SELECT d.`code`,CONCAT(d.pname,d.fname,SPACE(1),d.lname) AS doctor_name,CONCAT(d.sex,"-",s.`name`) AS sex,o.officer_phone,
        CASE WHEN d.council_code = 01 THEN "01-แพทย์สภา" WHEN d.council_code = 02 THEN "02-สภาการพยาบาล" WHEN d.council_code = 03 THEN "03-สภาเภสัชกรรม"
        WHEN d.council_code = 04 THEN "04-ทันตแพทย์สภา" WHEN d.council_code = 05 THEN "05-สภากายภาพบำบัด" WHEN d.council_code = 06 THEN "02-สภาเทคนิคการแพทย์"
        WHEN d.council_code = 07 THEN "07-สัตวแพทย์สภา" END AS "council_code",d.licenseno,p.`name` AS position_name,d.cid,o.auto_lockout_minute,
        CONCAT(p1.provider_type_code,"-",p1.provider_type_name) AS provider_type,d.force_diagnosis,o.officer_active,o.officer_active,o.auto_lockout,
        GROUP_CONCAT(d2.doctor_position_std_name) AS position_std_name,IF(d3.hospital_department_id="1","รพ.หัวตะพาน","") AS hospital
        FROM doctor d
        LEFT JOIN sex s ON s.`code`=d.sex
        LEFT JOIN doctor_position p ON p.id = d.position_id
        LEFT JOIN provider_type p1 ON p1.provider_type_code=d.provider_type_code
        LEFT JOIN doctor_position_list d1 ON d1.doctor=d.`code`
        LEFT JOIN doctor_position_std d2 ON d2.doctor_position_std_id=d1.position_id
        LEFT JOIN doctor_hospital_list d3 ON d3.doctor_code=d.`code`
        LEFT JOIN officer o ON o.officer_doctor_code=d.`code`
        WHERE d.active <> "Y" GROUP BY d.`code` ORDER BY d.provider_type_code');

    return view('hosxp_setting.doctor',compact('doctor','doctor_non_active'));            
}
//คลินิก
public function clinic()
{
    $clinic =  DB::connection('hosxp')->select('
        SELECT c.active_status,c.clinic,c.`name`,c.chronic,c.no_export,h1.hosxp_clinic_type_name,
        o1.oapp_activity_name,oc.sss_clinic_name,qsr.opd_qs_room_name,qd.button_caption
        FROM clinic c
        LEFT JOIN hosxp_clinic_type h1 ON h1.hosxp_clinic_type_id = c.hosxp_clinic_type_id
        LEFT JOIN oapp_activity o1 ON o1.oapp_activity_id = c.oapp_activity_id
        LEFT JOIN ovst_sss_clinic oc ON oc.sss_clinic_code = c.sss_clinic_code
        LEFT JOIN opd_qs_room qsr ON qsr.opd_qs_room_id = c.kiosk_opd_qs_room_id
        LEFT JOIN opd_kios_dep_menu qd ON qd.opd_kios_dep_menu_id=c.opd_kios_dep_menu_id 
        WHERE c.active_status ="Y" ORDER BY c.clinic');
    $clinic_non_active =  DB::connection('hosxp')->select('
        SELECT c.active_status,c.clinic,c.`name`,c.chronic,c.no_export,h1.hosxp_clinic_type_name,
        o1.oapp_activity_name,oc.sss_clinic_name,qsr.opd_qs_room_name,qd.button_caption
        FROM clinic c
        LEFT JOIN hosxp_clinic_type h1 ON h1.hosxp_clinic_type_id = c.hosxp_clinic_type_id
        LEFT JOIN oapp_activity o1 ON o1.oapp_activity_id = c.oapp_activity_id
        LEFT JOIN ovst_sss_clinic oc ON oc.sss_clinic_code = c.sss_clinic_code
        LEFT JOIN opd_qs_room qsr ON qsr.opd_qs_room_id = c.kiosk_opd_qs_room_id
        LEFT JOIN opd_kios_dep_menu qd ON qd.opd_kios_dep_menu_id=c.opd_kios_dep_menu_id 
        WHERE c.active_status <> "Y" ORDER BY c.clinic');

    return view('hosxp_setting.clinic',compact('clinic','clinic_non_active'));            
}
//แผนก
public function spclty()
{
    $spclty =  DB::connection('hosxp')->table('spclty')->where('active_status','Y')->get();
    $spclty_non_active = DB::connection('hosxp')->table('spclty')->where('active_status','<>','Y')->get();

    return view('hosxp_setting.spclty',compact('spclty','spclty_non_active'));            
}
//ห้องตรวจ
public function department()
{
    $depart =  DB::connection('hosxp')->select('
        SELECT k.depcode,k.department,h.NAME AS hospital_department_name,
        CONCAT(s.spclty,"-",s.`name`) AS spclty_name,s.nhso_code,o2.opd_qs_room_name 
        FROM kskdepartment k
        LEFT OUTER JOIN hospital_department h ON h.id = k.hospital_department_id
        LEFT OUTER JOIN spclty s ON s.spclty = k.spclty
        LEFT OUTER JOIN opd_qs_room o2 ON o2.opd_qs_room_id = k.opd_qs_room_id 
        WHERE k.department_active ="Y"');
    $depart_non_active = DB::connection('hosxp')->select('
        SELECT k.depcode,k.department,h.NAME AS hospital_department_name,
        CONCAT(s.spclty,"-",s.`name`) AS spclty_name,s.nhso_code,o2.opd_qs_room_name 
        FROM kskdepartment k
        LEFT OUTER JOIN hospital_department h ON h.id = k.hospital_department_id
        LEFT OUTER JOIN spclty s ON s.spclty = k.spclty
        LEFT OUTER JOIN opd_qs_room o2 ON o2.opd_qs_room_id = k.opd_qs_room_id 
        WHERE k.department_active <>"Y"');

    return view('hosxp_setting.department',compact('depart','depart_non_active'));            
}
//ประเภทมารับบริการ
public function ovstist()
{
    $ovstist =  DB::connection('hosxp')->table('ovstist')->get();
   
    return view('hosxp_setting.ovstist',compact('ovstist'));            
}
//ทะเบียนวัคซีน
public function vaccine()
{
    $vaccine =  DB::connection('hosxp')->select('
        SELECT p.person_vaccine_id,p.vaccine_name,p.vaccine_code,p.vaccine_group,p.export_vaccine_code,
        CONCAT( s.NAME, " ", s.strength," ", s.units ) AS item_name,unitprice,p.combine_vaccine,p.update_moph_registry,
        p.require_plan,p.report_name,p.vaccine_manufacturer,p.dx_icd10,p.auto_treatment_plan,p.treatment_plan_type_id,
        p.vaccine_nickname,p.multiple_doses,p.use_moph_lot,p.dose_per_bottle,v.vaccine_route_name,tp.treatment_plan_type_name 
        FROM person_vaccine p
        LEFT JOIN vaccine_route v ON v.vaccine_route_id = p.vaccine_route_id
        LEFT JOIN s_drugitems s ON s.icode = p.icode
        LEFT JOIN treatment_plan_type tp ON tp.treatment_plan_type_id = p.treatment_plan_type_id 
        WHERE p.active_status ="Y" ORDER BY p.vaccine_name');
    $vaccine_non_active = DB::connection('hosxp')->select('
        SELECT p.person_vaccine_id,p.vaccine_name,p.vaccine_code,p.vaccine_group,p.export_vaccine_code,
        CONCAT( s.NAME," ", s.strength," ", s.units ) AS item_name,unitprice,p.combine_vaccine,p.update_moph_registry,
        p.require_plan,p.report_name,p.vaccine_manufacturer,p.dx_icd10,p.auto_treatment_plan,p.treatment_plan_type_id,
        p.vaccine_nickname,p.multiple_doses,p.use_moph_lot,p.dose_per_bottle,v.vaccine_route_name,tp.treatment_plan_type_name 
        FROM person_vaccine p
        LEFT JOIN vaccine_route v ON v.vaccine_route_id = p.vaccine_route_id
        LEFT JOIN s_drugitems s ON s.icode = p.icode
        LEFT JOIN treatment_plan_type tp ON tp.treatment_plan_type_id = p.treatment_plan_type_id 
        WHERE p.active_status <>"Y" ORDER BY p.vaccine_name');

    return view('hosxp_setting.vaccine',compact('vaccine','vaccine_non_active'));            
}

//hosxp_backup
public function hosxp_backup()
{
    $backup =  DB::connection('hosxp')->select('
        SELECT backup_log_id AS id,backup_finish_datetime AS finish,backup_datetime AS start,
        RIGHT(backup_filename,18) AS filename 
        FROM system_backup_log ORDER BY backup_datetime DESC limit 1');
   
    return Response($backup);            
}

}
