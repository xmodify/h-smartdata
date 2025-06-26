<?php
header("Content-Type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename="Drug_DMHT.xls"');//ชื่อไฟล์
?>

<div><strong>ข้อมูลการใช้ยา DM ผู้ป่วยนอก วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>
    <table class="table table-bordered table-striped">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">สิทธิการรักษา</th>
            <th class="text-center">รหัสยา</th>
            <th class="text-center">ชื่อยา</th>
            <th class="text-center">ชื่อยาสามัญ</th>
            <th class="text-center">ความแรง</th>
            <th class="text-center">จำนวน HN</th>
            <th class="text-center">จำนวน VISIT</th>
            <th class="text-center">จำนวนยา</th>
            <th class="text-center">ต้นทุน</th>
            <th class="text-center">มูลค่า</th>
        </tr>
        </thead>
        <?php $count = 1 ; ?>
        <?php $sum_hn = 0 ; ?>
        <?php $sum_visit = 0 ; ?>
        <?php $sum_qty = 0 ; ?>
        <?php $sum_cost = 0 ; ?>
        <?php $sum_price = 0 ; ?>
        @foreach($dm_opd as $row)
        <tr>
            <td align="center">{{ $row->hipdata_code }}</td>
            <td align="center">{{ $row->icode }}</td>
            <td align="left">{{ $row->name }}</td>
            <td align="center">{{ $row->generic_name }}</td>
            <td align="center">{{ $row->strength }}</td>
            <td align="right">{{ $row->hn }}</td>
            <td align="right">{{ $row->visit }}</td>
            <td align="right">{{ $row->qty }}</td>
            <td align="right">{{ number_format($row->cost,2) }}</td>
            <td align="right">{{ number_format($row->price,2) }}</td>
        </tr>
        <?php $count++; ?>
        <?php $sum_hn += $row->hn ; ?>
        <?php $sum_visit += $row->visit ; ?>
        <?php $sum_qty += $row->qty ; ?>
        <?php $sum_cost += $row->cost ; ?>
        <?php $sum_price += $row->price ; ?>
        @endforeach
        <tr>
            <td colspan= "5" align="right"><strong>รวม </strong></td>
            <td align="right"><strong>{{ number_format($sum_hn) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_visit) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_qty) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_cost,2) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_price,2) }}</strong></td>
        </tr>
    </table>
</div>
<br>
<div><strong>ข้อมูลการใช้ยา DM ผู้ป่วยใน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>
    <table class="table table-bordered table-striped">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">สิทธิการรักษา</th>
            <th class="text-center">รหัสยา</th>
            <th class="text-center">ชื่อยา</th>
            <th class="text-center">ชื่อยาสามัญ</th>
            <th class="text-center">ความแรง</th>
            <th class="text-center">จำนวน HN</th>
            <th class="text-center">จำนวน AN</th>
            <th class="text-center">จำนวนยา</th>
            <th class="text-center">ต้นทุน</th>
            <th class="text-center">มูลค่า</th>
        </tr>
        </thead>
        <?php $count = 1 ; ?>
        <?php $sum_hn = 0 ; ?>
        <?php $sum_an = 0 ; ?>
        <?php $sum_qty = 0 ; ?>
        <?php $sum_cost = 0 ; ?>
        <?php $sum_price = 0 ; ?>
        @foreach($dm_ipd as $row)
        <tr>
            <td align="center">{{ $row->hipdata_code }}</td>
            <td align="center">{{ $row->icode }}</td>
            <td align="left">{{ $row->name }}</td>
            <td align="center">{{ $row->generic_name }}</td>
            <td align="center">{{ $row->strength }}</td>
            <td align="right">{{ $row->hn }}</td>
            <td align="right">{{ $row->an }}</td>
            <td align="right">{{ $row->qty }}</td>
            <td align="right">{{ number_format($row->cost,2) }}</td>
            <td align="right">{{ number_format($row->price,2) }}</td>
        </tr>
        <?php $count++; ?>
        <?php $sum_hn += $row->hn ; ?>
        <?php $sum_an += $row->an ; ?>
        <?php $sum_qty += $row->qty ; ?>
        <?php $sum_cost += $row->cost ; ?>
        <?php $sum_price += $row->price ; ?>
        @endforeach
        <tr>
            <td colspan= "5" align="right"><strong>รวม </strong></td>
            <td align="right"><strong>{{ number_format($sum_hn) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_an) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_qty) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_cost,2) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_price,2) }}</strong></td>
        </tr>
    </table>
</div>
<div><strong>ข้อมูลการใช้ยา HT ผู้ป่วยนอก วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>
    <table class="table table-bordered table-striped">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">สิทธิการรักษา</th>
            <th class="text-center">รหัสยา</th>
            <th class="text-center">ชื่อยา</th>
            <th class="text-center">ชื่อยาสามัญ</th>
            <th class="text-center">ความแรง</th>
            <th class="text-center">จำนวน HN</th>
            <th class="text-center">จำนวน VISIT</th>
            <th class="text-center">จำนวนยา</th>
            <th class="text-center">ต้นทุน</th>
            <th class="text-center">มูลค่า</th>
        </tr>
        </thead>
        <?php $count = 1 ; ?>
        <?php $sum_hn = 0 ; ?>
        <?php $sum_visit = 0 ; ?>
        <?php $sum_qty = 0 ; ?>
        <?php $sum_cost = 0 ; ?>
        <?php $sum_price = 0 ; ?>
        @foreach($ht_opd as $row)
        <tr>
            <td align="center">{{ $row->hipdata_code }}</td>
            <td align="center">{{ $row->icode }}</td>
            <td align="left">{{ $row->name }}</td>
            <td align="center">{{ $row->generic_name }}</td>
            <td align="center">{{ $row->strength }}</td>
            <td align="right">{{ $row->hn }}</td>
            <td align="right">{{ $row->visit }}</td>
            <td align="right">{{ $row->qty }}</td>
            <td align="right">{{ number_format($row->cost,2) }}</td>
            <td align="right">{{ number_format($row->price,2) }}</td>
        </tr>
        <?php $count++; ?>
        <?php $sum_hn += $row->hn ; ?>
        <?php $sum_visit += $row->visit ; ?>
        <?php $sum_qty += $row->qty ; ?>
        <?php $sum_cost += $row->cost ; ?>
        <?php $sum_price += $row->price ; ?>
        @endforeach
        <tr>
            <td colspan= "5" align="right"><strong>รวม </strong></td>
            <td align="right"><strong>{{ number_format($sum_hn) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_visit) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_qty) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_cost,2) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_price,2) }}</strong></td>
        </tr>
    </table>
</div>
<br>
<div><strong>ข้อมูลการใช้ยา HT ผู้ป่วยใน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>
    <table class="table table-bordered table-striped">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">สิทธิการรักษา</th>
            <th class="text-center">รหัสยา</th>
            <th class="text-center">ชื่อยา</th>
            <th class="text-center">ชื่อยาสามัญ</th>
            <th class="text-center">ความแรง</th>
            <th class="text-center">จำนวน HN</th>
            <th class="text-center">จำนวน AN</th>
            <th class="text-center">จำนวนยา</th>
            <th class="text-center">ต้นทุน</th>
            <th class="text-center">มูลค่า</th>
        </tr>
        </thead>
        <?php $count = 1 ; ?>
        <?php $sum_hn = 0 ; ?>
        <?php $sum_an = 0 ; ?>
        <?php $sum_qty = 0 ; ?>
        <?php $sum_cost = 0 ; ?>
        <?php $sum_price = 0 ; ?>
        @foreach($ht_ipd as $row)
        <tr>
            <td align="center">{{ $row->hipdata_code }}</td>
            <td align="center">{{ $row->icode }}</td>
            <td align="left">{{ $row->name }}</td>
            <td align="center">{{ $row->generic_name }}</td>
            <td align="center">{{ $row->strength }}</td>
            <td align="right">{{ $row->hn }}</td>
            <td align="right">{{ $row->an }}</td>
            <td align="right">{{ $row->qty }}</td>
            <td align="right">{{ number_format($row->cost,2) }}</td>
            <td align="right">{{ number_format($row->price,2) }}</td>
        </tr>
        <?php $count++; ?>
        <?php $sum_hn += $row->hn ; ?>
        <?php $sum_an += $row->an ; ?>
        <?php $sum_qty += $row->qty ; ?>
        <?php $sum_cost += $row->cost ; ?>
        <?php $sum_price += $row->price ; ?>
        @endforeach
        <tr>
            <td colspan= "5" align="right"><strong>รวม </strong></td>
            <td align="right"><strong>{{ number_format($sum_hn) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_an) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_qty) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_cost,2) }}</strong></td>
            <td align="right"><strong>{{ number_format($sum_price,2) }}</strong></td>
        </tr>
    </table>
</div>




