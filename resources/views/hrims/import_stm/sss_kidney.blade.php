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
<div class="container-fluid">
    <div class="row justify-content-center">  
        <div class="col-md-12"> 
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">ข้อมูล Statement ประกันสังคม SSS [ฟอกไต]</div>
                <div class="card-body">
                    <form action="{{ url('finance_stm/stm_sss_kidney_save') }}" method="POST" enctype="multipart/form-data">
                        @csrf      
                        <div class="row mb-2">            
                            <div class="col"></div>
                                <div class="col-md-5">
                                    <div class="mb-3 mt-3">
                                    <input class="form-control form-control-lg" id="formFileLg" name="file"
                                        type="file" required>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </div>
                                </div>
                            <div class="col"></div>
                        </div>
                        <div class="row mb-2">            
                            <div align="center">
                                <button type="submit"
                                    class="mb-3 me-2 btn-icon btn-shadow btn-dashed btn btn-outline-primary">
                                    <i class="fa-solid fa-cloud-arrow-up me-2" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="UP STM"></i>
                                    UPLOAD STM
                                </button> 
                            </div>
                        </div>
                        <div class="row"> 
                            @if ($message = Session::get('success'))
                            <div class="alert alert-success text-center">
                            <h5><strong>{{ $message }}</strong></h5>
                            </div>
                            @endif
                        </div>
                    </form>
                </div> 
                <div class="card-body">
                    <div style="overflow-x:auto;">   
                        <table id="stm_sss_kidney" class="table table-bordered table-striped my-3">
                            <thead>
                                <tr class="table-primary">
                                    <th class="text-center">FileName</th> 
                                    <th class="text-center">Station</th> 
                                    <th class="text-center">จำนวน</th>                      
                                    <th class="text-center">ค่าฟอกเลือดล้างไต</th> 
                                    <th class="text-center">ค่ายา EPOETIN</th> 
                                    <th class="text-center">ค่าฉีดยา EPOETIN</th> 
                                </tr>     
                                </thead> 
                                <?php $count = 1 ; ?>  
                                @foreach($stm_sss_kidney as $row)          
                                <tr>
                                    <td align="right">{{ $row->stmdoc }}</td>
                                    <td align="right">{{ $row->station }}</td>
                                    <td align="right">{{ number_format($row->count_no) }}</td> 
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
        $('#stm_sss_kidney').DataTable();
    });
</script>