@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <!-- Row -->
  <div class="row justify-content-center"> 
    <!-- Column left -->
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายงานข้อมูลการให้บริการทันตกรรม</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a  href="{{ url('service_dent/count') }}" target="_blank"><li>จำนวนผู้มารับบริการทันตกรรม</li></a></td>
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
