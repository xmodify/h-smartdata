<?php
$files = "ลูกหนี้รายตัวผังบัญชี-1102050101.303-ลูกหนี้ค่ารักษา ประกันสังคม OP-นอกเครือข่าย สังกัด สป.สธ..xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$files); //ชื่อไฟล์
?>
 
<div>        
    <strong>
        <p align=center>
            แบบรายงานบัญชีลูกหนี้ค่ารักษาพยาบาลแยกแยกรายตัว<br>
            รหัสผังบัญชี 1102050101.303-ลูกหนี้ค่ารักษา ประกันสังคม OP-นอกเครือข่าย สังกัด สป.สธ.<br>
            วันที่ {{dateThaifromFull($start_date)}} ถึง {{dateThaifromFull($end_date)}} <br><br>
        </p>
    </strong>
</div>

<div class="container">
    <div class="row justify-content-center">            
        <table width="100%" >
            <thead>
            <tr>
                <th class="text-center">ลำดับ</th>
                <th class="text-center">วันที่</th>
                <th class="text-center">HN</th>
                <th class="text-center">ชื่อ-สกุล</th>
                <th class="text-center">สิทธิ</th>
                <th class="text-center">ICD10</th>
                <th class="text-center">ค่ารักษาทั้งหมด</th>  
                <th class="text-center">ชำระเอง</th>  
                <th class="text-center">กองทุนอื่น</th>   
                <th class="text-center">ลูกหนี้</th>
                <th class="text-center text-primary">ชดเชย</th> 
                <th class="text-center text-primary">ผลต่าง</th>  
                <th class="text-center text-primary">REP</th>   
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
                <td align="center">{{ $count }}</td>
                <td align="right">{{ DateThai($row->vstdate) }} {{ $row->vsttime }}</td>
                <td align="center">{{ $row->hn }}</td>
                <td align="left">{{ $row->ptname }}</td>
                <td align="right">{{ $row->pttype }} [{{ $row->hospmain }}]</td>
                <td align="right">{{ $row->pdx }}</td>                      
                <td align="right">{{ number_format($row->income,2) }}</td>
                <td align="right">{{ number_format($row->rcpt_money,2) }}</td>
                <td align="right">{{ number_format($row->other,2) }}</td>
                <td align="right">{{ number_format($row->debtor,2) }}</td>  
                <td align="right">{{ number_format($row->receive,2) }}</td>
                <td align="right">{{ number_format($row->receive-$row->debtor,2) }}</td>
                <td align="center">{{ $row->repno }}</td>
            </tr>                
            <?php $count++; ?>
            <?php $sum_income += $row->income ; ?>
            <?php $sum_rcpt_money += $row->rcpt_money ; ?>
            <?php $sum_other += $row->other ; ?>
            <?php $sum_debtor += $row->debtor ; ?> 
            <?php $sum_receive += $row->receive ; ?>   
            @endforeach   
            <tr>
                <td align="right" colspan = "6"><strong>รวมค่ารักษาพยาบาลทั้งสิ้น &nbsp;</strong><br></td> 
                <td align="right"><strong>{{number_format($sum_income,2)}}&nbsp;</strong></td>  
                <td align="right"><strong>{{number_format($sum_rcpt_money,2)}}&nbsp;</strong></td>  
                <td align="right"><strong>{{number_format($sum_other,2)}}&nbsp;</strong></td>  
                <td align="right"><strong>{{number_format($sum_debtor,2)}}&nbsp;</strong></td>
                <td align="right"><strong>{{number_format($sum_receive,2)}}&nbsp;</strong></td> 
                <td align="right"><strong>{{number_format($sum_receive-$sum_debtor,2)}}&nbsp;</strong></td>
            </tr>          
        </table> 
    </div>
</div>    




