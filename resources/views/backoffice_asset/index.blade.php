@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center"> 
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">ทะเบียนครุภัณฑ์</div>
          <div class="card-body">
            <table class="table table-hover">
              <tbody>   
                <tr>
                  <td><a  href="{{ url('backoffice_asset/office') }}" target="_blank"><li>ครุภัณฑ์สำนักงาน</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_asset/car') }}" target="_blank"><li>ครุภัณฑ์ยานพาหนะและขนส่ง</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_asset/electric') }}" target="_blank"><li>ครุภัณฑ์ไฟฟ้าและวิทยุ</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_asset/generator') }}" target="_blank"><li>ครุภัณฑ์ไฟฟ้าและวิทยุ เครื่องกำเนิดไฟฟ้า</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_asset/advert') }}" target="_blank"><li>ครุภัณฑ์โฆษณาและเผยแพร่</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_asset/agriculture_tool') }}" target="_blank"><li>ครุภัณฑ์การเกษตร เครื่องมือและอุปกรณ์</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_asset/agriculture_mechanical') }}" target="_blank"><li>ครุภัณฑ์การเกษตร เครื่องจักรกล</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_asset/factory_tool') }}" target="_blank"><li>ครุภัณฑ์โรงงาน เครื่องมือและอุปกรณ์</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_asset/science') }}" target="_blank"><li>ครุภัณฑ์วิทยาศาสตร์และการแพทย์</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_asset/house') }}" target="_blank"><li>ครุภัณฑ์งานบ้านงานครัว</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_asset/physical') }}" target="_blank"><li>ครุภัณฑ์กีฬา/กายภาพ</li></a></td>
                </tr>
                <tr>
                  <td><a  href="{{ url('backoffice_asset/computer') }}" target="_blank"><li>ครุภัณฑ์คอมพิวเตอร์</li></a></td>
                </tr>
              </tbody>
            </table>
          </div> 
      </div>            
    </div>    
  </div>
</div>
@endsection

