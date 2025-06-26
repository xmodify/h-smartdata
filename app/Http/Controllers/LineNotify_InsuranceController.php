<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Line_token;

class LineNotify_InsuranceController extends Controller
{
// รายงานข้อมูลผู้ป่วยใน รัน 08.00 น.,16.00 น.
public function ipd_service()
{
      $non_dchsummay = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT i.an) AS total,
            SUM(CASE WHEN (a.diag_text_list ="" OR a.diag_text_list IS NULL) THEN 1 ELSE 0 END) AS non_diagtext,
            SUM(CASE WHEN (iptdiag.icd10 ="" OR iptdiag.icd10 IS NULL) THEN 1 ELSE 0 END) AS non_icd10
            FROM ipt i
            LEFT JOIN iptdiag ON iptdiag.an = i.an AND iptdiag.diagtype = "1"
            LEFT JOIN an_stat a ON a.an=i.an
            WHERE i.ward IN ("01","02","03","10") AND i.dchdate >= "2024-07-01" 
            AND (iptdiag.icd10 ="" OR iptdiag.icd10 IS NULL OR a.diag_text_list ="" OR a.diag_text_list IS NULL)');         

      foreach ($non_dchsummay as $row){ 
        $total=$row->total;
        $non_diagtext=$row->non_diagtext;
        $non_icd10=$row->non_icd10;
        $url_non_dchsummary=secure_url('medicalrecord_ipd/non_dchsummary'); 
      }

      $finance_chk = DB::connection('hosxp')->select('
            SELECT SUM(CASE WHEN (finance_transfer = "N" OR opd_wait_money <> "0") THEN 1 ELSE 0 END) AS not_transfer,
            SUM(CASE WHEN wait_paid_money <> "0" THEN 1 ELSE 0 END) AS wait_paid_money,
            SUM(wait_paid_money) AS sum_wait_paid_money
            FROM (SELECT w.`name` AS ward,i1.bedno,i.hn,i.an,p.`name` AS pttype,i2.hospmain,i.finance_transfer,
            a.opd_wait_money,a.item_money,a.uc_money-a.debt_money AS wait_debt_money,
            a.paid_money,a.rcpt_money,a.paid_money-a.rcpt_money AS wait_paid_money
            FROM ipt i
            LEFT JOIN ward w ON w.ward=i.ward
            LEFT JOIN iptadm i1 ON i1.an = i.an
            LEFT JOIN ipt_pttype i2 ON i2.an = i.an AND i2.pttype_number = 1
            LEFT JOIN pttype p ON p.pttype=i2.pttype
            LEFT JOIN an_stat a ON a.an=i.an
            WHERE i.confirm_discharge = "N" 
            AND (i.finance_transfer = "N" OR a.opd_wait_money <>"0" 
            OR a.paid_money-a.rcpt_money <>"0" ) GROUP BY i.an 
            ORDER BY a.opd_wait_money DESC,i.ward,wait_paid_money DESC) AS a ');  

      foreach ($finance_chk as $row){
            $not_transfer=$row->not_transfer;
            $wait_paid_money=$row->wait_paid_money;
            $sum_wait_paid_money=$row->sum_wait_paid_money;
            $url_finance_chk=secure_url('medicalrecord_ipd/finance_chk'); 
      }
    
     //แจ้งเตือน line     
     $line_token_db = DB::select('select line_token FROM line_tokens WHERE line_token_id IN ("1","16")'); 
     $line_token_array = array_column($line_token_db,'line_token');  

     $message = "เวชระเบียนผู้ป่วยใน" ."\n"
      ."ณ วันที่ ". DatetimeThai(date("Y-m-d h:i:sa")) ."\n"  
      ."ความสมบูรณ์เวชระเบียน" ."\n"       
      ." -รอแพทย์สรุป Chart " .$non_diagtext ." AN" ."\n"
      ." -รอลงรหัสวินิจฉัยโรค " .$non_icd10 ." AN" ."\n"  
      .$url_non_dchsummary ."\n" 
      ."ค่ารักษาพยาบาลคนไข้ Admit อยู่" ."\n"
      ." -รอโอนค่ารักษา " .$not_transfer ." AN" ."\n"
      ." -รอชำระเงินสด " .$wait_paid_money ." AN " ."\n"
      ."   จำนวนเงิน " .$sum_wait_paid_money ." บาท" ."\n"
      .$url_finance_chk ."\n";

     function notify_message($message,$token){           
           if($token !== '' && $token !== null){
           $chOne = curl_init();
           curl_setopt( $chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify");
           curl_setopt( $chOne, CURLOPT_SSL_VERIFYHOST, 0);
           curl_setopt( $chOne, CURLOPT_SSL_VERIFYPEER, 0);
           curl_setopt( $chOne, CURLOPT_POST, 1);
           curl_setopt( $chOne, CURLOPT_POSTFIELDS, $message);
           curl_setopt( $chOne, CURLOPT_POSTFIELDS, "message=$message");
           curl_setopt( $chOne, CURLOPT_FOLLOWLOCATION, 1);
           $headers = array( 'Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer '.$token.'', );
           curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers);
           curl_setopt( $chOne, CURLOPT_RETURNTRANSFER, 1);
           $result = curl_exec( $chOne );
           if(curl_error($chOne)) { echo 'error:' . curl_error($chOne); }
           else { $result_ = json_decode($result, true);
           echo "status : ".$result_['status']; echo "message : ". $result_['message']; }
           curl_close( $chOne ); }
     } 
           foreach($line_token_array as $token){
                 notify_message($message,$token);
           }          

return ;    
}

// รายงานข้อมูลผู้ป่วยนอก 16.00 น.
public function opd_service()
{
      $non_authen = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT o.vn) AS total
            FROM ovst o
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p ON p.pttype=vp.pttype 
            WHERE (o.an="" OR o.an IS NULL) AND o.vstdate = DATE(now())
            AND p.pttype NOT LIKE "O%" AND p.`name` NOT LIKE "%ต่างด้าว%"
            AND (vp.auth_code IS NULL OR vp.auth_code = "")');    
      foreach ($non_authen as $row){
            $no_authen=$row->total;
      }

      $non_hospmain = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT o.vn) AS total
            FROM ovst o
            LEFT JOIN visit_pttype vp ON vp.vn=o.vn
            LEFT JOIN pttype p ON p.pttype=vp.pttype 
            WHERE (o.an="" OR o.an IS NULL) AND o.vstdate = DATE(now())
            AND p.hipdata_code IN ("UCS","SSS") 
            AND (vp.hospmain IS NULL OR vp.hospmain = "")');    
      foreach ($non_hospmain as $row){
            $no_hospmain=$row->total;
      }

      $non_cc = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT o.vn) AS total
            FROM ovst o
            LEFT JOIN opdscreen o1 ON o1.vn = o.vn
            WHERE (o1.vn="" OR o1.vn IS NULL) 
            AND (o.an="" OR o.an IS NULL) AND o.vstdate =  DATE(now())');    
      foreach ($non_cc as $row){
            $no_cc=$row->total;
      }

      $non_pdx = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT o.vn) AS total
            FROM ovst o
            LEFT JOIN vn_stat v ON v.vn = o.vn
            WHERE (v.pdx="" OR v.pdx IS NULL) 
            AND (o.an="" OR o.an IS NULL) AND o.vstdate = DATE(now())');    
      foreach ($non_pdx as $row){
            $no_pdx=$row->total;
      }

      $non_diagtext = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT o.vn) AS total
            FROM ovst o
            LEFT JOIN ovst_seq oq ON oq.vn = o.vn
            WHERE (oq.dx_text_list="" OR oq.dx_text_list IS NULL) 
            AND (o.an="" OR o.an IS NULL) AND o.vstdate = DATE(now())');    
      foreach ($non_diagtext as $row){
            $no_diagtext=$row->total;
      }

      $non_pe = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT o.vn) AS total
            FROM ovst o     
            LEFT JOIN opdscreen_doctor_pe ope ON ope.vn = o.vn AND ope.doctor_code IN 
            (SELECT doctor.code FROM doctor WHERE doctor.provider_type_code IN ("01", "011", "02", "03"))
            WHERE (ope.vn IS NULL OR ope.vn ="")
            AND (o.an="" OR o.an IS NULL) AND o.vstdate = DATE(now())');    
      foreach ($non_pe as $row){
            $no_pe=$row->total;
}

     //แจ้งเตือน line     
     $line_token_db = DB::select('select line_token FROM line_tokens WHERE line_token_id IN ("1","16")'); 
     $line_token_array = array_column($line_token_db,'line_token');  

     $message = "เวชระเบียนผู้ป่วยนอก" ."\n"
      ."วันที่ ". DatetimeThai(date("Y-m-d h:i:sa")) ."\n"  
      ."ความสมบูรณ์เวชระเบียน" ."\n"  
      ." -ไม่ขอ Authen " .$no_authen ." Visit" ."\n"   
      ." -ไม่บันทึก Hmain " .$no_hospmain ." Visit" ."\n"
      ." -ไม่บันทึก Cc " .$no_cc ." Visit" ."\n"
      ." -ไม่บันทึก Pdx " .$no_pdx ." Visit" ."\n"
      ." -ไม่บันทึก DiagText " .$no_diagtext ." Visit" ."\n"
      ." -ไม่บันทึก Pe " .$no_pe ." Visit";

     function notify_message($message,$token){           
           if($token !== '' && $token !== null){
           $chOne = curl_init();
           curl_setopt( $chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify");
           curl_setopt( $chOne, CURLOPT_SSL_VERIFYHOST, 0);
           curl_setopt( $chOne, CURLOPT_SSL_VERIFYPEER, 0);
           curl_setopt( $chOne, CURLOPT_POST, 1);
           curl_setopt( $chOne, CURLOPT_POSTFIELDS, $message);
           curl_setopt( $chOne, CURLOPT_POSTFIELDS, "message=$message");
           curl_setopt( $chOne, CURLOPT_FOLLOWLOCATION, 1);
           $headers = array( 'Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer '.$token.'', );
           curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers);
           curl_setopt( $chOne, CURLOPT_RETURNTRANSFER, 1);
           $result = curl_exec( $chOne );
           if(curl_error($chOne)) { echo 'error:' . curl_error($chOne); }
           else { $result_ = json_decode($result, true);
           echo "status : ".$result_['status']; echo "message : ". $result_['message']; }
           curl_close( $chOne ); }
     } 
           foreach($line_token_array as $token){
                 notify_message($message,$token);
           }           
return ;    
}

}
