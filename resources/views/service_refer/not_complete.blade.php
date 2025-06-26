@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-12">
        <form method="POST" enctype="multipart/form-data">
        @csrf
          <div class="row">
              <div class="col-md-9" align="left"></div>
              <div class="col-md-2" align="right">
                  <select class="form-select my-1" name="budget_year">
                  @foreach ($budget_year_select as $row)
                  <option value="{{$row->LEAVE_YEAR_ID}}" @if ($budget_year == "$row->LEAVE_YEAR_ID") selected="selected"  @endif>{{$row->LEAVE_YEAR_NAME}}</option>
                  @endforeach
                  </select>
              </div>
              <div class="col-md-1" align="right">
                  <button type="submit" class="btn btn-primary my-1 ">{{ __('ค้นหา') }}</button>
              </div>
          </div>
        </form>
    </div>
  </div>
</div>

<!-- row -->
<div class="container-fluid">
  <div class="card">
    <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วยส่งต่อ Refer บันทึกข้อมูลไม่ครบถ้วน ปีงบประมาณ {{$budget_year}} </div>
    <div class="card-body">
      <table id="not_complete" class="table table-bordered table-striped my-3">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">ลำดับ</th>
            <th class="text-center">วันที่ Refer</th>
            <th class="text-center">HN</th>
            <th class="text-center">AN</th>
            <th class="text-center">ชื่อ-สกุล</th>
            <th class="text-center">ประเภท</th>
            <th class="text-center">จุดส่งต่อ</th>
            <th class="text-center">PDX ที่มา</th>
            <th class="text-center">PDX Refer</th>
            <th class="text-center">สถานพยาบาล</th>
        </tr>
        </thead>
        <?php $count = 1 ; ?>
        @foreach($not_complete as $row)
        <tr>
            <td align="center">{{ $count }}</td>
            <td align="right">{{ DateThai($row->refer_date) }}</td>
            <td align="center">{{ $row->hn }}</td>
            <td align="center">{{ $row->an }}</td>
            <td align="left">{{ $row->ptname }}</td>
            <td align="center">{{ $row->department }}</td>
            <td align="center">{{ $row->refer_point }}</td>
            <td align="center">{{ $row->pdx }}</td>
            <td align="center">{{ $row->pdx_refer }}</td>
            <td align="right">{{ $row->refer_hos }}</td>
        </tr>
        <?php $count++; ?>
        @endforeach
      </table>
    </div>
  </div>
</div>
@endsection

<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#not_complete').DataTable();
    });
</script>

