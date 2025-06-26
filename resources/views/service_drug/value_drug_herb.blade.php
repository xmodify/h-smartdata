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
    <div class="row">                          
        <div class="col-md-8" align="left"> 
            <h5 class="card-title text-primary"></h5>
        </div>                 
        <div class="col-md-4" align="right">
            <a class="btn btn-success my-2 " href="{{ url('service_drug/value_drug_herb_excel') }}" target="_blank" type="submit">
            Excel
            </a>                    
        </div>      
    </div>
</div>
<!-- row -->
<div class="container-fluid">  
    <div class="card">         
        <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลการใช้ยาสมุนไพร ผู้ป่วยนอก วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>      
        <div class="card-body">
            <h4 class="text-primary">ผู้ป่วยนอก</h4>
            <div style="overflow-x:auto;"> 
                <table id="drug" class="table table-bordered table-striped my-3">
                    <thead>
                    <tr class="table-primary">
                        <th class="text-center">ลำดับ</th>
                        <th class="text-center">icode</th>
                        <th class="text-center">ชื่อยา</th>
                        <th class="text-center">จำนวน</th>
                        <th class="text-center">มูลค่ายารวม</th>
                        <th class="text-center">มูลค่ายา UCS</th>
                        <th class="text-center">มูลค่ายา OFC</th>
                        <th class="text-center">มูลค่ายา SSS</th>
                        <th class="text-center">มูลค่ายา LGO</th>
                        <th class="text-center">มูลค่ายา อื่น ๆ</th>                   
                    </tr>     
                    </thead> 
                    <?php $count = 1 ; ?> 
                    <?php $sum_qty = 0 ; ?>
                    <?php $sum_sum_price = 0 ; ?>
                    <?php $sum_ucs_price = 0 ; ?>
                    <?php $sum_ofc_price = 0 ; ?>
                    <?php $sum_sss_price = 0 ; ?>
                    <?php $sum_lgo_price = 0 ; ?>
                    <?php $sum_other_price = 0 ; ?>
                    @foreach($drug as $row)          
                    <tr>
                        <td align="right">{{ $count }}</td> 
                        <td align="left">{{ $row->icode }}</td>
                        <td align="left">{{ $row->dname }}</td>
                        <td align="right">{{ number_format($row->qty) }}</td>
                        <td align="right">{{ number_format($row->sum_price,2) }}</td>
                        <td align="right">{{ number_format($row->ucs_price,2) }}</td>
                        <td align="right">{{ number_format($row->ofc_price,2) }}</td>
                        <td align="right">{{ number_format($row->sss_price,2) }}</td>
                        <td align="right">{{ number_format($row->lgo_price,2) }}</td>    
                        <td align="right">{{ number_format($row->other_price,2) }}</td>      
                    </tr>                
                    <?php $count++; ?>
                    <?php $sum_qty += $row->qty ; ?>
                    <?php $sum_sum_price += $row->sum_price ; ?>
                    <?php $sum_ucs_price += $row->ucs_price ; ?>
                    <?php $sum_ofc_price += $row->ofc_price ; ?>
                    <?php $sum_sss_price += $row->sss_price ; ?>
                    <?php $sum_lgo_price += $row->lgo_price ; ?>
                    <?php $sum_other_price += $row->other_price ; ?>
                    @endforeach  
                    <tr>   
                        <td colspan= "3" align="right"><strong>รวม</strong></td>                       
                        <td align="right"><strong>{{ number_format($sum_qty)}}</strong></td>
                        <td align="right"><strong>{{ number_format($sum_sum_price,2)}}</strong></td>
                        <td align="right"><strong>{{ number_format($sum_ucs_price,2)}}</strong></td> 
                        <td align="right"><strong>{{ number_format($sum_ofc_price,2)}}</strong></td> 
                        <td align="right"><strong>{{ number_format($sum_sss_price,2)}}</strong></td> 
                        <td align="right"><strong>{{ number_format($sum_lgo_price,2)}}</strong></td> 
                        <td align="right"><strong>{{ number_format($sum_other_price,2)}}</strong></td>                                
                    </tr>
                </table>
            </div>
            <hr>
            <h4 class="text-primary">ผู้ป่วยใน</h4>
            <div style="overflow-x:auto;"> 
                <table id="drug_ipd" class="table table-bordered table-striped my-3">
                    <thead>
                    <tr class="table-primary">
                        <th class="text-center">ลำดับ</th>
                        <th class="text-center">icode</th>
                        <th class="text-center">ชื่อยา</th>
                        <th class="text-center">จำนวน</th>
                        <th class="text-center">มูลค่ายารวม</th>
                        <th class="text-center">มูลค่ายา UCS</th>
                        <th class="text-center">มูลค่ายา OFC</th>
                        <th class="text-center">มูลค่ายา SSS</th>
                        <th class="text-center">มูลค่ายา LGO</th>
                        <th class="text-center">มูลค่ายา อื่น ๆ</th>  
                    </tr>     
                    </thead> 
                    <?php $count = 1 ; ?> 
                    <?php $sum_qty = 0 ; ?>
                    <?php $sum_sum_price = 0 ; ?>
                    <?php $sum_ucs_price = 0 ; ?>
                    <?php $sum_ofc_price = 0 ; ?>
                    <?php $sum_sss_price = 0 ; ?>
                    <?php $sum_lgo_price = 0 ; ?>
                    <?php $sum_other_price = 0 ; ?>
                    @foreach($drug_ipd as $row)          
                    <tr>
                        <td align="right">{{ $count }}</td> 
                        <td align="left">{{ $row->icode }}</td>
                        <td align="left">{{ $row->dname }}</td>
                        <td align="right">{{ number_format($row->qty) }}</td>
                        <td align="right">{{ number_format($row->sum_price,2) }}</td>
                        <td align="right">{{ number_format($row->ucs_price,2) }}</td>
                        <td align="right">{{ number_format($row->ofc_price,2) }}</td>
                        <td align="right">{{ number_format($row->sss_price,2) }}</td>
                        <td align="right">{{ number_format($row->lgo_price,2) }}</td>    
                        <td align="right">{{ number_format($row->other_price,2) }}</td>                                    
                    </tr>                
                    <?php $count++; ?>
                    <?php $sum_qty += $row->qty ; ?>
                    <?php $sum_sum_price += $row->sum_price ; ?>
                    <?php $sum_ucs_price += $row->ucs_price ; ?>
                    <?php $sum_ofc_price += $row->ofc_price ; ?>
                    <?php $sum_sss_price += $row->sss_price ; ?>
                    <?php $sum_lgo_price += $row->lgo_price ; ?>
                    <?php $sum_other_price += $row->other_price ; ?>
                    @endforeach  
                    <tr>   
                        <td colspan= "3" align="right"><strong>รวม</strong></td>                       
                        <td align="right"><strong>{{ number_format($sum_qty)}}</strong></td>
                        <td align="right"><strong>{{ number_format($sum_sum_price,2)}}</strong></td>
                        <td align="right"><strong>{{ number_format($sum_ucs_price,2)}}</strong></td> 
                        <td align="right"><strong>{{ number_format($sum_ofc_price,2)}}</strong></td> 
                        <td align="right"><strong>{{ number_format($sum_sss_price,2)}}</strong></td> 
                        <td align="right"><strong>{{ number_format($sum_lgo_price,2)}}</strong></td> 
                        <td align="right"><strong>{{ number_format($sum_other_price,2)}}</strong></td>                                
                    </tr>
                </table>
            </div>
        </div>            
    </div>
</div>
<br>
@endsection
{{-- <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#drug').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#drug_ipd').DataTable();
    });
</script> --}}