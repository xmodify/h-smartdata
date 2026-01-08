@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">      
    <div class="col-md-12">
        <form method="POST" enctype="multipart/form-data">
        @csrf
          <div class="row">                          
              <div class="col-md-9" align="left"></div>
              <div class="col-md-2" align="right">     
                  <select class="form-select my-1" name="budget_year">
                  @foreach ($budget_year_select as $row)
                  <option value="{{$row->LEAVE_YEAR_ID}}" @if ($budget_year == "$row->LEAVE_YEAR_ID") selected="selected"  @endif>{{$row->LEAVE_YEAR_NAME}}</option>
                  @endforeach 
                  </select>                        
              </div>
              <div class="col-md-1" align="right">  
                  <button type="submit" class="btn btn-primary my-1 ">{{ __('ค้นหา') }}</button> 
              </div>
          </div>
        </form>
    </div>    
  </div>
</div>
<br>
<!-- row --> 
<div class="container-fluid">
  <div class="row justify-content-center">  
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ระยะเวลารอคอยเฉลี่ยคลินิกเบาหวาน-ความดัน ปีงบประมาณ {{ $budget_year }} </div>        
        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead>
              <tr class="table-secondary">                  
                  <th class="text-center">เดือน</th>
                  <th class="text-center">รอซักประวัติ</th>     
                  <th class="text-center">ซักประวัติ</th>                    
                  <th class="text-center">รอตรวจ</th>
                  <th class="text-center">แพทย์ตรวจ</th>                    
                  <th class="text-center">รอรับยา</th>
                  <th class="text-center">รวมทั้งหมด</th>
              </tr>     
            </thead>         
            @foreach($waiting_period_month as $row)          
              <tr>
                  <td align="center">{{ $row->month }}</td> 
                  <td align="center">{{ $row->screen_wait }}</td>
                  <td align="center">{{ $row->screen_success }}</td>
                  <td align="center">{{ $row->doctor_wait }}</td>
                  <td align="center">{{ $row->doctor_success }}</td>
                  <td align="center">{{ $row->rx_success }}</td>
                  <td align="center">{{ $row->success_all }}</td>                 
              </tr>                
            @endforeach                
          </table>  
        </div>      
      </div>      
    </div>   
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ระยะเวลารอคอยเฉลี่ยคลินิกเบาหวาน-ความดัน ปีงบประมาณย้อนหลัง </div>        
        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead>
              <tr class="table-secondary">                  
                  <th class="text-center">ปีงบประมาณ</th>
                  <th class="text-center">รอซักประวัติ</th>     
                  <th class="text-center">ซักประวัติ</th>                    
                  <th class="text-center">รอตรวจ</th>
                  <th class="text-center">แพทย์ตรวจ</th>                    
                  <th class="text-center">รอรับยา</th>
                  <th class="text-center">รวมทั้งหมด</th>
              </tr>     
            </thead>         
            @foreach($waiting_period_year as $row)          
              <tr>
                  <td align="center">{{ $row->year_bud }}</td> 
                  <td align="center">{{ $row->screen_wait }}</td>
                  <td align="center">{{ $row->screen_success }}</td>
                  <td align="center">{{ $row->doctor_wait }}</td>
                  <td align="center">{{ $row->doctor_success }}</td>
                  <td align="center">{{ $row->rx_success }}</td>
                  <td align="center">{{ $row->success_all }}</td>                 
              </tr>                
            @endforeach                
          </table>   
        </div>     
      </div>      
    </div>                     
  </div>
</div>  
<br>
 
@endsection


