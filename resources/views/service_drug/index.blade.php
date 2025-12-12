@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <!-- Row -->
  <div class="row justify-content-center"> 
    <!-- Column left -->
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายงานข้อมูลการให้บริการด้านยา</div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <table class="table table-hover">
                  <tbody>   
                    <tr>
                      <td><a href="{{ url('service_drug/prescription') }}" target="_blank"><li>จำนวนใบสั่งยา</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('service_drug/value') }}" target="_blank"><li>มูลค่าการใช้ยา 5 ปีย้อนหลัง</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('service_drug/value_drug_top') }}" target="_blank"><li>มูลค่าการใช้ยา 20 อันดับ</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('service_drug/value_diag_opd') }}" target="_blank"><li>มูลค่าการใช้ยา 20 อันดับโรค (Primary Diagnosis) ผู้ป่วยนอก</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('service_drug/value_diag_ipd') }}" target="_blank"><li>มูลค่าการใช้ยา 20 อันดับโรค (Primary Diagnosis) ผู้ป่วยใน</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('service_drug/drugtime_s') }}" target="_blank"><li>ข้อมูลการสั่งยาช่วงเวลา 00.00-08.00 น.</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('service_drug/drugallergy') }}" target="_blank"><li>ข้อมูลการแพ้ยาแยก รพสต.</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('backoffice_risk/med_error') }}" target="_blank"><li>Medication Error Report</li></a></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-md-6">
                <table class="table table-hover">
                  <tbody>                       
                    <tr>
                      <td><a href="{{ url('service_drug/herb') }}" target="_blank"><li>มูลค่าการใช้ยาสมุนไพร</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('service_drug/herb9') }}" target="_blank"><li>มูลค่าการใช้ยาสมุนไพร 9 รายการ</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('service_drug/herb32') }}" target="_blank"><li>มูลค่าการใช้ยาสมุนไพร 32 รายการ</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('service_drug/esrd') }}" target="_blank"><li>มูลค่าการใช้ยา ESRD</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('service_drug/hd') }}" target="_blank"><li>มูลค่าการใช้ยา HD</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('service_drug/dmht') }}" target="_blank"><li>ข้อมูลการใช้ยา DM-HT</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('service_drug/due') }}" target="_blank"><li>ข้อมูลการใช้ยา DUE</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('service_drug/metformin') }}" target="_blank"><li>ข้อมูลการใช้ยา Metformin</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('service_drug/warfarin') }}" target="_blank"><li>ข้อมูลการใช้ยา Warfarin</li></a></td>
                    </tr>
                    <tr>
                      <td><a href="{{ url('service_drug/antiviral') }}" target="_blank"><li>ข้อมูลการใช้ยาต้านไวรัส</li></a></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
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


