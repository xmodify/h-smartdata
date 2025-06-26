<?php
header("Content-Type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename="มูลค่าการใช้ยาทั้งหมด.xls"');//ชื่อไฟล์
?>
        
<div><strong>ข้อมูลการใช้ยาทั้งหมด</strong></div>  
    <table id="drug" class="table table-bordered table-striped">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">รหัส</th>
            <th class="text-center">ชื่อยา</th>
            <th class="text-center">ชื่อสามัญ</th>                                
            <th class="text-center">ความแรง</th>
            <th class="text-center">หน่วยนับ</th>
            <th class="text-center">ชื่อการค้า</th>   
            <th class="text-center">ราคาทุน</th>   
            <th class="text-center">ราคาขาย</th>   
            <th class="text-center">Dosage Form</th>  
            <th class="text-center">หมวดค่ารักษาพยาบาล</th> 
            <th class="text-center">บัญชี</th>
            <th class="text-center">TMT GP Name</th>        
            <th class="text-center">TMT TP Name</th>  
            <th class="text-center">รหัส TMT สกส.</th>    
            <th class="text-center">ชื่อ TPU สกส.</th>    
            <th class="text-center">TTMT</th>  
            <th class="text-center">สรรพคุณ</th> 
            <th class="text-center">ฉลากช่วย</th>                                                    
        </thead>                          
        @foreach($drug as $row)          
        <tr>                          
            <td align="center">{{ $row->icode }}</td>
            <td align="left">{{ $row->name }}</td>
            <td align="left">{{ $row->generic_name }}</td>
            <td align="left">{{ $row->strength }}</td>
            <td align="left">{{ $row->units }}</td>
            <td align="left">{{ $row->sks_trade_name }}</td>
            <td align="right">{{ number_format($row->unitcost,2) }}</td>
            <td align="right">{{ number_format($row->unitprice,2) }}</td>                                                    
            <td align="left">{{ $row->dosageform }}</td>
            <td align="left">{{ $row->income_name }}</td>
            <td align="left">{{ $row->drugaccount }}</td> 
            <td align="left">{{ $row->gp_name }}</td>
            <td align="left">{{ $row->tp_name }}</td>
            <td align="left">{{ $row->sks_drug_code }}</td>
            <td align="left">{{ $row->sks_trade_name }}</td>
            <td align="left">{{ $row->ttmt_code }}</td>
            <td align="left">{{ $row->therapeutic }}</td>
            <td align="left">{{ $row->hinttext }}</td>
        </tr>      
        @endforeach 
    </table> 


