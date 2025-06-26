@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <!-- Row -->
  <div class="row justify-content-center"> 
    <!-- Column left -->
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายงานข้อมูลการให้บริการแผนกอุบัติเหตุ-ฉุกเฉิน ER</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>
                <tr>
                  <td><a href="{{ url('service_er/count') }}" target="_blank"><li>จำนวนผู้รับบริการแผนกอุบัติเหตุ-ฉุกเฉิน </li></a></td>
                </tr>   
                <tr>
                  <td><a href="{{ url('service_er/er_type') }}" target="_blank"><li>จำนวนผู้รับบริการแผนกอุบัติเหตุ-ฉุกเฉิน ตามความเร่งด่วน 5 ระดับ</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_er/er_oper') }}" target="_blank"><li>หัตถการที่สำคัญแผนกอุบัติเหตุ-ฉุกเฉิน</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_er/ems') }}" target="_blank"><li>จำนวนผู้ป่วยให้บริการ EMS</li></a></td>
                </tr>   
                <tr>
                  <td><a href="{{ url('service_er/revisit') }}" target="_blank"><li>Re-visit ใน 48 ชม. ด้วยโรคเดิม ER/OPD</li></a></td>
                </tr>  
                <tr>
                  <td><a href="{{ url('service_er/bps180up') }}" target="_blank"><li>รายชื่อผู้ป่วยที่มีความดันโลหิตค่าบนมากกว่า 180</li></a></td>
                </tr>  
                <tr>
                  <td><a href="{{ url('service_er/nurse_diag') }}" target="_blank"><li>รายชื่อผู้ป่วยที่ตรวจโดยพยาบาลช่วงเวลา 19.00-08.00 น.</li></a></td>
                </tr>      
                <tr>
                  <td><a href="{{ url('service_er/waitingtime_admit') }}" target="_blank"><li>รายชื่อผู้ป่วยรอ Admit เกิน 2 ชั่วโมง</li></a></td>
                </tr>  
                                <tr>
                  <td><a href="{{ url('service_er/diag_top30') }}" target="_blank"><li>จำนวนผู้ป่วยนอก 30 อันดับโรค (Primary Diagnosis) ER</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_er/diag_504') }}" target="_blank"><li>จำนวนผู้ป่วยนอก กลุ่มสาเหตุ (21 กลุ่มโรค) (รง.504) ER</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_er/diag_506') }}" target="_blank"><li>จำนวนผู้ป่วยนอก กลุ่มโรคที่ต้องเฝ้าระวัง (รง.506) ER</li></a></td>
                </tr>                
              </tbody>
            </table>
          </div> 
      </div>            
    </div> 
    <!-- END Column left -->
    <!-- Column Rigth -->
    
    <!-- End Column Rigth -->
  </div>
  <!-- End Row -->
</div>

@endsection

