@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <!-- Row -->
  <div class="row justify-content-center" > 
    <!-- Column left -->
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายงานข้อมูลเฉพาะโรค</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>
                <tr>
                  <td><a  href="{{ url('service_diag/alcohol_withdrawal') }}" target="_blank"><li>โรค Alcohol Withdrawal</li></a></td>
                </tr>   
                <tr>
                  <td><a  href="{{ url('service_diag/asthma') }}" target="_blank"><li>โรค Asthma</li></a></td>
                </tr> 
                <tr>
                  <td><a  href="{{ url('service_diag/copd') }}" target="_blank"><li>โรค COPD</li></a></td>
                </tr> 
                <tr>
                  <td><a  href="{{ url('service_diag/mi') }}" target="_blank"><li>โรค MI</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_diag/ihd') }}" target="_blank"><li>โรค หัวใจขาดเลือด(IHD)</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_diag/palliative_care') }}" target="_blank"><li>โรค Palliative Care</li></a></td>
                </tr>   
                <tr>
                  <td><a  href="{{ url('service_diag/pneumonia') }}" target="_blank"><li>โรค Pneumonia</li></a></td>
                </tr>   
                <tr>
                  <td><a  href="{{ url('service_diag/sepsis') }}" target="_blank"><li>โรค Sepsis</li></a></td>
                </tr> 
                <tr>
                  <td><a  href="{{ url('service_diag/septic_shock') }}" target="_blank"><li>โรค Septic Shock</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_diag/stroke') }}" target="_blank"><li>โรค Stroke</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_diag/head_injury') }}" target="_blank"><li>โรค Head Injury</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_diag/fracture') }}" target="_blank"><li>โรค กระดูกสะโพกหัก</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_diag/trauma') }}" target="_blank"><li>โรค Trauma</li></a></td>
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


