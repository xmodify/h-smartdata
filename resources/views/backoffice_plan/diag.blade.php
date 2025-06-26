@extends('layouts.app')

@section('content')
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
<br>
<!-- row -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6"> 
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">20 อันดับโรคผู้ป่วยนอก วันที่ {{DateThai($start_date)}} ถึง {{DateThai($end_date)}}</div>
                <div class="card-body">    
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr class="table-secondary">
                            <th class="text-center">อันดับ</th> 
                            <th class="text-center">ชื่อโรค</th>
                            <th class="text-center">ชาย</th>
                            <th class="text-center">หญิง</th>
                            <th class="text-center">รวม</th>
                        </tr>     
                        </thead>                 
                        <?php $count = 1 ; ?>
                        @foreach($opddiag_top20 as $row)          
                        <tr>
                            <td align="center">{{ $count }}</td>                   
                            <td align="left">{{ $row->name }}</td>
                            <td align="right">{{ number_format($row->male) }}</td>
                            <td align="right">{{ number_format($row->female) }}</td>
                            <td align="right">{{ number_format($row->sum) }}</td>                        
                        </tr>
                        <?php $count++; ?>
                        @endforeach  
                    </table>
                </div>  
            </div>
        </div>
        <div class="col-md-6"> 
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">20 อันดับโรคผู้ป่วยใน วันที่ {{DateThai($start_date)}} ถึง {{DateThai($end_date)}}</div>
                <div class="card-body">    
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr class="table-secondary">
                            <th class="text-center">อันดับ</th> 
                            <th class="text-center">ชื่อโรค</th>
                            <th class="text-center">ชาย</th>
                            <th class="text-center">หญิง</th>
                            <th class="text-center">รวม</th>
                        </tr>     
                        </thead>                 
                        <?php $count = 1 ; ?>
                        @foreach($ipddiag_top20 as $row)          
                        <tr>
                            <td align="center">{{ $count }}</td>                   
                            <td align="left">{{ $row->name }}</td>
                            <td align="right">{{ number_format($row->male) }}</td>
                            <td align="right">{{ number_format($row->female) }}</td>
                            <td align="right">{{ number_format($row->sum) }}</td>                        
                        </tr>
                        <?php $count++; ?>
                        @endforeach  
                    </table>
                </div>  
            </div>
        </div>
    </div>
</div>   
<br>
<!-- row -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12"> 
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยนอกกลุ่มสาเหตุ (21 กลุ่มโรค) (รง.504) วันที่ {{DateThai($start_date)}} ถึง {{DateThai($end_date)}}</div>        
                <div class="card-body"> 
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr class="table-secondary">
                            <th class="text-center">อันดับ</th>
                            <th class="text-center">ชื่อโรค</th>
                            <th class="text-center">ชาย</th>
                            <th class="text-center">หญิง</th>
                            <th class="text-center">รวม</th>
                        </tr>     
                        </thead>                 
                        <?php $count = 1 ; ?>
                        @foreach($diag_504 as $row)          
                        <tr>
                            <td align="center">{{ $count }}</td>                   
                            <td align="left">{{ $row->name }}</td>
                            <td align="right">{{ number_format($row->male) }}</td>
                            <td align="right">{{ number_format($row->female) }}</td>
                            <td align="right">{{ number_format($row->sum) }}</td>                        
                        </tr>
                        <?php $count++; ?>
                        @endforeach  
                    </table>  
                </div>              
            </div>
        </div>
    </div>
</div>   
<br> 
<!-- row -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">  
            <div class="card">                         
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยในจำแนกตามกลุ่มโรค (75 กลุ่มโรค) (รง.505) วันที่ {{DateThai($start_date)}} ถึง {{DateThai($end_date)}}</div>                                                                           
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr class="table-secondary">
                            <th class="text-center">อันดับ</th>
                            <th class="text-center">ชื่อโรค</th>
                            <th class="text-center">ชาย</th>
                            <th class="text-center">หญิง</th>
                            <th class="text-center">รวม</th>
                        </tr>     
                        </thead>                 
                        <?php $count = 1 ; ?>
                        @foreach($diag_505 as $row)          
                        <tr>
                            <td align="center">{{ $count }}</td>                   
                            <td align="left">{{ $row->name }}</td>
                            <td align="right">{{ number_format($row->male) }}</td>
                            <td align="right">{{ number_format($row->female) }}</td>
                            <td align="right">{{ number_format($row->sum) }}</td>                        
                        </tr>
                        <?php $count++; ?>
                        @endforeach   
                    </table>
            </div>                    
        </div>
    </div>
</div>
<br> 
<!-- row -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12"> 
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยนอกกลุ่มโรคที่ต้องเฝ้าระวัง (รง.506) วันที่ {{DateThai($start_date)}} ถึง {{DateThai($end_date)}}</div>                        
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr class="table-secondary">
                            <th class="text-center">อันดับ</th>
                            <th class="text-center">ชื่อโรค</th>
                            <th class="text-center">ชาย</th>
                            <th class="text-center">หญิง</th>
                            <th class="text-center">รวม</th>
                        </tr>     
                        </thead>                 
                        <?php $count = 1 ; ?>
                        @foreach($diag_506 as $row)          
                        <tr>
                            <td align="center">{{ $count }}</td>                   
                            <td align="left">{{ $row->name }}</td>
                            <td align="right">{{ number_format($row->male) }}</td>
                            <td align="right">{{ number_format($row->female) }}</td>
                            <td align="right">{{ number_format($row->sum) }}</td>                        
                        </tr>
                        <?php $count++; ?>
                        @endforeach  
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>   
<br>
@endsection