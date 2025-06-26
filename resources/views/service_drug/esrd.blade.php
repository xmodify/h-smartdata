@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">

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
    <div class="row">                          
        <div class="col-md-8" align="left"> 
            <h5 class="card-title text-primary"></h5>
        </div>                 
        <div class="col-md-4" align="right">
            <a class="btn btn-success my-2 " href="{{ url('service_drug/esrd_excel') }}" target="_blank" type="submit">
            Excel
            </a>                    
        </div>      
    </div>
</div>
<!-- row -->
<div class="container-fluid">  
    <div class="card">         
        <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลการใช้ยา ESRD ผู้ป่วยนอก วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>      
            <table id="esrd_opd" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center">สิทธิการรักษา</th>
                    <th class="text-center">รหัสยา</th>
                    <th class="text-center">ชื่อยา</th>
                    <th class="text-center">ชื่อยาสามัญ</th>
                    <th class="text-center">ความแรง</th>
                    <th class="text-center">จำนวน HN</th>
                    <th class="text-center">จำนวน VISIT</th>                   
                    <th class="text-center">จำนวนยา</th>
                    <th class="text-center">ต้นทุน</th>
                    <th class="text-center">มูลค่า</th>
                </tr>     
                </thead> 
                <?php $count = 1 ; ?> 
                <?php $sum_hn = 0 ; ?>
                <?php $sum_visit = 0 ; ?>              
                <?php $sum_qty = 0 ; ?>
                <?php $sum_cost = 0 ; ?>
                <?php $sum_price = 0 ; ?>
                @foreach($esrd_opd as $row)          
                <tr>
                    <td align="center">{{ $row->hipdata_code }}</td> 
                    <td align="center">{{ $row->icode }}</td>
                    <td align="left">{{ $row->name }}</td>
                    <td align="left">{{ $row->generic_name }}</td>
                    <td align="center">{{ $row->strength }}</td>
                    <td align="right">{{ $row->hn }}</td> 
                    <td align="right">{{ $row->visit }}</td>
                    <td align="right">{{ $row->qty }}</td>
                    <td align="right">{{ number_format($row->cost,2) }}</td>
                    <td align="right">{{ number_format($row->price,2) }}</td>                         
                </tr>                
                <?php $count++; ?>
                <?php $sum_hn += $row->hn ; ?>
                <?php $sum_visit += $row->visit ; ?>         
                <?php $sum_qty += $row->qty ; ?>
                <?php $sum_cost += $row->cost ; ?>
                <?php $sum_price += $row->price ; ?>
                @endforeach  
                <tr>   
                    <!-- <td colspan= "5" align="right"><strong>รวม </strong></td> -->
                    <td align="right"><strong></strong></td>
                    <td align="right"><strong></strong></td>
                    <td align="right"><strong></strong></td>
                    <td align="right"><strong></strong></td>
                    <td align="right"><strong></strong></td>      
                    <td align="right"><strong>{{ number_format($sum_hn) }}</strong></td>
                    <td align="right"><strong>{{ number_format($sum_visit) }}</strong></td>                
                    <td align="right"><strong>{{ number_format($sum_qty) }}</strong></td>     
                    <td align="right"><strong>{{ number_format($sum_cost,2) }}</strong></td>
                    <td align="right"><strong>{{ number_format($sum_price,2) }}</strong></td>                        
                </tr>
            </table>         
    </div>
</div>
<br>
<!-- row -->
<div class="container-fluid">  
    <div class="card">         
        <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลการใช้ยา ESRD ผู้ป่วยใน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>      
            <table id="esrd_ipd" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center">สิทธิการรักษา</th>
                    <th class="text-center">รหัสยา</th>
                    <th class="text-center">ชื่อยา</th>
                    <th class="text-center">ชื่อยาสามัญ</th>
                    <th class="text-center">ความแรง</th>
                    <th class="text-center">จำนวน HN</th>
                    <th class="text-center">จำนวน AN</th>                   
                    <th class="text-center">จำนวนยา</th>
                    <th class="text-center">ต้นทุน</th>
                    <th class="text-center">มูลค่า</th>
                </tr>     
                </thead> 
                <?php $count = 1 ; ?> 
                <?php $sum_hn = 0 ; ?>
                <?php $sum_an = 0 ; ?>              
                <?php $sum_qty = 0 ; ?>
                <?php $sum_cost = 0 ; ?>
                <?php $sum_price = 0 ; ?>
                @foreach($esrd_ipd as $row)          
                <tr>
                    <td align="center">{{ $row->hipdata_code }}</td> 
                    <td align="center">{{ $row->icode }}</td>
                    <td align="left">{{ $row->name }}</td>
                    <td align="left">{{ $row->generic_name }}</td>
                    <td align="center">{{ $row->strength }}</td>
                    <td align="right">{{ $row->hn }}</td> 
                    <td align="right">{{ $row->an }}</td>
                    <td align="right">{{ $row->qty }}</td>
                    <td align="right">{{ number_format($row->cost,2) }}</td>
                    <td align="right">{{ number_format($row->price,2) }}</td>                         
                </tr>                
                <?php $count++; ?>
                <?php $sum_hn += $row->hn ; ?>
                <?php $sum_an += $row->an ; ?>         
                <?php $sum_qty += $row->qty ; ?>
                <?php $sum_cost += $row->cost ; ?>
                <?php $sum_price += $row->price ; ?>
                @endforeach  
                <tr>   
                    <!-- <td colspan= "5" align="right"><strong>รวม </strong></td> -->
                    <td align="right"><strong></strong></td>
                    <td align="right"><strong></strong></td>
                    <td align="right"><strong></strong></td>
                    <td align="right"><strong></strong></td>
                    <td align="right"><strong></strong></td>      
                    <td align="right"><strong>{{ number_format($sum_hn) }}</strong></td>
                    <td align="right"><strong>{{ number_format($sum_an) }}</strong></td>                
                    <td align="right"><strong>{{ number_format($sum_qty) }}</strong></td>     
                    <td align="right"><strong>{{ number_format($sum_cost,2) }}</strong></td>
                    <td align="right"><strong>{{ number_format($sum_price,2) }}</strong></td>                        
                </tr>
            </table>         
    </div>
 </div>
@endsection
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#esrd_opd').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#esrd_ipd').DataTable();
    });
</script>
