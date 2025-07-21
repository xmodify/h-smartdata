<?php

namespace App\Http\Controllers;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class Backoffice_AssetController extends Controller
{
//Check Login
public function __construct()
{
    $this->middleware('auth');
}

//index
public function index()
{
    return view('backoffice_asset.index');            
}
//office
public function office(Request $request)
{  
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}     

    $asset=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.BUY_NAME,sbg.BUDGET_NAME,
        IF(ds.HR_DEPARTMENT_SUB_SUB_NAME IS NULL,"รพ.หัวตะพาน",ds.HR_DEPARTMENT_SUB_SUB_NAME) AS HR_DEPARTMENT_SUB_SUB_NAME 
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_buy st ON st.BUY_ID=a.BUY_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.DECLINE_ID ="5" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');

    $request->session()->put('asset',$asset);
    $request->session()->put('budget_year',$budget_year);
    $request->session()->save();

    return view('backoffice_asset.office',compact('asset','budget_year_select','budget_year'));            
}
//office_excel
public function office_excel(Request $request)
{
    $asset = Session::get('asset');  
    return view('backoffice_asset.office_excel',compact('asset'));
}
public function office_pdf()
{
      $asset = Session::get('asset');
      $budget_year = Session::get('budget_year');  
      $pdf = PDF::loadView('backoffice_asset.office_pdf', compact('asset','budget_year'))
                  ->setPaper('A4', 'landscape');
      return @$pdf->stream();
}
################################################################################################################
//car
public function car(Request $request)
{  
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}     

    $asset=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.BUY_NAME,sbg.BUDGET_NAME,
        IF(ds.HR_DEPARTMENT_SUB_SUB_NAME IS NULL,"รพ.หัวตะพาน",ds.HR_DEPARTMENT_SUB_SUB_NAME) AS HR_DEPARTMENT_SUB_SUB_NAME 
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_buy st ON st.BUY_ID=a.BUY_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.DECLINE_ID ="6" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');

    $request->session()->put('asset',$asset);
    $request->session()->put('budget_year',$budget_year);
    $request->session()->save();

    return view('backoffice_asset.car',compact('asset','budget_year_select','budget_year'));            
}
public function car_excel(Request $request)
{
    $asset = Session::get('asset');  
    return view('backoffice_asset.car_excel',compact('asset'));
}
public function car_pdf()
{
      $asset = Session::get('asset');
      $budget_year = Session::get('budget_year');  
      $pdf = PDF::loadView('backoffice_asset.car_pdf', compact('asset','budget_year'))
                  ->setPaper('A4', 'landscape');
      return @$pdf->stream();
}
##############################################################################################################################
public function electric(Request $request)
{  
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}     

    $asset=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.BUY_NAME,sbg.BUDGET_NAME,
        IF(ds.HR_DEPARTMENT_SUB_SUB_NAME IS NULL,"รพ.หัวตะพาน",ds.HR_DEPARTMENT_SUB_SUB_NAME) AS HR_DEPARTMENT_SUB_SUB_NAME 
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_buy st ON st.BUY_ID=a.BUY_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.DECLINE_ID ="7" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');

    $request->session()->put('asset',$asset);
    $request->session()->put('budget_year',$budget_year);
    $request->session()->save();

    return view('backoffice_asset.electric',compact('asset','budget_year_select','budget_year'));            
}
public function electric_excel(Request $request)
{
    $asset = Session::get('asset');  
    return view('backoffice_asset.electric_excel',compact('asset'));
}
public function electric_pdf()
{
      $asset = Session::get('asset');
      $budget_year = Session::get('budget_year');  
      $pdf = PDF::loadView('backoffice_asset.electric_pdf', compact('asset','budget_year'))
                  ->setPaper('A4', 'landscape');
      return @$pdf->stream();
}
##############################################################################################################################
public function generator(Request $request)
{  
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}     

    $asset=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.BUY_NAME,sbg.BUDGET_NAME,
        IF(ds.HR_DEPARTMENT_SUB_SUB_NAME IS NULL,"รพ.หัวตะพาน",ds.HR_DEPARTMENT_SUB_SUB_NAME) AS HR_DEPARTMENT_SUB_SUB_NAME 
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_buy st ON st.BUY_ID=a.BUY_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.DECLINE_ID ="8" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');

    $request->session()->put('asset',$asset);
    $request->session()->put('budget_year',$budget_year);
    $request->session()->save();

    return view('backoffice_asset.generator',compact('asset','budget_year_select','budget_year'));            
}
public function generator_excel(Request $request)
{
    $asset = Session::get('asset');  
    return view('backoffice_asset.generator_excel',compact('asset'));
}
public function generator_pdf()
{
      $asset = Session::get('asset');
      $budget_year = Session::get('budget_year');  
      $pdf = PDF::loadView('backoffice_asset.generator_pdf', compact('asset','budget_year'))
                  ->setPaper('A4', 'landscape');
      return @$pdf->stream();
}
##############################################################################################################################
public function advert(Request $request)
{  
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}     

    $asset=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.BUY_NAME,sbg.BUDGET_NAME,
        IF(ds.HR_DEPARTMENT_SUB_SUB_NAME IS NULL,"รพ.หัวตะพาน",ds.HR_DEPARTMENT_SUB_SUB_NAME) AS HR_DEPARTMENT_SUB_SUB_NAME 
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_buy st ON st.BUY_ID=a.BUY_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.DECLINE_ID ="9" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');

    $request->session()->put('asset',$asset);
    $request->session()->put('budget_year',$budget_year);
    $request->session()->save();

    return view('backoffice_asset.advert',compact('asset','budget_year_select','budget_year'));            
}
public function advert_excel(Request $request)
{
    $asset = Session::get('asset');  
    return view('backoffice_asset.advert_excel',compact('asset'));
}
public function advert_pdf()
{
      $asset = Session::get('asset');
      $budget_year = Session::get('budget_year');  
      $pdf = PDF::loadView('backoffice_asset.advert_pdf', compact('asset','budget_year'))
                  ->setPaper('A4', 'landscape');
      return @$pdf->stream();
}
##############################################################################################################################
public function agriculture_tool(Request $request)
{  
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}     

    $asset=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.BUY_NAME,sbg.BUDGET_NAME,
        IF(ds.HR_DEPARTMENT_SUB_SUB_NAME IS NULL,"รพ.หัวตะพาน",ds.HR_DEPARTMENT_SUB_SUB_NAME) AS HR_DEPARTMENT_SUB_SUB_NAME 
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_buy st ON st.BUY_ID=a.BUY_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.DECLINE_ID ="10" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');

    $request->session()->put('asset',$asset);
    $request->session()->put('budget_year',$budget_year);
    $request->session()->save();

    return view('backoffice_asset.agriculture_tool',compact('asset','budget_year_select','budget_year'));            
}
public function agriculture_tool_excel(Request $request)
{
    $asset = Session::get('asset');  
    return view('backoffice_asset.agriculture_tool_excel',compact('asset'));
}
public function agriculture_tool_pdf()
{
      $asset = Session::get('asset');
      $budget_year = Session::get('budget_year');  
      $pdf = PDF::loadView('backoffice_asset.agriculture_tool_pdf', compact('asset','budget_year'))
                  ->setPaper('A4', 'landscape');
      return @$pdf->stream();
}
##############################################################################################################################
public function agriculture_mechanical(Request $request)
{  
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}     

    $asset=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.BUY_NAME,sbg.BUDGET_NAME,
        IF(ds.HR_DEPARTMENT_SUB_SUB_NAME IS NULL,"รพ.หัวตะพาน",ds.HR_DEPARTMENT_SUB_SUB_NAME) AS HR_DEPARTMENT_SUB_SUB_NAME 
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_buy st ON st.BUY_ID=a.BUY_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.DECLINE_ID ="11" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');

    $request->session()->put('asset',$asset);
    $request->session()->put('budget_year',$budget_year);
    $request->session()->save();

    return view('backoffice_asset.agriculture_mechanical',compact('asset','budget_year_select','budget_year'));            
}
public function agriculture_mechanical_excel(Request $request)
{
    $asset = Session::get('asset');  
    return view('backoffice_asset.agriculture_mechanical_excel',compact('asset'));
}
public function agriculture_mechanical_pdf()
{
      $asset = Session::get('asset');
      $budget_year = Session::get('budget_year');  
      $pdf = PDF::loadView('backoffice_asset.agriculture_mechanical_pdf', compact('asset','budget_year'))
                  ->setPaper('A4', 'landscape');
      return @$pdf->stream();
}
##############################################################################################################################
public function factory_tool(Request $request)
{  
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}     

    $asset=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.BUY_NAME,sbg.BUDGET_NAME,
        IF(ds.HR_DEPARTMENT_SUB_SUB_NAME IS NULL,"รพ.หัวตะพาน",ds.HR_DEPARTMENT_SUB_SUB_NAME) AS HR_DEPARTMENT_SUB_SUB_NAME 
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_buy st ON st.BUY_ID=a.BUY_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.DECLINE_ID ="12" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');

    $request->session()->put('asset',$asset);
    $request->session()->put('budget_year',$budget_year);
    $request->session()->save();

    return view('backoffice_asset.factory_tool',compact('asset','budget_year_select','budget_year'));            
}
public function factory_tool_excel(Request $request)
{
    $asset = Session::get('asset');  
    return view('backoffice_asset.factory_tool_excel',compact('asset'));
}
public function factory_tool_pdf()
{
      $asset = Session::get('asset');
      $budget_year = Session::get('budget_year');  
      $pdf = PDF::loadView('backoffice_asset.factory_tool_pdf', compact('asset','budget_year'))
                  ->setPaper('A4', 'landscape');
      return @$pdf->stream();
}
##############################################################################################################################
public function science(Request $request)
{  
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}     

    $asset=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.BUY_NAME,sbg.BUDGET_NAME,
        IF(ds.HR_DEPARTMENT_SUB_SUB_NAME IS NULL,"รพ.หัวตะพาน",ds.HR_DEPARTMENT_SUB_SUB_NAME) AS HR_DEPARTMENT_SUB_SUB_NAME 
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_buy st ON st.BUY_ID=a.BUY_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.DECLINE_ID ="17" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');

    $request->session()->put('asset',$asset);
    $request->session()->put('budget_year',$budget_year);
    $request->session()->save();

    return view('backoffice_asset.science',compact('asset','budget_year_select','budget_year'));            
}
public function science_excel(Request $request)
{
    $asset = Session::get('asset');  
    return view('backoffice_asset.science_excel',compact('asset'));
}
public function science_pdf()
{
      $asset = Session::get('asset');
      $budget_year = Session::get('budget_year');  
      $pdf = PDF::loadView('backoffice_asset.science_pdf', compact('asset','budget_year'))
                  ->setPaper('A4', 'landscape');
      return @$pdf->stream();
}
##############################################################################################################################
public function house(Request $request)
{  
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}     

    $asset=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.BUY_NAME,sbg.BUDGET_NAME,
        IF(ds.HR_DEPARTMENT_SUB_SUB_NAME IS NULL,"รพ.หัวตะพาน",ds.HR_DEPARTMENT_SUB_SUB_NAME) AS HR_DEPARTMENT_SUB_SUB_NAME 
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_buy st ON st.BUY_ID=a.BUY_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.DECLINE_ID ="20" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');

    $request->session()->put('asset',$asset);
    $request->session()->put('budget_year',$budget_year);
    $request->session()->save();

    return view('backoffice_asset.house',compact('asset','budget_year_select','budget_year'));            
}
public function house_excel(Request $request)
{
    $asset = Session::get('asset');  
    return view('backoffice_asset.house_excel',compact('asset'));
}
public function house_pdf()
{
      $asset = Session::get('asset');
      $budget_year = Session::get('budget_year');  
      $pdf = PDF::loadView('backoffice_asset.house_pdf', compact('asset','budget_year'))
                  ->setPaper('A4', 'landscape');
      return @$pdf->stream();
}
##############################################################################################################################
public function physical(Request $request)
{  
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}     

    $asset=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.BUY_NAME,sbg.BUDGET_NAME,
        IF(ds.HR_DEPARTMENT_SUB_SUB_NAME IS NULL,"รพ.หัวตะพาน",ds.HR_DEPARTMENT_SUB_SUB_NAME) AS HR_DEPARTMENT_SUB_SUB_NAME 
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_buy st ON st.BUY_ID=a.BUY_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.DECLINE_ID ="21" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');

    $request->session()->put('asset',$asset);
    $request->session()->put('budget_year',$budget_year);
    $request->session()->save();

    return view('backoffice_asset.physical',compact('asset','budget_year_select','budget_year'));            
}
public function physical_excel(Request $request)
{
    $asset = Session::get('asset');  
    return view('backoffice_asset.physical_excel',compact('asset'));
}
public function physical_pdf()
{
      $asset = Session::get('asset');
      $budget_year = Session::get('budget_year');  
      $pdf = PDF::loadView('backoffice_asset.physical_pdf', compact('asset','budget_year'))
                  ->setPaper('A4', 'landscape');
      return @$pdf->stream();
}
############################################################################################################################
//computer
public function computer(Request $request)
{  
    $budget_year_select = DB::connection('backoffice')->select('select LEAVE_YEAR_ID,LEAVE_YEAR_NAME FROM budget_year ORDER BY LEAVE_YEAR_ID DESC LIMIT 7');
    $budget_year_last = DB::connection('backoffice')->table('budget_year')->where('DATE_END','>=',date('Y-m-d'))->where('DATE_BEGIN','<=',date('Y-m-d'))->value('LEAVE_YEAR_ID');
    $budget_year = $request->budget_year;
    if($budget_year == '' || $budget_year == null)
    {$budget_year = $budget_year_last;}else{$budget_year =$request->budget_year;}    

    $asset=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.BUY_NAME,sbg.BUDGET_NAME,
        IF(ds.HR_DEPARTMENT_SUB_SUB_NAME IS NULL,"รพ.หัวตะพาน",ds.HR_DEPARTMENT_SUB_SUB_NAME) AS HR_DEPARTMENT_SUB_SUB_NAME 
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_buy st ON st.BUY_ID=a.BUY_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.DECLINE_ID ="18" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');

    $asset_7440_001=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.METHOD_NAME,sbg.BUDGET_NAME,ds.HR_DEPARTMENT_SUB_SUB_NAME,
        CONCAT(hr.HR_FNAME,SPACE(1),hr.HR_LNAME) AS hr_name,a.ARTICLE_ID
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_method st ON st.METHOD_ID=a.METHOD_ID 
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.SUP_FSN LIKE "7440-001%" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');

    $server=DB::connection('backoffice')->select('select COUNT(*) AS sum FROM asset_article WHERE SUP_FSN BETWEEN "7440-001-0001" AND "7440-001-0002" AND STATUS_ID =1');        
    $client_pc=DB::connection('backoffice')->select('select COUNT(*) AS sum ,SUM(CASE WHEN ARTICLE_PROP LIKE "WindowLicense" THEN 1 ELSE 0 END) AS window,
        SUM(CASE WHEN ARTICLE_PROP LIKE "AntiVirus" THEN 1 ELSE 0 END) AS antivirus 
        FROM asset_article WHERE SUP_FSN BETWEEN "7440-001-0003" AND "7440-001-0007" AND STATUS_ID =1');
    $client_notebook=DB::connection('backoffice')->select('select COUNT(*) AS sum ,SUM(CASE WHEN ARTICLE_PROP LIKE "WindowLicense" THEN 1 ELSE 0 END) AS window,
        SUM(CASE WHEN ARTICLE_PROP LIKE "AntiVirus" THEN 1 ELSE 0 END) AS antivirus 
        FROM asset_article WHERE SUP_FSN BETWEEN "7440-001-0008" AND "7440-001-0009" AND STATUS_ID =1');
    $client_tablet=DB::connection('backoffice')->select('select COUNT(*) AS sum FROM asset_article WHERE SUP_FSN BETWEEN "7440-001-0010" AND "7440-001-0011" AND STATUS_ID =1');
    
    $asset_7440_003=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.METHOD_NAME,sbg.BUDGET_NAME,ds.HR_DEPARTMENT_SUB_SUB_NAME,
        CONCAT(hr.HR_FNAME,SPACE(1),hr.HR_LNAME) AS hr_name
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_method st ON st.METHOD_ID=a.METHOD_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.SUP_FSN LIKE "7440-003%" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');
    $switch_l2=DB::connection('backoffice')->select('select COUNT(*) AS sum FROM asset_article WHERE SUP_FSN BETWEEN "7440-003-0014" AND "7440-003-0015" AND STATUS_ID =1'); 
    $switch_l3=DB::connection('backoffice')->select('select COUNT(*) AS sum FROM asset_article WHERE SUP_FSN BETWEEN "7440-003-0016" AND "7440-003-0016" AND STATUS_ID =1'); 
    $ap=DB::connection('backoffice')->select('select COUNT(*) AS sum FROM asset_article WHERE SUP_FSN BETWEEN "7440-003-0017" AND "7440-003-0018" AND STATUS_ID =1');  
    $smart_card=DB::connection('backoffice')->select('select COUNT(*) AS sum FROM asset_article WHERE SUP_FSN BETWEEN "7440-003-0022" AND "7440-003-0022" AND STATUS_ID =1');               

    $asset_7440_005=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.METHOD_NAME,sbg.BUDGET_NAME,ds.HR_DEPARTMENT_SUB_SUB_NAME,
        CONCAT(hr.HR_FNAME,SPACE(1),hr.HR_LNAME) AS hr_name
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_method st ON st.METHOD_ID=a.METHOD_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.SUP_FSN LIKE "7440-005%" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');
    $asset_7440_006=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.METHOD_NAME,sbg.BUDGET_NAME,ds.HR_DEPARTMENT_SUB_SUB_NAME,
        CONCAT(hr.HR_FNAME,SPACE(1),hr.HR_LNAME) AS hr_name
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_method st ON st.METHOD_ID=a.METHOD_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.SUP_FSN LIKE "7440-006%" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');
    $asset_7440_007=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.METHOD_NAME,sbg.BUDGET_NAME,ds.HR_DEPARTMENT_SUB_SUB_NAME,
        CONCAT(hr.HR_FNAME,SPACE(1),hr.HR_LNAME) AS hr_name
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_method st ON st.METHOD_ID=a.METHOD_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.SUP_FSN LIKE "7440-007%" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');
    $asset_7440_009=DB::connection('backoffice')->select('
        SELECT a.SUP_FSN,s.SUP_NAME,a.ARTICLE_NUM,a.ARTICLE_NAME,sb.BRAND_NAME,sm.MODEL_NAME,sv.VENDOR_NAME,
        a.ARTICLE_PROP,a.RECEIVE_DATE,PRICE_PER_UNIT,st.METHOD_NAME,sbg.BUDGET_NAME,ds.HR_DEPARTMENT_SUB_SUB_NAME,
        CONCAT(hr.HR_FNAME,SPACE(1),hr.HR_LNAME) AS hr_name
        FROM asset_article a
        LEFT JOIN supplies s ON s.SUP_FSN_NUM=a.SUP_FSN
        LEFT JOIN supplies_brand sb ON sb.BRAND_ID=a.BRAND_ID
        LEFT JOIN supplies_model sm ON sm.MODEL_ID=a.MODEL_ID
        LEFT JOIN supplies_vendor sv ON sv.VENDOR_ID=a.VENDOR_ID
        LEFT JOIN supplies_method st ON st.METHOD_ID=a.METHOD_ID
        LEFT JOIN supplies_budget sbg ON sbg.BUDGET_ID=a.BUDGET_ID
        LEFT JOIN hrd_department_sub_sub ds ON ds.HR_DEPARTMENT_SUB_SUB_ID=a.DEP_ID
        LEFT JOIN hrd_person hr ON hr.ID=a.PERSON_ID
        WHERE a.SUP_FSN LIKE "7440-009%" AND a.STATUS_ID = 1
        GROUP BY a.ARTICLE_ID ORDER BY a.SUP_FSN,a.ARTICLE_NUM +0 ');

    $request->session()->put('asset',$asset);
    $request->session()->put('budget_year',$budget_year);
    $request->session()->put('asset_7440_001',$asset_7440_001);
    $request->session()->put('asset_7440_003',$asset_7440_003);
    $request->session()->put('asset_7440_005',$asset_7440_005);
    $request->session()->put('asset_7440_006',$asset_7440_006);
    $request->session()->put('asset_7440_007',$asset_7440_007);
    $request->session()->put('asset_7440_009',$asset_7440_009);
    $request->session()->save();

    return view('backoffice_asset.computer',compact('asset','budget_year','budget_year_select','asset_7440_001','server',
        'client_pc','client_notebook','client_tablet','asset_7440_003','switch_l2','switch_l3','ap','smart_card',
        'asset_7440_005','asset_7440_006','asset_7440_007','asset_7440_009'));            
}
public function computer_excel(Request $request)
{
    $asset = Session::get('asset');  
    return view('backoffice_asset.computer_excel',compact('asset'));
}
public function computer_pdf()
{
      $asset = Session::get('asset');
      $budget_year = Session::get('budget_year');  
      $pdf = PDF::loadView('backoffice_asset.computer_pdf', compact('asset','budget_year'))
                  ->setPaper('A4', 'landscape');
      return @$pdf->stream();
}
//computer_7440_001_excel
public function computer_7440_001_excel(Request $request)
  {
        $asset_7440_001 = Session::get('asset_7440_001');  
        return view('backoffice_asset.computer_7440_001_excel',compact('asset_7440_001'));
  }
public function computer_7440_001_software($ARTICLE_ID)
{
    $data = DB::connection('backoffice')->select('
        SELECT a.ARTICLE_NUM,a.ARTICLE_NAME,al.CARE_LIST_NAME
        FROM asset_article a
        INNER JOIN asset_care_list al ON al.ARTICLE_ID=a.ARTICLE_ID
        WHERE al.ARTICLE_ID = ? 
        GROUP BY a.ARTICLE_NUM,al.CARE_LIST_ID
        ORDER BY al.CARE_LIST_NAME',[$ARTICLE_ID]);
    return response()->json($data);
}
//computer_7440_003_excel
public function computer_7440_003_excel(Request $request)
{
      $asset_7440_003 = Session::get('asset_7440_003');  
      return view('backoffice_asset.computer_7440_003_excel',compact('asset_7440_003'));
}
//computer_7440_005_excel
public function computer_7440_005_excel(Request $request)
{
      $asset_7440_005 = Session::get('asset_7440_005');  
      return view('backoffice_asset.computer_7440_005_excel',compact('asset_7440_005'));
}
//computer_7440_006_excel
public function computer_7440_006_excel(Request $request)
{
      $asset_7440_006 = Session::get('asset_7440_006');  
      return view('backoffice_asset.computer_7440_006_excel',compact('asset_7440_006'));
}
//computer_7440_007_excel
public function computer_7440_007_excel(Request $request)
{
      $asset_7440_007 = Session::get('asset_7440_007');  
      return view('backoffice_asset.computer_7440_007_excel',compact('asset_7440_007'));
}
//computer_7440_009_excel
public function computer_7440_009_excel(Request $request)
{
      $asset_7440_009 = Session::get('asset_7440_009');  
      return view('backoffice_asset.computer_7440_009_excel',compact('asset_7440_009'));
}

}
