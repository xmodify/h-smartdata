@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">

@section('content')
<div class="container-fluid">
    <h5 class="alert alert-primary"><strong>รายงานผลิตภาพทางการพยาบาลแผนกห้องคลอด</strong></h5>  
</div> 
<div class="container-fluid">  
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
</div>
<!--row-->
<div class="container-fluid"> 
    <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>รายงานสรุปผลิตภาพทางการพยาบาล วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
        <table class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">เวร</th>
                        <th class="text-center">ผู้ป่วยในเวร</th>
                        <th class="text-center">OPD-เร่งด่วนปกติ</th>
                        <th class="text-center">OPD-เร่งด่วนมาก</th>
                        <th class="text-center">IPD-Convalescent</th>
                        <th class="text-center">IPD-Moderate ill</th>
                        <th class="text-center">IPD-Semi critical ill</th>
                        <th class="text-center">IPD-Critical ill</th>
                        <th class="text-center">ชม.การพยาบาล</th>
                        <th class="text-center">อัตรากำลัง Oncall</th>
                        <th class="text-center">อัตรากำลังเสริม</th>
                        <th class="text-center">อัตรากำลังปกติ</th>
                        <th class="text-center">ชม.การทำงาน</th>
                        <th class="text-center">Productivity</th>
                        <th class="text-center">HHPUOS</th>
                        <th class="text-center">พยาบาลที่ต้องการ</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($sum_productivity_lr as $row)
                    <tr>
                        <td align="right">{{ $row->shift_time }} {{ $row->shift_time_sum }} เวร</td>
                        <td align="right">{{ $row->patient_all }}</td>
                        <td align="right">{{ $row->opd_normal }}</td>
                        <td align="right">{{ $row->opd_high }}</td>
                        <td align="right">{{ $row->convalescent }}</td>
                        <td align="right">{{ $row->moderate_ill }}</td>
                        <td align="right">{{ $row->semi_critical_ill }}</td> 
                        <td align="right">{{ $row->critical_ill }}</td> 
                        <td align="right">{{ number_format($row->patient_hr,2) }}</td> 
                        <td align="right">{{ $row->nurse_oncall }}</td> 
                        <td align="right">{{ $row->nurse_partime }}</td> 
                        <td align="right">{{ $row->nurse_fulltime }}</td> 
                        <td align="right">{{ number_format($row->nurse_hr,2) }}</td> 
                        <td align="right">{{ number_format($row->productivity,2) }}</td> 
                        <td align="right">{{ number_format($row->hhpuos,2) }}</td> 
                        <td align="right">{{ number_format($row->nurse_shift_time,2) }}</td>
                    </tr>
                <?php $count++; ?>
                @endforeach
            </table>            
        </div>         
    </div>
</div>
<br>
<!--row-->
<div class="container-fluid"> 
    <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>การบันทึกข้อมูลผลิตภาพทางการพยาบาล วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">  
            <div class="row mb-3"> 
                @if ($message = Session::get('danger'))
                <div class="alert alert-danger text-center">
                <h5><strong>{{ $message }}</strong></h5>
                </div>
                @endif
            </div>   
            <table id="nurse_productivity_lr" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">วันที่-เวลา</th>
                        <th class="text-center">เวร</th>
                        <th class="text-center">ผู้ป่วยในเวร</th>
                        <th class="text-center">OPD-เร่งด่วนปกติ</th>
                        <th class="text-center">OPD-เร่งด่วนมาก</th>
                        <th class="text-center">IPD-Convalescent</th>
                        <th class="text-center">IPD-Moderate ill</th>
                        <th class="text-center">IPD-Semi critical ill</th>
                        <th class="text-center">IPD-Critical ill</th>
                        <th class="text-center">ชม.การพยาบาล</th>
                        <th class="text-center">อัตรากำลัง Oncall</th>
                        <th class="text-center">อัตรากำลังเสริม</th>
                        <th class="text-center">อัตรากำลังปกติ</th>
                        <th class="text-center">ชม.การทำงาน</th>
                        <th class="text-center">Productivity</th>
                        <th class="text-center">HHPUOS</th>
                        <th class="text-center">พยาบาลที่ต้องการ</th>
                        <th class="text-center">ผู้บันทึก</th>
                        <th class="text-center">หมายเหตุ</th>
                        @if(Auth::user()->username == $del_product)  
                        <th class="text-center">Action</th>
                        @endif
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($nurse_productivity_lr as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="right">{{ DateThai($row->report_date) }}</td>   
                        <td align="right">{{ $row->shift_time }}</td>
                        <td align="right">{{ $row->patient_all }}</td>
                        <td align="right">{{ $row->opd_normal }}</td>
                        <td align="right">{{ $row->opd_high }}</td>
                        <td align="right">{{ $row->convalescent }}</td>
                        <td align="right">{{ $row->moderate_ill }}</td>
                        <td align="right">{{ $row->semi_critical_ill }}</td> 
                        <td align="right">{{ $row->critical_ill }}</td> 
                        <td align="right">{{ number_format($row->patient_hr,2) }}</td> 
                        <td align="right">{{ $row->nurse_oncall }}</td> 
                        <td align="right">{{ $row->nurse_partime }}</td> 
                        <td align="right">{{ $row->nurse_fulltime }}</td> 
                        <td align="right">{{ number_format($row->nurse_hr,2) }}</td> 
                        <td align="right">{{ number_format($row->productivity,2) }}</td> 
                        <td align="right">{{ number_format($row->hhpuos,2) }}</td> 
                        <td align="right">{{ number_format($row->nurse_shift_time,2) }}</td> 
                        <td align="left">{{ $row->recorder }}</td> 
                        <td align="left">{{ $row->note }}</td> 
                        @if(Auth::user()->username == $del_product)   
                        <td class="text-center">
                            <form action="{{url('backoffice_hrd/nurse_productivity_lr_delete',$row->id)}}" method="GET">
                                @csrf @method('DELETE')<button type ="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('ต้องการลบข้อมูล {{ DateThai($row->report_date) }} {{ $row->shift_time }} Product {{ number_format($row->productivity,2) }}')">Delete</button>
                            </form>
                        </td>
                        @endif   
                    </tr>
                <?php $count++; ?>
                @endforeach
            </table>
        </div>         
    </div>
</div>
@endsection

<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#nurse_productivity_lr').DataTable();
    });
</script>
