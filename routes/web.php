<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MainSettingController;
use App\Http\Controllers\Admin\LookupIcodeController;
use App\Http\Controllers\Admin\LookupWardController;
use App\Http\Controllers\Admin\LookupHospcodeController;
use App\Http\Controllers\Admin\User_AccessController;
use App\Http\Controllers\Hrims\HrimsController;
use App\Http\Controllers\Hrims\ImportStmController;
use App\Http\Controllers\Hrims\ClaimIpController;
use App\Http\Controllers\Hrims\ClaimOpController;
use App\Http\Controllers\Hrims\DebtorController;
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
use App\Http\Controllers\LineNotify_InsuranceController;
use App\Http\Controllers\LineNotifyController;
use App\Http\Controllers\Medicalrecord_OpdController;
use App\Http\Controllers\Medicalrecord_IpdController;
use App\Http\Controllers\Service_DeathController;
use App\Http\Controllers\Service_DentController;
use App\Http\Controllers\Service_DiagController;
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
    Route::match(['get','post'],'backoffice_hrd/nurse_productivity_er',[Backoffice_HrdController::class,'nurse_productivity_er'])->name('nurse_productivity_er');
    Route::get('backoffice_hrd/nurse_productivity_er_delete/{id}',[Backoffice_HrdController::class,'nurse_productivity_er_delete']);
    Route::get('backoffice_hrd/nurse_productivity_er_night',[Backoffice_HrdController::class,'nurse_productivity_er_night'])->name('nurse_productivity_er_night');
    Route::post('backoffice_hrd/nurse_productivity_er_night_save',[Backoffice_HrdController::class,'nurse_productivity_er_night_save'])->name('nurse_productivity_er_night_save');
    Route::get('backoffice_hrd/nurse_productivity_er_morning',[Backoffice_HrdController::class,'nurse_productivity_er_morning'])->name('nurse_productivity_er_morning');
    Route::post('backoffice_hrd/nurse_productivity_er_morning_save',[Backoffice_HrdController::class,'nurse_productivity_er_morning_save'])->name('nurse_productivity_er_morning_save');
    Route::get('backoffice_hrd/nurse_productivity_er_afternoon',[Backoffice_HrdController::class,'nurse_productivity_er_afternoon'])->name('nurse_productivity_er_afternoon');
    Route::post('backoffice_hrd/nurse_productivity_er_afternoon_save',[Backoffice_HrdController::class,'nurse_productivity_er_afternoon_save'])->name('nurse_productivity_er_afternoon_save');
    Route::match(['get','post'],'backoffice_hrd/nurse_productivity_ipd',[Backoffice_HrdController::class,'nurse_productivity_ipd'])->name('nurse_productivity_ipd');
    Route::get('backoffice_hrd/nurse_productivity_ipd_delete/{id}',[Backoffice_HrdController::class,'nurse_productivity_ipd_delete']);
    Route::get('backoffice_hrd/nurse_productivity_ipd_night',[Backoffice_HrdController::class,'nurse_productivity_ipd_night'])->name('nurse_productivity_ipd_night');
    Route::post('backoffice_hrd/nurse_productivity_ipd_night_save',[Backoffice_HrdController::class,'nurse_productivity_ipd_night_save'])->name('nurse_productivity_ipd_night_save');
    Route::get('backoffice_hrd/nurse_productivity_ipd_morning',[Backoffice_HrdController::class,'nurse_productivity_ipd_morning'])->name('nurse_productivity_ipd_morning');
    Route::post('backoffice_hrd/nurse_productivity_ipd_morning_save',[Backoffice_HrdController::class,'nurse_productivity_ipd_morning_save'])->name('nurse_productivity_ipd_morning_save');
    Route::get('backoffice_hrd/nurse_productivity_ipd_afternoon',[Backoffice_HrdController::class,'nurse_productivity_ipd_afternoon'])->name('nurse_productivity_ipd_afternoon');
    Route::post('backoffice_hrd/nurse_productivity_ipd_afternoon_save',[Backoffice_HrdController::class,'nurse_productivity_ipd_afternoon_save'])->name('nurse_productivity_ipd_afternoon_save');
    Route::match(['get','post'],'backoffice_hrd/nurse_productivity_vip',[Backoffice_HrdController::class,'nurse_productivity_vip'])->name('nurse_productivity_vip');
    Route::get('backoffice_hrd/nurse_productivity_vip_delete/{id}',[Backoffice_HrdController::class,'nurse_productivity_vip_delete']);
    Route::get('backoffice_hrd/nurse_productivity_vip_night',[Backoffice_HrdController::class,'nurse_productivity_vip_night'])->name('nurse_productivity_vip_night');
    Route::post('backoffice_hrd/nurse_productivity_vip_night_save',[Backoffice_HrdController::class,'nurse_productivity_vip_night_save'])->name('nurse_productivity_vip_night_save');
    Route::get('backoffice_hrd/nurse_productivity_vip_morning',[Backoffice_HrdController::class,'nurse_productivity_vip_morning'])->name('nurse_productivity_vip_morning');
    Route::post('backoffice_hrd/nurse_productivity_vip_morning_save',[Backoffice_HrdController::class,'nurse_productivity_vip_morning_save'])->name('nurse_productivity_vip_morning_save');
    Route::get('backoffice_hrd/nurse_productivity_vip_afternoon',[Backoffice_HrdController::class,'nurse_productivity_vip_afternoon'])->name('nurse_productivity_vip_afternoon');
    Route::post('backoffice_hrd/nurse_productivity_vip_afternoon_save',[Backoffice_HrdController::class,'nurse_productivity_vip_afternoon_save'])->name('nurse_productivity_vip_afternoon_save');
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
    Route::match(['get','post'],'dashboard/nhso_endpoint_pull_daily',[Dashboard_DigitalhealthController::class,'nhso_endpoint_pull_daily']);
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

// Finance_claim ----------------------------------------------------------------------------------------------------------------------------------
    Route::get('finance_claim/',[Finance_ClaimController::class,'index']);
    Route::match(['get','post'],'finance_claim/ofc_claim_opd',[Finance_ClaimController::class,'ofc_claim_opd']);
    Route::match(['get','post'],'finance_claim/ofc_claim_ipd',[Finance_ClaimController::class,'ofc_claim_ipd']);
    Route::match(['get','post'],'finance_claim/bkk_claim_opd',[Finance_ClaimController::class,'bkk_claim_opd']);
    Route::match(['get','post'],'finance_claim/bkk_claim_ipd',[Finance_ClaimController::class,'bkk_claim_ipd']);
    Route::match(['get','post'],'finance_claim/bmt_claim_opd',[Finance_ClaimController::class,'bmt_claim_opd']);
    Route::match(['get','post'],'finance_claim/bmt_claim_ipd',[Finance_ClaimController::class,'bmt_claim_ipd']);
    Route::match(['get','post'],'finance_claim/lgo_claim_opd',[Finance_ClaimController::class,'lgo_claim_opd']);
    Route::match(['get','post'],'finance_claim/lgo_claim_ipd',[Finance_ClaimController::class,'lgo_claim_ipd']);
    Route::match(['get','post'],'finance_claim/sss_claim_kidney',[Finance_ClaimController::class,'sss_claim_kidney']);
    Route::match(['get','post'],'finance_claim/ucs_claim_ipd',[Finance_ClaimController::class,'ucs_claim_ipd']);
    Route::match(['get','post'],'finance_claim/ucs_claim_walkin',[Finance_ClaimController::class,'ucs_claim_walkin']);
    Route::match(['get','post'],'finance_claim/ucs_claim_kidney',[Finance_ClaimController::class,'ucs_claim_kidney']);
    Route::match(['get','post'],'finance_claim/ucs_claim_opanywhere',[Finance_ClaimController::class,'ucs_claim_opanywhere']);
    Route::match(['get','post'],'finance_claim/ucs_claim_instrument',[Finance_ClaimController::class,'ucs_claim_instrument']);
    Route::match(['get','post'],'finance_claim/ucs_claim_telehealth',[Finance_ClaimController::class,'ucs_claim_telehealth']);
    Route::match(['get','post'],'finance_claim/ucs_claim_rider',[Finance_ClaimController::class,'ucs_claim_rider']);
    Route::match(['get','post'],'finance_claim/ucs_claim_palliative',[Finance_ClaimController::class,'ucs_claim_palliative']);
    Route::match(['get','post'],'finance_claim/ucs_claim_t1dm_gdm_pdm',[Finance_ClaimController::class,'ucs_claim_t1dm_gdm_pdm']);
    Route::match(['get','post'],'finance_claim/ucs_claim_drug_herb',[Finance_ClaimController::class,'ucs_claim_drug_herb']);
    Route::match(['get','post'],'finance_claim/ucs_claim_drug_herb32',[Finance_ClaimController::class,'ucs_claim_drug_herb32']);
    Route::match(['get','post'],'finance_claim/ucs_claim_drug_morphine',[Finance_ClaimController::class,'ucs_claim_drug_morphine']);
    Route::match(['get','post'],'finance_claim/ucs_claim_drug_clopidogrel',[Finance_ClaimController::class,'ucs_claim_drug_clopidogrel']);
    Route::match(['get','post'],'finance_claim/ucs_claim_drug_sk',[Finance_ClaimController::class,'ucs_claim_drug_sk']);
    Route::match(['get','post'],'finance_claim/ucs_ppfs_2',[Finance_ClaimController::class,'ucs_ppfs_2']);
    Route::match(['get','post'],'finance_claim/ucs_ppfs_7',[Finance_ClaimController::class,'ucs_ppfs_7']);
    Route::match(['get','post'],'finance_claim/ucs_ppfs_8',[Finance_ClaimController::class,'ucs_ppfs_8']);
    Route::match(['get','post'],'finance_claim/ucs_ppfs_9',[Finance_ClaimController::class,'ucs_ppfs_9']);
    Route::match(['get','post'],'finance_claim/ucs_ppfs_14',[Finance_ClaimController::class,'ucs_ppfs_14']);
    Route::get('finance_claim/ucs_ppfs_14_excel',[Finance_ClaimController::class,'ucs_ppfs_14_excel']);
    Route::match(['get','post'],'finance_claim/ucs_ppfs_17',[Finance_ClaimController::class,'ucs_ppfs_17']);
    Route::match(['get','post'],'finance_claim/ucs_ppfs_18',[Finance_ClaimController::class,'ucs_ppfs_18']);
    Route::match(['get','post'],'finance_claim/ucs_ppfs_20',[Finance_ClaimController::class,'ucs_ppfs_20']);
    Route::match(['get','post'],'finance_claim/ucs_ppfs_21',[Finance_ClaimController::class,'ucs_ppfs_21']);
    Route::match(['get','post'],'finance_claim/stp_claim_opd',[Finance_ClaimController::class,'stp_claim_opd']);
    Route::match(['get','post'],'finance_claim/stp_claim_ipd',[Finance_ClaimController::class,'stp_claim_ipd']);
    
// Finance_debtor ---------------------------------------------------------------------------------------------------------------------------------
    Route::get('finance_debtor/',[Finance_DebtorController::class,'index']);
    Route::match(['get','post'],'finance_debtor/check_income',[Finance_DebtorController::class,'_check_income']);
    Route::match(['get','post'],'finance_debtor/summary',[Finance_DebtorController::class,'_summary']);
    Route::get('finance_debtor/summary_pdf',[Finance_DebtorController::class,'_summary_pdf']);
    Route::match(['get','post'],'finance_debtor/1102050101_103',[Finance_DebtorController::class,'_1102050101_103'])->name('_1102050101_103');
    Route::post('finance_debtor/1102050101_103_confirm',[Finance_DebtorController::class,'_1102050101_103_confirm']);
    Route::delete('finance_debtor/1102050101_103_delete',[Finance_DebtorController::class,'_1102050101_103_delete']);
    Route::put('finance_debtor/1102050101_103/update/{vn}',[Finance_DebtorController::class,'_1102050101_103_update']);
    Route::get('finance_debtor/1102050101_103_daily_pdf',[Finance_DebtorController::class,'_1102050101_103_daily_pdf']);
    Route::get('finance_debtor/1102050101_103_indiv_excel',[Finance_DebtorController::class,'_1102050101_103_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_109',[Finance_DebtorController::class,'_1102050101_109'])->name('_1102050101_109');
    Route::post('finance_debtor/1102050101_109_confirm',[Finance_DebtorController::class,'_1102050101_109_confirm']);
    Route::delete('finance_debtor/1102050101_109_delete',[Finance_DebtorController::class,'_1102050101_109_delete']);
    Route::put('finance_debtor/1102050101_109/update/{vn}',[Finance_DebtorController::class,'_1102050101_109_update']);
    Route::get('finance_debtor/1102050101_109_daily_pdf',[Finance_DebtorController::class,'_1102050101_109_daily_pdf']);
    Route::get('finance_debtor/1102050101_109_indiv_excel',[Finance_DebtorController::class,'_1102050101_109_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_201',[Finance_DebtorController::class,'_1102050101_201'])->name('_1102050101_201');
    Route::post('finance_debtor/1102050101_201_confirm',[Finance_DebtorController::class,'_1102050101_201_confirm']);
    Route::delete('finance_debtor/1102050101_201_delete',[Finance_DebtorController::class,'_1102050101_201_delete']);
    Route::get('finance_debtor/1102050101_201_daily_pdf',[Finance_DebtorController::class,'_1102050101_201_daily_pdf']);
    Route::get('finance_debtor/1102050101_201_indiv_excel',[Finance_DebtorController::class,'_1102050101_201_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_203',[Finance_DebtorController::class,'_1102050101_203'])->name('_1102050101_203');
    Route::post('finance_debtor/1102050101_203_confirm',[Finance_DebtorController::class,'_1102050101_203_confirm']);
    Route::delete('finance_debtor/1102050101_203_delete',[Finance_DebtorController::class,'_1102050101_203_delete']);
    Route::put('finance_debtor/1102050101_203/update/{vn}',[Finance_DebtorController::class,'_1102050101_203_update']);
    Route::get('finance_debtor/1102050101_203_daily_pdf',[Finance_DebtorController::class,'_1102050101_203_daily_pdf']);
    Route::get('finance_debtor/1102050101_203_indiv_excel',[Finance_DebtorController::class,'_1102050101_203_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_209',[Finance_DebtorController::class,'_1102050101_209'])->name('_1102050101_209');
    Route::post('finance_debtor/1102050101_209_confirm',[Finance_DebtorController::class,'_1102050101_209_confirm']);
    Route::post('finance_debtor/1102050101_209_confirm_nonpp',[Finance_DebtorController::class,'_1102050101_209_confirm_nonpp']);
    Route::delete('finance_debtor/1102050101_209_delete',[Finance_DebtorController::class,'_1102050101_209_delete']);
    Route::get('finance_debtor/1102050101_209_daily_pdf',[Finance_DebtorController::class,'_1102050101_209_daily_pdf']);
    Route::get('finance_debtor/1102050101_209_indiv_excel',[Finance_DebtorController::class,'_1102050101_209_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_216',[Finance_DebtorController::class,'_1102050101_216'])->name('_1102050101_216');
    Route::post('finance_debtor/1102050101_216_confirm_kidney',[Finance_DebtorController::class,'_1102050101_216_confirm_kidney']);
    Route::post('finance_debtor/1102050101_216_confirm_cr',[Finance_DebtorController::class,'_1102050101_216_confirm_cr']);
    Route::post('finance_debtor/1102050101_216_confirm_anywhere',[Finance_DebtorController::class,'_1102050101_216_confirm_anywhere']);
    Route::delete('finance_debtor/1102050101_216_delete',[Finance_DebtorController::class,'_1102050101_216_delete']);
    Route::get('finance_debtor/1102050101_216_daily_pdf',[Finance_DebtorController::class,'_1102050101_216_daily_pdf']);
    Route::get('finance_debtor/1102050101_216_indiv_excel',[Finance_DebtorController::class,'_1102050101_216_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_301',[Finance_DebtorController::class,'_1102050101_301'])->name('_1102050101_301');
    Route::post('finance_debtor/1102050101_301_confirm',[Finance_DebtorController::class,'_1102050101_301_confirm']);
    Route::delete('finance_debtor/1102050101_301_delete',[Finance_DebtorController::class,'_1102050101_301_delete']);
    Route::get('finance_debtor/1102050101_301_daily_pdf',[Finance_DebtorController::class,'_1102050101_301_daily_pdf']);
    Route::get('finance_debtor/1102050101_301_indiv_excel',[Finance_DebtorController::class,'_1102050101_301_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_303',[Finance_DebtorController::class,'_1102050101_303'])->name('_1102050101_303');
    Route::post('finance_debtor/1102050101_303_confirm',[Finance_DebtorController::class,'_1102050101_303_confirm']);
    Route::delete('finance_debtor/1102050101_303_delete',[Finance_DebtorController::class,'_1102050101_303_delete']);
    Route::put('finance_debtor/1102050101_303/update/{vn}',[Finance_DebtorController::class,'_1102050101_303_update']);
    Route::get('finance_debtor/1102050101_303_daily_pdf',[Finance_DebtorController::class,'_1102050101_303_daily_pdf']);
    Route::get('finance_debtor/1102050101_303_indiv_excel',[Finance_DebtorController::class,'_1102050101_303_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_307',[Finance_DebtorController::class,'_1102050101_307'])->name('_1102050101_307');
    Route::post('finance_debtor/1102050101_307_confirm',[Finance_DebtorController::class,'_1102050101_307_confirm']);
    Route::post('finance_debtor/1102050101_307_confirm_ip',[Finance_DebtorController::class,'_1102050101_307_confirm_ip']);
    Route::delete('finance_debtor/1102050101_307_delete',[Finance_DebtorController::class,'_1102050101_307_delete']);
    Route::put('finance_debtor/1102050101_307/update/{vn}',[Finance_DebtorController::class,'_1102050101_307_update']);
    Route::get('finance_debtor/1102050101_307_daily_pdf',[Finance_DebtorController::class,'_1102050101_307_daily_pdf']);
    Route::get('finance_debtor/1102050101_307_indiv_excel',[Finance_DebtorController::class,'_1102050101_307_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_309',[Finance_DebtorController::class,'_1102050101_309'])->name('_1102050101_309');
    Route::post('finance_debtor/1102050101_309_confirm',[Finance_DebtorController::class,'_1102050101_309_confirm']);
    Route::delete('finance_debtor/1102050101_309_delete',[Finance_DebtorController::class,'_1102050101_309_delete']);
    Route::get('finance_debtor/1102050101_309_daily_pdf',[Finance_DebtorController::class,'_1102050101_309_daily_pdf']);
    Route::get('finance_debtor/1102050101_309_indiv_excel',[Finance_DebtorController::class,'_1102050101_309_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_401',[Finance_DebtorController::class,'_1102050101_401'])->name('_1102050101_401');
    Route::post('finance_debtor/1102050101_401_confirm',[Finance_DebtorController::class,'_1102050101_401_confirm']);
    Route::post('finance_debtor/1102050101_401_confirm_pp',[Finance_DebtorController::class,'_1102050101_401_confirm_pp']);
    Route::delete('finance_debtor/1102050101_401_delete',[Finance_DebtorController::class,'_1102050101_401_delete']);
    Route::put('finance_debtor/1102050101_401/update/{vn}',[Finance_DebtorController::class,'_1102050101_401_update']);
    Route::get('finance_debtor/1102050101_401_daily_pdf',[Finance_DebtorController::class,'_1102050101_401_daily_pdf']);
    Route::get('finance_debtor/1102050101_401_indiv_excel',[Finance_DebtorController::class,'_1102050101_401_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_501',[Finance_DebtorController::class,'_1102050101_501'])->name('_1102050101_501');
    Route::post('finance_debtor/1102050101_501_confirm',[Finance_DebtorController::class,'_1102050101_501_confirm']);
    Route::delete('finance_debtor/1102050101_501_delete',[Finance_DebtorController::class,'_1102050101_501_delete']);
    Route::put('finance_debtor/1102050101_501/update/{vn}',[Finance_DebtorController::class,'_1102050101_501_update']);
    Route::get('finance_debtor/1102050101_501_daily_pdf',[Finance_DebtorController::class,'_1102050101_501_daily_pdf']);
    Route::get('finance_debtor/1102050101_501_indiv_excel',[Finance_DebtorController::class,'_1102050101_501_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_703',[Finance_DebtorController::class,'_1102050101_703'])->name('_1102050101_703');
    Route::post('finance_debtor/1102050101_703_confirm',[Finance_DebtorController::class,'_1102050101_703_confirm']);
    Route::delete('finance_debtor/1102050101_703_delete',[Finance_DebtorController::class,'_1102050101_703_delete']);
    Route::get('finance_debtor/1102050101_703_daily_pdf',[Finance_DebtorController::class,'_1102050101_703_daily_pdf']);
    Route::get('finance_debtor/1102050101_703_indiv_excel',[Finance_DebtorController::class,'_1102050101_703_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050102_106',[Finance_DebtorController::class,'_1102050102_106'])->name('_1102050102_106');
    Route::post('finance_debtor/1102050102_106_confirm',[Finance_DebtorController::class,'_1102050102_106_confirm']);
    Route::post('finance_debtor/1102050102_106_confirm_iclaim',[Finance_DebtorController::class,'_1102050102_106_confirm_iclaim']);
    Route::delete('finance_debtor/1102050102_106_delete',[Finance_DebtorController::class,'_1102050102_106_delete']);
    Route::put('finance_debtor/1102050102_106/update/{vn}',[Finance_DebtorController::class,'_1102050102_106_update']);
    Route::get('finance_debtor/1102050102_106/tracking/{vn}',[Finance_DebtorController::class,'_1102050102_106_tracking']);
    Route::post('finance_debtor/1102050102_106/tracking_insert',[Finance_DebtorController::class,'_1102050102_106_tracking_insert']);
    Route::put('finance_debtor/1102050102_106/tracking_update/{tracking_id}',[Finance_DebtorController::class,'_1102050102_106_tracking_update']);
    Route::get('finance_debtor/1102050102_106_daily_pdf',[Finance_DebtorController::class,'_1102050102_106_daily_pdf']);
    Route::get('finance_debtor/1102050102_106_indiv_excel',[Finance_DebtorController::class,'_1102050102_106_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050102_108',[Finance_DebtorController::class,'_1102050102_108'])->name('_1102050102_108');
    Route::post('finance_debtor/1102050102_108_confirm',[Finance_DebtorController::class,'_1102050102_108_confirm']);
    Route::delete('finance_debtor/1102050102_108_delete',[Finance_DebtorController::class,'_1102050102_108_delete']);
    Route::put('finance_debtor/1102050102_108/update/{vn}',[Finance_DebtorController::class,'_1102050102_108_update']);
    Route::get('finance_debtor/1102050102_108_daily_pdf',[Finance_DebtorController::class,'_1102050102_108_daily_pdf']);
    Route::get('finance_debtor/1102050102_108_indiv_excel',[Finance_DebtorController::class,'_1102050102_108_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050102_602',[Finance_DebtorController::class,'_1102050102_602'])->name('_1102050102_602');
    Route::post('finance_debtor/1102050102_602_confirm',[Finance_DebtorController::class,'_1102050102_602_confirm']);
    Route::delete('finance_debtor/1102050102_602_delete',[Finance_DebtorController::class,'_1102050102_602_delete']);
    Route::put('finance_debtor/1102050102_602/update/{vn}',[Finance_DebtorController::class,'_1102050102_602_update']);
    Route::get('finance_debtor/1102050102_602_daily_pdf',[Finance_DebtorController::class,'_1102050102_602_daily_pdf']);
    Route::get('finance_debtor/1102050102_602_indiv_excel',[Finance_DebtorController::class,'_1102050102_602_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050102_801',[Finance_DebtorController::class,'_1102050102_801'])->name('_1102050102_801');
    Route::post('finance_debtor/1102050102_801_confirm',[Finance_DebtorController::class,'_1102050102_801_confirm']);
    Route::post('finance_debtor/1102050102_801_confirm_pp',[Finance_DebtorController::class,'_1102050102_801_confirm_pp']);
    Route::delete('finance_debtor/1102050102_801_delete',[Finance_DebtorController::class,'_1102050102_801_delete']);
    Route::get('finance_debtor/1102050102_801_daily_pdf',[Finance_DebtorController::class,'_1102050102_801_daily_pdf']);
    Route::get('finance_debtor/1102050102_801_indiv_excel',[Finance_DebtorController::class,'_1102050102_801_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050102_803',[Finance_DebtorController::class,'_1102050102_803'])->name('_1102050102_803');
    Route::post('finance_debtor/1102050102_803_confirm',[Finance_DebtorController::class,'_1102050102_803_confirm']);
    Route::post('finance_debtor/1102050102_803_confirm_bmt',[Finance_DebtorController::class,'_1102050102_803_confirm_bmt']);
    Route::delete('finance_debtor/1102050102_803_delete',[Finance_DebtorController::class,'_1102050102_803_delete']);
    Route::put('finance_debtor/1102050102_803/update/{vn}',[Finance_DebtorController::class,'_1102050102_803_update']);
    Route::get('finance_debtor/1102050102_803_daily_pdf',[Finance_DebtorController::class,'_1102050102_803_daily_pdf']);
    Route::get('finance_debtor/1102050102_803_indiv_excel',[Finance_DebtorController::class,'_1102050102_803_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_202',[Finance_DebtorController::class,'_1102050101_202'])->name('_1102050101_202');
    Route::post('finance_debtor/1102050101_202_confirm',[Finance_DebtorController::class,'_1102050101_202_confirm']);
    Route::delete('finance_debtor/1102050101_202_delete',[Finance_DebtorController::class,'_1102050101_202_delete']);
    Route::get('finance_debtor/1102050101_202_daily_pdf',[Finance_DebtorController::class,'_1102050101_202_daily_pdf']);
    Route::get('finance_debtor/1102050101_202_indiv_excel',[Finance_DebtorController::class,'_1102050101_202_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_217',[Finance_DebtorController::class,'_1102050101_217'])->name('_1102050101_217');
    Route::post('finance_debtor/1102050101_217_confirm',[Finance_DebtorController::class,'_1102050101_217_confirm']);
    Route::delete('finance_debtor/1102050101_217_delete',[Finance_DebtorController::class,'_1102050101_217_delete']);
    Route::get('finance_debtor/1102050101_217_daily_pdf',[Finance_DebtorController::class,'_1102050101_217_daily_pdf']);
    Route::get('finance_debtor/1102050101_217_indiv_excel',[Finance_DebtorController::class,'_1102050101_217_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_302',[Finance_DebtorController::class,'_1102050101_302'])->name('_1102050101_302');
    Route::post('finance_debtor/1102050101_302_confirm',[Finance_DebtorController::class,'_1102050101_302_confirm']);
    Route::delete('finance_debtor/1102050101_302_delete',[Finance_DebtorController::class,'_1102050101_302_delete']);
    Route::put('finance_debtor/1102050101_302/update/{an}',[Finance_DebtorController::class,'_1102050101_302_update']);
    Route::get('finance_debtor/1102050101_302_daily_pdf',[Finance_DebtorController::class,'_1102050101_302_daily_pdf']);
    Route::get('finance_debtor/1102050101_302_indiv_excel',[Finance_DebtorController::class,'_1102050101_302_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_304',[Finance_DebtorController::class,'_1102050101_304'])->name('_1102050101_304');
    Route::post('finance_debtor/1102050101_304_confirm',[Finance_DebtorController::class,'_1102050101_304_confirm']);
    Route::delete('finance_debtor/1102050101_304_delete',[Finance_DebtorController::class,'_1102050101_304_delete']);
    Route::put('finance_debtor/1102050101_304/update/{an}',[Finance_DebtorController::class,'_1102050101_304_update']);
    Route::get('finance_debtor/1102050101_304_daily_pdf',[Finance_DebtorController::class,'_1102050101_304_daily_pdf']);
    Route::get('finance_debtor/1102050101_304_indiv_excel',[Finance_DebtorController::class,'_1102050101_304_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_308',[Finance_DebtorController::class,'_1102050101_308'])->name('_1102050101_308');
    Route::post('finance_debtor/1102050101_308_confirm',[Finance_DebtorController::class,'_1102050101_308_confirm']);
    Route::delete('finance_debtor/1102050101_308_delete',[Finance_DebtorController::class,'_1102050101_308_delete']);
    Route::put('finance_debtor/1102050101_308/update/{an}',[Finance_DebtorController::class,'_1102050101_308_update']);
    Route::get('finance_debtor/1102050101_308_daily_pdf',[Finance_DebtorController::class,'_1102050101_308_daily_pdf']);
    Route::get('finance_debtor/1102050101_308_indiv_excel',[Finance_DebtorController::class,'_1102050101_308_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_310',[Finance_DebtorController::class,'_1102050101_310'])->name('_1102050101_310');
    Route::post('finance_debtor/1102050101_310_confirm',[Finance_DebtorController::class,'_1102050101_310_confirm']);
    Route::delete('finance_debtor/1102050101_310_delete',[Finance_DebtorController::class,'_1102050101_310_delete']);
    Route::get('finance_debtor/1102050101_310_daily_pdf',[Finance_DebtorController::class,'_1102050101_310_daily_pdf']);
    Route::get('finance_debtor/1102050101_310_indiv_excel',[Finance_DebtorController::class,'_1102050101_310_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_402',[Finance_DebtorController::class,'_1102050101_402'])->name('_1102050101_402');
    Route::post('finance_debtor/1102050101_402_confirm',[Finance_DebtorController::class,'_1102050101_402_confirm']);
    Route::delete('finance_debtor/1102050101_402_delete',[Finance_DebtorController::class,'_1102050101_402_delete']);
    Route::get('finance_debtor/1102050101_402_daily_pdf',[Finance_DebtorController::class,'_1102050101_402_daily_pdf']);
    Route::get('finance_debtor/1102050101_402_indiv_excel',[Finance_DebtorController::class,'_1102050101_402_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_502',[Finance_DebtorController::class,'_1102050101_502'])->name('_1102050101_502');
    Route::post('finance_debtor/1102050101_502_confirm',[Finance_DebtorController::class,'_1102050101_502_confirm']);
    Route::delete('finance_debtor/1102050101_502_delete',[Finance_DebtorController::class,'_1102050101_502_delete']);
    Route::put('finance_debtor/1102050101_502/update/{an}',[Finance_DebtorController::class,'_1102050101_502_update']);
    Route::get('finance_debtor/1102050101_502_daily_pdf',[Finance_DebtorController::class,'_1102050101_502_daily_pdf']);
    Route::get('finance_debtor/1102050101_502_indiv_excel',[Finance_DebtorController::class,'_1102050101_502_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050101_704',[Finance_DebtorController::class,'_1102050101_704'])->name('_1102050101_704');
    Route::post('finance_debtor/1102050101_704_confirm',[Finance_DebtorController::class,'_1102050101_704_confirm']);
    Route::delete('finance_debtor/1102050101_704_delete',[Finance_DebtorController::class,'_1102050101_704_delete']);
    Route::get('finance_debtor/1102050101_704_daily_pdf',[Finance_DebtorController::class,'_1102050101_704_daily_pdf']);
    Route::get('finance_debtor/1102050101_704_indiv_excel',[Finance_DebtorController::class,'_1102050101_704_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050102_107',[Finance_DebtorController::class,'_1102050102_107'])->name('_1102050102_107');
    Route::post('finance_debtor/1102050102_107_confirm',[Finance_DebtorController::class,'_1102050102_107_confirm']);
    Route::post('finance_debtor/1102050102_107_confirm_iclaim',[Finance_DebtorController::class,'_1102050102_107_confirm_iclaim']);
    Route::delete('finance_debtor/1102050102_107_delete',[Finance_DebtorController::class,'_1102050102_107_delete']);
    Route::put('finance_debtor/1102050102_107/update/{an}',[Finance_DebtorController::class,'_1102050102_107_update']);
    Route::get('finance_debtor/1102050102_107/tracking/{an}',[Finance_DebtorController::class,'_1102050102_107_tracking']);
    Route::post('finance_debtor/1102050102_107/tracking_insert',[Finance_DebtorController::class,'_1102050102_107_tracking_insert']);
    Route::put('finance_debtor/1102050102_107/tracking_update/{tracking_id}',[Finance_DebtorController::class,'_1102050102_107_tracking_update']);
    Route::get('finance_debtor/1102050102_107_daily_pdf',[Finance_DebtorController::class,'_1102050102_107_daily_pdf']);
    Route::get('finance_debtor/1102050102_107_indiv_excel',[Finance_DebtorController::class,'_1102050102_107_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050102_109',[Finance_DebtorController::class,'_1102050102_109'])->name('_1102050102_109');
    Route::post('finance_debtor/1102050102_109_confirm',[Finance_DebtorController::class,'_1102050102_109_confirm']);
    Route::delete('finance_debtor/1102050102_109_delete',[Finance_DebtorController::class,'_1102050102_109_delete']);
    Route::put('finance_debtor/1102050102_109/update/{an}',[Finance_DebtorController::class,'_1102050102_109_update']);
    Route::get('finance_debtor/1102050102_109_daily_pdf',[Finance_DebtorController::class,'_1102050102_109_daily_pdf']);
    Route::get('finance_debtor/1102050102_109_indiv_excel',[Finance_DebtorController::class,'_1102050102_109_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050102_603',[Finance_DebtorController::class,'_1102050102_603'])->name('_1102050102_603');
    Route::post('finance_debtor/1102050102_603_confirm',[Finance_DebtorController::class,'_1102050102_603_confirm']);
    Route::delete('finance_debtor/1102050102_603_delete',[Finance_DebtorController::class,'_1102050102_603_delete']);
    Route::put('finance_debtor/1102050102_603/update/{an}',[Finance_DebtorController::class,'_1102050102_603_update']);
    Route::get('finance_debtor/1102050102_603_daily_pdf',[Finance_DebtorController::class,'_1102050102_603_daily_pdf']);
    Route::get('finance_debtor/1102050102_603_indiv_excel',[Finance_DebtorController::class,'_1102050102_603_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050102_802',[Finance_DebtorController::class,'_1102050102_802'])->name('_1102050102_802');
    Route::post('finance_debtor/1102050102_802_confirm',[Finance_DebtorController::class,'_1102050102_802_confirm']);
    Route::delete('finance_debtor/1102050102_802_delete',[Finance_DebtorController::class,'_1102050102_802_delete']);
    Route::get('finance_debtor/1102050102_802_daily_pdf',[Finance_DebtorController::class,'_1102050102_802_daily_pdf']);
    Route::get('finance_debtor/1102050102_802_indiv_excel',[Finance_DebtorController::class,'_1102050102_802_indiv_excel']);
    Route::match(['get','post'],'finance_debtor/1102050102_804',[Finance_DebtorController::class,'_1102050102_804'])->name('_1102050102_804');
    Route::post('finance_debtor/1102050102_804_confirm',[Finance_DebtorController::class,'_1102050102_804_confirm']);
    Route::delete('finance_debtor/1102050102_804_delete',[Finance_DebtorController::class,'_1102050102_804_delete']);
    Route::get('finance_debtor/1102050102_804_daily_pdf',[Finance_DebtorController::class,'_1102050102_804_daily_pdf']);
    Route::get('finance_debtor/1102050102_804_indiv_excel',[Finance_DebtorController::class,'_1102050102_804_indiv_excel']);
    Route::get('finance_debtor/forget_search', function() { Session::forget('search'); return redirect()->back();});

    Route::match(['get','post'],'finance_debtor/hosxp_1102050101_202',[Finance_DebtorController::class,'hosxp_1102050101_202']);
    Route::match(['get','post'],'finance_debtor/hosxp_1102050101_203',[Finance_DebtorController::class,'hosxp_1102050101_203']);
    Route::get('finance_debtor/hosxp_1102050101_203_pdf',[Finance_DebtorController::class,'hosxp_1102050101_203_pdf']);
    Route::get('finance_debtor/hosxp_1102050101_203_excel',[Finance_DebtorController::class,'hosxp_1102050101_203_excel']);
    Route::match(['get','post'],'finance_debtor/hosxp_1102050101_303',[Finance_DebtorController::class,'hosxp_1102050101_303']);
    Route::match(['get','post'],'finance_debtor/hosxp_1102050101_307',[Finance_DebtorController::class,'hosxp_1102050101_307']);
    Route::match(['get','post'],'finance_debtor/hosxp_1102050101_401',[Finance_DebtorController::class,'hosxp_1102050101_401']);
    Route::match(['get','post'],'finance_debtor/hosxp_1102050101_801',[Finance_DebtorController::class,'hosxp_1102050101_801']);
    Route::match(['get','post'],'finance_debtor/hosxp_1102050102_106',[Finance_DebtorController::class,'hosxp_1102050102_106']);
    Route::match(['get','post'],'finance_debtor/hosxp_1102050102_107',[Finance_DebtorController::class,'hosxp_1102050102_107']);

// From ----------------------------------------------------------------------------------------------------------------------------------------
    Route::get('form',[Form_CheckController::class,'index']);
    Route::match(['get','post'],'form/check_asset_report',[Form_CheckController::class,'check_asset_report']);
    Route::get('form/check_asset_create/{depart}',[Form_CheckController::class,'check_asset_create'])->name('check_asset_create');
    Route::post('form/check_asset_save',[Form_CheckController::class,'check_asset_save'])->name('check_asset_save');
    Route::match(['get','post'],'form/check_nurse_report',[Form_CheckController::class,'check_nurse_report']);
    Route::get('form/check_nurse_create/{depart}',[Form_CheckController::class,'check_nurse_create'])->name('check_nurse_create');
    Route::post('form/check_nurse_save',[Form_CheckController::class,'check_nurse_save'])->name('check_nurse_save');

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
    Route::match(['get','post'],'medicalrecord_opd/nhso_endpoint',[Medicalrecord_OpdController::class,'nhso_endpoint']);
    Route::match(['get','post'],'medicalrecord_opd/nhso_endpoint_pull',[Medicalrecord_OpdController::class,'nhso_endpoint_pull']);
    Route::match(['get','post'],'medicalrecord_opd/nhso_endpoint_pull/{vstdate}/{cid}',[Medicalrecord_OpdController::class,'nhso_endpoint_pull_indiv']);

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

// service_death ------------------------------------------------------------------------------------------------------------------------------------
    Route::get('service_death/',[Service_DeathController::class,'index']);
    Route::match(['get','post'],'service_death/count',[Service_DeathController::class,'count']);
    Route::match(['get','post'],'service_death/diag_504',[Service_DeathController::class,'diag_504']);
    Route::match(['get','post'],'service_death/diag_icd10',[Service_DeathController::class,'diag_icd10']);

// service_dent -------------------------------------------------------------------------------------------------------------------------------------
    Route::get('service_dent/',[Service_DentController::class,'index']);
    Route::match(['get','post'],'service_dent/count',[Service_DentController::class,'count']);

// service_diag -------------------------------------------------------------------------------------------------------------------------------------
    Route::get('service_diag/',[Service_DiagController::class,'index']);
    Route::match(['get','post'],'service_diag/alcohol_withdrawal',[Service_DiagController::class,'alcohol_withdrawal']);
    Route::match(['get','post'],'service_diag/asthma',[Service_DiagController::class,'asthma']);
    Route::match(['get','post'],'service_diag/copd',[Service_DiagController::class,'copd']);
    Route::match(['get','post'],'service_diag/mi',[Service_DiagController::class,'mi']);
    Route::match(['get','post'],'service_diag/ihd',[Service_DiagController::class,'ihd']);
    Route::match(['get','post'],'service_diag/palliative_care',[Service_DiagController::class,'palliative_care']);
    Route::match(['get','post'],'service_diag/pneumonia',[Service_DiagController::class,'pneumonia']);
    Route::match(['get','post'],'service_diag/sepsis',[Service_DiagController::class,'sepsis']);
    Route::match(['get','post'],'service_diag/septic_shock',[Service_DiagController::class,'septic_shock']);
    Route::match(['get','post'],'service_diag/stroke',[Service_DiagController::class,'stroke']);
    Route::match(['get','post'],'service_diag/head_injury',[Service_DiagController::class,'head_injury']);
    Route::match(['get','post'],'service_diag/fracture',[Service_DiagController::class,'fracture']);
    Route::match(['get','post'],'service_diag/trauma',[Service_DiagController::class,'trauma']);

// service_drug -------------------------------------------------------------------------------------------------------------------------------------
    Route::get('service_drug/',[Service_DrugController::class,'index']);
    Route::match(['get','post'],'service_drug/antiviral',[Service_DrugController::class,'antiviral']);
    Route::match(['get','post'],'service_drug/antiviral_opd_pdf',[Service_DrugController::class,'antiviral_opd_pdf']);
    Route::match(['get','post'],'service_drug/antiviral_ipd_pdf',[Service_DrugController::class,'antiviral_ipd_pdf']);
    Route::match(['get','post'],'service_drug/prescription',[Service_DrugController::class,'prescription']);
    Route::match(['get','post'],'service_drug/value',[Service_DrugController::class,'value']);
    Route::match(['get','post'],'service_drug/value_diag_opd',[Service_DrugController::class,'value_diag_opd']);
    Route::match(['get','post'],'service_drug/value_diag_ipd',[Service_DrugController::class,'value_diag_ipd']);
    Route::match(['get','post'],'service_drug/value_drug_herb',[Service_DrugController::class,'value_drug_herb']);
    Route::get('service_drug/value_drug_herb_excel',[Service_DrugController::class,'value_drug_herb_excel']);
    Route::match(['get','post'],'service_drug/value_drug_herb_9',[Service_DrugController::class,'value_drug_herb_9']);
    Route::get('service_drug/value_drug_herb_9_excel',[Service_DrugController::class,'value_drug_herb_9_excel']);
    Route::match(['get','post'],'service_drug/value_drug_herb_32',[Service_DrugController::class,'value_drug_herb_32']);
    Route::get('service_drug/value_drug_herb_32_excel',[Service_DrugController::class,'value_drug_herb_32_excel']);
    Route::match(['get','post'],'service_drug/esrd',[Service_DrugController::class,'esrd']);
    Route::get('service_drug/esrd_excel',[Service_DrugController::class,'esrd_excel']);
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

// H-RiMS ################################################################################################################################
Route::prefix('hrims')->middleware(['auth', 'hrims'])->name('hrims.')->group(function () {
    Route::get('/', [HrimsController::class, 'index'])->name('dashboard');

    // Import_stm -----------------------------------------------------------------------------------------------------------------------
    Route::get('import_stm/',[ImportStmController::class,'index']);
    Route::match(['get','post'],'import_stm/ofc',[ImportStmController::class,'ofc'])->name('import_stm.ofc');
    Route::post('import_stm/ofc_save',[ImportStmController::class,'ofc_save']);
    Route::match(['get','post'],'import_stm/ofc_detail',[ImportStmController::class,'ofc_detail']);
    Route::match(['get','post'],'import_stm/ofc_kidney',[ImportStmController::class,'ofc_kidney'])->name('import_stm.ofc_kidney');
    Route::post('import_stm/ofc_kidney_save',[ImportStmController::class,'ofc_kidney_save']);
    Route::match(['get','post'],'import_stm/ofc_kidneydetail',[ImportStmController::class,'ofc_kidneydetail']);
    Route::match(['get','post'],'import_stm/lgo',[ImportStmController::class,'lgo'])->name('import_stm.lgo');
    Route::post('import_stm/lgo_save',[ImportStmController::class,'lgo_save']);
    Route::match(['get','post'],'import_stm/lgo_detail',[ImportStmController::class,'lgo_detail']);
    Route::match(['get','post'],'import_stm/lgo_kidney',[ImportStmController::class,'lgo_kidney'])->name('import_stm.lgo_kidney');
    Route::post('import_stm/lgo_kidney_save',[ImportStmController::class,'lgo_kidney_save']);
    Route::match(['get','post'],'import_stm/lgo_kidneydetail',[ImportStmController::class,'lgo_kidneydetail']);
    Route::match(['get','post'],'import_stm/sss_kidney',[ImportStmController::class,'sss_kidney'])->name('import_stm.sss_kidney');
    Route::post('import_stm/sss_kidney_save',[ImportStmController::class,'sss_kidney_save']);
    Route::match(['get','post'],'import_stm/sss_kidneydetail',[ImportStmController::class,'sss_kidneydetail']);
    Route::match(['get','post'],'import_stm/ucs',[ImportStmController::class,'ucs'])->name('import_stm.ucs');
    Route::post('import_stm/ucs_save',[ImportStmController::class,'ucs_save']);
    Route::match(['get','post'],'import_stm/ucs_detail',[ImportStmController::class,'ucs_detail']);
    Route::match(['get','post'],'import_stm/ucs_kidney',[ImportStmController::class,'ucs_kidney'])->name('import_stm.ucs_kidney');
    Route::post('import_stm/ucs_kidney_save',[ImportStmController::class,'ucs_kidney_save']);
    Route::match(['get','post'],'import_stm/ucs_kidneydetail',[ImportStmController::class,'ucs_kidneydetail']);

    // Claim_OP -------------------------------------------------------------------------------------------------------------------------
    Route::match(['get','post'],'claim_op/ucs_incup',[ClaimOpController::class,'ucs_incup']);
    Route::match(['get','post'],'claim_op/ucs_inprovince',[ClaimOpController::class,'ucs_inprovince']);
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
    Route::match(['get','post'],'claim_op/rcpt',[ClaimOpController::class,'rcpt']);
    Route::match(['get','post'],'claim_op/act',[ClaimOpController::class,'act']);

    // Claim_IP -------------------------------------------------------------------------------------------------------------------------
    Route::match(['get','post'],'claim_ip/ucs_incup',[ClaimIpController::class,'ucs_incup']);
    Route::match(['get','post'],'claim_ip/ucs_outcup',[ClaimIpController::class,'ucs_outcup']);
    Route::match(['get','post'],'claim_ip/stp',[ClaimIpController::class,'stp']);
    Route::match(['get','post'],'claim_ip/ofc',[ClaimIpController::class,'ofc']);
    Route::match(['get','post'],'claim_ip/lgo',[ClaimIpController::class,'lgo']);
    Route::match(['get','post'],'claim_ip/bkk',[ClaimIpController::class,'bkk']);
    Route::match(['get','post'],'claim_ip/bmt',[ClaimIpController::class,'bmt']);
    Route::match(['get','post'],'claim_ip/sss',[ClaimIpController::class,'sss']);
    Route::match(['get','post'],'claim_ip/gof',[ClaimIpController::class,'gof']);
    Route::match(['get','post'],'claim_ip/rcpt',[ClaimIpController::class,'rcpt']);
    Route::match(['get','post'],'claim_ip/act',[ClaimIpController::class,'act']);

    // Debtor -------------------------------------------------------------------------------------------------------------------------
    Route::get('debtor',[DebtorController::class,'index']);    
    Route::match(['get','post'],'debtor/check_income',[DebtorController::class,'_check_income']);
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
    Route::match(['get','post'],'debtor/1102050101_203',[DebtorController::class,'_1102050101_203']);
    Route::post('debtor/1102050101_203_confirm',[DebtorController::class,'_1102050101_203_confirm']);
    Route::delete('debtor/1102050101_203_delete',[DebtorController::class,'_1102050101_203_delete']);
    Route::put('debtor/1102050101_203/update/{vn}',[DebtorController::class,'_1102050101_203_update']);
    Route::get('debtor/1102050101_203_daily_pdf',[DebtorController::class,'_1102050101_203_daily_pdf']);
    Route::get('debtor/1102050101_203_indiv_excel',[DebtorController::class,'_1102050101_203_indiv_excel']);

});
