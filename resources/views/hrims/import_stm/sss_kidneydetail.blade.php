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
                <div class="card-header bg-primary text-white">Statement ประกันสังคม SSS [ฟอกไต HD] รายละเอียด</div>
                <div class="card-body">
                    <div style="overflow-x:auto;">                           
                        <table id="stm_sss_kidney_list" class="table table-bordered table-striped my-3">
                            <thead>
                                <tr class="table-success">
                                    <th class="text-center">Hcode</th>
                                    <th class="text-center">Hname</th> 
                                    <th class="text-center">FileName</th> 
                                    <th class="text-center">Station</th> 
                                    <th class="text-center">Hreg</th>                      
                                    <th class="text-center">HN</th>
                                    <th class="text-center">CID</th>                    
                                    <th class="text-center">วันที่รับบริการ</th>
                                    <th class="text-center">เอกสารตอบรับ</th>                  
                                    <th class="text-center">ค่าฟอกเลือดล้างไต</th> 
                                    <th class="text-center">ค่ายา EPOETIN</th> 
                                    <th class="text-center">ค่าฉีดยา EPOETIN</th> 
                                </tr>     
                                </thead> 
                                <?php $count = 1 ; ?>  
                                @foreach($stm_sss_kidney_list as $row)          
                                <tr>
                                    <td align="center">{{ $row->hcode }}</td> 
                                    <td align="right">{{ $row->hname }}</td>
                                    <td align="right">{{ $row->stmdoc }}</td>
                                    <td align="right">{{ $row->station }}</td>
                                    <td align="right">{{ $row->hreg }}</td>
                                    <td align="left">{{ $row->hn }}</td>
                                    <td align="right">{{ $row->cid }}</td>
                                    <td align="right">{{ $row->dttran }}</td>
                                    <td align="right">{{ $row->rid }}</td>
                                    <td align="right">{{ number_format($row->amount,2) }}</td> 
                                    <td align="right">{{ number_format($row->epopay,2) }}</td>
                                    <td align="right">{{ number_format($row->epoadm,2) }}</td>
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
        $('#stm_sss_kidney_list').DataTable();
    });
</script>
