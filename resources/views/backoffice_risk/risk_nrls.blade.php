@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">

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
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลการเกิดอุบัติการณ์ความเสี่ยง ส่ง NRLS วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
          <div class="card-body">
            <div class="row">                          
                <div class="col-md-8" align="left"> 
                    <h5 class="card-title text-primary"></h5>
                </div>                 
                <div class="col-md-4" align="right">
                    <a class="btn btn-success my-2 " href="{{ url('backoffice_risk/nrls_export') }}" target="_blank" type="submit">
                    Export
                    </a>                    
                </div>      
            </div>
            <table id = "nrls" class="table table-bordered table-striped">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center">ลำดับ</th>
                    <th class="text-center">รหัสสถานพยาบาล</th>
                    <th class="text-center">รหัส IR</th>
                    <th class="text-center">รหัสอุบัติการณ์</th>
                    <th class="text-center">ประเภทผู้รับผลกระทบ</th>
                    <th class="text-center">เพศผู้รับผลกระทบ</th>
                    <th class="text-center">อายุผู้รับผลกระทบ</th>             
                    <th class="text-center">สถานที่เกิดอุบัติการณ์</th>
                    <th class="text-center">วันที่เกิดอุบัติการณ์ความเสี่ยง</th>
                    <th class="text-center">เวลา(เวร) ที่เกิดอุบัติการณ์</th>
                    <th class="text-center">ระดับความรุนแรง</th>
                    <th class="text-center">รายละเอียดเหตุการณ์พอสังเขป</th>
                    <th class="text-center">สถานะความรุนแรง</th>
                </tr>     
                </thead>
                <?php $count = 1 ; ?> 
                @foreach($nrls as $row)          
                <tr>
                    <td align="center">{{ $count }}</td>
                    <td align="center">{{ $row->hospital }}</td>
                    <td align="center">{{ $row->risk_id }}</td> 
                    <td align="left">{{ $row->datadic1 }}-{{ $row->datadic1_name }}</td>
                    <td align="left">{{ $row->effect_code }}-{{ $row->effect_name }}</td>      
                    <td align="center">{{ $row->pt_sex }}</td>
                    <td align="center">{{ $row->person_age }}</td>         
                    <td align="left">{{ $row->datadic4 }}-{{ $row->datadic4_name }}</td>    
                    <td align="center">{{ $row->risk_date }}</td> 
                    <td align="center">{{ $row->datadic5 }}</td>    
                    <td align="center">{{ $row->datadic6_name }}-{{ $row->datadic6 }}</td>   
                    <td align="left">{{ $row->risk_detail }}</td> 
                    <td align="center">{{ $row->status_lavel }}</td> 
                </tr>
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
        $('#nrls').DataTable();
    });
</script>