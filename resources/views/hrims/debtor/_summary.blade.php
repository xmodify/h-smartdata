@extends('layouts.hrims')

@section('content')
    <div class="container-fluid">
        <div class="card-body"> 
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
            <div class="alert alert-success text-primary" role="alert"><strong>ลูกหนี้ค่ารักษาพยาบาล วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>
        
            <div style="overflow-x:auto;">
                <table id="debtor" class="table table-bordered table-striped my-3">
                    <thead>  
                    <tr class="table-primary">
                        <th class="text-center">ลำดับ</th>
                        <th class="text-center">รหัสผังบัญชี</th>
                        <th class="text-center">ชื่อผังบัญชี</th>
                        <th class="text-center">จำนวน</th>
                        <th class="text-center">เรียกเก็บ</th> 
                        <th class="text-center">ชดเชย</th>
                        <th class="text-center">ผลต่าง</th>
                        <th class="text-center"><a class="btn btn-outline-danger" href="{{ url('hrims/debtor/summary_pdf')}}" target="_blank">พิมพ์สรุป</a></th>
                    </tr>
                    </thead>
                    <tr>
                        <th class="text-primary" colspan = "8">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ผู้ป่วยนอก</th>        
                    </tr>
                    <tr>            
                        <?php $sum_1102050101_103_debtor = 0 ; ?>
                        <?php $sum_1102050101_103_receive = 0 ; ?>
                        @foreach($_1102050101_103 as $row)
                        <td align="center">1</td>
                        <td align="right">1102050101.103</td>
                        <td class="text-left">ลูกหนี้ค่าตรวจสุขภาพ หน่วยงานภาครัฐ</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_103_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>  
                        <?php $sum_1102050101_103_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_103_receive += $row->receive ; ?>
                        @endforeach   
                    </tr>    
                    <tr>
                        <?php $sum_1102050101_109_debtor = 0 ; ?>
                        <?php $sum_1102050101_109_receive = 0 ; ?>
                        @foreach($_1102050101_109 as $row)
                        <td align="center">2</td>
                        <td align="right">1102050101.109</td>
                        <td class="text-left">ลูกหนี้-ระบบปฏิบัติการฉุกเฉิน</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_109_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>   
                        <?php $sum_1102050101_109_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_109_receive += $row->receive ; ?>
                        @endforeach    
                    </tr>   
                    <tr>
                        <?php $sum_1102050101_201_debtor = 0 ; ?>
                        <?php $sum_1102050101_201_receive = 0 ; ?>
                        @foreach($_1102050101_201 as $row)
                        <td align="center">3</td>
                        <td align="right">1102050101.201</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา UC-OP ใน CUP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_201_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_201_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_201_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_203_debtor = 0 ; ?>
                        <?php $sum_1102050101_203_receive = 0 ; ?>
                        @foreach($_1102050101_203 as $row)
                        <td align="center">3</td>
                        <td align="right">1102050101.203</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา UC-OP นอก CUP (ในจังหวัดสังกัด สธ.)</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_203_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_203_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_203_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_209_debtor = 0 ; ?>
                        <?php $sum_1102050101_209_receive = 0 ; ?>
                        @foreach($_1102050101_209 as $row)
                        <td align="center">4</td>
                        <td align="right">1102050101.209</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา ด้านการสร้างเสริมสุขภาพและป้องกันโรค (P&P)</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_209_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_209_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_209_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_216_debtor = 0 ; ?>
                        <?php $sum_1102050101_216_receive = 0 ; ?>
                        @foreach($_1102050101_216 as $row)
                        <td align="center">5</td>
                        <td align="right">1102050101.216</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา UC-OP บริการเฉพาะ (CR)</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">            
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_216_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_216_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_216_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_301_debtor = 0 ; ?>
                        <?php $sum_1102050101_301_receive = 0 ; ?>
                        @foreach($_1102050101_301 as $row)
                        <td align="center">6</td>
                        <td align="right">1102050101.301</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา ประกันสังคม OP-เครือข่าย</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_301_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_301_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_301_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_303_debtor = 0 ; ?>
                        <?php $sum_1102050101_303_receive = 0 ; ?>
                        @foreach($_1102050101_303 as $row)
                        <td align="center">7</td>
                        <td align="right">1102050101.303</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา ประกันสังคม OP-นอกเครือข่าย สังกัด สป.สธ.</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">              
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_303_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_303_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_303_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_307_debtor = 0 ; ?>
                        <?php $sum_1102050101_307_receive = 0 ; ?>
                        @foreach($_1102050101_307 as $row)
                        <td align="center">8</td>
                        <td align="right">1102050101.307</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา ประกันสังคม-กองทุนทดแทน</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>     
                        <td align="center">                
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_307_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_307_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_307_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_309_debtor = 0 ; ?>
                        <?php $sum_1102050101_309_receive = 0 ; ?>
                        @foreach($_1102050101_309 as $row)
                        <td align="center">9</td>
                        <td align="right">1102050101.309</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา ประกันสังคม-ค่าใช้จ่ายสูง/อุบัติเหตุ/ฉุกเฉิน OP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">              
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_309_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_309_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_309_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_401_debtor = 0 ; ?>
                        <?php $sum_1102050101_401_receive = 0 ; ?>
                        @foreach($_1102050101_401 as $row)
                        <td align="center">10</td>
                        <td align="right">1102050101.401</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา เบิกจ่ายตรงกรมบัญชีกลาง OP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_401_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_401_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_401_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_501_debtor = 0 ; ?>
                        <?php $sum_1102050101_501_receive = 0 ; ?>
                        @foreach($_1102050101_501 as $row)
                        <td align="center">11</td>
                        <td align="right">1102050101.501</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา คนต่างด้าวและแรงงานต่างด้าว OP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">              
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_501_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_501_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_501_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_703_debtor = 0 ; ?>
                        <?php $sum_1102050101_703_receive = 0 ; ?>
                        @foreach($_1102050101_703 as $row)
                        <td align="center">12</td>
                        <td align="right">1102050101.703</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา บุคคลที่มีปัญหาสถานะและสิทธิ เบิกจากส่วนกลาง OP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_703_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_703_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_703_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050102_106_debtor = 0 ; ?>
                        <?php $sum_1102050102_106_receive = 0 ; ?>
                        @foreach($_1102050102_106 as $row)
                        <td align="center">13</td>
                        <td align="right">1102050102.106</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา ชําระเงิน OP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">             
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050102_106_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050102_106_debtor += $row->debtor ; ?>
                        <?php $sum_1102050102_106_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050102_108_debtor = 0 ; ?>
                        <?php $sum_1102050102_108_receive = 0 ; ?>
                        @foreach($_1102050102_108 as $row)
                        <td align="center">14</td>
                        <td align="right">1102050102.108</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา เบิกต้นสังกัด OP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>     
                        <td align="center">             
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050102_108_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050102_108_debtor += $row->debtor ; ?>
                        <?php $sum_1102050102_108_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050102_602_debtor = 0 ; ?>
                        <?php $sum_1102050102_602_receive = 0 ; ?>
                        @foreach($_1102050102_602 as $row)
                        <td align="center">15</td>
                        <td align="right">1102050102.602</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา พรบ.รถ OP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050102_602_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050102_602_debtor += $row->debtor ; ?>
                        <?php $sum_1102050102_602_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050102_801_debtor = 0 ; ?>
                        <?php $sum_1102050102_801_receive = 0 ; ?>
                        @foreach($_1102050102_801 as $row)
                        <td align="center">16</td>
                        <td align="right">1102050102.801</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา เบิกจ่ายตรง อปท.OP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050102_801_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050102_801_debtor += $row->debtor ; ?>
                        <?php $sum_1102050102_801_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050102_803_debtor = 0 ; ?>
                        <?php $sum_1102050102_803_receive = 0 ; ?>
                        @foreach($_1102050102_803 as $row)
                        <td align="center">17</td>
                        <td align="right">1102050102.803</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา เบิกจ่ายตรง อปท.รูปแบบพิเศษ OP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">              
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050102_803_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050102_803_debtor += $row->debtor ; ?>
                        <?php $sum_1102050102_803_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <th class="text-primary" colspan = "8">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ผู้ป่วยใน</th>        
                    </tr>
                    <tr>
                        <?php $sum_1102050101_202_debtor = 0 ; ?>
                        <?php $sum_1102050101_202_receive = 0 ; ?>
                        @foreach($_1102050101_202 as $row)
                        <td align="center">18</td>
                        <td align="right">1102050101.202</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา UC-IP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_202_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_202_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_202_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_217_debtor = 0 ; ?>
                        <?php $sum_1102050101_217_receive = 0 ; ?>
                        @foreach($_1102050101_217 as $row)
                        <td align="center">19</td>
                        <td align="right">1102050101.217</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา UC-IP บริการเฉพาะ (CR)</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_217_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_217_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_217_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_302_debtor = 0 ; ?>
                        <?php $sum_1102050101_302_receive = 0 ; ?>
                        @foreach($_1102050101_302 as $row)
                        <td align="center">20</td>
                        <td align="right">1102050101.302</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา ประกันสังคม IP เครือข่าย</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">              
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_302_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_302_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_302_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_304_debtor = 0 ; ?>
                        <?php $sum_1102050101_304_receive = 0 ; ?>
                        @foreach($_1102050101_304 as $row)
                        <td align="center">21</td>
                        <td align="right">1102050101.304</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา ประกันสังคม IP นอกเครือข่าย สังกัด สป.สธ.</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">              
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_304_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_304_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_304_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_308_debtor = 0 ; ?>
                        <?php $sum_1102050101_308_receive = 0 ; ?>
                        @foreach($_1102050101_308 as $row)
                        <td align="center">22</td>
                        <td align="right">1102050101.308</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา ประกันสังคม 72 ชั่วโมงแรก</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_308_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_308_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_308_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_310_debtor = 0 ; ?>
                        <?php $sum_1102050101_310_receive = 0 ; ?>
                        @foreach($_1102050101_310 as $row)
                        <td align="center">23</td>
                        <td align="right">1102050101.310</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา ประกันสังคม ค่าใช้จ่ายสูง IP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">              
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_310_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_310_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_310_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_402_debtor = 0 ; ?>
                        <?php $sum_1102050101_402_receive = 0 ; ?>
                        @foreach($_1102050101_402 as $row)
                        <td align="center">24</td>
                        <td align="right">1102050101.402</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา-เบิกจ่ายตรง กรมบัญชีกลาง IP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_402_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_402_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_402_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_502_debtor = 0 ; ?>
                        <?php $sum_1102050101_502_receive = 0 ; ?>
                        @foreach($_1102050101_502 as $row)
                        <td align="center">25</td>
                        <td align="right">1102050101.502</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา คนต่างด้าวและแรงงานต่างด้าว IP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">                
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_502_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_502_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_502_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050101_704_debtor = 0 ; ?>
                        <?php $sum_1102050101_704_receive = 0 ; ?>
                        @foreach($_1102050101_704 as $row)
                        <td align="center">26</td>
                        <td align="right">1102050101.704</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา บุคคลที่มีปัญหาสถานะและสิทธิ เบิกจากส่วนกลาง IP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050101_704_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050101_704_debtor += $row->debtor ; ?>
                        <?php $sum_1102050101_704_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050102_107_debtor = 0 ; ?>
                        <?php $sum_1102050102_107_receive = 0 ; ?>
                        @foreach($_1102050102_107 as $row)
                        <td align="center">27</td>
                        <td align="right">1102050102.107</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา ชําระเงิน IP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050102_107_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050102_107_debtor += $row->debtor ; ?>
                        <?php $sum_1102050102_107_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050102_109_debtor = 0 ; ?>
                        <?php $sum_1102050102_109_receive = 0 ; ?>
                        @foreach($_1102050102_109 as $row)
                        <td align="center">28</td>
                        <td align="right">1102050102.109</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา เบิกต้นสังกัด IP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050102_109_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050102_109_debtor += $row->debtor ; ?>
                        <?php $sum_1102050102_109_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050102_603_debtor = 0 ; ?>
                        <?php $sum_1102050102_603_receive = 0 ; ?>
                        @foreach($_1102050102_603 as $row)
                        <td align="center">29</td>
                        <td align="right">1102050102.603</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา พรบ.รถ IP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050102_603_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050102_603_debtor += $row->debtor ; ?>
                        <?php $sum_1102050102_603_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050102_802_debtor = 0 ; ?>
                        <?php $sum_1102050102_802_receive = 0 ; ?>
                        @foreach($_1102050102_802 as $row)
                        <td align="center">30</td>
                        <td align="right">1102050102.802</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา เบิกจ่ายตรง อปท.IP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">               
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050102_802_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050102_802_debtor += $row->debtor ; ?>
                        <?php $sum_1102050102_802_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <?php $sum_1102050102_804_debtor = 0 ; ?>
                        <?php $sum_1102050102_804_receive = 0 ; ?>
                        @foreach($_1102050102_804 as $row)
                        <td align="center">31</td>
                        <td align="right">1102050102.804</td>
                        <td class="text-left">ลูกหนี้ค่ารักษา เบิกจ่ายตรง อปท.รูปแบบพิเศษ IP</td>
                        <td align="right">{{number_format($row->anvn)}}&nbsp;</td>
                        <td align="right" class="text-primary">{{number_format($row->debtor,2)}}&nbsp;</td>
                        <td align="right" @if($row->receive > 0) style="color:green"
                            @elseif($row->receive < 0) style="color:red" @endif>
                            {{number_format($row->receive,2)}}&nbsp;
                        </td> 
                        <td align="right" @if(($row->receive-$row->debtor) > 0) style="color:green"
                            @elseif(($row->receive-$row->debtor) < 0) style="color:red" @endif>
                            {{number_format($row->receive-$row->debtor,2)}}&nbsp;
                        </td>    
                        <td align="center">              
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('hrims/debtor/1102050102_804_daily_pdf')}}" target="_blank">พิมพ์รายวัน</a> 
                        </td>
                        <?php $sum_1102050102_804_debtor += $row->debtor ; ?>
                        <?php $sum_1102050102_804_receive += $row->receive ; ?>
                        @endforeach     
                    </tr>  
                    <tr>
                        <td align="right" colspan = "4"><strong>รวมลูกหนี้ค่ารักษาพยาบาลทั้งสิ้น &nbsp;</strong><br></td>   
                        <td align="right" class="text-primary"><strong>{{number_format($sum_1102050101_103_debtor+$sum_1102050101_109_debtor+$sum_1102050101_201_debtor+$sum_1102050101_203_debtor
                            +$sum_1102050101_209_debtor+$sum_1102050101_216_debtor+$sum_1102050101_301_debtor+$sum_1102050101_303_debtor+$sum_1102050101_307_debtor
                            +$sum_1102050101_309_debtor+$sum_1102050101_401_debtor+$sum_1102050101_501_debtor+$sum_1102050101_703_debtor+$sum_1102050102_106_debtor
                            +$sum_1102050102_108_debtor+$sum_1102050102_602_debtor+$sum_1102050102_801_debtor+$sum_1102050102_803_debtor+$sum_1102050101_202_debtor
                            +$sum_1102050101_217_debtor+$sum_1102050101_302_debtor+$sum_1102050101_304_debtor+$sum_1102050101_308_debtor+$sum_1102050101_310_debtor
                            +$sum_1102050101_402_debtor+$sum_1102050101_502_debtor+$sum_1102050101_704_debtor+$sum_1102050102_107_debtor+$sum_1102050102_109_debtor
                            +$sum_1102050102_603_debtor+$sum_1102050102_802_debtor+$sum_1102050102_804_debtor,2)}}&nbsp;</strong>
                        </td>
                        <td align="right" class="text-success"><strong>{{number_format($sum_1102050101_103_receive+$sum_1102050101_109_receive+$sum_1102050101_201_receive+$sum_1102050101_203_receive
                            +$sum_1102050101_209_receive+$sum_1102050101_216_receive+$sum_1102050101_301_receive+$sum_1102050101_303_receive+$sum_1102050101_307_receive
                            +$sum_1102050101_309_receive+$sum_1102050101_401_receive+$sum_1102050101_501_receive+$sum_1102050101_703_receive+$sum_1102050102_106_receive
                            +$sum_1102050102_108_receive+$sum_1102050102_602_receive+$sum_1102050102_801_receive+$sum_1102050102_803_receive+$sum_1102050101_202_receive
                            +$sum_1102050101_217_receive+$sum_1102050101_302_receive+$sum_1102050101_304_receive+$sum_1102050101_308_receive+$sum_1102050101_310_receive
                            +$sum_1102050101_402_receive+$sum_1102050101_502_receive+$sum_1102050101_704_receive+$sum_1102050102_107_receive+$sum_1102050102_109_receive
                            +$sum_1102050102_603_receive+$sum_1102050102_802_receive+$sum_1102050102_804_receive,2)}}&nbsp;</strong>
                        </td> 
                        <td align="right" style="color:red"><strong>{{number_format(($sum_1102050101_103_receive+$sum_1102050101_109_receive+$sum_1102050101_201_receive+$sum_1102050101_203_receive
                            +$sum_1102050101_209_receive+$sum_1102050101_216_receive+$sum_1102050101_301_receive+$sum_1102050101_303_receive+$sum_1102050101_307_receive
                            +$sum_1102050101_309_receive+$sum_1102050101_401_receive+$sum_1102050101_501_receive+$sum_1102050101_703_receive+$sum_1102050102_106_receive
                            +$sum_1102050102_108_receive+$sum_1102050102_602_receive+$sum_1102050102_801_receive+$sum_1102050102_803_receive+$sum_1102050101_202_receive
                            +$sum_1102050101_217_receive+$sum_1102050101_302_receive+$sum_1102050101_304_receive+$sum_1102050101_308_receive+$sum_1102050101_310_receive
                            +$sum_1102050101_402_receive+$sum_1102050101_502_receive+$sum_1102050101_704_receive+$sum_1102050102_107_receive+$sum_1102050102_109_receive
                            +$sum_1102050102_603_receive+$sum_1102050102_802_receive+$sum_1102050102_804_receive)-($sum_1102050101_103_debtor+$sum_1102050101_109_debtor+$sum_1102050101_201_debtor
                            +$sum_1102050101_203_debtor+$sum_1102050101_209_debtor+$sum_1102050101_216_debtor+$sum_1102050101_301_debtor+$sum_1102050101_303_debtor+$sum_1102050101_307_debtor
                            +$sum_1102050101_309_debtor+$sum_1102050101_401_debtor+$sum_1102050101_501_debtor+$sum_1102050101_703_debtor+$sum_1102050102_106_debtor
                            +$sum_1102050102_108_debtor+$sum_1102050102_602_debtor+$sum_1102050102_801_debtor+$sum_1102050102_803_debtor+$sum_1102050101_202_debtor
                            +$sum_1102050101_217_debtor+$sum_1102050101_302_debtor+$sum_1102050101_304_debtor+$sum_1102050101_308_debtor+$sum_1102050101_310_debtor
                            +$sum_1102050101_402_debtor+$sum_1102050101_502_debtor+$sum_1102050101_704_debtor+$sum_1102050102_107_debtor+$sum_1102050102_109_debtor
                            +$sum_1102050102_603_debtor+$sum_1102050102_802_debtor+$sum_1102050102_804_debtor),2)}}&nbsp;</strong>
                        </td>              
                    </tr>    
                </table>
            </div> 
        </div>
    </div>
@endsection
