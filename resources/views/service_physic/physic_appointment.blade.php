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
        <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วยนัดหมาย คลินิกกายภาพบำบัด วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div class="card-body">
            <table id="capd_appointment" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center">ลำดับ</th>
                    <th class="text-center">วันที่รับบริการ</th>
                    <th class="text-center">วันนัดถัดไป</th>
                    <th class="text-center">เวลานัด</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">ชื่อ-สกุล</th>
                    <th class="text-center">เบอร์โทรศัพท์</th>
                    <th class="text-center">ที่อยู่</th>
                    <th class="text-center">แพทย์ผู้นัด</th>
                    <th class="text-center">ห้องตรวจโรค</th>
                    <th class="text-center">หมายเหตุ</th>
                    <th class="text-center">ผู้ออกใบนัด</th>
                    <th class="text-center">สถานะ</th>
                </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($physic_appointment as $row)
                <tr>
                    <td align="center">{{$count}}</td>
                    <td align="left">{{DateThai($row->vstdate)}}</td>
                    <td align="left">{{DateThai($row->nextdate)}}</td>
                    <td align="left">{{$row->nexttime}} ถึง {{$row->nexttime_end}}</td>
                    <td align="center">{{$row->hn}}</td>
                    <td align="left">{{$row->ptname}}</td>
                    <td align="center">{{$row->mobile_phone_number}}</td>
                    <td align="left">{{$row->addr_name}}</td>
                    <td align="left">{{$row->doctor_name}}</td>
                    <td align="left">{{$row->department}}</td>
                    <td align="left">{{$row->note}}</td>
                    <td align="left">{{$row->app_user_name}}</td>
                    <td align="center">{{$row->oapp_status}}</td>
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
        $('#capd_appointment').DataTable();
    });
</script>
