@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <!-- Row -->
  <div class="row justify-content-center"> 
    <!-- Column left -->
    <div class="col-md-6"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">ระบบงานบัญชี 1</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a href="{{ url('service_pcu/pcu1_village') }}" target="_blank"><li>หมู่บ้านในเขตรับผิดชอบ</li></a></td>
                </tr> 
                <tr>
                  <td><a href="{{ url('service_pcu/pcu1_vt_ehp') }}" target="_blank"><li>ข้อมูลคัดกรองเชิงรุก-ประชากรในเขตรับผิดชอบ (EHP)</li></a></td>
                </tr>        
                <tr>
                  <td><a href="{{ url('service_pcu/pcu1_home_visit') }}" target="_blank"><li>การเยี่ยมบ้าน-ประชากรในเขตรับผิดชอบ</li></a></td>
                </tr>    
              </tbody>
            </table>
          </div>  
      </div> 
      <br>
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">ระบบงานบัญชี 2</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a href="#" ><li>ข้อมูลบริการ ANC</li></a></td>
                </tr>                                  
              </tbody>
            </table>
          </div> 
      </div>           
    </div> 
    <!-- END Column left -->
    <!-- Column Rigth -->
    <div class="col-md-6"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">ข้อมูลบริการ</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a href="{{ url('service_pcu/diag_top30') }}" target="_blank"><li>30 อันดับโรค (Primary Diagnosis) ต.รัตนวารี</li></a></td>
                </tr>   
                <tr>
                  <td><a href="{{ url('service_pcu/death') }}" target="_blank"><li>สาเหตุการเสียชีวิต ต.รัตนวารี</li></a></td>
                </tr>                                             
              </tbody>
            </table>
          </div> 
      </div>            
    </div> 
    <!-- End Column Rigth -->
  </div>
  <!-- End Row -->
</div>

@endsection


