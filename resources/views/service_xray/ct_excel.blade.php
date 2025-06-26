<?php
header("Content-Type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename="CT Scan.xls"');//ชื่อไฟล์
?>
        
<div><strong>ข้อมูลผู้ป่วยใช้บริการ CT Scan วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>      
    <table id="list" class="table table-bordered table-striped my-3">
        <thead>
        <tr class="table-success">
            <th class="text-center">ลำดับ</th>
            <th class="text-center">Dep</th>
            <th class="text-center">วันที่</th>
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
            <td align="left">{{ $row->ptname }}</td>
            <td align="center">{{ $row->hn }}</td>
            <td align="center">{{ $row->an }}</td>
            <td align="right">{{ $row->hipdata_code }}-{{ $row->pttype }}</td>
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


