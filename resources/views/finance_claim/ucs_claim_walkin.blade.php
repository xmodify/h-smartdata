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
    <div class="card border-success">
        <div class="card-header bg-success text-white">WALKIN การเข้ารับบริการผู้ป่วยนอก ปฐมภูมิกรณีเหตุสมควร  [ส่ง FDH] วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div class="card-body">   
            <div style="overflow-x:auto;">            
                <table id="eclaim_fdh" class="table table-bordered table-striped my-3">
                    <thead>
                    <tr class="table-success">
                        <th class="text-center">ลำดับ</th>                                       
                        <th class="text-center">วันที่รับบริการ</th>  
                        <th class="text-center">เวลา</th>                                                                           
                        <th class="text-center">Queue</th>
                        <th class="text-center">HN</th>
                        <th class="text-center">ชื่อ-สกุล</th>
                        <th class="text-center">อายุ</th>
                        <th class="text-center">สิทธิ</th>
                        <th class="text-center">ห้องตรวจ</th>
                        <th class="text-center">ความรุนแรง</th>  
                        <th class="text-center">ICD10</th>
                        <th class="text-center">อาการสำคัญ</th>                  
                        <th class="text-center">Refer</th>
                        <th class="text-center">ฟอกไต</th>            
                        <th class="text-center">Authen Code</th> 
                        <th class="text-center">Project Code</th>                                                
                        <th class="text-center">Upload FDH</th> 
                        <th class="text-center">ขอเบิกชดเชย</th> 
                        <th class="text-center">ค่ารักษา</th>
                        <th class="text-center">ชำระเงินเอง</th>
                        <th class="text-center">ลูกหนี้</th>
                        <th class="text-center">ปิดค่าใช้จ่าย</th>
                        <th class="text-center">Rep สปสช.</th> 
                        <th class="text-center">Rep ต้นสังกัด</th> 
                        <th class="text-center">Rep Error</th> 
                        <th class="text-center">Rep No.</th> 
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    <?php $sum_income = 0 ; ?>    
                    <?php $sum_rcpt_money = 0 ; ?>  
                    <?php $sum_rep_nhso = 0 ; ?>  
                    <?php $sum_rep_agency = 0 ; ?>  
                    @foreach($eclaim_fdh as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>   
                        <td align="right">{{ DateThai($row->vstdate) }}</td>        
                        <td align="center">{{ $row->vsttime }}</td>   
                        <td align="center">{{ $row->oqueue }}</td>
                        <td align="center">{{ $row->hn }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->age_y }}</td>
                        <td align="center">{{ $row->pttype }}</td>
                        <td align="center">{{ $row->dep }}</td>  
                        <td align="left">{{ $row->er_emergency_type }}</td>
                        <td align="right">{{ $row->pdx }}</td>
                        <td align="left">{{ $row->cc }}</td>                  
                        <td align="center">{{ $row->refer }}</td>                       
                        <td align="center">{{ $row->kidney }}</td> 
                        <td align="center">{{ $row->auth_code }}</td>
                        <td align="center">{{ $row->project }}</td>  
                        <td align="center">{{ $row->fdh }}</td>    
                        <td align="center" @if($row->request_funds == 'Y') style="color:green"
                            @elseif($row->request_funds == 'N') style="color:red" @endif>
                            <strong>{{ $row->request_funds }}</strong></td>       
                        <td align="right">{{ number_format($row->income,2) }}</td>
                        <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                        <td align="right">{{ number_format(($row->income)-($row->rcpt_money),2) }}</td>    
                        <td align="center">{{ $row->finance_lock }}</td>      
                        <td align="right">{{ number_format($row->rep_nhso,2) }}</td>
                        <td align="right">{{ number_format($row->rep_agency,2) }}</td>
                        <td align="center">{{ $row->rep_error }}</td>
                        <td align="center">{{ $row->rep_no }}</td>
                    </tr>
                    <?php $count++; ?>
                    <?php $sum_income += $row->income ; ?>
                    <?php $sum_rcpt_money += $row->rcpt_money ; ?>
                    <?php $sum_rep_nhso += $row->rep_nhso ; ?>
                    <?php $sum_rep_agency += $row->rep_agency ; ?>
                    @endforeach
                </table>
                <br>
                <div class="text-Primary">
                    <strong>ค่ารักษาพยาบาลทั้งหมด {{number_format($sum_income,2)}} บาท |</strong>
                    <strong>ชำระเงินเอง {{number_format($sum_rcpt_money,2)}} บาท |</strong>
                    <strong>ลูกหนี้ค่ารักษาพยาบาล {{number_format(($sum_income)-($sum_rcpt_money),2)}} บาท |</strong>
                    <strong>ชดเชย สปสช. {{number_format($sum_rep_nhso,2)}} บาท |</strong>
                    <strong>ชดเชย ต้นสังกัด. {{number_format($sum_rep_nhso,2)}} บาท</strong>
                </div>  
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="card border-Secondary"> 
        <div class="card-header bg-Secondary text-white">WALKIN การเข้ารับบริการผู้ป่วยนอก ปฐมภูมิกรณีเหตุสมควร [รอยืนยันส่ง FDH] วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div class="card-body">   
            <div style="overflow-x:auto;">            
                <table id="eclaim" class="table table-bordered table-striped my-3">
                    <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                                       
                        <th class="text-center">วันที่รับบริการ</th>  
                        <th class="text-center">เวลา</th>                                                                           
                        <th class="text-center">Queue</th>
                        <th class="text-center">HN</th>
                        <th class="text-center">ชื่อ-สกุล</th>
                        <th class="text-center">อายุ</th>
                        <th class="text-center">สิทธิ</th>
                        <th class="text-center">ห้องตรวจ</th>
                        <th class="text-center">ความรุนแรง</th>  
                        <th class="text-center">ICD10</th>
                        <th class="text-center">อาการสำคัญ</th>                  
                        <th class="text-center">Refer</th>
                        <th class="text-center">ฟอกไต</th>
                        <th class="text-center">Authen Code</th> 
                        <th class="text-center">Project Code</th>                                              
                        <th class="text-center">Upload Eclaim</th> 
                        <th class="text-center">Upload FDH</th> 
                        <th class="text-center">ขอเบิกชดเชย</th> 
                        <th class="text-center">ค่ารักษา</th>
                        <th class="text-center">ชำระเงินเอง</th>
                        <th class="text-center">ลูกหนี้</th>
                        <th class="text-center">ปิดค่าใช้จ่าย</th>
                        <th class="text-center">Rep สปสช.</th> 
                        <th class="text-center">Rep ต้นสังกัด</th> 
                        <th class="text-center">Rep Error</th> 
                        <th class="text-center">Rep No.</th> 
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    @foreach($eclaim as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>   
                        <td align="right">{{ DateThai($row->vstdate) }}</td>        
                        <td align="center">{{ $row->vsttime }}</td>   
                        <td align="center">{{ $row->oqueue }}</td>
                        <td align="center">{{ $row->hn }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->age_y }}</td>
                        <td align="center">{{ $row->pttype }}</td>
                        <td align="center">{{ $row->dep }}</td>  
                        <td align="left">{{ $row->er_emergency_type }}</td>
                        <td align="right">{{ $row->pdx }}</td>
                        <td align="left">{{ $row->cc }}</td>                  
                        <td align="center">{{ $row->refer }}</td>                       
                        <td align="center">{{ $row->kidney }}</td> 
                        <td align="center">{{ $row->auth_code }}</td>
                        <td align="center">{{ $row->project }}</td>  
                        <td align="center">{{ $row->eclaim }}</td> 
                        <td align="center">{{ $row->fdh }}</td>  
                        <td align="center" @if($row->request_funds == 'Y') style="color:green"
                            @elseif($row->request_funds == 'N') style="color:red" @endif>
                            <strong>{{ $row->request_funds }}</strong></td>           
                        <td align="right">{{ number_format($row->income,2) }}</td>
                        <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                        <td align="right">{{ number_format(($row->income)-($row->rcpt_money),2) }}</td>   
                        <td align="center">{{ $row->finance_lock }}</td> 
                        <td align="right">{{ number_format($row->rep_nhso,2) }}</td>
                        <td align="right">{{ number_format($row->rep_agency,2) }}</td>
                        <td align="center">{{ $row->rep_error }}</td>
                        <td align="center">{{ $row->rep_no }}</td>
                    </tr>
                    <?php $count++; ?>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="card border-success">
        <div class="card-header bg-success text-white">OP AE ข้ามจังหวัด  [ส่ง FDH] วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div class="card-body">   
            <div style="overflow-x:auto;">            
                <table id="eclaim_ae_fdh" class="table table-bordered table-striped my-3">
                    <thead>
                    <tr class="table-success">
                        <th class="text-center">ลำดับ</th>                                       
                        <th class="text-center">วันที่รับบริการ</th>  
                        <th class="text-center">เวลา</th>                                                                           
                        <th class="text-center">Queue</th>
                        <th class="text-center">HN</th>
                        <th class="text-center">ชื่อ-สกุล</th>
                        <th class="text-center">อายุ</th>
                        <th class="text-center">สิทธิ</th>
                        <th class="text-center">ห้องตรวจ</th>
                        <th class="text-center">ความรุนแรง</th>  
                        <th class="text-center">ICD10</th>
                        <th class="text-center">อาการสำคัญ</th>                  
                        <th class="text-center">Refer</th>
                        <th class="text-center">ฟอกไต</th>            
                        <th class="text-center">Authen Code</th> 
                        <th class="text-center">Project Code</th>                                                
                        <th class="text-center">Upload FDH</th>
                        <th class="text-center">ขอเบิกชดเชย</th>  
                        <th class="text-center">ค่ารักษา</th>
                        <th class="text-center">ชำระเงินเอง</th>
                        <th class="text-center">ลูกหนี้</th>
                        <th class="text-center">Rep สปสช.</th> 
                        <th class="text-center">Rep ต้นสังกัด</th> 
                        <th class="text-center">Rep Error</th> 
                        <th class="text-center">Rep No.</th> 
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    <?php $sum_income = 0 ; ?>    
                    <?php $sum_rcpt_money = 0 ; ?>  
                    <?php $sum_rep_nhso = 0 ; ?>  
                    <?php $sum_rep_agency = 0 ; ?>  
                    @foreach($eclaim_ae_fdh as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>   
                        <td align="right">{{ DateThai($row->vstdate) }}</td>        
                        <td align="center">{{ $row->vsttime }}</td>   
                        <td align="center">{{ $row->oqueue }}</td>
                        <td align="center">{{ $row->hn }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->age_y }}</td>
                        <td align="center">{{ $row->pttype }}</td>
                        <td align="center">{{ $row->dep }}</td>  
                        <td align="left">{{ $row->er_emergency_type }}</td>
                        <td align="right">{{ $row->pdx }}</td>
                        <td align="left">{{ $row->cc }}</td>                  
                        <td align="center">{{ $row->refer }}</td>                       
                        <td align="center">{{ $row->kidney }}</td> 
                        <td align="center">{{ $row->auth_code }}</td>
                        <td align="center">{{ $row->project }}</td>  
                        <td align="center">{{ $row->fdh }}</td>  
                        <td align="center" @if($row->request_funds == 'Y') style="color:green"
                            @elseif($row->request_funds == 'N') style="color:red" @endif>
                            <strong>{{ $row->request_funds }}</strong></td>            
                        <td align="right">{{ number_format($row->income,2) }}</td>
                        <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                        <td align="right">{{ number_format(($row->income)-($row->rcpt_money),2) }}</td>                   
                        <td align="right">{{ number_format($row->rep_nhso,2) }}</td>
                        <td align="right">{{ number_format($row->rep_agency,2) }}</td>
                        <td align="center">{{ $row->rep_error }}</td>
                        <td align="center">{{ $row->rep_no }}</td>
                    </tr>
                    <?php $count++; ?>
                    <?php $sum_income += $row->income ; ?>
                    <?php $sum_rcpt_money += $row->rcpt_money ; ?>
                    <?php $sum_rep_nhso += $row->rep_nhso ; ?>
                    <?php $sum_rep_agency += $row->rep_agency ; ?>
                    @endforeach
                </table>
                <br>
                <div class="text-Primary">
                    <strong>ค่ารักษาพยาบาลทั้งหมด {{number_format($sum_income,2)}} บาท |</strong>
                    <strong>ชำระเงินเอง {{number_format($sum_rcpt_money,2)}} บาท |</strong>
                    <strong>ลูกหนี้ค่ารักษาพยาบาล {{number_format(($sum_income)-($sum_rcpt_money),2)}} บาท |</strong>
                    <strong>ชดเชย สปสช. {{number_format($sum_rep_nhso,2)}} บาท |</strong>
                    <strong>ชดเชย ต้นสังกัด. {{number_format($sum_rep_nhso,2)}} บาท</strong>
                </div>  
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="card border-danger">
        <div class="card-header bg-danger text-white">OP AE [รอยืนยันส่ง FDH] วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div class="card-body">   
            <div style="overflow-x:auto;">            
                <table id="eclaim_ae" class="table table-bordered table-striped my-3">
                    <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                                       
                        <th class="text-center">วันที่รับบริการ</th>  
                        <th class="text-center">เวลา</th>                                                                           
                        <th class="text-center">Queue</th>
                        <th class="text-center">HN</th>
                        <th class="text-center">ชื่อ-สกุล</th>
                        <th class="text-center">อายุ</th>
                        <th class="text-center">สิทธิ</th>
                        <th class="text-center">ห้องตรวจ</th>
                        <th class="text-center">ความรุนแรง</th>  
                        <th class="text-center">ICD10</th>
                        <th class="text-center">อาการสำคัญ</th>                  
                        <th class="text-center">Refer</th>
                        <th class="text-center">ฟอกไต</th>
                        <th class="text-center">Authen Code</th> 
                        <th class="text-center">Project Code</th>                                              
                        <th class="text-center">Upload Eclaim</th> 
                        <th class="text-center">Upload FDH</th> 
                        <th class="text-center">ขอเบิกชดเชย</th> 
                        <th class="text-center">ค่ารักษา</th>
                        <th class="text-center">ชำระเงินเอง</th>
                        <th class="text-center">ลูกหนี้</th>
                        <th class="text-center">Rep สปสช.</th> 
                        <th class="text-center">Rep ต้นสังกัด</th> 
                        <th class="text-center">Rep Error</th> 
                        <th class="text-center">Rep No.</th> 
                    </tr>
                    </thead> 
                    <?php $count = 1 ; ?>
                    @foreach($eclaim_ae as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>   
                        <td align="right">{{ DateThai($row->vstdate) }}</td>        
                        <td align="center">{{ $row->vsttime }}</td>   
                        <td align="center">{{ $row->oqueue }}</td>
                        <td align="center">{{ $row->hn }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->age_y }}</td>
                        <td align="center">{{ $row->pttype }}</td>
                        <td align="center">{{ $row->dep }}</td>  
                        <td align="left">{{ $row->er_emergency_type }}</td>
                        <td align="right">{{ $row->pdx }}</td>
                        <td align="left">{{ $row->cc }}</td>                  
                        <td align="center">{{ $row->refer }}</td>                       
                        <td align="center">{{ $row->kidney }}</td> 
                        <td align="center">{{ $row->auth_code }}</td>
                        <td align="center">{{ $row->project }}</td>  
                        <td align="center">{{ $row->eclaim }}</td>  
                        <td align="center">{{ $row->fdh }}</td>  
                        <td align="center" @if($row->request_funds == 'Y') style="color:green"
                            @elseif($row->request_funds == 'N') style="color:red" @endif>
                            <strong>{{ $row->request_funds }}</strong></td>           
                        <td align="right">{{ number_format($row->income,2) }}</td>
                        <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                        <td align="right">{{ number_format(($row->income)-($row->rcpt_money),2) }}</td>             
                        <td align="right">{{ number_format($row->rep_nhso,2) }}</td>
                        <td align="right">{{ number_format($row->rep_agency,2) }}</td>
                        <td align="center">{{ $row->rep_error }}</td>
                        <td align="center">{{ $row->rep_no }}</td>
                    </tr>
                    <?php $count++; ?>
                    @endforeach
                </table>
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
        $('#eclaim_fdh').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#eclaim').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#eclaim_ae_fdh').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#eclaim_ae').DataTable();
    });
</script>
