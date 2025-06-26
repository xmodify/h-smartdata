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
    <div class="row"  >
        <div class="col-sm-12"> 
            <div class="alert alert-success text-primary" role="alert"><strong>ข้อมูลผู้ป่วยใช้บริการ CT Scan วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>          
        </div>
    </div>     
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
</div> <!-- row --> 

<div class="container-fluid">  
    <div class="card-body">   
        <div class="row">                          
            <div class="col-md-8" align="left"> 
                <h5 class="card-title text-primary"></h5>
            </div>                 
            <div class="col-md-4" align="right">
                <a class="btn btn-success my-2 " href="{{ url('service_xray/ct_excel') }}" target="_blank" type="submit">
                Excel
                </a>                    
            </div>      
        </div>        
        <div style="overflow-x:auto;">           
            <table id="list" class="table table-bordered table-striped">
                <thead>
                <tr class="table-primary">
                    <th class="text-center">ลำดับ</th>
                    <th class="text-center">Dep</th>
                    <th class="text-center">วันที่</th>
                    <th class="text-center">TimeUpdate</th>
                    <th class="text-center">ชื่อ-สกุล</th>             
                    <th class="text-center">HN</th>
                    <th class="text-center">AN</th>                  
                    <th class="text-center">สิทธิการรักษา</th>
                    <th class="text-center">รายการ</th>
                    <th class="text-center">วางบิล</th>   
                    <th class="text-center">HOSxP</th> 
                    <th class="text-center">บริษัท CT</th>                
                </tr>     
                </thead> 
                <?php $count = 1 ; ?> 
                <?php $sum_price_bill = 0 ; ?>
                <?php $sum_price_claim = 0 ; ?>
                <?php $sum_price_ct = 0 ; ?>
                @foreach($ct_list as $row)          
                <tr>
                    <td align="right">{{ $count }}</td> 
                    <td align="center">{{ $row->depart }}</td>
                    <td align="right">{{ DateThai($row->rxdate) }}</td>
                    <td align="center">{{ $row->updatetime }}</td>
                    <td align="left">{{ $row->ptname }}</td>
                    <td align="center">{{ $row->hn }}</td>
                    <td align="center">{{ $row->an }}</td>
                    <td align="right">{{ $row->pttype }} [{{ $row->hospmain }}]</td>
                    <td align="right">{{ $row->item_name }}</td>
                    <td align="right">{{ number_format($row->price_bill,2) }}</td> 
                    <td align="right">{{ number_format($row->price_claim,2) }}</td> 
                    <td align="right">{{ number_format($row->price_ct,2) }}</td> 
                </tr>                
                <?php $count++; ?>
                <?php $sum_price_bill += $row->price_bill ; ?>
                <?php $sum_price_claim += $row->price_claim ; ?>
                <?php $sum_price_ct += $row->price_ct ; ?>
                @endforeach    
            </table> 
        </div> 

        <div class="text-center text-primary my-3">
            <h4>
                วางบิล: <strong>{{number_format($sum_price_bill)}} |</strong> HOSxP: <strong>{{number_format($sum_price_claim)}} |</strong>
                บริษัท CT: <strong>{{number_format($sum_price_ct)}}</strong>
            </h4>
        </div>
    </div>           
</div>
<hr>
@endsection

<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#list').DataTable();
    });
</script>
