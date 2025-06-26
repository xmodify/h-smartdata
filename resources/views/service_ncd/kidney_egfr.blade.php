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
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยกลุ่ม CKD5RRT ปีงบ {{$budget_year}} จำนวน <strong>{{$ckd5rrt}}</strong> ราย</div>
        <div id="ckd5rrt" style="width: 100%; height: 350px"></div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยกลุ่ม CKD5 ปีงบ {{$budget_year}} จำนวน <strong>{{$ckd5}}</strong> ราย</div>
        <div id="ckd5" style="width: 100%; height: 350px"></div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยกลุ่ม CKD4 ปีงบ {{$budget_year}} จำนวน <strong>{{$ckd4}}</strong> ราย</div>
        <div id="ckd4" style="width: 100%; height: 350px"></div>
      </div>
    </div>
  </div>
</div>
<br>
<!-- row -->
<div class="container-fluid"> 
    <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วย eGFR ปีงบประมาณ {{$budget_year}}</div>
        <div class="card-body">
          <div style="overflow-x:auto;">      
            <table id="egfr_list" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center">ลำดับ</th>
                    <th class="text-center">วันที่</th>
                    <th class="text-center">ประเภท</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">ชื่อ-สกุล</th>
                    <th class="text-center">อาการสำคัญ</th>
                    <th class="text-center">pdx</th>
                    <th class="text-center">รายการ LAB</th>
                    <th class="text-center">ผล LAB</th>
                    <th class="text-center">Stage</th>
                    <th class="text-center">ฟอกไต HD</th>
                </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($egfr_list as $row)
                <tr>
                    <td align="center">{{$count}}</td>
                    <td align="left">{{DateThai($row->report_date)}}</td>
                    <td align="center">{{$row->department}}</td>
                    <td align="center">{{$row->hn}}</td>
                    <td align="left">{{$row->ptname}}</td>
                    <td align="left">{{$row->cc}}</td>
                    <td align="center">{{$row->pdx}}</td>
                    <td align="center">{{$row->lab_items_name}}</td>
                    <td align="center">{{$row->lab_order_result}}</td>
                    <td align="center">{{$row->stage}}</td>
                    <td align="center">{{$row->hd}}</td>
                </tr>
                <?php $count++; ?>
                @endforeach
            </table>
          </div>
        </div>
    </div>
</div>

@endsection
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#egfr_list').DataTable();
    });
</script>

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
<!-- Pie Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new ApexCharts(document.querySelector("#ckd5rrt"), {
        series: [{{$ckd5rrt_hd}}, {{$ckd5rrt_hd_n}}],
        chart: {
          height: 350,
          type: 'pie',
          toolbar: {
            show: true
          }
        },
        labels: ['ฟอกไต HD','ยังไม่ฟอก']
      }).render();
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new ApexCharts(document.querySelector("#ckd5"), {
        series: [{{$ckd5_hd}}, {{$ckd5_hd_n}}],
        chart: {
          height: 350,
          type: 'pie',
          toolbar: {
            show: true
          }
        },
        labels:  ['ฟอกไต HD','ยังไม่ฟอก']
      }).render();
    });
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      new ApexCharts(document.querySelector("#ckd4"), {
        series: [{{$ckd4_hd}}, {{$ckd4_hd_n}}],
        chart: {
          height: 350,
          type: 'pie',
          toolbar: {
            show: true
          }
        },
        labels:  ['ฟอกไต HD','ยังไม่ฟอก']
      }).render();
    });
  </script>