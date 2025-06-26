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
    <div class="col-md-7">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการฝังเข็ม ปีงบประมาณ {{$budget_year}} </div>
        <canvas id="service_month" style="width: 100%; height: 350px"></canvas>             
      </div>      
    </div>   
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการฝังเข็ม 5 ปีงบประมาณย้อนหลัง </div>
        <div id="service_year" style="width: 100%; height: 350px"></div>             
      </div>      
    </div>      
  </div>
</div> 
<br>
@endsection
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#service_month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($service_m); ?>,
          datasets: [{
            label: 'ครั้ง',
            data: <?php echo json_encode($service_visit_m); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          },{
            label: 'คน',
            data: <?php echo json_encode($service_hn_m); ?>,
            backgroundColor: [
              'rgba(54, 162, 235, 0.2)'
            ],
            borderColor: [
              'rgb(54, 162, 235)'
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
        new ApexCharts(document.querySelector("#service_year"), {
            
            series: [{
                name: 'ครั้ง',
                data: <?php echo json_encode($service_visit_y); ?>,
                    },
                    {
                name: 'คน',
                data: <?php echo json_encode($service_hn_y); ?>,
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
            colors: [ '#00CC00','#FF6600'],
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
                categories: <?php echo json_encode($service_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
