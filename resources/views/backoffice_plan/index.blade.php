@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center"> 
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">ข้อมูลพื้นฐานสถานบริการสุขภาพ</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a  href="{{ url('backoffice_plan/service') }}" target="_blank"><li>ข้อมูลบริการโรงพยาบาล</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_plan/diag') }}" target="_blank"><li>ข้อมูลอันดับโรคสำคัญ</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_plan/death') }}" target="_blank"><li>ข้อมูลการเสียชีวิต</li></a></td>
                </tr>
              </tbody>
            </table>
          </div> 
      </div> 
      <br>           
    </div>    
  </div>
</div>
<div class="container-fluid">
  <div class="row justify-content-center"> 
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">แผนงาน</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a  href="{{ url('backoffice_plan/plan_project') }}" target="_blank"><li>แผนงานโครงการ</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_plan/plan_humandev') }}" target="_blank"><li>แผนพัฒนาบุคลากร</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_plan/plan_durable') }}" target="_blank"><li>แผนจัดซื้อครุภัณฑ์</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_plan/plan_repair') }}" target="_blank"><li>แผนบำรุงรักษา</li></a></td>
                </tr>
              </tbody>
            </table>
          </div> 
      </div>            
    </div> 
  </div>
</div>
@endsection

