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
    <div class="card-header bg-primary text-white">หัตถการผู้ป่วยใน เปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                        <table id="icd9" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัส</th>
                                <th class="text-center">ชื่อหัตถการ</th>
                                <th class="text-center">ราคาที่หัตถการ</th>
                                <th class="text-center">ราคาค่ารักษาพยาบาล</th>
                                <th class="text-center">หัถตการ</th>
                                <th class="text-center">ค่ารักษาพยาบาล</th>
                                <th class="text-center">ADP Code</th>
                                <th class="text-center">เปิดใช้งาน</th>
                                <th class="text-center">หมวดค่ารักษาพยาบาล</th>
                            </tr>     
                            </thead>   
                            @foreach($icd9 as $row)          
                            <tr>
                                <td align="right">{{ $row->ipt_oper_code }}</td>
                                <td align="left">{{ $row->name }}</td>
                                <td align="right">{{ number_format($row->price,2) }}</td>
                                <td align="right">{{ number_format($row->unitprice,2) }}</td>
                                <td align="left">{{ $row->icd9 }}</td>
                                <td align="left">{{ $row->item_name }}</td>
                                <td align="left">{{ $row->adp }}</td>
                                <td align="left">{{ $row->istatus }}</td>
                                <td align="left">{{ $row->income }}</td>
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
    <div class="card-header bg-primary text-white">หัตถการผู้ป่วยใน ปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                        <table id="icd9_non_active" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัส</th>
                                <th class="text-center">ชื่อหัตถการ</th>
                                <th class="text-center">ราคาที่หัตถการ</th>
                                <th class="text-center">ราคาค่ารักษาพยาบาล</th>
                                <th class="text-center">หัถตการ</th>
                                <th class="text-center">ค่ารักษาพยาบาล</th>
                                <th class="text-center">ADP Code</th>
                                <th class="text-center">เปิดใช้งาน</th>
                                <th class="text-center">หมวดค่ารักษาพยาบาล</th>
                            </tr>     
                            </thead>   
                            @foreach($icd9_non_active as $row)          
                            <tr>
                                <td align="right">{{ $row->ipt_oper_code }}</td>
                                <td align="left">{{ $row->name }}</td>
                                <td align="right">{{ number_format($row->price,2) }}</td>
                                <td align="right">{{ number_format($row->unitprice,2) }}</td>
                                <td align="left">{{ $row->icd9 }}</td>
                                <td align="left">{{ $row->item_name }}</td>
                                <td align="left">{{ $row->adp }}</td>
                                <td align="left">{{ $row->istatus }}</td>
                                <td align="left">{{ $row->income }}</td>
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
        $('#icd9').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#icd9_non_active').DataTable();
    });
</script>