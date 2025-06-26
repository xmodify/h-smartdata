@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <!-- Row -->
  <div class="row justify-content-center"> 
    <!-- Column left -->
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายงานข้อมูลการให้บริการผู้ป่วยนอก</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody> 
                <tr>
                  <td><a href="{{ url('service_opd/count') }}" target="_blank"><li>จำนวนผู้ป่วยนอก</li></a></td>
                </tr> 
                <tr>
                  <td><a href="{{ url('service_opd/count_spclty') }}" target="_blank"><li>จำนวนผู้ป่วยนอก แยกบริการเฉพาะด้าน</li></a></td>
                </tr> 
                <tr>
                  <td><a href="{{ url('service_opd/diag_top30') }}" target="_blank"><li>จำนวนผู้ป่วยนอก 30 อันดับโรค (Primary Diagnosis)</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_opd/diag_504') }}" target="_blank"><li>จำนวนผู้ป่วยนอก กลุ่มสาเหตุ (21 กลุ่มโรค) (รง.504)</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_opd/diag_506') }}" target="_blank"><li>จำนวนผู้ป่วยนอก กลุ่มโรคที่ต้องเฝ้าระวัง (รง.506)</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_opd/waiting_period') }}" target="_blank"><li>ระยะเวลารอคอยเฉลี่ยผู้ป่วยนอก</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_opd/telehealth') }}" target="_blank"><li>การให้บริการแพทย์ทางไกล Telehealth</li></a></td>
                </tr>
              </tbody>
            </table>
          </div> 
      </div> 
      <br>           
    </div>     
  </div>
  <!-- End Row -->
</div>

@endsection

