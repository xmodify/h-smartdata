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
        <div class="card-header bg-primary bg-opacity-75 text-white">เบิกจ่ายตรงกรมบัญชีกลาง OFC ผู้ป่วยใน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
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
                            <th class="text-center">STM เรียกเก็บ</th>
                            <th class="text-center">STM ค่าห้อง</th> 
                            <th class="text-center">STM ค่าอวัยวะ</th>
                            <th class="text-center">STM ค่ายา</th>
                            <th class="text-center">STM ค่ารักษา</th> 
                            <th class="text-center">STM ค่ารถ</th> 
                            <th class="text-center">STM ค่าบริการอื่น</th> 
                            <th class="text-center">STM รับทั้งหมด</th> 
                            <th class="text-center">ผลต่าง</th> 
                            <th class="text-center">REP</th>  
                        </tr>     
                        </thead> 
                        <?php $count = 1 ; ?> 
                        <?php $sum_income = 0 ; ?>    
                        <?php $sum_rcpt_money = 0 ; ?>  
                        <?php $sum_charge = 0 ; ?>  
                        <?php $sum_receive_room = 0 ; ?>  
                        <?php $sum_receive_instument = 0 ; ?>  
                        <?php $sum_receive_drug = 0 ; ?>  
                        <?php $sum_receive_treatment = 0 ; ?>  
                        <?php $sum_receive_car = 0 ; ?>  
                        <?php $sum_receive_other = 0 ; ?> 
                        <?php $sum_receive_total = 0 ; ?>  
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
                            <td align="right">{{ number_format($row->charge,2) }}</td>
                            <td align="right">{{ number_format($row->receive_room,2) }}</td>
                            <td align="right">{{ number_format($row->receive_instument,2) }}</td>
                            <td align="right">{{ number_format($row->receive_drug,2) }}</td>
                            <td align="right">{{ number_format($row->receive_treatment,2) }}</td>
                            <td align="right">{{ number_format($row->receive_car,2) }}</td>
                            <td align="right">{{ number_format($row->receive_other,2) }}</td>
                            <td align="right">{{ number_format($row->receive_total,2) }}</td>
                            <td align="right">{{ number_format($row->receive_total-($row->income-$row->rcpt_money),2) }}</td>
                            <td align="center">{{ $row->repno }}</td>                
                        </tr>                
                        <?php $count++; ?>
                        <?php $sum_income += $row->income ; ?>
                        <?php $sum_rcpt_money += $row->rcpt_money ; ?>
                        <?php $sum_charge += $row->charge ; ?>
                        <?php $sum_receive_room += $row->receive_room ; ?>
                        <?php $sum_receive_instument += $row->receive_instument ; ?>
                        <?php $sum_receive_drug += $row->receive_drug ; ?>
                        <?php $sum_receive_treatment += $row->receive_treatment ; ?>
                        <?php $sum_receive_car += $row->receive_car ; ?>
                        <?php $sum_receive_other += $row->receive_other ; ?>
                        <?php $sum_receive_total += $row->receive_total ; ?>
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
                        <th class="text-center">รวมชดเชยค่าห้อง</th>
                        <th class="text-center">รวมชดเชยค่าอวัยวะ</th>
                        <th class="text-center">รวมชดเชยค่ายา</th>
                        <th class="text-center">รวมชดเชยค่ารักษา</th>
                        <th class="text-center">รวมชดเชยค่ารถ</th>
                        <th class="text-center">รวมชดเชยค่าบริการอื่น</th>
                        <th class="text-center">รวมชดเชยทั้งหมด</th>
                        <th class="text-center">ส่วนต่าง</th>
                    </tr>
                    </thead>
                    <tr>
                        <td align="right"><strong>{{number_format($sum_income,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_rcpt_money,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_income-$sum_rcpt_money,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_charge,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_receive_room,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_receive_instument,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_receive_drug,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_receive_treatment,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_receive_car,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_receive_other,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_receive_total,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_receive_total-($sum_income-$sum_rcpt_money),2)}}</strong></td>                   
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