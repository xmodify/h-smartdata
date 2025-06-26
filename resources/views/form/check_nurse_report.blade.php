@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">

@section('content')
<div class="container-fluid">
    <h5 class="alert alert-primary"><strong>รายงานการบันทึกเวรตรวจการพยาบาล</strong></h5>  
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
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>รายงานการบันทึกเวรตรวจการงานอุบัติเหตุ-ฉุกเฉิน ER วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="check_nurse_er" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">วันที่-เวลา</th>
                        <th class="text-center">ความเสี่ยง/เหตุการณ์ในเวร</th>
                        <th class="text-center">การแก้ไขจัดการ</th>
                        <th class="text-center">นิเทศ/แนะนำในขณะตรวจเวร</th>
                        <th class="text-center">หมายเหตุ</th>   
                        <th class="text-center">หัวหน้าเวรตรวจการ</th>                        
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($check_nurse_er as $row)
                    <tr>
                        <td align="right">{{ $count }}</td> 
                        <td align="right">{{ DatetimeThai($row->created_at) }}</td>   
                        <td align="right">{{ $row->risk }}</td>
                        <td align="right">{{ $row->correct }}</td>
                        <td align="right">{{ $row->complain }}</td>
                        <td align="right">{{ $row->note }}</td>
                        <td align="right">{{ $row->supervisor }}</td>                         
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
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>รายงานการบันทึกเวรตรวจการงานผู้ป่วยนอก วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="check_nurse_opd" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">วันที่-เวลา</th>
                        <th class="text-center">ความเสี่ยง/เหตุการณ์ในเวร</th>
                        <th class="text-center">การแก้ไขจัดการ</th>
                        <th class="text-center">นิเทศ/แนะนำในขณะตรวจเวร</th>
                        <th class="text-center">หมายเหตุ</th> 
                        <th class="text-center">หัวหน้าเวรตรวจการ</th>                        
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($check_nurse_opd as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="right">{{ DatetimeThai($row->created_at) }}</td>  
                        <td align="right">{{ $row->risk }}</td>
                        <td align="right">{{ $row->correct }}</td>
                        <td align="right">{{ $row->complain }}</td>
                        <td align="right">{{ $row->note }}</td>
                        <td align="right">{{ $row->supervisor }}</td>                         
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
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>รายงานการบันทึกเวรตรวจการงานผู้ป่วยในสามัญ วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="check_nurse_ipd" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">วันที่-เวลา</th>
                        <th class="text-center">ความเสี่ยง/เหตุการณ์ในเวร</th>
                        <th class="text-center">การแก้ไขจัดการ</th>
                        <th class="text-center">นิเทศ/แนะนำในขณะตรวจเวร</th>
                        <th class="text-center">หมายเหตุ</th>      
                        <th class="text-center">หัวหน้าเวรตรวจการ</th>                        
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($check_nurse_ipd as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="right">{{ DatetimeThai($row->created_at) }}</td> 
                        <td align="right">{{ $row->risk }}</td>
                        <td align="right">{{ $row->correct }}</td>
                        <td align="right">{{ $row->complain }}</td>
                        <td align="right">{{ $row->note }}</td>
                        <td align="right">{{ $row->supervisor }}</td>                         
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
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>รายงานการบันทึกเวรตรวจการงานผู้ป่วยใน VIP วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="check_nurse_vip" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">วันที่-เวลา</th>
                        <th class="text-center">ความเสี่ยง/เหตุการณ์ในเวร</th>
                        <th class="text-center">การแก้ไขจัดการ</th>
                        <th class="text-center">นิเทศ/แนะนำในขณะตรวจเวร</th>
                        <th class="text-center">หมายเหตุ</th>  
                        <th class="text-center">หัวหน้าเวรตรวจการ</th>                        
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($check_nurse_vip as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="right">{{ DatetimeThai($row->created_at) }}</td>   
                        <td align="right">{{ $row->risk }}</td>
                        <td align="right">{{ $row->correct }}</td>
                        <td align="right">{{ $row->complain }}</td>
                        <td align="right">{{ $row->note }}</td>
                        <td align="right">{{ $row->supervisor }}</td>                         
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
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>รายงานการบันทึกเวรตรวจการศูนย์ฟอกไต HD รพ. วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="check_nurse_hd" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">วันที่-เวลา</th>
                        <th class="text-center">ความเสี่ยง/เหตุการณ์ในเวร</th>
                        <th class="text-center">การแก้ไขจัดการ</th>
                        <th class="text-center">นิเทศ/แนะนำในขณะตรวจเวร</th>
                        <th class="text-center">หมายเหตุ</th>  
                        <th class="text-center">หัวหน้าเวรตรวจการ</th>                        
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($check_nurse_hd as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="right">{{ DatetimeThai($row->created_at) }}</td>  
                        <td align="right">{{ $row->risk }}</td>
                        <td align="right">{{ $row->correct }}</td>
                        <td align="right">{{ $row->complain }}</td>
                        <td align="right">{{ $row->note }}</td>
                        <td align="right">{{ $row->supervisor }}</td>                         
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
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>รายงานการบันทึกเวรตรวจการศูนย์ฟอกไต HD เอกชน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="check_nurse_hd_outsource" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">วันที่-เวลา</th>
                        <th class="text-center">ความเสี่ยง/เหตุการณ์ในเวร</th>
                        <th class="text-center">การแก้ไขจัดการ</th>
                        <th class="text-center">นิเทศ/แนะนำในขณะตรวจเวร</th>
                        <th class="text-center">หมายเหตุ</th>       
                        <th class="text-center">หัวหน้าเวรตรวจการ</th>                        
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($check_nurse_hd_outsource as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="right">{{ DatetimeThai($row->created_at) }}</td> 
                        <td align="right">{{ $row->risk }}</td>
                        <td align="right">{{ $row->correct }}</td>
                        <td align="right">{{ $row->complain }}</td>
                        <td align="right">{{ $row->note }}</td>
                        <td align="right">{{ $row->supervisor }}</td>                         
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
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>รายงานการบันทึกเวรตรวจการงานห้องคลอด LR วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="check_nurse_lr" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">วันที่-เวลา</th>
                        <th class="text-center">ความเสี่ยง/เหตุการณ์ในเวร</th>
                        <th class="text-center">การแก้ไขจัดการ</th>
                        <th class="text-center">นิเทศ/แนะนำในขณะตรวจเวร</th>
                        <th class="text-center">หมายเหตุ</th>   
                        <th class="text-center">หัวหน้าเวรตรวจการ</th>                        
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($check_nurse_lr as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="right">{{ DatetimeThai($row->created_at) }}</td>   
                        <td align="right">{{ $row->risk }}</td>
                        <td align="right">{{ $row->correct }}</td>
                        <td align="right">{{ $row->complain }}</td>
                        <td align="right">{{ $row->note }}</td>
                        <td align="right">{{ $row->supervisor }}</td>                         
                    </tr>
                <?php $count++; ?>
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
        $('#check_nurse_er').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#check_nurse_opd').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#check_nurse_ipd').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#check_nurse_vip').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#check_nurse_hd').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#check_nurse_hd_outsource').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#check_nurse_lr').DataTable();
    });
</script>