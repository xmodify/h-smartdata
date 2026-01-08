@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <!-- Row -->
  <div class="row justify-content-center">
    <!-- Column left -->
    <div class="col-md-6">
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">คลินิกเบาหวาน</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>
                <tr>
                  <td><a href="{{ url('service_ncd/dm_clinic') }}" target="_blank"><li>ทะเบียนผู้ป่วยคลินิกโรคเบาหวาน</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_ncd/dm') }}" target="_blank"><li>ข้อมูลบริการผู้ป่วยคลินิกโรคเบาหวาน</li></a></td>
                </tr>
                <tr>
                    <td><a href="{{ url('service_ncd/dm_appointment') }}" target="_blank"><li>ทะเบียนนัดหมาย ผู้ป่วยคลินิกโรคเบาหวาน</li></a></td>
                </tr>
                <tr>
                    <td><a href="{{ url('service_ncd/dm_nonclinic') }}" target="_blank"><li>รายชื่อผู้ป่วยวินิจจัยโรคเบาหวาน ที่ยังไม่ขึ้นทะเบียน Chronic</li></a></td>
                </tr>
                <tr>
                    <td><a href="{{ url('service_ncd/dm_admit') }}" target="_blank"><li>รายชื่อผู้ป่วยวินิจฉัยโรคเบาหวาน ที่ Admit</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_ncd/dm_death') }}" target="_blank"><li>ตรวจสอบสถานะการเสียชีวิตผู้ป่วยในทะเบียนคลินิกโรคเบาหวาน</li></a></td>
              </tr>
              <tr>
                  <td><a href="{{ url('service_ncd/dmht_waiting_period') }}" target="_blank"><li>ระยะเวลารอคอยเฉลี่ยคลินิกเบาหวาน-ความดัน</li></a></td>
              </tr>
              </tbody>
            </table>
          </div>      
      </div>
      <br>
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">คลินิกความดัน</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>
                <tr>
                  <td><a href="{{ url('service_ncd/ht_clinic') }}" target="_blank"><li>ทะเบียนผู้ป่วยคลินิกโรคความดัน</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_ncd/ht') }}" target="_blank"><li>ข้อมูลบริการผู้ป่วยคลินิกโรคความดัน</li></a></td>
                </tr>
                <tr>
                    <td><a href="{{ url('service_ncd/ht_appointment') }}" target="_blank"><li>ทะเบียนนัดหมาย ผู้ป่วยคลินิกโรคความดัน</li></a></td>
                </tr>
                <tr>
                    <td><a href="{{ url('service_ncd/ht_nonclinic') }}" target="_blank"><li>รายชื่อผู้ป่วยวินิจฉัยโรคความดัน ที่ยังไม่ขึ้นทะเบียน Chronic</li></a></td>
                </tr>
                <tr>
                    <td><a href="{{ url('service_ncd/ht_admit') }}" target="_blank"><li>รายชื่อผู้ป่วยวินิจฉัยโรคความดัน ที่ Admit</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_ncd/ht_death') }}" target="_blank"><li>ตรวจสอบสถานะการเสียชีวิตผู้ป่วยในทะเบียนคลินิกโรคความดัน</li></a></td>
              </tr>
              </tbody>
            </table>
          </div>      
      </div>
      <br>
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">คลินิก ARV</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>             
                <tr>
                  <td><a href="{{ url('service_ncd/arv_waiting_period') }}" target="_blank"><li>ระยะเวลารอคอยเฉลี่ยคลินิก ARV</li></a></td>
              </tr>
              </tbody>
            </table>
          </div>      
      </div>
      <br>
    </div>     
    <!-- END Column left -->
    <!-- Column Rigth -->
    <div class="col-md-6">
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">คลินิกฟอกไต HD</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>
                <tr>
                    <td><a href="{{ url('service_ncd/kidney_clinic') }}" target="_blank"><li>ทะเบียนผู้ป่วยคลินิกฟอกไต HD (รพ.)</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_ncd/kidney_hos') }}" target="_blank"><li>ข้อมูลบริการผู้ป่วยคลินิกฟอกไต HD (รพ.)</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_ncd/kidney_outsource') }}" target="_blank"><li>ข้อมูลบริการผู้ป่วยคลินิกฟอกไต HD (เอกชน)</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_ncd/kidney_egfr') }}" target="_blank"><li>ผู้ป่วย eGFR น้อยกว่า 30</li></a></td>
                </tr>
              </tbody>
            </table>
          </div>
      </div>
      <br>
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">คลินิกฟอกไต CAPD</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>
                <tr>
                    <td><a href="{{ url('service_ncd/capd_clinic') }}" target="_blank"><li>ทะเบียนผู้ป่วยคลินิกฟอกไต CAPD</li></a></td>
                </tr>
                <tr>
                    <td><a href="{{ url('service_ncd/capd') }}" target="_blank"><li>ข้อมูลบริการผู้ป่วยคลินิกฟอกไต CAPD</li></a></td>
                </tr>
                <tr>
                    <td><a href="{{ url('service_ncd/capd_appointment') }}" target="_blank"><li>ทะเบียนนัดหมาย ผู้ป่วยคลินิกฟอกไต CAPD</li></a></td>
                </tr>
                <tr>
                    <td><a href="{{ url('service_ncd/capd_nonclinic') }}" target="_blank"><li>ผู้ป่วย Stage eGFR 4,5 ที่ยังไม่ขึ้นทะเบียน</li></a></td>
                </tr>
              </tbody>
            </table>
          </div>
      </div>
      <br>
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">คลินิก Asthma</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>
                <tr>
                    <td><a href="{{ url('service_ncd/asthma_clinic') }}" target="_blank"><li>ทะเบียนผู้ป่วยคลินิก Asthma</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_diag/asthma') }}" target="_blank"><li>ข้อมูลบริการผู้ป่วยโรค Asthma</li></a></td>
                </tr>
              </tbody>
            </table>
          </div>
      </div>
      <br>
    </div>   
    <!-- End Column Rigth -->
  </div>
  <!-- End Row --> 
</div>
@endsection
