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
    <div class="row">                          
    <div class="col-md-8" align="left"> 
        <h5 class="card-title text-primary"></h5>
    </div>                 
    <div class="col-md-4" align="right">
        <a class="btn btn-success my-2 " href="{{ url('hosxp_setting/drug_all_excel') }}" target="_blank" type="submit">
        Excel
        </a>                    
    </div>      
</div>

    <div class="card">
    <div class="card-header bg-primary text-white">ทะเบียนยาทั้งหมด เปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                        <table id="drug" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัส</th>
                                <th class="text-center">ชื่อยา</th>
                                <th class="text-center">ชื่อสามัญ</th>                                
                                <th class="text-center">ความแรง</th>
                                <th class="text-center">หน่วยนับ</th>
                                <th class="text-center">ชื่อการค้า</th>   
                                <th class="text-center">ราคาทุน</th>   
                                <th class="text-center">ราคาขาย</th>   
                                <th class="text-center">Dosage Form</th>  
                                <th class="text-center">หมวดค่ารักษาพยาบาล</th> 
                                <th class="text-center">บัญชี</th>
                                <th class="text-center">TMT GP Name</th>        
                                <th class="text-center">TMT TP Name</th>  
                                <th class="text-center">รหัส TMT สกส.</th>    
                                <th class="text-center">ชื่อ TPU สกส.</th>    
                                <th class="text-center">TTMT</th>  
                                <th class="text-center">สรรพคุณ</th> 
                                <th class="text-center">ฉลากช่วย</th>                                                    
                            </thead>                          
                            @foreach($drug as $row)          
                            <tr>                          
                                <td align="center">{{ $row->icode }}</td>
                                <td align="left">{{ $row->name }}</td>
                                <td align="left">{{ $row->generic_name }}</td>
                                <td align="left">{{ $row->strength }}</td>
                                <td align="left">{{ $row->units }}</td>
                                <td align="left">{{ $row->sks_trade_name }}</td>
                                <td align="right">{{ number_format($row->unitcost,2) }}</td>
                                <td align="right">{{ number_format($row->unitprice,2) }}</td>                                                    
                                <td align="left">{{ $row->dosageform }}</td>
                                <td align="left">{{ $row->income_name }}</td>
                                <td align="left">{{ $row->drugaccount }}</td> 
                                <td align="left">{{ $row->gp_name }}</td>
                                <td align="left">{{ $row->tp_name }}</td>
                                <td align="left">{{ $row->sks_drug_code }}</td>
                                <td align="left">{{ $row->sks_trade_name }}</td>
                                <td align="left">{{ $row->ttmt_code }}</td>
                                <td align="left">{{ $row->therapeutic }}</td>
                                <td align="left">{{ $row->hinttext }}</td>
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
    <div class="card-header bg-primary text-white">ทะเบียนยาทั้งหมด ปิดการใช้งาน</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                        <table id="drug_non_active" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัส</th>
                                <th class="text-center">ชื่อยา</th>
                                <th class="text-center">ชื่อสามัญ</th>                                
                                <th class="text-center">ความแรง</th>
                                <th class="text-center">หน่วยนับ</th>
                                <th class="text-center">ชื่อการค้า</th>   
                                <th class="text-center">ราคาทุน</th>   
                                <th class="text-center">ราคาขาย</th>   
                                <th class="text-center">Dosage Form</th>  
                                <th class="text-center">หมวดค่ารักษาพยาบาล</th> 
                                <th class="text-center">บัญชี</th>
                                <th class="text-center">TMT GP Name</th>        
                                <th class="text-center">TMT TP Name</th>  
                                <th class="text-center">รหัส TMT สกส.</th>    
                                <th class="text-center">ชื่อ TPU สกส.</th>    
                                <th class="text-center">TTMT</th>  
                                <th class="text-center">สรรพคุณ</th> 
                                <th class="text-center">ฉลากช่วย</th>                                              
                            </thead> 
                            @foreach($drug_non_active as $row)          
                            <tr>
                               <td align="center">{{ $row->icode }}</td>
                                <td align="left">{{ $row->name }}</td>
                                <td align="left">{{ $row->generic_name }}</td>
                                <td align="left">{{ $row->strength }}</td>
                                <td align="left">{{ $row->units }}</td>
                                <td align="left">{{ $row->sks_trade_name }}</td>
                                <td align="right">{{ number_format($row->unitcost,2) }}</td>
                                <td align="right">{{ number_format($row->unitprice,2) }}</td>                                                    
                                <td align="left">{{ $row->dosageform }}</td>
                                <td align="left">{{ $row->income_name }}</td>
                                <td align="left">{{ $row->drugaccount }}</td> 
                                <td align="left">{{ $row->gp_name }}</td>
                                <td align="left">{{ $row->tp_name }}</td>
                                <td align="left">{{ $row->sks_drug_code }}</td>
                                <td align="left">{{ $row->sks_trade_name }}</td>
                                <td align="left">{{ $row->ttmt_code }}</td>
                                <td align="left">{{ $row->therapeutic }}</td>
                                <td align="left">{{ $row->hinttext }}</td>
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
        $('#drug').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#drug_non_active').DataTable();
    });
</script>