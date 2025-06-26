@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">

@section('content')
<div class="container-fluid">
    <h5 class="alert alert-primary"><strong>รายงานการตรวจสอบความพร้อมของเครื่องมือแพทย์และอุปกรณ์ฉุกเฉิน </strong></h5>  
</div> 
<div class="container-fluid">  
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
    </form>    
</div>
<!--row-->
<div class="container-fluid"> 
    <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>รายงานสรุปการตรวจสอบความพร้อมของเครื่องมือแพทย์และอุปกรณ์ฉุกเฉิน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">                       
                        <th class="text-center">หน่วยงาน</th>
                        <th class="text-center" colspan= "4">Defibrillator</th>
                        <th class="text-center" colspan= "4">Laryngoscope</th>
                        <th class="text-center" colspan= "4">Ambu bag</th>
                        <th class="text-center" colspan= "4">ETT+อุปกรณ์Away</th>
                        <th class="text-center" colspan= "4">Oxygen+อุปกรณ์</th>
                        <th class="text-center" colspan= "4">Auto CPR</th>
                        <th class="text-center" colspan= "4">กล่องยาฉุกเฉิน</th>
                    </tr>
                    <tr class="table-secondary"> 
                        <td class="text-center"></td>                      
                        <td class="text-center">พร้อมใช้</td>
                        <td class="text-center">ชำรุด</td>
                        <td class="text-center">ไม่พอ</td>
                        <td class="text-center">ไม่มี</td>
                        <td class="text-center">พร้อมใช้</td>
                        <td class="text-center">ชำรุด</td>
                        <td class="text-center">ไม่พอ</td>
                        <td class="text-center">ไม่มี</td>
                        <td class="text-center">พร้อมใช้</td>
                        <td class="text-center">ชำรุด</td>
                        <td class="text-center">ไม่พอ</td>
                        <td class="text-center">ไม่มี</td>
                        <td class="text-center">พร้อมใช้</td>
                        <td class="text-center">ชำรุด</td>
                        <td class="text-center">ไม่พอ</td>
                        <td class="text-center">ไม่มี</td>
                        <td class="text-center">พร้อมใช้</td>
                        <td class="text-center">ชำรุด</td>
                        <td class="text-center">ไม่พอ</td>
                        <td class="text-center">ไม่มี</td>
                        <td class="text-center">พร้อมใช้</td>
                        <td class="text-center">ชำรุด</td>
                        <td class="text-center">ไม่พอ</td>
                        <td class="text-center">ไม่มี</td>
                        <td class="text-center">พร้อมใช้</td>
                        <td class="text-center">ชำรุด</td>
                        <td class="text-center">ไม่พอ</td>
                        <td class="text-center">ไม่มี</td>
                    </tr>
                </thead>
                @foreach($check_asset_sum as $row)
                    <tr>
                        <td align="left">{{$row->depart}} {{$row->total}} ครั้ง</td>
                        <td align="center">{{$row->asset1_ready}}</td>
                        <td align="center">{{$row->asset1_repair}}</td>
                        <td align="center">{{$row->asset1_enough}}</td>
                        <td align="center">{{$row->asset1_have}}</td>
                        <td align="center">{{$row->asset2_ready}}</td>
                        <td align="center">{{$row->asset2_repair}}</td>
                        <td align="center">{{$row->asset2_enough}}</td>
                        <td align="center">{{$row->asset2_have}}</td>
                        <td align="center">{{$row->asset3_ready}}</td>
                        <td align="center">{{$row->asset3_repair}}</td>
                        <td align="center">{{$row->asset3_enough}}</td>
                        <td align="center">{{$row->asset3_have}}</td>
                        <td align="center">{{$row->asset4_ready}}</td>
                        <td align="center">{{$row->asset4_repair}}</td>
                        <td align="center">{{$row->asset4_enough}}</td>
                        <td align="center">{{$row->asset4_have}}</td>
                        <td align="center">{{$row->asset5_ready}}</td>
                        <td align="center">{{$row->asset5_repair}}</td>
                        <td align="center">{{$row->asset5_enough}}</td>
                        <td align="center">{{$row->asset5_have}}</td>
                        <td align="center">{{$row->asset6_ready}}</td>
                        <td align="center">{{$row->asset6_repair}}</td>
                        <td align="center">{{$row->asset6_enough}}</td>
                        <td align="center">{{$row->asset6_have}}</td>
                        <td align="center">{{$row->asset7_ready}}</td>
                        <td align="center">{{$row->asset7_repair}}</td>
                        <td align="center">{{$row->asset7_enough}}</td>
                        <td align="center">{{$row->asset7_have}}</td>
                    </tr>
                @endforeach
            </table> 
            <table class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">                       
                        <th class="text-center">หน่วยงาน</th>
                        <th class="text-center" colspan= "4">Ventilator</th>
                        <th class="text-center" colspan= "4">Ekg 12 lead</th>
                        <th class="text-center" colspan= "4">Suctionและอุปกรณ์</th>
                        <th class="text-center" colspan= "4">เครื่องดมยา</th>
                        <th class="text-center" colspan= "4">Radiant Warmer</th>
                        <th class="text-center" colspan= "4">เครื่อง NST</th>
                    </tr>
                    <tr class="table-secondary"> 
                        <td class="text-center"></td>                      
                        <td class="text-center">พร้อมใช้</td>
                        <td class="text-center">ชำรุด</td>
                        <td class="text-center">ไม่พอ</td>
                        <td class="text-center">ไม่มี</td>
                        <td class="text-center">พร้อมใช้</td>
                        <td class="text-center">ชำรุด</td>
                        <td class="text-center">ไม่พอ</td>
                        <td class="text-center">ไม่มี</td>
                        <td class="text-center">พร้อมใช้</td>
                        <td class="text-center">ชำรุด</td>
                        <td class="text-center">ไม่พอ</td>
                        <td class="text-center">ไม่มี</td>
                        <td class="text-center">พร้อมใช้</td>
                        <td class="text-center">ชำรุด</td>
                        <td class="text-center">ไม่พอ</td>
                        <td class="text-center">ไม่มี</td>
                        <td class="text-center">พร้อมใช้</td>
                        <td class="text-center">ชำรุด</td>
                        <td class="text-center">ไม่พอ</td>
                        <td class="text-center">ไม่มี</td>
                        <td class="text-center">พร้อมใช้</td>
                        <td class="text-center">ชำรุด</td>
                        <td class="text-center">ไม่พอ</td>
                        <td class="text-center">ไม่มี</td>
                    </tr>
                </thead>
                @foreach($check_asset_sum as $row)
                    <tr>
                        <td align="left">{{$row->depart}} {{$row->total}} ครั้ง</td>
                        <td align="center">{{$row->asset8_ready}}</td>
                        <td align="center">{{$row->asset8_repair}}</td>
                        <td align="center">{{$row->asset8_enough}}</td>
                        <td align="center">{{$row->asset8_have}}</td>
                        <td align="center">{{$row->asset9_ready}}</td>
                        <td align="center">{{$row->asset9_repair}}</td>
                        <td align="center">{{$row->asset9_enough}}</td>
                        <td align="center">{{$row->asset9_have}}</td>
                        <td align="center">{{$row->asset10_ready}}</td>
                        <td align="center">{{$row->asset10_repair}}</td>
                        <td align="center">{{$row->asset10_enough}}</td>
                        <td align="center">{{$row->asset10_have}}</td>
                        <td align="center">{{$row->asset11_ready}}</td>
                        <td align="center">{{$row->asset11_repair}}</td>
                        <td align="center">{{$row->asset11_enough}}</td>
                        <td align="center">{{$row->asset11_have}}</td>
                        <td align="center">{{$row->asset12_ready}}</td>
                        <td align="center">{{$row->asset12_repair}}</td>
                        <td align="center">{{$row->asset12_enough}}</td>
                        <td align="center">{{$row->asset12_have}}</td>
                        <td align="center">{{$row->asset13_ready}}</td>
                        <td align="center">{{$row->asset13_repair}}</td>
                        <td align="center">{{$row->asset13_enough}}</td>
                        <td align="center">{{$row->asset13_have}}</td>
                    </tr>
                @endforeach
            </table> 
        </div>         
    </div>
</div>
<br>
<!--row-->
<div class="container-fluid"> 
    <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>อุบัติเหตุ-ฉุกเฉิน ER วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="check_asset_er" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">วันที่-เวลา</th>
                        <th class="text-center">ผู้ตรวจสอบ</th>
                        <th class="text-center">Defibrillator</th>
                        <th class="text-center">Laryngoscope</th>
                        <th class="text-center">Ambu bag</th>
                        <th class="text-center">ETT+อุปกรณ์Away</th>
                        <th class="text-center">Oxygen+อุปกรณ์</th>
                        <th class="text-center">Auto CPR</th>
                        <th class="text-center">กล่องยาฉุกเฉิน</th>
                        <th class="text-center">Ventilator</th>
                        <th class="text-center">Ekg 12 lead</th>
                        <th class="text-center">Suction+อุปกรณ์</th>
                        <th class="text-center">เครื่องดมยา</th>
                        <th class="text-center">เครื่อง NST</th>
                        <th class="text-center">Radiant Warmer</th>
                        <th class="text-center">หมายเหตุ</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($check_asset_er as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="right">{{ DatetimeThai($row->created_at) }}</td>   
                        <td align="right">{{ $row->hr_check }}</td>
                        <td align="right">{{ $row->asset1 }}</td>
                        <td align="right">{{ $row->asset2 }}</td>
                        <td align="right">{{ $row->asset3 }}</td>
                        <td align="right">{{ $row->asset4 }}</td> 
                        <td align="right">{{ $row->asset5 }}</td> 
                        <td align="right">{{ $row->asset6 }}</td> 
                        <td align="right">{{ $row->asset7 }}</td> 
                        <td align="right">{{ $row->asset8 }}</td> 
                        <td align="right">{{ $row->asset9 }}</td> 
                        <td align="right">{{ $row->asset10 }}</td> 
                        <td align="right">{{ $row->asset11 }}</td> 
                        <td align="right">{{ $row->asset12 }}</td> 
                        <td align="right">{{ $row->asset13 }}</td> 
                        <td align="right">{{ $row->outher }}</td> 
                    </tr>
                <?php $count++; ?>
                @endforeach
            </table> 
        </div>         
    </div>
</div>
<br>
<!--row-->
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>ห้องคลอด LR วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="check_asset_lr" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">วันที่-เวลา</th>
                        <th class="text-center">ผู้ตรวจสอบ</th>
                        <th class="text-center">Defibrillator</th>
                        <th class="text-center">Laryngoscope</th>
                        <th class="text-center">Ambu bag</th>
                        <th class="text-center">ETT+อุปกรณ์Away</th>
                        <th class="text-center">Oxygen+อุปกรณ์</th>
                        <th class="text-center">Auto CPR</th>
                        <th class="text-center">กล่องยาฉุกเฉิน</th>
                        <th class="text-center">Ventilator</th>
                        <th class="text-center">Ekg 12 lead</th>
                        <th class="text-center">Suction+อุปกรณ์</th>
                        <th class="text-center">เครื่องดมยา</th>
                        <th class="text-center">เครื่อง NST</th>
                        <th class="text-center">Radiant Warmer</th>
                        <th class="text-center">หมายเหตุ</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($check_asset_lr as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="right">{{ DatetimeThai($row->created_at) }}</td>   
                        <td align="right">{{ $row->hr_check }}</td>
                        <td align="right">{{ $row->asset1 }}</td>
                        <td align="right">{{ $row->asset2 }}</td>
                        <td align="right">{{ $row->asset3 }}</td>
                        <td align="right">{{ $row->asset4 }}</td> 
                        <td align="right">{{ $row->asset5 }}</td> 
                        <td align="right">{{ $row->asset6 }}</td> 
                        <td align="right">{{ $row->asset7 }}</td> 
                        <td align="right">{{ $row->asset8 }}</td> 
                        <td align="right">{{ $row->asset9 }}</td> 
                        <td align="right">{{ $row->asset10 }}</td> 
                        <td align="right">{{ $row->asset11 }}</td> 
                        <td align="right">{{ $row->asset12 }}</td> 
                        <td align="right">{{ $row->asset13 }}</td> 
                        <td align="right">{{ $row->outher }}</td> 
                    </tr>
                <?php $count++; ?>
                @endforeach
            </table> 
        </div>         
    </div>
</div>
<br>
<!--row-->
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>ห้องผ่าตัด OR วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="check_asset_or" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">วันที่-เวลา</th>
                        <th class="text-center">ผู้ตรวจสอบ</th>
                        <th class="text-center">Defibrillator</th>
                        <th class="text-center">Laryngoscope</th>
                        <th class="text-center">Ambu bag</th>
                        <th class="text-center">ETT+อุปกรณ์Away</th>
                        <th class="text-center">Oxygen+อุปกรณ์</th>
                        <th class="text-center">Auto CPR</th>
                        <th class="text-center">กล่องยาฉุกเฉิน</th>
                        <th class="text-center">Ventilator</th>
                        <th class="text-center">Ekg 12 lead</th>
                        <th class="text-center">Suction+อุปกรณ์</th>
                        <th class="text-center">เครื่องดมยา</th>
                        <th class="text-center">เครื่อง NST</th>
                        <th class="text-center">Radiant Warmer</th>
                        <th class="text-center">หมายเหตุ</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($check_asset_or as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="right">{{ DatetimeThai($row->created_at) }} วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</td>   
                        <td align="right">{{ $row->hr_check }}</td>
                        <td align="right">{{ $row->asset1 }}</td>
                        <td align="right">{{ $row->asset2 }}</td>
                        <td align="right">{{ $row->asset3 }}</td>
                        <td align="right">{{ $row->asset4 }}</td> 
                        <td align="right">{{ $row->asset5 }}</td> 
                        <td align="right">{{ $row->asset6 }}</td> 
                        <td align="right">{{ $row->asset7 }}</td> 
                        <td align="right">{{ $row->asset8 }}</td> 
                        <td align="right">{{ $row->asset9 }}</td> 
                        <td align="right">{{ $row->asset10 }}</td> 
                        <td align="right">{{ $row->asset11 }}</td> 
                        <td align="right">{{ $row->asset12 }}</td> 
                        <td align="right">{{ $row->asset13 }}</td> 
                        <td align="right">{{ $row->outher }}</td> 
                    </tr>
                <?php $count++; ?>
                @endforeach
            </table> 
        </div>         
    </div>
</div>
<br>
<!--row-->
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>ฟอกไต HD รพ. วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="check_asset_hd" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">วันที่-เวลา</th>
                        <th class="text-center">ผู้ตรวจสอบ</th>
                        <th class="text-center">Defibrillator</th>
                        <th class="text-center">Laryngoscope</th>
                        <th class="text-center">Ambu bag</th>
                        <th class="text-center">ETT+อุปกรณ์Away</th>
                        <th class="text-center">Oxygen+อุปกรณ์</th>
                        <th class="text-center">Auto CPR</th>
                        <th class="text-center">กล่องยาฉุกเฉิน</th>
                        <th class="text-center">Ventilator</th>
                        <th class="text-center">Ekg 12 lead</th>
                        <th class="text-center">Suction+อุปกรณ์</th>
                        <th class="text-center">เครื่องดมยา</th>
                        <th class="text-center">เครื่อง NST</th>
                        <th class="text-center">Radiant Warmer</th>
                        <th class="text-center">หมายเหตุ</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($check_asset_hd as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="right">{{ DatetimeThai($row->created_at) }}</td>   
                        <td align="right">{{ $row->hr_check }}</td>
                        <td align="right">{{ $row->asset1 }}</td>
                        <td align="right">{{ $row->asset2 }}</td>
                        <td align="right">{{ $row->asset3 }}</td>
                        <td align="right">{{ $row->asset4 }}</td> 
                        <td align="right">{{ $row->asset5 }}</td> 
                        <td align="right">{{ $row->asset6 }}</td> 
                        <td align="right">{{ $row->asset7 }}</td> 
                        <td align="right">{{ $row->asset8 }}</td> 
                        <td align="right">{{ $row->asset9 }}</td> 
                        <td align="right">{{ $row->asset10 }}</td> 
                        <td align="right">{{ $row->asset11 }}</td> 
                        <td align="right">{{ $row->asset12 }}</td> 
                        <td align="right">{{ $row->asset13 }}</td> 
                        <td align="right">{{ $row->outher }}</td> 
                    </tr>
                <?php $count++; ?>
                @endforeach
            </table> 
        </div>         
    </div>
</div>
<br>
<!--row-->
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>ผู้ป่วยในสามัญ วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="check_asset_ipd" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">วันที่-เวลา</th>
                        <th class="text-center">ผู้ตรวจสอบ</th>
                        <th class="text-center">Defibrillator</th>
                        <th class="text-center">Laryngoscope</th>
                        <th class="text-center">Ambu bag</th>
                        <th class="text-center">ETT+อุปกรณ์Away</th>
                        <th class="text-center">Oxygen+อุปกรณ์</th>
                        <th class="text-center">Auto CPR</th>
                        <th class="text-center">กล่องยาฉุกเฉิน</th>
                        <th class="text-center">Ventilator</th>
                        <th class="text-center">Ekg 12 lead</th>
                        <th class="text-center">Suction+อุปกรณ์</th>
                        <th class="text-center">เครื่องดมยา</th>
                        <th class="text-center">เครื่อง NST</th>
                        <th class="text-center">Radiant Warmer</th>
                        <th class="text-center">หมายเหตุ</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($check_asset_ipd as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="right">{{ DatetimeThai($row->created_at) }} </td>   
                        <td align="right">{{ $row->hr_check }}</td>
                        <td align="right">{{ $row->asset1 }}</td>
                        <td align="right">{{ $row->asset2 }}</td>
                        <td align="right">{{ $row->asset3 }}</td>
                        <td align="right">{{ $row->asset4 }}</td> 
                        <td align="right">{{ $row->asset5 }}</td> 
                        <td align="right">{{ $row->asset6 }}</td> 
                        <td align="right">{{ $row->asset7 }}</td> 
                        <td align="right">{{ $row->asset8 }}</td> 
                        <td align="right">{{ $row->asset9 }}</td> 
                        <td align="right">{{ $row->asset10 }}</td> 
                        <td align="right">{{ $row->asset11 }}</td> 
                        <td align="right">{{ $row->asset12 }}</td> 
                        <td align="right">{{ $row->asset13 }}</td> 
                        <td align="right">{{ $row->outher }}</td> 
                    </tr>
                <?php $count++; ?>
                @endforeach
            </table> 
        </div>         
    </div>
</div>
<br>
<!--row-->
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>ผู้ป่วยใน VIP วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="check_asset_vip" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">วันที่-เวลา</th>
                        <th class="text-center">ผู้ตรวจสอบ</th>
                        <th class="text-center">Defibrillator</th>
                        <th class="text-center">Laryngoscope</th>
                        <th class="text-center">Ambu bag</th>
                        <th class="text-center">ETT+อุปกรณ์Away</th>
                        <th class="text-center">Oxygen+อุปกรณ์</th>
                        <th class="text-center">Auto CPR</th>
                        <th class="text-center">กล่องยาฉุกเฉิน</th>
                        <th class="text-center">Ventilator</th>
                        <th class="text-center">Ekg 12 lead</th>
                        <th class="text-center">Suction+อุปกรณ์</th>
                        <th class="text-center">เครื่องดมยา</th>
                        <th class="text-center">เครื่อง NST</th>
                        <th class="text-center">Radiant Warmer</th>
                        <th class="text-center">หมายเหตุ</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($check_asset_vip as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="right">{{ DatetimeThai($row->created_at) }}</td>   
                        <td align="right">{{ $row->hr_check }}</td>
                        <td align="right">{{ $row->asset1 }}</td>
                        <td align="right">{{ $row->asset2 }}</td>
                        <td align="right">{{ $row->asset3 }}</td>
                        <td align="right">{{ $row->asset4 }}</td> 
                        <td align="right">{{ $row->asset5 }}</td> 
                        <td align="right">{{ $row->asset6 }}</td> 
                        <td align="right">{{ $row->asset7 }}</td> 
                        <td align="right">{{ $row->asset8 }}</td> 
                        <td align="right">{{ $row->asset9 }}</td>
                        <td align="right">{{ $row->asset10 }}</td> 
                        <td align="right">{{ $row->asset11 }}</td> 
                        <td align="right">{{ $row->asset12 }}</td> 
                        <td align="right">{{ $row->asset13 }}</td> 
                        <td align="right">{{ $row->outher }}</td>  
                    </tr>
                <?php $count++; ?>
                @endforeach
            </table> 
        </div>         
    </div>
</div>
<br>
@endsection

<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#check_asset_er').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#check_asset_lr').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#check_asset_or').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#check_asset_hd').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#check_asset_ipd').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#check_asset_vip').DataTable();
    });
</script>