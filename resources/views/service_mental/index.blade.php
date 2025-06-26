@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <!-- Row -->
  <div class="row justify-content-center"> 
    <!-- Column left -->
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายงานข้อมูลการให้บริการสุขภาพจิตและยาเสพติด</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a  href="{{ url('service_mental/mental_appointment') }}" target="_blank"><li>ทะเบียนนัดหมาย ผู้ป่วยคลินิกสุขภาพจิตและยาเสพติด</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_mental/diag_dementia') }}" target="_blank"><li>จำนวนผู้ป่วยโรคสมองเสื่อม</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_mental/diag_addict') }}" target="_blank"><li>จำนวนผู้ป่วยติดสารเสพติด</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_mental/diag_addict_alcohol') }}" target="_blank"><li>จำนวนผู้ป่วยติดแอลกอฮอล์</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_mental/diag_schizophrenia') }}" target="_blank"><li>จำนวนผู้ป่วยโรคจิตเภท</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_mental/diag_depressive') }}" target="_blank"><li>จำนวนผู้ป่วยโรคซึมเศร้า</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_mental/diag_anxiety') }}" target="_blank"><li>จำนวนผู้ป่วยโรควิตกกังวล</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_mental/diag_epilepsy') }}" target="_blank"><li>จำนวนผู้ป่วยโรคลมชัก</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_mental/diag_retardation') }}" target="_blank"><li>จำนวนผู้พิการทางสติปัญญา</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_mental/diag_skills') }}" target="_blank"><li>จำนวนผู้พิการเกี่ยวกับการเรียนรู้</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_mental/diag_autism') }}" target="_blank"><li>จำนวนผู้ป่วยออสติสติก</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_mental/diag_behavior') }}" target="_blank"><li>จำนวนผู้ป่วยความผิดปกติเกียวกับพฤติกรรม</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('service_mental/diag_selfharm') }}" target="_blank"><li>จำนวนผู้ป่วยพยายามฆ่าตัวตาย</li></a></td>
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
