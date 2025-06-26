@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <!-- Row -->
  <div class="row justify-content-center"> 
    <!-- Column left -->
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายงานข้อมูลการให้บริการกายภาพบำบัด</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a  href="{{ url('service_physic/count') }}" target="_blank"><li>จำนวนผู้มารับบริการกายภาพบำบัด</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_physic/diag_top30') }}" target="_blank"><li>จำนวนผู้มารับบริการกายภาพบำบัด 30 อันดับโรค</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_physic/diag') }}" target="_blank"><li>จำนวนผู้มารับบริการกายภาพบำบัด รายโรคที่สำคัญ</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_physic/physic_appointment') }}" target="_blank"><li>ทะเบียนนัดหมาย คลินิกกายภาพบำบัด</li></a></td>
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

