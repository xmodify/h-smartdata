@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <!-- Row -->
  <div class="row justify-content-center"> 
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">ระบบงานรังสีวิทยา</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a href="{{ url('service_xray/ct') }}" target="_blank"><li>ข้อมูลผู้ป่วย CT Scan</li></a></td>
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


