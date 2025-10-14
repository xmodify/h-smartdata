@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <!-- Row -->
  <div class="row justify-content-center"> 
    <!-- Column left -->
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">ระบบตรวจสอบ/ประเมิน</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a  href="{{ url('/customer_complain') }}" target="_blank"><li>ความคิดเห็น-ข้อเสนอแนะ</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('/form/check_asset_report') }}" target="_blank"><li>รายงานการตรวจสอบเครื่องมือแพทย์และอุปกรณ์ฉุกเฉิน</li></a></td>
                </tr>
              </tbody>
            </table>
          </div> 
      </div> 
      <br>           
    </div> 
    <!-- END Column left -->
    <!-- Column Rigth -->
    
    <!-- End Column Rigth -->
  </div>
  <!-- End Row -->
</div>

@endsection

