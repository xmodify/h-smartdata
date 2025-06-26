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
    <div class="card border-primary">
        <div class="card-header bg-primary bg-opacity-75 text-white">เบิกจ่ายตรง อปท.LGO ผู้ป่วยใน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div class="card-body"> 
            <div style="overflow-x:auto;">   
                <p class="text-primary">ผู้ป่วยนอกทั่วไป</p>            
                <table id="claim" class="table table-bordered table-striped my-3">
                    <thead>
                        <tr class="table-primary">
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
                            <th class="text-center">Refer</th>
                            <th class="text-center">AdjRW</th>
                            <th class="text-center">ค่ารักษา</th>  
                            <th class="text-center">ชำระเอง</th>
                            <th class="text-center">ลูกหนี้</th> 
                            <th class="text-center">STM AdjRW</th>
                            <th class="text-center">STM อัตราจ่าย</th>
                            <th class="text-center">STM เรียกเก็บ</th>
                            <th class="text-center">STM IPLG</th> 
                            <th class="text-center">STM INSTLG</th>
                            <th class="text-center">STM OTLG</th>
                            <th class="text-center">STM PP</th> 
                            <th class="text-center">STM DRUG</th> 
                            <th class="text-center">STM ชดเชยทั้งหมด</th> 
                            <th class="text-center">ผลต่าง</th> 
                            <th class="text-center">REP</th>  
                        </tr>     
                        </thead> 
                        <?php $count = 1 ; ?> 
                        <?php $sum_income = 0 ; ?>    
                        <?php $sum_rcpt_money = 0 ; ?>  
                        <?php $sum_charge_treatment = 0 ; ?>  
                        <?php $sum_case_iplg  = 0 ; ?>  
                        <?php $sum_case_inslg = 0 ; ?>  
                        <?php $sum_case_otlg = 0 ; ?>  
                        <?php $sum_case_pp = 0 ; ?>  
                        <?php $sum_case_drug = 0 ; ?>  
                        <?php $sum_compensate_treatment = 0 ; ?> 
                        @foreach($claim as $row)          
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
                            <td align="right">{{ $row->refer }}</td>
                            <td align="right">{{ $row->adjrw }}</td>                        
                            <td align="right">{{ number_format($row->income,2) }}</td>
                            <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                            <td align="right">{{ number_format($row->income-$row->rcpt_money,2) }}</td>
                            <td align="right">{{ number_format($row->stm_adjrw,2) }}</td>
                            <td align="right">{{ number_format($row->payrate,2) }}</td>
                            <td align="right">{{ number_format($row->charge_treatment,2) }}</td>
                            <td align="right">{{ number_format($row->case_iplg,2) }}</td>
                            <td align="right">{{ number_format($row->case_inslg,2) }}</td>
                            <td align="right">{{ number_format($row->case_otlg,2) }}</td>
                            <td align="right">{{ number_format($row->case_pp,2) }}</td>
                            <td align="right">{{ number_format($row->case_drug,2) }}</td>
                            <td align="right">{{ number_format($row->compensate_treatment,2) }}</td>
                            <td align="right">{{ number_format($row->compensate_treatment-($row->income-$row->rcpt_money),2) }}</td>
                            <td align="center">{{ $row->repno }}</td>                
                        </tr>                
                        <?php $count++; ?>
                        <?php $sum_income += $row->income ; ?>
                        <?php $sum_rcpt_money += $row->rcpt_money ; ?>
                        <?php $sum_charge_treatment += $row->charge_treatment ; ?>
                        <?php $sum_case_iplg += $row->case_iplg ; ?>
                        <?php $sum_case_inslg += $row->case_inslg ; ?>
                        <?php $sum_case_otlg += $row->case_otlg ; ?>
                        <?php $sum_case_pp += $row->case_pp ; ?>
                        <?php $sum_case_drug += $row->case_drug ; ?>
                        <?php $sum_compensate_treatment += $row->compensate_treatment ; ?>
                        @endforeach   
                </table>
            </div>  
            <div style="overflow-x:auto;"> 
                <table class="table table-bordered ">
                    <thead>
                    <tr class="table-success" >
                        <th class="text-center">รวมค่ารักษาทั้งหมด</th>
                        <th class="text-center">รวมชำระเงินเอง</th>
                        <th class="text-center">รวมลูกหนี้</th>
                        <th class="text-center">รวมเรียกเก็บ</th>
                        <th class="text-center">รวมชดเชย IPLG</th>
                        <th class="text-center">รวมชดเชย INSTLG</th>
                        <th class="text-center">รวมชดเชย OTLG</th>
                        <th class="text-center">รวมชดเชย PP</th>
                        <th class="text-center">รวมชดเชย DRUG</th>
                        <th class="text-center">รวมชดเชย ทั้งหมด</th>
                        <th class="text-center">ส่วนต่าง</th>
                    </tr>
                    </thead>
                    <tr>
                        <td align="right"><strong>{{number_format($sum_income,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_rcpt_money,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_income-$sum_rcpt_money,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_charge_treatment,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_case_iplg,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_case_inslg,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_case_otlg,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_case_pp,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_case_drug,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_compensate_treatment,2)}}</strong></td>   
                        <td align="right"><strong>{{number_format($sum_compensate_treatment-($sum_income-$sum_rcpt_money),2)}}</strong></td>                   
                </table>     
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
        $('#claim').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#claim_kidney').DataTable();
    });
</script>