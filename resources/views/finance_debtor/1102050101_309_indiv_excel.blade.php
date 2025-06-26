<?php
$files = "ลูกหนี้รายตัวผังบัญชี-1102050101.309-ลูกหนี้ค่ารักษา ประกันสังคม-ค่าใช้จ่ายสูง/อุบัติเหตุ/ฉุกเฉิน OP.xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$files); //ชื่อไฟล์
?>
 
<div>        
    <strong>
        <p align=center>
            แบบรายงานบัญชีลูกหนี้ค่ารักษาพยาบาลแยกแยกรายตัว<br>
            รหัสผังบัญชี 1102050101.309-ลูกหนี้ค่ารักษา ประกันสังคม-ค่าใช้จ่ายสูง/อุบัติเหตุ/ฉุกเฉิน OP<br>
            วันที่ {{dateThaifromFull($start_date)}} ถึง {{dateThaifromFull($end_date)}} <br><br>
        </p>
    </strong>
</div>

<div class="container">
    <div class="row justify-content-center">            
        <table id="debtor" class="table table-bordered table-striped my-3">
            <thead>
            <tr class="table-success">
                <th class="text-center">วันที่</th>
                <th class="text-center">HN</th>
                <th class="text-center">ชื่อ-สกุล</th>
                <th class="text-center">สิทธิ</th>
                <th class="text-center">ICD10</th>
                <th class="text-center">ค่ารักษาทั้งหมด</th>  
                <th class="text-center">ชำระเอง</th>    
                <th class="text-center">ฟอกไต</th> 
                <th class="text-center text-primary">ลูกหนี้</th>
                <th class="text-center text-primary">ชดเชย</th> 
                <th class="text-center text-primary">ผลต่าง</th>  
                <th class="text-center text-primary">REP</th>   
            </tr>
            </thead>
            <?php $count = 1 ; ?>
            <?php $sum_income = 0 ; ?>
            <?php $sum_rcpt_money = 0 ; ?>
            <?php $sum_kidney = 0 ; ?>
            <?php $sum_debtor = 0 ; ?>
            <?php $sum_receive = 0 ; ?>
            @foreach($debtor as $row)
            <tr>
                <td align="right">{{ DateThai($row->vstdate) }} {{ $row->vsttime }}</td>
                <td align="center">{{ $row->hn }}</td>
                <td align="left">{{ $row->ptname }}</td>
                <td align="right">{{ $row->pttype }}</td>
                <td align="right">{{ $row->pdx }}</td>                      
                <td align="right">{{ number_format($row->income,2) }}</td>
                <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                <td align="right">{{ number_format($row->kidney,2) }}</td>
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
            <?php $count++; ?>
            <?php $sum_income += $row->income ; ?>
            <?php $sum_rcpt_money += $row->rcpt_money ; ?>
            <?php $sum_kidney += $row->kidney ; ?>
            <?php $sum_debtor += $row->debtor ; ?> 
            <?php $sum_receive += $row->receive ; ?>       
            @endforeach 
             </tr>   
            <tr>
                <td align="right" colspan = "5"><strong>รวมค่ารักษาพยาบาลทั้งสิ้น &nbsp;</strong><br></td> 
                <td class="text-primary" align="right">{{ number_format($sum_income,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_rcpt_money,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_kidney,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_debtor,2)}}</td>
                <td class="text-primary" align="right">{{ number_format($sum_receive,2)}}</td>
            </tr>    
               </table>        
    </div>
</div>    




