@extends('layouts.app')

@section('content')
<!-- row -->
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">จำนวนบุคลากรปัจจุบัน {{$hrd_total}} คน จำแนกตามประเภท</div>
        <div id="hrd_type" style="width: 100%; height: 350px"></div>
      </div>
      <br>
    </div>
    <div class="col-md-6">
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">จำนวนบุคลากรปัจจุบัน  {{$hrd_total}} คน จำแนกตามเพศ</div>
        <div id="hrd_sex" style="width: 100%; height: 350px"></div>
      </div>
      <br>
    </div>
  </div>
</div>
<br>
<!-- row -->
<div class="container-fluid">
  <div class="row justify-content-center"> 
    <div class="col-md-6"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายงานผลิตภาพทางการพยาบาล</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>
              <tr>
                  <td><a  href="{{ url('backoffice_hrd/nurse_productivity_opd') }}" target="_blank"><li>รายงานผลิตภาพทางการพยาบาลแผนกผู้ป่วยนอก OPD</li></a></td>
                </tr>      
                <tr>
                  <td><a  href="{{ url('backoffice_hrd/nurse_productivity_er') }}" target="_blank"><li>รายงานผลิตภาพทางการพยาบาลแผนกอุบัติเหตุ-ฉุกเฉิน ER</li></a></td>
                </tr>   
                <tr>
                  <td><a  href="{{ url('backoffice_hrd/nurse_productivity_ipd') }}" target="_blank"><li>รายงานผลิตภาพทางการพยาบาลแผนกผู้ป่วยใน สามัญ</li></a></td>
                </tr> 
                <tr>
                  <td><a  href="{{ url('backoffice_hrd/nurse_productivity_vip') }}" target="_blank"><li>รายงานผลิตภาพทางการพยาบาลแผนกผู้ป่วยใน VIP</li></a></td>
                </tr>    
                <tr>
                  <td><a  href="{{ url('backoffice_hrd/nurse_productivity_ncd') }}" target="_blank"><li>รายงานผลิตภาพทางการพยาบาลแผนกผู้ป่วย NCD</li></a></td>
                </tr>   
                <tr>
                  <td><a  href="{{ url('backoffice_hrd/nurse_productivity_lr') }}" target="_blank"><li>รายงานผลิตภาพทางการพยาบาลแผนกห้องคลอด LR</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_hrd/nurse_productivity_or') }}" target="_blank"><li>รายงานผลิตภาพทางการพยาบาลแผนกห้องผ่าตัด OR</li></a></td>
                </tr>      
                <tr>
                  <td><a  href="{{ url('backoffice_hrd/nurse_productivity_ckd') }}" target="_blank"><li>รายงานผลิตภาพทางการพยาบาลผู้ป่วย CKD</li></a></td>
                </tr>      
                <tr>
                  <td><a  href="{{ url('backoffice_hrd/nurse_productivity_hd') }}" target="_blank"><li>รายงานผลิตภาพทางการพยาบาลศูนย์ฟอกไต HD รพ.</li></a></td>
                </tr>            
              </tbody>
            </table>
          </div> 
      </div>    
      <br>        
    </div> 
    <div class="col-md-6"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายงานการปฏิบัติงาน</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>
                <tr>
                  <td><a  href="{{ url('backoffice_hrd/checkin') }}" target="_blank"><li>ข้อมูลการลงเวลา</li></a></td>
                </tr>  
              </tbody>
            </table>
          </div> 
      </div> 
      <br>               
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายงานข้อมูลสุขภาพ</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>
                <tr>
                  <td><a  href="{{ url('backoffice_hrd/health_screen') }}" target="_blank"><li>รายงานการคัดกรองสุขภาพ</li></a></td>
                </tr>      
                <tr>
                  <td><a  href="{{ url('backoffice_hrd/health_checkup') }}" target="_blank"><li>รายงานการตรวจสุขภาพ</li></a></td>
                </tr> 
                </tbody>
            </table>
          </div> 
      </div>  
      <br> 
    </div> 
  </div>
</div>
@endsection
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>

<!-- Pie Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new ApexCharts(document.querySelector("#hrd_type"), {
        series: <?php echo json_encode($hrd_type_total); ?>,
        chart: {
          height: 350,
          type: 'pie',
          toolbar: {
            show: true
          }
        },
        labels: <?php echo json_encode($hrd_type_name); ?>,
      }).render();
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new ApexCharts(document.querySelector("#hrd_sex"), {
        series: <?php echo json_encode($hrd_sex_total); ?>,
        chart: {
          height: 350,
          type: 'pie',
          toolbar: {
            show: true
          }
        },
        labels: <?php echo json_encode($hrd_sex); ?>,
      }).render();
    });
  </script>

