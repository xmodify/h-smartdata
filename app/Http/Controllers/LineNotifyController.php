<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Line_token;

class LineNotifyController extends Controller
{

// รายงานสถานะการณ์สรุปเวรดึก รัน 08.00 น.
public function service_s()
{
      $hrd_person = DB::connection('backoffice')->select('select HR_CID FROM hrd_person WHERE HR_STATUS_ID=1');
      $hr_cid = array_column($hrd_person,'HR_CID');
      $hrd_cid = join(",",$hr_cid);
      $service = DB::connection('hosxp')->select('
            SELECT DATE(NOW()) AS vstdate,COUNT(DISTINCT vn) as opd_visit,
            sum(CASE WHEN main_dep = "002" THEN 1 ELSE 0 END) AS opd,
            sum(CASE WHEN main_dep = "006" THEN 1 ELSE 0 END) AS er,
            sum(CASE WHEN main_dep = "009" THEN 1 ELSE 0 END) AS physic,
            sum(CASE WHEN main_dep = "010" THEN 1 ELSE 0 END) AS health_med,
            sum(CASE WHEN main_dep = "013" THEN 1 ELSE 0 END) AS dent,
            sum(CASE WHEN main_dep IN ("033") THEN 1 ELSE 0 END) AS kidney_hos,
            sum(CASE WHEN main_dep IN ("024") THEN 1 ELSE 0 END) AS kidney_os,
            (SELECT COUNT(DISTINCT vn) FROM person_anc_service WHERE anc_service_date = DATE(NOW()) 
                  AND anc_service_time BETWEEN "00:00:00" AND "07:59:59") AS anc,
            (SELECT COUNT(DISTINCT vn) FROM referout WHERE refer_date = DATE(NOW())
                  AND refer_time BETWEEN "00:00:00" AND "07:59:59") AS refer,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE regdate = DATE(NOW())
                  AND regtime BETWEEN "00:00:00" AND "07:59:59") AS admit,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE dchdate = DATE(NOW())
                  AND dchtime BETWEEN "00:00:00" AND "07:59:59") AS discharge,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE regdate = DATE(NOW()) AND ipt_type ="4" 
                  AND regtime BETWEEN "00:00:00" AND "07:59:59") AS labor,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE regdate = DATE(NOW()) AND ipt_type ="3" 
                  AND regtime BETWEEN "00:00:00" AND "07:59:59") AS newborn,
            (SELECT COUNT(DISTINCT operation_id) FROM operation_list WHERE request_operation_date = DATE(NOW())
                  AND request_operation_time BETWEEN "00:00:00" AND "07:59:59") AS operation,
		(SELECT COUNT(DISTINCT hn) FROM opitemrece  WHERE rxdate = DATE(NOW()) AND TIME(last_modified) BETWEEN "00:00:00" AND "07:59:59"
                  AND icode IN (SELECT icode FROM xray_items WHERE xray_items_group = 3)) AS "ct",
		(SELECT SUM(sum_price) FROM opitemrece  WHERE rxdate = DATE(NOW()) AND TIME(last_modified) BETWEEN "00:00:00" AND "07:59:59"
                  AND icode IN (SELECT icode FROM xray_items WHERE xray_items_group = 3)) AS "ct_price",
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE confirm_discharge = "N" ) AS ipd_all,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE confirm_discharge = "N" AND ward = "01" ) AS ipd_normal,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE confirm_discharge = "N" AND ward = "02" ) AS ipd_labor,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE confirm_discharge = "N" AND ward IN ("03","08")) AS ipd_vip,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE confirm_discharge = "N" AND ward = "06" ) AS homeward,
            (SELECT COUNT(DISTINCT an) FROM ipt i , patient p WHERE p.hn=i.hn AND i.confirm_discharge = "N"
                  AND p.cid IN ('.$hrd_cid.')) AS ipd_hr
            FROM (SELECT o.vn,o.vstdate,o.vsttime,o.main_dep FROM ovst o
            WHERE o.vstdate = DATE(NOW()) AND o.vsttime BETWEEN "00:00:00" AND "07:59:59" 
            GROUP BY o.hn,o.main_dep ORDER BY o.an DESC ) AS a ');         

      foreach ($service as $row){
      $vstdate=$row->vstdate;
      $opd_visit= $row->opd_visit;
      $opd=$row->opd;
      $er=$row->er;
      $physic=$row->physic;
      $health_med=$row->health_med;
      $dent=$row->dent;
      $kidney_hos=$row->kidney_hos;
      $kidney_os=$row->kidney_os;
      $anc=$row->anc;
      $refer=$row->refer;
      $admit=$row->admit;
      $discharge=$row->discharge;
      $labor=$row->labor;
      $newborn=$row->newborn;
      $operation=$row->operation;
      $ct=$row->ct;
      $ct_price=$row->ct_price;
      $ipd_all=$row->ipd_all;
      $ipd_normal=$row->ipd_normal;
      $ipd_labor=$row->ipd_labor;
      $ipd_vip=$row->ipd_vip;
      $homeward=$row->homeward;
      $ipd_hr=$row->ipd_hr;
      $url_dashboard=secure_url('dashboard/opd_mornitor'); 
      $url_dashboard_ipd=secure_url('dashboard/ipd_mornitor'); 
    }
      $oapp = DB::connection('hosxp')->select('
            SELECT  DATE(NOW()) AS nextdate,
            CONCAT(clinic,"[",oapp,"]") AS appoint FROM
            (SELECT o.nextdate,c.`name` AS clinic,COUNT(DISTINCT o.oapp_id) AS oapp
            FROM oapp o LEFT JOIN clinic c ON c.clinic=o.clinic
            WHERE o.nextdate = DATE(NOW())
            GROUP BY o.clinic ) AS a ORDER BY oapp DESC limit 10 '); 
      $oapp_c = array_column($oapp,'appoint');
      $appoint = join(",",$oapp_c); foreach($oapp as $row){$nextdate=$row->nextdate;} 

      $hr_admit = DB::connection('hosxp')->select('
            SELECT CONCAT(p.pname,p.fname," ",p.lname) AS ptname
            FROM ipt i , patient p
            WHERE p.hn=i.hn AND i.confirm_discharge = "N"
            AND p.cid IN ('.$hrd_cid.') '); 
      $hr_name = array_column($hr_admit,'ptname');
      $hrd_name = join(",",$hr_name); 

//แจ้งเตือน Telegram

      $message = "\n"."วันที่ ". DateThai($vstdate).  
            "\n". "เวลา 00.00-08.00 น.(เวรดึก)". 
            "\n"."OPDVisit ". $opd_visit. " ราย".       
            "\n"." -ตรวจโรคทั่วไป ". $opd. " ราย".
            "\n"." -อุบัติเหตุฉุกเฉิน ". $er. " ราย".
            "\n"." -กายภาพบำบัด ". $physic. " ราย". 
            "\n"." -แพทย์แผนไทย ". $health_med. " ราย".
            "\n"." -ทันตกรรม ". $dent. " ราย".
            "\n"." -ฟอกไต รพ ". $kidney_hos. " ราย".
            "\n"." -ฟอกไต เอกชน ". $kidney_os. " ราย".
            "\n"." -ฝากครรภ์ ". $anc. " ราย".
            "\n"." -Refer ". $refer. " ราย".
            "\n"." -Admit ". $admit. " ราย".
            "\n"." -Discharge ". $discharge. " ราย".
            "\n"." -มาคลอดบุตร ". $labor. " ราย". 
            "\n"." -เด็กแรกเกิด ". $newborn. " ราย".
            "\n"." -ผ่าตัด ". $operation. " ราย".  
            "\n"." -CT Scan ". $ct. " ราย ".$ct_price. " บาท".  
            "\n". $url_dashboard."\n". 
            "\n"."สถานะผู้ป่วยในปัจจุบัน ". $ipd_all. " ราย". 
            "\n"." -สามัญ ". $ipd_normal. " ราย".
            "\n"." -VIP ". $ipd_vip. " ราย".
            "\n"." -ห้องคลอด ". $ipd_labor. " ราย".     
            "\n"." -Homeward ". $homeward. " ราย".
            "\n". $url_dashboard_ipd."\n".
            "\n"."เจ้าหน้าที่ Admit ". $ipd_hr. " ราย".    
            "\n". "   ". $hrd_name. "\n".
            "\n"."นัดหมาย ".  DateThai(date("Y-m-d")).  
            "\n"."   ". $appoint. "\n"; 
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994"]; //Test_Notify_Group2
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200);  
}

// รายงานสถานะการณ์สรุปเวรเช้า รัน 16.00 น.
public function service_i()
{
      $hrd_person = DB::connection('backoffice')->select('select HR_CID FROM hrd_person WHERE HR_STATUS_ID=1');
      $hr_cid = array_column($hrd_person,'HR_CID');
      $hrd_cid = join(",",$hr_cid);
      $service = DB::connection('hosxp')->select('
            SELECT DATE(NOW()) AS vstdate,COUNT(DISTINCT vn) as opd_visit,
            sum(CASE WHEN main_dep = "002" THEN 1 ELSE 0 END) AS opd,
            sum(CASE WHEN main_dep = "006" THEN 1 ELSE 0 END) AS er,
            sum(CASE WHEN main_dep = "009" THEN 1 ELSE 0 END) AS physic,
            sum(CASE WHEN main_dep = "010" THEN 1 ELSE 0 END) AS health_med,
            sum(CASE WHEN main_dep = "013" THEN 1 ELSE 0 END) AS dent,
            sum(CASE WHEN main_dep IN ("033") THEN 1 ELSE 0 END) AS kidney_hos,
            sum(CASE WHEN main_dep IN ("024") THEN 1 ELSE 0 END) AS kidney_os,
            (SELECT COUNT(DISTINCT vn) FROM person_anc_service WHERE anc_service_date = DATE(NOW()) 
                  AND anc_service_time BETWEEN "08:00:00" AND "15:59:59") AS anc,
            (SELECT COUNT(DISTINCT vn) FROM referout WHERE refer_date = DATE(NOW())
                  AND refer_time BETWEEN "08:00:00" AND "15:59:59") AS refer,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE regdate = DATE(NOW())
                  AND regtime BETWEEN "08:00:00" AND "15:59:59") AS admit,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE dchdate = DATE(NOW())
                  AND dchtime BETWEEN "08:00:00" AND "15:59:59") AS discharge,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE regdate = DATE(NOW()) AND ipt_type ="4" 
                  AND regtime BETWEEN "08:00:00" AND "15:59:59") AS labor,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE regdate = DATE(NOW()) AND ipt_type ="3" 
                  AND regtime BETWEEN "08:00:00" AND "15:59:59") AS newborn,
            (SELECT COUNT(DISTINCT operation_id) FROM operation_list WHERE request_operation_date = DATE(NOW())
                  AND request_operation_time BETWEEN "08:00:00" AND "15:59:59") AS operation,
            (SELECT COUNT(DISTINCT hn) FROM opitemrece  WHERE rxdate = DATE(NOW()) AND TIME(last_modified) BETWEEN "08:00:00" AND "15:59:59"
                  AND icode IN (SELECT icode FROM xray_items WHERE xray_items_group = 3)) AS "ct",
		(SELECT SUM(sum_price) FROM opitemrece  WHERE rxdate = DATE(NOW()) AND TIME(last_modified) BETWEEN "08:00:00" AND "15:59:59"
                  AND icode IN (SELECT icode FROM xray_items WHERE xray_items_group = 3)) AS "ct_price",
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE confirm_discharge = "N" ) AS ipd_all,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE confirm_discharge = "N" AND ward = "01" ) AS ipd_normal,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE confirm_discharge = "N" AND ward = "02" ) AS ipd_labor,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE confirm_discharge = "N" AND ward IN ("03","08")) AS ipd_vip,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE confirm_discharge = "N" AND ward = "06" ) AS homeward,
            (SELECT COUNT(DISTINCT an) FROM ipt i , patient p WHERE p.hn=i.hn AND i.confirm_discharge = "N"
                  AND p.cid IN ('.$hrd_cid.')) AS ipd_hr
            FROM (SELECT o.vn,o.vstdate,o.vsttime,o.main_dep FROM ovst o
            WHERE o.vstdate = DATE(NOW()) AND o.vsttime BETWEEN "08:00:00" AND "15:59:59" 
            GROUP BY o.hn,o.main_dep ORDER BY o.an DESC ) AS a ');         

      foreach ($service as $row){
      $vstdate=$row->vstdate;
      $opd_visit= $row->opd_visit;
      $opd=$row->opd;
      $er=$row->er;
      $physic=$row->physic;
      $health_med=$row->health_med;
      $dent=$row->dent;
      $kidney_hos=$row->kidney_hos;
      $kidney_os=$row->kidney_os;
      $anc=$row->anc;
      $refer=$row->refer;
      $admit=$row->admit;
      $discharge=$row->discharge;
      $labor=$row->labor;
      $newborn=$row->newborn;
      $operation=$row->operation;
      $ct=$row->ct;
      $ct_price=$row->ct_price;
      $ipd_all=$row->ipd_all;
      $ipd_normal=$row->ipd_normal;
      $ipd_labor=$row->ipd_labor;
      $ipd_vip=$row->ipd_vip;
      $homeward=$row->homeward;
      $ipd_hr=$row->ipd_hr;
      $url_dashboard=secure_url('dashboard/opd_mornitor'); 
      $url_dashboard_ipd=secure_url('dashboard/ipd_mornitor'); 
    }
      $oapp = DB::connection('hosxp')->select('
            SELECT  date(DATE_ADD(now(), INTERVAL +1 DAY )) AS nextdate,
            CONCAT(clinic,"[",oapp,"]") AS appoint FROM
            (SELECT o.nextdate,c.`name` AS clinic,COUNT(DISTINCT o.oapp_id) AS oapp
            FROM oapp o LEFT JOIN clinic c ON c.clinic=o.clinic
            WHERE o.nextdate = date(DATE_ADD(now(), INTERVAL +1 DAY ))
            GROUP BY o.clinic ) AS a ORDER BY oapp DESC limit 10 '); 
      $oapp_c = array_column($oapp,'appoint');
      $appoint = join(",",$oapp_c); foreach($oapp as $row){$nextdate=$row->nextdate;} 

      $hr_admit = DB::connection('hosxp')->select('
            SELECT CONCAT(p.pname,p.fname," ",p.lname) AS ptname
            FROM ipt i , patient p
            WHERE p.hn=i.hn AND i.confirm_discharge = "N"
            AND p.cid IN ('.$hrd_cid.') '); 
      $hr_name = array_column($hr_admit,'ptname');
      $hrd_name = join(",",$hr_name); 

//แจ้งเตือน Telegram

      $message = "\n"."วันที่ ". DateThai($vstdate).  
            "\n". "เวลา 08.00-16.00 น.(เวรเช้า)". 
            "\n"."OPDVisit ". $opd_visit. " ราย".       
            "\n"." -ตรวจโรคทั่วไป ". $opd. " ราย".
            "\n"." -อุบัติเหตุฉุกเฉิน ". $er. " ราย".
            "\n"." -กายภาพบำบัด ". $physic. " ราย". 
            "\n"." -แพทย์แผนไทย ". $health_med. " ราย".
            "\n"." -ทันตกรรม ". $dent. " ราย".
            "\n"." -ฟอกไต รพ ". $kidney_hos. " ราย".
            "\n"." -ฟอกไต เอกชน ". $kidney_os. " ราย".
            "\n"." -ฝากครรภ์ ". $anc. " ราย".
            "\n"." -Refer ". $refer. " ราย".
            "\n"." -Admit ". $admit. " ราย".
            "\n"." -Discharge ". $discharge. " ราย".
            "\n"." -มาคลอดบุตร ". $labor. " ราย". 
            "\n"." -เด็กแรกเกิด ". $newborn. " ราย".
            "\n"." -ผ่าตัด ". $operation. " ราย".  
            "\n"." -CT Scan ". $ct. " ราย ".$ct_price. " บาท". 
            "\n". $url_dashboard."\n".
            "\n"."สถานะผู้ป่วยในปัจจุบัน ". $ipd_all. " ราย". 
            "\n"." -สามัญ ". $ipd_normal. " ราย".
            "\n"." -VIP ". $ipd_vip. " ราย".
            "\n"." -ห้องคลอด ". $ipd_labor. " ราย".     
            "\n"." -Homeward ". $homeward. " ราย".
            "\n". $url_dashboard_ipd."\n".
            "\n"."เจ้าหน้าที่ Admit ". $ipd_hr. " ราย".    
            "\n". "   ". $hrd_name. "\n".
            "\n"."นัดหมาย ".  DateThai(date("Y-m-d",strtotime("+1 day"))).
            "\n"."   ". $appoint. "\n";
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994"]; //Test_Notify_Group2
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200);  
} 

// รายงานสถานะการณ์สรุปเวรบ่าย รัน 00.05 น.  
public function service_o()
{
      $hrd_person = DB::connection('backoffice')->select('select HR_CID FROM hrd_person WHERE HR_STATUS_ID=1');
      $hr_cid = array_column($hrd_person,'HR_CID');
      $hrd_cid = join(",",$hr_cid);
      $service = DB::connection('hosxp')->select('
            SELECT date(DATE_ADD(now(), INTERVAL -1 DAY )) AS vstdate,COUNT(DISTINCT vn) as opd_visit,
            sum(CASE WHEN main_dep = "002" THEN 1 ELSE 0 END) AS opd,
            sum(CASE WHEN main_dep = "006" THEN 1 ELSE 0 END) AS er,
            sum(CASE WHEN main_dep = "009" THEN 1 ELSE 0 END) AS physic,
            sum(CASE WHEN main_dep = "010" THEN 1 ELSE 0 END) AS health_med,
            sum(CASE WHEN main_dep = "013" THEN 1 ELSE 0 END) AS dent,
            sum(CASE WHEN main_dep IN ("033") THEN 1 ELSE 0 END) AS kidney_hos,
            sum(CASE WHEN main_dep IN ("024") THEN 1 ELSE 0 END) AS kidney_os,
            (SELECT COUNT(DISTINCT vn) FROM person_anc_service WHERE anc_service_date = date(DATE_ADD(now(), INTERVAL +1 DAY ))
                  AND anc_service_time BETWEEN "16:00:00" AND "23:59:59") AS anc,
            (SELECT COUNT(DISTINCT vn) FROM referout WHERE refer_date = date(DATE_ADD(now(), INTERVAL -1 DAY ))
                  AND refer_time BETWEEN "00:00:00" AND "15:59:59") AS refer,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE regdate = date(DATE_ADD(now(), INTERVAL -1 DAY ))
                  AND regtime BETWEEN "16:00:00" AND "23:59:59") AS admit,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE dchdate = date(DATE_ADD(now(), INTERVAL -1 DAY ))
                  AND dchtime BETWEEN "16:00:00" AND "23:59:59") AS discharge,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE regdate = date(DATE_ADD(now(), INTERVAL -1 DAY )) 
                  AND ipt_type ="4" AND regtime BETWEEN "16:00:00" AND "23:59:59") AS labor,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE regdate = date(DATE_ADD(now(), INTERVAL -1 DAY )) 
                  AND ipt_type ="3" AND regtime BETWEEN "16:00:00" AND "23:59:59") AS newborn,
            (SELECT COUNT(DISTINCT operation_id) FROM operation_list WHERE request_operation_date = date(DATE_ADD(now(), INTERVAL -1 DAY )) 
                  AND request_operation_time BETWEEN "16:00:00" AND "23:59:59") AS operation,
            (SELECT COUNT(DISTINCT hn) FROM opitemrece  WHERE rxdate = date(DATE_ADD(now(), INTERVAL -1 DAY )) AND TIME(last_modified) BETWEEN "16:00:00" AND "23:59:59"
                  AND icode IN (SELECT icode FROM xray_items WHERE xray_items_group = 3)) AS "ct",
		(SELECT SUM(sum_price) FROM opitemrece  WHERE rxdate = date(DATE_ADD(now(), INTERVAL -1 DAY )) AND TIME(last_modified) BETWEEN "16:00:00" AND "23:59:59"
                  AND icode IN (SELECT icode FROM xray_items WHERE xray_items_group = 3)) AS "ct_price",
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE confirm_discharge = "N" ) AS ipd_all,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE confirm_discharge = "N" AND ward = "01" ) AS ipd_normal,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE confirm_discharge = "N" AND ward = "02" ) AS ipd_labor,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE confirm_discharge = "N" AND ward IN ("03","08")) AS ipd_vip,
            (SELECT COUNT(DISTINCT an) FROM ipt  WHERE confirm_discharge = "N" AND ward = "06" ) AS homeward,
            (SELECT COUNT(DISTINCT an) FROM ipt i , patient p WHERE p.hn=i.hn AND i.confirm_discharge = "N"
                  AND p.cid IN ('.$hrd_cid.')) AS ipd_hr
            FROM (SELECT o.vn,o.vstdate,o.vsttime,o.main_dep FROM ovst o
            WHERE o.vstdate = date(DATE_ADD(now(), INTERVAL -1 DAY )) AND o.vsttime BETWEEN "16:00:00" AND "23:59:59" 
            GROUP BY o.hn,o.main_dep ORDER BY o.an DESC ) AS a ');         

      foreach ($service as $row){
      $vstdate=$row->vstdate;
      $opd_visit= $row->opd_visit;
      $opd=$row->opd;
      $er=$row->er;
      $physic=$row->physic;
      $health_med=$row->health_med;
      $dent=$row->dent;
      $kidney_hos=$row->kidney_hos;
      $kidney_os=$row->kidney_os;
      $anc=$row->anc;
      $refer=$row->refer;
      $admit=$row->admit;
      $discharge=$row->discharge;
      $labor=$row->labor;
      $newborn=$row->newborn;
      $operation=$row->operation;
      $ct=$row->ct;
      $ct_price=$row->ct_price;
      $ipd_all=$row->ipd_all;
      $ipd_normal=$row->ipd_normal;
      $ipd_labor=$row->ipd_labor;
      $ipd_vip=$row->ipd_vip;
      $homeward=$row->homeward;
      $ipd_hr=$row->ipd_hr;
      $url_dashboard=secure_url('dashboard/opd_mornitor'); 
      $url_dashboard_ipd=secure_url('dashboard/ipd_mornitor'); 
    }
      $oapp = DB::connection('hosxp')->select('
            SELECT  DATE(NOW()) AS nextdate,
            CONCAT(clinic,"[",oapp,"]") AS appoint FROM
            (SELECT o.nextdate,c.`name` AS clinic,COUNT(DISTINCT o.oapp_id) AS oapp
            FROM oapp o LEFT JOIN clinic c ON c.clinic=o.clinic
            WHERE o.nextdate = DATE(NOW())
            GROUP BY o.clinic ) AS a ORDER BY oapp DESC limit 10 '); 
      $oapp_c = array_column($oapp,'appoint');
      $appoint = join(",",$oapp_c); foreach($oapp as $row){$nextdate=$row->nextdate;} 

      $hr_admit = DB::connection('hosxp')->select('
            SELECT CONCAT(p.pname,p.fname," ",p.lname) AS ptname
            FROM ipt i , patient p
            WHERE p.hn=i.hn AND i.confirm_discharge = "N"
            AND p.cid IN ('.$hrd_cid.') '); 
      $hr_name = array_column($hr_admit,'ptname');
      $hrd_name = join(",",$hr_name); 

//แจ้งเตือน Telegram

      $message = "\n"."วันที่ ". DateThai($vstdate).  
            "\n". "เวลา 16.00-00.00 น.(เวรบ่าย)". 
            "\n"."OPDVisit ". $opd_visit. " ราย".       
            "\n"." -ตรวจโรคทั่วไป ". $opd. " ราย".
            "\n"." -อุบัติเหตุฉุกเฉิน ". $er. " ราย".
            "\n"." -กายภาพบำบัด ". $physic. " ราย". 
            "\n"." -แพทย์แผนไทย ". $health_med. " ราย".
            "\n"." -ทันตกรรม ". $dent. " ราย".
            "\n"." -ฟอกไต รพ ". $kidney_hos. " ราย".
            "\n"." -ฟอกไต เอกชน ". $kidney_os. " ราย".
            "\n"." -ฝากครรภ์ ". $anc. " ราย".
            "\n"." -Refer ". $refer. " ราย".
            "\n"." -Admit ". $admit. " ราย".
            "\n"." -Discharge ". $discharge. " ราย".
            "\n"." -มาคลอดบุตร ". $labor. " ราย". 
            "\n"." -เด็กแรกเกิด ". $newborn. " ราย".  
            "\n"." -ผ่าตัด ". $operation. " ราย".  
            "\n"." -CT Scan ". $ct. " ราย ".$ct_price. " บาท". 
            "\n". $url_dashboard."\n".
            "\n"."สถานะผู้ป่วยในปัจจุบัน ". $ipd_all. " ราย". 
            "\n"." -สามัญ ". $ipd_normal. " ราย".
            "\n"." -VIP ". $ipd_vip. " ราย".
            "\n"." -ห้องคลอด ". $ipd_labor. " ราย".   
            "\n"." -Homeward ". $homeward. " ราย".
            "\n". $url_dashboard_ipd."\n".
            "\n"."เจ้าหน้าที่ Admit ". $ipd_hr. " ราย".    
            "\n". "   ". $hrd_name. "\n".
            "\n"."นัดหมาย ".  DateThai(date("Y-m-d")). 
            "\n"."   ". $appoint. "\n";
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994"]; //Test_Notify_Group2
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200);     
}

// รายงานความเสี่ยงประจำเดือน รันเสร ช บ ด
public function risk()
{
      $risk = DB::connection('backoffice')->select('
            SELECT CASE WHEN MONTH(DATE(NOW()))="10" THEN CONCAT("ต.ค. ",YEAR(DATE(NOW()))+543)
            WHEN MONTH(DATE(NOW()))="11" THEN CONCAT("พ.ย. ",YEAR(DATE(NOW()))+543)
            WHEN MONTH(DATE(NOW()))="12" THEN CONCAT("ธ.ค. ",YEAR(DATE(NOW()))+543)
            WHEN MONTH(DATE(NOW()))="1" THEN CONCAT("ม.ค. ",YEAR(DATE(NOW()))+543)
            WHEN MONTH(DATE(NOW()))="2" THEN CONCAT("ก.พ. ",YEAR(DATE(NOW()))+543)
            WHEN MONTH(DATE(NOW()))="3" THEN CONCAT("มี.ค. ",YEAR(DATE(NOW()))+543)
            WHEN MONTH(DATE(NOW()))="4" THEN CONCAT("เม.ย. ",YEAR(DATE(NOW()))+543)
            WHEN MONTH(DATE(NOW()))="5" THEN CONCAT("พ.ค. ",YEAR(DATE(NOW()))+543)
            WHEN MONTH(DATE(NOW()))="6" THEN CONCAT("มิ.ย. ",YEAR(DATE(NOW()))+543)
            WHEN MONTH(DATE(NOW()))="7" THEN CONCAT("ก.ค. ",YEAR(DATE(NOW()))+543)
            WHEN MONTH(DATE(NOW()))="8" THEN CONCAT("ส.ค. ",YEAR(DATE(NOW()))+543)
            WHEN MONTH(DATE(NOW()))="9" THEN CONCAT("ก.ย. ",YEAR(DATE(NOW()))+543)
            END AS "month",COUNT(DISTINCT r.RISKREP_ID) AS total,
            SUM(CASE WHEN l.RISK_REP_LEVEL_NAME IN ("A","B","1") THEN 1 ELSE 0 END) AS "near_miss",
            SUM(CASE WHEN l.RISK_REP_LEVEL_NAME IN ("C","D","2") THEN 1 ELSE 0 END) AS "low_risk",
            SUM(CASE WHEN l.RISK_REP_LEVEL_NAME IN ("E","F","3") THEN 1 ELSE 0 END) AS "moderate_risk",
            SUM(CASE WHEN l.RISK_REP_LEVEL_NAME IN ("G","H","4","5") THEN 1 ELSE 0 END) AS "high_risk",
            SUM(CASE WHEN (l.RISK_REP_LEVEL_NAME = "" OR l.RISK_REP_LEVEL_NAME IS NULL) THEN 1 ELSE 0 END) AS "null"
            FROM risk_rep r
            LEFT JOIN risk_rep_level l ON l.RISK_REP_LEVEL_ID=r.RISKREP_LEVEL
            WHERE MONTH(r.RISKREP_DATESAVE)=MONTH(DATE(NOW())) 
            AND YEAR(r.RISKREP_DATESAVE)=YEAR(DATE(NOW()))
            AND r.RISKREP_STATUS <> "CANCEL" ');         

      foreach ($risk as $row){
      $month=$row->month;
      $total= $row->total;
      $near_miss=$row->near_miss;
      $low_risk=$row->low_risk;
      $moderate_risk=$row->moderate_risk;
      $high_risk=$row->high_risk;
      $null=$row->null;     
    }
    
      $risk_wait = DB::connection('backoffice')->select('
            SELECT CONCAT(leader,"[",total,"]") AS wait
            FROM (SELECT LEADER_PERSON_NAME AS leader,COUNT(DISTINCT RISKREP_ID) AS total 
            FROM risk_rep WHERE (RISKREP_LEVEL IS NULL OR RISKREP_LEVEL ="") 
            AND RISKREP_STATUS <> "CANCEL"           
            GROUP BY LEADER_PERSON_ID ORDER BY total DESC) AS a'); 
      $r_wait = array_column($risk_wait,'wait');
      $wait = join(",",$r_wait); 

//แจ้งเตือน Telegram

      $message = "อุบัติการณ์เดือน " .$month ."\n" 
      ."ทั้งหมด " .$total ."\n" 
      ." -Near Miss " .$near_miss ."\n" 
      ." -Low Risk " .$low_risk ."\n" 
      ." -Moderate " .$moderate_risk ."\n" 
      ." -High Risk " .$high_risk ."\n"
      ." -รอตรวจสอบ " .$null ."\n" ."\n"
      ."หัวหน้างานที่ยังไม่ตรวจสอบ" ."\n"  
      ."   " .$wait ."\n";

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4605577318", "-4729376994"]; //RM-รพ.หัวตะพาน,Test_Notify_Group2

      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";

            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }

      return response()->json(['success' => 'success'], 200);    
}

// ER รายงานสถานะการณ์สรุปเวรดึก รัน 08.00 น.
public function er_service_s()
{
      $service = DB::connection('hosxp')->select('
            select DATE(NOW()) AS vstdate,count(DISTINCT vn) as visit,                 
            SUM(CASE WHEN er_emergency_type IN ("1","2") THEN 1	ELSE 0 END) AS "Emergent",
            SUM(CASE WHEN er_emergency_type ="3" THEN 1 ELSE 0 END) AS "Urgent",
            SUM(CASE WHEN er_emergency_type ="4" THEN 1 ELSE 0 END) AS "Acute_illness",
            SUM(CASE WHEN er_emergency_type ="5" THEN 1 ELSE 0 END) AS "Non_acute_illness"
            FROM er_regist  WHERE DATE(enter_er_time) = DATE(NOW()) 
            AND TIME(enter_er_time) BETWEEN "00:00:00" AND "07:59:59"');         

      foreach ($service as $row){
            $vstdate=$row->vstdate;
            $visit=$row->visit;
            $Emergent= $row->Emergent;
            $Urgent=$row->Urgent;
            $Acute_illness=$row->Acute_illness;
            $Non_acute_illness=$row->Non_acute_illness;
            $url=route('nurse_productivity_er_night'); 
      }  
               
//แจ้งเตือน Telegram

      $message = "Productivity งานอุบัติเหตุ-ฉุกเฉิน" ."\n"
      ."วันที่ " .DateThai($vstdate) ."\n"  
      ."เวลา 00.00-08.00 น. (เวรดึก)" ."\n"
      ."ผู้ป่วยในเวร " .$visit ." ราย" ."\n"       
      ." -Emergent " .$Emergent ." ราย" ."\n"
      ." -Urgent " .$Urgent ." ราย" ."\n"
      ." -Acute illness " .$Acute_illness ." ราย" ."\n" 
      ." -Non Acute illness " .$Non_acute_illness ." ราย" ."\n" ."\n"
      ."บันทึก Productivity " ."\n"
      . $url. "\n";

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4747192701", "-4729376994"]; //งานอุบัติเหตุฉุกเฉิน-รพ.หัวตะพาน,Test_Notify_Group2

      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";

            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }

      return response()->json(['success' => 'success'], 200);    
}

// ER รายงานสถานะการณ์สรุปเวรเช้า รัน 16.00 น.
public function er_service_i()
{
      $service = DB::connection('hosxp')->select('
            select DATE(NOW()) AS vstdate,count(DISTINCT vn) as visit,                 
            SUM(CASE WHEN er_emergency_type IN ("1","2") THEN 1	ELSE 0 END) AS "Emergent",
            SUM(CASE WHEN er_emergency_type ="3" THEN 1 ELSE 0 END) AS "Urgent",
            SUM(CASE WHEN er_emergency_type ="4" THEN 1 ELSE 0 END) AS "Acute_illness",
            SUM(CASE WHEN er_emergency_type ="5" THEN 1 ELSE 0 END) AS "Non_acute_illness"
            FROM er_regist  WHERE DATE(enter_er_time) = DATE(NOW()) 
            AND TIME(enter_er_time) BETWEEN "08:00:00" AND "15:59:59"');         

      foreach ($service as $row){
            $vstdate=$row->vstdate;
            $visit=$row->visit;
            $Emergent= $row->Emergent;
            $Urgent=$row->Urgent;
            $Acute_illness=$row->Acute_illness;
            $Non_acute_illness=$row->Non_acute_illness;
            $url=route('nurse_productivity_er_morning');
      }
    
//แจ้งเตือน Telegram

      $message = "Productivity งานอุบัติเหตุ-ฉุกเฉิน" ."\n"
      ."วันที่ " .DateThai($vstdate) ."\n"  
      ."เวลา 08.00-16.00 น. (เวรเช้า)" ."\n"
      ."ผู้ป่วยในเวร " .$visit ." ราย" ."\n"       
      ." -Emergent " .$Emergent ." ราย" ."\n"
      ." -Urgent " .$Urgent ." ราย" ."\n"
      ." -Acute illness " .$Acute_illness ." ราย" ."\n" 
      ." -Non Acute illness " .$Non_acute_illness ." ราย" ."\n" ."\n"
      ."บันทึก Productivity " ."\n"
      . $url. "\n";

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4747192701", "-4729376994"]; //งานอุบัติเหตุฉุกเฉิน-รพ.หัวตะพาน,Test_Notify_Group2

      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";

            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }

      return response()->json(['success' => 'success'], 200);   
}

// ER รายงานสถานะการณ์สรุปบ่าย รัน 00.01 น.
public function er_service_o()
{
      $service = DB::connection('hosxp')->select('
            select date(DATE_ADD(now(), INTERVAL -1 DAY )) AS vstdate,count(DISTINCT vn) as visit,                 
            SUM(CASE WHEN er_emergency_type IN ("1","2") THEN 1	ELSE 0 END) AS "Emergent",
            SUM(CASE WHEN er_emergency_type ="3" THEN 1 ELSE 0 END) AS "Urgent",
            SUM(CASE WHEN er_emergency_type ="4" THEN 1 ELSE 0 END) AS "Acute_illness",
            SUM(CASE WHEN er_emergency_type ="5" THEN 1 ELSE 0 END) AS "Non_acute_illness"
            FROM er_regist  WHERE DATE(enter_er_time) = date(DATE_ADD(now(), INTERVAL -1 DAY )) 
            AND TIME(enter_er_time) BETWEEN "16:00:00" AND "23:59:59"');         

      foreach ($service as $row){
            $vstdate=$row->vstdate;
            $visit=$row->visit;
            $Emergent= $row->Emergent;
            $Urgent=$row->Urgent;
            $Acute_illness=$row->Acute_illness;
            $Non_acute_illness=$row->Non_acute_illness;
            $url=route('nurse_productivity_er_afternoon');
      }
    
    //แจ้งเตือน Telegram

    $message = "Productivity งานอุบัติเหตุ-ฉุกเฉิน" ."\n"
    ."วันที่ " .DateThai($vstdate) ."\n"  
    ."เวลา 16.00-23.00 น. (เวรบ่าย)" ."\n"
    ."ผู้ป่วยในเวร " .$visit ." ราย" ."\n"       
    ." -Emergent " .$Emergent ." ราย" ."\n"
    ." -Urgent " .$Urgent ." ราย" ."\n"
    ." -Acute illness " .$Acute_illness ." ราย" ."\n" 
    ." -Non Acute illness " .$Non_acute_illness ." ราย" ."\n" ."\n"
    ."บันทึก Productivity " ."\n"
    . $url. "\n";

    $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
    $chat_ids = ["-4747192701", "-4729376994"]; //งานอุบัติเหตุฉุกเฉิน-รพ.หัวตะพาน,Test_Notify_Group2

    foreach ($chat_ids as $chat_id) {
          $url = "https://api.telegram.org/bot$token/sendMessage";

          $data = [
                'chat_id' => $chat_id,
                'text'    => $message
          ];

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_exec($ch);
          curl_close($ch);
          sleep(1);
    }

    return response()->json(['success' => 'success'], 200);  
}

// IPD รายงานสถานะการณ์สรุปเวรดึก รัน 08.00 น.
public function ipd_service_s()
{
      $service = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT i.an) AS patient_all,
            SUM(CASE WHEN i.dch_severe_type_id = 1 THEN 1 ELSE 0 END) AS "convalescent",
            SUM(CASE WHEN i.dch_severe_type_id = 2 THEN 1 ELSE 0 END) AS "moderate_ill",
            SUM(CASE WHEN i.dch_severe_type_id = 3 THEN 1 ELSE 0 END) AS "semi_critical_ill",
            SUM(CASE WHEN i.dch_severe_type_id = 4 THEN 1 ELSE 0 END) AS "critical_ill",
            SUM(CASE WHEN (i.dch_severe_type_id IS NULL OR i.dch_severe_type_id ="")
            THEN 1 ELSE 0 END) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("01") AND i.confirm_discharge = "N" ');         

      foreach ($service as $row){
            $patient_all=$row->patient_all;
            $convalescent= $row->convalescent;
            $moderate_ill=$row->moderate_ill;
            $semi_critical_ill=$row->semi_critical_ill;
            $critical_ill=$row->critical_ill;
            $severe_type_null=$row->severe_type_null;
            $url=route('nurse_productivity_ipd_night');
      }
    
     //แจ้งเตือน line     
     $line_token_db = DB::select('select line_token FROM line_tokens WHERE line_token_id IN ("1","7")'); 
     $line_token_array = array_column($line_token_db,'line_token');  

     $message = "งานผู้ป่วยในสามัญ".
      "\n". "วันที่ ". DateThai(date("Y-m-d")). 
      "\n". "ณ เวลา 08.00 น. (เวรดึก)". 
      "\n". "ผู้ป่วยในเวร ". $patient_all. " ราย".       
      "\n". " -Convalescent ". $convalescent. " ราย".
      "\n". " -Moderate ill ". $moderate_ill. " ราย".
      "\n". " -Semi critical ill ". $semi_critical_ill. " ราย". 
      "\n". " -Critical ill ". $critical_ill. " ราย".
      "\n". " -ไม่ระบุความรุนแรง ". $severe_type_null. " ราย"."\n".
      "\n". "บันทึก Productivity ". "\n". $url. "\n";

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
           
//แจ้งเตือน Telegram

      $message = "Productivity งานผู้ป่วยในสามัญ" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n" 
            ."ณ เวลา 08.00 น. (เวรดึก)" ."\n" 
            ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n"       
            ." -Convalescent " .$convalescent ." ราย" ."\n"
            ." -Moderate ill " .$moderate_ill ." ราย" ."\n"
            ." -Semi critical ill " .$semi_critical_ill ." ราย" ."\n" 
            ." -Critical ill " .$critical_ill ." ราย" ."\n" 
            ." -ไม่ระบุความรุนแรง ". $severe_type_null ." ราย" ."\n" ."\n"
            ."บันทึก Productivity " ."\n" 
            . $url ."\n";

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994","-4620837416"]; //Test_Notify_Group2,งานผู้ป่วยในสามัญ -รพ.หัวตะพาน

      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";

            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }

      return response()->json(['success' => 'success'], 200); 
    
}

// IPD รายงานสถานะการณ์สรุปเวรเช้า รัน 16.00 น.
public function ipd_service_i()
{
      $service = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT i.an) AS patient_all,
            SUM(CASE WHEN i.dch_severe_type_id = 1 THEN 1 ELSE 0 END) AS "convalescent",
            SUM(CASE WHEN i.dch_severe_type_id = 2 THEN 1 ELSE 0 END) AS "moderate_ill",
            SUM(CASE WHEN i.dch_severe_type_id = 3 THEN 1 ELSE 0 END) AS "semi_critical_ill",
            SUM(CASE WHEN i.dch_severe_type_id = 4 THEN 1 ELSE 0 END) AS "critical_ill",
            SUM(CASE WHEN (i.dch_severe_type_id IS NULL OR i.dch_severe_type_id ="")
            THEN 1 ELSE 0 END) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("01") AND i.confirm_discharge = "N" ');         

      foreach ($service as $row){
            $patient_all=$row->patient_all;
            $convalescent= $row->convalescent;
            $moderate_ill=$row->moderate_ill;
            $semi_critical_ill=$row->semi_critical_ill;
            $critical_ill=$row->critical_ill;
            $severe_type_null=$row->severe_type_null;
            $url=route('nurse_productivity_ipd_morning');
      }
    
//แจ้งเตือน Telegram

    $message = "Productivity งานผู้ป่วยในสามัญ" ."\n"
      ."วันที่ " .DateThai(date("Y-m-d")) ."\n" 
      ."ณ เวลา 16.00 น. (เวรเช้า)" ."\n" 
      ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n"       
      ." -Convalescent " .$convalescent ." ราย" ."\n"
      ." -Moderate ill " .$moderate_ill ." ราย" ."\n"
      ." -Semi critical ill " .$semi_critical_ill ." ราย" ."\n" 
      ." -Critical ill " .$critical_ill ." ราย" ."\n" 
      ." -ไม่ระบุความรุนแรง ". $severe_type_null ." ราย" ."\n" ."\n"
      ."บันทึก Productivity " ."\n" 
      . $url ."\n";

    $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
    $chat_ids = ["-4729376994","-4620837416"]; //Test_Notify_Group2,งานผู้ป่วยในสามัญ -รพ.หัวตะพาน

    foreach ($chat_ids as $chat_id) {
          $url = "https://api.telegram.org/bot$token/sendMessage";

          $data = [
                'chat_id' => $chat_id,
                'text'    => $message
          ];

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_exec($ch);
          curl_close($ch);
          sleep(1);
    }

    return response()->json(['success' => 'success'], 200); 
}

// IPD รายงานสถานะการณ์สรุปเวรบ่าย รัน 00.01 น.
public function ipd_service_o()
{
      $service = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT i.an) AS patient_all,
            SUM(CASE WHEN i.dch_severe_type_id = 1 THEN 1 ELSE 0 END) AS "convalescent",
            SUM(CASE WHEN i.dch_severe_type_id = 2 THEN 1 ELSE 0 END) AS "moderate_ill",
            SUM(CASE WHEN i.dch_severe_type_id = 3 THEN 1 ELSE 0 END) AS "semi_critical_ill",
            SUM(CASE WHEN i.dch_severe_type_id = 4 THEN 1 ELSE 0 END) AS "critical_ill",
            SUM(CASE WHEN (i.dch_severe_type_id IS NULL OR i.dch_severe_type_id ="")
            THEN 1 ELSE 0 END) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("01") AND i.confirm_discharge = "N" ');         

      foreach ($service as $row){
            $patient_all=$row->patient_all;
            $convalescent= $row->convalescent;
            $moderate_ill=$row->moderate_ill;
            $semi_critical_ill=$row->semi_critical_ill;
            $critical_ill=$row->critical_ill;
            $severe_type_null=$row->severe_type_null;
            $url=route('nurse_productivity_ipd_afternoon');
      }
    
//แจ้งเตือน Telegram

        $message = "Productivity งานผู้ป่วยในสามัญ" ."\n"
        ."วันที่ " .DateThai(date("Y-m-d")) ."\n" 
        ."ณ เวลา 00.00 น. (เวรบ่าย)" ."\n" 
        ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n"       
        ." -Convalescent " .$convalescent ." ราย" ."\n"
        ." -Moderate ill " .$moderate_ill ." ราย" ."\n"
        ." -Semi critical ill " .$semi_critical_ill ." ราย" ."\n" 
        ." -Critical ill " .$critical_ill ." ราย" ."\n" 
        ." -ไม่ระบุความรุนแรง ". $severe_type_null ." ราย" ."\n" ."\n"
        ."บันทึก Productivity " ."\n" 
        . $url ."\n";
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994","-4620837416"]; //Test_Notify_Group2,งานผู้ป่วยในสามัญ -รพ.หัวตะพาน
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200); 

}

// VIP รายงานสถานะการณ์สรุปเวรดึก รัน 08.00 น.
public function vip_service_s()
{
      $service = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT i.an) AS patient_all,
            SUM(CASE WHEN i.dch_severe_type_id = 1 THEN 1 ELSE 0 END) AS "convalescent",
            SUM(CASE WHEN i.dch_severe_type_id = 2 THEN 1 ELSE 0 END) AS "moderate_ill",
            SUM(CASE WHEN i.dch_severe_type_id = 3 THEN 1 ELSE 0 END) AS "semi_critical_ill",
            SUM(CASE WHEN i.dch_severe_type_id = 4 THEN 1 ELSE 0 END) AS "critical_ill",
            SUM(CASE WHEN (i.dch_severe_type_id IS NULL OR i.dch_severe_type_id ="")
            THEN 1 ELSE 0 END) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("03","08") AND i.confirm_discharge = "N" ');         

      foreach ($service as $row){
            $patient_all=$row->patient_all;
            $convalescent= $row->convalescent;
            $moderate_ill=$row->moderate_ill;
            $semi_critical_ill=$row->semi_critical_ill;
            $critical_ill=$row->critical_ill;
            $severe_type_null=$row->severe_type_null;
            $url=route('nurse_productivity_vip_night');
      }
    
//แจ้งเตือน Telegram

        $message = "Productivity งานผู้ป่วยใน VIP" ."\n"
        ."วันที่ " .DateThai(date("Y-m-d")) ."\n" 
        ."ณ เวลา 08.00 น. (เวรดึก)" ."\n" 
        ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n"       
        ." -Convalescent " .$convalescent ." ราย" ."\n"
        ." -Moderate ill " .$moderate_ill ." ราย" ."\n"
        ." -Semi critical ill " .$semi_critical_ill ." ราย" ."\n" 
        ." -Critical ill " .$critical_ill ." ราย" ."\n" 
        ." -ไม่ระบุความรุนแรง ". $severe_type_null ." ราย" ."\n" ."\n"
        ."บันทึก Productivity " ."\n" 
        . $url ."\n";
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994","-4642404406"]; //Test_Notify_Group2,งานผู้ป่วยใน VIP-รพ.หัวตะพาน
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200); 
}

// VIP รายงานสถานะการณ์สรุปเวรเช้า รัน 16.00 น.
public function vip_service_i()
{
      $service = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT i.an) AS patient_all,
            SUM(CASE WHEN i.dch_severe_type_id = 1 THEN 1 ELSE 0 END) AS "convalescent",
            SUM(CASE WHEN i.dch_severe_type_id = 2 THEN 1 ELSE 0 END) AS "moderate_ill",
            SUM(CASE WHEN i.dch_severe_type_id = 3 THEN 1 ELSE 0 END) AS "semi_critical_ill",
            SUM(CASE WHEN i.dch_severe_type_id = 4 THEN 1 ELSE 0 END) AS "critical_ill",
            SUM(CASE WHEN (i.dch_severe_type_id IS NULL OR i.dch_severe_type_id ="")
            THEN 1 ELSE 0 END) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("03","08") AND i.confirm_discharge = "N" ');         

      foreach ($service as $row){
            $patient_all=$row->patient_all;
            $convalescent= $row->convalescent;
            $moderate_ill=$row->moderate_ill;
            $semi_critical_ill=$row->semi_critical_ill;
            $critical_ill=$row->critical_ill;
            $severe_type_null=$row->severe_type_null;
            $url=route('nurse_productivity_vip_morning');
      }
    
//แจ้งเตือน Telegram

        $message = "Productivity งานผู้ป่วยใน VIP" ."\n"
        ."วันที่ " .DateThai(date("Y-m-d")) ."\n" 
        ."ณ เวลา 16.00 น. (เวรเช้า)" ."\n" 
        ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n"       
        ." -Convalescent " .$convalescent ." ราย" ."\n"
        ." -Moderate ill " .$moderate_ill ." ราย" ."\n"
        ." -Semi critical ill " .$semi_critical_ill ." ราย" ."\n" 
        ." -Critical ill " .$critical_ill ." ราย" ."\n" 
        ." -ไม่ระบุความรุนแรง ". $severe_type_null ." ราย" ."\n" ."\n"
        ."บันทึก Productivity " ."\n" 
        . $url ."\n";
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994","-4642404406"]; //Test_Notify_Group2,งานผู้ป่วยใน VIP-รพ.หัวตะพาน
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200); 
}

// VIP รายงานสถานะการณ์สรุปเวรบ่าย รัน 00.01 น.
public function vip_service_o()
{
      $service = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT i.an) AS patient_all,
            SUM(CASE WHEN i.dch_severe_type_id = 1 THEN 1 ELSE 0 END) AS "convalescent",
            SUM(CASE WHEN i.dch_severe_type_id = 2 THEN 1 ELSE 0 END) AS "moderate_ill",
            SUM(CASE WHEN i.dch_severe_type_id = 3 THEN 1 ELSE 0 END) AS "semi_critical_ill",
            SUM(CASE WHEN i.dch_severe_type_id = 4 THEN 1 ELSE 0 END) AS "critical_ill",
            SUM(CASE WHEN (i.dch_severe_type_id IS NULL OR i.dch_severe_type_id ="")
            THEN 1 ELSE 0 END) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("03","08") AND i.confirm_discharge = "N" ');         

      foreach ($service as $row){
            $patient_all=$row->patient_all;
            $convalescent= $row->convalescent;
            $moderate_ill=$row->moderate_ill;
            $semi_critical_ill=$row->semi_critical_ill;
            $critical_ill=$row->critical_ill;
            $severe_type_null=$row->severe_type_null;
            $url=route('nurse_productivity_vip_afternoon');
      }
    
//แจ้งเตือน Telegram

        $message = "Productivity งานผู้ป่วยใน VIP" ."\n"
        ."วันที่ " .DateThai(date("Y-m-d")) ."\n" 
        ."ณ เวลา 00.00 น. (เวรบ่าย)" ."\n" 
        ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n"       
        ." -Convalescent " .$convalescent ." ราย" ."\n"
        ." -Moderate ill " .$moderate_ill ." ราย" ."\n"
        ." -Semi critical ill " .$semi_critical_ill ." ราย" ."\n" 
        ." -Critical ill " .$critical_ill ." ราย" ."\n" 
        ." -ไม่ระบุความรุนแรง ". $severe_type_null ." ราย" ."\n" ."\n"
        ."บันทึก Productivity " ."\n" 
        . $url ."\n";
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994","-4642404406"]; //Test_Notify_Group2,งานผู้ป่วยใน VIP-รพ.หัวตะพาน
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200); 
}

// OPD รายงานสถานะการณ์สรุปผู้ป่วยนอกเวรเช้า รัน 16.00 น.
public function opd_service_i()
{
      $service = DB::connection('hosxp')->select('
      SELECT COUNT(DISTINCT vn) as patient_all,
      sum(CASE WHEN main_dep = "002" THEN 1 ELSE 0 END) AS opd,
      sum(CASE WHEN main_dep = "032" THEN 1 ELSE 0 END) AS ari
      FROM ovst WHERE vstdate = DATE(NOW()) AND main_dep IN ("002","032")
      AND vsttime BETWEEN "00:00:00" AND "15:59:59" ');         

      foreach ($service as $row){
            $patient_all=$row->patient_all;
            $opd= $row->opd;
            $ari=$row->ari;
            $url=route('nurse_productivity_opd_morning');
      }
    
     //แจ้งเตือน line     
     $line_token_db = DB::select('select line_token FROM line_tokens WHERE line_token_id IN ("1","10")'); 
     $line_token_array = array_column($line_token_db,'line_token');  

     $message = "งานผู้ป่วยนอก".
      "\n". "วันที่ ". DateThai(date("Y-m-d")).
      "\n". "ณ เวลา 16.00 น.(เวรเช้า)". 
      "\n". "ผู้ป่วยในเวร ". $patient_all. " ราย".       
      "\n". " -OPD ". $opd. " ราย".
      "\n". " -ARI ". $ari. " ราย".
      "\n". "บันทึก Productivity ". "\n". $url. "\n";

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

//แจ้งเตือน Telegram

      $message = "Productivity งานผู้ป่วยนอก" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."ณ เวลา 16.00 น.(เวรเช้า)" ."\n" 
            ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n"       
            ." -OPD " .$opd ." ราย" ."\n"
            ." -ARI " .$ari ." ราย" ."\n"
            ."บันทึก Productivity " ."\n"
            .$url ."\n";
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994","-4614719699"]; //Test_Notify_Group2,งานผู้ป่วยนอก-รพ.หัวตะพาน
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200); 
}

// OPD รายงานสถานะการณ์สรุปผู้ป่วยนอกเวร bd รัน 19.00 น.
public function opd_service_bd()
{
      $service = DB::connection('hosxp')->select('
      SELECT COUNT(DISTINCT vn) as patient_all,
      sum(CASE WHEN main_dep = "002" THEN 1 ELSE 0 END) AS opd,
      sum(CASE WHEN main_dep = "032" THEN 1 ELSE 0 END) AS ari
      FROM ovst WHERE vstdate = DATE(NOW()) AND main_dep IN ("002","032")
      AND vsttime BETWEEN "16:00:00" AND "18:59:59" ');         

      foreach ($service as $row){
            $patient_all=$row->patient_all;
            $opd= $row->opd;
            $ari=$row->ari;
            $url=route('nurse_productivity_opd_bd');
      }
    
//แจ้งเตือน Telegram

      $message = "Productivity งานผู้ป่วยนอก" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."ณ เวลา 19.00 น.(เวร BD)" ."\n" 
            ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n"       
            ." -OPD " .$opd ." ราย" ."\n"
            ." -ARI " .$ari ." ราย" ."\n"
            ."บันทึก Productivity " ."\n"
            .$url ."\n";
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994","-4614719699"]; //Test_Notify_Group2,งานผู้ป่วยนอก-รพ.หัวตะพาน
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200); 
}

// NCD รายงานสถานะการณ์สรุปผู้ป่วย NCD เวรเช้า รัน 16.00 น.
public function ncd_service_i()
{
      $service = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS patient_all
            FROM opd_dep_queue o1, ovst o2 WHERE o1.depcode IN ("011")
            AND o1.vn = o2.vn AND o2.vstdate = DATE(NOW()) 
            AND o2.vsttime BETWEEN "00:00:00" AND "15:59:59" ');         

      foreach ($service as $row){
            $patient_all=$row->patient_all;
            $url=route('nurse_productivity_ncd_morning');
      }
      
//แจ้งเตือน Telegram

      $message = "Productivity งานผู้ป่วย NCD" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."ณ เวลา 16.00 น.(เวรเช้า)" ."\n" 
            ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n" 
            ."บันทึก Productivity " ."\n" 
            . $url ."\n";
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994","-4689585177"]; //Test_Notify_Group2,คลินิกเบาหวานความดัน-รพ.หัวตะพาน
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200);  
}

// LR รายงานสถานะการณ์สรุปเวรดึก รัน 08.00 น.
public function lr_service_s()
{
      $service_ipd = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT i.an),0) AS patient_all,
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 1 THEN 1 ELSE 0 END),0) AS "convalescent",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 2 THEN 1 ELSE 0 END),0) AS "moderate_ill",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 3 THEN 1 ELSE 0 END),0) AS "semi_critical_ill",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 4 THEN 1 ELSE 0 END),0) AS "critical_ill",
            IFNULL(SUM(CASE WHEN (i.dch_severe_type_id IS NULL OR i.dch_severe_type_id ="")
            THEN 1 ELSE 0 END),0) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("02") AND i.confirm_discharge = "N"'); 
      $service_opd_normal = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_normal FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority = "0"
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "00:00:00" AND "07:59:59"'); 
      $service_opd_high = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_high FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority IN ("1","2")
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "00:00:00" AND "07:59:59"');       

      foreach ($service_opd_normal as $row){
            $opd_normal= $row->opd_normal;    
      }
      foreach ($service_opd_high as $row){
            $opd_high= $row->opd_high;    
      }
      foreach ($service_ipd as $row){
            $convalescent= $row->convalescent;
            $moderate_ill=$row->moderate_ill;
            $semi_critical_ill=$row->semi_critical_ill;
            $critical_ill=$row->critical_ill;
            $severe_type_null=$row->severe_type_null;
            $url=route('nurse_productivity_lr_night');
      }    

//แจ้งเตือน Telegram

      $message = "Productivity งานห้องคลอด" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 00.00-08.00 น.(เวรดึก)" ."\n" 
            ."ผู้ป่วยนอกในเวร " ."\n"
            ." -ความเร่งด่วนปกติ " .$opd_normal ." ราย" ."\n"   
            ." -ความเร่งด่วนมาก " .$opd_high ." ราย" ."\n"     
            ."ผู้ป่วยสูติกรรมในเวร " ."\n"    
            ." -Convalescent " .$convalescent ." ราย" ."\n"
            ." -Moderate ill " .$moderate_ill ." ราย" ."\n"
            ." -Semi critical ill " .$semi_critical_ill ." ราย" ."\n" 
            ." -Critical ill " .$critical_ill ." ราย" ."\n"
            ."บันทึก Productivity " ."\n"
            . $url ."\n";
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994","-4725475158"]; //Test_Notify_Group2,งานห้องคลอด-รพ.หัวตะพาน
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200);           
  
}

// LR รายงานสถานะการณ์สรุปเวรเช้า รัน 16.00 น.
public function lr_service_i()
{
      $service_ipd = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT i.an),0) AS patient_all,
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 1 THEN 1 ELSE 0 END),0) AS "convalescent",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 2 THEN 1 ELSE 0 END),0) AS "moderate_ill",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 3 THEN 1 ELSE 0 END),0) AS "semi_critical_ill",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 4 THEN 1 ELSE 0 END),0) AS "critical_ill",
            IFNULL(SUM(CASE WHEN (i.dch_severe_type_id IS NULL OR i.dch_severe_type_id ="")
            THEN 1 ELSE 0 END),0) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("02") AND i.confirm_discharge = "N"'); 
      $service_opd_normal = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_normal FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority = "0"
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "08:00:00" AND "15:59:59"'); 
      $service_opd_high = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_high FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority IN ("1","2")
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "08:00:00" AND "15:59:59"');       

      foreach ($service_opd_normal as $row){
            $opd_normal= $row->opd_normal;    
      }
      foreach ($service_opd_high as $row){
            $opd_high= $row->opd_high;    
      }
      foreach ($service_ipd as $row){
            $convalescent= $row->convalescent;
            $moderate_ill=$row->moderate_ill;
            $semi_critical_ill=$row->semi_critical_ill;
            $critical_ill=$row->critical_ill;
            $severe_type_null=$row->severe_type_null;
            $url=route('nurse_productivity_lr_morning');
      }
    
//เปิดแจ้งเตือน Telegram
      $message = "Productivity งานห้องคลอด" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 08.00-16.00 น.(เวรเช้า)" ."\n" 
            ."ผู้ป่วยนอกในเวร " ."\n"
            ." -ความเร่งด่วนปกติ " .$opd_normal ." ราย" ."\n"   
            ." -ความเร่งด่วนมาก " .$opd_high ." ราย" ."\n"     
            ."ผู้ป่วยสูติกรรมในเวร " ."\n"    
            ." -Convalescent " .$convalescent ." ราย" ."\n"
            ." -Moderate ill " .$moderate_ill ." ราย" ."\n"
            ." -Semi critical ill " .$semi_critical_ill ." ราย" ."\n" 
            ." -Critical ill " .$critical_ill ." ราย" ."\n"
            ."บันทึก Productivity " ."\n"
            . $url ."\n";
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994","-4725475158"]; //Test_Notify_Group2,งานห้องคลอด-รพ.หัวตะพาน
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
//ปิดแจ้งเตือน Telegram

      return response()->json(['success' => 'success'], 200);  
}

// LR รายงานสถานะการณ์สรุปเวรบ่าย รัน 00.01 น.
public function lr_service_o()
{
      $service_ipd = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT i.an),0) AS patient_all,
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 1 THEN 1 ELSE 0 END),0) AS "convalescent",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 2 THEN 1 ELSE 0 END),0) AS "moderate_ill",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 3 THEN 1 ELSE 0 END),0) AS "semi_critical_ill",
            IFNULL(SUM(CASE WHEN i.dch_severe_type_id = 4 THEN 1 ELSE 0 END),0) AS "critical_ill",
            IFNULL(SUM(CASE WHEN (i.dch_severe_type_id IS NULL OR i.dch_severe_type_id ="")
            THEN 1 ELSE 0 END),0) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("02") AND i.confirm_discharge = "N"'); 
      $service_opd_normal = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_normal FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority = "0"
            AND o2.vstdate = date(DATE_ADD(now(), INTERVAL -1 DAY )) AND o2.vsttime BETWEEN "16:00:00" AND "23:59:59"'); 
      $service_opd_high = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_high FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority IN ("1","2")
            AND o2.vstdate = date(DATE_ADD(now(), INTERVAL -1 DAY )) AND o2.vsttime BETWEEN "16:00:00" AND "23:59:59"');       

      foreach ($service_opd_normal as $row){
            $opd_normal= $row->opd_normal;    
      }
      foreach ($service_opd_high as $row){
            $opd_high= $row->opd_high;    
      }
      foreach ($service_ipd as $row){
            $convalescent= $row->convalescent;
            $moderate_ill=$row->moderate_ill;
            $semi_critical_ill=$row->semi_critical_ill;
            $critical_ill=$row->critical_ill;
            $severe_type_null=$row->severe_type_null;
            $url=route('nurse_productivity_lr_afternoon');
      }
    
 //แจ้งเตือน Telegram

      $message = "Productivity งานห้องคลอด" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 16.00-00.00 น.(เวรบ่าย)" ."\n" 
            ."ผู้ป่วยนอกในเวร " ."\n"
            ." -ความเร่งด่วนปกติ " .$opd_normal ." ราย" ."\n"   
            ." -ความเร่งด่วนมาก " .$opd_high ." ราย" ."\n"     
            ."ผู้ป่วยสูติกรรมในเวร " ."\n"    
            ." -Convalescent " .$convalescent ." ราย" ."\n"
            ." -Moderate ill " .$moderate_ill ." ราย" ."\n"
            ." -Semi critical ill " .$semi_critical_ill ." ราย" ."\n" 
            ." -Critical ill " .$critical_ill ." ราย" ."\n"
            ."บันทึก Productivity " ."\n"
            . $url ."\n";
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994","-4725475158"]; //Test_Notify_Group2,งานห้องคลอด-รพ.หัวตะพาน
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200);  
}

// OR รายงานสถานะการณ์สรุปผู้ป่วย OR เวรเช้า รัน 16.00 น.
public function or_service_i()
{
      $service = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT operation_id),0) AS patient_all
            FROM operation_list WHERE request_operation_date = DATE(NOW()) 
            AND request_operation_time BETWEEN "00:00:00" AND "15:59:59"');         

      foreach ($service as $row){
            $patient_all=$row->patient_all;
            $url=route('nurse_productivity_or_morning');
      }   

//แจ้งเตือน Telegram

      $message = "งานห้องผ่าตัด OR" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."ณ เวลา 16.00 น.(เวรเช้า)" ."\n"
            ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n" 
            ."บันทึก Productivity " ."\n" 
            .$url ."\n";
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994"]; //Test_Notify_Group2
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200);  
}

// CKD รายงานสถานะการณ์สรุปผู้ป่วย CKD  เวรเช้า รัน 16.00 น.
public function ckd_service_i()
{
      $service = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS patient_all 
            FROM opd_dep_queue o1, ovst o2 WHERE o1.depcode IN ("031") AND o1.vn = o2.vn  
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "00:00:00" AND "15:59:59"');         

      foreach ($service as $row){
            $patient_all=$row->patient_all;
            $url=route('nurse_productivity_ckd_morning');
      }    

//แจ้งเตือน Telegram

      $message = "ผู้ป่วย CKD" ."\n"
      ."วันที่ ". DateThai(date("Y-m-d")) ."\n"
      ."ณ เวลา 16.00 น.(เวรเช้า)" ."\n"
      ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n" 
      ."บันทึก Productivity " ."\n"
      .$url ."\n";
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994"]; //Test_Notify_Group2
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200);  
}

// HD รายงานสถานะการณ์สรุปผู้ป่วย HD  เวรเช้า รัน 19.00 น.
public function hd_service()
{
      $service = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS patient_all 
            FROM opd_dep_queue o1, ovst o2 WHERE o1.depcode IN ("033") AND o1.vn = o2.vn  
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "00:00:00" AND "21:59:59"');         

      foreach ($service as $row){
            $patient_all=$row->patient_all;
            $url=route('nurse_productivity_hd_service');
      }    

//แจ้งเตือน Telegram

      $message = "ศูนย์ฟอกไต HD รพ." ."\n"
      ."วันที่ ". DateThai(date("Y-m-d")) ."\n"
      ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n" 
      ."บันทึก Productivity " ."\n"
      . $url ."\n";
  
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994"]; //Test_Notify_Group2
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200);  
}

// HRD Health_screen รายงานสถานะการคัดกรองสุขภาพเจ้าหน้าที่ รันทุกวันที่ 25-31 เวลา 9.00,15.00 น. ของทุกเดือน
public function health_screen()
{
      $health_screen = DB::connection('backoffice')->select('
            SELECT(SELECT COUNT(DISTINCT HEALTH_SCREEN_PERSON_ID) FROM health_screen
            WHERE YEAR(HEALTH_SCREEN_DATE) = YEAR(DATE(NOW())) AND MONTH(HEALTH_SCREEN_DATE) = MONTH(DATE(NOW()))) AS screen,
            (SELECT COUNT(DISTINCT ID) FROM hrd_person WHERE HR_STATUS_ID = "1" AND ID NOT IN (SELECT HEALTH_SCREEN_PERSON_ID FROM health_screen
            WHERE YEAR(HEALTH_SCREEN_DATE) = YEAR(DATE(NOW())) AND MONTH(HEALTH_SCREEN_DATE) = MONTH(DATE(NOW())))) AS notscreen');         

      foreach ($health_screen as $row){
            $hrd_person=$row->screen+$row->notscreen;
            $screen=$row->screen;
            $notscreen=$row->notscreen;            
            $url=route('health_notscreen');
            $url1=url('https://shorturl.at/yEISX');
      }    

//แจ้งเตือน Telegram

      $message = "ข้อมูลคัดกรองสุขภาพ" ."\n" 
      ."ณ วันที่ " .DatetimeThai(date('Y-m-d H:i:s')) ."\n"
      ."เจ้าหน้าที่ทั้งหมด " .$hrd_person ." ราย" ."\n" 
      ." - คัดกรอง " .$screen ." ราย" ."\n" 
      ." - ยังไม่คัดกรอง " .$notscreen ." ราย" ."\n" ."\n"
      ."ตรวจสอบรายชื่อที่ยังไม่คัดกรอง" ."\n"
      . $url ."\n" ."\n"
      ."ขั้นตอนการลงข้อมูลคัดกรอง" ."\n"
      .$url1 ."\n" ."\n"
      ."**แจ้งให้เจ้าหน้าที่ทุกท่านเข้าโปรแกรม BackOffice เพื่อคัดกรองสุขภาพตนเองระหว่างวันที่ 25-30 ของทุกเดือน หากมีข้อสงสัยสอบถามข้อมูลเพิ่มเติมที่ฝ่ายส่งเสริมสุขภาพ โทร.132-133 **"
      ."\n";
      
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4729376994"]; //Test_Notify_Group2
  
      foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
  
            $data = [
                  'chat_id' => $chat_id,
                  'text'    => $message
            ];
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
      }
  
      return response()->json(['success' => 'success'], 200);  
}

}
