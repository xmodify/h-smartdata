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
            <label class="col-md-2 col-form-label text-md-end my-1">{{ __('วันที่') }}</label>
        <div class="col-md-2">
            <input type="date" name="start_date" class="form-control my-1" placeholder="Date" value="{{ $start_date }}" >
        </div>
            <label class="col-md-1 col-form-label text-md-end my-1">{{ __('ถึง') }}</label>
        <div class="col-md-2">
            <input type="date" name="end_date" class="form-control my-1" placeholder="Date" value="{{ $end_date }}" >
        </div>
            <label class="col-md-1 col-form-label text-md-end my-1">{{ __('ค้นหา ชื่อ-สกุล,HN,AN') }}</label>
        <div class="col-md-2" >
            <input id="search" type="text" class="form-control my-1" name="search" value="{{ $search }}" >
        </div>
        <div class="col-md-1" >
            <button type="submit" class="btn btn-primary my-1 ">{{ __('ค้นหา') }}</button>
            <a class="btn btn-primary my-1 text-white" href="{{ url('finance_debtor/forget_search') }}">Reset</a>
        </div>
    </div>
</form>
<div class="container-fluid">
    <div style="overflow-x:auto;">
        <form action="{{ url('finance_debtor/1102050102_603_delete') }}" method="POST" enctype="multipart/form-data">
            @csrf   
            @method('DELETE')
            <table id="debtor" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-success">
                    <th class="text-center" width="5%">
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('ต้องการลบลูกหนี้')">ลบลูกหนี้</button>
                    </th>
                    <th class="text-center" colspan = "11">1102050102.603-ลูกหนี้ค่ารักษา พรบ.รถ IP วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</th> 
                    <th class="text-center text-primary" colspan = "6">การชดเชย</th>                                                 
                </tr>
                <tr class="table-success">
                    <th class="text-center"><input type="checkbox" onClick="toggle_d(this)"> All</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">AN</th>
                    <th class="text-center">ชื่อ-สกุล</th>  
                    <th class="text-center" width = "15%">สิทธิ</th>
                    <th class="text-center">Admit</th>
                    <th class="text-center">Discharge</th>
                    <th class="text-center">ICD10</th>
                    <th class="text-center">AdjRW</th>
                    <th class="text-center">ค่ารักษาทั้งหมด</th>  
                    <th class="text-center">ชำระเอง</th>
                    <th class="text-center">กองทุนอื่น</th>
                    <th class="text-center text-primary">ลูกหนี้</th>
                    <th class="text-center text-primary">ชดเชย</th> 
                    <th class="text-center text-primary">ผลต่าง</th>                     
                    <th class="text-center text-primary" width="9%">สถานะ</th> 
                    <th class="text-center text-primary" width="6%">Action</th> 
                    <th class="text-center text-primary">Lock</th>                                  
                </tr>
                </thead>
                <?php $count = 1 ; ?>
                <?php $sum_income = 0 ; ?>
                <?php $sum_rcpt_money = 0 ; ?>
                <?php $sum_other = 0 ; ?>
                <?php $sum_debtor = 0 ; ?>
                <?php $sum_receive = 0 ; ?>
                @foreach($debtor as $row)
                <tr>
                    <td class="text-center"><input type="checkbox" name="checkbox[]" value="{{$row->an}}"></td> 
                    <td align="center">{{ $row->hn }}</td>
                    <td align="center">{{ $row->an }}</td>
                    <td align="left">{{ $row->ptname }}</td>
                    <td align="left">{{ $row->pttype }} </td>
                    <td align="right">{{ DateThai($row->regdate) }}</td>
                    <td align="right">{{ DateThai($row->dchdate) }}</td>
                    <td align="right">{{ $row->pdx }}</td>  
                    <td align="right">{{ $row->adjrw }}</td>                        
                    <td align="right">{{ number_format($row->income,2) }}</td>
                    <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                    <td align="right">{{ number_format($row->other,2) }}</td>
                    <td align="right" class="text-primary">{{ number_format($row->debtor,2) }}</td>  
                    <td align="right" @if($row->receive > 0) style="color:green" 
                        @elseif($row->receive < 0) style="color:red" @endif>
                        {{ number_format($row->receive,2) }}
                    </td>
                    <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                        @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                        {{ number_format($row->receive-$row->debtor,2) }}
                    </td>        
                    <td align="right">{{ $row->status }}</td> 
                    <td align="center">         
                        <button type="button" class="btn btn-outline-warning btn-sm text-primary " data-toggle="modal" data-target="#receive-{{ $row->an }}"> 
                            บันทึกชดเชย
                        </button>
                    </td>     
                    <td align="center" style="color:blue">{{ $row->debtor_lock }}</td>                            
                <?php $count++; ?>
                <?php $sum_income += $row->income ; ?>
                <?php $sum_rcpt_money += $row->rcpt_money ; ?>
                <?php $sum_debtor += $row->debtor ; ?> 
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
                <th class="text-center">กองทุนอื่น</th>
                <th class="text-center">ลูกหนี้</th> 
                <th class="text-center">ชดเชย</th>   
                <th class="text-center">ผลต่าง</th> 
                <th class="text-center">รายงาน</th>                
            </tr>
            </thead>
            <tr>
                <td class="text-primary" align="right">1102050102.603</td>
                <td class="text-primary" align="left">ลูกหนี้ค่ารักษา พรบ.รถ IP</td>
                <td class="text-primary" align="right">{{ number_format($sum_income,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_rcpt_money,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_other,2)}}</td>
                <td class="text-primary" align="right"><strong>{{ number_format($sum_debtor,2)}}</strong></td>
                <td align="right" @if($sum_receive > 0) style="color:green"
                    @elseif($sum_receive < 0) style="color:red" @endif>
                    <strong>{{ number_format($sum_receive,2)}}</strong>
                </td>
                <td align="right" @if(($sum_receive-$sum_debtor) > 0) style="color:green"
                    @elseif(($sum_receive-$sum_debtor) < 0) style="color:red" @endif>
                    <strong>{{ number_format($sum_receive-$sum_debtor,2)}}</strong>
                </td>
                <td align="center">
                    <a class="btn btn-outline-success btn-sm" href="{{ url('finance_debtor/1102050102_603_indiv_excel')}}" target="_blank">ส่งออกรายตัว</a>                
                    <a class="btn btn-outline-primary btn-sm" href="{{ url('finance_debtor/1102050102_603_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                </td>                    
            </tr>
        </table>
    </div> 
<hr>
    <div style="overflow-x:auto;">
        <form action="{{ url('finance_debtor/1102050102_603_confirm') }}" method="POST" enctype="multipart/form-data">
            @csrf                
            <table id="debtor_search" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center" width="5%">
                        <button type="submit" class="btn btn-outline-success btn-sm"  onclick="return confirm('ต้องการยืนยันลูกหนี้')">ยืนยันลูกหนี้</button></th>
                    <th class="text-center" colspan = "15">ผู้มารับบริการ พรบ.รถ IP วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }} รอยืนยันลูกหนี้</th>                         
                </tr>
                <tr class="table-secondary">
                    <th class="text-center"><input type="checkbox" onClick="toggle(this)"> All</th>  
                    <th class="text-center">ตึกผู้ป่วย</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">AN</th>
                    <th class="text-center">ชื่อ-สกุล</th>              
                    <th class="text-center">อายุ</th>
                    <th class="text-center" width = "15%">สิทธิ</th>
                    <th class="text-center">Admit</th>
                    <th class="text-center">Discharge</th>
                    <th class="text-center">ICD10</th>
                    <th class="text-center">AdjRW</th>
                    <th class="text-center">ค่ารักษาทั้งหมด</th>  
                    <th class="text-center">ชำระเอง</th>
                    <th class="text-center">กองทุนอื่น</th>
                    <th class="text-center">ลูกหนี้</th> 
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
                    <td align="left">{{ $row->pttype }}</td>
                    <td align="right">{{ DateThai($row->regdate) }}</td>
                    <td align="right">{{ DateThai($row->dchdate) }}</td>
                    <td align="right">{{ $row->pdx }}</td>      
                    <td align="right">{{ $row->adjrw }}</td>                        
                    <td align="right">{{ number_format($row->income,2) }}</td>
                    <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                    <td align="right">{{ number_format($row->other,2) }}</td>
                    <td align="right">{{ number_format($row->debtor,2) }}</td>
                <?php $count++; ?>
                @endforeach 
            </tr>   
            </table>
        </form>
    </div> 
 </div>
<br> 

<!-- Modal Structure -->
@foreach($debtor as $row)
<div id="receive-{{ $row->an }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="receive-{{ $row->an }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title text-primary">รายการการชำระเงิน/ลูกหนี้</h4>
          <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
          </button>
        </div>         
        <form action={{ url('finance_debtor/1102050102_603/update', $row->an) }} method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <input type="hidden" id="an" name="an">
                <div class="row">
                    <div class="col-md-6">  
                        <div class="mb-3">
                            <label for="ptname" class="form-label">ชื่อ-สกุล : <strong><font style="color:blue">{{ $row->ptname }}</font></strong></label>           
                        </div>
                    </div>
                    <div class="col-md-6">  
                        <div class="mb-3">                          
                            <label for="debtor" class="form-label">ลูกหนี้ : <strong><font style="color:blue">{{ $row->debtor }} </font> บาท</strong></label>           
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">  
                        <div class="mb-3">
                            <label for="item-description" class="form-label">วันที่เรียกเก็บ : <strong><font style="color:blue">{{ DateThai($row->charge_date) }}</font></strong></label>
                            <input type="date" class="form-control" id="charge_date" name="charge_date" value="{{ $row->charge_date }}" >
                        </div>
                        <div class="mb-3">
                            <label for="item-description" class="form-label">เลขที่หนังสือเรียกเก็บ : <strong><font style="color:blue">{{ $row->charge_no }}</font></strong></label>
                            <input type="text" class="form-control" id="charge_no" name="charge_no" value="{{ $row->charge_no }}" >
                        </div>
                        <div class="mb-3">
                            <label for="item-description" class="form-label">จำนวนเงิน : <strong><font style="color:blue">{{ number_format($row->charge,2) }}</font></strong></label>
                            <input type="text" class="form-control" id="charge" name="charge" value="{{ $row->charge }}" >
                        </div> 
                        <div class="mb-3">
                            <label for="item-description" class="form-label">สถานะ : <strong><font style="color:blue">{{$row->status}}</font></strong></label>
                            <select class="form-select my-1" name="status">                                                       
                                <option value="ยืนยันลูกหนี้" @if ($row->status == 'ยืนยันลูกหนี้') selected="selected" @endif>ยืนยันข้อมูลลูกหนี้</option>                                           
                                <option value="อยู่ระหว่างเรียกเก็บ" @if ($row->status  == 'อยู่ระหว่างเรียกเก็บ') selected="selected" @endif>อยู่ระหว่างเรียกเก็บ</option> 
                                <option value="อยู่ระหว่างการขออุทธรณ์" @if ($row->status == 'อยู่ระหว่างการขออุทธรณ์') selected="selected" @endif>อยู่ระหว่างการขออุทธรณ์</option>
                                <option value="กระทบยอดแล้ว" @if ($row->status == 'กระทบยอดแล้ว') selected="selected" @endif>กระทบยอดแล้ว</option>  
                            </select> 
                        </div>      
                    </div> 
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="item-description" class="form-label">วันที่ชดเชย : <strong><font style="color:blue">{{ DateThai($row->receive_date) }}</font></strong></label>
                            <input type="date" class="form-control" id="receive_date" name="receive_date" value="{{ $row->receive_date }}" >
                        </div>
                        <div class="mb-3">
                            <label for="item-description" class="form-label">เลขที่หนังสือชดเชย : <strong><font style="color:blue">{{ $row->receive_no }}</font></strong></label>
                            <input type="text" class="form-control" id="receive_no" name="receive_no" value="{{ $row->receive_no }}" >
                        </div>
                        <div class="mb-3">
                            <label for="item-description" class="form-label">จำนวนเงิน : <strong><font style="color:blue">{{ number_format($row->receive,2) }}</font></strong></label>
                            <input type="text" class="form-control" id="receive" name="receive" value="{{ $row->receive }}" >
                        </div>                
                        <div class="mb-3">
                            <label for="item-description" class="form-label">เลขที่ใบเสร็จ : <strong><font style="color:blue">{{ $row->repno }}</font></strong></label>
                            <input type="text" class="form-control" id="repno" name="repno" value="{{ $row->repno }}">
                        </div>
                    </div>
                </div> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
            </div>
        </form>     
      </div>
    </div>
</div>
@endforeach

@endsection
<!-- Modal -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

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
        $('#debtor').DataTable({sDom: 'lrtip'});
    });
</script>
