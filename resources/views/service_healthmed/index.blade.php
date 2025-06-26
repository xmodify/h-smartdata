@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <!-- Row -->
  <div class="row justify-content-center"> 
    <!-- Column left -->
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายงานข้อมูลการให้บริการแพทย์แผนไทย</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a  href="{{ url('service_healthmed/count') }}" target="_blank"><li>จำนวนผู้มารับบริการแพทย์แผนไทย</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_healthmed/acupuncture') }}" target="_blank"><li>จำนวนผู้มารับบริการฝังเข็ม</li></a></td>
                </tr>
                <tr>
                  <td><a href="{{ url('service_drug/value_drug_herb') }}" target="_blank"><li>มูลค่าการใช้ยาสมุนไพร</li></a></td>
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
