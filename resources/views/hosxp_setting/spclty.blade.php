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
    <div class="card-header bg-primary text-white">แผนก เปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                        <table id="spclty" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัส</th>
                                <th class="text-center">ชื่อแผนก</th>
                                <th class="text-center">ovstostlink</th>
                                <th class="text-center">Spname</th>
                                <th class="text-center">Shortname</th>
                                <th class="text-center">ill_visit</th>   
                                <th class="text-center">nhso_code</th>   
                                <th class="text-center">provis_code</th>   
                                <th class="text-center">ไม่คิดค่าธรรมเนียม</th> 
                                <th class="text-center">ไม่ส่ง 43 แฟ้ม</th>                     
                            </thead>   
                            @foreach($spclty as $row)          
                            <tr>
                                <td align="center">{{ $row->spclty }}</td>
                                <td align="left">{{ $row->name }}</td>
                                <td align="center">{{ $row->ovstostlink }}</td>
                                <td align="center">{{ $row->spname }}</td>
                                <td align="center">{{ $row->shortname }}</td>
                                <td align="center">{{ $row->ill_visit }}</td>
                                <td align="center">{{ $row->nhso_code }}</td>
                                <td align="center">{{ $row->provis_code }}</td>
                                <td align="center">{{ $row->no_service_charge }}</td> 
                                <td align="center">{{ $row->no_export_43 }}</td>                                   
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
    <div class="card-header bg-primary text-white">แผนก ปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                    <table id="spclty_non_active" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัส</th>
                                <th class="text-center">ชื่อแผนก</th>
                                <th class="text-center">ovstostlink</th>
                                <th class="text-center">Spname</th>
                                <th class="text-center">Shortname</th>
                                <th class="text-center">ill_visit</th>   
                                <th class="text-center">nhso_code</th>   
                                <th class="text-center">provis_code</th>   
                                <th class="text-center">ไม่คิดค่าธรรมเนียม</th> 
                                <th class="text-center">ไม่ส่ง 43 แฟ้ม</th>                     
                            </thead>   
                            @foreach($spclty_non_active as $row)          
                            <tr>
                                <td align="center">{{ $row->spclty }}</td>
                                <td align="left">{{ $row->name }}</td>
                                <td align="center">{{ $row->ovstostlink }}</td>
                                <td align="center">{{ $row->spname }}</td>
                                <td align="center">{{ $row->shortname }}</td>
                                <td align="center">{{ $row->ill_visit }}</td>
                                <td align="center">{{ $row->nhso_code }}</td>
                                <td align="center">{{ $row->provis_code }}</td>
                                <td align="center">{{ $row->no_service_charge }}</td> 
                                <td align="center">{{ $row->no_export_43 }}</td>                                   
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
        $('#spclty').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#spclty_non_active').DataTable();
    });
</script>