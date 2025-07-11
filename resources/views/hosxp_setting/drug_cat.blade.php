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
        <div class="alert alert-success" role="alert">
            <div class="row"> 
                <div class="col-md-4" align="left">   
                    <strong>ตรวจสอบ Drug Catalog</strong>              
                </div>  
                <div class="col-md-4" align="center">
                    @if ($message = Session::get('success'))<strong class="text-center">ImportFile {{ $message }} Success</strong> @endif
                </div>  
                <div class="col-md-4" align="right">                    
                </div> 
            </div>
        </div>                
        <div class="card-body"> 
            <div class="row">   
                <div class="col-md-6" align="left">                   
                    <form action="{{ url('hosxp_setting/drug_cat_nhso_save') }}" method="POST" enctype="multipart/form-data">
                        @csrf  
                        <div class="row" >
                            <div class="col-md-6" >
                                <input class="form-control form-control" id="formFile" name="file" type="file" required>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </div>
                            <div class="col-md-3" >
                                <button type="submit" class="btn btn-outline-danger">ImportDrugCat NHSO</button>
                            </div> 
                            <div class="col-md-3">                                
                            </div>      
                        </div>                                         
                    </form>   
                </div>  
                <div class="col-md-6" align="right">  
                    {{-- <form action="{{ url('hosxp_setting/drug_cat_aipn_save') }}" method="POST" enctype="multipart/form-data">
                        @csrf  
                        <div class="row" >
                            <div class="col-md-3">                                
                            </div>   
                            <div class="col-md-6" >
                                <input class="form-control form-control" id="formFile" name="file" type="file" required>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </div>
                            <div class="col-md-3" >
                                <button type="submit" class="btn btn-outline-primary">ImportDrugCat AIPN</button>
                            </div>    
                        </div>                                          --}}
                    </form>  
                </div>  
            </div>  
            <div class="row">   
                <div class="col-md-6" align="left">
                    <a class="btn btn-outline-success " href="{{ url('hosxp_setting/drug_cat') }}">
                        ทั้งหมดที่ HOSxP
                    </a>  
                    <a class="btn btn-outline-danger " href="{{ url('hosxp_setting/drug_cat_non_nhso') }}">
                        ไม่พบที่ NHSO
                    </a>  
                    <a class="btn btn-outline-danger " href="{{ url('hosxp_setting/drug_cat_nhso_price_notmatch_hosxp') }}">
                        ราคาไม่ตรง HOSxP
                    </a> 
                    <a class="btn btn-outline-danger " href="{{ url('hosxp_setting/drug_cat_nhso_tmt_notmatch_hosxp') }}">
                        รหัส TMT ไม่ตรง HOSxP
                    </a> 
                    <a class="btn btn-outline-danger " href="{{ url('hosxp_setting/drug_cat_nhso_code24_notmatch_hosxp') }}">
                        รหัส 24 หลักไม่ตรง HOSxP
                    </a> 
                </div>   
                <div class="col-md-6" align="right"> 
                    <a class="btn btn-outline-primary " href="{{ url('hosxp_setting/drug_cat_non_aipn') }}">
                        ไม่พบที่ AIPN
                    </a>  
                    <a class="btn btn-outline-primary " href="{{ url('hosxp_setting/drug_cat_aipn_price_notmatch_hosxp') }}">
                        ราคาไม่ตรง HOSxP
                    </a> 
                    <a class="btn btn-outline-primary " href="{{ url('hosxp_setting/drug_cat_aipn_tmt_notmatch_hosxp') }}">
                        รหัส TMT ไม่ตรง HOSxP
                    </a> 
                    <a class="btn btn-outline-primary " href="{{ url('hosxp_setting/drug_cat_aipn_code24_notmatch_hosxp') }}">
                        รหัส 24 หลักไม่ตรง HOSxP
                    </a> 
                    <a class="btn btn-outline-success " href="{{ url('hosxp_setting/drug_cat_aipn_export') }}">
                        Export AIPN
                    </a>  
                </div>              
            </div>
        </div>
        <div class="card-body">
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                          
                        <table id="drug" class="table table-bordered table-striped" width = "100%" >
                            <thead>
                                <tr class="table-primary">
                                    <th class="text-center" rowspan="2">NHSO</th>   
                                    <th class="text-center" rowspan="2">AIPN</th>  
                                    <th class="text-center" rowspan="2">รหัส HOSxP</th>             
                                    <th class="text-center" rowspan="2">ชื่อยา</th>                   
                                    <th class="text-center" rowspan="2">หน่วยนับ</th>
                                    <th class="text-center" colspan="3" style="background-color: #4fc3f7">ราคา</th>                   
                                    <th class="text-center" colspan="3" style="background-color: #81d4fa">รหัส TMT</th>                   
                                    <th class="text-center" colspan="3" style="background-color: #b3e5fc">รหัส 24 หลัก</th> 
                                </tr>
                                <tr class="table-primary">                    
                                    <th class="text-center" style="background-color: #4fc3f7">HOSxP</th>   
                                    <th class="text-center" style="background-color: #4fc3f7">NHSO</th>     
                                    <th class="text-center" style="background-color: #4fc3f7">AIPN</th> 
                                    <th class="text-center" style="background-color: #81d4fa">HOSxP</th> 
                                    <th class="text-center" style="background-color: #81d4fa">HNSO</th>  
                                    <th class="text-center" style="background-color: #81d4fa">AIPN</th>  
                                    <th class="text-center" style="background-color: #b3e5fc">HOSxP</th> 
                                    <th class="text-center" style="background-color: #b3e5fc">HNSO</th>  
                                    <th class="text-center" style="background-color: #b3e5fc">AIPN</th>                                                                                   
                                </tr>
                            </thead>                          
                            @foreach($drug as $row)          
                                <tr>          
                                    <td align="center">{{ $row->chk_nhso_drugcat }}</td> 
                                    <td align="center">{{ $row->chk_aipn_drugcat }}</td>                 
                                    <td align="center">{{ $row->icode }}</td>                          
                                    <td align="left" width = "15%">{{ $row->dname }}</td>                        
                                    <td align="left">{{ $row->units }}</td>
                                    <td align="right">{{ number_format($row->price_hos,2) }}</td>
                                    <td align="right" @if($row->price_nhso != $row->price_hos) style="color:red" @endif>{{ number_format($row->price_nhso,2) }}</td>                                     
                                    <td align="right" @if($row->price_aipn != $row->price_hos) style="color:red" @endif>{{ number_format($row->price_aipn,2) }}</td>
                                    <td align="center">{{ $row->code_tmt_hos }}</td>
                                    <td align="center" @if($row->code_tmt_nhso != $row->code_tmt_hos) style="color:red" @endif>{{ $row->code_tmt_nhso }}</td>
                                    <td align="center" @if($row->code_tmt_aipn != $row->code_tmt_hos) style="color:red" @endif>{{ $row->code_tmt_aipn }}</td> 
                                    <td align="center">{{ $row->code_24_hos }}</td>
                                    <td align="center" @if($row->code_24_nhso != $row->code_24_hos) style="color:red" @endif>{{ $row->code_24_nhso }}</td>
                                    <td align="center" @if($row->code_24_aipn != $row->code_24_hos) style="color:red" @endif>{{ $row->code_24_aipn }}</td>  
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