<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MainSettingController;
use App\Http\Controllers\Admin\BudgetYearController;
use App\Http\Controllers\Admin\LookupIcodeController;
use App\Http\Controllers\Admin\LookupWardController;
use App\Http\Controllers\Admin\LookupHospcodeController;
use App\Http\Controllers\Admin\User_AccessController;
use App\Http\Controllers\Hrims\HrimsController;
use App\Http\Controllers\Hrims\ImportStmController;
use App\Http\Controllers\Hrims\CheckController;
use App\Http\Controllers\Hrims\ClaimIpController;
use App\Http\Controllers\Hrims\ClaimOpController;
use App\Http\Controllers\Hrims\MishosController;
use App\Http\Controllers\Hrims\DebtorController;
use App\Http\Controllers\Hnplus\HnplusController;
use App\Http\Controllers\Hnplus\ProductERController;
use App\Http\Controllers\Hnplus\ProductIPDController;
use App\Http\Controllers\Hnplus\ProductVIPController;
use App\Http\Controllers\Hnplus\ProductLRController;
use App\Http\Controllers\Hnplus\ProductOPDController;
use App\Http\Controllers\Hnplus\ProductNCDController;
use App\Http\Controllers\Hnplus\ProductCKDController;
use App\Http\Controllers\Hnplus\ProductHDController;
use App\Http\Controllers\Hnplus\ProductORController;
use App\Http\Controllers\MophNotify\ServiceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Backoffice_AssetController;
use App\Http\Controllers\Backoffice_HrdController;
use App\Http\Controllers\Backoffice_PlanController;
use App\Http\Controllers\Backoffice_RiskController;
use App\Http\Controllers\Customer_QueueController;
use App\Http\Controllers\Customer_ComplainController;
use App\Http\Controllers\Dashboard_DigitalhealthController;
use App\Http\Controllers\Finance_ClaimController;
use App\Http\Controllers\Finance_DebtorController;
use App\Http\Controllers\Finance_StmController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\Form_CheckController;
use App\Http\Controllers\Hosxp_SettingController;
use App\Http\Controllers\Medicalrecord_OpdController;
use App\Http\Controllers\Medicalrecord_IpdController;
use App\Http\Controllers\Medicalrecord_DiagController;
use App\Http\Controllers\Service_DeathController;
use App\Http\Controllers\Service_DentController;
use App\Http\Controllers\Service_DrugController;
use App\Http\Controllers\Service_ERController;
use App\Http\Controllers\Service_HealthMedController;
use App\Http\Controllers\Service_IPDController;
use App\Http\Controllers\Service_MentalController;
use App\Http\Controllers\Service_NCDController;
use App\Http\Controllers\Service_OPDController;
use App\Http\Controllers\Service_OperationController;
use App\Http\Controllers\Service_PCUController;
use App\Http\Controllers\Service_PhysicController;
use App\Http\Controllers\Service_ReferController;
use App\Http\Controllers\Service_XrayController;
use App\Http\Controllers\Service_LabController;
use App\Http\Controllers\SkpcardController;

Auth::routes();
 
// H-SmartData ###########################################################################################################################
// IsAdmin --------------------------------------------------------------------------------------------------------------------------
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    // Clear-cache --------------------------------------------------------------------------------------------------
    Route::post('clear-cache', [MainSettingController::class, 'clearCache'])->name('clear_cache');
    // Git Pull-----------------------------------------------------------------------------------------------------   
    Route::post('/git-pull', function () {
        try { $output = shell_exec('cd ' . base_path() . ' && git pull origin main 2>&1');
            return response()->json(['output' => $output]);
        } catch (\Exception $e) { return response()->json(['error' => $e->getMessage()], 500);}})->name('git.pull');
    //---------------------------------------------------------------------------------------------------
    Route::resource('user_access', User_AccessController::class)->parameters(['user_access' => 'user']);
    Route::get('main_setting', [MainSettingController::class, 'index'])->name('main_setting');
    Route::put('main_setting/{id}', [MainSettingController::class, 'update']);
    Route::post('main_setting/up_structure', [MainSettingController::class, 'up_structure'])->name('up_structure');;
    Route::resource('lookup_icode', LookupIcodeController::class)->parameters(['lookup_icode' => 'icode']);
    Route::post('insert_lookup_uc_cr', [LookupIcodeController::class, 'insert_lookup_uc_cr'])->name('insert_lookup_uc_cr');
    Route::post('insert_lookup_ppfs', [LookupIcodeController::class, 'insert_lookup_ppfs'])->name('insert_lookup_ppfs');
    Route::post('insert_lookup_herb32', [LookupIcodeController::class, 'insert_lookup_herb32'])->name('insert_lookup_herb32');
    Route::resource('lookup_ward', LookupWardController::class)->parameters(['lookup_ward' => 'ward']);
    Route::post('insert_lookup_ward', [LookupWardController::class, 'insert_lookup_ward'])->name('insert_lookup_ward');
    Route::resource('lookup_hospcode', LookupHospcodeController::class)->parameters(['lookup_hospcode' => 'hospcode']);
    Route::resource('budget_year', BudgetYearController::class)->parameters(['LEAVE_YEAR_ID' => 'LEAVE_YEAR_ID']);
});

// home ------------------------------------------------------------------------------------------------------------------------------
    Route::get('/', function () {
            return redirect()->route('home') ;
            });
    Route::match(['get','post'],'/home', [HomeController::class, 'index'])->name('home');

// backoffice_asset ------------------------------------------------------------------------------------------------------------------
    Route::get('backoffice_asset/',[Backoffice_AssetController::class,'index']);
    Route::match(['get','post'],'backoffice_asset/office',[Backoffice_AssetController::class,'office']);
    Route::get('backoffice_asset/office_excel',[Backoffice_AssetController::class,'office_excel']);
    Route::get('backoffice_asset/office_pdf',[Backoffice_AssetController::class,'office_pdf']);
    Route::match(['get','post'],'backoffice_asset/car',[Backoffice_AssetController::class,'car']);
    Route::get('backoffice_asset/car_excel',[Backoffice_AssetController::class,'car_excel']);
    Route::get('backoffice_asset/car_pdf',[Backoffice_AssetController::class,'car_pdf']);
    Route::match(['get','post'],'backoffice_asset/electric',[Backoffice_AssetController::class,'electric']);
    Route::get('backoffice_asset/electric_excel',[Backoffice_AssetController::class,'electric_excel']);
    Route::get('backoffice_asset/electric_pdf',[Backoffice_AssetController::class,'electric_pdf']);
    Route::match(['get','post'],'backoffice_asset/generator',[Backoffice_AssetController::class,'generator']);
    Route::get('backoffice_asset/generator_excel',[Backoffice_AssetController::class,'generator_excel']);
    Route::get('backoffice_asset/generator_pdf',[Backoffice_AssetController::class,'generator_pdf']);
    Route::match(['get','post'],'backoffice_asset/advert',[Backoffice_AssetController::class,'advert']);
    Route::get('backoffice_asset/advert_excel',[Backoffice_AssetController::class,'advert_excel']);
    Route::get('backoffice_asset/advert_pdf',[Backoffice_AssetController::class,'advert_pdf']);
    Route::match(['get','post'],'backoffice_asset/agriculture_tool',[Backoffice_AssetController::class,'agriculture_tool']);
    Route::get('backoffice_asset/agriculture_tool_excel',[Backoffice_AssetController::class,'agriculture_tool_excel']);
    Route::get('backoffice_asset/agriculture_tool_pdf',[Backoffice_AssetController::class,'agriculture_tool_pdf']);
    Route::match(['get','post'],'backoffice_asset/agriculture_mechanical',[Backoffice_AssetController::class,'agriculture_mechanical']);
    Route::get('backoffice_asset/agriculture_mechanical_excel',[Backoffice_AssetController::class,'agriculture_mechanical_excel']);
    Route::get('backoffice_asset/agriculture_mechanical_pdf',[Backoffice_AssetController::class,'agriculture_mechanical_pdf']);
    Route::match(['get','post'],'backoffice_asset/factory_tool',[Backoffice_AssetController::class,'factory_tool']);
    Route::get('backoffice_asset/factory_tool_excel',[Backoffice_AssetController::class,'factory_tool_excel']);
    Route::get('backoffice_asset/factory_tool_pdf',[Backoffice_AssetController::class,'factory_tool_pdf']);
    Route::match(['get','post'],'backoffice_asset/science',[Backoffice_AssetController::class,'science']);
    Route::get('backoffice_asset/science_excel',[Backoffice_AssetController::class,'science_excel']);
    Route::get('backoffice_asset/science_pdf',[Backoffice_AssetController::class,'science_pdf']);
    Route::match(['get','post'],'backoffice_asset/house',[Backoffice_AssetController::class,'house']);
    Route::get('backoffice_asset/house_excel',[Backoffice_AssetController::class,'house_excel']);
    Route::get('backoffice_asset/house_pdf',[Backoffice_AssetController::class,'house_pdf']);
    Route::match(['get','post'],'backoffice_asset/physical',[Backoffice_AssetController::class,'physical']);
    Route::get('backoffice_asset/physical_excel',[Backoffice_AssetController::class,'physical_excel']);
    Route::get('backoffice_asset/physical_pdf',[Backoffice_AssetController::class,'physical_pdf']);
    Route::match(['get','post'],'backoffice_asset/computer',[Backoffice_AssetController::class,'computer']);    
    Route::get('backoffice_asset/computer_excel',[Backoffice_AssetController::class,'computer_excel']);
    Route::get('backoffice_asset/computer_pdf',[Backoffice_AssetController::class,'computer_pdf']);
    Route::get('backoffice_asset/computer_7440_001_excel',[Backoffice_AssetController::class,'computer_7440_001_excel']);
    Route::get('backoffice_asset/computer_7440_001_software/{ARTICLE_ID}',[Backoffice_AssetController::class,'computer_7440_001_software'])->name('asset.computer_7440_001_software');;
    Route::get('backoffice_asset/computer_7440_003_excel',[Backoffice_AssetController::class,'computer_7440_003_excel']);
    Route::get('backoffice_asset/computer_7440_005_excel',[Backoffice_AssetController::class,'computer_7440_005_excel']);
    Route::get('backoffice_asset/computer_7440_006_excel',[Backoffice_AssetController::class,'computer_7440_006_excel']);
    Route::get('backoffice_asset/computer_7440_007_excel',[Backoffice_AssetController::class,'computer_7440_007_excel']);
    Route::get('backoffice_asset/computer_7440_009_excel',[Backoffice_AssetController::class,'computer_7440_009_excel']);

// backoffice_hrd -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    Route::get('backoffice_hrd/',[Backoffice_HrdController::class,'index']);
    Route::match(['get','post'],'backoffice_hrd/health_screen',[Backoffice_HrdController::class,'health_screen']);
    Route::get('backoffice_hrd/health_screen_excel',[Backoffice_HrdController::class,'health_screen_excel']);
    Route::get('backoffice_hrd/health_notscreen',[Backoffice_HrdController::class,'health_notscreen'])->name('health_notscreen');
    Route::get('backoffice_hrd/health_notscreen_pdf',[Backoffice_HrdController::class,'health_notscreen_pdf']);
    Route::match(['get','post'],'backoffice_hrd/checkin',[Backoffice_HrdController::class,'checkin']);
    Route::get('backoffice_hrd/checkin_indiv_pdf/{id}',[Backoffice_HrdController::class,'checkin_indiv_pdf']);
    Route::get('backoffice_hrd/checkin_indiv_detail_pdf/{id}',[Backoffice_HrdController::class,'checkin_indiv_detail_pdf']);    
    Route::match(['get','post'],'backoffice_hrd/nurse_productivity_opd',[Backoffice_HrdController::class,'nurse_productivity_opd'])->name('nurse_productivity_opd');
    Route::get('backoffice_hrd/nurse_productivity_opd_delete/{id}',[Backoffice_HrdController::class,'nurse_productivity_opd_delete']);
    Route::get('backoffice_hrd/nurse_productivity_opd_morning',[Backoffice_HrdController::class,'nurse_productivity_opd_morning'])->name('nurse_productivity_opd_morning'); 
    Route::post('backoffice_hrd/nurse_productivity_opd_morning_save',[Backoffice_HrdController::class,'nurse_productivity_opd_morning_save'])->name('nurse_productivity_opd_morning_save');
    Route::get('backoffice_hrd/nurse_productivity_opd_bd',[Backoffice_HrdController::class,'nurse_productivity_opd_bd'])->name('nurse_productivity_opd_bd'); 
    Route::post('backoffice_hrd/nurse_productivity_opd_bd_save',[Backoffice_HrdController::class,'nurse_productivity_opd_bd_save'])->name('nurse_productivity_opd_bd_save');
    Route::match(['get','post'],'backoffice_hrd/nurse_productivity_ncd',[Backoffice_HrdController::class,'nurse_productivity_ncd'])->name('nurse_productivity_ncd');
    Route::get('backoffice_hrd/nurse_productivity_ncd_delete/{id}',[Backoffice_HrdController::class,'nurse_productivity_ncd_delete']);
    Route::get('backoffice_hrd/nurse_productivity_ncd_morning',[Backoffice_HrdController::class,'nurse_productivity_ncd_morning'])->name('nurse_productivity_ncd_morning'); 
    Route::post('backoffice_hrd/nurse_productivity_ncd_morning_save',[Backoffice_HrdController::class,'nurse_productivity_ncd_morning_save'])->name('nurse_productivity_ncd_morning_save');
    Route::match(['get','post'],'backoffice_hrd/nurse_productivity_lr',[Backoffice_HrdController::class,'nurse_productivity_lr'])->name('nurse_productivity_lr');
    Route::get('backoffice_hrd/nurse_productivity_lr_delete/{id}',[Backoffice_HrdController::class,'nurse_productivity_lr_delete']);
    Route::get('backoffice_hrd/nurse_productivity_lr_night',[Backoffice_HrdController::class,'nurse_productivity_lr_night'])->name('nurse_productivity_lr_night');
    Route::post('backoffice_hrd/nurse_productivity_lr_night_save',[Backoffice_HrdController::class,'nurse_productivity_lr_night_save'])->name('nurse_productivity_lr_night_save');
    Route::get('backoffice_hrd/nurse_productivity_lr_morning',[Backoffice_HrdController::class,'nurse_productivity_lr_morning'])->name('nurse_productivity_lr_morning');
    Route::post('backoffice_hrd/nurse_productivity_lr_morning_save',[Backoffice_HrdController::class,'nurse_productivity_lr_morning_save'])->name('nurse_productivity_lr_morning_save');
    Route::get('backoffice_hrd/nurse_productivity_lr_afternoon',[Backoffice_HrdController::class,'nurse_productivity_lr_afternoon'])->name('nurse_productivity_lr_afternoon');
    Route::post('backoffice_hrd/nurse_productivity_lr_afternoon_save',[Backoffice_HrdController::class,'nurse_productivity_lr_afternoon_save'])->name('nurse_productivity_lr_afternoon_save');
    Route::match(['get','post'],'backoffice_hrd/nurse_productivity_or',[Backoffice_HrdController::class,'nurse_productivity_or'])->name('nurse_productivity_or');
    Route::get('backoffice_hrd/nurse_productivity_or_delete/{id}',[Backoffice_HrdController::class,'nurse_productivity_or_delete']);
    Route::get('backoffice_hrd/nurse_productivity_or_morning',[Backoffice_HrdController::class,'nurse_productivity_or_morning'])->name('nurse_productivity_or_morning'); 
    Route::post('backoffice_hrd/nurse_productivity_or_morning_save',[Backoffice_HrdController::class,'nurse_productivity_or_morning_save'])->name('nurse_productivity_or_morning_save');
    Route::match(['get','post'],'backoffice_hrd/nurse_productivity_ckd',[Backoffice_HrdController::class,'nurse_productivity_ckd'])->name('nurse_productivity_ckd');
    Route::get('backoffice_hrd/nurse_productivity_ckd_delete/{id}',[Backoffice_HrdController::class,'nurse_productivity_ckd_delete']);
    Route::get('backoffice_hrd/nurse_productivity_ckd_morning',[Backoffice_HrdController::class,'nurse_productivity_ckd_morning'])->name('nurse_productivity_ckd_morning'); 
    Route::post('backoffice_hrd/nurse_productivity_ckd_morning_save',[Backoffice_HrdController::class,'nurse_productivity_ckd_morning_save'])->name('nurse_productivity_ckd_morning_save');
    Route::match(['get','post'],'backoffice_hrd/nurse_productivity_hd',[Backoffice_HrdController::class,'nurse_productivity_hd'])->name('nurse_productivity_hd');
    Route::get('backoffice_hrd/nurse_productivity_hd_delete/{id}',[Backoffice_HrdController::class,'nurse_productivity_hd_delete']);
    Route::get('backoffice_hrd/nurse_productivity_hd_service',[Backoffice_HrdController::class,'nurse_productivity_hd_service'])->name('nurse_productivity_hd_service'); 
    Route::post('backoffice_hrd/nurse_productivity_hd_service_save',[Backoffice_HrdController::class,'nurse_productivity_hd_service_save'])->name('nurse_productivity_hd_service_save');

// backoffice_plan ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    Route::get('backoffice_plan/',[Backoffice_PlanController::class,'index']);
    Route::match(['get','post'],'backoffice_plan/service',[Backoffice_PlanController::class,'service']);
    Route::match(['get','post'],'backoffice_plan/diag',[Backoffice_PlanController::class,'diag']);
    Route::match(['get','post'],'backoffice_plan/death',[Backoffice_PlanController::class,'death']);
    Route::match(['get','post'],'backoffice_plan/plan_project',[Backoffice_PlanController::class,'plan_project']);

// backoffice_risk ---------------------------------------------------------------------------------------------------------------------------------
    Route::match(['get','post'],'backoffice_risk/',[Backoffice_RiskController::class,'index']);
    Route::get('backoffice_risk/program_sub/{id}',[Backoffice_RiskController::class,'risk_program_sub']);
    Route::get('backoffice_risk/program_subsub/{id}',[Backoffice_RiskController::class,'risk_program_subsub']);
    Route::get('backoffice_risk/program_detail/{id}',[Backoffice_RiskController::class,'risk_program_detail']);
    Route::get('backoffice_risk/program_sub_detail/{id}',[Backoffice_RiskController::class,'risk_program_sub_detail']);
    Route::get('backoffice_risk/program_subsub_detail/{id}_{id2}',[Backoffice_RiskController::class,'risk_program_subsub_detail']);
    Route::get('backoffice_risk/risk_matrix_detail/{clinic}{consequence}_{likelihood}',[Backoffice_RiskController::class,'risk_matrix_detail']);
    Route::match(['get','post'],'backoffice_risk/med_error',[Backoffice_RiskController::class,'med_error']);
    Route::match(['get','post'],'backoffice_risk/nrls_dataset',[Backoffice_RiskController::class,'risk_nrls_dataset']);
    Route::get('backoffice_risk/nrls_dataset_export',[Backoffice_RiskController::class,'risk_nrls_dataset_export']);
    Route::match(['get','post'],'backoffice_risk/nrls',[Backoffice_RiskController::class,'risk_nrls']);
    Route::get('backoffice_risk/nrls_export',[Backoffice_RiskController::class,'risk_nrls_export']);
    Route::match(['get','post'],'backoffice_risk/nrls_edit',[Backoffice_RiskController::class,'risk_nrls_edit']);
    Route::get('backoffice_risk/nrls_editexport',[Backoffice_RiskController::class,'risk_nrls_editexport']);

// Customer -----------------------------------------------------------------------------------------------------------------------------------------
    Route::match(['get','post'],'customer_complain/',[Customer_ComplainController::class,'index']);
    Route::get('customer_complain/create',[Customer_ComplainController::class,'create'])->name('customer_complain.create');
    Route::post('customer_complain/store',[Customer_ComplainController::class,'store'])->name('customer_complain.store');
    Route::get('customer_queue/{vn}',[Customer_QueueController::class,'index']);

// Dashboard_DigitalhealthController ---------------------------------------------------------------------------------------------------------------
    Route::match(['get','post'],'dashboard/digitalhealth',[Dashboard_DigitalhealthController::class,'digitalhealth']);
    Route::match(['get','post'],'dashboard/opd_mornitor',[Dashboard_DigitalhealthController::class,'opd_mornitor']);
    Route::match(['get','post'],'dashboard/nhso_endpoint',[Dashboard_DigitalhealthController::class,'nhso_endpoint']);
    Route::post('dashboard/nhso_endpoint_pull',[Dashboard_DigitalhealthController::class,'nhso_endpoint_pull']);
    Route::match(['get','post'],'dashboard/nhso_endpoint_pull/{vstdate}/{cid}',[Dashboard_DigitalhealthController::class,'nhso_endpoint_pull_indiv']);
    Route::match(['get','post'],'dashboard/opd_mornitor_non_authen',[Dashboard_DigitalhealthController::class,'opd_mornitor_non_authen']);
    Route::match(['get','post'],'dashboard/opd_mornitor_non_hospmain',[Dashboard_DigitalhealthController::class,'opd_mornitor_non_hospmain']);
    Route::match(['get','post'],'dashboard/opd_mornitor_opanywhere',[Dashboard_DigitalhealthController::class,'opd_mornitor_opanywhere']);
    Route::match(['get','post'],'dashboard/opd_mornitor_ofc',[Dashboard_DigitalhealthController::class,'opd_mornitor_ofc']);
    Route::match(['get','post'],'dashboard/opd_mornitor_tb',[Dashboard_DigitalhealthController::class,'opd_mornitor_tb']);
    Route::match(['get','post'],'dashboard/opd_mornitor_kidney',[Dashboard_DigitalhealthController::class,'opd_mornitor_kidney']);
    Route::match(['get','post'],'dashboard/opd_mornitor_ucherb',[Dashboard_DigitalhealthController::class,'opd_mornitor_ucherb']);
    Route::match(['get','post'],'dashboard/opd_mornitor_ucop_cr',[Dashboard_DigitalhealthController::class,'opd_mornitor_ucop_cr']);
    Route::match(['get','post'],'dashboard/opd_mornitor_ppfs',[Dashboard_DigitalhealthController::class,'opd_mornitor_ppfs']);
    Route::match(['get','post'],'dashboard/opd_mornitor_homeward',[Dashboard_DigitalhealthController::class,'opd_mornitor_homeward']);
    Route::match(['get','post'],'dashboard/opd_mornitor_healthmed',[Dashboard_DigitalhealthController::class,'opd_mornitor_healthmed']);
    Route::match(['get','post'],'dashboard/ipd_mornitor',[Dashboard_DigitalhealthController::class,'ipd_mornitor']);

// From ----------------------------------------------------------------------------------------------------------------------------------------
    Route::get('form',[Form_CheckController::class,'index']);
    Route::match(['get','post'],'form/check_asset_report',[Form_CheckController::class,'check_asset_report']);
    Route::get('form/check_asset_create/{depart}',[Form_CheckController::class,'check_asset_create'])->name('check_asset_create');
    Route::post('form/check_asset_save',[Form_CheckController::class,'check_asset_save'])->name('check_asset_save');

// hosxp_setting -------------------------------------------------------------------------------------------------------------------------------
    Route::get('hosxp_setting',[Hosxp_settingController::class,'index']);
    Route::get('hosxp_setting/income',[Hosxp_settingController::class,'income']);
    Route::match(['get','post'],'hosxp_setting/nondrug',[Hosxp_settingController::class,'nondrug']);
    Route::match(['get','post'],'hosxp_setting/adp_code',[Hosxp_settingController::class,'adp_code']);
    Route::get('hosxp_setting/icd9_opd',[Hosxp_settingController::class,'icd9_opd']);
    Route::get('hosxp_setting/icd9_ipd',[Hosxp_settingController::class,'icd9_ipd']);
    Route::get('hosxp_setting/icd9_dent',[Hosxp_settingController::class,'icd9_dent']);
    Route::post('hosxp_setting/drug_cat_nhso_save',[Hosxp_settingController::class,'drug_cat_nhso_save']);
    Route::post('hosxp_setting/drug_cat_aipn_save',[Hosxp_settingController::class,'drug_cat_aipn_save']);
    Route::get('hosxp_setting/drug_cat',[Hosxp_settingController::class,'drug_cat'])->name('drug_cat');;
    Route::get('hosxp_setting/drug_cat_non_nhso',[Hosxp_settingController::class,'drug_cat_non_nhso']);
    Route::get('hosxp_setting/drug_cat_nhso_price_notmatch_hosxp',[Hosxp_settingController::class,'drug_cat_nhso_price_notmatch_hosxp']);
    Route::get('hosxp_setting/drug_cat_nhso_tmt_notmatch_hosxp',[Hosxp_settingController::class,'drug_cat_nhso_tmt_notmatch_hosxp']);
    Route::get('hosxp_setting/drug_cat_nhso_code24_notmatch_hosxp',[Hosxp_settingController::class,'drug_cat_nhso_code24_notmatch_hosxp']);
    Route::get('hosxp_setting/drug_cat_non_aipn',[Hosxp_settingController::class,'drug_cat_non_aipn']);
    Route::get('hosxp_setting/drug_cat_aipn_price_notmatch_hosxp',[Hosxp_settingController::class,'drug_cat_aipn_price_notmatch_hosxp']);
    Route::get('hosxp_setting/drug_cat_aipn_tmt_notmatch_hosxp',[Hosxp_settingController::class,'drug_cat_aipn_tmt_notmatch_hosxp']);
    Route::get('hosxp_setting/drug_cat_aipn_code24_notmatch_hosxp',[Hosxp_settingController::class,'drug_cat_aipn_code24_notmatch_hosxp']);
    Route::get('hosxp_setting/drug_cat_aipn_export',[Hosxp_settingController::class,'drug_cat_aipn_export']);
    Route::get('hosxp_setting/drug_all',[Hosxp_settingController::class,'drug_all']);
    Route::get('hosxp_setting/drug_all_excel',[Hosxp_settingController::class,'drug_all_excel']);
    Route::get('hosxp_setting/drug_herb',[Hosxp_settingController::class,'drug_herb']);
    Route::get('hosxp_setting/drug_support',[Hosxp_settingController::class,'drug_support']);
    Route::get('hosxp_setting/drug_outside',[Hosxp_settingController::class,'drug_outside']);
    Route::get('hosxp_setting/pttype',[Hosxp_settingController::class,'pttype']);
    Route::get('hosxp_setting/doctor',[Hosxp_settingController::class,'doctor']);
    Route::get('hosxp_setting/clinic',[Hosxp_settingController::class,'clinic']);
    Route::get('hosxp_setting/spclty',[Hosxp_settingController::class,'spclty']);
    Route::get('hosxp_setting/department',[Hosxp_settingController::class,'department']);
    Route::get('hosxp_setting/ovstist',[Hosxp_settingController::class,'ovstist']);
    Route::get('hosxp_setting/vaccine',[Hosxp_settingController::class,'vaccine']);
    Route::get('hosxp_backup',[Hosxp_settingController::class,'hosxp_backup']);

// medicalrecord_opd ---------------------------------------------------------------------------------------------------------------------------------
    Route::get('medicalrecord_opd/',[Medicalrecord_OpdController::class,'index']);
    Route::match(['get','post'],'medicalrecord_opd/non_authen',[Medicalrecord_OpdController::class,'non_authen']);
    Route::match(['get','post'],'medicalrecord_opd/non_hospmain',[Medicalrecord_OpdController::class,'non_hospmain']);
    Route::match(['get','post'],'medicalrecord_opd/nhso_authen',[Medicalrecord_OpdController::class,'nhso_authen']);    

// medicalrecord_ipd ---------------------------------------------------------------------------------------------------------------------------------
    Route::get('medicalrecord_ipd/',[Medicalrecord_IpdController::class,'index']);
    Route::match(['get','post'],'medicalrecord_ipd/wait_doctor_dchsummary',[Medicalrecord_IpdController::class,'wait_doctor_dchsummary']);
    Route::match(['get','post'],'medicalrecord_ipd/wait_icd_coder',[Medicalrecord_IpdController::class,'wait_icd_coder']);
    Route::match(['get','post'],'medicalrecord_ipd/dchsummary',[Medicalrecord_IpdController::class,'dchsummary']);
    Route::match(['get','post'],'medicalrecord_ipd/dchsummary_audit',[Medicalrecord_IpdController::class,'dchsummary_audit']);
    Route::get('medicalrecord_ipd/non_dchsummary',[Medicalrecord_IpdController::class,'non_dchsummary']);  
    Route::get('medicalrecord_ipd/finance_chk',[Medicalrecord_IpdController::class,'finance_chk']);
    Route::get('medicalrecord_ipd/finance_chk_opd_wait_money',[Medicalrecord_IpdController::class,'finance_chk_opd_wait_money']);
    Route::get('medicalrecord_ipd/finance_chk_wait_rcpt_money',[Medicalrecord_IpdController::class,'finance_chk_wait_rcpt_money']);

// medicalrecord_diag -------------------------------------------------------------------------------------------------------------------------------------
    Route::get('medicalrecord_diag/',[Medicalrecord_DiagController::class,'index']);
    Route::match(['get','post'],'medicalrecord_diag/alcohol_withdrawal',[Medicalrecord_DiagController::class,'alcohol_withdrawal']);
    Route::match(['get','post'],'medicalrecord_diag/asthma',[Medicalrecord_DiagController::class,'asthma']);
    Route::match(['get','post'],'medicalrecord_diag/copd',[Medicalrecord_DiagController::class,'copd']);
    Route::match(['get','post'],'medicalrecord_diag/mi',[Medicalrecord_DiagController::class,'mi']);
    Route::match(['get','post'],'medicalrecord_diag/ihd',[Medicalrecord_DiagController::class,'ihd']);
    Route::match(['get','post'],'medicalrecord_diag/palliative_care',[Medicalrecord_DiagController::class,'palliative_care']);
    Route::match(['get','post'],'medicalrecord_diag/pneumonia',[Medicalrecord_DiagController::class,'pneumonia']);
    Route::match(['get','post'],'medicalrecord_diag/sepsis',[Medicalrecord_DiagController::class,'sepsis']);
    Route::match(['get','post'],'medicalrecord_diag/septic_shock',[Medicalrecord_DiagController::class,'septic_shock']);
    Route::match(['get','post'],'medicalrecord_diag/stroke',[Medicalrecord_DiagController::class,'stroke']);
    Route::match(['get','post'],'medicalrecord_diag/head_injury',[Medicalrecord_DiagController::class,'head_injury']);
    Route::match(['get','post'],'medicalrecord_diag/fracture',[Medicalrecord_DiagController::class,'fracture']);
    Route::match(['get','post'],'medicalrecord_diag/trauma',[Medicalrecord_DiagController::class,'trauma']);

// service_death ------------------------------------------------------------------------------------------------------------------------------------
    Route::get('service_death/',[Service_DeathController::class,'index']);
    Route::match(['get','post'],'service_death/count',[Service_DeathController::class,'count']);
    Route::match(['get','post'],'service_death/diag_504',[Service_DeathController::class,'diag_504']);
    Route::match(['get','post'],'service_death/diag_icd10',[Service_DeathController::class,'diag_icd10']);

// service_dent -------------------------------------------------------------------------------------------------------------------------------------
    Route::get('service_dent/',[Service_DentController::class,'index']);
    Route::match(['get','post'],'service_dent/count',[Service_DentController::class,'count']);

// service_drug -------------------------------------------------------------------------------------------------------------------------------------
    Route::get('service_drug/',[Service_DrugController::class,'index']);
    Route::match(['get','post'],'service_drug/antiviral',[Service_DrugController::class,'antiviral']);
    Route::match(['get','post'],'service_drug/antiviral_opd_pdf',[Service_DrugController::class,'antiviral_opd_pdf']);
    Route::match(['get','post'],'service_drug/antiviral_ipd_pdf',[Service_DrugController::class,'antiviral_ipd_pdf']);
    Route::match(['get','post'],'service_drug/prescription',[Service_DrugController::class,'prescription']);
    Route::match(['get','post'],'service_drug/value',[Service_DrugController::class,'value']);
    Route::match(['get','post'],'service_drug/value_diag_opd',[Service_DrugController::class,'value_diag_opd']);
    Route::match(['get','post'],'service_drug/value_diag_ipd',[Service_DrugController::class,'value_diag_ipd']);
    Route::match(['get','post'],'service_drug/herb',[Service_DrugController::class,'herb']);
    Route::match(['get','post'],'service_drug/herb9',[Service_DrugController::class,'herb9']);   
    Route::match(['get','post'],'service_drug/herb32',[Service_DrugController::class,'herb32']);
    Route::match(['get','post'],'service_drug/esrd',[Service_DrugController::class,'esrd']);
    Route::match(['get','post'],'service_drug/hd',[Service_DrugController::class,'hd']);
    Route::match(['get','post'],'service_drug/dmht',[Service_DrugController::class,'dmht']);
    Route::get('service_drug/dmht_excel',[Service_DrugController::class,'dmht_excel']);
    Route::match(['get','post'],'service_drug/due',[Service_DrugController::class,'due']);
    Route::get('service_drug/due_excel',[Service_DrugController::class,'due_excel']);
    Route::match(['get','post'],'service_drug/metformin',[Service_DrugController::class,'metformin']);
    Route::get('service_drug/metformin_excel',[Service_DrugController::class,'metformin_excel']);
    Route::match(['get','post'],'service_drug/warfarin',[Service_DrugController::class,'warfarin']);
    Route::get('service_drug/warfarin_excel',[Service_DrugController::class,'warfarin_excel']);
    Route::match(['get','post'],'service_drug/drugtime_s',[Service_DrugController::class,'drugtime_s']);
    Route::get('service_drug/drugtime_s_excel',[Service_DrugController::class,'drugtime_s_excel']);
    Route::match(['get','post'],'service_drug/drugallergy',[Service_DrugController::class,'drugallergy']);
    Route::get('service_drug/drugallergy_excel',[Service_DrugController::class,'drugallergy_excel']);
    Route::match(['get','post'],'service_drug/value_drug_top',[Service_DrugController::class,'value_drug_top']);
    Route::get('service_drug/value_drug_top_excel',[Service_DrugController::class,'value_drug_top_excel']);

// service_er ------------------------------------------------------------------------------------------------------------------------------------
    Route::get('service_er/',[Service_ERController::class,'index']);
    Route::match(['get','post'],'service_er/count',[Service_ERController::class,'count']);
    Route::match(['get','post'],'service_er/er_type',[Service_ERController::class,'er_type']);
    Route::match(['get','post'],'service_er/er_oper',[Service_ERController::class,'er_oper']);
    Route::match(['get','post'],'service_er/ems',[Service_ERController::class,'ems']);
    Route::match(['get','post'],'service_er/revisit',[Service_ERController::class,'revisit']);
    Route::match(['get','post'],'service_er/bps180up',[Service_ERController::class,'bps180up']);
    Route::match(['get','post'],'service_er/nurse_diag',[Service_ERController::class,'nurse_diag']);
    Route::match(['get','post'],'service_er/waitingtime_admit',[Service_ERController::class,'waitingtime_admit']);
    Route::match(['get','post'],'service_er/diag_top30',[Service_ERController::class,'diag_top30']);
    Route::match(['get','post'],'service_er/diag_504',[Service_ERController::class,'diag_504']);
    Route::match(['get','post'],'service_er/diag_506',[Service_ERController::class,'diag_506']);

// service_health_med ----------------------------------------------------------------------------------------------------------------------------
    Route::get('service_healthmed/',[Service_HealthMedController::class,'index']);
    Route::match(['get','post'],'service_healthmed/count',[Service_HealthMedController::class,'count']);
    Route::match(['get','post'],'service_healthmed/acupuncture',[Service_HealthMedController::class,'acupuncture']);

// service_ipd -----------------------------------------------------------------------------------------------------------------------------------
    Route::get('service_ipd/',[Service_IPDController::class,'index']);
    Route::match(['get','post'],'service_ipd/count',[Service_IPDController::class,'count']);
    Route::match(['get','post'],'service_ipd/count_spclty',[Service_IPDController::class,'count_spclty']);
    Route::match(['get','post'],'service_ipd/diag_top30',[Service_IPDController::class,'diag_top30']);
    Route::match(['get','post'],'service_ipd/diag_505',[Service_IPDController::class,'diag_505']);
    Route::match(['get','post'],'service_ipd/readmit28',[Service_IPDController::class,'readmit28']);
    Route::match(['get','post'],'service_ipd/ipd_oper',[Service_IPDController::class,'ipd_oper']);
    Route::match(['get','post'],'service_ipd/severe_type',[Service_IPDController::class,'severe_type']);
    Route::match(['get','post'],'service_ipd/severe_type_ipd',[Service_IPDController::class,'severe_type_ipd']);
    Route::match(['get','post'],'service_ipd/severe_type_vip',[Service_IPDController::class,'severe_type_vip']);

// service_mental -------------------------------------------------------------------------------------------------------------------------------
    Route::get('service_mental/',[Service_MentalController::class,'index']);
    Route::match(['get','post'],'service_mental/mental_appointment',[Service_MentalController::class,'mental_appointment']);
    Route::match(['get','post'],'service_mental/diag_dementia',[Service_MentalController::class,'diag_dementia']);
    Route::match(['get','post'],'service_mental/diag_addict',[Service_MentalController::class,'diag_addict']);
    Route::match(['get','post'],'service_mental/diag_addict_alcohol',[Service_MentalController::class,'diag_addict_alcohol']);
    Route::match(['get','post'],'service_mental/diag_schizophrenia',[Service_MentalController::class,'diag_schizophrenia']);
    Route::match(['get','post'],'service_mental/diag_depressive',[Service_MentalController::class,'diag_depressive']);
    Route::match(['get','post'],'service_mental/diag_anxiety',[Service_MentalController::class,'diag_anxiety']);
    Route::match(['get','post'],'service_mental/diag_epilepsy',[Service_MentalController::class,'diag_epilepsy']);
    Route::match(['get','post'],'service_mental/diag_retardation',[Service_MentalController::class,'diag_retardation']);
    Route::match(['get','post'],'service_mental/diag_skills',[Service_MentalController::class,'diag_skills']);
    Route::match(['get','post'],'service_mental/diag_autism',[Service_MentalController::class,'diag_autism']);
    Route::match(['get','post'],'service_mental/diag_behavior',[Service_MentalController::class,'diag_behavior']);
    Route::match(['get','post'],'service_mental/diag_selfharm',[Service_MentalController::class,'diag_selfharm']);

// service_ncd ---------------------------------------------------------------------------------------------------------------------------------
    Route::get('service_ncd/',[Service_NCDController::class,'index']);
    Route::match(['get','post'],'service_ncd/dm_clinic',[Service_NCDController::class,'dm_clinic']);
    Route::match(['get','post'],'service_ncd/dm',[Service_NCDController::class,'dm']);
    Route::match(['get','post'],'service_ncd/dm_appointment',[Service_NCDController::class,'dm_appointment']);
    Route::match(['get','post'],'service_ncd/dm_nonclinic',[Service_NCDController::class,'dm_nonclinic']);
    Route::match(['get','post'],'service_ncd/dm_admit',[Service_NCDController::class,'dm_admit']);
    Route::match(['get','post'],'service_ncd/dm_death',[Service_NCDController::class,'dm_death']);
    Route::match(['get','post'],'service_ncd/ht_clinic',[Service_NCDController::class,'ht_clinic']);
    Route::match(['get','post'],'service_ncd/ht',[Service_NCDController::class,'ht']);
    Route::match(['get','post'],'service_ncd/ht_appointment',[Service_NCDController::class,'ht_appointment']);
    Route::match(['get','post'],'service_ncd/ht_nonclinic',[Service_NCDController::class,'ht_nonclinic']);
    Route::match(['get','post'],'service_ncd/ht_admit',[Service_NCDController::class,'ht_admit']);
    Route::match(['get','post'],'service_ncd/ht_death',[Service_NCDController::class,'ht_death']);
    Route::match(['get','post'],'service_ncd/capd_clinic',[Service_NCDController::class,'capd_clinic']);
    Route::match(['get','post'],'service_ncd/capd',[Service_NCDController::class,'capd']);
    Route::match(['get','post'],'service_ncd/capd_appointment',[Service_NCDController::class,'capd_appointment']);
    Route::match(['get','post'],'service_ncd/capd_nonclinic',[Service_NCDController::class,'capd_nonclinic']);
    Route::match(['get','post'],'service_ncd/kidney_clinic',[Service_NCDController::class,'kidney_clinic']);
    Route::match(['get','post'],'service_ncd/kidney_hos',[Service_NCDController::class,'kidney_hos']);
    Route::match(['get','post'],'service_ncd/kidney_outsource',[Service_NCDController::class,'kidney_outsource']);
    Route::match(['get','post'],'service_ncd/kidney_egfr',[Service_NCDController::class,'kidney_egfr']);
    Route::match(['get','post'],'service_ncd/asthma_clinic',[Service_NCDController::class,'asthma_clinic']);
    Route::match(['get','post'],'service_ncd/arv_waiting_period',[Service_NCDController::class,'arv_waiting_period']);

// service_opd -------------------------------------------------------------------------------------------------------------------------------
    Route::get('service_opd/',[Service_OPDController::class,'index']);
    Route::match(['get','post'],'service_opd/count',[Service_OPDController::class,'count']);
    Route::match(['get','post'],'service_opd/count_spclty',[Service_OPDController::class,'count_spclty']);
    Route::match(['get','post'],'service_opd/diag_top30',[Service_OPDController::class,'diag_top30']);
    Route::match(['get','post'],'service_opd/diag_504',[Service_OPDController::class,'diag_504']);
    Route::match(['get','post'],'service_opd/diag_506',[Service_OPDController::class,'diag_506']);
    Route::match(['get','post'],'service_opd/waiting_period',[Service_OPDController::class,'waiting_period']);
    Route::match(['get','post'],'service_opd/telehealth',[Service_OPDController::class,'telehealth']);

// service_operation ------------------------------------------------------------------------------------------------------------------------
    Route::get('service_operation/',[Service_OperationController::class,'index']);
    Route::match(['get','post'],'service_operation/count',[Service_OperationController::class,'count']);

// service_pcu ------------------------------------------------------------------------------------------------------------------------------
    Route::get('service_pcu/',[Service_PCUController::class,'index']);
    Route::match(['get','post'],'service_pcu/pcu1_village',[Service_PCUController::class,'pcu1_village']);
    Route::match(['get','post'],'service_pcu/pcu1_vt_ehp',[Service_PCUController::class,'pcu1_vt_ehp']);
    Route::match(['get','post'],'service_pcu/pcu1_home_visit',[Service_PCUController::class,'pcu1_home_visit']);
    Route::match(['get','post'],'service_pcu/diag_top30',[Service_PCUController::class,'diag_top30']);
    Route::match(['get','post'],'service_pcu/death',[Service_PCUController::class,'death']);

// service_physic --------------------------------------------------------------------------------------------------------------------------
    Route::get('service_physic/',[Service_PhysicController::class,'index']);
    Route::match(['get','post'],'service_physic/count',[Service_PhysicController::class,'count']);
    Route::match(['get','post'],'service_physic/diag_top30',[Service_PhysicController::class,'diag_top30']);
    Route::match(['get','post'],'service_physic/diag',[Service_PhysicController::class,'diag']);
    Route::match(['get','post'],'service_physic/physic_appointment',[Service_PhysicController::class,'physic_appointment']);

// service_refer ----------------------------------------------------------------------------------------------------------------------------
    Route::get('service_refer/',[Service_ReferController::class,'index']);
    Route::match(['get','post'],'service_refer/count',[Service_ReferController::class,'count']);
    Route::match(['get','post'],'service_refer/diag',[Service_ReferController::class,'diag']);
    Route::match(['get','post'],'service_refer/diag_top',[Service_ReferController::class,'diag_top']);
    Route::match(['get','post'],'service_refer/after_admit4',[Service_ReferController::class,'after_admit4']);
    Route::match(['get','post'],'service_refer/after_admit24',[Service_ReferController::class,'after_admit24']);
    Route::match(['get','post'],'service_refer/not_complete',[Service_ReferController::class,'not_complete']);

// service_xray ---------------------------------------------------------------------------------------------------------------------------
    Route::get('service_xray/',[Service_XrayController::class,'index']);
    Route::match(['get','post'],'service_xray/ct',[Service_XrayController::class,'ct']);
    Route::get('service_xray/ct_excel',[Service_XrayController::class,'ct_excel']);

// service_lab ---------------------------------------------------------------------------------------------------------------------------
    Route::get('service_lab/',[Service_LabController::class,'index']);
    Route::match(['get','post'],'service_lab/value_top',[Service_LabController::class,'value_top']);
    Route::get('service_lab/value_top_excel',[Service_LabController::class,'value_top_excel']);

// skpcard -------------------------------------------------------------------------------------------------------------------------------
    Route::match(['get','post'],'skpcard',[SkpcardController::class,'index'])->name('skpcard.index');
    Route::get('skpcard/create',[SkpcardController::class,'create'])->name('skpcard.create');
    Route::post('skpcard/store',[SkpcardController::class,'store'])->name('skpcard.store');
    Route::get('skpcard/edit/{id}',[SkpcardController::class,'edit'])->name('skpcard.edit');
    Route::put('skpcard/update/{id}',[SkpcardController::class,'update'])->name('skpcard.update');

// Moph Notify ###########################################################################################################################
    Route::get('/mophnotify/service_night', [ServiceController::class, 'service_night']);
    Route::get('/mophnotify/service_morning', [ServiceController::class, 'service_morning']);
    Route::get('/mophnotify/service_afternoon', [ServiceController::class, 'service_afternoon']);

// H-RiMS ################################################################################################################################
Route::prefix('hrims')->middleware(['auth', 'hrims'])->name('hrims.')->group(function () {
    Route::get('/', [HrimsController::class, 'index'])->name('dashboard');

    // H-RiMS Import_stm -----------------------------------------------------------------------------------------------------------------------
    Route::get('import_stm/',[ImportStmController::class,'index']);
    Route::match(['get','post'],'import_stm/ofc',[ImportStmController::class,'ofc'])->name('import_stm.ofc');
    Route::post('import_stm/ofc_save',[ImportStmController::class,'ofc_save']);
    Route::post('import_stm/ofc_updateReceipt',[ImportStmController::class,'ofc_updateReceipt']);
    Route::match(['get','post'],'import_stm/ofc_detail',[ImportStmController::class,'ofc_detail']);
    Route::match(['get','post'],'import_stm/ofc_kidney',[ImportStmController::class,'ofc_kidney'])->name('import_stm.ofc_kidney');
    Route::post('import_stm/ofc_kidney_save',[ImportStmController::class,'ofc_kidney_save']);
    Route::post('import_stm/ofc_kidney_updateReceipt',[ImportStmController::class,'ofc_kidney_updateReceipt']);
    Route::match(['get','post'],'import_stm/ofc_kidneydetail',[ImportStmController::class,'ofc_kidneydetail']);
    Route::match(['get','post'],'import_stm/lgo',[ImportStmController::class,'lgo'])->name('import_stm.lgo');
    Route::post('import_stm/lgo_save',[ImportStmController::class,'lgo_save']);
    Route::post('import_stm/lgo_updateReceipt',[ImportStmController::class,'lgo_updateReceipt']);
    Route::match(['get','post'],'import_stm/lgo_detail',[ImportStmController::class,'lgo_detail']);
    Route::match(['get','post'],'import_stm/lgo_kidney',[ImportStmController::class,'lgo_kidney'])->name('import_stm.lgo_kidney');
    Route::post('import_stm/lgo_kidney_save',[ImportStmController::class,'lgo_kidney_save']);
    Route::post('import_stm/lgo_kidney_updateReceipt',[ImportStmController::class,'lgo_kidney_updateReceipt']);
    Route::match(['get','post'],'import_stm/lgo_kidneydetail',[ImportStmController::class,'lgo_kidneydetail']);
    Route::match(['get','post'],'import_stm/sss_kidney',[ImportStmController::class,'sss_kidney'])->name('import_stm.sss_kidney');
    Route::post('import_stm/sss_kidney_save',[ImportStmController::class,'sss_kidney_save']);
    Route::post('import_stm/sss_kidney_updateReceipt',[ImportStmController::class,'sss_kidney_updateReceipt']);
    Route::match(['get','post'],'import_stm/sss_kidneydetail',[ImportStmController::class,'sss_kidneydetail']);
    Route::match(['get','post'],'import_stm/ucs',[ImportStmController::class,'ucs'])->name('import_stm.ucs');
    Route::post('import_stm/ucs_save',[ImportStmController::class,'ucs_save']);
    Route::post('import_stm/ucs_updateReceipt',[ImportStmController::class,'ucs_updateReceipt']);
    Route::match(['get','post'],'import_stm/ucs_detail',[ImportStmController::class,'ucs_detail']);
    Route::match(['get','post'],'import_stm/ucs_kidney',[ImportStmController::class,'ucs_kidney'])->name('import_stm.ucs_kidney');
    Route::post('import_stm/ucs_kidney_save',[ImportStmController::class,'ucs_kidney_save']);
    Route::post('import_stm/ucs_kidney_updateReceipt',[ImportStmController::class,'ucs_kidney_updateReceipt']);
    Route::match(['get','post'],'import_stm/ucs_kidneydetail',[ImportStmController::class,'ucs_kidneydetail']);

    //H-RiMS Check------------------------------------------------------------------------------------------------------------------------------
    Route::match(['get','post'],'check/nhso_endpoint',[CheckController::class,'nhso_endpoint']);
    Route::match(['get','post'],'check/fdh_claim_status',[CheckController::class,'fdh_claim_status']);
    Route::post('check/drug_cat_nhso_save',[CheckController::class,'drug_cat_nhso_save']);
    Route::get('check/drug_cat',[CheckController::class,'drug_cat'])->name('drug_cat');;
    Route::get('check/drug_cat_non_nhso',[CheckController::class,'drug_cat_non_nhso']);
    Route::get('check/drug_cat_nhso_price_notmatch_hosxp',[CheckController::class,'drug_cat_nhso_price_notmatch_hosxp']);
    Route::get('check/drug_cat_nhso_tmt_notmatch_hosxp',[CheckController::class,'drug_cat_nhso_tmt_notmatch_hosxp']);
    Route::get('check/drug_cat_nhso_code24_notmatch_hosxp',[CheckController::class,'drug_cat_nhso_code24_notmatch_hosxp']);
    Route::get('check/drug_cat_herb',[CheckController::class,'drug_cat_herb']);
    Route::get('check/pttype',[CheckController::class,'pttype']);
    Route::get('check/nhso_subinscl',[CheckController::class,'nhso_subinscl']);

    // H-RiMS Claim_OP -------------------------------------------------------------------------------------------------------------------------
    Route::match(['get','post'],'claim_op/ucs_incup',[ClaimOpController::class,'ucs_incup']);
    Route::match(['get','post'],'claim_op/ucs_inprovince',[ClaimOpController::class,'ucs_inprovince']);
    Route::match(['get','post'],'claim_op/ucs_inprovince_va',[ClaimOpController::class,'ucs_inprovince_va']);
    Route::match(['get','post'],'claim_op/ucs_outprovince',[ClaimOpController::class,'ucs_outprovince']);
    Route::match(['get','post'],'claim_op/ucs_kidney',[ClaimOpController::class,'ucs_kidney']);
    Route::match(['get','post'],'claim_op/stp_incup',[ClaimOpController::class,'stp_incup']);
    Route::match(['get','post'],'claim_op/stp_outcup',[ClaimOpController::class,'stp_outcup']);
    Route::match(['get','post'],'claim_op/ofc',[ClaimOpController::class,'ofc']);
    Route::match(['get','post'],'claim_op/ofc_kidney',[ClaimOpController::class,'ofc_kidney']);
    Route::match(['get','post'],'claim_op/lgo',[ClaimOpController::class,'lgo']);
    Route::match(['get','post'],'claim_op/lgo_kidney',[ClaimOpController::class,'lgo_kidney']);
    Route::match(['get','post'],'claim_op/bkk',[ClaimOpController::class,'bkk']);
    Route::match(['get','post'],'claim_op/bmt',[ClaimOpController::class,'bmt']);
    Route::match(['get','post'],'claim_op/sss_ppfs',[ClaimOpController::class,'sss_ppfs']);
    Route::match(['get','post'],'claim_op/sss_fund',[ClaimOpController::class,'sss_fund']);
    Route::match(['get','post'],'claim_op/sss_kidney',[ClaimOpController::class,'sss_kidney']);
    Route::match(['get','post'],'claim_op/sss_hc',[ClaimOpController::class,'sss_hc']);
    Route::match(['get','post'],'claim_op/rcpt',[ClaimOpController::class,'rcpt']);
    Route::match(['get','post'],'claim_op/act',[ClaimOpController::class,'act']);

    // H-RiMS Claim_IP -------------------------------------------------------------------------------------------------------------------------
    Route::match(['get','post'],'claim_ip/ucs_incup',[ClaimIpController::class,'ucs_incup']);
    Route::match(['get','post'],'claim_ip/ucs_outcup',[ClaimIpController::class,'ucs_outcup']);
    Route::match(['get','post'],'claim_ip/stp',[ClaimIpController::class,'stp']);
    Route::match(['get','post'],'claim_ip/ofc',[ClaimIpController::class,'ofc']);
    Route::match(['get','post'],'claim_ip/lgo',[ClaimIpController::class,'lgo']);
    Route::match(['get','post'],'claim_ip/bkk',[ClaimIpController::class,'bkk']);
    Route::match(['get','post'],'claim_ip/bmt',[ClaimIpController::class,'bmt']);
    Route::match(['get','post'],'claim_ip/sss',[ClaimIpController::class,'sss']);
    Route::match(['get','post'],'claim_ip/sss_hc',[ClaimIpController::class,'sss_hc']);
    Route::match(['get','post'],'claim_ip/gof',[ClaimIpController::class,'gof']);
    Route::match(['get','post'],'claim_ip/rcpt',[ClaimIpController::class,'rcpt']);
    Route::match(['get','post'],'claim_ip/act',[ClaimIpController::class,'act']);

    // H-RiMS Mishos -------------------------------------------------------------------------------------------------------------------------
    Route::match(['get','post'],'mishos/ucs_ae',[MishosController::class,'ucs_ae']);
    Route::match(['get','post'],'mishos/ucs_walkin',[MishosController::class,'ucs_walkin']);
    Route::match(['get','post'],'mishos/ucs_herb',[MishosController::class,'ucs_herb']);
    Route::match(['get','post'],'mishos/ucs_telemed',[MishosController::class,'ucs_telemed']);
    Route::match(['get','post'],'mishos/ucs_rider',[MishosController::class,'ucs_rider']);
    Route::match(['get','post'],'mishos/ucs_gdm',[MishosController::class,'ucs_gdm']);
    Route::match(['get','post'],'mishos/ucs_drug_clopidogrel',[MishosController::class,'ucs_drug_clopidogrel']);
    Route::match(['get','post'],'mishos/ucs_drug_sk',[MishosController::class,'ucs_drug_sk']);
    Route::match(['get','post'],'mishos/ucs_ins',[MishosController::class,'ucs_ins']);
    Route::match(['get','post'],'mishos/ucs_palliative',[MishosController::class,'ucs_palliative']);
    Route::match(['get','post'],'mishos/ucs_ppfs_fp',[MishosController::class,'ucs_ppfs_fp']);
    Route::match(['get','post'],'mishos/ucs_ppfs_prt',[MishosController::class,'ucs_ppfs_prt']);
    Route::match(['get','post'],'mishos/ucs_ppfs_ida',[MishosController::class,'ucs_ppfs_ida']);
    Route::match(['get','post'],'mishos/ucs_ppfs_ferrofolic',[MishosController::class,'ucs_ppfs_ferrofolic']);
    Route::match(['get','post'],'mishos/ucs_ppfs_fluoride',[MishosController::class,'ucs_ppfs_fluoride']);
    Route::match(['get','post'],'mishos/ucs_ppfs_anc',[MishosController::class,'ucs_ppfs_anc']);
    Route::match(['get','post'],'mishos/ucs_ppfs_postnatal',[MishosController::class,'ucs_ppfs_postnatal']);
    Route::match(['get','post'],'mishos/ucs_ppfs_fittest',[MishosController::class,'ucs_ppfs_fittest']);
    Route::match(['get','post'],'mishos/ucs_ppfs_scr',[MishosController::class,'ucs_ppfs_scr']);

    // H-RiMS Debtor -------------------------------------------------------------------------------------------------------------------------
    Route::get('debtor',[DebtorController::class,'index']);    
    Route::match(['get','post'],'debtor/check_income',[DebtorController::class,'_check_income']);
    Route::match(['get','post'],'debtor/check_nondebtor',[DebtorController::class,'_check_nondebtor']);
    Route::match(['get','post'],'debtor/summary',[DebtorController::class,'_summary']);
    Route::match(['get','post'],'debtor/summary_pdf',[DebtorController::class,'_summary_pdf']);
    Route::get('debtor/forget_search', function() { Session::forget('search'); return redirect()->back();});
    Route::match(['get','post'],'debtor/1102050101_103',[DebtorController::class,'_1102050101_103']);
    Route::post('debtor/1102050101_103_confirm',[DebtorController::class,'_1102050101_103_confirm']);
    Route::delete('debtor/1102050101_103_delete',[DebtorController::class,'_1102050101_103_delete']);
    Route::put('debtor/1102050101_103/update/{vn}',[DebtorController::class,'_1102050101_103_update']);
    Route::get('debtor/1102050101_103_daily_pdf',[DebtorController::class,'_1102050101_103_daily_pdf']);
    Route::get('debtor/1102050101_103_indiv_excel',[DebtorController::class,'_1102050101_103_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_109',[DebtorController::class,'_1102050101_109']);
    Route::post('debtor/1102050101_109_confirm',[DebtorController::class,'_1102050101_109_confirm']);
    Route::delete('debtor/1102050101_109_delete',[DebtorController::class,'_1102050101_109_delete']);
    Route::put('debtor/1102050101_109/update/{vn}',[DebtorController::class,'_1102050101_109_update']);
    Route::get('debtor/1102050101_109_daily_pdf',[DebtorController::class,'_1102050101_109_daily_pdf']);
    Route::get('debtor/1102050101_109_indiv_excel',[DebtorController::class,'_1102050101_109_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_201',[DebtorController::class,'_1102050101_201']);
    Route::post('debtor/1102050101_201_confirm',[DebtorController::class,'_1102050101_201_confirm']);
    Route::delete('debtor/1102050101_201_delete',[DebtorController::class,'_1102050101_201_delete']);
    Route::get('debtor/1102050101_201_daily_pdf',[DebtorController::class,'_1102050101_201_daily_pdf']);
    Route::get('debtor/1102050101_201_indiv_excel',[DebtorController::class,'_1102050101_201_indiv_excel']);
    Route::post('debtor/1102050101_201_average_receive',[DebtorController::class, '_1102050101_201_average_receive']);  
    Route::match(['get','post'],'debtor/1102050101_203',[DebtorController::class,'_1102050101_203']);
    Route::post('debtor/1102050101_203_confirm',[DebtorController::class,'_1102050101_203_confirm']);
    Route::delete('debtor/1102050101_203_delete',[DebtorController::class,'_1102050101_203_delete']);
    Route::put('debtor/1102050101_203/update/{vn}',[DebtorController::class,'_1102050101_203_update']);
    Route::get('debtor/1102050101_203_daily_pdf',[DebtorController::class,'_1102050101_203_daily_pdf']);
    Route::get('debtor/1102050101_203_indiv_excel',[DebtorController::class,'_1102050101_203_indiv_excel']);
    Route::post('debtor/1102050101_203_average_receive',[DebtorController::class, '_1102050101_203_average_receive']); 
    Route::match(['get','post'],'debtor/1102050101_209',[DebtorController::class,'_1102050101_209']);
    Route::post('debtor/1102050101_209_confirm',[DebtorController::class,'_1102050101_209_confirm']);
    Route::delete('debtor/1102050101_209_delete',[DebtorController::class,'_1102050101_209_delete']);
    Route::get('debtor/1102050101_209_daily_pdf',[DebtorController::class,'_1102050101_209_daily_pdf']);
    Route::get('debtor/1102050101_209_indiv_excel',[DebtorController::class,'_1102050101_209_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_216',[DebtorController::class,'_1102050101_216']);
    Route::post('debtor/1102050101_216_confirm_kidney',[DebtorController::class,'_1102050101_216_confirm_kidney']);
    Route::post('debtor/1102050101_216_confirm_cr',[DebtorController::class,'_1102050101_216_confirm_cr']);
    Route::post('debtor/1102050101_216_confirm_anywhere',[DebtorController::class,'_1102050101_216_confirm_anywhere']);
    Route::delete('debtor/1102050101_216_delete',[DebtorController::class,'_1102050101_216_delete']);
    Route::get('debtor/1102050101_216_daily_pdf',[DebtorController::class,'_1102050101_216_daily_pdf']);
    Route::get('debtor/1102050101_216_indiv_excel',[DebtorController::class,'_1102050101_216_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_301',[DebtorController::class,'_1102050101_301']);
    Route::post('debtor/1102050101_301_confirm',[DebtorController::class,'_1102050101_301_confirm']);
    Route::delete('debtor/1102050101_301_delete',[DebtorController::class,'_1102050101_301_delete']);
    Route::get('debtor/1102050101_301_daily_pdf',[DebtorController::class,'_1102050101_301_daily_pdf']);
    Route::get('debtor/1102050101_301_indiv_excel',[DebtorController::class,'_1102050101_301_indiv_excel']); 
    Route::post('debtor/1102050101_301_average_receive',[DebtorController::class, '_1102050101_301_average_receive']);   
    Route::match(['get','post'],'debtor/1102050101_303',[DebtorController::class,'_1102050101_303']);
    Route::post('debtor/1102050101_303_confirm',[DebtorController::class,'_1102050101_303_confirm']);
    Route::delete('debtor/1102050101_303_delete',[DebtorController::class,'_1102050101_303_delete']);
    Route::put('debtor/1102050101_303/update/{vn}',[DebtorController::class,'_1102050101_303_update']);
    Route::get('debtor/1102050101_303_daily_pdf',[DebtorController::class,'_1102050101_303_daily_pdf']);
    Route::get('debtor/1102050101_303_indiv_excel',[DebtorController::class,'_1102050101_303_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_307',[DebtorController::class,'_1102050101_307']);
    Route::post('debtor/1102050101_307_confirm',[DebtorController::class,'_1102050101_307_confirm']);
    Route::post('debtor/1102050101_307_confirm_ip',[DebtorController::class,'_1102050101_307_confirm_ip']);
    Route::delete('debtor/1102050101_307_delete',[DebtorController::class,'_1102050101_307_delete']);
    Route::put('debtor/1102050101_307/update/{vn}',[DebtorController::class,'_1102050101_307_update']);
    Route::get('debtor/1102050101_307_daily_pdf',[DebtorController::class,'_1102050101_307_daily_pdf']);
    Route::get('debtor/1102050101_307_indiv_excel',[DebtorController::class,'_1102050101_307_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_309',[DebtorController::class,'_1102050101_309']);
    Route::post('debtor/1102050101_309_confirm',[DebtorController::class,'_1102050101_309_confirm']);
    Route::delete('debtor/1102050101_309_delete',[DebtorController::class,'_1102050101_309_delete']);
    Route::put('debtor/1102050101_309/update/{vn}',[DebtorController::class,'_1102050101_309_update']);
    Route::get('debtor/1102050101_309_daily_pdf',[DebtorController::class,'_1102050101_309_daily_pdf']);
    Route::get('debtor/1102050101_309_indiv_excel',[DebtorController::class,'_1102050101_309_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_401',[DebtorController::class,'_1102050101_401']);
    Route::post('debtor/1102050101_401_confirm',[DebtorController::class,'_1102050101_401_confirm']);
    Route::delete('debtor/1102050101_401_delete',[DebtorController::class,'_1102050101_401_delete']);
    Route::put('debtor/1102050101_401/update/{vn}',[DebtorController::class,'_1102050101_401_update']);
    Route::get('debtor/1102050101_401_daily_pdf',[DebtorController::class,'_1102050101_401_daily_pdf']);
    Route::get('debtor/1102050101_401_indiv_excel',[DebtorController::class,'_1102050101_401_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_501',[DebtorController::class,'_1102050101_501']);
    Route::post('debtor/1102050101_501_confirm',[DebtorController::class,'_1102050101_501_confirm']);
    Route::delete('debtor/1102050101_501_delete',[DebtorController::class,'_1102050101_501_delete']);
    Route::put('debtor/1102050101_501/update/{vn}',[DebtorController::class,'_1102050101_501_update']);
    Route::get('debtor/1102050101_501_daily_pdf',[DebtorController::class,'_1102050101_501_daily_pdf']);
    Route::get('debtor/1102050101_501_indiv_excel',[DebtorController::class,'_1102050101_501_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_503',[DebtorController::class,'_1102050101_503']);
    Route::post('debtor/1102050101_503_confirm',[DebtorController::class,'_1102050101_503_confirm']);
    Route::delete('debtor/1102050101_503_delete',[DebtorController::class,'_1102050101_503_delete']);
    Route::put('debtor/1102050101_503/update/{vn}',[DebtorController::class,'_1102050101_503_update']);
    Route::get('debtor/1102050101_503_daily_pdf',[DebtorController::class,'_1102050101_503_daily_pdf']);
    Route::get('debtor/1102050101_503_indiv_excel',[DebtorController::class,'_1102050101_503_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_701',[DebtorController::class,'_1102050101_701']);
    Route::post('debtor/1102050101_701_confirm',[DebtorController::class,'_1102050101_701_confirm']);
    Route::delete('debtor/1102050101_701_delete',[DebtorController::class,'_1102050101_701_delete']);
    Route::get('debtor/1102050101_701_daily_pdf',[DebtorController::class,'_1102050101_701_daily_pdf']);
    Route::get('debtor/1102050101_701_indiv_excel',[DebtorController::class,'_1102050101_701_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_702',[DebtorController::class,'_1102050101_702']);
    Route::post('debtor/1102050101_702_confirm',[DebtorController::class,'_1102050101_702_confirm']);
    Route::delete('debtor/1102050101_702_delete',[DebtorController::class,'_1102050101_702_delete']);
    Route::get('debtor/1102050101_702_daily_pdf',[DebtorController::class,'_1102050101_702_daily_pdf']);
    Route::get('debtor/1102050101_702_indiv_excel',[DebtorController::class,'_1102050101_702_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050102_106',[DebtorController::class,'_1102050102_106']);
    Route::post('debtor/1102050102_106_confirm',[DebtorController::class,'_1102050102_106_confirm']);
    Route::post('debtor/1102050102_106_confirm_iclaim',[DebtorController::class,'_1102050102_106_confirm_iclaim']);
    Route::delete('debtor/1102050102_106_delete',[DebtorController::class,'_1102050102_106_delete']);
    Route::put('debtor/1102050102_106/update/{vn}',[DebtorController::class,'_1102050102_106_update']);
    Route::get('debtor/1102050102_106_daily_pdf',[DebtorController::class,'_1102050102_106_daily_pdf']);
    Route::get('debtor/1102050102_106_indiv_excel',[DebtorController::class,'_1102050102_106_indiv_excel']);
    Route::get('debtor/1102050102_106/tracking/{vn}',[DebtorController::class,'_1102050102_106_tracking']);
    Route::post('debtor/1102050102_106/tracking_insert',[DebtorController::class,'_1102050102_106_tracking_insert']);
    Route::put('debtor/1102050102_106/tracking_update/{tracking_id}',[DebtorController::class,'_1102050102_106_tracking_update']);
    Route::match(['get','post'],'debtor/1102050102_108',[DebtorController::class,'_1102050102_108']);
    Route::post('debtor/1102050102_108_confirm',[DebtorController::class,'_1102050102_108_confirm']);
    Route::delete('debtor/1102050102_108_delete',[DebtorController::class,'_1102050102_108_delete']);
    Route::put('debtor/1102050102_108/update/{vn}',[DebtorController::class,'_1102050102_108_update']);
    Route::get('debtor/1102050102_108_daily_pdf',[DebtorController::class,'_1102050102_108_daily_pdf']);
    Route::get('debtor/1102050102_108_indiv_excel',[DebtorController::class,'_1102050102_108_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050102_110',[DebtorController::class,'_1102050102_110']);
    Route::post('debtor/1102050102_110_confirm',[DebtorController::class,'_1102050102_110_confirm']);
    Route::delete('debtor/1102050102_110_delete',[DebtorController::class,'_1102050102_110_delete']);
    Route::put('debtor/1102050102_110/update/{vn}',[DebtorController::class,'_1102050102_110_update']);
    Route::get('debtor/1102050102_110_daily_pdf',[DebtorController::class,'_1102050102_110_daily_pdf']);
    Route::get('debtor/1102050102_110_indiv_excel',[DebtorController::class,'_1102050102_110_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050102_602',[DebtorController::class,'_1102050102_602']);
    Route::post('debtor/1102050102_602_confirm',[DebtorController::class,'_1102050102_602_confirm']);
    Route::delete('debtor/1102050102_602_delete',[DebtorController::class,'_1102050102_602_delete']);
    Route::put('debtor/1102050102_602/update/{vn}',[DebtorController::class,'_1102050102_602_update']);
    Route::get('debtor/1102050102_602_daily_pdf',[DebtorController::class,'_1102050102_602_daily_pdf']);
    Route::get('debtor/1102050102_602_indiv_excel',[DebtorController::class,'_1102050102_602_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050102_801',[DebtorController::class,'_1102050102_801']);
    Route::post('debtor/1102050102_801_confirm',[DebtorController::class,'_1102050102_801_confirm']);
    Route::delete('debtor/1102050102_801_delete',[DebtorController::class,'_1102050102_801_delete']);
    Route::put('debtor/1102050102_801/update/{vn}',[DebtorController::class,'_1102050102_801_update']);
    Route::get('debtor/1102050102_801_daily_pdf',[DebtorController::class,'_1102050102_801_daily_pdf']);
    Route::get('debtor/1102050102_801_indiv_excel',[DebtorController::class,'_1102050102_801_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050102_803',[DebtorController::class,'_1102050102_803']);
    Route::post('debtor/1102050102_803_confirm',[DebtorController::class,'_1102050102_803_confirm']);
    Route::delete('debtor/1102050102_803_delete',[DebtorController::class,'_1102050102_803_delete']);
    Route::put('debtor/1102050102_803/update/{vn}',[DebtorController::class,'_1102050102_803_update']);
    Route::get('debtor/1102050102_803_daily_pdf',[DebtorController::class,'_1102050102_803_daily_pdf']);
    Route::get('debtor/1102050102_803_indiv_excel',[DebtorController::class,'_1102050102_803_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_202',[DebtorController::class,'_1102050101_202']);
    Route::post('debtor/1102050101_202_confirm',[DebtorController::class,'_1102050101_202_confirm']);
    Route::delete('debtor/1102050101_202_delete',[DebtorController::class,'_1102050101_202_delete']);
    Route::get('debtor/1102050101_202_daily_pdf',[DebtorController::class,'_1102050101_202_daily_pdf']);
    Route::get('debtor/1102050101_202_indiv_excel',[DebtorController::class,'_1102050101_202_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_217',[DebtorController::class,'_1102050101_217']);
    Route::post('debtor/1102050101_217_confirm',[DebtorController::class,'_1102050101_217_confirm']);
    Route::delete('debtor/1102050101_217_delete',[DebtorController::class,'_1102050101_217_delete']);
    Route::get('debtor/1102050101_217_daily_pdf',[DebtorController::class,'_1102050101_217_daily_pdf']);
    Route::get('debtor/1102050101_217_indiv_excel',[DebtorController::class,'_1102050101_217_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_302',[DebtorController::class,'_1102050101_302']);
    Route::post('debtor/1102050101_302_confirm',[DebtorController::class,'_1102050101_302_confirm']);
    Route::delete('debtor/1102050101_302_delete',[DebtorController::class,'_1102050101_302_delete']);
    Route::put('debtor/1102050101_302/update/{an}',[DebtorController::class,'_1102050101_302_update']);
    Route::get('debtor/1102050101_302_daily_pdf',[DebtorController::class,'_1102050101_302_daily_pdf']);
    Route::get('debtor/1102050101_302_indiv_excel',[DebtorController::class,'_1102050101_302_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_304',[DebtorController::class,'_1102050101_304']);
    Route::post('debtor/1102050101_304_confirm',[DebtorController::class,'_1102050101_304_confirm']);
    Route::delete('debtor/1102050101_304_delete',[DebtorController::class,'_1102050101_304_delete']);
    Route::put('debtor/1102050101_304/update/{an}',[DebtorController::class,'_1102050101_304_update']);
    Route::get('debtor/1102050101_304_daily_pdf',[DebtorController::class,'_1102050101_304_daily_pdf']);
    Route::get('debtor/1102050101_304_indiv_excel',[DebtorController::class,'_1102050101_304_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_308',[DebtorController::class,'_1102050101_308']);
    Route::post('debtor/1102050101_308_confirm',[DebtorController::class,'_1102050101_308_confirm']);
    Route::delete('debtor/1102050101_308_delete',[DebtorController::class,'_1102050101_308_delete']);
    Route::put('debtor/1102050101_308/update/{an}',[DebtorController::class,'_1102050101_308_update']);
    Route::get('debtor/1102050101_308_daily_pdf',[DebtorController::class,'_1102050101_308_daily_pdf']);
    Route::get('debtor/1102050101_308_indiv_excel',[DebtorController::class,'_1102050101_308_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_310',[DebtorController::class,'_1102050101_310']);
    Route::post('debtor/1102050101_310_confirm',[DebtorController::class,'_1102050101_310_confirm']);
    Route::delete('debtor/1102050101_310_delete',[DebtorController::class,'_1102050101_310_delete']);
    Route::put('debtor/1102050101_310/update/{an}',[DebtorController::class,'_1102050101_310_update']);
    Route::get('debtor/1102050101_310_daily_pdf',[DebtorController::class,'_1102050101_310_daily_pdf']);
    Route::get('debtor/1102050101_310_indiv_excel',[DebtorController::class,'_1102050101_310_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_402',[DebtorController::class,'_1102050101_402']);
    Route::post('debtor/1102050101_402_confirm',[DebtorController::class,'_1102050101_402_confirm']);
    Route::delete('debtor/1102050101_402_delete',[DebtorController::class,'_1102050101_402_delete']);
    Route::get('debtor/1102050101_402_daily_pdf',[DebtorController::class,'_1102050101_402_daily_pdf']);
    Route::get('debtor/1102050101_402_indiv_excel',[DebtorController::class,'_1102050101_402_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_502',[DebtorController::class,'_1102050101_502']);
    Route::post('debtor/1102050101_502_confirm',[DebtorController::class,'_1102050101_502_confirm']);
    Route::delete('debtor/1102050101_502_delete',[DebtorController::class,'_1102050101_502_delete']);
    Route::put('debtor/1102050101_502/update/{an}',[DebtorController::class,'_1102050101_502_update']);
    Route::get('debtor/1102050101_502_daily_pdf',[DebtorController::class,'_1102050101_502_daily_pdf']);
    Route::get('debtor/1102050101_502_indiv_excel',[DebtorController::class,'_1102050101_502_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_504',[DebtorController::class,'_1102050101_504']);
    Route::post('debtor/1102050101_504_confirm',[DebtorController::class,'_1102050101_504_confirm']);
    Route::delete('debtor/1102050101_504_delete',[DebtorController::class,'_1102050101_504_delete']);
    Route::put('debtor/1102050101_504/update/{an}',[DebtorController::class,'_1102050101_504_update']);
    Route::get('debtor/1102050101_504_daily_pdf',[DebtorController::class,'_1102050101_504_daily_pdf']);
    Route::get('debtor/1102050101_504_indiv_excel',[DebtorController::class,'_1102050101_504_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050101_704',[DebtorController::class,'_1102050101_704']);
    Route::post('debtor/1102050101_704_confirm',[DebtorController::class,'_1102050101_704_confirm']);
    Route::delete('debtor/1102050101_704_delete',[DebtorController::class,'_1102050101_704_delete']);
    Route::put('debtor/1102050101_704/update/{an}',[DebtorController::class,'_1102050101_704_update']);
    Route::get('debtor/1102050101_704_daily_pdf',[DebtorController::class,'_1102050101_704_daily_pdf']);
    Route::get('debtor/1102050101_704_indiv_excel',[DebtorController::class,'_1102050101_704_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050102_107',[DebtorController::class,'_1102050102_107']);
    Route::post('debtor/1102050102_107_confirm',[DebtorController::class,'_1102050102_107_confirm']);
    Route::post('debtor/1102050102_107_confirm_iclaim',[DebtorController::class,'_1102050102_107_confirm_iclaim']);
    Route::delete('debtor/1102050102_107_delete',[DebtorController::class,'_1102050102_107_delete']);
    Route::put('debtor/1102050102_107/update/{an}',[DebtorController::class,'_1102050102_107_update']);
    Route::get('debtor/1102050102_107/tracking/{an}',[DebtorController::class,'_1102050102_107_tracking']);
    Route::post('debtor/1102050102_107/tracking_insert',[DebtorController::class,'_1102050102_107_tracking_insert']);
    Route::put('debtor/1102050102_107/tracking_update/{tracking_id}',[DebtorController::class,'_1102050102_107_tracking_update']);
    Route::get('debtor/1102050102_107_daily_pdf',[DebtorController::class,'_1102050102_107_daily_pdf']);
    Route::get('debtor/1102050102_107_indiv_excel',[DebtorController::class,'_1102050102_107_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050102_109',[DebtorController::class,'_1102050102_109']);
    Route::post('debtor/1102050102_109_confirm',[DebtorController::class,'_1102050102_109_confirm']);
    Route::delete('debtor/1102050102_109_delete',[DebtorController::class,'_1102050102_109_delete']);
    Route::put('debtor/1102050102_109/update/{an}',[DebtorController::class,'_1102050102_109_update']);
    Route::get('debtor/1102050102_109_daily_pdf',[DebtorController::class,'_1102050102_109_daily_pdf']);
    Route::get('debtor/1102050102_109_indiv_excel',[DebtorController::class,'_1102050102_109_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050102_111',[DebtorController::class,'_1102050102_111']);
    Route::post('debtor/1102050102_111_confirm',[DebtorController::class,'_1102050102_111_confirm']);
    Route::delete('debtor/1102050102_111_delete',[DebtorController::class,'_1102050102_111_delete']);
    Route::get('debtor/1102050102_111_daily_pdf',[DebtorController::class,'_1102050102_111_daily_pdf']);
    Route::get('debtor/1102050102_111_indiv_excel',[DebtorController::class,'_1102050102_111_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050102_603',[DebtorController::class,'_1102050102_603']);
    Route::post('debtor/1102050102_603_confirm',[DebtorController::class,'_1102050102_603_confirm']);
    Route::delete('debtor/1102050102_603_delete',[DebtorController::class,'_1102050102_603_delete']);
    Route::put('debtor/1102050102_603/update/{an}',[DebtorController::class,'_1102050102_603_update']);
    Route::get('debtor/1102050102_603_daily_pdf',[DebtorController::class,'_1102050102_603_daily_pdf']);
    Route::get('debtor/1102050102_603_indiv_excel',[DebtorController::class,'_1102050102_603_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050102_802',[DebtorController::class,'_1102050102_802']);
    Route::post('debtor/1102050102_802_confirm',[DebtorController::class,'_1102050102_802_confirm']);
    Route::delete('debtor/1102050102_802_delete',[DebtorController::class,'_1102050102_802_delete']);
    Route::get('debtor/1102050102_802_daily_pdf',[DebtorController::class,'_1102050102_802_daily_pdf']);
    Route::get('debtor/1102050102_802_indiv_excel',[DebtorController::class,'_1102050102_802_indiv_excel']);
    Route::match(['get','post'],'debtor/1102050102_804',[DebtorController::class,'_1102050102_804']);
    Route::post('debtor/1102050102_804_confirm',[DebtorController::class,'_1102050102_804_confirm']);
    Route::delete('debtor/1102050102_804_delete',[DebtorController::class,'_1102050102_804_delete']);
    Route::get('debtor/1102050102_804_daily_pdf',[DebtorController::class,'_1102050102_804_daily_pdf']);
    Route::get('debtor/1102050102_804_indiv_excel',[DebtorController::class,'_1102050102_804_indiv_excel']);
});

//HN-Plus ################################################################################################################################
    // ✅ กลุ่มที่ต้องล็อกอิน
    Route::prefix('hnplus')->middleware(['auth', 'hnplus'])->name('hnplus.')->group(function () {
        Route::get('/', [HnplusController::class, 'index'])->name('dashboard');
        Route::match(['get','post'],'inspection/report', [HnplusController::class, 'inspection_report']);
        Route::match(['get','post'],'product/er_report', [ProductERController::class, 'er_report'])->name('product.er_report');       
        Route::delete('product/er_product_delete/{id}', [ProductERController::class, 'er_product_delete']);
        Route::match(['get','post'],'product/ipd_report', [ProductIPDController::class, 'ipd_report'])->name('product.ipd_report');       
        Route::delete('product/ipd_product_delete/{id}', [ProductIPDController::class, 'ipd_product_delete']);
        Route::match(['get','post'],'product/vip_report', [ProductVIPController::class, 'vip_report'])->name('product.vip_report');       
        Route::delete('product/vip_product_delete/{id}', [ProductVIPController::class, 'vip_product_delete']);
        Route::match(['get','post'],'product/lr_report', [ProductLRController::class, 'lr_report'])->name('product.lr_report');       
        Route::delete('product/lr_product_delete/{id}', [ProductLRController::class, 'lr_product_delete']);
        Route::match(['get','post'],'product/opd_report', [ProductOPDController::class, 'opd_report'])->name('product.opd_report');       
        Route::delete('product/opd_product_delete/{id}', [ProductOPDController::class, 'opd_product_delete']);
        Route::match(['get','post'],'product/ncd_report', [ProductNCDController::class, 'ncd_report'])->name('product.ncd_report');       
        Route::delete('product/ncd_product_delete/{id}', [ProductNCDController::class, 'ncd_product_delete']);
        Route::match(['get','post'],'product/ckd_report', [ProductCKDController::class, 'ckd_report'])->name('product.ckd_report');       
        Route::delete('product/ckd_product_delete/{id}', [ProductCKDController::class, 'ckd_product_delete']);
        Route::match(['get','post'],'product/hd_report', [ProductHDController::class, 'hd_report'])->name('product.hd_report');       
        Route::delete('product/hd_product_delete/{id}', [ProductHDController::class, 'hd_product_delete']);
        Route::match(['get','post'],'product/or_report', [ProductORController::class, 'or_report'])->name('product.or_report');       
        Route::delete('product/or_product_delete/{id}', [ProductORController::class, 'or_product_delete']);
    });

    // ✅ กลุ่มที่ไม่ต้องล็อกอิน (public)
    Route::prefix('hnplus')->name('hnplus.')->group(function () {
        Route::get('inspection/create/{depart}', [HnplusController::class, 'inspection_create'])->name('inspection_create');
        Route::post('inspection/save', [HnplusController::class, 'inspection_save'])->name('inspection_save');
        //product ER-----------------------------------------------------------------------------------------------------------
        Route::get('product/er_night_notify',[ProductERController::class,'er_night_notify']);
        Route::get('product/er_night',[ProductERController::class,'er_night']);
        Route::post('product/er_night_save',[ProductERController::class,'er_night_save']);
        Route::get('product/er_morning_notify',[ProductERController::class,'er_morning_notify']);
        Route::get('product/er_morning',[ProductERController::class,'er_morning']);
        Route::post('product/er_morning_save',[ProductERController::class,'er_morning_save']);
        Route::get('product/er_afternoon_notify',[ProductERController::class,'er_afternoon_notify']);
        Route::get('product/er_afternoon',[ProductERController::class,'er_afternoon']);
        Route::post('product/er_afternoon_save',[ProductERController::class,'er_afternoon_save']);
        //product ipd-----------------------------------------------------------------------------------------------------------
        Route::get('product/ipd_night_notify',[ProductIPDController::class,'ipd_night_notify']);
        Route::get('product/ipd_night',[ProductIPDController::class,'ipd_night']);
        Route::post('product/ipd_night_save',[ProductIPDController::class,'ipd_night_save']);
        Route::get('product/ipd_morning_notify',[ProductIPDController::class,'ipd_morning_notify']);
        Route::get('product/ipd_morning',[ProductIPDController::class,'ipd_morning']);
        Route::post('product/ipd_morning_save',[ProductIPDController::class,'ipd_morning_save']);
        Route::get('product/ipd_afternoon_notify',[ProductIPDController::class,'ipd_afternoon_notify']);
        Route::get('product/ipd_afternoon',[ProductIPDController::class,'ipd_afternoon']);
        Route::post('product/ipd_afternoon_save',[ProductIPDController::class,'ipd_afternoon_save']);
        //product vip-----------------------------------------------------------------------------------------------------------
        Route::get('product/vip_night_notify',[ProductVIPController::class,'vip_night_notify']);
        Route::get('product/vip_night',[ProductVIPController::class,'vip_night']);
        Route::post('product/vip_night_save',[ProductVIPController::class,'vip_night_save']);
        Route::get('product/vip_morning_notify',[ProductVIPController::class,'vip_morning_notify']);
        Route::get('product/vip_morning',[ProductVIPController::class,'vip_morning']);
        Route::post('product/vip_morning_save',[ProductVIPController::class,'vip_morning_save']);
        Route::get('product/vip_afternoon_notify',[ProductVIPController::class,'vip_afternoon_notify']);
        Route::get('product/vip_afternoon',[ProductVIPController::class,'vip_afternoon']);
        Route::post('product/vip_afternoon_save',[ProductVIPController::class,'vip_afternoon_save']);
        //product lr-----------------------------------------------------------------------------------------------------------
        Route::get('product/lr_night_notify',[ProductLRController::class,'lr_night_notify']);
        Route::get('product/lr_night',[ProductLRController::class,'lr_night']);
        Route::post('product/lr_night_save',[ProductLRController::class,'lr_night_save']);
        Route::get('product/lr_morning_notify',[ProductLRController::class,'lr_morning_notify']);
        Route::get('product/lr_morning',[ProductLRController::class,'lr_morning']);
        Route::post('product/lr_morning_save',[ProductLRController::class,'lr_morning_save']);
        Route::get('product/lr_afternoon_notify',[ProductLRController::class,'lr_afternoon_notify']);
        Route::get('product/lr_afternoon',[ProductLRController::class,'lr_afternoon']);
        Route::post('product/lr_afternoon_save',[ProductLRController::class,'lr_afternoon_save']);
        //product OPD-----------------------------------------------------------------------------------------------------------
        Route::get('product/opd_morning_notify',[ProductOPDController::class,'opd_morning_notify']);
        Route::get('product/opd_morning',[ProductOPDController::class,'opd_morning']);
        Route::post('product/opd_morning_save',[ProductOPDController::class,'opd_morning_save']);
        Route::get('product/opd_bd_notify',[ProductOPDController::class,'opd_bd_notify']);
        Route::get('product/opd_bd',[ProductOPDController::class,'opd_bd']);
        Route::post('product/opd_bd_save',[ProductOPDController::class,'opd_bd_save']);
        //product NCD-----------------------------------------------------------------------------------------------------------
        Route::get('product/ncd_morning_notify',[ProductNCDController::class,'ncd_morning_notify']);
        Route::get('product/ncd_morning',[ProductNCDController::class,'ncd_morning']);
        Route::post('product/ncd_morning_save',[ProductNCDController::class,'ncd_morning_save']);
        //product CKD-----------------------------------------------------------------------------------------------------------
        Route::get('product/ckd_morning_notify',[ProductCKDController::class,'ckd_morning_notify']);
        Route::get('product/ckd_morning',[ProductCKDController::class,'ckd_morning']);
        Route::post('product/ckd_morning_save',[ProductCKDController::class,'ckd_morning_save']);
        //product HD-----------------------------------------------------------------------------------------------------------
        Route::get('product/hd_morning_notify',[ProductHDController::class,'hd_morning_notify']);
        Route::get('product/hd_morning',[ProductHDController::class,'hd_morning']);
        Route::post('product/hd_morning_save',[ProductHDController::class,'hd_morning_save']);
        //product OR-----------------------------------------------------------------------------------------------------------
        Route::get('product/or_morning_notify',[ProductORController::class,'or_morning_notify']);
        Route::get('product/or_morning',[ProductORController::class,'or_morning']);
        Route::post('product/or_morning_save',[ProductORController::class,'or_morning_save']);
    });