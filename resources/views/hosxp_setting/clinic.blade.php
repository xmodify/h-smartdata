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
    <div class="card-header bg-primary text-white">คลินิก เปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                        <table id="clinic" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัส</th>
                                <th class="text-center">ชื่อคลินิก</th>
                                <th class="text-center">โรคเรื้อรัง</th>
                                <th class="text-center">ไม่ส่ง 43 แฟ้ม</th>
                                <th class="text-center">ประเภทโรค</th>
                                <th class="text-center">ประเภทกิจกรรม</th>   
                                <th class="text-center">โรคเรื้อรัง ปสก.</th> 
                                <th class="text-center">คิว Kiosk</th>
                                <th class="text-center">นัด Kiosk</th> 
                            </thead>   
                            @foreach($clinic as $row)          
                            <tr>
                                <td align="center">{{ $row->clinic }}</td>
                                <td align="left">{{ $row->name }}</td>
                                <td align="center">{{ $row->chronic }}</td>
                                <td align="center">{{ $row->no_export }}</td>
                                <td align="left">{{ $row->hosxp_clinic_type_name }}</td>
                                <td align="left">{{ $row->oapp_activity_name }}</td>
                                <td align="left">{{ $row->sss_clinic_name }}</td>
                                <td align="left">{{ $row->opd_qs_room_name }}</td>
                                <td align="left">{{ $row->button_caption }}</td>                                
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
    <div class="card-header bg-primary text-white">คลินิก ปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                    <table id="clinic_non_active" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัส</th>
                                <th class="text-center">ชื่อคลินิก</th>
                                <th class="text-center">โรคเรื้อรัง</th>
                                <th class="text-center">ไม่ส่ง 43 แฟ้ม</th>
                                <th class="text-center">ประเภทโรค</th>
                                <th class="text-center">ประเภทกิจกรรม</th>   
                                <th class="text-center">โรคเรื้อรัง ปสก.</th> 
                                <th class="text-center">คิว Kiosk</th>
                                <th class="text-center">นัด Kiosk</th> 
                            </thead>   
                            @foreach($clinic_non_active as $row)          
                            <tr>
                                <td align="center">{{ $row->clinic }}</td>
                                <td align="left">{{ $row->name }}</td>
                                <td align="center">{{ $row->chronic }}</td>
                                <td align="center">{{ $row->no_export }}</td>
                                <td align="left">{{ $row->hosxp_clinic_type_name }}</td>
                                <td align="left">{{ $row->oapp_activity_name }}</td>
                                <td align="left">{{ $row->sss_clinic_name }}</td>
                                <td align="left">{{ $row->opd_qs_room_name }}</td>
                                <td align="left">{{ $row->button_caption }}</td>                                
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
        $('#clinic').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#clinic_non_active').DataTable();
    });
</script>