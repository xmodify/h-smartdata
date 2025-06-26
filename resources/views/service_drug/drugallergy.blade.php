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
</style>
@section('content')
<div class="container-fluid">  
    <div class="row">                          
        <div class="col-md-8" align="left"> 
            <h5 class="card-title text-primary"></h5>
        </div>                 
        <div class="col-md-4" align="right">
            <a class="btn btn-success my-2 " href="{{ url('service_drug/drugallergy_excel') }}" target="_blank" type="submit">
            Excel
            </a>                    
        </div>      
    </div>
</div>
<!-- row -->
<div class="container-fluid">  
    <div class="card">         
        <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลการแพัยาแยก รพสต.</div> 
            <div class="card-body"> 
                <div style="overflow-x:auto;">     
                    <table id="drugallergy" class="table table-bordered table-striped my-3">
                        <thead>
                        <tr class="table-secondary">
                            <th class="text-center">ลำดับ</th>
                            <th class="text-center">CID</th>  
                            <th class="text-center">HN</th>
                            <th class="text-center">ชื่อ-สกุล</th> 
                            <th class="text-center">วันที่</th>
                            <th class="text-center">ชื่อยาที่แพ้</th>
                            <th class="text-center">อาการ</th>
                            <th class="text-center">ความร้ายแรง</th>
                            <th class="text-center">ผลที่เกิดขึ้น</th>
                            <th class="text-center">จำนวนรายการ</th>
                            <th class="text-center">รพสต.</th>           
                        </tr>     
                        </thead> 
                        <?php $count = 1 ; ?> 
                        @foreach($drugallergy as $row)          
                        <tr>
                            <td align="center">{{ $count }}</td>
                            <td align="center">{{ $row->cid }}</td>
                            <td align="center">{{ $row->hn }}</td>
                            <td align="left">{{ $row->ptname }}</td> 
                            <td align="left">{{ DateThai($row->report_date) }}</td> 
                            <td align="left">{{ $row->drugallergy }}</td>
                            <td align="left">{{ $row->symptom }}</td>
                            <td align="left">{{ $row->seiousness_name }}</td>
                            <td align="left">{{ $row->result_name }}</td>
                            <td align="center">{{ $row->agent_count }}</td>
                            <td align="left">{{ $row->pcu }}</td>
                        <?php $count++; ?>
                        @endforeach  
                    </table>  
                </div>   
            </div>           
        </div>
    </div>
</div>
<br>
@endsection
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#drugallergy').DataTable();
    });
</script>
