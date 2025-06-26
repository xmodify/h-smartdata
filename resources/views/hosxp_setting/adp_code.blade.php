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
    <div class="card-header bg-primary text-white">ทะเบียน ADP Code หมวด {{$adp_type_name}} (Eclaim)</div>  
        <div class="card-body">    
            <div style="overflow-x:auto;">         
                <div class="row">
                    <div class="col-md-12">    
                        <form method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">                          
                            <div class="col-md-7" align="left"></div>
                            <div class="col-md-4" align="right">     
                                <select class="form-select my-1" name="adp_type">
                                @foreach ($adp_type_select as $row)
                                <option value="{{$row->nhso_adp_type_id}}" @if ($adp_type == "$row->nhso_adp_type_id") selected="selected"  @endif>{{$row->nhso_adp_type_name}}</option>
                                @endforeach 
                                </select>                   
                            </div>
                            <div class="col-md-1" align="right">  
                            <button type="submit" class="btn btn-primary my-1 ">{{ __('ค้นหา') }}</button> 
                            </div>
                        </div>
                        </form>                        
                        <table id="adp_code" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัส</th>
                                <th class="text-center">รายการ</th>
                                <th class="text-center">OFC</th>
                                <th class="text-center">LGO</th>                          
                                <th class="text-center">UCS</th>
                                <th class="text-center">UCEP</th>
                                <th class="text-center">Walkin-AE</th>
                                <th class="text-center">PPFS</th>
                                <th class="text-center">ราคา สธ.</th>
                                <th class="text-center">หมวด ADP</th>
                                <th class="text-center">หมวด Eclaim</th>
                                <th class="text-center">หมวด HOSxP</th>
                                <th class="text-center">รายการ HOSxP</th>
                            </tr>     
                            </thead>   
                            @foreach($adp_code as $row)          
                            <tr>
                                <td align="right">{{ $row->nhso_adp_code }}</td>
                                <td align="left">{{ $row->nhso_adp_code_name }}</td>                                                     
                                <td align="right">{{number_format($row->ofc,2)}}</td>
                                <td align="right">{{number_format($row->lgo,2)}}</td>                     
                                <td align="right">{{number_format($row->ucs,2)}}</td>
                                <td align="right">{{number_format($row->ucep,2)}}</td>
                                <td align="right">{{number_format($row->fs,2)}}</td>
                                <td align="right">{{number_format($row->ppfs,2)}}</td>
                                <td align="right">{{number_format($row->moph,2)}}</td>
                                <td align="left">{{ $row->nhso_adp_type_name }}</td>
                                <td align="left">{{ $row->drg_chrgitem_name }}</td>
                                <td align="left">{{ $row->income }}</td>
                                <td align="left">{{ $row->hosxp }}</td>
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
        $('#adp_code').DataTable();
    });
</script>