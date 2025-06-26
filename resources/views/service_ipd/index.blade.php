@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <!-- Row -->
  <div class="row justify-content-center"> 
    <!-- Column left -->
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายงานข้อมูลการให้บริการผู้ป่วยใน</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a href="{{ url('service_ipd/count') }}" target="_blank"><li>ข้อมูลบริการผู้ป่วยใน</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_ipd/count_spclty') }}" target="_blank"><li>ข้อมูลบริการผู้ป่วยใน แยกบริการเฉพาะด้าน</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_ipd/diag_top30') }}" target="_blank"><li>จำนวนผู้ป่วยใน 30 อันดับโรค (Primary Diagnosis)</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_ipd/diag_505') }}" target="_blank"><li>จำนวนผู้ป่วยในจำแนกตามกลุ่มโรค (75 กลุ่มโรค) (รง.505)</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_ipd/severe_type') }}" target="_blank"><li>จำนวนผู้ป่วยในแยกระดับความรุนแรง</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_ipd/severe_type_ipd') }}" target="_blank"><li>จำนวนผู้ป่วยในแยกระดับความรุนแรง (สามัญ)</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_ipd/severe_type_vip') }}" target="_blank"><li>จำนวนผู้ป่วยในแยกระดับความรุนแรง (VIP)</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_ipd/ipd_oper') }}" target="_blank"><li>หัตถการที่สำคัญผู้ป่วยใน</li></a></td>
                </tr>  
                <tr>
                  <td><a href="{{ url('service_ipd/readmit28') }}" target="_blank"><li>Re-Admit ใน 28 วัน ด้วยโรคเดิม</li></a></td>
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
