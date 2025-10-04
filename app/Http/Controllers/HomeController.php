<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request) 
    {
        $budget_year_select = DB::table('budget_year')
            ->select('LEAVE_YEAR_ID', 'LEAVE_YEAR_NAME')
            ->orderByDesc('LEAVE_YEAR_ID')
            ->limit(7)
            ->get();
        $budget_year_now = DB::table('budget_year')
            ->whereDate('DATE_END', '>=', date('Y-m-d'))
            ->whereDate('DATE_BEGIN', '<=', date('Y-m-d'))
            ->value('LEAVE_YEAR_ID');       
        $budget_year = $request->budget_year ?: $budget_year_now;
        $year_data = DB::table('budget_year')
            ->whereIn('LEAVE_YEAR_ID', [$budget_year, $budget_year - 4])
            ->pluck('DATE_BEGIN', 'LEAVE_YEAR_ID');
        $start_date   = $year_data[$budget_year] ?? null;
        $end_date = DB::table('budget_year')
            ->where('LEAVE_YEAR_ID', $budget_year)
            ->value('DATE_END');

        $opd = DB::connection('hosxp')->select('select 
                    COUNT(DISTINCT vn) as visit , COUNT(DISTINCT hn) as hn
                    FROM vn_stat                        
                    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                    GROUP BY MONTH(vstdate)
                    ORDER BY YEAR(vstdate),MONTH(vstdate)');
        $opd_visit = array_column($opd,'visit');
        $opd_hn = array_column($opd,'hn');  
        
        $opd_diag_top = DB::connection('hosxp')->select('select 
                    CONCAT("[",pdx,"] " ,name) AS name,count(*) AS sum
                    FROM (SELECT v.vn,v.hn,v.vstdate,v.pdx,i.name FROM vn_stat v
                    LEFT JOIN icd101 i ON i.code=v.pdx
                    WHERE v.vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                    AND (v.pdx <>"" AND v.pdx IS NOT NULL)
                    AND v.pdx NOT LIKE "z%" AND v.pdx NOT IN ("u119")) AS a
                    GROUP BY pdx  
                    ORDER BY sum desc limit 10');
        $opd_diag_top_name = array_column($opd_diag_top,'name');
        $opd_diag_top_sum = array_column($opd_diag_top,'sum');
        
        $ipd = DB::connection('hosxp')->select('select
                    COUNT(DISTINCT i.an) AS an ,sum(a.admdate) AS admdate,
                    ROUND((SUM(a.admdate)*100)/(60*DAY(LAST_DAY(a.dchdate))),2) AS "bed_occupancy"
                    FROM an_stat a
                    INNER JOIN ipt i ON a.an=i.an
                    WHERE i.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                    AND a.pdx NOT IN ("Z290","Z208")
                    GROUP BY MONTH(i.dchdate)
                    ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)');
        $ipd_visit = array_column($ipd,'an');  
        $ipd_admdate = array_column($ipd,'admdate');
        $ipd_bed_occupancy = array_column($ipd,'bed_occupancy'); 
        
        $ipd_diag_top = DB::connection('hosxp')->select('select 
                    CONCAT("[",pdx,"] " ,name) AS name,count(*) AS sum
                    FROM (SELECT a.an,a.hn,a.dchdate,a.pdx,i.name FROM an_stat a
                    LEFT JOIN icd101 i ON i.code=a.pdx
                    WHERE a.dchdate BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND a.pdx <>""	
                    AND a.pdx NOT LIKE "z%" AND a.pdx NOT IN ("Z290","Z208")) AS a
                    GROUP BY pdx  
                    ORDER BY sum desc limit 10');
        $ipd_diag_top_name = array_column($ipd_diag_top,'name');
        $ipd_diag_top_sum = array_column($ipd_diag_top,'sum');
              
        $er = DB::connection('hosxp')->select('select MONTH(vstdate) as month,
                    SUM(CASE WHEN er_emergency_type ="1" THEN 1 ELSE 0 END) AS "Resuscitate",
                    SUM(CASE WHEN er_emergency_type ="2" THEN 1 ELSE 0 END) AS "Emergency",
                    SUM(CASE WHEN er_emergency_type ="3" THEN 1 ELSE 0 END) AS "Urgency",
                    SUM(CASE WHEN er_emergency_type ="4" THEN 1 ELSE 0 END) AS "Semi_Urgency",
                    SUM(CASE WHEN er_emergency_type ="5" THEN 1 ELSE 0 END) AS "Non_Urgency",
                    COUNT(DISTINCT vn) as visit
                    FROM er_regist                        
                    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                    GROUP BY MONTH(vstdate)
                    ORDER BY YEAR(vstdate),MONTH(vstdate)');
        $er_visit = array_column($er,'visit');
        $er_Resuscitate = array_column($er,'Resuscitate');
        $er_Emergency = array_column($er,'Emergency');     
        $er_Urgency = array_column($er,'Urgency');
        $er_Semi_Urgency = array_column($er,'Semi_Urgency');
        $er_Non_Urgency = array_column($er,'Non_Urgency');

        $physic = DB::connection('hosxp')->select('select MONTH(DATE(begin_datetime)) AS month,
                    COUNT(DISTINCT vn) as visit
                    FROM physic_list                        
                    WHERE DATE(begin_datetime) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                    GROUP BY MONTH(DATE(begin_datetime))
                    ORDER BY YEAR(DATE(begin_datetime)),MONTH(DATE(begin_datetime))');
        $physic_visit = array_column($physic,'visit'); 
        
        $health_med = DB::connection('hosxp')->select('select MONTH(service_date) AS month,
                    COUNT(DISTINCT vn) as visit
                    FROM health_med_service                        
                    WHERE service_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                    GROUP BY MONTH(service_date)
                    ORDER BY YEAR(service_date),MONTH(service_date)');
        $health_med_visit = array_column($health_med,'visit'); 

        $dent = DB::connection('hosxp')->select('select MONTH(vstdate) as month,
                    COUNT(DISTINCT vn) as visit
                    FROM dtmain                        
                    WHERE vstdate BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                    GROUP BY MONTH(vstdate)
                    ORDER BY YEAR(vstdate),MONTH(vstdate)');
        $dent_visit = array_column($dent,'visit'); 

        $anc = DB::connection('hosxp')->select('select MONTH(anc_service_date) AS month,
                    COUNT(DISTINCT vn) as visit
                    FROM person_anc_service                        
                    WHERE anc_service_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                    GROUP BY MONTH(anc_service_date)
                    ORDER BY YEAR(anc_service_date),MONTH(anc_service_date)');
        $anc_visit = array_column($anc,'visit'); 

        $refer = DB::connection('hosxp')->select('select MONTH(refer_date) AS month,
                    COUNT(DISTINCT vn) as visit
                    FROM referout                        
                    WHERE refer_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                    GROUP BY MONTH(refer_date)
                    ORDER BY YEAR(refer_date),MONTH(refer_date)');
        $refer_visit = array_column($refer,'visit');  

        return view('home',compact('budget_year','opd_visit','opd_hn','er_visit','er_Resuscitate','er_Emergency',
        'er_Urgency','er_Semi_Urgency','er_Non_Urgency','physic_visit','health_med_visit','dent_visit','refer_visit',
        'anc_visit','ipd_visit','ipd_admdate','ipd_bed_occupancy','opd_diag_top_name','opd_diag_top_sum',
        'ipd_diag_top_name','ipd_diag_top_sum','budget_year_select'));
    }
}
