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
<script>
    function toggle(source) {
        checkboxes = document.getElementsByName('checkboxes[]');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>
<script>
    function toggle_d(source) {
        checkbox = document.getElementsByName('checkbox[]');
        for (var i = 0; i < checkbox.length; i++) {
            checkbox[i].checked = source.checked;
        }
    }
</script>
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
    <div style="overflow-x:auto;">
        <form action="{{ url('finance_debtor/1102050101_217_delete') }}" method="POST" enctype="multipart/form-data">
            @csrf   
            @method('DELETE')
            <table id="debtor" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-success">
                    <th class="text-center" width="5%">
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('ต้องการลบลูกหนี้')">ลบลูกหนี้</button>
                    </th>
                    <th class="text-center" colspan = "12">1102050101.217-ลูกหนี้ค่ารักษา UC-IP บริการเฉพาะ (CR) วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</th> 
                    <th class="text-center text-primary" colspan = "8">การชดเชย</th>                                                 
                </tr>
                <tr class="table-success">
                    <th class="text-center"><input type="checkbox" onClick="toggle_d(this)"> All</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">AN</th>
                    <th class="text-center">ชื่อ-สกุล</th>  
                    <th class="text-center">สิทธิ</th>
                    <th class="text-center">Admit</th>
                    <th class="text-center">Discharge</th>
                    <th class="text-center">ICD10</th>
                    <th class="text-center">AdjRW</th>
                    <th class="text-center" width="5%">ค่ารักษาทั้งหมด</th>  
                    <th class="text-center" width="5%">ชำระเอง</th>
                    <th class="text-center" width="5%">CR</th>
                    <th class="text-center" width="5%">ฟอกไต</th>                  
                    <th class="text-center text-primary" width="5%">ลูกหนี้</th>  
                    <th class="text-center text-primary" width="5%">CR</th>                  
                    <th class="text-center text-primary" width="5%">ฟอกไต</th>                   
                    <th class="text-center text-primary" width="5%">ชดเชย</th> 
                    <th class="text-center text-primary" width="5%">ผลต่าง</th> 
                    <th class="text-center text-primary" width="5%">REP</th>
                    <th class="text-center text-primary" width="3%">Lock</th>     
                                
                </tr>
                </thead>
                <?php $count = 1 ; ?>
                <?php $sum_income = 0 ; ?>
                <?php $sum_rcpt_money = 0 ; ?>
                <?php $sum_kidney = 0 ; ?>
                <?php $sum_cr = 0 ; ?>
                <?php $sum_debtor = 0 ; ?>
                <?php $sum_receive = 0 ; ?>
                <?php $sum_receive_cr = 0 ; ?>
                <?php $sum_receive_kidney = 0 ; ?>
                @foreach($debtor as $row)
                <tr>
                    <td class="text-center"><input type="checkbox" name="checkbox[]" value="{{$row->an}}"></td> 
                    <td align="center">{{ $row->hn }}</td>
                    <td align="center">{{ $row->an }}</td>
                    <td align="left">{{ $row->ptname }}</td>
                    <td align="center">{{ $row->pttype }} [{{ $row->hospmain }}]</td>
                    <td align="right">{{ DateThai($row->regdate) }}</td>
                    <td align="right">{{ DateThai($row->dchdate) }}</td>
                    <td align="right">{{ $row->pdx }}</td>  
                    <td align="right">{{ $row->adjrw }}</td>                        
                    <td align="right">{{ number_format($row->income,2) }}</td>
                    <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                    <td align="right">{{ number_format($row->cr,2) }}</td>
                    <td align="right">{{ number_format($row->kidney,2) }}</td>                   
                    <td align="right" class="text-primary">{{ number_format($row->debtor,2) }}</td>  
                    <td align="right">{{ number_format($row->receive_cr,2) }}</td>    
                    <td align="right">{{ number_format($row->receive_kidney,2) }}</td>                          
                    <td align="right" @if($row->receive > 0) style="color:green" 
                        @elseif($row->receive < 0) style="color:red" @endif>
                        {{ number_format($row->receive,2) }}
                    </td>
                    <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green" 
                        @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                        {{ number_format($row->receive-$row->debtor,2) }}
                    </td>                   
                    <td align="center">{{ $row->repno }} {{ $row->repno_kidney }}</td>   
                    <td align="center" style="color:blue">{{ $row->debtor_lock }}</td>                          
                <?php $count++; ?>
                <?php $sum_income += $row->income ; ?>
                <?php $sum_rcpt_money += $row->rcpt_money ; ?>
                <?php $sum_kidney += $row->kidney ; ?>
                <?php $sum_cr += $row->cr ; ?>  
                <?php $sum_debtor += $row->debtor ; ?> 
                <?php $sum_receive_cr += $row->receive_cr ; ?>  
                <?php $sum_receive_kidney += $row->receive_kidney ; ?>  
                <?php $sum_receive += $row->receive ; ?> 
                @endforeach 
            </tr>   
            </table>
        </form>
        <table class="table table-bordered ">
            <thead>
            <tr class="table-primary" >
                <th class="text-center">รหัสผังบัญชี</th>
                <th class="text-center">ชื่อผังบัญชี</th>
                <th class="text-center">ค่ารักษาพยาบาล</th>
                <th class="text-center">ชำระเอง</th>
                <th class="text-center">ฟอกไต</th>
                <th class="text-center">CR</th>
                <th class="text-center">ลูกหนี้</th> 
                <th class="text-center">ชดเชย CR</th>
                <th class="text-center">ชดเชย ฟอกไต</th>      
                <th class="text-center">ชดเชยทั้งหมด</th>   
                <th class="text-center">ผลต่าง</th> 
                <th class="text-center">รายงาน</th>                
            </tr>
            </thead>
            <tr>
                <td class="text-primary" align="right">1102050101.217</td>
                <td class="text-primary" align="left">ลูกหนี้ค่ารักษา UC-IP บริการเฉพาะ (CR)</td>
                <td class="text-primary" align="right">{{ number_format($sum_income,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_rcpt_money,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_kidney,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_cr,2)}}</td>
                <td class="text-primary" align="right"><strong>{{ number_format($sum_debtor,2)}}</strong></td>
                <td align="right" @if($sum_receive_cr > 0) style="color:green" 
                    @elseif($sum_receive_cr < 0) style="color:red" @endif>
                    {{ number_format($sum_receive_cr,2)}}
                </td>
                <td align="right" @if($sum_receive_kidney > 0) style="color:green" 
                    @elseif($sum_receive_kidney < 0) style="color:red" @endif>
                    {{ number_format($sum_receive_kidney,2)}} 
                </td>
                <td align="right" @if($sum_receive > 0) style="color:green" 
                    @elseif($sum_receive < 0) style="color:red" @endif>
                    <strong>{{ number_format($sum_receive,2)}}</strong>
                </td>
                <td align="right" @if(($sum_receive-$sum_debtor) > 0) style="color:green" 
                    @elseif(($sum_receive-$sum_debtor) < 0) style="color:red" @endif>
                    <strong>{{ number_format($sum_receive-$sum_debtor,2)}}</strong>
                </td>
                <td align="center">
                    <a class="btn btn-outline-success btn-sm" href="{{ url('finance_debtor/1102050101_217_indiv_excel')}}" target="_blank">ส่งออกรายตัว</a>                
                    <a class="btn btn-outline-primary btn-sm" href="{{ url('finance_debtor/1102050101_217_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                </td>                    
            </tr>
        </table>
    </div> 
<hr>
    <div style="overflow-x:auto;">
        <form action="{{ url('finance_debtor/1102050101_217_confirm') }}" method="POST" enctype="multipart/form-data">
            @csrf                
            <table id="debtor_search" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center" width="5%">
                        <button type="submit" class="btn btn-outline-success btn-sm"  onclick="return confirm('ต้องการยืนยันลูกหนี้')">ยืนยันลูกหนี้</button></th>
                    <th class="text-center" colspan = "16">ผู้มารับบริการ วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }} รอยืนยันลูกหนี้</th>                         
                </tr>
                <tr class="table-secondary">
                    <th class="text-center"><input type="checkbox" onClick="toggle(this)"> All</th>  
                    <th class="text-center">ตึกผู้ป่วย</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">AN</th>
                    <th class="text-center">ชื่อ-สกุล</th>              
                    <th class="text-center">อายุ</th>
                    <th class="text-center">สิทธิ</th>
                    <th class="text-center">Admit</th>
                    <th class="text-center">Discharge</th>
                    <th class="text-center">ICD10</th>
                    <th class="text-center">AdjRW</th>
                    <th class="text-center">ค่ารักษาทั้งหมด</th>  
                    <th class="text-center">ชำระเอง</th>
                    <th class="text-center">ฟอกไต</th>
                    <th class="text-center">CR</th>
                    <th class="text-center">ลูกหนี้</th> 
                    <th class="text-center">รายการบริการเฉพาะ</th>                  
                </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($debtor_search as $row)
                <tr>
                    <td class="text-center"><input type="checkbox" name="checkboxes[]" value="{{$row->an}}"></td> 
                    <td align="right">{{$row->ward}}</td>
                    <td align="center">{{ $row->hn }}</td>
                    <td align="center">{{ $row->an }}</td>
                    <td align="left">{{ $row->ptname }}</td>
                    <td align="center">{{ $row->age_y }}</td>
                    <td align="center">{{ $row->pttype }} [{{ $row->hospmain }}]</td>
                    <td align="right">{{ DateThai($row->regdate) }}</td>
                    <td align="right">{{ DateThai($row->dchdate) }}</td>
                    <td align="right">{{ $row->pdx }}</td>      
                    <td align="right">{{ $row->adjrw }}</td>                        
                    <td align="right">{{ number_format($row->income,2) }}</td>
                    <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                    <td align="right">{{ number_format($row->kidney,2) }}</td>
                    <td align="right">{{ number_format($row->cr,2) }}</td>
                    <td align="right">{{ number_format($row->debtor,2) }}</td>
                    <td align="left">{{ $row->kidney_list }}{{ $row->cr_list }}</td>                      
                <?php $count++; ?>
                @endforeach 
            </tr>   
            </table>
        </form>
    </div> 
 </div>
<br> 
@endsection
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#debtor_search').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#debtor').DataTable();
    });
</script>
