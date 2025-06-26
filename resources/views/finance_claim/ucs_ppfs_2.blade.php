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
</style> 

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
                <button type="submit" class="btn btn-primary my-1 ">{{ __('ค้นหา') }}</button>
            </div>
        </div>
    </form> 
    <div class="alert alert-primary text-primary" role="alert"><strong>PPFS บริการฝากครรภ์ ANC วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>
    <div class="card-body"> 
        <!-- Pills Tabs -->
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="search-tab" data-bs-toggle="pill" data-bs-target="#search" type="button" role="tab" aria-controls="search" aria-selected="false">รอส่ง Claim</button>
            </li>       
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="claim_fdh-tab" data-bs-toggle="pill" data-bs-target="#claim_fdh" type="button" role="tab" aria-controls="claim_fdh" aria-selected="false">ส่ง FDH</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="claim_eclaim-tab" data-bs-toggle="pill" data-bs-target="#claim_claim" type="button" role="tab" aria-controls="claim_claim" aria-selected="false">ส่ง E-Claim</button>
            </li>
        </ul>
        <div class="tab-content pt-2" id="myTabContent">
            <div class="tab-pane fade show active" id="search" role="tabpanel" aria-labelledby="search-tab">
                <div style="overflow-x:auto;">
                    <table id ="t_search" class="table table-bordered table-striped my-3" width="100%">
                        <thead>
                        <tr class="table-secondary">
                            <th class="text-center">ลำดับ</th>
                            <th class="text-center">Authen</th>
                            <th class="text-center">ปิดสิทธิ</th>
                            <th class="text-center">ประสงค์เบิก</th> 
                            <th class="text-center">พร้อมส่ง</th>
                            <th class="text-center">ANC</th>
                            <th class="text-center">Project</th>
                            <th class="text-center" width="6%">วันที่</th>
                            <th class="text-center">Queue</th>
                            <th class="text-center">HN</th>
                            <th class="text-center">ชื่อ-สกุล</th>
                            <th class="text-center">อายุ</th>
                            <th class="text-center" width="5%">สิทธิ</th>                                
                            <th class="text-center">ICD10</th>
                            <th class="text-center">ICD9</th>   
                            <th class="text-center">ค่ารักษาทั้งหมด</th>
                            <th class="text-center">รายการเรียกเก็บ</th>
                            <th class="text-center">ราคาเรียกเก็บ</th>
                            <th class="text-center">Upload Eclaim</th> 
                            <th class="text-center">Upload FDH</th>                               
                        </tr>
                        </thead>
                        <?php $count = 1 ; ?>
                        <?php $sum_sum_price = 0 ; ?>   
                        @foreach($search as $row)
                        <tr>
                            <td align="center">{{ $count }}</td>
                            <td align="center" @if($row->auth_code == 'Y') style="color:green"
                                @elseif($row->auth_code == 'N') style="color:red" @endif>
                                <strong>{{ $row->auth_code }}</strong></td>
                            <td align="center" @if($row->endpoint == 'Y') style="color:green"
                                @elseif($row->endpoint == 'N') style="color:red" @endif>
                                <strong>{{ $row->endpoint }}</strong></td> 
                            <td align="center" @if($row->request_funds == 'Y') style="color:green"
                                @elseif($row->request_funds == 'N') style="color:red" @endif>
                                <strong>{{ $row->request_funds }}</strong></td>  
                            <td align="center" @if($row->confirm_and_locked == 'Y') style="color:green"
                                @elseif($row->confirm_and_locked == 'N') style="color:red" @endif>
                                <strong>{{ $row->confirm_and_locked }}</strong></td>  
                            <td align="center" style="color:#0d6efd">{{ $row->anc_service_number }}</td>  
                            <td align="center">{{ $row->project }}</td>                                       
                            <td align="right">{{ DateThai($row->vstdate) }}</td>
                            <td align="center">{{ $row->oqueue }}</td>
                            <td align="center">{{ $row->hn }}</td>
                            <td align="left">{{ $row->ptname }}</td>
                            <td align="center">{{ $row->age_y }}</td>
                            <td align="center">{{ $row->pttype }}</td>                                
                            <td align="right">{{ $row->pdx }}</td>
                            <td align="right">{{ $row->icd9 }}</td>  
                            <td align="center">{{ number_format($row->income,2) }}</td>
                            <td align="left">{{ $row->nondrug }}</td>
                            <td align="center">{{ number_format($row->sum_price,2) }}</td>
                            <td align="center">{{ $row->eclaim }}</td>   
                            <td align="center">{{ $row->fdh }}</td> 
                        </tr>
                        <?php $count++; ?>
                        <?php $sum_sum_price += $row->sum_price ; ?>
                        @endforeach
                    </table>
                    <div class="text-center text-primary">
                        <h4>
                            รวมราคารอเรียกเก็บ <strong>{{number_format($sum_sum_price,2)}} </strong>บาท 
                        </h4>
                    </div>
                    <br>
                </div>
            </div>
            <div class="tab-pane fade" id="claim_fdh" role="tabpanel" aria-labelledby="claim_fdh-tab">
                <div style="overflow-x:auto;">
                    <table id = "t_claim_fdh" class="table table-bordered table-striped my-3" width="100%">
                        <thead>
                        <tr class="table-success">
                            <th class="text-center">ลำดับ</th>
                            <th class="text-center">Authen</th>
                            <th class="text-center">ปิดสิทธิ</th>
                            <th class="text-center">ประสงค์เบิก</th> 
                            <th class="text-center">พร้อมส่ง</th>
                            <th class="text-center">ANC</th>
                            <th class="text-center">Project</th>
                            <th class="text-center" width="6%">วันที่</th>
                            <th class="text-center">Queue</th>
                            <th class="text-center">HN</th>
                            <th class="text-center">ชื่อ-สกุล</th>
                            <th class="text-center">อายุ</th>
                            <th class="text-center" width="5%">สิทธิ</th>                                
                            <th class="text-center">ICD10</th>
                            <th class="text-center">ICD9</th>   
                            <th class="text-center">ค่ารักษาทั้งหมด</th>
                            <th class="text-center">รายการเรียกเก็บ</th>
                            <th class="text-center">ราคาเรียกเก็บ</th>
                            <th class="text-center">Upload FDH</th>       
                            <th class="text-center">Rep NHSO</th> 
                            <th class="text-center">Error</th> 
                            <th class="text-center">STM ชดเชย</th> 
                            <th class="text-center">ผลต่าง</th> 
                            <th class="text-center">REP</th> 
                        </tr>
                        </thead>
                        <?php $count = 1 ; ?>
                        <?php $sum_sum_price = 0 ; ?>  
                        <?php $sum_receive_pp = 0 ; ?>  
                        @foreach($claim_fdh as $row)
                        <tr>
                            <td align="center">{{ $count }}</td>
                            <td align="center" @if($row->auth_code == 'Y') style="color:green"
                                @elseif($row->auth_code == 'N') style="color:red" @endif>
                                <strong>{{ $row->auth_code }}</strong></td>
                            <td align="center" @if($row->endpoint == 'Y') style="color:green"
                                @elseif($row->endpoint == 'N') style="color:red" @endif>
                                <strong>{{ $row->endpoint }}</strong></td> 
                            <td align="center" @if($row->request_funds == 'Y') style="color:green"
                                @elseif($row->request_funds == 'N') style="color:red" @endif>
                                <strong>{{ $row->request_funds }}</strong></td>  
                            <td align="center" @if($row->confirm_and_locked == 'Y') style="color:green"
                                @elseif($row->confirm_and_locked == 'N') style="color:red" @endif>
                                <strong>{{ $row->confirm_and_locked }}</strong></td>  
                            <td align="center" style="color:#0d6efd">{{ $row->anc_service_number }}</td>  
                            <td align="center">{{ $row->project }}</td>                                       
                            <td align="right" width="6%">{{ DateThai($row->vstdate) }}</td>
                            <td align="center">{{ $row->oqueue }}</td>
                            <td align="center">{{ $row->hn }}</td>
                            <td align="left">{{ $row->ptname }}</td>
                            <td align="center">{{ $row->age_y }}</td>
                            <td align="center">{{ $row->pttype }}</td>                                
                            <td align="right">{{ $row->pdx }}</td>
                            <td align="right">{{ $row->icd9 }}</td>  
                            <td align="center">{{ number_format($row->income,2) }}</td>
                            <td align="left">{{ $row->nondrug }}</td>
                            <td align="center">{{ number_format($row->sum_price,2) }}</td>       
                            <td align="center">{{ $row->fdh }}</td>  
                            <td align="right">{{ number_format($row->rep_nhso,2) }}</td>
                            <td align="center">{{ $row->rep_error }}</td>
                            <td align="center">{{ number_format($row->receive_pp,2) }}</td>
                            <td align="center">{{ number_format($row->receive_pp-$row->sum_price,2) }}</td>
                            <td align="center">{{ $row->repno }}</td>
                        </tr>
                        <?php $count++; ?>
                        <?php $sum_sum_price += $row->sum_price ; ?>
                        <?php $sum_receive_pp += $row->receive_pp ; ?>
                        @endforeach
                    </table>
                    <div class="text-center text-primary">
                        <h4>
                            รวมราคาเรียกเก็บทั้งหมด <strong>{{number_format($sum_sum_price,2)}} </strong>บาท |
                            ชดเชยทั้งหมด <strong>{{number_format($sum_receive_pp,2)}} </strong>บาท |
                            ส่วนต่าง <strong>{{number_format($sum_receive_pp-$sum_sum_price,2)}} </strong>บาท
                        </h4>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="claim_claim" role="tabpanel" aria-labelledby="claim_claim-tab">
                <div style="overflow-x:auto;">
                    <table id = "t_claim_eclaim" class="table table-bordered table-striped my-3" width="100%">
                        <thead>
                        <tr class="table-success">
                            <th class="text-center">ลำดับ</th>
                            <th class="text-center">Authen</th>
                            <th class="text-center">ปิดสิทธิ</th>
                            <th class="text-center">ประสงค์เบิก</th> 
                            <th class="text-center">พร้อมส่ง</th>
                            <th class="text-center">ANC</th>
                            <th class="text-center">Project</th>
                            <th class="text-center" width="6%">วันที่</th>
                            <th class="text-center">Queue</th>
                            <th class="text-center">HN</th>
                            <th class="text-center">ชื่อ-สกุล</th>
                            <th class="text-center">อายุ</th>
                            <th class="text-center" width="5%">สิทธิ</th>                                
                            <th class="text-center">ICD10</th>
                            <th class="text-center">ICD9</th>   
                            <th class="text-center">ค่ารักษาทั้งหมด</th>
                            <th class="text-center">รายการเรียกเก็บ</th>
                            <th class="text-center">ราคาเรียกเก็บ</th>
                            <th class="text-center">Upload Eclaim</th>       
                            <th class="text-center">Rep NHSO</th> 
                            <th class="text-center">Error</th> 
                            <th class="text-center">STM ชดเชย</th> 
                            <th class="text-center">ผลต่าง</th> 
                            <th class="text-center">REP</th> 
                        </tr>
                        </thead>
                        <?php $count = 1 ; ?>
                        <?php $sum_sum_price = 0 ; ?>  
                        <?php $sum_receive_pp = 0 ; ?>  
                        @foreach($claim_eclaim as $row)
                        <tr>
                            <td align="center">{{ $count }}</td>
                            <td align="center" @if($row->auth_code == 'Y') style="color:green"
                                @elseif($row->auth_code == 'N') style="color:red" @endif>
                                <strong>{{ $row->auth_code }}</strong></td>
                            <td align="center" @if($row->endpoint == 'Y') style="color:green"
                                @elseif($row->endpoint == 'N') style="color:red" @endif>
                                <strong>{{ $row->endpoint }}</strong></td> 
                            <td align="center" @if($row->request_funds == 'Y') style="color:green"
                                @elseif($row->request_funds == 'N') style="color:red" @endif>
                                <strong>{{ $row->request_funds }}</strong></td>  
                            <td align="center" @if($row->confirm_and_locked == 'Y') style="color:green"
                                @elseif($row->confirm_and_locked == 'N') style="color:red" @endif>
                                <strong>{{ $row->confirm_and_locked }}</strong></td>  
                            <td align="center" style="color:#0d6efd">{{ $row->anc_service_number }}</td>  
                            <td align="center">{{ $row->project }}</td>                                       
                            <td align="right" width="6%">{{ DateThai($row->vstdate) }}</td>
                            <td align="center">{{ $row->oqueue }}</td>
                            <td align="center">{{ $row->hn }}</td>
                            <td align="left">{{ $row->ptname }}</td>
                            <td align="center">{{ $row->age_y }}</td>
                            <td align="center">{{ $row->pttype }}</td>                                
                            <td align="right">{{ $row->pdx }}</td>
                            <td align="right">{{ $row->icd9 }}</td>  
                            <td align="center">{{ number_format($row->income,2) }}</td>
                            <td align="left">{{ $row->nondrug }}</td>
                            <td align="center">{{ number_format($row->sum_price,2) }}</td>       
                            <td align="center">{{ $row->eclaim }}</td>  
                            <td align="right">{{ number_format($row->rep_nhso,2) }}</td>
                            <td align="center">{{ $row->rep_error }}</td>
                            <td align="center">{{ number_format($row->receive_pp,2) }}</td>
                            <td align="center">{{ number_format($row->receive_pp-$row->sum_price,2) }}</td>
                            <td align="center">{{ $row->repno }}</td>
                        </tr>
                        <?php $count++; ?>
                        <?php $sum_sum_price += $row->sum_price ; ?>
                        <?php $sum_receive_pp += $row->receive_pp ; ?>
                        @endforeach
                    </table>
                    <div class="text-center text-primary">
                        <h4>
                            รวมราคาเรียกเก็บทั้งหมด <strong>{{number_format($sum_sum_price,2)}} </strong>บาท |
                            ชดเชยทั้งหมด <strong>{{number_format($sum_receive_pp,2)}} </strong>บาท |
                            ส่วนต่าง <strong>{{number_format($sum_receive_pp-$sum_sum_price,2)}} </strong>บาท
                        </h4>
                    </div>
                </div>
            </div>
        </div> <!-- Pills Tabs -->
    </div>
</div>
@endsection
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#t_search').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#t_claim_fdh').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#t_claim_eclaim').DataTable();
    });
</script>
