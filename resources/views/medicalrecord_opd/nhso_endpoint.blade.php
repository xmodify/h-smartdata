@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">

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
        <div class="col-md-3" >
            <button type="submit" class="btn btn-primary my-1 ">{{ __('ค้นหา') }}</button>
            <a class="btn btn-success" href="{{ url('medicalrecord_opd/nhso_endpoint_pull') }}">ดึงข้อมูล ปิดสิทธิ สปสช.</a>  
        </div>
    </div>
</form>
<!-- row -->
<div class="container-fluid">  
    <div class="card">         
        <div class="card-header bg-primary text-white"><strong>ข้อมูลการ ปิดสิทธิ์ สปสช.
            วันที่ {{DateThai($start_date)}} ถึง {{DateThai($end_date)}}</strong></div>      
        <div class="card-body">
            <table id="nhso_endpoint" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-primary">
                    <th class="text-center">ลำดับ</th>
                    <th class="text-center">วันที่รับบริการ</th>  
                    <th class="text-center">CID</th>
                    <th class="text-center">ชื่อ-สกุล</th>  
                    <th class="text-center">สิทธิ</th>
                    <th class="text-center">สถานะ</th>  
                    <th class="text-center">claimCode</th>  
                    <th class="text-center">claimType</th>  
                    <th class="text-center">claimTypeName</th>
                    <th class="text-center">sourceChannel</th> 
                    <th class="text-center">claimAuthen</th>             
                </tr>     
                </thead> 
                <?php $count = 1 ; ?> 
                @foreach($nhso_endpoint as $row)          
                <tr>
                    <td align="center">{{ $count }}</td> 
                    <td align="center">{{ DateThai($row->claimDate) }}</td>
                    <td align="center">{{ $row->personalId }}</td>
                    <td align="left">{{ $row->patientName }}</td>        
                    <td align="left">{{ $row->subInscl }} {{ $row->subInsclName }}</td> 
                    <td align="center">{{ $row->claimStatus }}</td>
                    <td align="center">{{ $row->claimCode }}</td>  
                    <td align="right">{{ $row->claimType }}</td> 
                    <td align="right">{{ $row->claimTypeName }}</td> 
                    <td align="right">{{ $row->sourceChannel }}</td> 
                    <td align="right">{{ $row->claimAuthen }}</td> 
                </tr>                
                <?php $count++; ?>
                @endforeach  
            </table>             
        </div>      
    </div>
 </div>

@endsection
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#nhso_endpoint').DataTable();
    });
</script>
