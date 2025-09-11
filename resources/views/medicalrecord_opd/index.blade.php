@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <div class="card border-primary">      
    <div class="card-header bg-primary text-white">ตรวจสอบการบันทึกข้อมูลเวชระเบียนผู้ป่วยนอก</div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6"> 
          <table class="table table-striped table-bordered">
            <thead>
              <tr class="table-primary">
                <th class="text-primary">การขึ้นบัตร</th>             
              </tr> 
            </thead> 
            <tbody> 
              <tr>
                <td><a href="{{ url('dashboard/nhso_endpoint') }}" target="_blank"><li>ข้อมูลการ ปิดสิทธิ สปสช.</li></a></td>
              </tr>  
              <tr>
                <td><a href="{{ url('dashboard/opd_mornitor_non_authen') }}" target="_blank"><li>ไม่ขอ AuthenCode สปสช.</li></a></td>
              </tr>  
              <tr>
                <td><a href="{{ url('dashboard/opd_mornitor_non_hospmain') }}" target="_blank"><li>ไม่บันทึกรหัสสถานพยาบาลหลัก</li></a></td>
              </tr>                   
            </tbody>
          </table>
        </div>
        <div class="col-md-6"> 
          <table class="table table-striped table-bordered ">
            <thead>
              <tr class="table-primary ">
                <th class="text-primary">การให้รหัสโรค</th>             
              </tr> 
            </thead> 
            <tbody> 
              <tr>
                <td><a href="{{ url('medicalrecord_opd/non_diagtext') }}" target="_blank"><li>ไม่บันทึกการวินิจฉัยโรค Diagnosis Text</li></a></td>
              </tr>       
              <tr>
                <td><a href="{{ url('medicalrecord_opd/non_diagtext') }}" target="_blank"><li>ไม่บันทึกการตรวจร่างกาย Physical Examination</li></a></td>
              </tr>               
            </tbody>
          </table>
        </div>
      </div>
    </div>           
  </div>
</div>

@endsection

