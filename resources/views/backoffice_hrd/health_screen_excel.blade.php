<?php
header("Content-Type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename="รายงานการคัดกรองสุขภาพเจ้าหน้าที่.xls"');//ชื่อไฟล์
?>
<body>
    <div>
        <strong>รายงานการคัดกรองสุขภาพเจ้าหน้าที่ วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong>
    </div> 
    <div>  
        <table id="health_screen" class="table table-bordered table-striped my-3">
            <thead>
                <tr class="table-secondary">
                    <th class="text-center">ลำดับ</th> 
                    <th class="text-center">ชื่อ-สกุล</th>                 
                    <th class="text-center">หน่วยงาน</th>  
                    <th class="text-center">เบอร์โทร</th>                     
                    <th class="text-center">วันที่คัดกรอง</th>
                    <th class="text-center">อายุ</th> 
                    <th class="text-center">กรุ๊ปเลือด</th> 
                    <th class="text-center">ส่วนสูง</th>
                    <th class="text-center">น้ำหนัก</th>
                    <th class="text-center">ดัชนีมวลกาย</th>
                    <th class="text-center">อยู่ในเกณท์</th>
                    <th class="text-center">เบาหวาน</th>
                    <th class="text-center">ความดัน</th>
                    <th class="text-center">อุบัติเหตุจากทำงาน</th>
                    <th class="text-center">ติดเชื้อจากทำงาน</th>
                    <th class="text-center">ออกกำลังกาย</th>
                    <th class="text-center">สูบบุหรี่</th>
                    <th class="text-center">ดื่มแอลกอฮอล์</th>
                </tr>
            </thead>
            <?php $count = 1 ; ?>
            @foreach($health_screen as $row)
                <tr>
                    <td align="center">{{ $count }}</td>
                    <td align="left">{{ $row->hrd_name }} </td>                   
                    <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }} </td>
                    <td align="left">{{ $row->HR_PHONE }} </td>
                    <td align="right">{{ DateThai($row->HEALTH_SCREEN_DATE) }}</td> 
                    <td align="right">{{ $row->HEALTH_SCREEN_AGE }} </td>
                    <td align="right">{{ $row->HR_BLOODGROUP_NAME }} </td>
                    <td align="right">{{ $row->HEALTH_SCREEN_HEIGHT }}</td>
                    <td align="right">{{ $row->HEALTH_SCREEN_WEIGHT }}</td>
                    <td align="right">{{ $row->HEALTH_SCREEN_BODY }}</td>
                    <td align="left">{{ $row->bmi }}</td>
                    <td align="left">{{ $row->dm }}</td>
                    <td align="left">{{ $row->ht }}</td>
                    <td align="left">{{ $row->accident }} {{ $row->accident_comment }}</td>
                    <td align="left">{{ $row->infect }} {{ $row->infect_comment }}</td>
                    <td align="left">{{ $row->exer }}</td>
                    <td align="left">{{ $row->smok }}</td>
                    <td align="left">{{ $row->drink }}</td>
                </tr>
            <?php $count++; ?>
            @endforeach
        </table>
    </div>
</body>
