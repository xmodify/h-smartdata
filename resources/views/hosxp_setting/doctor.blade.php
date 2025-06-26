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
    <div class="card-header bg-primary text-white">บุคลากรทางการแพทย์ เปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                        <table id="doctor" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัส</th>
                                <th class="text-center">ชื่อ-สกุล</th>
                                <th class="text-center">เพศ</th>
                                <th class="text-center">รหัสวิชาชีพ</th>
                                <th class="text-center">เลขใบประกอบวิชาชีพ</th>
                                <th class="text-center">ตำแหน่งหลัก</th>   
                                <th class="text-center">เลขบัตรประชาชน</th> 
                                <th class="text-center">Provider Type</th>
                                <th class="text-center">บังคับลง icd10</th> 
                                <th class="text-center">ตำแหน่งมาตรฐาน</th>   
                                <th class="text-center">โรงพยาบาล</th>
                                <th class="text-center">User</th>   
                                <th class="text-center">User.Tel</th>   
                                <th class="text-center">User.Logout</th> 
                            </thead>   
                            @foreach($doctor as $row)          
                            <tr>
                                <td align="left">{{ $row->code }}</td>
                                <td align="left">{{ $row->doctor_name }}</td>
                                <td align="left">{{ $row->sex }}</td>
                                <td align="left">{{ $row->council_code }}</td>
                                <td align="left">{{ $row->licenseno }}</td>
                                <td align="left">{{ $row->position_name }}</td>
                                <td align="center">{{ $row->cid }}</td>
                                <td align="left">{{ $row->provider_type }}</td>
                                <td align="center">{{ $row->force_diagnosis }}</td>
                                <td align="left">{{ $row->position_std_name }}</td>
                                <td align="left">{{ $row->hospital }}</td>
                                <td align="center">{{ $row->officer_active }}</td>
                                <td align="left">{{ $row->officer_phone }}</td> 
                                <td align="left">{{ $row->auto_lockout }}-{{ $row->auto_lockout_minute }}</td>  
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
    <div class="card-header bg-primary text-white">บุคลากรทางการแพทย์ ปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                        <table id="doctor_non_active" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัส</th>
                                <th class="text-center">ชื่อ-สกุล</th>
                                <th class="text-center">เพศ</th>
                                <th class="text-center">รหัสวิชาชีพ</th>
                                <th class="text-center">เลขใบประกอบวิชาชีพ</th>
                                <th class="text-center">ตำแหน่งหลัก</th>   
                                <th class="text-center">เลขบัตรประชาชน</th> 
                                <th class="text-center">Provider Type</th>
                                <th class="text-center">บังคับลง icd10</th> 
                                <th class="text-center">ตำแหน่งมาตรฐาน</th>   
                                <th class="text-center">โรงพยาบาล</th> 
                                <th class="text-center">User</th>   
                                <th class="text-center">User.Tel</th> 
                                <th class="text-center">User.Logout</th> 
                            </thead>   
                            @foreach($doctor_non_active as $row)          
                            <tr>
                                <td align="left">{{ $row->code }}</td>
                                <td align="left">{{ $row->doctor_name }}</td>
                                <td align="left">{{ $row->sex }}</td>
                                <td align="left">{{ $row->council_code }}</td>
                                <td align="left">{{ $row->licenseno }}</td>
                                <td align="left">{{ $row->position_name }}</td>
                                <td align="center">{{ $row->cid }}</td>
                                <td align="left">{{ $row->provider_type }}</td>
                                <td align="center">{{ $row->force_diagnosis }}</td>
                                <td align="left">{{ $row->position_std_name }}</td>
                                <td align="left">{{ $row->hospital }}</td>
                                <td align="center">{{ $row->officer_active }}</td>
                                <td align="left">{{ $row->officer_phone }}</td>    
                                <td align="left">{{ $row->auto_lockout }}-{{ $row->auto_lockout_minute }}</td>                        
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
        $('#doctor').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#doctor_non_active').DataTable();
    });
</script>