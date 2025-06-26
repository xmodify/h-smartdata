@extends('layouts.app')

@section('content')
<div class="container-fluid">
<!-- Row -->
  <div class="row justify-content-center">   
    <!-- Column left -->
    <div class="col-md-6"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">ค่ารักษาพยาบาล</div>
        <div class="card-body">
          <table class="table table-hover">
            <tbody> 
              <tr>
                <td><a href="{{ url('hosxp_setting/income') }}" target="_blank"><li>หมวดค่ารักษาพยาบาล</li></a></td>
              </tr>  
              <tr>
                <td><a href="{{ url('hosxp_setting/nondrug') }}" target="_blank"><li>รายการค่ารักษาพยายาล</li></a></td>
              </tr>      
              <tr>
                <td><a href="{{ url('hosxp_setting/adp_code') }}" target="_blank"><li>ทะเบียน ADP Code (Eclaim)</li></a></td>
              </tr>           
            </tbody>
          </table>
        </div> 
      </div>
      <br>
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">หัตถการ</div>
        <div class="card-body">
          <table class="table table-hover">
            <tbody> 
              <tr>
                <td><a href="{{ url('hosxp_setting/icd9_opd') }}" target="_blank"><li>หัตถการผู้ป่วยนอก (OPD,ER)</li></a></td>
              </tr>  
              <tr>
                <td><a href="{{ url('hosxp_setting/icd9_ipd') }}" target="_blank"><li>หัตถการผู้ป่วยใน</li></a></td>
              </tr>      
              <tr>
                <td><a href="{{ url('hosxp_setting/icd9_dent') }}" target="_blank"><li>หัตถการทันตกรรม</li></a></td>
              </tr>     
            </tbody>
          </table>
        </div>
      </div>  
      <br>
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">เวชภัณฑ์ยา</div>
        <div class="card-body">
          <table class="table table-hover">
            <tbody> 
              <tr>
                <td><a href="{{ url('hosxp_setting/drug_cat') }}" target="_blank"><li>ตรวจสอบ Drug Catalog</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('hosxp_setting/drug_all') }}" target="_blank"><li>ทะเบียนยาทั้งหมด</li></a></td>
              </tr>    
              <tr>
                <td><a href="{{ url('hosxp_setting/drug_herb') }}" target="_blank"><li>ทะเบียนยาสมุนไพร</li></a></td>
              </tr>  
              <tr>
                <td><a href="{{ url('hosxp_setting/drug_support') }}" target="_blank"><li>ทะเบียนยาสนับสนุน</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('hosxp_setting/drug_outside') }}" target="_blank"><li>ทะเบียนยานอก รพ.</li></a></td>
              </tr>                 
            </tbody>
          </table>
        </div> 
      </div>  
      <br>     
    </div>     
    <!-- Column right -->    
    <div class="col-md-6">  
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายการ Lab</div>
        <div class="card-body">
          <table class="table table-hover">
            <tbody> 
              <tr>
                <td><a href="{{ url('hosxp_setting/lab_item') }}" target="_blank"><li>รายการ Lab</li></a></td>
              </tr>          
            </tbody>
          </table>
        </div> 
      </div>
      <br> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายการ Xray</div>
        <div class="card-body">
          <table class="table table-hover">
            <tbody> 
              <tr>
                <td><a href="{{ url('hosxp_setting/xray_item') }}" target="_blank"><li>รายการ Xray</li></a></td>
              </tr>          
            </tbody>
          </table>
        </div> 
      </div>
      <br>
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">รายการอื่น ๆ</div>
        <div class="card-body">
          <table class="table table-hover">
            <tbody> 
              <tr>
                <td><a href="{{ url('hosxp_setting/pttype') }}" target="_blank"><li>สิทธิการรักษา</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('hosxp_setting/doctor') }}" target="_blank"><li>บุคลากรทางการแพทย์</li></a></td>
              </tr>    
              <tr>
                <td><a href="{{ url('hosxp_setting/clinic') }}" target="_blank"><li>คลินิก</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('hosxp_setting/spclty') }}" target="_blank"><li>แผนก</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('hosxp_setting/department') }}" target="_blank"><li>ห้องตรวจ</li></a></td>
              </tr>     
              <tr>
                <td><a href="{{ url('hosxp_setting/ovstist') }}" target="_blank"><li>ประเภทการมารับบริการ</li></a></td>
              </tr>   
              <tr>
                <td><a href="{{ url('hosxp_setting/vaccine') }}" target="_blank"><li>ทะเบียนวัคซีน</li></a></td>
              </tr>                               
            </tbody>
          </table>
        </div> 
      </div>    
      <br>    
    </div>    
  <!-- Row -->
  </div>
</div>
@endsection

