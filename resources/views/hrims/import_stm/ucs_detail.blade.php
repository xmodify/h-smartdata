@extends('layouts.hrims')
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
<form method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row" >
            <label class="col-md-3 col-form-label text-md-end my-1">{{ __('วันที่') }}</label>
        <div class="col-md-2">
            <input type="date" name="start_date" class="form-control my-1" placeholder="Date" value="{{ $start_date }}" >
        </div>
            <label class="col-md-1 col-form-label text-md-end my-1">{{ __('ถึง') }}</label>
        <div class="col-md-2">
            <input type="date" name="end_date" class="form-control my-1" placeholder="Date" value="{{ $end_date }}" >
        </div>
        <div class="col-md-1" >
            <button type="submit" class="btn btn-primary my-1 ">{{ __('ค้นหา') }}</button>
        </div>
    </div>
</form>
<div class="container-fluid">
    <div class="row justify-content-center">  
        <div class="col-md-12"> 
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">รายละเอียด Statement ประกันสุขภาพ UCS [OP-IP]</div>
                <div class="card-body">
                    <div style="overflow-x:auto;"> 
                        <h4 class="text-primary">ผู้ป่วยนอก OP</h4>       
                        <table id="stm_ucs_list" class="table table-bordered table-striped my-3">
                            <thead>
                                <tr class="table-success">
                                    <th class="text-center">Dep</th>
                                    <th class="text-center">Filename</th> 
                                    <th class="text-center">REP</th> 
                                    <th class="text-center">HN</th>
                                    <th class="text-center">AN</th>    
                                    <th class="text-center">ชื่อ-สกุล</th>
                                    <th class="text-center">วันเข้ารักษา</th>
                                    <th class="text-center">วันจำหน่าย</th>  
                                    <th class="text-center">PROJCODE</th>  
                                    <th class="text-center">เรียกเก็บ</th>                                         
                                    <th class="text-center">ชดเชยสุทธิ</th> 
                                    <th class="text-center">OP</th>
                                    <th class="text-center">IP</th>  
                                    <th class="text-center">HC</th> 
                                    <th class="text-center">HC_Drug</th>
                                    <th class="text-center">AE</th> 
                                    <th class="text-center">AE_Drug</th> 
                                    <th class="text-center">INST</th> 
                                    <th class="text-center">Palliative</th> 
                                    <th class="text-center">PP</th> 
                                    <th class="text-center">FS</th>                                      
                                </tr>     
                                </thead> 
                                <?php $count = 1 ; ?>  
                                @foreach($stm_ucs_list as $row) 
                                <tr>
                                    <td align="center">{{ $row->dep }}</td> 
                                    <td align="right">{{ $row->stm_filename }}</td>
                                    <td align="right">{{ $row->repno }}</td>                            
                                    <td align="right">{{ $row->hn }}</td>
                                    <td align="right">{{ $row->an }}</td>                                   
                                    <td align="left">{{ $row->pt_name }}</td>
                                    <td align="right">{{ $row->datetimeadm }}</td>
                                    <td align="right">{{ $row->datetimedch }}</td>
                                    <td align="right">{{ $row->projcode }}</td>
                                    <td align="right">{{ number_format($row->charge,2) }}</td>
                                    <td align="right">{{ number_format($row->receive_total,2) }}</td>    
                                    <td align="right">{{ number_format($row->receive_op,2) }}</td>
                                    <td align="right">{{ number_format($row->receive_ip_compensate_pay,2) }}</td>                                         
                                    <td align="right">{{ number_format($row->receive_hc_hc,2) }}</td>  
                                    <td align="right">{{ number_format($row->receive_hc_drug,2) }}</td>
                                    <td align="right">{{ number_format($row->receive_ae_ae,2) }}</td>  
                                    <td align="right">{{ number_format($row->receive_ae_drug,2) }}</td> 
                                    <td align="right">{{ number_format($row->receive_inst,2) }}</td> 
                                    <td align="right">{{ number_format($row->receive_palliative,2) }}</td>  
                                    <td align="right">{{ number_format($row->receive_pp,2) }}</td>
                                    <td align="right">{{ number_format($row->receive_fs,2) }}</td>    
                                </tr>                
                                <?php $count++; ?>  
                                @endforeach   
                        </table>
                    </div> 
                </div>  
                <hr>
                <div class="card-body">
                    <div style="overflow-x:auto;"> 
                        <h4 class="text-primary">ผู้ป่วยใน IP</h4>       
                        <table id="stm_ucs_list_ip" class="table table-bordered table-striped my-3">
                            <thead>
                                <tr class="table-success">
                                    <th class="text-center">Dep</th>
                                    <th class="text-center">Filename</th> 
                                    <th class="text-center">REP</th> 
                                    <th class="text-center">HN</th>
                                    <th class="text-center">AN</th>    
                                    <th class="text-center">ชื่อ-สกุล</th>
                                    <th class="text-center">วันเข้ารักษา</th>
                                    <th class="text-center">วันจำหน่าย</th>  
                                    <th class="text-center">PROJCODE</th>  
                                    <th class="text-center">เรียกเก็บ</th>                                         
                                    <th class="text-center">ชดเชยสุทธิ</th> 
                                    <th class="text-center">OP</th>
                                    <th class="text-center">IP</th>  
                                    <th class="text-center">HC</th> 
                                    <th class="text-center">HC_Drug</th>
                                    <th class="text-center">AE</th> 
                                    <th class="text-center">AE_Drug</th> 
                                    <th class="text-center">INST</th> 
                                    <th class="text-center">Palliative</th> 
                                    <th class="text-center">PP</th> 
                                    <th class="text-center">FS</th>                                      
                                </tr>     
                                </thead> 
                                <?php $count = 1 ; ?>  
                                @foreach($stm_ucs_list_ip as $row) 
                                <tr>
                                    <td align="center">{{ $row->dep }}</td> 
                                    <td align="right">{{ $row->stm_filename }}</td>
                                    <td align="right">{{ $row->repno }}</td>                            
                                    <td align="right">{{ $row->hn }}</td>
                                    <td align="right">{{ $row->an }}</td>                                   
                                    <td align="left">{{ $row->pt_name }}</td>
                                    <td align="right">{{ $row->datetimeadm }}</td>
                                    <td align="right">{{ $row->datetimedch }}</td>
                                    <td align="right">{{ $row->projcode }}</td>
                                    <td align="right">{{ number_format($row->charge,2) }}</td>
                                    <td align="right">{{ number_format($row->receive_total,2) }}</td>    
                                    <td align="right">{{ number_format($row->receive_op,2) }}</td>
                                    <td align="right">{{ number_format($row->receive_ip_compensate_pay,2) }}</td>                                         
                                    <td align="right">{{ number_format($row->receive_hc_hc,2) }}</td>  
                                    <td align="right">{{ number_format($row->receive_hc_drug,2) }}</td>
                                    <td align="right">{{ number_format($row->receive_ae_ae,2) }}</td>  
                                    <td align="right">{{ number_format($row->receive_ae_drug,2) }}</td> 
                                    <td align="right">{{ number_format($row->receive_inst,2) }}</td> 
                                    <td align="right">{{ number_format($row->receive_palliative,2) }}</td>  
                                    <td align="right">{{ number_format($row->receive_pp,2) }}</td>
                                    <td align="right">{{ number_format($row->receive_fs,2) }}</td>    
                                </tr>                
                                <?php $count++; ?>  
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
        $('#stm_ucs_list').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#stm_ucs_list_ip').DataTable();
    });
</script>