<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Customer_QueueController extends Controller
{
//Create Single_queue
public function index($vn)
{
    $queue= DB::connection('hosxp')->select('
        SELECT o.vn,o.hn,CONCAT("คุณ",p.fname,SPACE(1),p.lname) AS ptname,k.department,oqs.queue_slot_number,
        o.vstdate,o.vsttime,TIME(NOW()) AS cur_time,rsq.opd_qs_room_s_queue_status,rsq.opd_qs_call_status,
        rsq.opd_qs_dispensary_call_status,rsq.opd_qs_financial_status,
        LEFT(SEC_TO_TIME(time_to_sec(TIME(TIME(NOW())))-time_to_sec(TIME(o.vsttime))),8) AS wait 
        FROM ovst o
        LEFT JOIN patient p ON p.hn=o.hn
        LEFT JOIN kskdepartment k ON k.depcode=o.main_dep
        LEFT JOIN opd_qs_slot oqs ON oqs.vn = o.vn
        LEFT JOIN opd_qs_room r ON r.opd_qs_room_id = oqs.opd_qs_room_id
        LEFT JOIN opd_qs_room_sub_queue rsq ON rsq.vn = o.vn
        WHERE o.vn= "'.$vn.'" 
        ORDER BY o.oqueue');

    foreach ($queue as $row){
        $ptname = $row->ptname; 
        $department = $row->department;  
        $queue_slot_number = $row->queue_slot_number; 
        $vstdate = $row->vstdate; 
        $vsttime = $row->vsttime; 
        $cur_time = $row->cur_time;
        $wait = $row->wait;
        }

    return view('customer_queue.index',compact('ptname','department','queue_slot_number','vstdate','vsttime',
        'cur_time','wait'));
}
}
