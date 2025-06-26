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
                    <label class="col-md-2 col-form-label text-md-end my-1">{{ __('วันที่') }}</label>
                <div class="col-md-2">
                    <input type="date" name="start_date" class="form-control my-1" placeholder="Date" value="{{ $start_date }}" > 
                </div>
                    <label class="col-md-1 col-form-label text-md-end my-1">{{ __('ถึง') }}</label>
                <div class="col-md-2">
                    <input type="date" name="end_date" class="form-control my-1" placeholder="Date" value="{{ $end_date }}" > 
                </div> 
                    <label class="col-md-1 col-form-label text-md-end my-1"></label>   
                <div class="col-md-2" align="right">     
                    <select class="form-select my-1" name="village">
                    @foreach ($village_select as $row)
                    <option value="{{$row->village_id}}" @if ($village == "$row->village_id") selected="selected"  @endif>{{$row->village_name}}</option>
                    @endforeach 
                    </select>                   
                </div>
                <div class="col-md-2" align="left">  
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
    <div class="card-header bg-primary text-white">ข้อมูลคัดกรองเชิงรุก-ประชากรในเขตรับผิดชอบ (EHP) หมู่บ้าน {{$village_name}}</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">    
                        <table id="ehp" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">ลำดับ</th>
                                <th class="text-center">ผู้บันทึก(อสม.)</th>
                                <th class="text-center">บ้านเลขที่</th>
                                <th class="text-center">ผู้ที่คัดกรอง</th>
                                <th class="text-center">วันที่คัดกรอง</th>
                                <th class="text-center">เวลาวัดความดัน</th>
                                <th class="text-center">ความดัน</th>
                                <th class="text-center">เวลาวัดอุณหภูมิ</th>   
                                <th class="text-center">อุณหภูมิ</th>   
                                <th class="text-center">เวลาชั่งน้ำหนัก</th>   
                                <th class="text-center">น้ำหนัก</th>     
                                <th class="text-center">เวลาวัดระดับน้ำตาล</th>   
                                <th class="text-center">ระดับน้ำตาล</th>         
                            </thead>   
                            <?php $count = 1 ; ?> 
                            @foreach($ehp as $row)          
                            <tr>
                                <td align="center">{{ $count }}</td>
                                <td align="left">{{ $row->officer_name }}</td>
                                <td align="center">{{ $row->address }}</td>
                                <td align="left">{{ $row->ptname }}</td>
                                <td align="center">{{ DateThai($row->date_vt) }}</td>
                                <td align="center">{{ $row->bp_time }}</td>
                                <td align="center">{{ $row->bp }}</td>
                                <td align="center">{{ $row->temp_time }}</td>
                                <td align="center">{{ $row->temp }}</td>
                                <td align="center">{{ $row->bw_time }}</td>      
                                <td align="center">{{ $row->bw }}</td> 
                                <td align="center">{{ $row->bgm_time }}</td>      
                                <td align="center">{{ $row->bgm }}</td>                              
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
        $('#ehp').DataTable();
    });
</script>
