@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลเสียชีวิตผู้ป่วยทะเบียนคลินิกโรคเบาหวาน </div>
        <div class="card-body">
            <table id="death" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center">ลำดับ</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">PID</th>
                    <th class="text-center">ชื่อ-สกุล</th>
                    <th class="text-center">Regist Type</th>
                    <th class="text-center">Death ทะเบียน</th>
                    <th class="text-center">Death เวชระเบียน</th>
                    <th class="text-center">Death บัญชี 1</th>
                    <th class="text-center">สถานะคลินิก</th>
                    <th class="text-center">จำหน่าย</th>
                </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($dm_death as $row)
                <tr>
                    <td align="center">{{$count}}</td>
                    <td align="center">{{$row->hn}}</td>
                    <td align="center">{{$row->pid}}</td>
                    <td align="left">{{$row->fullname}}</td>
                    <td align="center">{{$row->house_regist_type_id}}</td>
                    <td align="center">{{$row->death}}</td>
                    <td align="center">{{$row->death_patient}}</td>
                    <td align="center">{{$row->death_person}}</td>   
                    <td align="center">{{$row->clinic_member_status_name}}</td>
                    <td align="center">{{$row->discharge}}</td>
                </tr>
                <?php $count++; ?>
                @endforeach
            </table>
        </div>
    </div>
</div>
<br>
@endsection
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>

<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#death').DataTable();
    });
</script>