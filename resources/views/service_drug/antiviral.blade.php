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
        <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลการใช้ยาต้านไวรัสสิทธิประกันสังคม วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>      
        <div class="card-body">
            <table id="drug_opd_sss" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center" colspan = "7">ผู้ป่วยนอก</th>
                    <th class="text-center" > <a class="btn btn-outline-danger btn-sm" href="{{ url('service_drug/antiviral_opd_pdf')}}" target="_blank">พิมพ์</a>       </th>
                </tr>    
                <tr class="table-secondary">
                    <th class="text-center">วันที่ได้รับ</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">CID</th>
                    <th class="text-center">ชื่อ-สกุล</th>
                    <th class="text-center">อายุ</th>
                    <th class="text-center">สิทธิการรักษา</th>
                    <th class="text-center">รายการยา</th>
                    <th class="text-center">จำนวน</th>             
                </tr>     
                </thead> 
                <?php $count = 1 ; ?> 
                @foreach($drug_opd_sss as $row)          
                <tr>
                    <td align="right">{{DateThai($row->rxdate)}}</td>
                    <td align="center">{{$row->hn}}</td>
                    <td align="center">{{$row->cid}}</td>
                    <td align="left">{{$row->ptname}}</td>
                    <td align="center">{{$row->age_y}}</td>
                    <td align="left">{{$row->pttype}}</td>
                    <td align="left">{{$row->drug}}</td>
                    <td align="center">{{$row->qty}}</td>            
                </tr>                
                <?php $count++; ?>
                @endforeach  
            </table> 
            <hr>
            <table id="drug_ipd_sss" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center" colspan = "8">ผู้ป่วยใน</th>
                    <th class="text-center" > <a class="btn btn-outline-danger btn-sm" href="{{ url('service_drug/antiviral_ipd_pdf')}}" target="_blank">พิมพ์</a>       </th>
                </tr>   
                <tr class="table-secondary">
                    <th class="text-center">วันที่ได้รับ</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">AN</th>
                    <th class="text-center">CID</th>
                    <th class="text-center">ชื่อ-สกุล</th>
                    <th class="text-center">อายุ</th>
                    <th class="text-center">สิทธิการรักษา</th>
                    <th class="text-center">รายการยา</th>
                    <th class="text-center">จำนวน</th>             
                </tr>     
                </thead> 
                <?php $count = 1 ; ?> 
                @foreach($drug_ipd_sss as $row)          
                <tr>
                    <td align="right">{{DateThai($row->rxdate)}}</td>
                    <td align="center">{{$row->hn}}</td>
                    <td align="center">{{$row->an}}</td>
                    <td align="center">{{$row->cid}}</td>
                    <td align="left">{{$row->ptname}}</td>
                    <td align="center">{{$row->age_y}}</td>
                    <td align="left">{{$row->pttype}}</td>
                    <td align="left">{{$row->drug}}</td>
                    <td align="center">{{$row->qty}}</td>            
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
        $('#drug_opd_sss').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#drug_ipd_sss').DataTable();
    });
</script>
