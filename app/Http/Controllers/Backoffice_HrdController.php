<?php

namespace App\Http\Controllers;
use PDF;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Nurse_productivity_er;
use App\Models\Nurse_productivity_ipd;
use App\Models\Nurse_productivity_vip;
use App\Models\Nurse_productivity_opd;
use App\Models\Nurse_productivity_ncd;
use App\Models\Nurse_productivity_lr;
use App\Models\Nurse_productivity_or;
use App\Models\Nurse_productivity_ckd;
use App\Models\Nurse_productivity_hd;

class Backoffice_HrdController extends Controller
{
   
    //Check Login
public function __construct()
{
      $this->middleware('auth')->only(['index','health_screen','nurse_productivity_er','nurse_productivity_ipd',
      'nurse_productivity_vip','nurse_productivity_opd','nurse_productivity_ncd','nurse_productivity_lr',
      'nurse_productivity_or','nurse_productivity_ckd','nurse_productivity_hd']);
}

//Create index
public function index()
{
      $hrd_total=DB::connection('backoffice')->table('hrd_person')->where('HR_STATUS_ID','1')->count();
      $hrd_person_type=DB::connection('backoffice')->select('
            SELECT hrt.HR_PERSON_TYPE_NAME,COUNT(DISTINCT hr.ID) AS total
            FROM hrd_person hr
            LEFT JOIN hrd_person_type hrt ON hr.HR_PERSON_TYPE_ID=hrt.HR_PERSON_TYPE_ID
            WHERE hr.HR_STATUS_ID = 1 GROUP BY hr.HR_PERSON_TYPE_ID');
      $hrd_type_name = array_column($hrd_person_type,'HR_PERSON_TYPE_NAME');              
      $hrd_type_total = array_column($hrd_person_type,'total');

      $hrd_person_sex=DB::connection('backoffice')->select('
            SELECT hs.SEX_NAME,COUNT(DISTINCT hr.ID) AS total
            FROM hrd_person hr
            LEFT JOIN hrd_sex hs ON hr.SEX=hs.SEX_ID
            WHERE hr.HR_STATUS_ID = 1 GROUP BY hr.SEX');
      $hrd_sex = array_column($hrd_person_sex,'SEX_NAME');              
      $hrd_sex_total = array_column($hrd_person_sex,'total');

      return view('backoffice_hrd.index',compact('hrd_type_name','hrd_type_total','hrd_sex','hrd_sex_total','hrd_total'));            
}

//Create health_screen
public function health_screen(Request $request)
{
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      if($start_date == '' || $end_date == null)
      {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
      if($end_date == '' || $end_date == null)
      {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

      $health_screen_sum=DB::connection('backoffice')->select('
            SELECT SUM(CASE WHEN hs.HEALTH_SCREEN_BODY < "18.5" THEN 1 ELSE 0 END) AS bmi_1,
            SUM(CASE WHEN hs.HEALTH_SCREEN_BODY BETWEEN "18.5" AND "22.9" THEN 1 ELSE 0 END) AS bmi_2,
            SUM(CASE WHEN hs.HEALTH_SCREEN_BODY BETWEEN "23.0" AND "24.9" THEN 1 ELSE 0 END) AS bmi_3,
            SUM(CASE WHEN hs.HEALTH_SCREEN_BODY BETWEEN "25.0" AND "29.9" THEN 1 ELSE 0 END) AS bmi_4,
            SUM(CASE WHEN hs.HEALTH_SCREEN_BODY > "30" THEN 1 ELSE 0 END) AS bmi_5,
            SUM(CASE WHEN hs.HEALTH_SCREEN_H_1 = "have" THEN 1 ELSE 0 END) AS dm_1,
            SUM(CASE WHEN hs.HEALTH_SCREEN_H_1 = "nothave" THEN 1 ELSE 0 END) AS dm_2,
            SUM(CASE WHEN hs.HEALTH_SCREEN_H_1 = "never" THEN 1 ELSE 0 END) AS dm_3,
            SUM(CASE WHEN hs.HEALTH_SCREEN_H_2 = "have" THEN 1 ELSE 0 END) AS ht_1,
            SUM(CASE WHEN hs.HEALTH_SCREEN_H_2 = "nothave" THEN 1 ELSE 0 END) AS ht_2,
            SUM(CASE WHEN hs.HEALTH_SCREEN_H_2 = "never" THEN 1 ELSE 0 END) AS ht_3,
            SUM(CASE WHEN hs.HEALTH_SCREEN_H_30 = "have" THEN 1 ELSE 0 END) AS accident_1,
            SUM(CASE WHEN hs.HEALTH_SCREEN_H_30 = "nothave" THEN 1 ELSE 0 END) AS accident_2,
            SUM(CASE WHEN hs.HEALTH_SCREEN_H_30 = "never" THEN 1 ELSE 0 END) AS accident_3,
            SUM(CASE WHEN hs.HEALTH_SCREEN_H_31 = "have" THEN 1 ELSE 0 END) AS infect_1,
            SUM(CASE WHEN hs.HEALTH_SCREEN_H_31 = "nothave" THEN 1 ELSE 0 END) AS infect_2,
            SUM(CASE WHEN hs.HEALTH_SCREEN_H_31 = "never" THEN 1 ELSE 0 END) AS infect_3,
            SUM(CASE WHEN hs.HEALTH_SCREEN_EXERCISE = "1" THEN 1 ELSE 0 END) AS exer_1,
            SUM(CASE WHEN hs.HEALTH_SCREEN_EXERCISE = "2" THEN 1 ELSE 0 END) AS exer_2,
            SUM(CASE WHEN hs.HEALTH_SCREEN_EXERCISE = "3" THEN 1 ELSE 0 END) AS exer_3,
            SUM(CASE WHEN hs.HEALTH_SCREEN_EXERCISE = "4" THEN 1 ELSE 0 END) AS exer_4
            FROM  health_screen hs 
            WHERE hs.HEALTH_SCREEN_DATE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND hs.HEALTH_SCREEN_DATE = (SELECT MAX(hs1.HEALTH_SCREEN_DATE) FROM health_screen hs1 
            WHERE hs1.HEALTH_SCREEN_PERSON_ID=hs.HEALTH_SCREEN_PERSON_ID 
            AND hs1.HEALTH_SCREEN_DATE BETWEEN "'.$start_date.'" AND "'.$end_date.'")');
      foreach ($health_screen_sum as $row){
            $bmi_1=$row->bmi_1; $bmi_2=$row->bmi_2; $bmi_3=$row->bmi_3; $bmi_4=$row->bmi_4; $bmi_5=$row->bmi_5;
            $dm_1=$row->dm_1; $dm_2=$row->dm_2; $dm_3=$row->dm_3; $ht_1=$row->ht_1; $ht_2=$row->ht_2; $ht_3=$row->ht_3;
            $accident_1=$row->accident_1; $accident_2=$row->accident_2; $accident_3=$row->accident_3;
            $infect_1=$row->infect_1; $infect_2=$row->infect_2; $infect_3=$row->infect_3;
            $exer_1=$row->exer_1; $exer_2=$row->exer_2; $exer_3=$row->exer_3; $exer_4=$row->exer_4;
      }

      $health_screen=DB::connection('backoffice')->select('
            SELECT hs.HEALTH_SCREEN_PERSON_ID,CONCAT(hp.HR_PREFIX_NAME,hr.HR_FNAME,SPACE(1),hr.HR_LNAME) AS hrd_name,
            hd.HR_DEPARTMENT_SUB_SUB_NAME,hs.HEALTH_SCREEN_DATE,hs.HEALTH_SCREEN_AGE,hs.HEALTH_SCREEN_HEIGHT,
            hs.HEALTH_SCREEN_WEIGHT,hs.HEALTH_SCREEN_BODY,CASE WHEN HEALTH_SCREEN_BODY < "18.5" THEN "นน.ต่ำกว่าเกณฑ์"
            WHEN HEALTH_SCREEN_BODY BETWEEN "18.5" AND "22.9" THEN "สมส่วน" WHEN HEALTH_SCREEN_BODY BETWEEN "23.0" AND "24.9" THEN "น้ำหนักเกิน"
            WHEN HEALTH_SCREEN_BODY BETWEEN "25.0" AND "29.9" THEN "โรคอ้วน" WHEN HEALTH_SCREEN_BODY > "30" THEN "โรคอ้วนอันตราย" END AS bmi,
            CASE WHEN hs.HEALTH_SCREEN_H_1 = "have" THEN "เป็น" WHEN hs.HEALTH_SCREEN_H_1 = "nothave" THEN "ไม่เป็น"
            WHEN hs.HEALTH_SCREEN_H_1 = "never" THEN "ไม่เคยตรวจ" END AS dm,
            CASE WHEN hs.HEALTH_SCREEN_H_2 = "have" THEN "เป็น" WHEN hs.HEALTH_SCREEN_H_2 = "nothave" THEN "ไม่เป็น"
            WHEN hs.HEALTH_SCREEN_H_2 = "never" THEN "ไม่เคยตรวจ" END AS ht,
            CASE WHEN hs.HEALTH_SCREEN_H_30 = "have" THEN "มี" WHEN hs.HEALTH_SCREEN_H_30 = "nothave" THEN "ไม่มี"
            WHEN hs.HEALTH_SCREEN_H_30 = "never" THEN "ไม่เคยตรวจ" END AS accident,hs.HEALTH_SCREEN_H_30_COMMENT AS accident_comment,
            CASE WHEN hs.HEALTH_SCREEN_H_31 = "have" THEN "มี" WHEN hs.HEALTH_SCREEN_H_31 = "nothave" THEN "ไม่มี"
            WHEN hs.HEALTH_SCREEN_H_31 = "never" THEN "ไม่เคยตรวจ" END AS infect,hs.HEALTH_SCREEN_H_31_COMMENT AS infect_comment,
            CASE WHEN hs.HEALTH_SCREEN_EXERCISE = "1" THEN "ทุกวันครั้งละ 30นาที" WHEN hs.HEALTH_SCREEN_EXERCISE = "2" THEN "สัปดาห์ละ 3ครั้ง ครั้งละ 30นาที"
            WHEN hs.HEALTH_SCREEN_EXERCISE = "3" THEN "น้อยกว่าสัปดาห์ละ 3ครั้ง" WHEN hs.HEALTH_SCREEN_EXERCISE = "4" THEN "ไม่ออกกำลังกาย" END AS exer,
            CASE WHEN hs.HEALTH_SCREEN_SMOK = "smok" THEN "สูบ" WHEN hs.HEALTH_SCREEN_SMOK = "onsmok" THEN "ไม่สูบ"
            WHEN hs.HEALTH_SCREEN_SMOK = "usesmok" THEN "เคยสูบแต่เลิกแล้ว" END AS smok,
            CASE WHEN hs.HEALTH_SCREEN_DRINK = "drink" THEN "ดื่ม" WHEN hs.HEALTH_SCREEN_DRINK = "nodrink" THEN "ไม่ดื่ม"
            WHEN hs.HEALTH_SCREEN_DRINK = "usedrink" THEN "เคยดื่มแต่เลิกแล้ว" END AS drink,hb.HR_BLOODGROUP_NAME,hr.HR_PHONE
            FROM  health_screen hs LEFT JOIN hrd_person hr ON hr.ID=hs.HEALTH_SCREEN_PERSON_ID
            LEFT JOIN hrd_prefix hp ON hp.HR_PREFIX_ID=hr.HR_PREFIX_ID
            LEFT JOIN hrd_department_sub_sub hd ON hd.HR_DEPARTMENT_SUB_SUB_ID=hr.HR_DEPARTMENT_SUB_SUB_ID
            LEFT JOIN hrd_bloodgroup hb ON hb.HR_BLOODGROUP_ID=hr.HR_BLOODGROUP_ID
            WHERE hs.HEALTH_SCREEN_DATE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND hs.HEALTH_SCREEN_DATE = (SELECT MAX(hs1.HEALTH_SCREEN_DATE) FROM health_screen hs1 
            WHERE hs1.HEALTH_SCREEN_PERSON_ID=hs.HEALTH_SCREEN_PERSON_ID 
            AND hs1.HEALTH_SCREEN_DATE BETWEEN "'.$start_date.'" AND "'.$end_date.'")
            ORDER BY hr.HR_DEPARTMENT_SUB_SUB_ID,hs.HEALTH_SCREEN_PERSON_ID,hs.HEALTH_SCREEN_DATE');  

      $health_notscreen=DB::connection('backoffice')->select('
            SELECT CONCAT(hp.HR_PREFIX_NAME,hr.HR_FNAME,SPACE(1),hr.HR_LNAME) AS hrd_name,IF(hr.SEX="M","ชาย","หญิง") AS SEX,
            TIMESTAMPDIFF(YEAR,HR_BIRTHDAY,DATE(NOW())) AS AGE,hr.HR_CID,hpt.HR_PERSON_TYPE_NAME,
            hdss.HR_DEPARTMENT_SUB_SUB_NAME,hb.HR_BLOODGROUP_NAME,hr.HR_PHONE 
            FROM hrd_person hr
            LEFT JOIN hrd_prefix hp ON hp.HR_PREFIX_ID=hr.HR_PREFIX_ID
            LEFT JOIN hrd_department_sub_sub hdss ON hdss.HR_DEPARTMENT_SUB_SUB_ID=hr.HR_DEPARTMENT_SUB_SUB_ID
            LEFT JOIN hrd_person_type hpt ON hpt.HR_PERSON_TYPE_ID=hr.HR_PERSON_TYPE_ID
            LEFT JOIN hrd_bloodgroup hb ON hb.HR_BLOODGROUP_ID=hr.HR_BLOODGROUP_ID
            WHERE hr.ID NOT IN (SELECT HEALTH_SCREEN_PERSON_ID FROM health_screen 
                  WHERE HEALTH_SCREEN_DATE BETWEEN "'.$start_date.'" AND "'.$end_date.'")
            AND hr.HR_STATUS_ID = "1" GROUP BY hr.ID
            ORDER BY hr.HR_DEPARTMENT_SUB_SUB_ID ,hr.HR_FNAME');   

      $request->session()->put('health_screen',$health_screen);
      $request->session()->put('health_notscreen',$health_notscreen);
      $request->session()->put('start_date',$start_date);
      $request->session()->put('end_date',$end_date);
      $request->session()->save();

      return view('backoffice_hrd.health_screen',compact('health_screen','health_notscreen','start_date','end_date',
            'bmi_1','bmi_2','bmi_3','bmi_4','bmi_5','dm_1','dm_2','dm_3','ht_1','ht_2','ht_3','accident_1','accident_2',
            'accident_3','infect_1','infect_2','infect_3','exer_1','exer_2','exer_3','exer_4'));            
}
// *************Excel*********************//
public function health_screen_excel()
{
      $health_screen = Session::get('health_screen');
      $start_date = Session::get('start_date');
      $end_date = Session::get('end_date');
      return view('backoffice_hrd.health_screen_excel',compact('health_screen','start_date','end_date'));
}
// *************Dom pdf*********************//
public function health_notscreen_pdf()
{
      $health_notscreen = Session::get('health_notscreen');
      $start_date = Session::get('start_date');
      $end_date = Session::get('end_date');
      $pdf = PDF::loadView('backoffice_hrd.health_notscreen_pdf', compact('health_notscreen','start_date','end_date'))
                  ->setPaper('A4', 'portrait');
      return @$pdf->stream();
}
// Create health_notscreen//
public function health_notscreen(Request $request)
{
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      if($start_date == '' || $end_date == null)
      {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
      if($end_date == '' || $end_date == null)
      {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
      $health_notscreen=DB::connection('backoffice')->select('
            SELECT CONCAT(hp.HR_PREFIX_NAME,hr.HR_FNAME,SPACE(1),hr.HR_LNAME) AS hrd_name,IF(hr.SEX="M","ชาย","หญิง") AS SEX,
            TIMESTAMPDIFF(YEAR,HR_BIRTHDAY,DATE(NOW())) AS AGE,hr.HR_CID,hpt.HR_PERSON_TYPE_NAME,
            hdss.HR_DEPARTMENT_SUB_SUB_NAME,hb.HR_BLOODGROUP_NAME,hr.HR_PHONE 
            FROM hrd_person hr LEFT JOIN hrd_prefix hp ON hp.HR_PREFIX_ID=hr.HR_PREFIX_ID
            LEFT JOIN hrd_department_sub_sub hdss ON hdss.HR_DEPARTMENT_SUB_SUB_ID=hr.HR_DEPARTMENT_SUB_SUB_ID
            LEFT JOIN hrd_person_type hpt ON hpt.HR_PERSON_TYPE_ID=hr.HR_PERSON_TYPE_ID
            LEFT JOIN hrd_bloodgroup hb ON hb.HR_BLOODGROUP_ID=hr.HR_BLOODGROUP_ID
            WHERE hr.ID NOT IN (SELECT HEALTH_SCREEN_PERSON_ID FROM health_screen 
            WHERE YEAR(HEALTH_SCREEN_DATE) = YEAR(DATE(NOW())) AND MONTH(HEALTH_SCREEN_DATE) = MONTH(DATE(NOW())))
            AND hr.HR_STATUS_ID = "1" GROUP BY hr.ID
            ORDER BY hr.HR_DEPARTMENT_SUB_SUB_ID ,hr.HR_FNAME');   

      return view('backoffice_hrd.health_notscreen',compact('health_notscreen','start_date','end_date'));
}

// Create checkin//
public function checkin(Request $request)
{
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      if($start_date == '' || $end_date == null)
      {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
      if($end_date == '' || $end_date == null)
      {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}

      $checkin_type1=DB::connection('backoffice')->select('
            SELECT ID,ptname,HR_PHONE,HR_POSITION_NAME,HR_DEPARTMENT_SUB_SUB_NAME,HR_PERSON_TYPE_NAME,
            COUNT(DISTINCT SHIFT_DATE) AS sumday,
            SUM(CASE WHEN (SHIFT_ID <>"" OR SHIFT_ID IS NOT NULL) THEN 1 ELSE 0 END) AS sumshift
            FROM (SELECT p.ID,CONCAT(pn.HR_PREFIX_NAME,p.HR_FNAME,SPACE(1),p.HR_LNAME) AS ptname,
            p.HR_PHONE,pp.HR_POSITION_NAME,hd.HR_DEPARTMENT_SUB_SUB_NAME,pt.HR_PERSON_TYPE_NAME,
            cs.SHIFT_DATE,cs.SHIFT_ID 
            FROM hrd_person p
            LEFT JOIN hrd_prefix pn ON pn.HR_PREFIX_ID=p.HR_PREFIX_ID
            LEFT JOIN hrd_position pp ON pp.HR_POSITION_ID=p.HR_POSITION_ID
            LEFT JOIN hrd_person_type pt ON pt.HR_PERSON_TYPE_ID=p.HR_PERSON_TYPE_ID 
            LEFT JOIN hrd_department_sub_sub hd ON hd.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
            INNER JOIN checkin_shift_summary cs ON cs.USER_ID=p.ID
            WHERE cs.SHIFT_DATE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND p.HR_PERSON_TYPE_ID = "1") AS a
            GROUP BY ID ORDER BY HR_PERSON_TYPE_NAME,HR_DEPARTMENT_SUB_SUB_NAME,ptname');  

      $checkin_type2=DB::connection('backoffice')->select('
            SELECT ID,ptname,HR_PHONE,HR_POSITION_NAME,HR_DEPARTMENT_SUB_SUB_NAME,HR_PERSON_TYPE_NAME,
            COUNT(DISTINCT SHIFT_DATE) AS sumday,
            SUM(CASE WHEN (SHIFT_ID <>"" OR SHIFT_ID IS NOT NULL) THEN 1 ELSE 0 END) AS sumshift
            FROM (SELECT p.ID,CONCAT(pn.HR_PREFIX_NAME,p.HR_FNAME,SPACE(1),p.HR_LNAME) AS ptname,
            p.HR_PHONE,pp.HR_POSITION_NAME,hd.HR_DEPARTMENT_SUB_SUB_NAME,pt.HR_PERSON_TYPE_NAME,
            cs.SHIFT_DATE,cs.SHIFT_ID 
            FROM hrd_person p
            LEFT JOIN hrd_prefix pn ON pn.HR_PREFIX_ID=p.HR_PREFIX_ID
            LEFT JOIN hrd_position pp ON pp.HR_POSITION_ID=p.HR_POSITION_ID
            LEFT JOIN hrd_person_type pt ON pt.HR_PERSON_TYPE_ID=p.HR_PERSON_TYPE_ID 
            LEFT JOIN hrd_department_sub_sub hd ON hd.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
            INNER JOIN checkin_shift_summary cs ON cs.USER_ID=p.ID
            WHERE cs.SHIFT_DATE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND p.HR_PERSON_TYPE_ID = "2") AS a
            GROUP BY ID ORDER BY HR_PERSON_TYPE_NAME,HR_DEPARTMENT_SUB_SUB_NAME,ptname');  

      $checkin_type3=DB::connection('backoffice')->select('
            SELECT ID,ptname,HR_PHONE,HR_POSITION_NAME,HR_DEPARTMENT_SUB_SUB_NAME,HR_PERSON_TYPE_NAME,
            COUNT(DISTINCT SHIFT_DATE) AS sumday,
            SUM(CASE WHEN (SHIFT_ID <>"" OR SHIFT_ID IS NOT NULL) THEN 1 ELSE 0 END) AS sumshift
            FROM (SELECT p.ID,CONCAT(pn.HR_PREFIX_NAME,p.HR_FNAME,SPACE(1),p.HR_LNAME) AS ptname,
            p.HR_PHONE,pp.HR_POSITION_NAME,hd.HR_DEPARTMENT_SUB_SUB_NAME,pt.HR_PERSON_TYPE_NAME,
            cs.SHIFT_DATE,cs.SHIFT_ID 
            FROM hrd_person p
            LEFT JOIN hrd_prefix pn ON pn.HR_PREFIX_ID=p.HR_PREFIX_ID
            LEFT JOIN hrd_position pp ON pp.HR_POSITION_ID=p.HR_POSITION_ID
            LEFT JOIN hrd_person_type pt ON pt.HR_PERSON_TYPE_ID=p.HR_PERSON_TYPE_ID 
            LEFT JOIN hrd_department_sub_sub hd ON hd.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
            INNER JOIN checkin_shift_summary cs ON cs.USER_ID=p.ID
            WHERE cs.SHIFT_DATE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND p.HR_PERSON_TYPE_ID = "3") AS a
            GROUP BY ID ORDER BY HR_PERSON_TYPE_NAME,HR_DEPARTMENT_SUB_SUB_NAME,ptname');  

      $checkin_type4=DB::connection('backoffice')->select('
            SELECT ID,ptname,HR_PHONE,HR_POSITION_NAME,HR_DEPARTMENT_SUB_SUB_NAME,HR_PERSON_TYPE_NAME,
            COUNT(DISTINCT SHIFT_DATE) AS sumday,
            SUM(CASE WHEN (SHIFT_ID <>"" OR SHIFT_ID IS NOT NULL) THEN 1 ELSE 0 END) AS sumshift
            FROM (SELECT p.ID,CONCAT(pn.HR_PREFIX_NAME,p.HR_FNAME,SPACE(1),p.HR_LNAME) AS ptname,
            p.HR_PHONE,pp.HR_POSITION_NAME,hd.HR_DEPARTMENT_SUB_SUB_NAME,pt.HR_PERSON_TYPE_NAME,
            cs.SHIFT_DATE,cs.SHIFT_ID 
            FROM hrd_person p
            LEFT JOIN hrd_prefix pn ON pn.HR_PREFIX_ID=p.HR_PREFIX_ID
            LEFT JOIN hrd_position pp ON pp.HR_POSITION_ID=p.HR_POSITION_ID
            LEFT JOIN hrd_person_type pt ON pt.HR_PERSON_TYPE_ID=p.HR_PERSON_TYPE_ID 
            LEFT JOIN hrd_department_sub_sub hd ON hd.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
            INNER JOIN checkin_shift_summary cs ON cs.USER_ID=p.ID
            WHERE cs.SHIFT_DATE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND p.HR_PERSON_TYPE_ID = "4") AS a
            GROUP BY ID ORDER BY HR_PERSON_TYPE_NAME,HR_DEPARTMENT_SUB_SUB_NAME,ptname');   

      $checkin_type5=DB::connection('backoffice')->select('
            SELECT ID,ptname,HR_PHONE,HR_POSITION_NAME,HR_DEPARTMENT_SUB_SUB_NAME,HR_PERSON_TYPE_NAME,
            COUNT(DISTINCT SHIFT_DATE) AS sumday,
            SUM(CASE WHEN (SHIFT_ID <>"" OR SHIFT_ID IS NOT NULL) THEN 1 ELSE 0 END) AS sumshift
            FROM (SELECT p.ID,CONCAT(pn.HR_PREFIX_NAME,p.HR_FNAME,SPACE(1),p.HR_LNAME) AS ptname,
            p.HR_PHONE,pp.HR_POSITION_NAME,hd.HR_DEPARTMENT_SUB_SUB_NAME,pt.HR_PERSON_TYPE_NAME,
            cs.SHIFT_DATE,cs.SHIFT_ID 
            FROM hrd_person p
            LEFT JOIN hrd_prefix pn ON pn.HR_PREFIX_ID=p.HR_PREFIX_ID
            LEFT JOIN hrd_position pp ON pp.HR_POSITION_ID=p.HR_POSITION_ID
            LEFT JOIN hrd_person_type pt ON pt.HR_PERSON_TYPE_ID=p.HR_PERSON_TYPE_ID 
            LEFT JOIN hrd_department_sub_sub hd ON hd.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
            INNER JOIN checkin_shift_summary cs ON cs.USER_ID=p.ID
            WHERE cs.SHIFT_DATE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND p.HR_PERSON_TYPE_ID = "5") AS a
            GROUP BY ID ORDER BY HR_PERSON_TYPE_NAME,HR_DEPARTMENT_SUB_SUB_NAME,ptname');  

      $checkin_type6=DB::connection('backoffice')->select('
            SELECT ID,ptname,HR_PHONE,HR_POSITION_NAME,HR_DEPARTMENT_SUB_SUB_NAME,HR_PERSON_TYPE_NAME,
            COUNT(DISTINCT SHIFT_DATE) AS sumday,
            SUM(CASE WHEN (SHIFT_ID <>"" OR SHIFT_ID IS NOT NULL) THEN 1 ELSE 0 END) AS sumshift
            FROM (SELECT p.ID,CONCAT(pn.HR_PREFIX_NAME,p.HR_FNAME,SPACE(1),p.HR_LNAME) AS ptname,
            p.HR_PHONE,pp.HR_POSITION_NAME,hd.HR_DEPARTMENT_SUB_SUB_NAME,pt.HR_PERSON_TYPE_NAME,
            cs.SHIFT_DATE,cs.SHIFT_ID 
            FROM hrd_person p
            LEFT JOIN hrd_prefix pn ON pn.HR_PREFIX_ID=p.HR_PREFIX_ID
            LEFT JOIN hrd_position pp ON pp.HR_POSITION_ID=p.HR_POSITION_ID
            LEFT JOIN hrd_person_type pt ON pt.HR_PERSON_TYPE_ID=p.HR_PERSON_TYPE_ID 
            LEFT JOIN hrd_department_sub_sub hd ON hd.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
            INNER JOIN checkin_shift_summary cs ON cs.USER_ID=p.ID
            WHERE cs.SHIFT_DATE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND p.HR_PERSON_TYPE_ID = "6") AS a
            GROUP BY ID ORDER BY HR_PERSON_TYPE_NAME,HR_DEPARTMENT_SUB_SUB_NAME,ptname'); 

      $checkin_type7=DB::connection('backoffice')->select('
            SELECT ID,ptname,HR_PHONE,HR_POSITION_NAME,HR_DEPARTMENT_SUB_SUB_NAME,HR_PERSON_TYPE_NAME,
            COUNT(DISTINCT SHIFT_DATE) AS sumday,
            SUM(CASE WHEN (SHIFT_ID <>"" OR SHIFT_ID IS NOT NULL) THEN 1 ELSE 0 END) AS sumshift
            FROM (SELECT p.ID,CONCAT(pn.HR_PREFIX_NAME,p.HR_FNAME,SPACE(1),p.HR_LNAME) AS ptname,
            p.HR_PHONE,pp.HR_POSITION_NAME,hd.HR_DEPARTMENT_SUB_SUB_NAME,pt.HR_PERSON_TYPE_NAME,
            cs.SHIFT_DATE,cs.SHIFT_ID 
            FROM hrd_person p
            LEFT JOIN hrd_prefix pn ON pn.HR_PREFIX_ID=p.HR_PREFIX_ID
            LEFT JOIN hrd_position pp ON pp.HR_POSITION_ID=p.HR_POSITION_ID
            LEFT JOIN hrd_person_type pt ON pt.HR_PERSON_TYPE_ID=p.HR_PERSON_TYPE_ID 
            LEFT JOIN hrd_department_sub_sub hd ON hd.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
            INNER JOIN checkin_shift_summary cs ON cs.USER_ID=p.ID
            WHERE cs.SHIFT_DATE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND p.HR_PERSON_TYPE_ID = "7") AS a
            GROUP BY ID ORDER BY HR_PERSON_TYPE_NAME,HR_DEPARTMENT_SUB_SUB_NAME,ptname'); 

      $request->session()->put('start_date',$start_date);
      $request->session()->put('end_date',$end_date);
      $request->session()->save();

      return view('backoffice_hrd.checkin',compact('start_date','end_date','checkin_type1','checkin_type2','checkin_type3',
                  'checkin_type4','checkin_type5','checkin_type6','checkin_type7'));
}
// *************Dom pdf*********************//
public function checkin_indiv_pdf(Request $request,$id)
{
      $start_date = Session::get('start_date');
      $end_date = Session::get('end_date');
      $checkin_indiv=DB::connection('backoffice')->select('
            SELECT p.ID,CONCAT(pn.HR_PREFIX_NAME,p.HR_FNAME,SPACE(1),p.HR_LNAME) AS ptname,
            p.HR_PHONE,pp.HR_POSITION_NAME,hd.HR_DEPARTMENT_SUB_SUB_NAME,pt.HR_PERSON_TYPE_NAME,cs.SHIFT_DATE,
            IF(o.OPERATE_JOB_NAME IS NULL,"ไม่มีเวร",o.OPERATE_JOB_NAME) AS shift,
            TIME(cs.SCAN_START_DATETIME) AS scan_start,TIME(cs.SCAN_END_DATETIME) AS scan_end
            FROM hrd_person p
            LEFT JOIN hrd_prefix pn ON pn.HR_PREFIX_ID=p.HR_PREFIX_ID
            LEFT JOIN hrd_position pp ON pp.HR_POSITION_ID=p.HR_POSITION_ID
            LEFT JOIN hrd_person_type pt ON pt.HR_PERSON_TYPE_ID=p.HR_PERSON_TYPE_ID 
            LEFT JOIN hrd_department_sub_sub hd ON hd.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
            INNER JOIN checkin_shift_summary cs ON cs.USER_ID=p.ID
            LEFT JOIN operate_job o ON o.OPERATE_JOB_ID=cs.SHIFT_ID
            WHERE cs.SHIFT_DATE BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND p.ID= "'.$id.'" GROUP BY cs.SCAN_START_DATETIME,cs.SCAN_END_DATETIME ORDER BY cs.SHIFT_DATE,scan_start');   
      foreach($checkin_indiv as $row){
            $type_name = $row->HR_PERSON_TYPE_NAME;
            $ptname = $row->ptname;
            $phone = $row->HR_PHONE;
            $position_name = $row->HR_POSITION_NAME;
            $depart = $row->HR_DEPARTMENT_SUB_SUB_NAME ;
      }
      $pdf = PDF::loadView('backoffice_hrd.checkin_indiv_pdf', compact('type_name','ptname','phone','position_name',
            'depart','checkin_indiv','start_date','end_date'))
                  ->setPaper('A4', 'portrait');;
      return @$pdf->stream();
}
public function checkin_indiv_detail_pdf(Request $request,$id)
{
      $start_date = Session::get('start_date');
      $end_date = Session::get('end_date');
      $checkin_indiv=DB::connection('backoffice')->select('
            SELECT m.PERSON_ID,CONCAT(pn.HR_PREFIX_NAME,p.HR_FNAME,SPACE(1),p.HR_LNAME) AS ptname,
            pp.HR_POSITION_NAME,hd.HR_DEPARTMENT_SUB_SUB_NAME,cd.`name` AS device,
            DATE(c.time_attendance) AS c_date,TIME(c.time_attendance) AS c_time FROM checkin_device_time_attendance c
            LEFT JOIN checkin_device_setting cd ON cd.id=c.device_id
            LEFT JOIN map_user_hr_scan m ON m.HR_SCAN_ID=c.user_id 
            LEFT JOIN hrd_person p ON p.ID=m.PERSON_ID
            LEFT JOIN hrd_prefix pn ON pn.HR_PREFIX_ID=p.HR_PREFIX_ID
            LEFT JOIN hrd_position pp ON pp.HR_POSITION_ID=p.HR_POSITION_ID
            LEFT JOIN hrd_department_sub_sub hd ON hd.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
            WHERE DATE(c.time_attendance) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            AND m.PERSON_ID = "'.$id.'" ORDER BY c.time_attendance');   
      foreach($checkin_indiv as $row){
            $ptname = $row->ptname;           
            $position_name = $row->HR_POSITION_NAME;
            $depart = $row->HR_DEPARTMENT_SUB_SUB_NAME ;
      }
      $pdf = PDF::loadView('backoffice_hrd.checkin_indiv_detail_pdf', compact('ptname','position_name',
            'depart','checkin_indiv','start_date','end_date'))
                  ->setPaper('A4', 'portrait');;
      return @$pdf->stream();
}

//Create nurse_productivity_er
public function nurse_productivity_er(Request $request)
{
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      if($start_date == '' || $end_date == null)
      {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
      if($end_date == '' || $end_date == null)
      {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
      
      $nurse_productivity_er=Nurse_productivity_er::whereBetween('report_date',[$start_date, $end_date])->get(); 
      $sum_productivity_er=DB::select('
            SELECT CASE WHEN shift_time = "เวรเช้า" THEN "1" WHEN shift_time = "เวรบ่าย" THEN "2"
            WHEN shift_time = "เวรดึก" THEN "3" END AS "id",shift_time,COUNT(shift_time) AS shift_time_sum,
            SUM(patient_all) AS patient_all, SUM(emergent) AS emergent,SUM(urgent) AS urgent,SUM(acute_illness) AS acute_illness,
            SUM(non_acute_illness) AS non_acute_illness,SUM(patient_hr) AS patient_hr,SUM(nurse_oncall) AS nurse_oncall,
            SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime, SUM(nurse_hr) AS nurse_hr,
            ((SUM(patient_hr)*100)/SUM(nurse_hr)) AS productivity,(SUM(patient_hr)/SUM(patient_all)) AS hhpuos,
            (SUM(patient_all)*(SUM(patient_hr)/SUM(patient_all))*(1.4/7))/COUNT(shift_time) AS nurse_shift_time
            FROM nurse_productivity_ers
            WHERE report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY shift_time ORDER BY id');
      $day_er_night=DB::select('
            SELECT report_date,ROUND(((SUM(patient_hr)*100)/SUM(nurse_hr)),2) AS productivity
            FROM nurse_productivity_ers
            WHERE report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND shift_time = "เวรดึก"
            GROUP BY report_date ORDER BY report_date ');
      $report_date = array_column($day_er_night,'report_date');
      $night = array_column($day_er_night,'productivity');
      $day_er_morning=DB::select('
            SELECT report_date,ROUND(((SUM(patient_hr)*100)/SUM(nurse_hr)),2) AS productivity
            FROM nurse_productivity_ers
            WHERE report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND shift_time = "เวรเช้า"
            GROUP BY report_date ORDER BY report_date ');
      $morning = array_column($day_er_morning,'productivity');
      $day_er_afternoon=DB::select('
            SELECT report_date,ROUND(((SUM(patient_hr)*100)/SUM(nurse_hr)),2) AS productivity
            FROM nurse_productivity_ers
            WHERE report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND shift_time = "เวรบ่าย"
            GROUP BY report_date ORDER BY report_date ');
      $afternoon = array_column($day_er_afternoon,'productivity');

      $username=Auth::user()->username;
      $del_product=DB::table('users_access')->where('username',$username)->where('del_product','Y')->value("username"); 
      
      return view('backoffice_hrd.nurse_productivity_er',compact('sum_productivity_er','nurse_productivity_er','start_date',
            'end_date','del_product','report_date','night','morning','afternoon'));        
}

//Create nurse_productivity_er_delete
public function nurse_productivity_er_delete($id)
{
      $nurse_productivity_er=Nurse_productivity_er::find($id)->delete();
      return redirect()->route('nurse_productivity_er')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
}

//Create nurse_productivity_er_night
public function nurse_productivity_er_night()
{
      $shift_night = DB::connection('hosxp')->select('
            SELECT count(DISTINCT vn) as visit,                 
            SUM(CASE WHEN er_emergency_type IN ("1","2") THEN 1 ELSE 0 END) AS "Emergent",
            SUM(CASE WHEN er_emergency_type ="3" THEN 1 ELSE 0 END) AS "Urgent",
            SUM(CASE WHEN er_emergency_type ="4" THEN 1 ELSE 0 END) AS "Acute_illness",
            SUM(CASE WHEN er_emergency_type ="5" THEN 1 ELSE 0 END) AS "Non_acute_illness"           
            FROM er_regist  WHERE DATE(enter_er_time) = DATE(NOW()) 
            AND TIME(enter_er_time) BETWEEN "00:00:00" AND "07:59:59"'); 

      return view('backoffice_hrd.nurse_productivity_er_night',compact('shift_night'));            
}

//nurse_productivity_er_night_save
public function nurse_productivity_er_night_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $productivity_er = new Nurse_productivity_er;
      $productivity_er->report_date = $request->report_date;   
      $productivity_er->shift_time = $request->shift_time;
      $productivity_er->patient_all = $request->patient_all;
      $productivity_er->emergent = $request->emergent;
      $productivity_er->urgent = $request->urgent;
      $productivity_er->acute_illness = $request->acute_illness;
      $productivity_er->non_acute_illness = $request->non_acute_illness;
      $productivity_er->patient_hr = (($request->emergent*3.2)+($request->urgent*2.7)
            +($request->acute_illness*1.4)+($request->non_acute_illness*0.5));
      $productivity_er->nurse_oncall = $request->nurse_oncall;
      $productivity_er->nurse_partime = $request->nurse_partime;
      $productivity_er->nurse_fulltime = $request->nurse_fulltime;
      $productivity_er->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
      $productivity_er->productivity = ((($request->emergent*3.2)+($request->urgent*2.7)
            +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
      $productivity_er->hhpuos = (($request->emergent*3.2)+($request->urgent*2.7)
            +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))/$request->patient_all;
      $productivity_er->nurse_shift_time = $request->patient_all*( (($request->emergent*3.2)+($request->urgent*2.7)
      +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))/$request->patient_all)*(1.4/7);
      $productivity_er->recorder = $request->recorder;
      $productivity_er->note = $request->note;
      $productivity_er->save();

//เปิดแจ้งเตือน Telegram
      $message = "งานอุบัติเหตุ-ฉุกเฉิน" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 00.00-08.00 น.(เวรดึก)"."\n"
            ."ผู้ป่วยในเวร " .$productivity_er->patient_all ." ราย" ."\n"       
            ." -Emergent " .$productivity_er->emergent ." ราย" ."\n"
            ." -Urgent " .$productivity_er->urgent ." ราย" ."\n"
            ." -Acute illness " .$productivity_er->acute_illness ." ราย" ."\n" 
            ." -Non Acute illness " .$productivity_er->non_acute_illness ." ราย" ."\n"
            ."อัตรากำลัง Oncall : " .$productivity_er->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_er->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_er->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_er->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_er->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_er->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_er->recorder ."\n"; 

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4747192701","-4716437484","-4729376994"]; //งานอุบัติเหตุฉุกเฉิน-รพ.หัวตะพาน,กลุ่มการพยาบาล-รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_er_night')->with('success', 'ส่งข้อมูลเวรดึกเรียบร้อยแล้ว');
}

//Create nurse_productivity_er_morning
public function nurse_productivity_er_morning()
{
      $shift_morning = DB::connection('hosxp')->select('
            SELECT DATE(NOW()) AS vstdate,count(DISTINCT vn) as visit,                 
            SUM(CASE WHEN er_emergency_type IN ("1","2") THEN 1 ELSE 0 END) AS "Emergent",
            SUM(CASE WHEN er_emergency_type ="3" THEN 1 ELSE 0 END) AS "Urgent",
            SUM(CASE WHEN er_emergency_type ="4" THEN 1 ELSE 0 END) AS "Acute_illness",
            SUM(CASE WHEN er_emergency_type ="5" THEN 1 ELSE 0 END) AS "Non_acute_illness"
            FROM er_regist  WHERE DATE(enter_er_time) = DATE(NOW()) 
            AND TIME(enter_er_time) BETWEEN "08:00:00" AND "15:59:59"'); 

      return view('backoffice_hrd.nurse_productivity_er_morning',compact('shift_morning'));            
}

//nurse_productivity_er_morning_save
public function nurse_productivity_er_morning_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',   
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $productivity_er = new Nurse_productivity_er;   
      $productivity_er->report_date = $request->report_date;   
      $productivity_er->shift_time = $request->shift_time;
      $productivity_er->patient_all = $request->patient_all;
      $productivity_er->emergent = $request->emergent;
      $productivity_er->urgent = $request->urgent;
      $productivity_er->acute_illness = $request->acute_illness;
      $productivity_er->non_acute_illness = $request->non_acute_illness;
      $productivity_er->patient_hr = (($request->emergent*3.2)+($request->urgent*2.7)
            +($request->acute_illness*1.4)+($request->non_acute_illness*0.5));
      $productivity_er->nurse_oncall = $request->nurse_oncall;
      $productivity_er->nurse_partime = $request->nurse_partime;
      $productivity_er->nurse_fulltime = $request->nurse_fulltime;
      $productivity_er->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
      $productivity_er->productivity = ((($request->emergent*3.2)+($request->urgent*2.7)
            +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
      $productivity_er->hhpuos = (($request->emergent*3.2)+($request->urgent*2.7)
            +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))/$request->patient_all;
      $productivity_er->nurse_shift_time = $request->patient_all*( (($request->emergent*3.2)+($request->urgent*2.7)
      +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))/$request->patient_all)*(1.4/7);
      $productivity_er->recorder = $request->recorder;
      $productivity_er->note = $request->note;
      $productivity_er->save();    

//เปิดแจ้งเตือน Telegram
      $message = "งานอุบัติเหตุ-ฉุกเฉิน" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 08.00-16.00 น.(เวรเช้า)"."\n"
            ."ผู้ป่วยในเวร " .$productivity_er->patient_all ." ราย" ."\n"       
            ." -Emergent " .$productivity_er->emergent ." ราย" ."\n"
            ." -Urgent " .$productivity_er->urgent ." ราย" ."\n"
            ." -Acute illness " .$productivity_er->acute_illness ." ราย" ."\n" 
            ." -Non Acute illness " .$productivity_er->non_acute_illness ." ราย" ."\n"
            ."อัตรากำลัง Oncall : " .$productivity_er->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_er->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_er->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_er->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_er->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_er->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_er->recorder ."\n"; 

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4747192701","-4716437484","-4729376994"]; //งานอุบัติเหตุฉุกเฉิน-รพ.หัวตะพาน,กลุ่มการพยาบาล-รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_er_morning')->with('success', 'ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
}

//Create nurse_productivity_er_afternoon
public function nurse_productivity_er_afternoon()
{
      $shift_afternoon = DB::connection('hosxp')->select('
            SELECT date(DATE_ADD(now(), INTERVAL -1 DAY )) AS vstdate,count(DISTINCT vn) as visit,                 
            SUM(CASE WHEN er_emergency_type IN ("1","2") THEN 1 ELSE 0 END) AS "Emergent",
            SUM(CASE WHEN er_emergency_type ="3" THEN 1 ELSE 0 END) AS "Urgent",
            SUM(CASE WHEN er_emergency_type ="4" THEN 1 ELSE 0 END) AS "Acute_illness",
            SUM(CASE WHEN er_emergency_type ="5" THEN 1 ELSE 0 END) AS "Non_acute_illness"
            FROM er_regist  WHERE DATE(enter_er_time) = date(DATE_ADD(now(), INTERVAL -1 DAY )) 
            AND TIME(enter_er_time) BETWEEN "16:00:00" AND "23:59:59"'); 

      return view('backoffice_hrd.nurse_productivity_er_afternoon',compact('shift_afternoon'));            
}

//nurse_productivity_er_afternoon_save
public function nurse_productivity_er_afternoon_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $productivity_er = new Nurse_productivity_er; 
      $productivity_er->report_date = $request->report_date;  
      $productivity_er->shift_time = $request->shift_time;
      $productivity_er->patient_all = $request->patient_all;
      $productivity_er->emergent = $request->emergent;
      $productivity_er->urgent = $request->urgent;
      $productivity_er->acute_illness = $request->acute_illness;
      $productivity_er->non_acute_illness = $request->non_acute_illness;
      $productivity_er->patient_hr = (($request->emergent*3.2)+($request->urgent*2.7)
            +($request->acute_illness*1.4)+($request->non_acute_illness*0.5));
      $productivity_er->nurse_oncall = $request->nurse_oncall;
      $productivity_er->nurse_partime = $request->nurse_partime;
      $productivity_er->nurse_fulltime = $request->nurse_fulltime;
      $productivity_er->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
      $productivity_er->productivity = ((($request->emergent*3.2)+($request->urgent*2.7)
            +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
      $productivity_er->hhpuos = (($request->emergent*3.2)+($request->urgent*2.7)
            +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))/$request->patient_all;
      $productivity_er->nurse_shift_time = $request->patient_all*( (($request->emergent*3.2)+($request->urgent*2.7)
      +($request->acute_illness*1.4)+($request->non_acute_illness*0.5))/$request->patient_all)*(1.4/7);
      $productivity_er->recorder = $request->recorder;
      $productivity_er->note = $request->note;
      $productivity_er->save();

//เปิดแจ้งเตือน Telegram
      $message = "งานอุบัติเหตุ-ฉุกเฉิน" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 16.00-00.00 น.(เวรบ่าย)"."\n"
            ."ผู้ป่วยในเวร " .$productivity_er->patient_all ." ราย" ."\n"       
            ." -Emergent " .$productivity_er->emergent ." ราย" ."\n"
            ." -Urgent " .$productivity_er->urgent ." ราย" ."\n"
            ." -Acute illness " .$productivity_er->acute_illness ." ราย" ."\n" 
            ." -Non Acute illness " .$productivity_er->non_acute_illness ." ราย" ."\n"
            ."อัตรากำลัง Oncall : " .$productivity_er->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_er->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_er->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_er->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_er->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_er->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_er->recorder ."\n"; 

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4747192701","-4716437484","-4729376994"]; //งานอุบัติเหตุฉุกเฉิน-รพ.หัวตะพาน,กลุ่มการพยาบาล-รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_er_afternoon')->with('success', 'ส่งข้อมูลเวรบ่ายเรียบร้อยแล้ว');
}

//Create nurse_productivity_ipd

public function nurse_productivity_ipd(Request $request)
{
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      if($start_date == '' || $end_date == null)
      {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
      if($end_date == '' || $end_date == null)
      {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
      
      $nurse_productivity_ipd=Nurse_productivity_ipd::whereBetween('report_date',[$start_date,$end_date])->get(); 
      $sum_productivity_ipd=DB::select('
            SELECT CASE WHEN shift_time = "เวรเช้า" THEN "1" WHEN shift_time = "เวรบ่าย" THEN "2"
            WHEN shift_time = "เวรดึก" THEN "3" END AS "id",shift_time,COUNT(shift_time) AS shift_time_sum,
            SUM(patient_all) AS patient_all,SUM(convalescent) AS convalescent,SUM(moderate_ill) AS moderate_ill,
            SUM(semi_critical_ill) AS semi_critical_ill,SUM(critical_ill) AS critical_ill,SUM(patient_hr) AS patient_hr,
            SUM(nurse_oncall) AS nurse_oncall,SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime,
            SUM(nurse_hr) AS nurse_hr,((SUM(patient_hr)*100)/SUM(nurse_hr)) AS productivity,(SUM(patient_hr)/SUM(patient_all)) AS hhpuos,
            (SUM(patient_all)*(SUM(patient_hr)/SUM(patient_all))*(1.4/7))/COUNT(shift_time) AS nurse_shift_time
            FROM nurse_productivity_ipds
            WHERE report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY shift_time ORDER BY id');

      $username=Auth::user()->username;
      $del_product=DB::table('users_access')->where('username',$username)->where('del_product','Y')->value("username"); 

      return view('backoffice_hrd.nurse_productivity_ipd',compact('sum_productivity_ipd','nurse_productivity_ipd','start_date','end_date','del_product'));        
}

//Create nurse_productivity_ipd_delete
public function nurse_productivity_ipd_delete($id)
{
      $nurse_productivity_ipd=Nurse_productivity_ipd::find($id)->delete();
      return redirect()->route('nurse_productivity_ipd')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
}

//Create nurse_productivity_ipd_night
public function nurse_productivity_ipd_night()
{
      $shift_night = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT i.an) AS patient_all,
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "1%" THEN 1 ELSE 0 END) AS "convalescent",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "2%" THEN 1 ELSE 0 END) AS "moderate_ill",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "3%" THEN 1 ELSE 0 END) AS "semi_critical_ill",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "4%" THEN 1 ELSE 0 END) AS "critical_ill",
            SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code ="")
            THEN 1 ELSE 0 END) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("01") AND i.confirm_discharge = "N" '); 

      return view('backoffice_hrd.nurse_productivity_ipd_night',compact('shift_night'));            
}

//nurse_productivity_ipd_night_save
public function nurse_productivity_ipd_night_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $productivity_ipd = new Nurse_productivity_ipd;   
      $productivity_ipd->report_date = $request->report_date;  
      $productivity_ipd->shift_time = $request->shift_time;
      $productivity_ipd->patient_all = $request->patient_all;
      $productivity_ipd->convalescent = $request->convalescent;
      $productivity_ipd->moderate_ill = $request->moderate_ill;
      $productivity_ipd->semi_critical_ill = $request->semi_critical_ill;
      $productivity_ipd->critical_ill = $request->critical_ill;  
      $productivity_ipd->patient_hr = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99));
      $productivity_ipd->nurse_oncall = $request->nurse_oncall;
      $productivity_ipd->nurse_partime = $request->nurse_partime;
      $productivity_ipd->nurse_fulltime = $request->nurse_fulltime;
      $productivity_ipd->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
      $productivity_ipd->productivity = ((($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
      $productivity_ipd->hhpuos = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))/$request->patient_all;
      $productivity_ipd->nurse_shift_time = $request->patient_all*( (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))/$request->patient_all)*(1.4/7);
      $productivity_ipd->recorder = $request->recorder;
      $productivity_ipd->note = $request->note;
      $productivity_ipd->save();

//เปิดแจ้งเตือน Telegram
      $message = "Productivity งานผู้ป่วยในสามัญ" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 00.00-08.00 น.(เวรดึก)" ."\n" 
            ."ผู้ป่วยในเวร " .$productivity_ipd->patient_all ." ราย" ."\n"       
            ." -Convalescent " .$productivity_ipd->convalescent ." ราย" ."\n"
            ." -Moderate ill " .$productivity_ipd->moderate_ill . " ราย" ."\n"
            ." -Semi critical ill " .$productivity_ipd->semi_critical_ill . " ราย" ."\n" 
            ." -Critical ill " .$productivity_ipd->critical_ill . " ราย" ."\n"
            ."อัตรากำลัง Oncall : " .$productivity_ipd->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_ipd->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_ipd->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_ipd->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_ipd->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_ipd->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_ipd->recorder ."\n";                

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4620837416","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,งานผู้ป่วยในสามัญ -รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_ipd_night')->with('success', 'ส่งข้อมูลเวรดึกเรียบร้อยแล้ว');
}

//Create nurse_productivity_ipd_morning
public function nurse_productivity_ipd_morning()
{
      $shift_night = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT i.an) AS patient_all,
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "1%" THEN 1 ELSE 0 END) AS "convalescent",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "2%" THEN 1 ELSE 0 END) AS "moderate_ill",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "3%" THEN 1 ELSE 0 END) AS "semi_critical_ill",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "4%" THEN 1 ELSE 0 END) AS "critical_ill",
            SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code ="")
            THEN 1 ELSE 0 END) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("01") AND i.confirm_discharge = "N" '); 

      return view('backoffice_hrd.nurse_productivity_ipd_morning',compact('shift_night'));            
}

//nurse_productivity_ipd_morning_save
public function nurse_productivity_ipd_morning_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $productivity_ipd = new Nurse_productivity_ipd;  
      $productivity_ipd->report_date = $request->report_date;   
      $productivity_ipd->shift_time = $request->shift_time;
      $productivity_ipd->patient_all = $request->patient_all;
      $productivity_ipd->convalescent = $request->convalescent;
      $productivity_ipd->moderate_ill = $request->moderate_ill;
      $productivity_ipd->semi_critical_ill = $request->semi_critical_ill;
      $productivity_ipd->critical_ill = $request->critical_ill;  
      $productivity_ipd->patient_hr = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99));
      $productivity_ipd->nurse_oncall = $request->nurse_oncall;
      $productivity_ipd->nurse_partime = $request->nurse_partime;
      $productivity_ipd->nurse_fulltime = $request->nurse_fulltime;
      $productivity_ipd->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
      $productivity_ipd->productivity = ((($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
      $productivity_ipd->hhpuos = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))/$request->patient_all;
      $productivity_ipd->nurse_shift_time = $request->patient_all*( (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))/$request->patient_all)*(1.4/7);
      $productivity_ipd->recorder = $request->recorder;
      $productivity_ipd->note = $request->note;
      $productivity_ipd->save();

//เปิดแจ้งเตือน Telegram
      $message = "Productivity งานผู้ป่วยในสามัญ" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 08.00-16.00 น.(เวรเช้า)" ."\n" 
            ."ผู้ป่วยในเวร " .$productivity_ipd->patient_all ." ราย" ."\n"       
            ." -Convalescent " .$productivity_ipd->convalescent ." ราย" ."\n"
            ." -Moderate ill " .$productivity_ipd->moderate_ill . " ราย" ."\n"
            ." -Semi critical ill " .$productivity_ipd->semi_critical_ill . " ราย" ."\n" 
            ." -Critical ill " .$productivity_ipd->critical_ill . " ราย" ."\n"
            ."อัตรากำลัง Oncall : " .$productivity_ipd->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_ipd->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_ipd->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_ipd->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_ipd->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_ipd->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_ipd->recorder ."\n";                

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4620837416","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,งานผู้ป่วยในสามัญ -รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_ipd_morning')->with('success', 'ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
}

//Create nurse_productivity_ipd_afternoon
public function nurse_productivity_ipd_afternoon()
{
      $shift_night = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT i.an) AS patient_all,
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "1%" THEN 1 ELSE 0 END) AS "convalescent",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "2%" THEN 1 ELSE 0 END) AS "moderate_ill",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "3%" THEN 1 ELSE 0 END) AS "semi_critical_ill",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "4%" THEN 1 ELSE 0 END) AS "critical_ill",
            SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code ="")
            THEN 1 ELSE 0 END) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("01") AND i.confirm_discharge = "N" '); 

      return view('backoffice_hrd.nurse_productivity_ipd_afternoon',compact('shift_night'));            
}

//nurse_productivity_ipd_afternoon_save
public function nurse_productivity_ipd_afternoon_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $productivity_ipd = new Nurse_productivity_ipd; 
      $productivity_ipd->report_date = $request->report_date;     
      $productivity_ipd->shift_time = $request->shift_time;
      $productivity_ipd->patient_all = $request->patient_all;
      $productivity_ipd->convalescent = $request->convalescent;
      $productivity_ipd->moderate_ill = $request->moderate_ill;
      $productivity_ipd->semi_critical_ill = $request->semi_critical_ill;
      $productivity_ipd->critical_ill = $request->critical_ill;  
      $productivity_ipd->patient_hr = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99));
      $productivity_ipd->nurse_oncall = $request->nurse_oncall;
      $productivity_ipd->nurse_partime = $request->nurse_partime;
      $productivity_ipd->nurse_fulltime = $request->nurse_fulltime;
      $productivity_ipd->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
      $productivity_ipd->productivity = ((($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
      $productivity_ipd->hhpuos = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))/$request->patient_all;
      $productivity_ipd->nurse_shift_time = $request->patient_all*( (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))/$request->patient_all)*(1.4/7);
      $productivity_ipd->recorder = $request->recorder;
      $productivity_ipd->note = $request->note;
      $productivity_ipd->save();

//เปิดแจ้งเตือน Telegram
      $message = "Productivity งานผู้ป่วยในสามัญ" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 16.00-00.00 น.(เวรบ่าย)" ."\n" 
            ."ผู้ป่วยในเวร " .$productivity_ipd->patient_all ." ราย" ."\n"       
            ." -Convalescent " .$productivity_ipd->convalescent ." ราย" ."\n"
            ." -Moderate ill " .$productivity_ipd->moderate_ill . " ราย" ."\n"
            ." -Semi critical ill " .$productivity_ipd->semi_critical_ill . " ราย" ."\n" 
            ." -Critical ill " .$productivity_ipd->critical_ill . " ราย" ."\n"
            ."อัตรากำลัง Oncall : " .$productivity_ipd->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_ipd->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_ipd->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_ipd->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_ipd->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_ipd->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_ipd->recorder ."\n";                

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4620837416","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,งานผู้ป่วยในสามัญ -รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_ipd_afternoon')->with('success', 'ส่งข้อมูลเวรบ่ายเรียบร้อยแล้ว');
}

//Create nurse_productivity_vip

public function nurse_productivity_vip(Request $request)
{
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      if($start_date == '' || $end_date == null)
      {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
      if($end_date == '' || $end_date == null)
      {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
      
      $nurse_productivity_vip=Nurse_productivity_vip::whereBetween('report_date',[$start_date, $end_date])->get(); 
      $sum_productivity_vip=DB::select('
            SELECT CASE WHEN shift_time = "เวรเช้า" THEN "1" WHEN shift_time = "เวรบ่าย" THEN "2"
            WHEN shift_time = "เวรดึก" THEN "3" END AS "id",shift_time,COUNT(shift_time) AS shift_time_sum,
            SUM(patient_all) AS patient_all,SUM(convalescent) AS convalescent,SUM(moderate_ill) AS moderate_ill,
            SUM(semi_critical_ill) AS semi_critical_ill,SUM(critical_ill) AS critical_ill,SUM(patient_hr) AS patient_hr,
            SUM(nurse_oncall) AS nurse_oncall,SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime,
            SUM(nurse_hr) AS nurse_hr,((SUM(patient_hr)*100)/SUM(nurse_hr)) AS productivity,(SUM(patient_hr)/SUM(patient_all)) AS hhpuos,
            (SUM(patient_all)*(SUM(patient_hr)/SUM(patient_all))*(1.4/7))/COUNT(shift_time) AS nurse_shift_time
            FROM nurse_productivity_vips
            WHERE report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY shift_time ORDER BY id');

      $username=Auth::user()->username;
      $del_product=DB::table('users_access')->where('username',$username)->where('del_product','Y')->value("username"); 

      return view('backoffice_hrd.nurse_productivity_vip',compact('sum_productivity_vip','nurse_productivity_vip','start_date','end_date','del_product'));        
}

//Create nurse_productivity_vip_delete
public function nurse_productivity_vip_delete($id)
{
      $nurse_productivity_vip=Nurse_productivity_vip::find($id)->delete();
      return redirect()->route('nurse_productivity_vip')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
}

//Create nurse_productivity_vip_night
public function nurse_productivity_vip_night()
{
      $shift_night = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT i.an) AS patient_all,
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "1%" THEN 1 ELSE 0 END) AS "convalescent",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "2%" THEN 1 ELSE 0 END) AS "moderate_ill",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "3%" THEN 1 ELSE 0 END) AS "semi_critical_ill",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "4%" THEN 1 ELSE 0 END) AS "critical_ill",
            SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code ="")
            THEN 1 ELSE 0 END) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("03","08") AND i.confirm_discharge = "N" '); 

      return view('backoffice_hrd.nurse_productivity_vip_night',compact('shift_night'));            
}

//nurse_productivity_vip_night_save
public function nurse_productivity_vip_night_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $productivity_vip = new Nurse_productivity_vip;   
      $productivity_vip->report_date = $request->report_date;    
      $productivity_vip->shift_time = $request->shift_time;
      $productivity_vip->patient_all = $request->patient_all;
      $productivity_vip->convalescent = $request->convalescent;
      $productivity_vip->moderate_ill = $request->moderate_ill;
      $productivity_vip->semi_critical_ill = $request->semi_critical_ill;
      $productivity_vip->critical_ill = $request->critical_ill;  
      $productivity_vip->patient_hr = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99));
      $productivity_vip->nurse_oncall = $request->nurse_oncall;
      $productivity_vip->nurse_partime = $request->nurse_partime;
      $productivity_vip->nurse_fulltime = $request->nurse_fulltime;
      $productivity_vip->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
      $productivity_vip->productivity = ((($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
      $productivity_vip->hhpuos = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))/$request->patient_all;
      $productivity_vip->nurse_shift_time = $request->patient_all*( (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))/$request->patient_all)*(1.4/7);
      $productivity_vip->recorder = $request->recorder;
      $productivity_vip->note = $request->note;
      $productivity_vip->save();

//เปิดแจ้งเตือน Telegram
      $message = "Productivity งานผู้ป่วยใน VIP" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 00.00-08.00 น.(เวรดึก)" ."\n" 
            ."ผู้ป่วยในเวร " .$productivity_vip->patient_all ." ราย" ."\n"       
            ." -Convalescent " .$productivity_vip->convalescent ." ราย" ."\n"
            ." -Moderate ill " .$productivity_vip->moderate_ill . " ราย" ."\n"
            ." -Semi critical ill " .$productivity_vip->semi_critical_ill . " ราย" ."\n" 
            ." -Critical ill " .$productivity_vip->critical_ill . " ราย" ."\n"
            ."อัตรากำลัง Oncall : " .$productivity_vip->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_vip->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_vip->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_vip->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_vip->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_vip->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_vip->recorder ."\n";                

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4642404406","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,งานผู้ป่วยใน VIP-รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_vip_night')->with('success', 'ส่งข้อมูลเวรดึกเรียบร้อยแล้ว');
}

//Create nurse_productivity_vip_morning
public function nurse_productivity_vip_morning()
{
      $shift_night = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT i.an) AS patient_all,
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "1%" THEN 1 ELSE 0 END) AS "convalescent",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "2%" THEN 1 ELSE 0 END) AS "moderate_ill",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "3%" THEN 1 ELSE 0 END) AS "semi_critical_ill",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "4%" THEN 1 ELSE 0 END) AS "critical_ill",
            SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code ="")
            THEN 1 ELSE 0 END) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("03","08") AND i.confirm_discharge = "N" '); 

      return view('backoffice_hrd.nurse_productivity_vip_morning',compact('shift_night'));            
}

//nurse_productivity_vip_morning_save
public function nurse_productivity_vip_morning_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $productivity_vip = new Nurse_productivity_vip;
      $productivity_vip->report_date = $request->report_date;       
      $productivity_vip->shift_time = $request->shift_time;
      $productivity_vip->patient_all = $request->patient_all;
      $productivity_vip->convalescent = $request->convalescent;
      $productivity_vip->moderate_ill = $request->moderate_ill;
      $productivity_vip->semi_critical_ill = $request->semi_critical_ill;
      $productivity_vip->critical_ill = $request->critical_ill;  
      $productivity_vip->patient_hr = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99));
      $productivity_vip->nurse_oncall = $request->nurse_oncall;
      $productivity_vip->nurse_partime = $request->nurse_partime;
      $productivity_vip->nurse_fulltime = $request->nurse_fulltime;
      $productivity_vip->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
      $productivity_vip->productivity = ((($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
      $productivity_vip->hhpuos = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))/$request->patient_all;
      $productivity_vip->nurse_shift_time = $request->patient_all*( (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))/$request->patient_all)*(1.4/7);
      $productivity_vip->recorder = $request->recorder;
      $productivity_vip->note = $request->note;
      $productivity_vip->save();

//เปิดแจ้งเตือน Telegram
      $message = "Productivity งานผู้ป่วยใน VIP" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 08.00-16.00 น.(เวรเช้า)" ."\n" 
            ."ผู้ป่วยในเวร " .$productivity_vip->patient_all ." ราย" ."\n"       
            ." -Convalescent " .$productivity_vip->convalescent ." ราย" ."\n"
            ." -Moderate ill " .$productivity_vip->moderate_ill . " ราย" ."\n"
            ." -Semi critical ill " .$productivity_vip->semi_critical_ill . " ราย" ."\n" 
            ." -Critical ill " .$productivity_vip->critical_ill . " ราย" ."\n"
            ."อัตรากำลัง Oncall : " .$productivity_vip->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_vip->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_vip->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_vip->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_vip->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_vip->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_vip->recorder ."\n";                

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4642404406","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,งานผู้ป่วยใน VIP-รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_vip_morning')->with('success', 'ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
}

//Create nurse_productivity_vip_afternoon
public function nurse_productivity_vip_afternoon()
{
      $shift_night = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT i.an) AS patient_all,
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "1%" THEN 1 ELSE 0 END) AS "convalescent",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "2%" THEN 1 ELSE 0 END) AS "moderate_ill",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "3%" THEN 1 ELSE 0 END) AS "semi_critical_ill",
            SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "4%" THEN 1 ELSE 0 END) AS "critical_ill",
            SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code ="")
            THEN 1 ELSE 0 END) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("03","08") AND i.confirm_discharge = "N" '); 

      return view('backoffice_hrd.nurse_productivity_vip_afternoon',compact('shift_night'));            
}

//nurse_productivity_vip_afternoon_save
public function nurse_productivity_vip_afternoon_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $productivity_vip = new Nurse_productivity_vip; 
      $productivity_vip->report_date = $request->report_date;  
      $productivity_vip->shift_time = $request->shift_time;
      $productivity_vip->patient_all = $request->patient_all;
      $productivity_vip->convalescent = $request->convalescent;
      $productivity_vip->moderate_ill = $request->moderate_ill;
      $productivity_vip->semi_critical_ill = $request->semi_critical_ill;
      $productivity_vip->critical_ill = $request->critical_ill;  
      $productivity_vip->patient_hr = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99));
      $productivity_vip->nurse_oncall = $request->nurse_oncall;
      $productivity_vip->nurse_partime = $request->nurse_partime;
      $productivity_vip->nurse_fulltime = $request->nurse_fulltime;
      $productivity_vip->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
      $productivity_vip->productivity = ((($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
      $productivity_vip->hhpuos = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))/$request->patient_all;
      $productivity_vip->nurse_shift_time = $request->patient_all*( (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99))/$request->patient_all)*(1.4/7);
      $productivity_vip->recorder = $request->recorder;
      $productivity_vip->note = $request->note;
      $productivity_vip->save();

//เปิดแจ้งเตือน Telegram
      $message = "Productivity งานผู้ป่วยใน VIP" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 16.00-00.00 น.(เวรบ่าย)" ."\n" 
            ."ผู้ป่วยในเวร " .$productivity_vip->patient_all ." ราย" ."\n"       
            ." -Convalescent " .$productivity_vip->convalescent ." ราย" ."\n"
            ." -Moderate ill " .$productivity_vip->moderate_ill . " ราย" ."\n"
            ." -Semi critical ill " .$productivity_vip->semi_critical_ill . " ราย" ."\n" 
            ." -Critical ill " .$productivity_vip->critical_ill . " ราย" ."\n"
            ."อัตรากำลัง Oncall : " .$productivity_vip->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_vip->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_vip->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_vip->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_vip->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_vip->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_vip->recorder ."\n";                

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4642404406","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,งานผู้ป่วยใน VIP-รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_vip_afternoon')->with('success', 'ส่งข้อมูลเวรบ่ายเรียบร้อยแล้ว');
}

//Create nurse_productivity_opd

public function nurse_productivity_opd(Request $request)
{
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      if($start_date == '' || $end_date == null)
      {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
      if($end_date == '' || $end_date == null)
      {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
      
      $nurse_productivity_opd=Nurse_productivity_opd::whereBetween('report_date',[$start_date, $end_date])->get(); 
      $sum_productivity_opd=DB::select('
            SELECT shift_time,COUNT(shift_time) AS shift_time_sum,SUM(patient_all) AS patient_all,
            SUM(opd) AS opd,SUM(ari) AS ari,SUM(patient_hr) AS patient_hr,SUM(nurse_oncall) AS nurse_oncall,
            SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime, SUM(nurse_hr) AS nurse_hr,
            ((SUM(patient_hr)*100)/SUM(nurse_hr)) AS productivity,(SUM(patient_hr)/SUM(patient_all)) AS hhpuos,
            (SUM(patient_all)*(SUM(patient_hr)/SUM(patient_all))*(1.4/9))/COUNT(shift_time) AS nurse_shift_time
            FROM nurse_productivity_opds
            WHERE report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY shift_time ORDER BY shift_time DESC');

      $username=Auth::user()->username;
      $del_product=DB::table('users_access')->where('username',$username)->where('del_product','Y')->value("username"); 

      return view('backoffice_hrd.nurse_productivity_opd',compact('sum_productivity_opd','nurse_productivity_opd','start_date','end_date','del_product'));        
}

//Create nurse_productivity_opd_delete
public function nurse_productivity_opd_delete($id)
{
      $nurse_productivity_opd=Nurse_productivity_opd::find($id)->delete();
      return redirect()->route('nurse_productivity_opd')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
}

//Create nurse_productivity_opd_morning
public function nurse_productivity_opd_morning()
{
      $visit = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT vn) as patient_all,
            sum(CASE WHEN main_dep = "002" THEN 1 ELSE 0 END) AS opd,
            sum(CASE WHEN main_dep = "032" THEN 1 ELSE 0 END) AS ari
            FROM ovst WHERE vstdate = DATE(NOW()) AND main_dep IN ("002","032")
            AND vsttime BETWEEN "00:00:00" AND "15:59:59"'); 

      return view('backoffice_hrd.nurse_productivity_opd_morning',compact('visit'));            
}

//nurse_productivity_opd_morning_save
public function nurse_productivity_opd_morning_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $productivity_opd = new Nurse_productivity_opd; 
      $productivity_opd->report_date = $request->report_date;  
      $productivity_opd->shift_time = $request->shift_time;
      $productivity_opd->patient_all = $request->patient_all;
      $productivity_opd->opd = $request->opd;
      $productivity_opd->ari = $request->ari;
      $productivity_opd->patient_hr = (($request->ari*0.5)+($request->opd*0.37));
      $productivity_opd->nurse_oncall = $request->nurse_oncall;
      $productivity_opd->nurse_partime = $request->nurse_partime;
      $productivity_opd->nurse_fulltime = $request->nurse_fulltime;
      $productivity_opd->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*9;
      $productivity_opd->productivity = ((($request->ari*0.5)+($request->opd*0.37))*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*9);
      $productivity_opd->hhpuos = (($request->ari*0.5)+($request->opd*0.37))/$request->patient_all;
      $productivity_opd->nurse_shift_time = $request->patient_all*((($request->ari*0.5)+($request->opd*0.37))/$request->patient_all)*(1.4/9);
      $productivity_opd->recorder = $request->recorder;
      $productivity_opd->note = $request->note;
      $productivity_opd->save();

//เปิดแจ้งเตือน Telegram
      $message = "Productivity งานผู้ป่วยนอก" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 08.00-16.00 น.(เวรเช้า)" ."\n" 
            ."ผู้ป่วยในเวร " .$productivity_opd->patient_all ." ราย" ."\n"       
            ." -OPD " .$productivity_opd->opd ." ราย" ."\n"
            ." -ARI " .$productivity_opd->ari. " ราย" ."\n"
            ."อัตรากำลัง Oncall : " .$productivity_opd->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_opd->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_opd->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_opd->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_opd->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_opd->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_opd->recorder ."\n";                         

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4614719699","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,งานผู้ป่วยนอก-รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_opd_morning')->with('success', 'ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
}

//Create nurse_productivity_opd_bd
public function nurse_productivity_opd_bd()
{
      $visit = DB::connection('hosxp')->select('
            SELECT COUNT(DISTINCT vn) as patient_all,
            sum(CASE WHEN main_dep = "002" THEN 1 ELSE 0 END) AS opd,
            sum(CASE WHEN main_dep = "032" THEN 1 ELSE 0 END) AS ari
            FROM ovst WHERE vstdate = DATE(NOW()) AND main_dep IN ("002","032")
            AND vsttime BETWEEN "16:00:00" AND "18:59:59"'); 

      return view('backoffice_hrd.nurse_productivity_opd_bd',compact('visit'));            
}

//nurse_productivity_opd_bd_save
public function nurse_productivity_opd_bd_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $productivity_opd = new Nurse_productivity_opd; 
      $productivity_opd->report_date = $request->report_date;  
      $productivity_opd->shift_time = $request->shift_time;
      $productivity_opd->patient_all = $request->patient_all;
      $productivity_opd->opd = $request->opd;
      $productivity_opd->ari = $request->ari;
      $productivity_opd->patient_hr = (($request->ari*0.5)+($request->opd*0.37));
      $productivity_opd->nurse_oncall = $request->nurse_oncall;
      $productivity_opd->nurse_partime = $request->nurse_partime;
      $productivity_opd->nurse_fulltime = $request->nurse_fulltime;
      $productivity_opd->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*9;
      $productivity_opd->productivity = ((($request->ari*0.5)+($request->opd*0.37))*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*9);
      $productivity_opd->hhpuos = (($request->ari*0.5)+($request->opd*0.37))/max($request->patient_all,1);
      $productivity_opd->nurse_shift_time = $request->patient_all*((($request->ari*0.5)+($request->opd*0.37))/max($request->patient_all,1))*(1.4/9);
      $productivity_opd->recorder = $request->recorder;
      $productivity_opd->note = $request->note;
      $productivity_opd->save();

//เปิดแจ้งเตือน Telegram
      $message = "Productivity งานผู้ป่วยนอก" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."ณ เวลา 16.00-19.00 น.(เวร BD)" ."\n" 
            ."ผู้ป่วยในเวร " .$productivity_opd->patient_all ." ราย" ."\n"       
            ." -OPD " .$productivity_opd->opd ." ราย" ."\n"
            ." -ARI " .$productivity_opd->ari. " ราย" ."\n"
            ."อัตรากำลัง Oncall : " .$productivity_opd->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_opd->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_opd->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_opd->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_opd->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_opd->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_opd->recorder ."\n";                         

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4614719699","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,งานผู้ป่วยนอก-รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_opd_bd')->with('success', 'ส่งข้อมูลเวร BD เรียบร้อยแล้ว');
}

//Create nurse_productivity_ncd

public function nurse_productivity_ncd(Request $request)
{
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      if($start_date == '' || $end_date == null)
      {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
      if($end_date == '' || $end_date == null)
      {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
      
      $nurse_productivity_ncd=Nurse_productivity_ncd::whereBetween('report_date',[$start_date, $end_date])->get(); 
      $sum_productivity_ncd=DB::select('
            SELECT shift_time,COUNT(shift_time) AS shift_time_sum,SUM(patient_all) AS patient_all,
            SUM(patient_hr) AS patient_hr,SUM(nurse_oncall) AS nurse_oncall,
            SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime, SUM(nurse_hr) AS nurse_hr,
            ((SUM(patient_hr)*100)/SUM(nurse_hr)) AS productivity,(SUM(patient_hr)/SUM(patient_all)) AS hhpuos,
            (SUM(patient_all)*(SUM(patient_hr)/SUM(patient_all))*(1.4/9))/COUNT(shift_time) AS nurse_shift_time
            FROM nurse_productivity_ncds
            WHERE report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY shift_time ORDER BY shift_time DESC');

      $username=Auth::user()->username;
      $del_product=DB::table('users_access')->where('username',$username)->where('del_product','Y')->value("username"); 

      return view('backoffice_hrd.nurse_productivity_ncd',compact('sum_productivity_ncd','nurse_productivity_ncd','start_date','end_date','del_product'));        
}

//Create nurse_productivity_ncd_delete
public function nurse_productivity_ncd_delete($id)
{
      $nurse_productivity_ncd=Nurse_productivity_ncd::find($id)->delete();
      return redirect()->route('nurse_productivity_ncd')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
}

//Create nurse_productivity_ncd_morning
public function nurse_productivity_ncd_morning()
{
      $visit = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS patient_all
            FROM opd_dep_queue o1, ovst o2 WHERE o1.depcode IN ("011")
            AND o1.vn = o2.vn AND o2.vstdate = DATE(NOW()) 
            AND o2.vsttime BETWEEN "00:00:00" AND "15:59:59"'); 

      return view('backoffice_hrd.nurse_productivity_ncd_morning',compact('visit'));            
}

//nurse_productivity_ncd_morning_save
public function nurse_productivity_ncd_morning_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $productivity_ncd = new Nurse_productivity_ncd; 
      $productivity_ncd->report_date = $request->report_date;  
      $productivity_ncd->shift_time = $request->shift_time;
      $productivity_ncd->patient_all = $request->patient_all;
      $productivity_ncd->patient_hr = ($request->patient_all*0.5);
      $productivity_ncd->nurse_oncall = $request->nurse_oncall;
      $productivity_ncd->nurse_partime = $request->nurse_partime;
      $productivity_ncd->nurse_fulltime = $request->nurse_fulltime;
      $productivity_ncd->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*9;
      $productivity_ncd->productivity = (($request->patient_all*0.5)*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*9);
      $productivity_ncd->hhpuos = ($request->patient_all*0.5)/max($request->patient_all,1);
      $productivity_ncd->nurse_shift_time = $request->patient_all*(($request->patient_all*0.5)/max($request->patient_all,1))*(1.4/9);
      $productivity_ncd->recorder = $request->recorder;
      $productivity_ncd->note = $request->note;
      $productivity_ncd->save();

//เปิดแจ้งเตือน Telegram
      $message = "Productivity งานผู้ป่วย NCD" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."ณ เวลา 16.00 น.(เวรเช้า)" ."\n" 
            ."ผู้ป่วยในเวร " .$productivity_ncd->patient_all ." ราย" ."\n" 
            ."อัตรากำลัง Oncall : " .$productivity_ncd->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_ncd->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_ncd->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_ncd->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_ncd->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_ncd->productivity,2) ."\n"  
            . "ผู้บันทึก : " .$productivity_ncd->recorder ."\n";     

      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4689585177","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,คลินิกเบาหวานความดัน-รพ.หัวตะพานTest_Notify_Group2

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

      return redirect()->route('nurse_productivity_ncd_morning')->with('success', 'ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
}

//Create nurse_productivity_lr

public function nurse_productivity_lr(Request $request)
{
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      if($start_date == '' || $end_date == null)
      {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
      if($end_date == '' || $end_date == null)
      {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
      
      $nurse_productivity_lr=Nurse_productivity_lr::whereBetween('report_date',[$start_date,$end_date])->get(); 
      $sum_productivity_lr=DB::select('
            SELECT CASE WHEN shift_time = "เวรเช้า" THEN "1" WHEN shift_time = "เวรบ่าย" THEN "2"
            WHEN shift_time = "เวรดึก" THEN "3" END AS "id",shift_time,COUNT(shift_time) AS shift_time_sum,
            (SUM(patient_all)+SUM(opd_normal)+SUM(opd_high)) AS patient_all,SUM(opd_normal) AS opd_normal,SUM(opd_high) AS opd_high,
            SUM(convalescent) AS convalescent,SUM(moderate_ill) AS moderate_ill,SUM(semi_critical_ill) AS semi_critical_ill,
            SUM(critical_ill) AS critical_ill,SUM(patient_hr) AS patient_hr,SUM(nurse_oncall) AS nurse_oncall,
            SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime,SUM(nurse_hr) AS nurse_hr,
            ((SUM(patient_hr)*100)/SUM(nurse_hr)) AS productivity,(SUM(patient_hr)/(SUM(patient_all)+SUM(opd_normal)+SUM(opd_high))) AS hhpuos,
            ((SUM(patient_all)+SUM(opd_normal)+SUM(opd_high))*(SUM(patient_hr)/(SUM(patient_all)+SUM(opd_normal)+SUM(opd_high)))
            *(1.4/7))/COUNT(shift_time) AS nurse_shift_time
            FROM nurse_productivity_lrs
            WHERE report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY shift_time ORDER BY id');

      $username=Auth::user()->username;
      $del_product=DB::table('users_access')->where('username',$username)->where('del_product','Y')->value("username"); 

      return view('backoffice_hrd.nurse_productivity_lr',compact('sum_productivity_lr','nurse_productivity_lr','start_date','end_date','del_product'));        
}

//Create nurse_productivity_lr_delete
public function nurse_productivity_lr_delete($id)
{
      $nurse_productivity_lr=Nurse_productivity_lr::find($id)->delete();
      return redirect()->route('nurse_productivity_lr')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
}

//Create nurse_productivity_lr_night
public function nurse_productivity_lr_night()
{
      $shift_night = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT i.an),0) AS patient_all,
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "1%" THEN 1 ELSE 0 END),0) AS "convalescent",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "2%" THEN 1 ELSE 0 END),0) AS "moderate_ill",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "3%" THEN 1 ELSE 0 END),0) AS "semi_critical_ill",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "4%" THEN 1 ELSE 0 END),0) AS "critical_ill",
            IFNULL(SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code ="")
            THEN 1 ELSE 0 END),0) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("02") AND i.confirm_discharge = "N"'); 
      $shift_night_opd_normal = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_normal FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority = "0"
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "00:00:00" AND "07:59:59"'); 
      $shift_night_opd_high = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_high FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority IN ("1","2")
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "00:00:00" AND "07:59:59"'); 

      return view('backoffice_hrd.nurse_productivity_lr_night',compact('shift_night','shift_night_opd_normal','shift_night_opd_high'));            
}

//nurse_productivity_lr_night_save
public function nurse_productivity_lr_night_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $productivity_lr = new Nurse_productivity_lr;   
      $productivity_lr->report_date = $request->report_date;  
      $productivity_lr->shift_time = $request->shift_time;
      $productivity_lr->opd_normal = $request->opd_normal;
      $productivity_lr->opd_high = $request->opd_high;
      $productivity_lr->patient_all = ($request->patient_all+$request->opd_normal+$request->opd_high);
      $productivity_lr->convalescent = $request->convalescent;
      $productivity_lr->moderate_ill = $request->moderate_ill;
      $productivity_lr->semi_critical_ill = $request->semi_critical_ill;
      $productivity_lr->critical_ill = $request->critical_ill;  
      $productivity_lr->patient_hr = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99)+($request->opd_normal*0.5)
            +($request->opd_high*1.4));
      $productivity_lr->nurse_oncall = $request->nurse_oncall;
      $productivity_lr->nurse_partime = $request->nurse_partime;
      $productivity_lr->nurse_fulltime = $request->nurse_fulltime;
      $productivity_lr->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
      $productivity_lr->productivity = ((($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99)
            +($request->opd_normal*0.5)+($request->opd_high*1.4))*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
      $productivity_lr->hhpuos = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99)+($request->opd_normal*0.5)
            +($request->opd_high*1.4))/max(($request->patient_all+$request->opd_normal+$request->opd_high),1);
      $productivity_lr->nurse_shift_time =($request->patient_all+$request->opd_normal+$request->opd_high)
            *((($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99)+($request->opd_normal*0.5)
            +($request->opd_high*1.4))/max(($request->patient_all+$request->opd_normal+$request->opd_high),1))*(1.4/7);
      $productivity_lr->recorder = $request->recorder;
      $productivity_lr->note = $request->note;
      $productivity_lr->save();
 
//เปิดแจ้งเตือน Telegram
      $message = "Productivity งานห้องคลอด" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 00.00-08.00 น.(เวรดึก)" ."\n" 
            ."ผู้ป่วยนอกในเวร " ."\n"
            ." -ความเร่งด่วนปกติ " .$productivity_lr->opd_normal ." ราย" ."\n"   
            ." -ความเร่งด่วนมาก " .$productivity_lr->opd_high ." ราย" ."\n"     
            ."ผู้ป่วยสูติกรรมในเวร " ."\n"    
            ." -Convalescent " .$productivity_lr->convalescent ." ราย" ."\n"
            ." -Moderate ill " .$productivity_lr->moderate_ill ." ราย" ."\n"
            ." -Semi critical ill " .$productivity_lr->semi_critical_ill ." ราย" ."\n" 
            ." -Critical ill " .$productivity_lr->critical_ill ." ราย" ."\n"
            ."อัตรากำลัง Oncall : " .$productivity_lr->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_lr->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_lr->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_lr->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_lr->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_lr->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_lr->recorder ."\n";    
      
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4725475158","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,งานห้องคลอด-รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_lr_night')->with('success', 'ส่งข้อมูลเวรดึกเรียบร้อยแล้ว');
}

//Create nurse_productivity_lr_morning
public function nurse_productivity_lr_morning()
{
      $shift_morning = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT i.an),0) AS patient_all,
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "1%" THEN 1 ELSE 0 END),0) AS "convalescent",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "2%" THEN 1 ELSE 0 END),0) AS "moderate_ill",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "3%" THEN 1 ELSE 0 END),0) AS "semi_critical_ill",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "4%" THEN 1 ELSE 0 END),0) AS "critical_ill",
            IFNULL(SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code ="")
            THEN 1 ELSE 0 END),0) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("02") AND i.confirm_discharge = "N"'); 
      $shift_morning_opd_normal = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_normal FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority = "0"
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "08:00:00" AND "15:59:59"'); 
      $shift_morning_opd_high = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_high FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority IN ("1","2")
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "08:00:00" AND "15:59:59"'); 

      return view('backoffice_hrd.nurse_productivity_lr_morning',compact('shift_morning','shift_morning_opd_normal','shift_morning_opd_high'));            
}

//nurse_productivity_lr_morning_save
public function nurse_productivity_lr_morning_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $productivity_lr = new Nurse_productivity_lr;   
      $productivity_lr->report_date = $request->report_date;  
      $productivity_lr->shift_time = $request->shift_time;
      $productivity_lr->opd_normal = $request->opd_normal;
      $productivity_lr->opd_high = $request->opd_high;
      $productivity_lr->patient_all = ($request->patient_all+$request->opd_normal+$request->opd_high);
      $productivity_lr->convalescent = $request->convalescent;
      $productivity_lr->moderate_ill = $request->moderate_ill;
      $productivity_lr->semi_critical_ill = $request->semi_critical_ill;
      $productivity_lr->critical_ill = $request->critical_ill;  
      $productivity_lr->patient_hr = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99)+($request->opd_normal*0.5)
            +($request->opd_high*1.4));
      $productivity_lr->nurse_oncall = $request->nurse_oncall;
      $productivity_lr->nurse_partime = $request->nurse_partime;
      $productivity_lr->nurse_fulltime = $request->nurse_fulltime;
      $productivity_lr->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
      $productivity_lr->productivity = ((($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99)
            +($request->opd_normal*0.5)+($request->opd_high*1.4))*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
      $productivity_lr->hhpuos = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99)+($request->opd_normal*0.5)
            +($request->opd_high*1.4))/max(($request->patient_all+$request->opd_normal+$request->opd_high),1);
      $productivity_lr->nurse_shift_time = ($request->patient_all+$request->opd_normal+$request->opd_high)
            *((($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99)+($request->opd_normal*0.5)
            +($request->opd_high*1.4))/max(($request->patient_all+$request->opd_normal+$request->opd_high),1))*(1.4/7);
      $productivity_lr->recorder = $request->recorder;
      $productivity_lr->note = $request->note;
      $productivity_lr->save();

//เปิดแจ้งเตือน Telegram
      $message = "Productivity งานห้องคลอด" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 08.00-16.00 น.(เวรเช้า)" ."\n" 
            ."ผู้ป่วยนอกในเวร " ."\n"
            ." -ความเร่งด่วนปกติ " .$productivity_lr->opd_normal ." ราย" ."\n"   
            ." -ความเร่งด่วนมาก " .$productivity_lr->opd_high ." ราย" ."\n"     
            ."ผู้ป่วยสูติกรรมในเวร " ."\n"    
            ." -Convalescent " .$productivity_lr->convalescent ." ราย" ."\n"
            ." -Moderate ill " .$productivity_lr->moderate_ill ." ราย" ."\n"
            ." -Semi critical ill " .$productivity_lr->semi_critical_ill ." ราย" ."\n" 
            ." -Critical ill " .$productivity_lr->critical_ill ." ราย" ."\n"
            ."อัตรากำลัง Oncall : " .$productivity_lr->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_lr->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_lr->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_lr->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_lr->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_lr->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_lr->recorder ."\n";    
      
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4725475158","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,งานห้องคลอด-รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_lr_morning')->with('success', 'ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
}

//Create nurse_productivity_lr_afternoon
public function nurse_productivity_lr_afternoon()
{
      $shift_afternoon = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT i.an),0) AS patient_all,
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "1%" THEN 1 ELSE 0 END),0) AS "convalescent",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "2%" THEN 1 ELSE 0 END),0) AS "moderate_ill",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "3%" THEN 1 ELSE 0 END),0) AS "semi_critical_ill",
            IFNULL(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE "4%" THEN 1 ELSE 0 END),0) AS "critical_ill",
            IFNULL(SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code ="")
            THEN 1 ELSE 0 END),0) AS "severe_type_null" 
            FROM ipt i LEFT JOIN an_stat a ON a.an=i.an
            LEFT JOIN patient p ON p.hn=i.hn
            WHERE i.ward IN ("02") AND i.confirm_discharge = "N"'); 
      $shift_afternoon_opd_normal = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_normal FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority = "0"
            AND o2.vstdate = date(DATE_ADD(now(), INTERVAL -1 DAY )) AND o2.vsttime BETWEEN "16:00:00" AND "23:59:59"'); 
      $shift_afternoon_opd_high = DB::connection('hosxp')->select('
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS opd_high FROM opd_dep_queue o1, ovst o2 
            WHERE o1.depcode IN ("021","015") AND o1.vn = o2.vn  AND o2.pt_priority IN ("1","2")
            AND o2.vstdate = date(DATE_ADD(now(), INTERVAL -1 DAY )) AND o2.vsttime BETWEEN "16:00:00" AND "23:59:59"'); 

      return view('backoffice_hrd.nurse_productivity_lr_afternoon',compact('shift_afternoon','shift_afternoon_opd_normal','shift_afternoon_opd_high'));            
}

//nurse_productivity_lr_afternoon_save
public function nurse_productivity_lr_afternoon_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $productivity_lr = new Nurse_productivity_lr;   
      $productivity_lr->report_date = $request->report_date;  
      $productivity_lr->shift_time = $request->shift_time;
      $productivity_lr->opd_normal = $request->opd_normal;
      $productivity_lr->opd_high = $request->opd_high;
      $productivity_lr->patient_all = ($request->patient_all+$request->opd_normal+$request->opd_high);
      $productivity_lr->convalescent = $request->convalescent;
      $productivity_lr->moderate_ill = $request->moderate_ill;
      $productivity_lr->semi_critical_ill = $request->semi_critical_ill;
      $productivity_lr->critical_ill = $request->critical_ill;  
      $productivity_lr->patient_hr = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99)+($request->opd_normal*0.5)
            +($request->opd_high*1.4));
      $productivity_lr->nurse_oncall = $request->nurse_oncall;
      $productivity_lr->nurse_partime = $request->nurse_partime;
      $productivity_lr->nurse_fulltime = $request->nurse_fulltime;
      $productivity_lr->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
      $productivity_lr->productivity = ((($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99)
            +($request->opd_normal*0.5)+($request->opd_high*1.4))*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
      $productivity_lr->hhpuos = (($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99)+($request->opd_normal*0.5)
            +($request->opd_high*1.4))/max(($request->patient_all+$request->opd_normal+$request->opd_high),1);
      $productivity_lr->nurse_shift_time = ($request->patient_all+$request->opd_normal+$request->opd_high)
            *((($request->convalescent*0.45)+($request->moderate_ill*1.17)
            +($request->semi_critical_ill*1.71)+($request->critical_ill*1.99)+($request->opd_normal*0.5)
            +($request->opd_high*1.4))/max(($request->patient_all+$request->opd_normal+$request->opd_high),1))*(1.4/7);
      $productivity_lr->recorder = $request->recorder;
      $productivity_lr->note = $request->note;
      $productivity_lr->save();

//เปิดแจ้งเตือน Telegram
      $message = "Productivity งานห้องคลอด" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."เวลา 08.00-16.00 น.(เวรบ่าย)" ."\n" 
            ."ผู้ป่วยนอกในเวร " ."\n"
            ." -ความเร่งด่วนปกติ " .$productivity_lr->opd_normal ." ราย" ."\n"   
            ." -ความเร่งด่วนมาก " .$productivity_lr->opd_high ." ราย" ."\n"     
            ."ผู้ป่วยสูติกรรมในเวร " ."\n"    
            ." -Convalescent " .$productivity_lr->convalescent ." ราย" ."\n"
            ." -Moderate ill " .$productivity_lr->moderate_ill ." ราย" ."\n"
            ." -Semi critical ill " .$productivity_lr->semi_critical_ill ." ราย" ."\n" 
            ." -Critical ill " .$productivity_lr->critical_ill ." ราย" ."\n"
            ."อัตรากำลัง Oncall : " .$productivity_lr->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_lr->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_lr->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_lr->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_lr->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_lr->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_lr->recorder ."\n";    
            
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4725475158","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,งานห้องคลอด-รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_lr_afternoon')->with('success', 'ส่งข้อมูลเวรบ่ายเรียบร้อยแล้ว');
}

//Create nurse_productivity_or

public function nurse_productivity_or(Request $request)
{
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      if($start_date == '' || $end_date == null)
      {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
      if($end_date == '' || $end_date == null)
      {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
      
      $nurse_productivity_or=Nurse_productivity_or::whereBetween('report_date',[$start_date, $end_date])->get(); 
      $sum_productivity_or=DB::select('
            SELECT shift_time,COUNT(shift_time) AS shift_time_sum,SUM(patient_all) AS patient_all,
            SUM(patient_hr) AS patient_hr,SUM(nurse_oncall) AS nurse_oncall,
            SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime, SUM(nurse_hr) AS nurse_hr,
            ((SUM(patient_hr)*100)/SUM(nurse_hr)) AS productivity,(SUM(patient_hr)/SUM(patient_all)) AS hhpuos,
            (SUM(patient_all)*(SUM(patient_hr)/SUM(patient_all))*(1.4/7))/COUNT(shift_time) AS nurse_shift_time
            FROM nurse_productivity_ors
            WHERE report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY shift_time ORDER BY shift_time DESC');

      $username=Auth::user()->username;
      $del_product=DB::table('users_access')->where('username',$username)->where('del_product','Y')->value("username"); 
      
      return view('backoffice_hrd.nurse_productivity_or',compact('sum_productivity_or','nurse_productivity_or','start_date','end_date','del_product'));        
}

//Create nurse_productivity_or_delete
public function nurse_productivity_or_delete($id)
{
      $nurse_productivity_or=Nurse_productivity_or::find($id)->delete();
      return redirect()->route('nurse_productivity_or')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
}

//Create nurse_productivity_or_morning
public function nurse_productivity_or_morning()
{
      $visit = DB::connection('hosxp')->select(' 
            SELECT IFNULL(COUNT(DISTINCT operation_id),0) AS patient_all
            FROM operation_list WHERE request_operation_date = DATE(NOW()) 
            AND request_operation_time BETWEEN "00:00:00" AND "15:59:59"'); 

      return view('backoffice_hrd.nurse_productivity_or_morning',compact('visit'));            
}

//nurse_productivity_or_morning_save
public function nurse_productivity_or_morning_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $zero="1";
      $productivity_or = new Nurse_productivity_or; 
      $productivity_or->report_date = $request->report_date;  
      $productivity_or->shift_time = $request->shift_time;
      $productivity_or->patient_all = $request->patient_all;
      $productivity_or->patient_hr = ($request->patient_all*0.75);
      $productivity_or->nurse_oncall = $request->nurse_oncall;
      $productivity_or->nurse_partime = $request->nurse_partime;
      $productivity_or->nurse_fulltime = $request->nurse_fulltime;
      $productivity_or->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7;
      $productivity_or->productivity = (($request->patient_all*0.75)*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*7);
      $productivity_or->hhpuos = ($request->patient_all*0.75)/max($request->patient_all,1);
      $productivity_or->nurse_shift_time = $request->patient_all*(($request->patient_all*0.75)/max($request->patient_all,1))*(1.4/7);
      $productivity_or->recorder = $request->recorder;
      $productivity_or->note = $request->note;
      $productivity_or->save();

//เปิดแจ้งเตือน Telegram
      $message = "Productivity ห้องผ่าตัด OR" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."ณ เวลา 16.00 น.(เวรเช้า)" ."\n" 
            ."ผู้ป่วยในเวร " .$productivity_or->patient_all ." ราย" ."\n" 
            ."อัตรากำลัง Oncall : " .$productivity_or->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_or->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_or->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_or->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_or->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ " .number_format($productivity_or->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_or->recorder ."\n";  
            
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_or_morning')->with('success', 'ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
}

//Create nurse_productivity_ckd

public function nurse_productivity_ckd(Request $request)
{
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      if($start_date == '' || $end_date == null)
      {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
      if($end_date == '' || $end_date == null)
      {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
      
      $nurse_productivity_ckd=Nurse_productivity_ckd::whereBetween('report_date',[$start_date, $end_date])->get(); 
      $sum_productivity_ckd=DB::select('
            SELECT shift_time,COUNT(shift_time) AS shift_time_sum,SUM(patient_all) AS patient_all,
            SUM(patient_hr) AS patient_hr,SUM(nurse_oncall) AS nurse_oncall,
            SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime, SUM(nurse_hr) AS nurse_hr,
            ((SUM(patient_hr)*100)/SUM(nurse_hr)) AS productivity,(SUM(patient_hr)/SUM(patient_all)) AS hhpuos,
            (SUM(patient_all)*(SUM(patient_hr)/SUM(patient_all))*(1.4/9))/COUNT(shift_time) AS nurse_shift_time
            FROM nurse_productivity_ckds
            WHERE report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY shift_time ORDER BY shift_time DESC');

      $username=Auth::user()->username;
      $del_product=DB::table('users_access')->where('username',$username)->where('del_product','Y')->value("username"); 

      return view('backoffice_hrd.nurse_productivity_ckd',compact('sum_productivity_ckd','nurse_productivity_ckd','start_date','end_date','del_product'));        
}

//Create nurse_productivity_ckd_delete
public function nurse_productivity_ckd_delete($id)
{
      $nurse_productivity_ckd=Nurse_productivity_ckd::find($id)->delete();
      return redirect()->route('nurse_productivity_ckd')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
}

//Create nurse_productivity_ckd_morning
public function nurse_productivity_ckd_morning()
{
      $visit = DB::connection('hosxp')->select(' 
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS patient_all 
            FROM opd_dep_queue o1, ovst o2 WHERE o1.depcode IN ("031") AND o1.vn = o2.vn  
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "00:00:00" AND "15:59:59"'); 

      return view('backoffice_hrd.nurse_productivity_ckd_morning',compact('visit'));            
}

//nurse_productivity_ckd_morning_save
public function nurse_productivity_ckd_morning_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $zero="1";
      $productivity_ckd = new Nurse_productivity_ckd; 
      $productivity_ckd->report_date = $request->report_date;  
      $productivity_ckd->shift_time = $request->shift_time;
      $productivity_ckd->patient_all = $request->patient_all;
      $productivity_ckd->patient_hr = ($request->patient_all*0.5);
      $productivity_ckd->nurse_oncall = $request->nurse_oncall;
      $productivity_ckd->nurse_partime = $request->nurse_partime;
      $productivity_ckd->nurse_fulltime = $request->nurse_fulltime;
      $productivity_ckd->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*9;
      $productivity_ckd->productivity = (($request->patient_all*0.5)*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*9);
      $productivity_ckd->hhpuos = ($request->patient_all*0.5)/max($request->patient_all,1);
      $productivity_ckd->nurse_shift_time = $request->patient_all*(($request->patient_all*0.5)/max($request->patient_all,1))*(1.4/9);
      $productivity_ckd->recorder = $request->recorder;
      $productivity_ckd->note = $request->note;
      $productivity_ckd->save();

//เปิดแจ้งเตือน Telegram
      $message = "Productivity ผู้ป่วย CKD" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
            ."ณ เวลา 16.00 น.(เวรเช้า)" ."\n" 
            ."ผู้ป่วยในเวร " .$productivity_ckd->patient_all ." ราย" ."\n" 
            ."อัตรากำลัง Oncall : " .$productivity_ckd->nurse_oncall ."\n" 
            ."อัตรากำลังเสริม : " .$productivity_ckd->nurse_partime ."\n"
            ."อัตรากำลังปกติ : " .$productivity_ckd->nurse_fulltime ."\n"
            ."ชม.การพยาบาล : " .number_format($productivity_ckd->patient_hr,2) ."\n"  
            ."ชม.การทำงาน : " .number_format($productivity_ckd->nurse_hr,2) ."\n"  
            ."Productivity ร้อยละ ".number_format($productivity_ckd->productivity,2) ."\n"  
            ."ผู้บันทึก : " .$productivity_ckd->recorder ."\n";   
            
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_ckd_morning')->with('success', 'ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
}

//Create nurse_productivity_hd

public function nurse_productivity_hd(Request $request)
{
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      if($start_date == '' || $end_date == null)
      {$start_date = date('Y-m-d', strtotime("first day of this month"));}else{$start_date =$request->start_date;}
      if($end_date == '' || $end_date == null)
      {$end_date = date('Y-m-d');}else{$end_date =$request->end_date;}
      
      $nurse_productivity_hd=Nurse_productivity_hd::whereBetween('report_date',[$start_date, $end_date])->get(); 
      $sum_productivity_hd=DB::select('
            SELECT shift_time,COUNT(shift_time) AS shift_time_sum,SUM(patient_all) AS patient_all,
            SUM(patient_hr) AS patient_hr,SUM(nurse_oncall) AS nurse_oncall,
            SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime, SUM(nurse_hr) AS nurse_hr,
            ((SUM(patient_hr)*100)/SUM(nurse_hr)) AS productivity,(SUM(patient_hr)/SUM(patient_all)) AS hhpuos,
            (SUM(patient_all)*(SUM(patient_hr)/SUM(patient_all))*(1.4/15))/COUNT(shift_time) AS nurse_shift_time
            FROM nurse_productivity_hds
            WHERE report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
            GROUP BY shift_time ORDER BY shift_time DESC');
      $day_productivity_hd=DB::select('
            SELECT report_date,ROUND(((SUM(patient_hr)*100)/SUM(nurse_hr)),2) AS productivity
            FROM nurse_productivity_hds
            WHERE report_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND shift_time = "เวรเช้า"
            GROUP BY report_date ORDER BY report_date');
      $report_date = array_column($day_productivity_hd,'report_date');
      $morning = array_column($day_productivity_hd,'productivity');

      $username=Auth::user()->username;
      $del_product=DB::table('users_access')->where('username',$username)->where('del_product','Y')->value("username"); 

      return view('backoffice_hrd.nurse_productivity_hd',compact('sum_productivity_hd','nurse_productivity_hd',
            'start_date','end_date','del_product','report_date','morning'));        
}

//Create nurse_productivity_hd_delete
public function nurse_productivity_hd_delete($id)
{
      $nurse_productivity_hd=Nurse_productivity_hd::find($id)->delete();
      return redirect()->route('nurse_productivity_hd')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
}

//Create nurse_productivity_hd_service
public function nurse_productivity_hd_service()
{
      $visit = DB::connection('hosxp')->select(' 
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS patient_all 
            FROM opd_dep_queue o1, ovst o2 WHERE o1.depcode IN ("033") AND o1.vn = o2.vn  
            AND o2.vstdate = DATE(NOW()) AND o2.vsttime BETWEEN "00:00:00" AND "21:59:59"'); 

      return view('backoffice_hrd.nurse_productivity_hd_service',compact('visit'));            
}

//nurse_productivity_hd_service_save
public function nurse_productivity_hd_service_save(Request $request)
{
      $request->validate([
            'nurse_oncall' => 'required',
            'nurse_partime' => 'required',  
            'nurse_fulltime' => 'required',    
            'recorder' => 'required'
        ]);  
      $zero="1";
      $productivity_hd = new Nurse_productivity_hd; 
      $productivity_hd->report_date = $request->report_date;  
      $productivity_hd->shift_time = $request->shift_time;
      $productivity_hd->patient_all = $request->patient_all;
      $productivity_hd->patient_hr = ($request->patient_all*4);
      $productivity_hd->nurse_oncall = $request->nurse_oncall;
      $productivity_hd->nurse_partime = $request->nurse_partime;
      $productivity_hd->nurse_fulltime = $request->nurse_fulltime;
      $productivity_hd->nurse_hr = ($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*15;
      $productivity_hd->productivity = (($request->patient_all*4)*100)
            /(($request->nurse_oncall+$request->nurse_partime+$request->nurse_fulltime)*15);
      $productivity_hd->hhpuos = ($request->patient_all*4)/max($request->patient_all,1);
      $productivity_hd->nurse_shift_time = $request->patient_all*(($request->patient_all*4)/max($request->patient_all,1))*(1.4/15);
      $productivity_hd->recorder = $request->recorder;
      $productivity_hd->note = $request->note;
      $productivity_hd->save();

//เปิดแจ้งเตือน Telegram
      $message = "Productivity ศูนย์ฟอกไต HD รพ." ."\n"
      ."วันที่ " .DateThai(date("Y-m-d")) ."\n"
      ."ผู้ป่วยในเวร " .$productivity_hd->patient_all ." ราย" ."\n" 
      ."อัตรากำลัง Oncall : " .$productivity_hd->nurse_oncall ."\n" 
      ."อัตรากำลังเสริม : " .$productivity_hd->nurse_partime ."\n"
      ."อัตรากำลังปกติ : " .$productivity_hd->nurse_fulltime ."\n"
      ."ชม.การพยาบาล : " .number_format($productivity_hd->patient_hr,2) ."\n"  
      ."ชม.การทำงาน : " .number_format($productivity_hd->nurse_hr,2) ."\n"  
      ."Productivity ร้อยละ " .number_format($productivity_hd->productivity,2) ."\n"  
      ."ผู้บันทึก : " .$productivity_hd->recorder ."\n";    
            
      $token = "7878226178:AAGNIxtdhgi2C607l0lsKmgVXshgzmUp-p0"; //HTP_Notify_Bot
      $chat_ids = ["-4716437484","-4729376994"]; //กลุ่มการพยาบาล-รพ.หัวตะพาน,Test_Notify_Group2

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

      return redirect()->route('nurse_productivity_hd_service')->with('success', 'ส่งข้อมูลเรียบร้อยแล้ว');
}


}
