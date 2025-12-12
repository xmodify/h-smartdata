@extends('layouts.app')

@section('content')

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
                    <button onclick="fetchData()" type="submit" class="btn btn-primary my-1 ">{{ __('ค้นหา') }}</button>
                </div>
            </div>
        </form> 
        <div class="alert alert-success text-primary" role="alert"><strong>ข้อมูลการใช้ยา HD วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>
    
        <div class="card-body">
            <!-- Pills Tabs -->
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="opd-tab" data-bs-toggle="pill" data-bs-target="#opd" type="button" role="tab" aria-controls="opd" aria-selected="false">ผู้ป่วยนอก</button>
                </li>       
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ipd-tab" data-bs-toggle="pill" data-bs-target="#ipd" type="button" role="tab" aria-controls="ipd" aria-selected="false">ผู้ป่วยใน</button>
                </li>
            </ul>
            <div class="tab-content pt-2" id="myTabContent">
                <div class="tab-pane fade show active" id="opd" role="tabpanel" aria-labelledby="opd-tab">
                    <div style="overflow-x:auto;">            
                        <table id="t_opd" class="table table-bordered table-striped my-3" width="100%">
                            <thead>
                                <tr class="table-primary">                                   
                                    <th rowspan="2" class="text-center text-primary">รหัสยา</th>
                                    <th rowspan="2" class="text-center text-primary">ชื่อยา</th>
                                    <th rowspan="2" class="text-center text-primary">ชื่อยาสามัญ</th>
                                    <th colspan="4" class="text-center text-primary">TOTAL</th>
                                    <!-- กลุ่มสิทธิ -->
                                    <th colspan="4" class="text-center">UCS</th>
                                    <th colspan="4" class="text-center">OFC</th>
                                    <th colspan="4" class="text-center">LGO</th>
                                    <th colspan="4" class="text-center">SSS</th>
                                    <th colspan="4" class="text-center">Other</th>
                                </tr>
                                <tr class="table-primary">
                                    <!-- TOTAL -->
                                    <th class="text-center text-primary">VISIT</th>
                                    <th class="text-center text-primary">QTY</th>
                                    <th class="text-center text-primary">COST</th>
                                    <th class="text-center text-primary">PRICE</th>
                                    <!-- UCS -->
                                    <th class="text-center">VISIT</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-center">COST</th>
                                    <th class="text-center">PRICE</th>
                                    <!-- OFC -->
                                    <th class="text-center">VISIT</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-center">COST</th>
                                    <th class="text-center">PRICE</th>
                                    <!-- LGO -->
                                    <th class="text-center">VISIT</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-center">COST</th>
                                    <th class="text-center">PRICE</th>
                                    <!-- SSS/SSI -->
                                    <th class="text-center">VISIT</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-center">COST</th>
                                    <th class="text-center">PRICE</th>
                                    <!-- OTHER -->
                                    <th class="text-center">VISIT</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-center">COST</th>
                                    <th class="text-center">PRICE</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($opd as $row)
                                <tr>                                   
                                    <td align="center">{{ $row->icode }}</td>
                                    <td align="left">{{ $row->name }}</td>
                                    <td align="left">{{ $row->generic_name }}</td>
                                    <!-- TOTAL -->
                                    <td align="right">{{ $row->total_visit }}</td>
                                    <td align="right">{{ $row->total_qty }}</td>
                                    <td align="right">{{ number_format($row->total_cost,2) }}</td>
                                    <td align="right">{{ number_format($row->total_price,2) }}</td>
                                    <!-- UCS -->
                                    <td align="right">{{ $row->ucs_visit }}</td>
                                    <td align="right">{{ $row->ucs_qty }}</td>
                                    <td align="right">{{ number_format($row->ucs_cost,2) }}</td>
                                    <td align="right">{{ number_format($row->ucs_price,2) }}</td>
                                    <!-- OFC -->
                                    <td align="right">{{ $row->ofc_visit }}</td>
                                    <td align="right">{{ $row->ofc_qty }}</td>
                                    <td align="right">{{ number_format($row->ofc_cost,2) }}</td>
                                    <td align="right">{{ number_format($row->ofc_price,2) }}</td>
                                    <!-- LGO -->
                                    <td align="right">{{ $row->lgo_visit }}</td>
                                    <td align="right">{{ $row->lgo_qty }}</td>
                                    <td align="right">{{ number_format($row->lgo_cost,2) }}</td>
                                    <td align="right">{{ number_format($row->lgo_price,2) }}</td>
                                    <!-- SSS/SSI -->
                                    <td align="right">{{ $row->sss_visit }}</td>
                                    <td align="right">{{ $row->sss_qty }}</td>
                                    <td align="right">{{ number_format($row->sss_cost,2) }}</td>
                                    <td align="right">{{ number_format($row->sss_price,2) }}</td>
                                    <!-- OTHER -->
                                    <td align="right">{{ $row->other_visit }}</td>
                                    <td align="right">{{ $row->other_qty }}</td>
                                    <td align="right">{{ number_format($row->other_cost,2) }}</td>
                                    <td align="right">{{ number_format($row->other_price,2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="3" class="text-end">รวมทั้งหมด</td>
                                    <!-- TOTAL -->
                                    <td class="text-end">{{ number_format($opd->sum('total_visit')) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('total_qty')) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('total_cost'),2) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('total_price'),2) }}</td>
                                    <!-- UCS -->
                                    <td class="text-end">{{ number_format($opd->sum('ucs_visit')) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('ucs_qty')) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('ucs_cost'),2) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('ucs_price'),2) }}</td>
                                    <!-- OFC -->
                                    <td class="text-end">{{ number_format($opd->sum('ofc_visit')) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('ofc_qty')) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('ofc_cost'),2) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('ofc_price'),2) }}</td>
                                    <!-- LGO -->
                                    <td class="text-end">{{ number_format($opd->sum('lgo_visit')) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('lgo_qty')) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('lgo_cost'),2) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('lgo_price'),2) }}</td>
                                    <!-- SSS/SSI -->
                                    <td class="text-end">{{ number_format($opd->sum('sss_visit')) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('sss_qty')) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('sss_cost'),2) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('sss_price'),2) }}</td>
                                    <!-- OTHER -->
                                    <td class="text-end">{{ number_format($opd->sum('other_visit')) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('other_qty')) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('other_cost'),2) }}</td>
                                    <td class="text-end">{{ number_format($opd->sum('other_price'),2) }}</td>
                                </tr>
                            </tfoot>
                        </table>                 
                    </div>          
                </div>      
                <div class="tab-pane fade" id="ipd" role="tabpanel" aria-labelledby="ipd-tab">
                    <div style="overflow-x:auto;">            
                        <table id="t_ipd" class="table table-bordered table-striped my-3" width="100%">
                            <thead>
                                <tr class="table-primary">
                                    <th rowspan="2" class="text-center text-primary">รหัสยา</th>
                                    <th rowspan="2" class="text-center text-primary">ชื่อยา</th>
                                    <th rowspan="2" class="text-center text-primary">ชื่อยาสามัญ</th>
                                    <th colspan="4" class="text-center text-primary">TOTAL</th>
                                    <!-- กลุ่มสิทธิ -->
                                    <th colspan="4" class="text-center">UCS</th>
                                    <th colspan="4" class="text-center">OFC</th>
                                    <th colspan="4" class="text-center">LGO</th>
                                    <th colspan="4" class="text-center">SSS</th>
                                    <th colspan="4" class="text-center">Other</th>
                                </tr>
                                <tr class="table-primary">
                                    <!-- TOTAL -->
                                    <th class="text-center text-primary">AN</th>
                                    <th class="text-center text-primary">QTY</th>
                                    <th class="text-center text-primary">COST</th>
                                    <th class="text-center text-primary">PRICE</th>
                                    <!-- UCS -->
                                    <th class="text-center">AN</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-center">COST</th>
                                    <th class="text-center">PRICE</th>
                                    <!-- OFC -->
                                    <th class="text-center">AN</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-center">COST</th>
                                    <th class="text-center">PRICE</th>
                                    <!-- LGO -->
                                    <th class="text-center">AN</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-center">COST</th>
                                    <th class="text-center">PRICE</th>
                                    <!-- SSS/SSI -->
                                    <th class="text-center">AN</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-center">COST</th>
                                    <th class="text-center">PRICE</th>
                                    <!-- OTHER -->
                                    <th class="text-center">AN</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-center">COST</th>
                                    <th class="text-center">PRICE</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($ipd as $row)
                                <tr>
                                    <td align="center">{{ $row->icode }}</td>
                                    <td align="left">{{ $row->name }}</td>
                                    <td align="left">{{ $row->generic_name }}</td>
                                    <!-- TOTAL -->
                                    <td align="right">{{ $row->total_an }}</td>
                                    <td align="right">{{ $row->total_qty }}</td>
                                    <td align="right">{{ number_format($row->total_cost,2) }}</td>
                                    <td align="right">{{ number_format($row->total_price,2) }}</td>
                                    <!-- UCS -->
                                    <td align="right">{{ $row->ucs_an }}</td>
                                    <td align="right">{{ $row->ucs_qty }}</td>
                                    <td align="right">{{ number_format($row->ucs_cost,2) }}</td>
                                    <td align="right">{{ number_format($row->ucs_price,2) }}</td>
                                    <!-- OFC -->
                                    <td align="right">{{ $row->ofc_an }}</td>
                                    <td align="right">{{ $row->ofc_qty }}</td>
                                    <td align="right">{{ number_format($row->ofc_cost,2) }}</td>
                                    <td align="right">{{ number_format($row->ofc_price,2) }}</td>
                                    <!-- LGO -->
                                    <td align="right">{{ $row->lgo_an }}</td>
                                    <td align="right">{{ $row->lgo_qty }}</td>
                                    <td align="right">{{ number_format($row->lgo_cost,2) }}</td>
                                    <td align="right">{{ number_format($row->lgo_price,2) }}</td>
                                    <!-- SSS / SSI -->
                                    <td align="right">{{ $row->sss_an }}</td>
                                    <td align="right">{{ $row->sss_qty }}</td>
                                    <td align="right">{{ number_format($row->sss_cost,2) }}</td>
                                    <td align="right">{{ number_format($row->sss_price,2) }}</td>
                                    <!-- OTHER -->
                                    <td align="right">{{ $row->other_an }}</td>
                                    <td align="right">{{ $row->other_qty }}</td>
                                    <td align="right">{{ number_format($row->other_cost,2) }}</td>
                                    <td align="right">{{ number_format($row->other_price,2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="3" class="text-end">รวมทั้งหมด</td>
                                    <!-- TOTAL -->
                                    <td class="text-end">{{ number_format($ipd->sum('total_an')) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('total_qty')) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('total_cost'),2) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('total_price'),2) }}</td>
                                    <!-- UCS -->
                                    <td class="text-end">{{ number_format($ipd->sum('ucs_an')) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('ucs_qty')) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('ucs_cost'),2) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('ucs_price'),2) }}</td>
                                    <!-- OFC -->
                                    <td class="text-end">{{ number_format($ipd->sum('ofc_an')) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('ofc_qty')) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('ofc_cost'),2) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('ofc_price'),2) }}</td>
                                    <!-- LGO -->
                                    <td class="text-end">{{ number_format($ipd->sum('lgo_an')) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('lgo_qty')) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('lgo_cost'),2) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('lgo_price'),2) }}</td>
                                    <!-- SSS / SSI -->
                                    <td class="text-end">{{ number_format($ipd->sum('sss_an')) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('sss_qty')) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('sss_cost'),2) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('sss_price'),2) }}</td>
                                    <!-- OTHER -->
                                    <td class="text-end">{{ number_format($ipd->sum('other_an')) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('other_qty')) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('other_cost'),2) }}</td>
                                    <td class="text-end">{{ number_format($ipd->sum('other_price'),2) }}</td>
                                </tr>
                                </tfoot>
                        </table>             
                    </div>  
                </div>
            </div>
            <!-- Pills Tabs -->
        </div> 
    </div>   

    <script>
        function showLoading() {
            Swal.fire({
                title: 'กำลังโหลด...',
                text: 'กรุณารอสักครู่',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        function fetchData() {
            showLoading();
        }
    </script>

@endsection

    @push('scripts')
    <script>
        $(document).ready(function () {
        $('#t_opd').DataTable({
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
                title: 'ข้อมูลการใช้ยา HD ผู้ป่วยนอก วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}'
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
        $('#t_ipd').DataTable({
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
                title: 'ข้อมูลการใช้ยา HD ผู้ป่วยใน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}'
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

