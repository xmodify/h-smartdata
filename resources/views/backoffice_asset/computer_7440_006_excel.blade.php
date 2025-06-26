<?php
header("Content-Type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename="ทะเบียนครุภัณฑ์จอแสดงภาพคอมพิวเตอร์.xls"');//ชื่อไฟล์
?>
<body>
    <div>
        <strong>ทะเบียนครุภัณฑ์จอแสดงภาพคอมพิวเตอร์</strong>
    </div> 
    <div>  
        <table class="table table-bordered table-striped">
            <thead>
            <tr class="table-secondary">
                <th class="text-center">รหัสกลุ่ม</th>
                <th class="text-center">กลุ่ม</th>                               
                <th class="text-center">รหัสครุภัณฑ์</th>
                <th class="text-center">ชื่อครุภัณฑ์</th>
                <th class="text-center">ยี่ห้อ</th>
                <th class="text-center">รุ่น</th>
                <th class="text-center">คุณลักษณะ</th>    
                <th class="text-center">วันที่รับเข้า</th>   
                <th class="text-center">ราคา</th>   
                <th class="text-center">วิธีได้มา</th>     
                <th class="text-center">งบที่ใช้</th>   
                <th class="text-center">ประจำหน่วยงาน</th> 
                <th class="text-center">ผู้รับผิดชอบ</th>                                                 
            </thead>                          
            @foreach($asset_7440_006 as $row)          
            <tr>                          
                <td align="left">{{ $row->SUP_FSN }}</td>
                <td align="left">{{ $row->SUP_NAME }}</td>                                
                <td align="left">{{ $row->ARTICLE_NUM }}</td>
                <td align="left">{{ $row->ARTICLE_NAME }}</td>
                <td align="left">{{ $row->BRAND_NAME }}</td>
                <td align="left">{{ $row->MODEL_NAME }}</td>
                <td align="left">{{ $row->ARTICLE_PROP }}</td>
                <td align="center">{{ DateThai($row->RECEIVE_DATE) }}</td>
                <td align="right">{{ number_format($row->PRICE_PER_UNIT,2) }}</td>
                <td align="left">{{ $row->METHOD_NAME }}</td>
                <td align="left">{{ $row->BUDGET_NAME }}</td>
                <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                <td align="left">{{ $row->hr_name }}</td>
            @endforeach 
        </table> 
    </div>
</body>
