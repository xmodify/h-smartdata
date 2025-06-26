@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <!-- Row -->
  <div class="row justify-content-center"> 
    <!-- Column -->
    <div class="col-md-6"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายงานข้อมูลการให้บริการส่งต่อ Refer</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a href="{{ url('service_refer/count') }}" target="_blank"><li>ผู้ป่วยส่งต่อ Refer</li></a></td>
                </tr> 
                <tr>
                  <td><a href="{{ url('service_refer/diag') }}" target="_blank"><li>ผู้ป่วยส่งต่อ Refer รายโรคที่สำคัญ</li></a></td>
                </tr> 
                <tr>
                  <td><a href="{{ url('service_refer/diag_top') }}" target="_blank"><li>อันดับโรคผู้ป่วยส่งต่อ Refer แยกจุดส่งต่อ</li></a></td>
                </tr>    
                <tr>
                  <td><a href="{{ url('service_refer/after_admit4') }}" target="_blank"><li>ผู้ป่วยส่งต่อ Refer ภายใน 4 ชม. หลังadmit</li></a></td>
                </tr>  
                <tr>
                  <td><a href="{{ url('service_refer/after_admit24') }}" target="_blank"><li>ผู้ป่วยส่งต่อ Refer ภายใน 24 ชม. หลังadmit</li></a></td>
                </tr>                
              </tbody>
            </table>
          </div> 
      </div>  
      <br>          
    </div> 
    <div class="col-md-6"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">ตรวจสอบการบันทึกข้อมูลส่งต่อ Refer</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody> 
                <tr>
                  <td><a href="{{ url('service_refer/not_complete') }}" target="_blank"><li>บันทึกข้อมูลส่งต่อ Refer ไม่ครบถ้วน</li></a></td>
                </tr>                 
              </tbody>
            </table>
          </div> 
      </div>            
    </div> 
    <!-- End Column -->
  </div>
  <!-- End Row -->
</div>

@endsection


