@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <div class="card border-primary">      
    <div class="card-header bg-primary text-white">เวชระเบียนผู้ป่วยใน</div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-12"> 
          <table class="table table-striped table-bordered">
            <thead>
              <tr class="table-primary">
                <th class="text-primary">ตรวจสอบการบันทึกข้อมูลเวชระเบียนผู้ป่วยใน</th>             
              </tr> 
            </thead> 
            <tbody> 
              <tr>
                <td><a href="{{ url('medicalrecord_ipd/non_dchsummary') }}" target="_blank"><li>ข้อมูลผู้ป่วย รอแพทย์สรุป Chart</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('medicalrecord_ipd/wait_icd_coder') }}" target="_blank"><li>ข้อมูลผู้ป่วย รอลงรหัสโรค ICD10 </li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('medicalrecord_ipd/patient_dchsummary') }}" target="_blank"><li>ข้อมูลผู้ป่วย สรุป Chart แล้ว</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('medicalrecord_ipd/dchsummary_audit') }}" target="_blank"><li>ข้อมูลผู้ป่วย Audit Chart แล้ว</li></a></td>
              </tr> 
            </tbody>
          </table>
        </div>        
      </div>
    </div>           
  </div>
</div>

@endsection

