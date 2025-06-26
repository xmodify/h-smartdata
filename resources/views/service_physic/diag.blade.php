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
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวน(ครั้ง)ผู้มารับบริการกายภาพบำบัด รายโรคที่สำคัญ ปีงบประมาณ {{$budget_year}} </div>
                <canvas id="diag_mount" style="width: 100%; height: 350px"></canvas>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวน(ครั้ง)ผู้มารับบริการกายภาพบำบัด รายโรคที่สำคัญ 5 ปีงบประมาณย้อนหลัง </div>
                <div id="diag_year" style="width: 100%; height: 350px"></div>
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
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวน(คน)ผู้มารับบริการกายภาพบำบัด รายโรคที่สำคัญ ปีงบประมาณ {{$budget_year}} </div>
                <canvas id="diag_mount_hn" style="width: 100%; height: 350px"></canvas>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวน(คน)ผู้มารับบริการกายภาพบำบัด รายโรคที่สำคัญ 5 ปีงบประมาณย้อนหลัง </div>
                <div id="diag_year_hn" style="width: 100%; height: 350px"></div>
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
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#diag_mount'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($physic_diag_m); ?>,
          datasets: [{
            label: 'Spondylosis',
            data: <?php echo json_encode($physic_diag_m47_m); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)'
            ],
            borderColor: [
                'rgb(255, 99, 132)'
            ],
            borderWidth: 1
          },{
            label: 'Spondylopathies',
            data: <?php echo json_encode($physic_diag_m48_m); ?>,
            backgroundColor: [
                'rgba(255, 159, 64, 0.2)',
            ],
            borderColor: [
                'rgb(255, 159, 64)',
            ],
            borderWidth: 1
          },{
            label: 'Intervertebral Disc Disorders',
            data: <?php echo json_encode($physic_diag_m51_m); ?>,
            backgroundColor: [
                'rgba(255, 205, 86, 0.2)',
            ],
            borderColor: [
                'rgb(255, 205, 86)',
            ],
            borderWidth: 1
          },{
            label: 'Dorsalgia',
            data: <?php echo json_encode($physic_diag_m54_m); ?>,
            backgroundColor: [
                'rgba(75, 192, 192, 0.2)',
            ],
            borderColor: [
                'rgb(75, 192, 192)',
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
<!-- Bar Chart -->
<!-- Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#diag_year"), {

            series: [{
                name: 'Spondylosis',
                data: <?php echo json_encode($physic_diag_m47_y); ?>,
                    },{
                name: 'Spondylopathies',
                data: <?php echo json_encode($physic_diag_m48_y); ?>,
                    },{
                name: 'Intervertebral Disc Disorders',
                data: <?php echo json_encode($physic_diag_m51_y); ?>,
                    },{
                name: 'Dorsalgia',
                data: <?php echo json_encode($physic_diag_m54_y); ?>,
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
            colors: [ '#FF3300','#FF33FF','#FF9900','#00CC00','#0033FF','#9933FF','#9999FF'],
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
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                type: 'text',
                categories: <?php echo json_encode($physic_diag_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->

<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#diag_mount_hn'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($physic_diag_m_hn); ?>,
          datasets: [{
            label: 'Spondylosis',
            data: <?php echo json_encode($physic_diag_m47_m_hn); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)'
            ],
            borderColor: [
                'rgb(255, 99, 132)'
            ],
            borderWidth: 1
          },{
            label: 'Spondylopathies',
            data: <?php echo json_encode($physic_diag_m48_m_hn); ?>,
            backgroundColor: [
                'rgba(255, 159, 64, 0.2)',
            ],
            borderColor: [
                'rgb(255, 159, 64)',
            ],
            borderWidth: 1
          },{
            label: 'Intervertebral Disc Disorders',
            data: <?php echo json_encode($physic_diag_m51_m_hn); ?>,
            backgroundColor: [
                'rgba(255, 205, 86, 0.2)',
            ],
            borderColor: [
                'rgb(255, 205, 86)',
            ],
            borderWidth: 1
          },{
            label: 'Dorsalgia',
            data: <?php echo json_encode($physic_diag_m54_m_hn); ?>,
            backgroundColor: [
                'rgba(75, 192, 192, 0.2)',
            ],
            borderColor: [
                'rgb(75, 192, 192)',
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
<!-- Bar Chart -->
<!-- Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#diag_year_hn"), {

            series: [{
                name: 'Spondylosis',
                data: <?php echo json_encode($physic_diag_m47_y_hn); ?>,
                    },{
                name: 'Spondylopathies',
                data: <?php echo json_encode($physic_diag_m48_y_hn); ?>,
                    },{
                name: 'Intervertebral Disc Disorders',
                data: <?php echo json_encode($physic_diag_m51_y_hn); ?>,
                    },{
                name: 'Dorsalgia',
                data: <?php echo json_encode($physic_diag_m54_y_hn); ?>,
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
            colors: [ '#FF3300','#FF33FF','#FF9900','#00CC00','#0033FF','#9933FF','#9999FF'],
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
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                type: 'text',
                categories: <?php echo json_encode($physic_diag_y_hn); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->

