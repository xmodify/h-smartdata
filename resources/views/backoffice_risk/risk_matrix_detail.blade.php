@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>รายละเอียดความเสี่ยงตาม Risk Matrix</strong></div>
        <div class="card-body">
          <table id = "risk_matrix_detail" class="table table-bordered table-striped">
            <thead>
            <tr class="table-secondary">
                <th class="text-center">รหัส</th>
                <th class="text-center">วันที่เกิดความเสี่ยง</th>
                <th class="text-center">ระดับความรุนแรง</th>
                <th class="text-center">Consequence Score</th>
                <th class="text-center">Likelihood Score</th>
                <th class="text-center">โปรแกรมหลัก</th>
                <th class="text-center">โปรแกรมย่อย</th>
                <th class="text-center">รายละเอียด</th>
                <th class="text-center">วันที่ทบทวน</th>
            </tr>     
            </thead> 
            @foreach($risk_matrix_detail as $row)          
            <tr>
                <td align="center">{{ $row->id }}</td>
                <td align="left">{{ DateThai($row->RISKREP_STARTDATE) }}</td> 
                <td align="center">{{ $row->RISK_REP_LEVEL_NAME }}</td>      
                <td align="center">{{ $row->consequence }}</td>
                <td align="center">{{ $row->likelihood }}</td>
                <td align="left">{{ $row->RISK_REPPROGRAM_NAME }}</td>
                <td align="left">{{ $row->RISK_REPPROGRAMSUB_NAME }}</td>
                <td align="left">{{ $row->RISKREP_DETAILRISK }}</td>  
                <td align="left">{{ DateThai($row->recheck) }}</td>                       
            </tr>
            @endforeach          
          </table>        
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
        $('#risk_matrix_detail').DataTable();
    });
</script>