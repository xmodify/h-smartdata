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
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยนอก โรคที่สำคัญปีงบประมาณ {{$budget_year}} </div>
                <canvas id="diag_mount" style="width: 100%; height: 350px"></canvas>             
            </div>      
        </div>          
    </div>
</div> 
<br> 
<!-- row -->
<div class="container-fluid">
    <div class="row justify-content-center">          
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยนอก โรคที่สำคัญ 5 ปีงบประมาณย้อนหลัง </div>
                <div id="diag_year" style="width: 100%; height: 350px"></div> 
            </div>      
        </div>    
    </div>
</div>  
@endsection
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#diag_mount'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($diag_m); ?>,
          datasets: [{
            label: 'PNEUMONIA',
            data: <?php echo json_encode($diag_pneumonia_m); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)'
            ],
            borderColor: [
                'rgb(255, 99, 132)'
            ],
            borderWidth: 1
          },{
            label: 'COPD',
            data: <?php echo json_encode($diag_copd_m); ?>,
            backgroundColor: [
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgb(255, 159, 64)'
            ],
            borderWidth: 1
          },{
            label: 'SEPSIS',
            data: <?php echo json_encode($diag_sepsis_m); ?>,
            backgroundColor: [
                'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
                'rgb(75, 192, 192)'
            ],
            borderWidth: 1
          },{
            label: 'SEPTIC SHOCK',
            data: <?php echo json_encode($diag_septic_shock_m); ?>,
            backgroundColor: [
                'rgba(54, 162, 235, 0.2)'
            ],
            borderColor: [
                'rgb(54, 162, 235)'
            ],
            borderWidth: 1
          },{
            label: 'STEMI',
            data: <?php echo json_encode($diag_stemi_m); ?>,
            backgroundColor: [
                'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
                'rgb(153, 102, 255)'
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
        new ApexCharts(document.querySelector("#diag_year"), {
            
            series: [{
                name: 'PNEUMONIA',
                data: <?php echo json_encode($diag_pneumonia_y); ?>,
                    },{
                name: 'COPD',
                data: <?php echo json_encode($diag_copd_y); ?>,
                    },{
                name: 'SEPSIS',
                data: <?php echo json_encode($diag_sepsis_y); ?>,
                    },{
                name: 'SEPTIC SHOCK',
                data: <?php echo json_encode($diag_septic_shock_y); ?>,
                    },{
                name: 'STEMI',
                data: <?php echo json_encode($diag_stemi_y); ?>,
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
            colors: [ '#0033FF','#FF33FF','#FF9900','#FF6600','#00CC00'],
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
                categories: <?php echo json_encode($diag_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->


<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
