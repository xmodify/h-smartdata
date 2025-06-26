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
    <div class="card-header bg-primary text-white">ทะเบียนวัคซีน เปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                        <table id="vaccine" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">ID</th>
                                <th class="text-center">ชื่อวัคซีน</th>
                                <th class="text-center">รหัส</th>
                                <th class="text-center">กลุ่ม</th>
                                <th class="text-center">รหัสส่งออก</th>
                                <th class="text-center">ค่าบริการ</th>   
                                <th class="text-center">ราคา</th>   
                                <th class="text-center">ICD10</th>   
                                <th class="text-center">Update Moph</th>     
                                <th class="text-center">Multiple_doses</th>  
                                <th class="text-center">วิธีให้</th>                                              
                            </thead>   
                            @foreach($vaccine as $row)          
                            <tr>
                                <td align="center">{{ $row->person_vaccine_id }}</td>
                                <td align="left">{{ $row->vaccine_name }}</td>
                                <td align="center">{{ $row->vaccine_code }}</td>
                                <td align="center">{{ $row->vaccine_group }}</td>
                                <td align="center">{{ $row->export_vaccine_code }}</td>
                                <td align="left">{{ $row->item_name }}</td>
                                <td align="right">{{ number_format($row->unitprice,2) }}</td>
                                <td align="left">{{ $row->dx_icd10 }}</td>
                                <td align="center">{{ $row->update_moph_registry }}</td>
                                <td align="center">{{ $row->multiple_doses }} [{{ $row->dose_per_bottle }}]</td>
                                <td align="left">{{ $row->vaccine_route_name }}</td>
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
    <div class="card-header bg-primary text-white">ทะเบียนวัคซีน ปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                        <table id="vaccine_non_active" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">ID</th>
                                <th class="text-center">ชื่อวัคซีน</th>
                                <th class="text-center">รหัส</th>
                                <th class="text-center">กลุ่ม</th>
                                <th class="text-center">รหัสส่งออก</th>
                                <th class="text-center">ค่าบริการ</th>   
                                <th class="text-center">ราคา</th>   
                                <th class="text-center">ICD10</th>   
                                <th class="text-center">Update Moph</th>     
                                <th class="text-center">Multiple_doses</th>  
                                <th class="text-center">วิธีให้</th>                                              
                            </thead>   
                            @foreach($vaccine_non_active as $row)          
                            <tr>
                                <td align="center">{{ $row->person_vaccine_id }}</td>
                                <td align="left">{{ $row->vaccine_name }}</td>
                                <td align="center">{{ $row->vaccine_code }}</td>
                                <td align="center">{{ $row->vaccine_group }}</td>
                                <td align="center">{{ $row->export_vaccine_code }}</td>
                                <td align="left">{{ $row->item_name }}</td>
                                <td align="right">{{ number_format($row->unitprice,2) }}</td>
                                <td align="left">{{ $row->dx_icd10 }}</td>
                                <td align="center">{{ $row->update_moph_registry }}</td>
                                <td align="center">{{ $row->multiple_doses }} [{{ $row->dose_per_bottle }}]</td>
                                <td align="left">{{ $row->vaccine_route_name }}</td>
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
        $('#vaccine').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#vaccine_non_active').DataTable();
    });
</script>