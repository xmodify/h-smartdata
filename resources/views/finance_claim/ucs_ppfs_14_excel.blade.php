<?php
$files = "บริการคัดกรองและประเมินปัจจัยเสี่ยงต่อสุขภาพกาย/สุขภาพจิต.xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$files); //ชื่อไฟล์
?>

<div class="container-fluid">
    <div class="card border-success">
        <div class="card-header bg-success text-white">บริการคัดกรองและประเมินปัจจัยเสี่ยงต่อสุขภาพกาย/สุขภาพจิต [ส่ง FDH] วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div class="card-body">  
            <div style="overflow-x:auto;">               
                <table id="eclaim_fdh" class="table table-bordered table-striped my-3">
                    <thead>
                    <tr class="table-success">
                        <th class="text-center">ลำดับ</th>
                        <th class="text-center">วันที่รับบริการ</th>
                        <th class="text-center">Queue</th>
                        <th class="text-center">HN</th>
                        <th class="text-center">ชื่อ-สกุล</th>
                        <th class="text-center">อายุ</th>
                        <th class="text-center">สิทธิ</th>
                        <th class="text-center">PDX</th>
                        <th class="text-center">ICD10_Claim</th>
                        <th class="text-center">AuthenCode</th>
                        <th class="text-center">ProjectCode</th>
                        <th class="text-center">ค่ารักษาทั้งหมด</th>  
                        <th class="text-center">รายการเรียกเก็บ</th>                   
                        <th class="text-center">ราคาเรียกเก็บ</th>
                        <th class="text-center">Upload Eclaim</th> 
                        <th class="text-center">Upload FDH</th> 
                        <th class="text-center">ประสงค์เบิก</th> 
                        <th class="text-center">Rep NHSO</th> 
                        <th class="text-center">Error</th> 
                        <th class="text-center">STM ชดเชย</th> 
                        <th class="text-center">ผลต่าง</th> 
                        <th class="text-center">REP</th> 
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    <?php $sum_sum_price = 0 ; ?>  
                    <?php $sum_receive_pp = 0 ; ?>  
                    @foreach($eclaim_fdh as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>
                        <td align="right">{{ DateThai($row->vstdate) }}</td>
                        <td align="center">{{ $row->oqueue }}</td>
                        <td align="center">{{ $row->hn }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->age_y }}</td>
                        <td align="center">{{ $row->pttype }}</td>
                        <td align="right">{{ $row->pdx }}</td>
                        <td align="right">{{ $row->icd10_claim }}</td>
                        <td align="center">{{ $row->auth_code }}</td>
                        <td align="center">{{ $row->project }}</td>                    
                        <td align="center">{{ number_format($row->income,2) }}</td>
                        <td align="left">{{ $row->nondrug }}</td>
                        <td align="center">{{ number_format($row->sum_price,2) }}</td>
                        <td align="center">{{ $row->eclaim }}</td> 
                        <td align="center">{{ $row->fdh }}</td>  
                        <td align="center" @if($row->request_funds == 'Y') style="color:green"
                            @elseif($row->request_funds == 'N') style="color:red" @endif>
                            <strong>{{ $row->request_funds }}</strong></td>     
                        <td align="right">{{ number_format($row->rep_nhso,2) }}</td>
                        <td align="center">{{ $row->rep_error }}</td>
                        <td align="center">{{ number_format($row->receive_pp,2) }}</td>
                        <td align="center">{{ number_format($row->receive_pp-$row->sum_price,2) }}</td>
                        <td align="center">{{ $row->repno }}</td>
                    </tr>
                    <?php $count++; ?>
                    <?php $sum_sum_price += $row->sum_price ; ?>
                    <?php $sum_receive_pp += $row->receive_pp ; ?>
                    @endforeach
                </table>
                <div class="text-center text-primary">
                    <h4>
                        รวมราคาเรียกเก็บทั้งหมด <strong>{{number_format($sum_sum_price,2)}} </strong>บาท |
                        ชดเชยทั้งหมด <strong>{{number_format($sum_receive_pp,2)}} </strong>บาท |
                        ส่วนต่าง <strong>{{number_format($sum_receive_pp-$sum_sum_price,2)}} </strong>บาท
                    </h4>
                </div>
                <br>
            </div>  
        </div>
    </div>
</div>

