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
    tr:nth-child(even){background-color: #f2f2f2}
</style>

@section('content')
<div class="container-fluid">
    <div class="card">
    <div class="card-header bg-primary text-white">ห้องตรวจ เปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                        <table id="depart" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัส</th>
                                <th class="text-center">ชื่อห้องตรวจ</th>
                                <th class="text-center">สาขา</th>
                                <th class="text-center">แผนก</th>
                                <th class="text-center">แผนก สปสช</th>
                                <th class="text-center">ห้องแสดงคิว</th>                 
                            </thead>   
                            @foreach($depart as $row)          
                            <tr>
                                <td align="center">{{ $row->depcode }}</td>
                                <td align="left">{{ $row->department }}</td>
                                <td align="left">{{ $row->hospital_department_name }}</td>
                                <td align="left">{{ $row->spclty_name }}</td>
                                <td align="center">{{ $row->nhso_code }}</td>
                                <td align="left">{{ $row->opd_qs_room_name }}</td>
                            </tr>
                            @endforeach 
                        </table> 
                    </div> 
                </div>        
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="card">
    <div class="card-header bg-primary text-white">ห้องตรวจ ปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                    <table id="depart_non_active" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัส</th>
                                <th class="text-center">ชื่อห้องตรวจ</th>
                                <th class="text-center">สาขา</th>
                                <th class="text-center">แผนก</th>
                                <th class="text-center">แผนก สปสช</th>
                                <th class="text-center">ห้องแสดงคิว</th>                 
                            </thead>   
                            @foreach($depart_non_active as $row)          
                            <tr>
                                <td align="center">{{ $row->depcode }}</td>
                                <td align="left">{{ $row->department }}</td>
                                <td align="left">{{ $row->hospital_department_name }}</td>
                                <td align="left">{{ $row->spclty_name }}</td>
                                <td align="center">{{ $row->nhso_code }}</td>
                                <td align="left">{{ $row->opd_qs_room_name }}</td>
                            </tr>
                            @endforeach 
                        </table> 
                    </div> 
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
        $('#depart').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#depart_non_active').DataTable();
    });
</script>