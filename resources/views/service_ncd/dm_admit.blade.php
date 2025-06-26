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
    <div class="col-md-7">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยโรคเบาหวาน ที่ Admit ปีงบประมาณ {{$budget_year}} </div>
        <canvas id="admit_month" style="width: 100%; height: 350px"></canvas>
      </div>
    </div>
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยโรคเบาหวาน ที่ Admit 5 ปีงบประมาณย้อนหลัง </div>
        <div id="admit_year" style="width: 100%; height: 350px"></div>
      </div>
    </div>
  </div>
</div>
<br>
<!-- row -->
<div class="container-fluid">
    <div class="card">
      <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วยโรคเบาหวาน ที่ Admit ปีงบประมาณ {{$budget_year}}</div>
      <div class="card-body">
        <table id="admit_dm" class="table table-bordered table-striped my-3">
          <thead>
          <tr class="table-secondary">
              <th class="text-center">ลำดับ</th>
              <th class="text-center">HN</th>
              <th class="text-center">AN</th>
              <th class="text-center">ชื่อ-สกุล</th>
              <th class="text-center">อาการสำคัญ</th>
              <th class="text-center">โรคหลัก</th>
              <th class="text-center">ICD10 DM</th>
              <th class="text-center">วันที่ Admit</th>
              <th class="text-center">วันที่ D/C</th>
              <th class="text-center">ทะเบียนคลินิก</th>
          </tr>
          </thead>
          <?php $count = 1 ; ?>
          @foreach($admit_dm_list as $row)
          <tr>
              <td align="center">{{$count}}</td>
              <td align="center">{{$row->hn}}</td>
              <td align="center">{{$row->an}}</td>
              <td align="left">{{$row->ptname}}</td>
              <td align="left">{{$row->prediag}}</td>
              <td align="left">{{$row->pdx}}</td>
              <td align="center">{{$row->icd10}} Type {{$row->diagtype}}</td>
              <td align="center">{{DateThai($row->regdate)}} เวลา {{$row->regtime}}</td>
              <td align="center">{{DateThai($row->dchdate)}} เวลา {{$row->dchtime}}</td>
              <td align="center">{{$row->clinic}}</td>
          </tr>
          <?php $count++; ?>
          @endforeach
        </table>
      </div>
    </div>
</div>
<br>
@endsection
<!-- Datatable -->
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#admit_dm').DataTable();
    });
</script>
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#admit_month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($admit_m); ?>,
          datasets: [{
            label: 'Admit',
            data: <?php echo json_encode($admit_dm_m); ?>,
            backgroundColor: [
              'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
              'rgb(75, 192, 192)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    });
  </script>
<!-- End Bar CHart -->
<!-- Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#admit_year"), {

            series: [{
                name: 'DM',
                data: <?php echo json_encode($admit_dm_y); ?>,
                    }],

            chart: {
                height: 300,
                type: 'area',
                toolbar: {
                show: false
                },
            },
            markers: {
                size: 4
            },
            colors: [ '#2eca6a', '#ff771d','#9933FF'],
            fill: {
                type: "gradient",
                gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.3,
                opacityTo: 0.4,
                stops: [0, 90, 100]
                }
            },
            dataLabels: {
                enabled: true
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                type: 'text',
                categories:  <?php echo json_encode($admit_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
