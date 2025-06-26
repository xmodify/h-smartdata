@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <div class="row justify-content-center"> 
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายงานข้อมูลการเสียชีวิต</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a  href="{{ url('service_death/count') }}" target="_blank"><li>จำนวนผู้ป่วยเสียชีวิต</li></a></td>
                </tr>  
                <tr>
                  <td><a  href="{{ url('service_death/diag_504') }}" target="_blank"><li>จำนวนผู้เสียชีวิต วินิจฉัยตามสาเหตุ 21 กลุ่มโรค</li></a></td>
                </tr>   
                <tr>
                  <td><a  href="{{ url('service_death/diag_icd10') }}" target="_blank"><li>จำนวนผู้เสียชีวิต วินิจฉัยตามรหัส ICD10</li></a></td>
                </tr>                    
              </tbody>
            </table>
          </div> 
      </div>            
    </div> 
  </div>
</div>

@endsection


