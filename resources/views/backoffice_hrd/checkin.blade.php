@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
    
@section('content')
<div class="container-fluid">
    <h5 class="alert alert-primary"><strong>รายงานข้อมูลการลงเวลาปฏิบัติงาน</strong></h5>  
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
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>ข้อมูลการลงเวลาปฏิบัติงานลูกจ้างรายวัน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">     
            <table id="checkin_type6" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>   
                        <th class="text-center">ชื่อ-สกุล</th>                        
                        <th class="text-center">เบอร์โทร.</th>
                        <th class="text-center">ตำแหน่ง</th>
                        <th class="text-center">หน่วยงาน</th>
                        <th class="text-center">ประเภทการจ้าง</th>
                        <th class="text-center">จำนวนวัน</th>
                        <th class="text-center">จำนวนเวร</th>
                        <th class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($checkin_type6 as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->HR_PHONE }}</td>
                        <td align="left">{{ $row->HR_POSITION_NAME }}</td>
                        <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                        <td align="left">{{ $row->HR_PERSON_TYPE_NAME }}</td>
                        <td align="center">{{ $row->sumday }}</td> 
                        <td align="center">{{ $row->sumshift }}</td> 
                        <td class="text-center">
                            <a class="btn btn-outline-danger btn-sm" href="{{ url('backoffice_hrd/checkin_indiv_pdf/'.$row->ID)}}" target="_blank">พิมพ์สรุป</a>
                            <a class="btn btn-outline-info btn-sm" href="{{ url('backoffice_hrd/checkin_indiv_detail_pdf/'.$row->ID)}}" target="_blank">พิมพ์เวลาจากเครื่องสแกน</a>
                        </td> 
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
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>ข้อมูลการลงเวลาปฏิบัติงานลูกจ้างรายเดือน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">     
            <table id="checkin_type5" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>   
                        <th class="text-center">ชื่อ-สกุล</th>                        
                        <th class="text-center">เบอร์โทร.</th>
                        <th class="text-center">ตำแหน่ง</th>
                        <th class="text-center">หน่วยงาน</th>
                        <th class="text-center">ประเภทการจ้าง</th>
                        <th class="text-center">จำนวนวัน</th>
                        <th class="text-center">จำนวนเวร</th>
                        <th class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($checkin_type5 as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->HR_PHONE }}</td>
                        <td align="left">{{ $row->HR_POSITION_NAME }}</td>
                        <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                        <td align="left">{{ $row->HR_PERSON_TYPE_NAME }}</td>
                        <td align="center">{{ $row->sumday }}</td> 
                        <td align="center">{{ $row->sumshift }}</td> 
                        <td class="text-center">
                            <a class="btn btn-outline-danger btn-sm" href="{{ url('backoffice_hrd/checkin_indiv_pdf/'.$row->ID)}}" target="_blank">พิมพ์สรุป</a>
                            <a class="btn btn-outline-info btn-sm" href="{{ url('backoffice_hrd/checkin_indiv_detail_pdf/'.$row->ID)}}" target="_blank">พิมพ์เวลาจากเครื่องสแกน</a>
                        </td> 
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
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>ข้อมูลการลงเวลาปฏิบัติงานพนักงานกระทรวงสาธารณสุข วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">     
            <table id="checkin_type4" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>   
                        <th class="text-center">ชื่อ-สกุล</th>                        
                        <th class="text-center">เบอร์โทร.</th>
                        <th class="text-center">ตำแหน่ง</th>
                        <th class="text-center">หน่วยงาน</th>
                        <th class="text-center">ประเภทการจ้าง</th>
                        <th class="text-center">จำนวนวัน</th>
                        <th class="text-center">จำนวนเวร</th>
                        <th class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($checkin_type4 as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->HR_PHONE }}</td>
                        <td align="left">{{ $row->HR_POSITION_NAME }}</td>
                        <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                        <td align="left">{{ $row->HR_PERSON_TYPE_NAME }}</td>
                        <td align="center">{{ $row->sumday }}</td> 
                        <td align="center">{{ $row->sumshift }}</td> 
                        <td class="text-center">
                            <a class="btn btn-outline-danger btn-sm" href="{{ url('backoffice_hrd/checkin_indiv_pdf/'.$row->ID)}}" target="_blank">พิมพ์สรุป</a>
                            <a class="btn btn-outline-info btn-sm" href="{{ url('backoffice_hrd/checkin_indiv_detail_pdf/'.$row->ID)}}" target="_blank">พิมพ์เวลาจากเครื่องสแกน</a>
                        </td> 
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
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>ข้อมูลการลงเวลาปฏิบัติงานพนักงานราชการ วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">     
            <table id="checkin_type3" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>   
                        <th class="text-center">ชื่อ-สกุล</th>                        
                        <th class="text-center">เบอร์โทร.</th>
                        <th class="text-center">ตำแหน่ง</th>
                        <th class="text-center">หน่วยงาน</th>
                        <th class="text-center">ประเภทการจ้าง</th>
                        <th class="text-center">จำนวนวัน</th>
                        <th class="text-center">จำนวนเวร</th>
                        <th class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($checkin_type3 as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->HR_PHONE }}</td>
                        <td align="left">{{ $row->HR_POSITION_NAME }}</td>
                        <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                        <td align="left">{{ $row->HR_PERSON_TYPE_NAME }}</td>
                        <td align="center">{{ $row->sumday }}</td> 
                        <td align="center">{{ $row->sumshift }}</td> 
                        <td class="text-center">
                            <a class="btn btn-outline-danger btn-sm" href="{{ url('backoffice_hrd/checkin_indiv_pdf/'.$row->ID)}}" target="_blank">พิมพ์สรุป</a>
                            <a class="btn btn-outline-info btn-sm" href="{{ url('backoffice_hrd/checkin_indiv_detail_pdf/'.$row->ID)}}" target="_blank">พิมพ์เวลาจากเครื่องสแกน</a>
                        </td> 
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
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>ข้อมูลการลงเวลาปฏิบัติงานลูกจ้างประจำ วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">     
            <table id="checkin_type2" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>   
                        <th class="text-center">ชื่อ-สกุล</th>                        
                        <th class="text-center">เบอร์โทร.</th>
                        <th class="text-center">ตำแหน่ง</th>
                        <th class="text-center">หน่วยงาน</th>
                        <th class="text-center">ประเภทการจ้าง</th>
                        <th class="text-center">จำนวนวัน</th>
                        <th class="text-center">จำนวนเวร</th>
                        <th class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($checkin_type2 as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->HR_PHONE }}</td>
                        <td align="left">{{ $row->HR_POSITION_NAME }}</td>
                        <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                        <td align="left">{{ $row->HR_PERSON_TYPE_NAME }}</td>
                        <td align="center">{{ $row->sumday }}</td> 
                        <td align="center">{{ $row->sumshift }}</td> 
                        <td class="text-center">
                            <a class="btn btn-outline-danger btn-sm" href="{{ url('backoffice_hrd/checkin_indiv_pdf/'.$row->ID)}}" target="_blank">พิมพ์สรุป</a>
                            <a class="btn btn-outline-info btn-sm" href="{{ url('backoffice_hrd/checkin_indiv_detail_pdf/'.$row->ID)}}" target="_blank">พิมพ์เวลาจากเครื่องสแกน</a>
                        </td> 
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
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>ข้อมูลการลงเวลาปฏิบัติงานข้าราชการ วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">     
            <table id="checkin_type1" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>   
                        <th class="text-center">ชื่อ-สกุล</th>                        
                        <th class="text-center">เบอร์โทร.</th>
                        <th class="text-center">ตำแหน่ง</th>
                        <th class="text-center">หน่วยงาน</th>
                        <th class="text-center">ประเภทการจ้าง</th>
                        <th class="text-center">จำนวนวัน</th>
                        <th class="text-center">จำนวนเวร</th>
                        <th class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($checkin_type1 as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->HR_PHONE }}</td>
                        <td align="left">{{ $row->HR_POSITION_NAME }}</td>
                        <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                        <td align="left">{{ $row->HR_PERSON_TYPE_NAME }}</td>
                        <td align="center">{{ $row->sumday }}</td> 
                        <td align="center">{{ $row->sumshift }}</td> 
                        <td class="text-center">
                            <a class="btn btn-outline-danger btn-sm" href="{{ url('backoffice_hrd/checkin_indiv_pdf/'.$row->ID)}}" target="_blank">พิมพ์สรุป</a>
                            <a class="btn btn-outline-info btn-sm" href="{{ url('backoffice_hrd/checkin_indiv_detail_pdf/'.$row->ID)}}" target="_blank">พิมพ์เวลาจากเครื่องสแกน</a>
                        </td> 
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
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>ข้อมูลการลงเวลาปฏิบัติงานผู้พิเศษ วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">     
            <table id="checkin_type7" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>   
                        <th class="text-center">ชื่อ-สกุล</th>                        
                        <th class="text-center">เบอร์โทร.</th>
                        <th class="text-center">ตำแหน่ง</th>
                        <th class="text-center">หน่วยงาน</th>
                        <th class="text-center">ประเภทการจ้าง</th>
                        <th class="text-center">จำนวนวัน</th>
                        <th class="text-center">จำนวนเวร</th>
                        <th class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($checkin_type7 as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->HR_PHONE }}</td>
                        <td align="left">{{ $row->HR_POSITION_NAME }}</td>
                        <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                        <td align="left">{{ $row->HR_PERSON_TYPE_NAME }}</td>
                        <td align="center">{{ $row->sumday }}</td> 
                        <td align="center">{{ $row->sumshift }}</td> 
                        <td class="text-center">                            
                            <a class="btn btn-outline-danger btn-sm" href="{{ url('backoffice_hrd/checkin_indiv_pdf/'.$row->ID)}}" target="_blank">พิมพ์สรุป</a>
                            <a class="btn btn-outline-info btn-sm" href="{{ url('backoffice_hrd/checkin_indiv_detail_pdf/'.$row->ID)}}" target="_blank">พิมพ์เวลาจากเครื่องสแกน</a>
                        </td> 
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
        $('#checkin_type6').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#checkin_type5').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#checkin_type4').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#checkin_type3').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#checkin_type2').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#checkin_type1').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#checkin_type7').DataTable();
    });
</script>
