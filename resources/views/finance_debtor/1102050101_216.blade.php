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
    function toggle_d(source) {
        checkbox = document.getElementsByName('checkbox_d[]');
        for (var i = 0; i < checkbox.length; i++) {
            checkbox[i].checked = source.checked;
        }
    }
</script>
<script>
    function toggle_kidney(source) {
        checkboxes = document.getElementsByName('checkbox_kidney[]');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>
<script>
    function toggle_cr(source) {
        checkboxes = document.getElementsByName('checkbox_cr[]');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>
<script>
    function toggle_anywhere(source) {
        checkboxes = document.getElementsByName('checkbox_anywhere[]');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
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
        <form action="{{ url('finance_debtor/1102050101_216_delete') }}" method="POST" enctype="multipart/form-data">
            @csrf   
            @method('DELETE')
            <table id="debtor" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-success">
                    <th class="text-center" width="5%">
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('ต้องการลบลูกหนี้')">ลบลูกหนี้</button>
                    </th>
                    <th class="text-center" colspan = "10">1102050101.216-ลูกหนี้ค่ารักษา UC-OP บริการเฉพาะ (CR) วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</th> 
                    <th class="text-center text-primary" colspan = "5">การชดเชย</th>                                                 
                </tr>
                <tr class="table-success">
                    <th class="text-center"><input type="checkbox" onClick="toggle_d(this)"> All</th>
                    <th class="text-center">วันที่</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">ชื่อ-สกุล</th>
                    <th class="text-center">สิทธิ</th>
                    <th class="text-center">ICD10</th>
                    <th class="text-center">ค่ารักษาทั้งหมด</th>  
                    <th class="text-center">ชำระเอง</th>    
                    <th class="text-center">ฟอกไต</th> 
                    <th class="text-center">บริการเฉพาะ</th> 
                    <th class="text-center">OP Anywhere</th> 
                    <th class="text-center text-primary">รวมลูกหนี้</th>
                    <th class="text-center text-primary">ชดเชย</th> 
                    <th class="text-center text-primary">ผลต่าง</th>  
                    <th class="text-center text-primary">REP</th>               
                    <th class="text-center text-primary">Lock</th>     
                                
                </tr>
                </thead>
                <?php $count = 1 ; ?>
                <?php $sum_income = 0 ; ?>
                <?php $sum_rcpt_money = 0 ; ?>
                <?php $sum_kidney = 0 ; ?>  
                <?php $sum_cr = 0 ; ?>   
                <?php $sum_anywhere = 0 ; ?> 
                <?php $sum_debtor = 0 ; ?>     
                <?php $sum_receive = 0 ; ?>
                @foreach($debtor as $row)
                <tr>
                    <td class="text-center"><input type="checkbox" name="checkbox_d[]" value="{{$row->vn}}"></td>   
                    <td align="right">{{ DateThai($row->vstdate) }} {{ $row->vsttime }}</td>
                    <td align="center">{{ $row->hn }}</td>
                    <td align="left">{{ $row->ptname }}</td>
                    <td align="right">{{ $row->pttype }} [{{ $row->hospmain }}]</td>
                    <td align="right">{{ $row->pdx }}</td>                      
                    <td align="right">{{ number_format($row->income,2) }}</td>
                    <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                    <td align="right">{{ number_format($row->kidney,2) }}</td>
                    <td align="right">{{ number_format($row->cr,2) }}</td>  
                    <td align="right">{{ number_format($row->anywhere,2) }}</td>  
                    <td align="right" class="text-primary">{{ number_format($row->debtor,2) }}</td>  
                    <td align="right" @if($row->receive > 0) style="color:green" 
                        @elseif($row->receive < 0) style="color:red" @endif>
                        {{ number_format($row->receive,2) }}
                    </td>
                    <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                        @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                        {{ number_format($row->receive-$row->debtor,2) }}
                    </td>
                    <td align="center">{{ $row->repno }}</td>
                    <td align="center" style="color:blue">{{ $row->debtor_lock }}</td>                          
                <?php $count++; ?>
                <?php $sum_income += $row->income ; ?>
                <?php $sum_rcpt_money += $row->rcpt_money ; ?>
                <?php $sum_kidney += $row->kidney ; ?>
                <?php $sum_cr += $row->cr ; ?>
                <?php $sum_anywhere += $row->anywhere ; ?>
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
                <th class="text-center">ฟอกไต</th> 
                <th class="text-center">บริการเฉพาะ</th>
                <th class="text-center">OP Anywhere</th>
                <th class="text-center">รวมลูกหนี้</th> 
                <th class="text-center">ชดเชย</th>   
                <th class="text-center">ผลต่าง</th> 
                <th class="text-center">รายงาน</th>                
            </tr>
            </thead>
            <tr>
                <td class="text-primary" align="right">1102050101.216</td>
                <td class="text-primary" align="left">ลูกหนี้ค่ารักษา UC-OP บริการเฉพาะ (CR)</td>
                <td class="text-primary" align="right">{{ number_format($sum_income,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_rcpt_money,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_kidney,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_cr,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_anywhere,2)}}</td>
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
                    <a class="btn btn-outline-success btn-sm" href="{{ url('finance_debtor/1102050101_216_indiv_excel')}}" target="_blank">ส่งออกรายตัว</a>                
                    <a class="btn btn-outline-primary btn-sm" href="{{ url('finance_debtor/1102050101_216_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                </td>                    
            </tr>
        </table>
    </div> 
<hr>
    <!-- Pills Tabs -->
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="kidney-tab" data-bs-toggle="pill" data-bs-target="#kidney" type="button" role="tab" aria-controls="kidney" aria-selected="true">ฟอกไต</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="cr-tab" data-bs-toggle="pill" data-bs-target="#cr" type="button" role="tab" aria-controls="cr" aria-selected="false">บริการเฉพาะ</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="anywhere-tab" data-bs-toggle="pill" data-bs-target="#anywhere" type="button" role="tab" aria-controls="anywhere" aria-selected="false">OP Anywhere</button>
        </li>
    </ul> 
    <!-- Pills Tabs -->
    <div class="tab-content pt-2" id="myTabContent">
        <div class="tab-pane fade show active" id="kidney" role="tabpanel" aria-labelledby="kidney-tab">
            <div style="overflow-x:auto;">
                <form action="{{ url('finance_debtor/1102050101_216_confirm_kidney') }}" method="POST" enctype="multipart/form-data">
                    @csrf                
                    <table id="debtor_search_kidney" class="table table-bordered table-striped my-3">
                        <thead>
                        <tr class="table-secondary">
                            <th class="text-center" width="5%">
                                <button type="submit" class="btn btn-outline-success btn-sm"  onclick="return confirm('ต้องการยืนยันลูกหนี้')">ยืนยันลูกหนี้</button></th>
                            <th class="text-center" colspan = "11">ผู้มารับบริการ UC-OP บริการเฉพาะ (CR) ฟอกไต วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }} รอยืนยันลูกหนี้</th>
                        </tr>
                        <tr class="table-secondary">
                            <th class="text-center"><input type="checkbox" onClick="toggle_kidney(this)"> All</th>  
                            <th class="text-center" width="6%">วันที่</th>
                            <th class="text-center">Q</th>
                            <th class="text-center">HN</th>
                            <th class="text-center">ชื่อ-สกุล</th>
                            <th class="text-center">สิทธิ</th>
                            <th class="text-center">ICD10</th>
                            <th class="text-center">ค่ารักษาทั้งหมด</th>  
                            <th class="text-center">ชำระเอง</th>
                            <th class="text-center">กองทุนอื่น</th>                          
                            <th class="text-center">เรียกเก็บ</th>
                            <th class="text-center" width="25%">รายการเรียกเก็บ</th>
                        </tr>
                        </thead>
                        <?php $count = 1 ; ?>
                        @foreach($debtor_search_kidney as $row)
                        <tr>
                            <td class="text-center"><input type="checkbox" name="checkbox_kidney[]" value="{{$row->vn}}"></td>                   
                            <td align="center">{{ DateThai($row->vstdate) }} {{ $row->vsttime }}</td>
                            <td align="center">{{ $row->oqueue }}</td>
                            <td align="center">{{ $row->hn }}</td>
                            <td align="left">{{ $row->ptname }}</td>
                            <td align="right">{{ $row->pttype }} [{{ $row->hospmain }}]</td>
                            <td align="right">{{ $row->pdx }}</td>                  
                            <td align="right">{{ number_format($row->income,2) }}</td>
                            <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                            <td align="right">{{ number_format($row->other,2) }}</td>
                            <td align="right">{{ number_format($row->debtor,2) }}</td>
                            <td align="left">{{ $row->other_list }}</td> 
                        <?php $count++; ?>
                        @endforeach 
                    </tr>   
                    </table>
                </form>
            </div> 
        </div>
    
        <div class="tab-pane fade" id="cr" role="tabpanel" aria-labelledby="cr-tab">
            <div style="overflow-x:auto;">
                <form action="{{ url('finance_debtor/1102050101_216_confirm_cr') }}" method="POST" enctype="multipart/form-data">
                    @csrf                
                    <table id="debtor_search_cr" class="table table-bordered table-striped my-3" width="100%">
                        <thead>
                        <tr class="table-secondary">
                            <th class="text-center" width="5%">
                                <button type="submit" class="btn btn-outline-success btn-sm"  onclick="return confirm('ต้องการยืนยันลูกหนี้')">ยืนยันลูกหนี้</button></th>
                            <th class="text-center" colspan = "11">ผู้มารับบริการ UC-OP บริการเฉพาะ (CR) วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }} รอยืนยันลูกหนี้</th> 
                            <th class="text-center text-success" colspan = "3">FDH</th>                            
                        </tr>
                        <tr class="table-secondary">
                            <th class="text-center"><input type="checkbox" onClick="toggle_cr(this)"> All</th>  
                            <th class="text-center" width="6%">วันที่</th>
                            <th class="text-center">Q</th>
                            <th class="text-center">HN</th>
                            <th class="text-center">ชื่อ-สกุล</th>
                            <th class="text-center">สิทธิ</th>
                            <th class="text-center">ICD10</th>
                            <th class="text-center">Project</th>
                            <th class="text-center">ค่ารักษาทั้งหมด</th>  
                            <th class="text-center">ชำระเอง</th>                        
                            <th class="text-center">เรียกเก็บ</th> 
                            <th class="text-center">รายการเรียกเก็บ</th> 
                            <th class="text-center text-success" width="4%">พร้อมส่ง</th>
                            <th class="text-center text-success" width="4%">ประสงค์เบิก</th>
                            <th class="text-center text-success" width="6%">Upload DateTime</th>
                        </tr>
                        </thead>
                        <?php $count = 1 ; ?>
                        @foreach($debtor_search_cr as $row)
                        <tr>
                            <td class="text-center"><input type="checkbox" name="checkbox_cr[]" value="{{$row->vn}}"></td>                   
                            <td align="center">{{ DateThai($row->vstdate) }} {{ $row->vsttime }}</td>
                            <td align="center">{{ $row->oqueue }}</td>
                            <td align="center">{{ $row->hn }}</td>
                            <td align="left">{{ $row->ptname }}</td>
                            <td align="right">{{ $row->pttype }} [{{ $row->hospmain }}]</td>
                            <td align="right">{{ $row->pdx }}</td>     
                            <td align="right">{{ $row->project }}</td>               
                            <td align="right">{{ number_format($row->income,2) }}</td>
                            <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                            <td align="right">{{ number_format($row->debtor,2) }}</td>
                            <td align="left">{{ $row->other_list }}</td> 
                            <td align="center">{{ $row->confirm_and_locked }}</td> 
                            <td align="center">{{ $row->request_funds }}</td> 
                            <td align="center">{{ DateTimeThai($row->fdh) }}</td> 
                        <?php $count++; ?>
                        @endforeach 
                        </tr>   
                    </table>
                </form>
            </div> 
        </div>

        <div class="tab-pane fade" id="anywhere" role="tabpanel" aria-labelledby="anywhere-tab">
            <div style="overflow-x:auto;">
                <form action="{{ url('finance_debtor/1102050101_216_confirm_anywhere') }}" method="POST" enctype="multipart/form-data">
                    @csrf                
                    <table id="debtor_search_anywhere" class="table table-bordered table-striped my-3" width="100%">
                        <thead>
                        <tr class="table-secondary">
                            <th class="text-center" width="5%">
                                <button type="submit" class="btn btn-outline-success btn-sm"  onclick="return confirm('ต้องการยืนยันลูกหนี้')">ยืนยันลูกหนี้</button></th>
                            <th class="text-center" colspan = "12">ผู้มารับบริการ UC-OP บริการเฉพาะ (CR) OP Anywhere วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }} รอยืนยันลูกหนี้</th> 
                            <th class="text-center text-success" colspan = "3">FDH</th>                            
                        </tr>
                        <tr class="table-secondary">
                            <th class="text-center"><input type="checkbox" onClick="toggle_anywhere(this)"> All</th>  
                            <th class="text-center" width="6%">วันที่</th>
                            <th class="text-center">Q</th>
                            <th class="text-center">HN</th>
                            <th class="text-center">ชื่อ-สกุล</th>
                            <th class="text-center">สิทธิ</th>
                            <th class="text-center">ICD10</th>
                            <th class="text-center">ความรุนแรง</th>
                            <th class="text-center">ค่ารถ Refer</th>  
                            <th class="text-center">Project</th>
                            <th class="text-center">ค่ารักษาทั้งหมด</th>  
                            <th class="text-center">ชำระเอง</th>                                                   
                            <th class="text-center">เรียกเก็บ</th>
                            <th class="text-center text-success" width="4%">พร้อมส่ง</th>
                            <th class="text-center text-success" width="4%">ประสงค์เบิก</th>
                            <th class="text-center text-success" width="6%">Upload DateTime</th>
                        </tr>
                        </thead>
                        <?php $count = 1 ; ?>
                        @foreach($debtor_search_anywhere as $row)
                        <tr>
                            <td class="text-center"><input type="checkbox" name="checkbox_anywhere[]" value="{{$row->vn}}"></td>                   
                            <td align="center">{{ DateThai($row->vstdate) }} {{ $row->vsttime }}</td>
                            <td align="center">{{ $row->oqueue }}</td>
                            <td align="center">{{ $row->hn }}</td>
                            <td align="left">{{ $row->ptname }}</td>
                            <td align="right">{{ $row->pttype }} [{{ $row->hospmain }}]</td>
                            <td align="right">{{ $row->pdx }}</td>  
                            <td align="right">{{ $row->er_emergency_type }}</td>  
                            <td align="right">{{ number_format($row->refer,2) }}</td>      
                            <td align="right">{{ $row->project }}</td>             
                            <td align="right">{{ number_format($row->income,2) }}</td>
                            <td align="right">{{ number_format($row->rcpt_money,2) }}</td>                   
                            <td align="right">{{ number_format($row->debtor,2) }}</td>
                            <td align="center">{{ $row->confirm_and_locked }}</td> 
                            <td align="center">{{ $row->request_funds }}</td> 
                            <td align="center">{{ DateTimeThai($row->fdh) }}</td> 
                        <?php $count++; ?>
                        @endforeach 
                    </tr>   
                    </table>
                </form>
            </div> 
        </div>
    </div><!-- End Pills Tabs -->
</div>
<br> 
@endsection
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#debtor').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#debtor_search_kidney').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#debtor_search_cr').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#debtor_search_anywhere').DataTable();
    });
</script>
