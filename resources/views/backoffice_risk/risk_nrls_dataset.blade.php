@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <div class="row justify-content-center">  
    <div class="col-md-10">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูล dataset แบบรายเดือน ส่ง NRLS วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                @csrf            
                <div class="row" >
                        <label class="col-md-3 col-form-label text-md-end my-1">{{ __('วันที่') }}</label>
                    <div class="col-md-2">
                        <input type="date" name="start_date" class="form-control my-1" placeholder="Date" value="{{ $start_date }}" > 
                    </div>
                        <label class="col-md-1 col-form-label text-md-end my-1">{{ __('ถึง') }}</label>
                    <div class="col-md-2">
                        <input type="date" name="end_date" class="form-control my-1" placeholder="Date" value="{{ $end_date }}" > 
                    </div>                     
                    <div class="col-md-1" >                            
                        <button type="submit" class="btn btn-primary my-1 ">{{ __('ค้นหา') }}</button>
                    </div>
                </div>
                <div class="row">                          
                    <div class="col-md-8" align="left"> 
                        <h5 class="card-title text-primary"></h5>
                    </div>                 
                    <div class="col-md-4" align="right">
                        <a class="btn btn-success my-2 " href="{{ url('backoffice_risk/nrls_dataset_export') }}" target="_blank" type="submit">
                        Export
                        </a>                    
                    </div>      
                </div>
            </form>   
            <table class="table table-bordered table-striped">      
                <tr>
                    @foreach($rr001 as $row) 
                    <td align="left">RR001-จำนวนวันนอนผู้ป่วยใน (จำนวนวัน ผู้ป่วยนอนใน รพ.)</td> 
                    <td align="center">{{ $row->rr001 }} </td>
                    @endforeach                       
                </tr>
                <tr>
                    @foreach($rr003 as $row) 
                    <td align="left">RR003-จำนวนราย visit ผู้ป่วยนอก ในเวลาราชการ</td> 
                    <td align="center">{{ $row->rr003 }}</td> 
                    @endforeach
                </tr>
                <tr>
                    @foreach($rr004 as $row) 
                    <td align="left">RR004-จำนวนราย visit ผู้ป่วยนอก นอกเวลาราชการ</td> 
                    <td align="center">{{ $row->rr004 }}</td> 
                    @endforeach
                </tr>
                <tr>
                    @foreach($rr005 as $row) 
                    <td align="left">RR005-จำนวนผู้ป่วยนอก ในเวลาราชการ</td> 
                    <td align="center">{{ $row->rr005 }}</td> 
                    @endforeach
                </tr>
                <tr>
                    @foreach($rr006 as $row) 
                    <td align="left">RR006-จำนวนผู้ป่วยนอก นอกเวลาราชการ</td> 
                    <td align="center">{{ $row->rr006 }}</td> 
                    @endforeach
                </tr>
                <tr>
                    @foreach($rr007 as $row) 
                    <td align="left">RR007-จำนวนผู้ป่วยฉุกเฉินวิกฤต (สีแดง) ของหน่วยอุบัติเหตุ-ฉุกเฉิน</td> 
                    <td align="center">{{ $row->rr007 }}</td> 
                    @endforeach
                </tr>
                <tr>
                    @foreach($rr008 as $row) 
                    <td align="left">RR008-จำนวนผู้ป่วยฉุกเฉิน (สีเหลือง) ของหน่วยอุบัติเหตุ-ฉุกเฉิน</td> 
                    <td align="center">{{ $row->rr008 }}</td> 
                    @endforeach
                </tr>
                <tr>
                    @foreach($rr009 as $row) 
                    <td align="left">RR009-จำนวนผู้ป่วยเจ็บเล็กน้อย (สีเขียว) ของหน่วยอุบัติเหตุ-ฉุกเฉิน</td> 
                    <td align="center">{{ $row->rr009 }}</td> 
                    @endforeach
                </tr>
                <tr>
                    @foreach($rr010 as $row) 
                    <td align="left">RR010-จำนวนผู้ป่วยนอกหรือผู้ป่วยทั่วไป (สีขาว) ของหน่วยอุบัติเหตุ-ฉุกเฉิน</td> 
                    <td align="center">{{ $row->rr010 }}</td> 
                    @endforeach
                </tr>
                <tr>
                    @foreach($rr011 as $row) 
                    <td align="left">RR011-จำนวนผู้ป่วยที่มีการส่งต่อ (Refer)</td> 
                    <td align="center">{{ $row->rr011 }}</td> 
                    @endforeach
                </tr>
                <tr>
                    @foreach($rr015 as $row)  
                    <td align="left">RR015-จำนวนผู้คลอด</td> 
                    <td align="center">{{ $row->rr015 }}</td> 
                    @endforeach
                </tr>
                <tr>
                    @foreach($rr016 as $row) 
                    <td align="left">RR016-จำนวนทารกแรกเกิดมีชีพ</td> 
                    <td align="center">{{ $row->rr016 }}</td> 
                    @endforeach
                </tr>
                <tr>
                    @foreach($rr022 as $row) 
                    <td align="left">RR022-จำนวนใบสั่งยาผู้ป่วยนอก</td> 
                    <td align="center">{{ $row->rr022 }}</td> 
                    @endforeach
                </tr>
                <tr>
                    @foreach($rr024 as $row) 
                    <td align="left">RR024-จำนวนผู้ป่วยฉุกเฉินรุนแรง (สีชมพู) ของหน่วยอุบัติเหตุ-ฉุกเฉิน</td> 
                    <td align="center">{{ $row->rr024 }}</td> 
                    @endforeach
                </tr>
            </table>   
          </div>       
      </div>      
    </div>
  </div>
</div>
<br>
@endsection