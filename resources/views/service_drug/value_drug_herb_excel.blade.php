<?php
header("Content-Type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename="มูลค่าการใช้ยาสมุนไพร.xls"');//ชื่อไฟล์
?>
        
<div><strong>ข้อมูลการใช้ยาสมุนไพร วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>      
<h4 class="text-primary">ผู้ป่วยนอก</h4>
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
<h4 class="text-primary">ผู้ป่วยใน</h4>
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



