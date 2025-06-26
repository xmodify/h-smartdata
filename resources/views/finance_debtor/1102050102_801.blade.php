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
    function toggle_lgo(source) {
        checkboxes = document.getElementsByName('checkbox_lgo[]');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>
<script>
    function toggle_pp(source) {
        checkboxes = document.getElementsByName('checkbox_pp[]');
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
        <form action="{{ url('finance_debtor/1102050102_801_delete') }}" method="POST" enctype="multipart/form-data">
            @csrf   
            @method('DELETE')
            <table id="debtor" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-success">
                    <th class="text-center" width="5%">
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('ต้องการลบลูกหนี้')">ลบลูกหนี้</button>
                    </th>
                    <th class="text-center" colspan = "11">1102050102.801-ลูกหนี้ค่ารักษา เบิกจ่ายตรง อปท.OP วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</th> 
                    <th class="text-center text-primary" colspan = "6">การชดเชย</th>                                                 
                </tr>
                <tr class="table-success">
                    <th class="text-center"><input type="checkbox" onClick="toggle_d(this)"> All</th>
                    <th class="text-center">วันที่</th>
                    <th class="text-center">HN</th>
                    <th class="text-center" width="10%">ชื่อ-สกุล</th>
                    <th class="text-center">สิทธิ</th>
                    <th class="text-center">ICD10</th>
                    <th class="text-center" width="5%">ค่ารักษาทั้งหมด</th>  
                    <th class="text-center" width="5%">ชำระเอง</th>    
                    <th class="text-center" width="5%">ทั่วไป</th> 
                    <th class="text-center" width="5%">ฟอกไต</th> 
                    <th class="text-center" width="5%">PPFS</th> 
                    <th class="text-center" width="6%">กองทุนอื่น</th> 
                    <th class="text-center text-primary" width="6%">รวมลูกหนี้</th>
                    <th class="text-center text-primary" width="5%">ชดเชย</th> 
                    <th class="text-center text-primary" width="5%">ผลต่าง</th>
                    <th class="text-center text-primary" width="5%">PPFS</th>  
                    <th class="text-center text-primary" width="5%">REP</th>               
                    <th class="text-center text-primary" width="3%">Lock</th>     
                                
                </tr>
                </thead>
                <?php $count = 1 ; ?>
                <?php $sum_income = 0 ; ?>
                <?php $sum_rcpt_money = 0 ; ?>
                <?php $sum_lgo = 0 ; ?>  
                <?php $sum_kidney = 0 ; ?>  
                <?php $sum_pp = 0 ; ?>  
                <?php $sum_other = 0 ; ?> 
                <?php $sum_debtor = 0 ; ?>     
                <?php $sum_receive = 0 ; ?>
                <?php $sum_receive_pp = 0 ; ?>
                @foreach($debtor as $row)
                <tr>
                    <td class="text-center"><input type="checkbox" name="checkbox_d[]" value="{{$row->vn}}"></td>   
                    <td align="right">{{ DateThai($row->vstdate) }} {{ $row->vsttime }}</td>
                    <td align="center">{{ $row->hn }}</td>
                    <td align="left">{{ $row->ptname }}</td>
                    <td align="right">{{ $row->pttype }}</td>
                    <td align="right">{{ $row->pdx }}</td>                      
                    <td align="right">{{ number_format($row->income,2) }}</td>
                    <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                    <td align="right">{{ number_format($row->lgo,2) }}</td>
                    <td align="right">{{ number_format($row->kidney,2) }}</td> 
                    <td align="right">{{ number_format($row->pp,2) }}</td>   
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
                    <td align="right" @if($row->receive_pp > 0) style="color:green" 
                        @elseif($row->receive_pp < 0) style="color:red" @endif>
                        {{ number_format($row->receive_pp,2) }}
                    </td>
                    <td align="center">{{ $row->repno }} {{ $row->rid }}</td>
                    <td align="center" style="color:blue">{{ $row->debtor_lock }}</td>                          
                <?php $count++; ?>
                <?php $sum_income += $row->income ; ?>
                <?php $sum_rcpt_money += $row->rcpt_money ; ?>
                <?php $sum_lgo += $row->lgo ; ?>
                <?php $sum_kidney += $row->kidney ; ?>
                <?php $sum_pp += $row->pp ; ?>
                <?php $sum_other += $row->other ; ?>
                <?php $sum_debtor += $row->debtor ; ?>
                <?php $sum_receive += $row->receive ; ?>   
                <?php $sum_receive_pp += $row->receive_pp ; ?>        
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
                <th class="text-center">ทั่วไป</th> 
                <th class="text-center">ฟอกไต</th>    
                <th class="text-center">PPFS | ชดเชย</th>    
                <th class="text-center">กองทุนอื่น</th> 
                <th class="text-center">รวมลูกหนี้</th> 
                <th class="text-center">ชดเชย</th>   
                <th class="text-center">ผลต่าง</th> 
                <th class="text-center">รายงาน</th>                
            </tr>
            </thead>
            <tr>
                <td class="text-primary" align="right">1102050102.801</td>
                <td class="text-primary" align="left">ลูกหนี้ค่ารักษา เบิกจ่ายตรง อปท.OP</td>
                <td class="text-primary" align="right">{{ number_format($sum_income,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_rcpt_money,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_lgo,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_kidney,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_pp,2)}} | {{ number_format($sum_receive_pp,2)}}</td>
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
                    <a class="btn btn-outline-success btn-sm" href="{{ url('finance_debtor/1102050102_801_indiv_excel')}}" target="_blank">ส่งออกรายตัว</a>                
                    <a class="btn btn-outline-primary btn-sm" href="{{ url('finance_debtor/1102050102_801_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                </td>                    
            </tr>
        </table>
    </div> 
<hr>
    <!-- Pills Tabs -->
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="lgo-tab" data-bs-toggle="pill" data-bs-target="#lgo" type="button" role="tab" aria-controls="lgo" aria-selected="false">LGO ทั่วไป</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pp-tab" data-bs-toggle="pill" data-bs-target="#pp" type="button" role="tab" aria-controls="pp" aria-selected="false">LGO PP</button>
        </li>
    </ul>
    <!-- Pills Tabs -->
    <div class="tab-content pt-2" id="myTabContent">
        <div class="tab-pane fade show active" id="lgo" role="tabpanel" aria-labelledby="lgo-tab">
            <div style="overflow-x:auto;">
                <form action="{{ url('finance_debtor/1102050102_801_confirm') }}" method="POST" enctype="multipart/form-data">
                    @csrf                
                    <table id="debtor_search_lgo" class="table table-bordered table-striped my-3" width="100%">
                        <thead>
                        <tr class="table-secondary">
                            <th class="text-center" width="5%">
                                <button type="submit" class="btn btn-outline-success btn-sm"  onclick="return confirm('ต้องการยืนยันลูกหนี้')">ยืนยันลูกหนี้</button></th>
                            <th class="text-center" colspan = "14">ผู้มารับบริการเบิกจ่ายตรง อปท.OP ทั่วไป วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }} รอยืนยันลูกหนี้</th>      
                        </tr>
                        <tr class="table-secondary">
                            <th class="text-center"><input type="checkbox" onClick="toggle_lgo(this)"> All</th>  
                            <th class="text-center" width="6%">วันที่</th>
                            <th class="text-center">Q</th>
                            <th class="text-center">HN</th>
                            <th class="text-center">ชื่อ-สกุล</th>
                            <th class="text-center">สิทธิ</th>
                            <th class="text-center">ICD10</th>
                            <th class="text-center">ค่ารักษาทั้งหมด</th>  
                            <th class="text-center">ชำระเอง</th>    
                            <th class="text-center">ทั่วไป</th>                      
                            <th class="text-center">ฟอกไต</th> 
                            <th class="text-center">PPFS</th> 
                            <th class="text-center">กองทุนอื่น</th> 
                            <th class="text-center">ลูกหนี้</th> 
                            <th class="text-center">Upload E-Claim</th> 
                        </tr>
                        </thead>
                        <?php $count = 1 ; ?>
                        @foreach($debtor_search_lgo as $row)
                        <tr>
                            <td class="text-center"><input type="checkbox" name="checkbox_lgo[]" value="{{$row->vn}}"></td>                   
                            <td align="center">{{ DateThai($row->vstdate) }} {{ $row->vsttime }}</td>
                            <td align="center">{{ $row->oqueue }}</td>
                            <td align="center">{{ $row->hn }}</td>
                            <td align="left">{{ $row->ptname }}</td>
                            <td align="right">{{ $row->pttype }}</td>
                            <td align="right">{{ $row->pdx }}</td>  
                            <td align="right">{{ number_format($row->income,2) }}</td>
                            <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                            <td align="right">{{ number_format($row->lgo,2) }}</td>
                            <td align="right">{{ number_format($row->kidney,2) }}</td>
                            <td align="right">{{ number_format($row->pp,2) }}</td>
                            <td align="right">{{ number_format($row->other,2) }}</td>
                            <td align="right">{{ number_format($row->debtor,2) }}</td>
                            <td align="right">{{ $row->ecliam }}</td> 
                        <?php $count++; ?>
                        @endforeach 
                        </tr>   
                    </table>
                </form>
            </div> 
        </div>
      
        <div class="tab-pane fade" id="pp" role="tabpanel" aria-labelledby="pp-tab">
            <div style="overflow-x:auto;">
                <form action="{{ url('finance_debtor/1102050102_801_confirm_pp') }}" method="POST" enctype="multipart/form-data"> 
                    @csrf                
                    <table id="debtor_search_pp" class="table table-bordered table-striped my-3" width="100%">
                        <thead>
                        <tr class="table-secondary">
                            <th class="text-center" width="5%">
                                <button type="submit" class="btn btn-outline-success btn-sm"  onclick="return confirm('ต้องการยืนยันลูกหนี้')">ยืนยันลูกหนี้</button></th>
                            <th class="text-center" colspan = "14">ผู้มารับบริการเบิกจ่ายตรง อปท. PP วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }} รอยืนยันลูกหนี้</th>      
                        </tr>
                        <tr class="table-secondary">
                            <th class="text-center"><input type="checkbox" onClick="toggle_pp(this)"> All</th>  
                            <th class="text-center" width="6%">วันที่</th>
                            <th class="text-center">Q</th>
                            <th class="text-center">HN</th>
                            <th class="text-center">ชื่อ-สกุล</th>
                            <th class="text-center">สิทธิ</th>
                            <th class="text-center">ICD10</th>
                            <th class="text-center">ค่ารักษาทั้งหมด</th>  
                            <th class="text-center">ชำระเอง</th>    
                            <th class="text-center">ทั่วไป</th>  
                            <th class="text-center">ฟอกไต</th> 
                            <th class="text-center">PPFS</th> 
                            <th class="text-center">กองทุนอื่น</th> 
                            <th class="text-center">ลูกหนี้</th> 
                            <th class="text-center">Upload E-Claim</th> 
                        </tr>
                        </thead>
                        <?php $count = 1 ; ?>
                        @foreach($debtor_search_pp as $row)
                        <tr>
                            <td class="text-center"><input type="checkbox" name="checkbox_pp[]" value="{{$row->vn}}"></td>                   
                            <td align="center">{{ DateThai($row->vstdate) }} {{ $row->vsttime }}</td>
                            <td align="center">{{ $row->oqueue }}</td>
                            <td align="center">{{ $row->hn }}</td>
                            <td align="left">{{ $row->ptname }}</td>
                            <td align="right">{{ $row->pttype }}</td>
                            <td align="right">{{ $row->pdx }}</td>  
                            <td align="right">{{ number_format($row->income,2) }}</td>
                            <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                            <td align="right">{{ number_format($row->lgo,2) }}</td>  
                            <td align="right">{{ number_format($row->kidney,2) }}</td>    
                            <td align="right">{{ number_format($row->pp,2) }}</td>
                            <td align="right">{{ number_format($row->other,2) }}</td>
                            <td align="right">{{ number_format($row->debtor,2) }}</td>
                            <td align="right">{{ $row->ecliam }}</td> 
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
        $('#debtor_search_lgo').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#debtor_search_pp').DataTable();
    });
</script>

