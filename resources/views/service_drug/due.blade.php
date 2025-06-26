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
            <a class="btn btn-success my-2 " href="{{ url('service_drug/due_excel') }}" target="_blank" type="submit">
            Excel
            </a>                    
        </div>      
    </div>
</div>
<!-- row -->
<div class="container-fluid">  
    <div class="card">         
        <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลการใช้ยา DUE ผู้ป่วยใน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>      
        <div class="card-body">
            <table id="due_ipd" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center">วันที่รับบริการ</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">AN</th>
                    <th class="text-center">ชื่อ-สกุล</th>
                    <th class="text-center">อายุ</th>                   
                    <th class="text-center">น้ำหนัก</th>
                    <th class="text-center">ยา</th>
                    <th class="text-center">วันที่สั่ง Lab sCr</th> 
                    <th class="text-center">ผล Lab sCr</th>                   
                    <th class="text-center">วันที่สั่งยา</th>
                    <th class="text-center">วิธีใช้ยา</th>                
                </tr>     
                </thead> 
                <?php $count = 1 ; ?> 
                @foreach($due_ipd as $row)          
                <tr>
                    <td align="right">{{ DateThai($row->vstdate) }} เวลา {{ $row->vsttime }}</td>
                    <td align="center">{{ $row->hn }}</td>
                    <td align="center">{{ $row->an }}</td>
                    <td align="left">{{ $row->ptname }}</td>
                    <td align="center">{{ $row->age_y }}</td>
                    <td align="center">{{ $row->bw }}</td>
                    <td align="center">{{ $row->drug }}</td>
                    <td align="right">{{ DateThai($row->report_date) }} เวลา {{ $row->report_time }}</td> 
                    <td align="right">{{ $row->lab_order_result }}</td>
                    <td align="right">{{ DateThai($row->rxdate) }} เวลา {{ $row->rxtime }}</td>
                    <td align="right">{{ $row->drugusage }}</td>                 
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
        $('#due_ipd').DataTable();
    });
</script>
