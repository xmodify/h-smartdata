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
</div>
<!-- row -->
<div class="container-fluid">  
    <div class="card">         
        <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วยตรวจโดยพยาบาลช่วงเวลา 19.00-08.00 น. วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>      
        <div class="card-body"> 
            <table id="nurse_diag" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center">วันที่มา</th> 
                    <th class="text-center">Q</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">ชื่อ-สกุลผู้ป่วย</th>                    
                    <th class="text-center">เวลาเริ่มตรวจ</th>
                    <th class="text-center">ความเร่งด่วน</th>
                    <th class="text-center">อาการสำคัญ</th>
                    <th class="text-center">โรคหลัก</th>
                    <th class="text-center">หัตถการ</th>
                    <th class="text-center">พยาบาลที่ตรวจ</th> 
                    <th class="text-center">แพทย์เวร</th>   
                </tr>     
                </thead> 
                <?php $count = 1 ; ?> 
                @foreach($nurse_diag as $row)          
                <tr>
                    <td align="center">{{ DateThai($row->vstdate) }} {{ $row->time }}</td>
                    <td align="center">{{ $row->oqueue }}</td>
                    <td align="center">{{ $row->hn }}</td>
                    <td align="left">{{ $row->ptname }}</td>
                    <td align="center">{{ $row->time }}</td>
                    <td align="right">{{ $row->emergency_type }}</td>
                    <td align="left">{{ $row->cc }}</td>
                    <td align="right">{{ $row->pdx }}</td>
                    <td align="right">{{ $row->er_list }}</td>
                    <td align="right">{{ $row->nurse_diag }}</td>
                    <td align="right">{{ $row->er_doctor }}</td>             
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
        $('#nurse_diag').DataTable();
    });
</script>
