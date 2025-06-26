<?php
header("Content-Type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename="Drug_DUE.xls"');//ชื่อไฟล์
?>
        
<div><strong>ข้อมูลการใช้ยา DUE ผู้ป่วยใน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>      
        <table id="due_ipd" class="table table-bordered table-striped my-3">
            <thead>
            <tr class="table-secondary">
                <th class="text-center">วันที่รับบริการ</th>
                <th class="text-center">HN</th>
                <th class="text-center">AN</th>
                <th class="text-center">ชื่อ-สกุล</th>
                <th class="text-center">อายุ</th>
                <th class="text-center">น้ำหนัก</th>
                <th class="text-center">ยา</th>
                <th class="text-center">วันที่สั่ง Lab sCr</th> 
                <th class="text-center">ผล Lab sCr</th>                   
                <th class="text-center">วันที่สั่งยา</th>
                <th class="text-center">วิธีใช้ยา</th>                
            </tr>     
            </thead> 
            <?php $count = 1 ; ?> 
            @foreach($due_ipd as $row)          
            <tr>
                <td align="right">{{ DateThai($row->vstdate) }} เวลา {{ $row->vsttime }}</td>
                <td align="center">{{ $row->hn }}</td>
                <td align="center">{{ $row->an }}</td>
                <td align="left">{{ $row->ptname }}</td>
                <td align="center">{{ $row->age_y }}</td>
                <td align="center">{{ $row->bw }}</td>
                <td align="center">{{ $row->drug }}</td>
                <td align="right">{{ DateThai($row->report_date) }} เวลา {{ $row->report_time }}</td> 
                <td align="right">{{ $row->lab_order_result }}</td>
                <td align="right">{{ DateThai($row->rxdate) }} เวลา {{ $row->rxtime }}</td>
                <td align="right">{{ $row->drugusage }}</td>                 
            </tr>                
            <?php $count++; ?>
            @endforeach  
        </table>    
</div>


