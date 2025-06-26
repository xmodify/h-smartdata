@extends('layouts.app')

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
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">ทะเบียนผู้ป่วยคลินิกฟอกไต CAPD</div>
                <div id="clinic" style="width: 100%; height: 350px"></div>
            </div>
        </div>
    </div>
</div>
<br>
<!-- row -->
<div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-md-7">
        <div class="card">
          <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยคลินิกคลินิกฟอกไต CAPD รายใหม่ ปีงบประมาณ {{$budget_year}} </div>
          <canvas id="month" style="width: 100%; height: 350px"></canvas>
        </div>
      </div>
      <div class="col-md-5">
        <div class="card">
          <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยคลินิกคลินิกฟอกไต CAPD รายใหม่ 5 ปีงบประมาณย้อนหลัง </div>
          <div id="year" style="width: 100%; height: 350px"></div>
        </div>
      </div>
    </div>
  </div>
  <br>
@endsection
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>

<!-- Pie Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new ApexCharts(document.querySelector("#clinic"), {
        series: <?php echo json_encode($clinic_total); ?>,
        chart: {
          height: 350,
          type: 'pie',
          toolbar: {
            show: true
          }
        },
        labels: <?php echo json_encode($clinic_member_status); ?>,
      }).render();
    });
</script>
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($newcase_m); ?>,
          datasets: [{
            label: 'NewCase',
            data: <?php echo json_encode($newcase_total_m); ?>,
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
        new ApexCharts(document.querySelector("#year"), {

            series: [{
                name: 'NewCase',
                data: <?php echo json_encode($newcase_total_y); ?>,
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
                categories:  <?php echo json_encode($newcase_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
