@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <!-- Row -->
  <div class="row justify-content-center"> 
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">ระบบงานห้องผ่าตัด</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a href="{{ url('service_operation/count') }}" target="_blank"><li>จำนวนผู้ป่วยผ่าตัด</li></a></td>
                </tr>  
              </tbody>
            </table>
          </div> 
      </div>            
    </div> 
  </div>
  <!-- End Row -->
</div>

@endsection


