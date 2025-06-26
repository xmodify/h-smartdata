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
        <div class="card-header bg-success text-white"> บุคคลที่มีปัญหาสถานะและสิทธิ STP ผู้ป่วยใน [ส่ง FDH] วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div class="card-body">  
            <div style="overflow-x:auto;">               
                <table id="debtor_fdh" class="table table-bordered table-striped my-3">
                    <thead>
                    <tr class="table-success">
                        <th class="text-center">ลำดับ</th>
                        <th class="text-center">ตึกผู้ป่วย</th>
                        <th class="text-center">วันที่ Admit</th>
                        <th class="text-center">วันที่ Discharge</th>
                        <th class="text-center">HN</th>
                        <th class="text-center">AN</th>
                        <th class="text-center">ชื่อ-สกุล</th>
                        <th class="text-center">อายุ</th>
                        <th class="text-center">สิทธิ</th>
                        <th class="text-center">วินิจฉัยแพทย์</th>
                        <th class="text-center">รหัสโรค</th>
                        <th class="text-center">หัตถการ</th>
                        <th class="text-center">Authen Code</th>
                        <th class="text-center">Refer</th>
                        <th class="text-center">AdjRW</th>
                        <th class="text-center">ค่ารักษา</th>  
                        <th class="text-center">ชำระเอง</th>
                        <th class="text-center">ลูกหนี้</th>    
                        <th class="text-center">Upload FDH</th>  
                        <th class="text-center">Rep NHSO</th> 
                        <th class="text-center">Error</th> 
                        <th class="text-center">STM INST</th>
                        <th class="text-center">STM AE</th>
                        <th class="text-center">STM AdjRW</th> 
                        <th class="text-center">STM อัตราจ่าย</th>
                        <th class="text-center">STM จ่ายตาม AdjRW</th>
                        <th class="text-center">STM ชดเชยทั้งหมด</th> 
                        <th class="text-center">ผลต่าง</th> 
                        <th class="text-center">REP</th>                       
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    <?php $sum_debtor = 0 ; ?> 
                    <?php $sum_receive_inst = 0 ; ?>   
                    <?php $sum_receive_ae_ae = 0 ; ?>  
                    <?php $sum_receive_ip_compensate_pay = 0 ; ?>  
                    <?php $sum_receive_total = 0 ; ?>  
                    @foreach($eclaim_fdh as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>
                        <td align="right">{{$row->ward}}</td>
                        <td align="right">{{ DateThai($row->regdate) }}</td>
                        <td align="right">{{ DateThai($row->dchdate) }}</td>
                        <td align="center">{{ $row->hn }}</td>
                        <td align="center">{{ $row->an }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->age_y }}</td>
                        <td align="center">{{ $row->pttype }}</td>
                        <td align="left">{{ $row->diag_text_list }}</td>
                        <td align="right">{{ $row->icd10 }}</td>
                        <td align="right">{{ $row->icd9 }}</td>
                        <td align="right">{{ $row->auth_code }}</td>
                        <td align="right">{{ $row->refer }}</td>
                        <td align="right">{{ $row->adjrw }}</td>                        
                        <td align="right">{{ number_format($row->income,2) }}</td>
                        <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                        <td align="right">{{ number_format($row->income-$row->rcpt_money,2) }}</td>
                        <td align="center">{{ $row->fdh }}</td>
                        <td align="right">{{ number_format($row->rep_nhso,2) }}</td>
                        <td align="center">{{ $row->rep_error }}</td>
                        <td align="right">{{ number_format($row->receive_inst,2) }}</td>
                        <td align="right">{{ number_format($row->receive_ae_ae,2) }}</td>
                        <td align="right">{{ number_format($row->fund_ip_adjrw,2) }}</td>
                        <td align="right">{{ number_format($row->fund_ip_payrate,2) }}</td>
                        <td align="right">{{ number_format($row->receive_ip_compensate_pay,2) }}</td>
                        <td align="right">{{ number_format($row->receive_total,2) }}</td>
                        <td align="right">{{ number_format($row->receive_total-($row->income-$row->rcpt_money),2) }}</td>
                        <td align="center">{{ $row->repno }}</td>
                    </tr>
                    <?php $count++; ?>
                    <?php $sum_debtor += ($row->income-$row->rcpt_money) ; ?>
                    <?php $sum_receive_inst += $row->receive_inst ; ?>
                    <?php $sum_receive_ae_ae += $row->receive_ae_ae ; ?>
                    <?php $sum_receive_ip_compensate_pay += $row->receive_ip_compensate_pay ; ?>
                    <?php $sum_receive_total += $row->receive_total ; ?>
                    @endforeach
                </table>
                <div class="text-center text-primary">
                    <h4>
                        รวมลูกหนี้ทั้งหมด <strong>{{number_format($sum_debtor,2)}} </strong>บาท |
                        ชดเชยทั้งหมด <strong>{{number_format($sum_receive_total,2)}} </strong>บาท |
                        ส่วนต่าง <strong>{{number_format($sum_receive_total-$sum_debtor,2)}} </strong>บาท
                    </h4>
                </div>
            </div>  
        </div>
    </div>
 </div>
 <br>
 <div class="container-fluid">
    <div class="card border-Secondary">
        <div class="card-header bg-Secondary text-white">บุคคลที่มีปัญหาสถานะและสิทธิ STP ผู้ป่วยใน [รอยืนยันส่ง FDH] วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div class="card-body">  
            <div style="overflow-x:auto;">               
                <table id="debtor" class="table table-bordered table-striped my-3">
                    <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>
                        <th class="text-center">ตึกผู้ป่วย</th>
                        <th class="text-center">วันที่ Admit</th>
                        <th class="text-center">วันที่ Discharge</th>
                        <th class="text-center">HN</th>
                        <th class="text-center">AN</th>
                        <th class="text-center">ชื่อ-สกุล</th>
                        <th class="text-center">อายุ</th>
                        <th class="text-center">สิทธิ</th>
                        <th class="text-center">วินิจฉัยแพทย์</th>
                        <th class="text-center">รหัสโรค</th>
                        <th class="text-center">หัตถการ</th>
                        <th class="text-center">Authen Code</th>
                        <th class="text-center">Refer</th>
                        <th class="text-center">AdjRW</th>
                        <th class="text-center">ค่ารักษา</th>  
                        <th class="text-center">ชำระเอง</th>
                        <th class="text-center">ลูกหนี้</th>    
                        <th class="text-center">Upload FDH</th>  
                        <th class="text-center">Rep NHSO</th> 
                        <th class="text-center">Error</th> 
                        <th class="text-center">STM INST</th>
                        <th class="text-center">STM AE</th>
                        <th class="text-center">STM AdjRW</th> 
                        <th class="text-center">STM อัตราจ่าย</th>
                        <th class="text-center">STM จ่ายตาม AdjRW</th>
                        <th class="text-center">STM ชดเชยทั้งหมด</th> 
                        <th class="text-center">ผลต่าง</th> 
                        <th class="text-center">REP</th>                       
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?> 
                    <?php $sum_debtor = 0 ; ?> 
                    <?php $sum_receive_inst = 0 ; ?>   
                    <?php $sum_receive_ae_ae = 0 ; ?> 
                    <?php $sum_receive_ip_compensate_pay = 0 ; ?>  
                    <?php $sum_receive_total = 0 ; ?>  
                    @foreach($eclaim as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>
                        <td align="right">{{$row->ward}}</td>
                        <td align="right">{{ DateThai($row->regdate) }}</td>
                        <td align="right">{{ DateThai($row->dchdate) }}</td>
                        <td align="center">{{ $row->hn }}</td>
                        <td align="center">{{ $row->an }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->age_y }}</td>
                        <td align="center">{{ $row->pttype }}</td>
                        <td align="left">{{ $row->diag_text_list }}</td>
                        <td align="right">{{ $row->icd10 }}</td>
                        <td align="right">{{ $row->icd9 }}</td>
                        <td align="right">{{ $row->auth_code }}</td>
                        <td align="right">{{ $row->refer }}</td>
                        <td align="right">{{ $row->adjrw }}</td>                        
                        <td align="right">{{ number_format($row->income,2) }}</td>
                        <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                        <td align="right">{{ number_format($row->income-$row->rcpt_money,2) }}</td>
                        <td align="center">{{ $row->fdh }}</td>
                        <td align="right">{{ number_format($row->rep_nhso,2) }}</td>
                        <td align="center">{{ $row->rep_error }}</td>
                        <td align="right">{{ number_format($row->receive_inst,2) }}</td>
                        <td align="right">{{ number_format($row->receive_ae_ae,2) }}</td>
                        <td align="right">{{ number_format($row->fund_ip_adjrw,2) }}</td>
                        <td align="right">{{ number_format($row->fund_ip_payrate,2) }}</td>
                        <td align="right">{{ number_format($row->receive_ip_compensate_pay,2) }}</td>
                        <td align="right">{{ number_format($row->receive_total,2) }}</td>
                        <td align="right">{{ number_format($row->receive_total-($row->income-$row->rcpt_money),2) }}</td>
                        <td align="center">{{ $row->repno }}</td>
                    </tr>
                    <?php $count++; ?>
                    <?php $sum_debtor += ($row->income-$row->rcpt_money) ; ?>
                    <?php $sum_receive_inst += $row->receive_inst ; ?>
                    <?php $sum_receive_ae_ae += $row->receive_ae_ae ; ?>
                    <?php $sum_receive_ip_compensate_pay += $row->receive_ip_compensate_pay ; ?>
                    <?php $sum_receive_total += $row->receive_total ; ?>
                    @endforeach
                </table>
                <div class="text-center text-primary">
                    <h4>
                        รวมลูกหนี้ทั้งหมด <strong>{{number_format($sum_debtor,2)}} </strong>บาท |
                        ชดเชยทั้งหมด <strong>{{number_format($sum_receive_total,2)}} </strong>บาท |
                        ส่วนต่าง <strong>{{number_format($sum_receive_total-$sum_debtor,2)}} </strong>บาท
                    </h4>
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
        $('#debtor_fdh').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#debtor').DataTable();
    });
</script>
