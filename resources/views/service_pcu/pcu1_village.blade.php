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
  <div class="row justify-content-center">      
    <div class="col-md-12">
        <form method="POST" enctype="multipart/form-data">
        @csrf
            <div class="row">                          
                <div class="col-md-9" align="right"></div>
                <div class="col-md-2" align="right">     
                    <select class="form-select my-1" name="village">
                    @foreach ($village_select as $row)
                    <option value="{{$row->village_id}}" @if ($village == "$row->village_id") selected="selected"  @endif>{{$row->village_name}}</option>
                    @endforeach 
                    </select>                   
                </div>
                <div class="col-md-1" align="left">  
                <button type="submit" class="btn btn-primary my-1 ">{{ __('ค้นหา') }}</button> 
                </div>
            </div>
        </form>   
    </div>    
  </div>
</div> 
<!-- row -->
<div class="container-fluid">
    <div class="card">
    <div class="card-header bg-primary text-white">หมู่บ้าน {{$village_name}}</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">    
                        <table id="village" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">ลำดับ</th>
                                <th class="text-center">เลขที่ทะเบียนบ้าน</th>
                                <th class="text-center">บ้านเลขที่</th>
                                <th class="text-center">Latitude</th>
                                <th class="text-center">Longitude</th>
                                <th class="text-center">บุคคลในบ้าน</th>
                                <th class="text-center">แพทย์ประจำบ้าน</th>
                                <th class="text-center">จนท.สาธารณสุขประจำบ้าน</th>   
                                <th class="text-center">อสม.ประจำบ้าน</th>   
                                <th class="text-center">อสม.บัญชี 8</th>   
                                            
                            </thead>   
                            <?php $count = 1 ; ?> 
                            @foreach($house as $row)          
                            <tr>
                                <td align="center">{{ $count }}</td>
                                <td align="center">{{ $row->census_id }}</td>
                                <td align="center">{{ $row->address }}</td>
                                <td align="center">{{ $row->latitude }}</td>
                                <td align="center">{{ $row->longitude }}</td>
                                <td align="center">{{ $row->person_count }}</td>
                                <td align="left">{{ $row->doctor_name2 }}</td>
                                <td align="left">{{ $row->doctor_name1 }}</td>
                                <td align="left">{{ $row->vms_name }}</td>
                                <td align="left">{{ $row->vms_name8 }}</td>                               
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
        $('#village').DataTable();
    });
</script>
