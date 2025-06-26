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
        <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วยรอ Admit ที่ ER เกิน 2 ชั่วโมง วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>      
        <div class="card-body"> 
            <table id="waitingtime_admit" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center">วันที่มา</th> 
                    <th class="text-center">Q</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">ชื่อ-สกุลผู้ป่วย</th> 
                    <th class="text-center">เวลาเริ่มตรวจ</th>
                    <th class="text-center">ความเร่งด่วน</th>
                    <th class="text-center">แพทย์เวร</th>
                    <th class="text-center">เวลา Admit</th>
                    <th class="text-center">AN</th>   
                    <th class="text-center">ระยะเวลารอ Admit</th>   
                </tr>     
                </thead> 
                <?php $count = 1 ; ?> 
                @foreach($waitingtime_admit as $row)          
                <tr>
                    <td align="center">{{ DateThai($row->vstdate) }}</td>
                    <td align="center">{{ $row->oqueue }}</td>
                    <td align="center">{{ $row->hn }}</td>
                    <td align="left">{{ $row->ptname }}</td>                  
                    <td align="center">{{ $row->er_time }}</td>
                    <td align="left">{{ $row->emergency_type }}</td>
                    <td align="left">{{ $row->er_doctor }}</td>
                    <td align="center">{{ $row->admit_time }}</td>
                    <td align="center">{{ $row->an }}</td>
                    <td align="center">{{ $row->time_wait_admit }}</td>   
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
        $('#waitingtime_admit').DataTable();
    });
</script>
