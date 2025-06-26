<?php
header("Content-Type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename="ครุภัณฑ์ไฟฟ้าและวิทยุ เครื่องกำเนิดไฟฟ้า.xls"');//ชื่อไฟล์
?>

<div class="container-fluid">
    <div class="card">
    <div class="card-header bg-primary text-white"><strong>ครุภัณฑ์ไฟฟ้าและวิทยุ เครื่องกำเนิดไฟฟ้า สถานะใช้งานปกติ</strong></div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">    
                        <table id="office" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">ลำดับ</th>   
                                <th class="text-center">ชื่อครุภัณฑ์</th>
                                <th class="text-center">รหัสครุภัณฑ์</th>
                                <th class="text-center">รหัสทรัพย์สิน</th>
                                <th class="text-center">วันที่ได้มา</th>
                                <th class="text-center">แหล่งเงิน</th>   
                                <th class="text-center">วิธีได้มา</th>    
                                <th class="text-center">ราคาทรัพย์สิน</th>   
                                <th class="text-center">ประจำหน่วยงาน</th> 
                                <th class="text-center">อายุการใช้งาน</th>                                                   
                            </thead>   
                            <?php $count = 1 ; ?>                                              
                            @foreach($asset as $row)  
                            <?php $diff = abs(strtotime(date('Y-m-d')) - strtotime($row->RECEIVE_DATE));  ?> 
                            <?php $years = floor($diff / (365*60*60*24));  ?> 
                            <?php $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));  ?> 
                            <?php $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));  ?> 
                            <tr>                          
                                <td align="left">{{ $count }}</td>      
                                <td align="left">{{ $row->ARTICLE_NAME }}</td>
                                <td align="left">{{ $row->ARTICLE_NUM }}</td>
                                <td align="left">{{ $row->SUP_FSN }}</td>
                                <td align="center">{{ DateThai($row->RECEIVE_DATE) }}</td>
                                <td align="left">{{ $row->BUDGET_NAME }}</td>
                                <td align="left">{{ $row->BUY_NAME }}</td>
                                <td align="right">{{ number_format($row->PRICE_PER_UNIT,2) }}</td>   
                                <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                                <td align="left">{{$years}} ปี {{$months}} เดือน {{$days}} วัน</td>
                                <?php $count++; ?>
                            @endforeach 
                        </table> 
                    </div> 
                </div>        
            </div>
        </div>
    </div>
</div>


