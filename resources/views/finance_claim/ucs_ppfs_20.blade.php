@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
<style>
    table {
    border-collapse: collapse;
    border-spacing: 0;
    width: 100%;
    border: 1px solid #ddd;
    }
    th, td {
    padding: 8px;
    }
</style>

@section('content')
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
<div class="container-fluid">
</div>
<div class="container-fluid">
    <div class="card border-success">
        <div class="card-header bg-success text-white">บริการเคลือบฟลูออไรด์กลุ่มสี่ยง [ส่ง FDH] วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div class="card-body">  
            <div style="overflow-x:auto;">               
                <table id="eclaim_fdh" class="table table-bordered table-striped my-3">
                    <thead>
                    <tr class="table-success">
                        <th class="text-center">ลำดับ</th>
                        <th class="text-center">วันที่รับบริการ</th>
                        <th class="text-center">Queue</th>
                        <th class="text-center">HN</th>
                        <th class="text-center">ชื่อ-สกุล</th>
                        <th class="text-center">อายุ</th>
                        <th class="text-center">สิทธิ</th>
                        <th class="text-center">ICD10</th>
                        <th class="text-center">AuthenCode</th>
                        <th class="text-center">ProjectCode</th>
                        <th class="text-center">ค่ารักษาทั้งหมด</th>  
                        <th class="text-center">รายการเรียกเก็บ</th>                   
                        <th class="text-center">ราคาเรียกเก็บ</th>
                        <th class="text-center">Upload Eclaim</th>
                        <th class="text-center">Upload FDH</th> 
                        <th class="text-center">ประสงค์เบิก</th> 
                        <th class="text-center">Rep NHSO</th> 
                        <th class="text-center">Error</th> 
                        <th class="text-center">STM ชดเชย</th> 
                        <th class="text-center">ผลต่าง</th> 
                        <th class="text-center">REP</th> 
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    <?php $sum_sum_price = 0 ; ?>  
                    <?php $sum_receive_pp = 0 ; ?>  
                    @foreach($eclaim_fdh as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>
                        <td align="right">{{ DateThai($row->vstdate) }}</td>
                        <td align="center">{{ $row->oqueue }}</td>
                        <td align="center">{{ $row->hn }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->age_y }}</td>
                        <td align="center">{{ $row->pttype }}</td>
                        <td align="right">{{ $row->pdx }}</td>
                        <td align="center">{{ $row->auth_code }}</td>
                        <td align="center">{{ $row->project }}</td>                    
                        <td align="center">{{ number_format($row->income,2) }}</td>
                        <td align="left">{{ $row->nondrug }}</td>
                        <td align="center">{{ number_format($row->sum_price,2) }}</td>
                        <td align="center">{{ $row->eclaim }}</td> 
                        <td align="center">{{ $row->fdh }}</td>  
                        <td align="center" @if($row->request_funds == 'Y') style="color:green"
                            @elseif($row->request_funds == 'N') style="color:red" @endif>
                            <strong>{{ $row->request_funds }}</strong></td>     
                        <td align="right">{{ number_format($row->rep_nhso,2) }}</td>
                        <td align="center">{{ $row->rep_error }}</td>
                        <td align="center">{{ number_format($row->receive_pp,2) }}</td>
                        <td align="center">{{ number_format($row->receive_pp-$row->sum_price,2) }}</td>
                        <td align="center">{{ $row->repno }}</td>
                    </tr>
                    <?php $count++; ?>
                    <?php $sum_sum_price += $row->sum_price ; ?>
                    <?php $sum_receive_pp += $row->receive_pp ; ?>
                    @endforeach
                </table>
                <div class="text-center text-primary">
                    <h4>
                        รวมราคาเรียกเก็บทั้งหมด <strong>{{number_format($sum_sum_price,2)}} </strong>บาท |
                        ชดเชยทั้งหมด <strong>{{number_format($sum_receive_pp,2)}} </strong>บาท |
                        ส่วนต่าง <strong>{{number_format($sum_receive_pp-$sum_sum_price,2)}} </strong>บาท
                    </h4>
                </div>
                <br>
            </div>  
        </div>
    </div>
 </div>
 <br>
 <div class="container-fluid">
    <div class="card border-Secondary">
        <div class="card-header bg-Secondary text-white">บริการเคลือบฟลูออไรด์กลุ่มสี่ยง [รอยืนยันส่ง FDH] วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div class="card-body">  
            <div style="overflow-x:auto;">            
                <table id="eclaim" class="table table-bordered table-striped my-3">
                    <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>
                        <th class="text-center">วันที่รับบริการ</th>
                        <th class="text-center">Queue</th>
                        <th class="text-center">HN</th>
                        <th class="text-center">ชื่อ-สกุล</th>
                        <th class="text-center">อายุ</th>
                        <th class="text-center">สิทธิ</th>
                        <th class="text-center">ICD10</th>
                        <th class="text-center">AuthenCode</th>
                        <th class="text-center">Project Code</th>                        
                        <th class="text-center">ค่ารักษาทั้งหมด</th>
                        <th class="text-center">รายการเรียกเก็บ</th>
                        <th class="text-center">ราคาเรียกเก็บ</th>
                        <th class="text-center">Upload Eclaim</th>
                        <th class="text-center">Upload FDH</th> 
                        <th class="text-center">ประสงค์เบิก</th> 
                        <th class="text-center">Rep NHSO</th> 
                        <th class="text-center">Error</th> 
                        <th class="text-center">STM ชดเชย</th> 
                        <th class="text-center">ผลต่าง</th> 
                        <th class="text-center">REP</th> 
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    <?php $sum_sum_price = 0 ; ?>  
                    <?php $sum_receive_pp = 0 ; ?>  
                    @foreach($eclaim as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>
                        <td align="right">{{ DateThai($row->vstdate) }}</td>
                        <td align="center">{{ $row->oqueue }}</td>
                        <td align="center">{{ $row->hn }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->age_y }}</td>
                        <td align="center">{{ $row->pttype }}</td>
                        <td align="right">{{ $row->pdx }}</td>
                        <td align="center">{{ $row->auth_code }}</td>
                        <td align="center">{{ $row->project }}</td>                        
                        <td align="center">{{ number_format($row->income,2) }}</td>
                        <td align="left">{{ $row->nondrug }}</td>
                        <td align="center">{{ number_format($row->sum_price,2) }}</td>
                        <td align="center">{{ $row->eclaim }}</td> 
                        <td align="center">{{ $row->fdh }}</td>   
                        <td align="center" @if($row->request_funds == 'Y') style="color:green"
                            @elseif($row->request_funds == 'N') style="color:red" @endif>
                            <strong>{{ $row->request_funds }}</strong></td>   
                        <td align="right">{{ number_format($row->rep_nhso,2) }}</td>
                        <td align="center">{{ $row->rep_error }}</td>
                        <td align="center">{{ number_format($row->receive_pp,2) }}</td>
                        <td align="center">{{ number_format($row->receive_pp-$row->sum_price,2) }}</td>
                        <td align="center">{{ $row->repno }}</td>
                    </tr>
                    <?php $count++; ?>
                    <?php $sum_sum_price += $row->sum_price ; ?>
                    <?php $sum_receive_pp += $row->receive_pp ; ?>
                    @endforeach
                </table>
                <div class="text-center text-primary">
                    <h4>
                        รวมราคาเรียกเก็บทั้งหมด <strong>{{number_format($sum_sum_price,2)}} </strong>บาท |
                        ชดเชยทั้งหมด <strong>{{number_format($sum_receive_pp,2)}} </strong>บาท |
                        ส่วนต่าง <strong>{{number_format($sum_receive_pp-$sum_sum_price,2)}} </strong>บาท
                    </h4>
                </div>
            </div>   
        </div>
    </div>
 </div>
@endsection
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#eclaim_fdh').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#eclaim').DataTable();
    });
</script>
