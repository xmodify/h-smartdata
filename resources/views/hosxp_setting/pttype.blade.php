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
    <div class="card-header bg-primary text-white">สิทธิการักษา เปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                        <table id="pttype" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">สิทธิการรักษา</th>
                                <th class="text-center">การชำระเงิน</th>
                                <th class="text-center">กลุ่มค่าบริการ</th>
                                <th class="text-center">สิทธิมาตรฐาน</th>
                                <th class="text-center">รหัสมาตรฐาน INSCL</th>
                                <th class="text-center">รหัสมาตรฐาน สปสช.</th>   
                                <th class="text-center">รหัสส่งออก สปสช.</th>   
                            </thead>   
                            @foreach($pttype as $row)          
                            <tr>
                                <td align="left">{{ $row->pttype }}</td>
                                <td align="left">{{ $row->paidst }}</td>
                                <td align="left">{{ $row->pttype_price_group_name }}</td>
                                <td align="left">{{ $row->pcode }}</td>
                                <td align="left">{{ $row->hipdata_code }}</td>
                                <td align="left">{{ $row->provis_instype }}</td>
                                <td align="center">{{ $row->pttype_std_code }}</td>
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
    <div class="card-header bg-primary text-white">สิทธิการรักษา ปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                    <table id="pttype" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">สิทธิการรักษา</th>
                                <th class="text-center">การชำระเงิน</th>
                                <th class="text-center">กลุ่มค่าบริการ</th>
                                <th class="text-center">สิทธิมาตรฐาน</th>
                                <th class="text-center">รหัสมาตรฐาน INSCL</th>
                                <th class="text-center">รหัสมาตรฐาน สปสช.</th>    
                                <th class="text-center">รหัสส่งออก สปสช.</th>     
                            </thead>   
                            @foreach($pttype_non_use as $row)          
                            <tr>
                                <td align="left">{{ $row->pttype }}</td>
                                <td align="left">{{ $row->paidst }}</td>
                                <td align="left">{{ $row->pttype_price_group_name }}</td>
                                <td align="left">{{ $row->pcode }}</td>
                                <td align="left">{{ $row->hipdata_code }}</td>
                                <td align="left">{{ $row->provis_instype }}</td>
                                <td align="center">{{ $row->pttype_std_code }}</td>
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
        $('#pttype').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#pttype_non_use').DataTable();
    });
</script>