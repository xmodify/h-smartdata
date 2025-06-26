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
        <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วยโรคความดันที่ยังไม่ขึ้นทะเบียนคลินิก วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div class="card-body">
            <table id="ht_nonclinic" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center">ลำดับ</th>
                    <th class="text-center">วันที่รับบริการ</th>
                    <th class="text-center">Q</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">ชื่อ-สกุล</th>
                    <th class="text-center">BP</th>
                    <th class="text-center">อาการสำคัญ</th>
                    <th class="text-center">pdx</th>
                    <th class="text-center">dx0</th>
                    <th class="text-center">dx1</th>
                    <th class="text-center">dx2</th>
                    <th class="text-center">dx3</th>
                    <th class="text-center">dx4</th>
                    <th class="text-center">dx5</th>
                </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($ht_nonclinic as $row)
                <tr>
                    <td align="center">{{$count}}</td>
                    <td align="center">{{DateThai($row->vstdate)}} เวลา {{$row->vsttime}}</td>
                    <td align="center">{{$row->oqueue}}</td>
                    <td align="center">{{$row->hn}}</td>
                    <td align="left">{{$row->ptname}}</td>
                    <td align="center">{{$row->bp}}</td>
                    <td align="left">{{$row->cc}}</td>
                    <td align="center">{{$row->pdx}}</td>
                    <td align="center">{{$row->dx0}}</td>
                    <td align="center">{{$row->dx1}}</td>
                    <td align="center">{{$row->dx2}}</td>
                    <td align="center">{{$row->dx3}}</td>
                    <td align="center">{{$row->dx4}}</td>
                    <td align="center">{{$row->dx5}}</td>
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
        $('#ht_nonclinic').DataTable();
    });
</script>
