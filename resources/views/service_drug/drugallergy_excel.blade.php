<?php
header("Content-Type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename="ข้อมูลการแพ้ยาแยก รพสต..xls"');//ชื่อไฟล์
?>
        
<div><strong>ข้อมูลการแพ้ยาแยก รพสต.</strong></div>      
<table id="drugallergy" class="table table-bordered table-striped my-3">
    <thead>
    <tr class="table-secondary">
        <th class="text-center">ลำดับ</th>
        <th class="text-center">CID</th>  
        <th class="text-center">HN</th>
        <th class="text-center">ชื่อ-สกุล</th> 
        <th class="text-center">วันที่</th>
        <th class="text-center">ชื่อยาที่แพ้</th>
        <th class="text-center">อาการ</th>
        <th class="text-center">ความร้ายแรง</th>
        <th class="text-center">ผลที่เกิดขึ้น</th>
        <th class="text-center">จำนวนรายการ</th>
        <th class="text-center">รพสต.</th>           
    </tr>     
    </thead> 
    <?php $count = 1 ; ?> 
    @foreach($drugallergy as $row)          
    <tr>
        <td align="center">{{ $count }}</td>
        <td align="center">{{ $row->cid }}</td>
        <td align="center">{{ $row->hn }}</td>
        <td align="left">{{ $row->ptname }}</td> 
        <td align="left">{{ DateThai($row->report_date) }}</td> 
        <td align="left">{{ $row->drugallergy }}</td>
        <td align="left">{{ $row->symptom }}</td>
        <td align="left">{{ $row->seiousness_name }}</td>
        <td align="left">{{ $row->result_name }}</td>
        <td align="center">{{ $row->agent_count }}</td>
        <td align="left">{{ $row->pcu }}</td>
    <?php $count++; ?>
    @endforeach  
</table>  
</div>


