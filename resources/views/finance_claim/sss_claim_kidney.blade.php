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
        <div class="card-header bg-primary bg-opacity-75 text-white">ประกันสังคม SSS ฟอกไต วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div class="card-body"> 
            <div style="overflow-x:auto;">         
                <table id="claim_kidney" class="table table-bordered table-striped my-3">
                    <thead>
                        <tr class="table-danger">
                            <th class="text-center">ลำดับ</th>
                            <th class="text-center">รับบริการ</th> 
                            <th class="text-center">Queue</th>                      
                            <th class="text-center">HN</th>
                            <th class="text-center">ชื่อ-สกุล</th>                    
                            <th class="text-center">อายุ</th>
                            <th class="text-center">สิทธิ</th>                  
                            <th class="text-center">ICD10</th>                          
                            <th class="text-center">Authen</th>               
                            <th class="text-center">ค่ารักษาทั้งหมด</th>
                            <th class="text-center">ชำระเอง</th>
                            <th class="text-center">ค่าฟอก</th>
                            <th class="text-center">ค่ายา</th>
                            <th class="text-center">ชดเชยค่าฟอก</th>
                            <th class="text-center">ชดเชยค่ายา</th>
                            <th class="text-center">ชดเชยค่าฉีดยา</th>
                            <th class="text-center">ส่วนต่าง</th>
                            <th class="text-center">Rep No.</th>
                            <th class="text-center">STM Filename</th>
                        </tr>     
                        </thead> 
                        <?php $count = 1 ; ?> 
                        <?php $sum_income = 0 ; ?>    
                        <?php $sum_rcpt_money = 0 ; ?>  
                        <?php $sum_price_kidney = 0 ; ?>  
                        <?php $sum_price_epo = 0 ; ?>  
                        <?php $sum_amount = 0 ; ?>  
                        <?php $sum_epopay = 0 ; ?>  
                        <?php $sum_epoadm = 0 ; ?>  
                        @foreach($claim_kidney as $row)          
                        <tr>
                            <td align="center">{{ $count }}</td> 
                            <td align="right">{{ DatetimeThai($row->vstdate) }} </td>
                            <td align="center">{{ $row->oqueue }}</td>
                            <td align="center">{{ $row->hn }}</td>
                            <td align="left">{{ $row->ptname }}</td>
                            <td align="center">{{ $row->age_y }}</td>
                            <td align="left">{{ $row->pttype }}</td>                               
                            <td align="center">{{ $row->pdx }}</td>     
                            <td align="center">{{ $row->auth_code }}</td>                                                
                            <td align="right">{{ number_format($row->income,2) }}</td>
                            <td align="right">{{ number_format($row->rcpt_money,2) }}</td> 
                            <td align="right">{{ number_format($row->price_kidney,2) }}</td> 
                            <td align="right">{{ number_format($row->price_epo,2) }}</td>                        
                            <td align="right">{{ number_format($row->amount,2) }}</td> 
                            <td align="right">{{ number_format($row->epopay,2) }}</td> 
                            <td align="right">{{ number_format($row->epoadm,2) }}</td> 
                            <td align="right">{{ number_format(($row->amount+$row->epopay+$row->epoadm)-($row->price_kidney+$row->price_epo),2) }}</td>
                            <td align="center">{{ $row->rid }}</td>    
                            <td align="center">{{ $row->stmdoc }}</td>                    
                        </tr>                
                        <?php $count++; ?>
                        <?php $sum_income += $row->income ; ?>
                        <?php $sum_rcpt_money += $row->rcpt_money ; ?>
                        <?php $sum_price_kidney += $row->price_kidney ; ?>
                        <?php $sum_price_epo += $row->price_epo ; ?>
                        <?php $sum_amount += $row->amount ; ?>
                        <?php $sum_epopay += $row->epopay ; ?>
                        <?php $sum_epoadm += $row->epoadm ; ?>
                        @endforeach   
                </table>
                <br>
                <table class="table table-bordered ">
                    <thead>
                    <tr class="table-warning" >
                        <th class="text-center">ค่ารักษาทั้งหมด</th>
                        <th class="text-center">ชำระเงินเอง</th>
                        <th class="text-center">ค่าฟอก</th>
                        <th class="text-center">ค่ายา</th>
                        <th class="text-center">ชดเชยค่าฟอก</th>
                        <th class="text-center">ชดเชยค่ายา</th>
                        <th class="text-center">ชดเชยค่าฉีดยา</th>
                        <th class="text-center">ส่วนต่าง</th>
                    </tr>
                    </thead>
                    <tr>
                        <td align="right"><strong>{{number_format($sum_income,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_rcpt_money,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_price_kidney,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_price_epo,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_amount,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_epopay,2)}}</strong></td>
                        <td align="right"><strong>{{number_format($sum_epoadm,2)}}</strong></td>
                        <td align="right"><strong>{{number_format(($sum_amount+$sum_epopay+$sum_epoadm)-($sum_price_kidney+$sum_price_epo),2)}}</strong></td>                   
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
        $('#claim_kidney').DataTable();
    });
</script>