<?php
header("Content-Type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename="ข้อมูลการใช้ยาช่วงเวลา 00.00-08.00 น..xls"');//ชื่อไฟล์
?>
        
<div><strong>ข้อมูลการใช้ยา DUE ผู้ป่วยใน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>      
    <table id="drugitem_s" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center">วันที่</th>
                    <th class="text-center">เวลา</th>
                    <th class="text-center">จุดที่จ่าย</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">AN</th>
                    <th class="text-center">ชื่อ-สกุลผู้ป่วย</th>
                    <th class="text-center">ชื่อยา</th>
                    <th class="text-center">ผู้สั่ง</th>
                    <th class="text-center">ประเภท</th>
                    <th class="text-center">จำนวน</th> 
                    <th class="text-center">ราคาทุน</th>                   
                    <th class="text-center">ราคาขาย</th>              
                </tr>     
                </thead> 
                <?php $count = 1 ; ?> 
                @foreach($drugtime_s as $row)          
                <tr>
                    <td align="right">{{ DateThai($row->rxdate) }}</td>
                    <td align="right">{{ $row->rxtime }}</td>
                    <td align="right">{{ $row->department }}</td>
                    <td align="center">{{ $row->hn }}</td>
                    <td align="center">{{ $row->an }}</td>
                    <td align="left">{{ $row->ptname }}</td>
                    <td align="right">{{ $row->drug_name }}</td>
                    <td align="left">{{ $row->doctor }}</td>
                    <td align="center">{{ $row->acc }}</td>
                    <td align="center">{{ $row->qty }}</td>
                    <td align="right">{{ $row->sum_cost }}</td>
                    <td align="right">{{ $row->sum_price }}</td>                 
                </tr>                
                <?php $count++; ?>
                @endforeach  
            </table>     
</div>


