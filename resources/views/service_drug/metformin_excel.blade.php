<?php
header("Content-Type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename="Drug_Metformin.xls"');//ชื่อไฟล์
?>
        
<div><strong>ข้อมูลการใช้ยา Metformin วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>      
<h4 class="text-primary">ผู้ป่วยนอก</h4>
<table id="drug" class="table table-bordered table-striped my-3">
    <thead>
    <tr class="table-primary">
        <th class="text-center">ชื่อยา</th>
        <th class="text-center">ชื่อ-สกุล</th>
        <th class="text-center">อายุ</th>
        <th class="text-center">HN</th>
        <th class="text-center">วันที่สั่งยา</th> 
        <th class="text-center">วิธีใช้</th> 
        <th class="text-center">วันที่รายงาน Lab</th> 
        <th class="text-center">ชื่อ Lab</th>   
        <th class="text-center">ผล Lab</th>   
    </tr>     
    </thead> 
    <?php $count = 1 ; ?> 
    @foreach($drug as $row)          
    <tr>
        <td align="right">{{ $row->drug }}</td> 
        <td align="left">{{ $row->ptname }}</td>
        <td align="center">{{ $row->age_y }}</td>
        <td align="center">{{ $row->hn }}</td>
        <td align="right">{{ DateThai($row->rxdate) }} เวลา {{ $row->rxtime }}</td>
        <td align="right">{{ $row->drugusage }}</td>
        <td align="right">{{ DateThai($row->report_date) }} เวลา {{ $row->report_time }}</td> 
        <td align="right">{{ $row->lab_items_name_ref }}</td>
        <td align="right">{{ $row->lab_order_result }}</td>                                    
    </tr>                
    <?php $count++; ?>
    @endforeach  
</table>
<h4 class="text-primary">ผู้ป่วยใน</h4>
<table id="drug_ipd" class="table table-bordered table-striped my-3">
    <thead>
    <tr class="table-primary">
        <th class="text-center">ชื่อยา</th>
        <th class="text-center">ชื่อ-สกุล</th>
        <th class="text-center">อายุ</th>
        <th class="text-center">HN</th>
        <th class="text-center">AN</th>
        <th class="text-center">วันที่สั่งยา</th>
        <th class="text-center">วิธีใช้</th> 
        <th class="text-center">วันที่รายงาน Lab</th> 
        <th class="text-center">ชื่อ Lab</th>   
        <th class="text-center">ผล Lab</th>     
    </tr>     
    </thead> 
    <?php $count = 1 ; ?> 
    @foreach($drug_ipd as $row)          
    <tr>
        <td align="right">{{ $row->drug }}</td> 
        <td align="left">{{ $row->ptname }}</td>
        <td align="center">{{ $row->age_y }}</td>
        <td align="center">{{ $row->hn }}</td>
        <td align="center">{{ $row->an }}</td>
        <td align="right">{{ DateThai($row->rxdate) }} เวลา {{ $row->rxtime }}</td>
        <td align="right">{{ $row->drugusage }}</td>
        <td align="right">{{ DateThai($row->report_date) }} เวลา {{ $row->report_time }}</td> 
        <td align="right">{{ $row->lab_items_name_ref }}</td>
        <td align="right">{{ $row->lab_order_result }}</td>                                    
    </tr>                
    <?php $count++; ?>
    @endforeach  
</table>



