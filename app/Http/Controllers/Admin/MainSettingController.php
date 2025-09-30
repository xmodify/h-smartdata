<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MainSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class MainSettingController extends Controller
{
    public function index()
    {
        $data = MainSetting::orderBy('name_th', 'asc')->get();
        return view('admin.main_setting', compact('data'));
    }
// clearCache ------------------------------------------------------------------------------------------------------------
    public function clearCache()
    {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return back()->with('success', 'ล้าง Cache เรียบร้อยแล้ว!');
    }
//-----------------------------------------------------------------------------------------------------------------------------
    public function update(Request $request, $id)
    {
        $request->validate([           
            'value' => 'required|string',
        ]);

        $setting = MainSetting::findOrFail($id);        
        $setting->value = $request->value;
        $setting->save();

    return redirect()->back()->with('success', 'แก้ไขข้อมูลสำเร็จ');
    }
#######################################################################################################################################    
// UP Structure -----------------------------------------------------------------------------------------------------------------------        
    public function up_structure(Request $request)
    {
    //Table main_setting-----------------------------------------------------------------------------------------------------------
        $main_setting = [
            ['id' => 1, 'name_th' => 'IPD จำนวนเตียง', 'name' => 'bed_qty', 'value' => ''],
            ['id' => 2, 'name_th' => 'Token Authen Kiosk สปสช.', 'name' => 'token_authen_kiosk_nhso', 'value' => ''],
            ['id' => 3, 'name_th' => 'Telegram Token', 'name' => 'telegram_token', 'value' => ''],
            ['id' => 4, 'name_th' => 'Telegram ChatID Notify_Summary', 'name' => 'telegram_chat_id', 'value' => ''], 
            ['id' => 5, 'name_th' => 'IPD ค่า K ', 'name' => 'k_value', 'value' => '1'],   
            ['id' => 6, 'name_th' => 'IPD BaseRate UCS ในเขต', 'name' => 'base_rate', 'value' => '8350'],
            ['id' => 7, 'name_th' => 'IPD BaseRate UCS นอกเขต', 'name' => 'base_rate2', 'value' => '9600'],  
            ['id' => 8, 'name_th' => 'IPD BaseRate OFC', 'name' => 'base_rate_ofc', 'value' => '6200'],  
            ['id' => 9, 'name_th' => 'IPD BaseRate LGO', 'name' => 'base_rate_lgo', 'value' => '6194'],  
            ['id' => 10, 'name_th' => 'IPD BaseRate SSS', 'name' => 'base_rate_sss', 'value' => '6200'],           
            ['id' => 11, 'name_th' => 'สิทธิ พรบ. (รหัสสิทธิ HOSxP)', 'name' => 'pttype_act', 'value' => '29'],
            ['id' => 12, 'name_th' => 'สิทธิ ปกส. กองทุนทดแทน (รหัสสิทธิ HOSxP)', 'name' => 'pttype_sss_fund', 'value' => '"S6",25,31'],
            ['id' => 13, 'name_th' => 'สิทธิ ตรวจสุขภาพหน่วยงานภาครัฐ (รหัสสิทธิ HOSxP)', 'name' => 'pttype_checkup', 'value' => '14,27'],
            ['id' => 14, 'name_th' => 'สิทธิ ประกันชีวิต iClaim (รหัสสิทธิ HOSxP)', 'name' => 'pttype_iclaim', 'value' => '26'],
            ['id' => 15, 'name_th' => 'สิทธิ ปกส. 72 ชั่วโมงแรก (รหัสสิทธิ HOSxP)', 'name' => 'pttype_sss_72', 'value' => '32'],
            ['id' => 16, 'name_th' => 'LAB Pregnancy Test (รหัส lab_items HOSxP)', 'name' => 'lab_prt', 'value' => '444'],
            ['id' => 17, 'name_th' => 'ยา Clopidogrel (รหัส drugitems HOSxP)', 'name' => 'drug_clopidogrel', 'value' => '1520019'],
            ['id' => 18, 'name_th' => 'ชื่อโรงพยาบาล', 'name' => 'hospital_name', 'value' => 'โรงพยาบาลหัวตะพาน'],
            ['id' => 19, 'name_th' => 'รหัส 5 หลักโรงพยาบาล', 'name' => 'hospital_code', 'value' => '10989'],
            ['id' => 20, 'name_th' => 'OPOH Token', 'name' => 'opoh_token', 'value' => ''],
        ];
        
        foreach ($main_setting as $row) {
            $check = MainSetting::where('id', $row['id'])->count();
            if ($check > 0) {
                DB::table('main_setting')
                ->where('id', $row['id']) 
                ->update([
                    'name_th' => $row['name_th'],
                    'name' => $row['name'],
                ]);
            } else {
                DB::table('main_setting')
                ->insert([
                    'id' => $row['id'],
                    'name_th' => $row['name_th'],
                    'name' => $row['name'],
                    'value' => $row['value'],
                ]);
            }
        }

    //After Table-----------------------------------------------------------------------------------------------------------
        $tables = [
            'lookup_icode' => [
                ['name' => 'ems', 'definition' => 'VARCHAR(1) NULL AFTER `kidney`'],
            ],
            'lookup_ward' => [               
                ['name' => 'ward_normal', 'definition' => 'VARCHAR(1) NULL AFTER `ward_name`'],
                ['name' => 'bed_qty', 'definition' => 'INT UNSIGNED NULL AFTER `ward_homeward`']
            ],
        ];

        try {
            foreach ($tables as $table => $columns) {
                foreach ($columns as $col) {
                    if (!Schema::hasColumn($table, $col['name'])) {
                        DB::statement("ALTER TABLE `$table` ADD COLUMN `{$col['name']}` {$col['definition']}");
                    }
                }
            }

            return redirect()->route('admin.main_setting')
                ->with('success', 'Upgrade Structure สำเร็จ');

        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
    
}
