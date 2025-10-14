@extends('layouts.hnplus')

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
        <div class="card-header text-white" style="background-color:#23A7A7;"><strong>รายงานการบันทึกเวรตรวจการงานอุบัติเหตุ-ฉุกเฉิน ER วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="er" class="table table-bordered  table-striped my-3">
                <thead>
                    <tr class="table-primary">
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
                @foreach($er as $row)
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
        <div class="card-header text-white" style="background-color:#23A7A7;"><strong>รายงานการบันทึกเวรตรวจการงานผู้ป่วยนอก วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="opd" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-primary">
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
                @foreach($opd as $row)
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
        <div class="card-header text-white" style="background-color:#23A7A7;"><strong>รายงานการบันทึกเวรตรวจการงานผู้ป่วยในสามัญ วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="ipd" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-primary">
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
                @foreach($ipd as $row)
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
        <div class="card-header text-white" style="background-color:#23A7A7;"><strong>รายงานการบันทึกเวรตรวจการงานผู้ป่วยใน VIP วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="vip" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-primary">
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
                @foreach($vip as $row)
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
        <div class="card-header text-white" style="background-color:#23A7A7;"><strong>รายงานการบันทึกเวรตรวจการศูนย์ฟอกไต HD รพ. วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="hd" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-primary">
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
                @foreach($hd as $row)
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
        <div class="card-header text-white" style="background-color:#23A7A7;"><strong>รายงานการบันทึกเวรตรวจการศูนย์ฟอกไต HD เอกชน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="hd_outsource" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-primary">
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
                @foreach($hd_outsource as $row)
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
        <div class="card-header text-white" style="background-color:#23A7A7;"><strong>รายงานการบันทึกเวรตรวจการงานห้องคลอด LR วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <table id="lr" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-primary">
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
                @foreach($lr as $row)
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

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#er').DataTable({
                dom: '<"row mb-3"' +
                        '<"col-md-6"l>' + // Show รายการ
                        '<"col-md-6 d-flex justify-content-end align-items-center gap-2"fB>' + // Search + Export
                    '>' +
                    'rt' +
                    '<"row mt-3"' +
                        '<"col-md-6"i>' + // Info
                        '<"col-md-6"p>' + // Pagination
                    '>',
                buttons: [
                    {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-success',
                    title: 'รายงานการบันทึกเวรตรวจการงานอุบัติเหตุ-ฉุกเฉิน ER วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}'
                    }
                ],
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    paginate: {
                    previous: "ก่อนหน้า",
                    next: "ถัดไป"
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#opd').DataTable({
                dom: '<"row mb-3"' +
                        '<"col-md-6"l>' + // Show รายการ
                        '<"col-md-6 d-flex justify-content-end align-items-center gap-2"fB>' + // Search + Export
                    '>' +
                    'rt' +
                    '<"row mt-3"' +
                        '<"col-md-6"i>' + // Info
                        '<"col-md-6"p>' + // Pagination
                    '>',
                buttons: [
                    {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-success',
                    title: 'รายงานการบันทึกเวรตรวจการงานผู้ป่วยนอก วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}'
                    }
                ],
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    paginate: {
                    previous: "ก่อนหน้า",
                    next: "ถัดไป"
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#ipd').DataTable({
                dom: '<"row mb-3"' +
                        '<"col-md-6"l>' + // Show รายการ
                        '<"col-md-6 d-flex justify-content-end align-items-center gap-2"fB>' + // Search + Export
                    '>' +
                    'rt' +
                    '<"row mt-3"' +
                        '<"col-md-6"i>' + // Info
                        '<"col-md-6"p>' + // Pagination
                    '>',
                buttons: [
                    {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-success',
                    title: 'รายงานการบันทึกเวรตรวจการงานผู้ป่วยในสามัญ วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}'
                    }
                ],
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    paginate: {
                    previous: "ก่อนหน้า",
                    next: "ถัดไป"
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#vip').DataTable({
                dom: '<"row mb-3"' +
                        '<"col-md-6"l>' + // Show รายการ
                        '<"col-md-6 d-flex justify-content-end align-items-center gap-2"fB>' + // Search + Export
                    '>' +
                    'rt' +
                    '<"row mt-3"' +
                        '<"col-md-6"i>' + // Info
                        '<"col-md-6"p>' + // Pagination
                    '>',
                buttons: [
                    {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-success',
                    title: 'รายงานการบันทึกเวรตรวจการงานผู้ป่วยใน VIP วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}'
                    }
                ],
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    paginate: {
                    previous: "ก่อนหน้า",
                    next: "ถัดไป"
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#hd').DataTable({
                dom: '<"row mb-3"' +
                        '<"col-md-6"l>' + // Show รายการ
                        '<"col-md-6 d-flex justify-content-end align-items-center gap-2"fB>' + // Search + Export
                    '>' +
                    'rt' +
                    '<"row mt-3"' +
                        '<"col-md-6"i>' + // Info
                        '<"col-md-6"p>' + // Pagination
                    '>',
                buttons: [
                    {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-success',
                    title: 'รายงานการบันทึกเวรตรวจการศูนย์ฟอกไต HD รพ. วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}'
                    }
                ],
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    paginate: {
                    previous: "ก่อนหน้า",
                    next: "ถัดไป"
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#hd_outsource').DataTable({
                dom: '<"row mb-3"' +
                        '<"col-md-6"l>' + // Show รายการ
                        '<"col-md-6 d-flex justify-content-end align-items-center gap-2"fB>' + // Search + Export
                    '>' +
                    'rt' +
                    '<"row mt-3"' +
                        '<"col-md-6"i>' + // Info
                        '<"col-md-6"p>' + // Pagination
                    '>',
                buttons: [
                    {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-success',
                    title: 'รายงานการบันทึกเวรตรวจการศูนย์ฟอกไต HD เอกชน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}'
                    }
                ],
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    paginate: {
                    previous: "ก่อนหน้า",
                    next: "ถัดไป"
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#lr').DataTable({
                dom: '<"row mb-3"' +
                        '<"col-md-6"l>' + // Show รายการ
                        '<"col-md-6 d-flex justify-content-end align-items-center gap-2"fB>' + // Search + Export
                    '>' +
                    'rt' +
                    '<"row mt-3"' +
                        '<"col-md-6"i>' + // Info
                        '<"col-md-6"p>' + // Pagination
                    '>',
                buttons: [
                    {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-success',
                    title: 'รายงานการบันทึกเวรตรวจการงานห้องคลอด LR วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}'
                    }
                ],
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    paginate: {
                    previous: "ก่อนหน้า",
                    next: "ถัดไป"
                    }
                }
            });
        });
    </script>
  
@endpush