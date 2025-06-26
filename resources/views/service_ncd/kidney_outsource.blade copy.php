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
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนการให้บริการ ปีงบประมาณ {{$budget_year}}</div>
        <canvas id="kidney_count" style="width: 100%; height: 300px"></canvas>           
      </div>      
    </div>
    <div class="col-md-7">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวน(ครั้ง)การให้บริการแยกรายสิทธิ ปีงบประมาณ {{$budget_year}}</div>
        <canvas id="kidney_visit_pttype" style="width: 100%; height: 300px"></canvas>          
      </div>      
    </div>      
  </div>
</div> 
<br>
<div class="container-fluid">
  <div class="row justify-content-center">      
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ค่าฟอกไต ปีงบประมาณ {{$budget_year}}</div>
        <canvas id="kidney_price" style="width: 100%; height: 300px"></canvas>          
      </div>      
    </div>
    <div class="col-md-7">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ค่าฟอกไตแยกรายสิทธิ ปีงบประมาณ {{$budget_year}}</div>
        <canvas id="kidney_price_pttype" style="width: 100%; height: 300px"></canvas>           
      </div>      
    </div>      
  </div>
</div> 
<br>

@endsection
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#kidney_price'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($kidney_month); ?>,
          datasets: [{
            label: 'ค่าฟอก',
            data: <?php echo json_encode($kidney_price); ?>,
            backgroundColor: [
              'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
              'rgb(255, 159, 64)'
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
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#kidney_count'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($kidney_month); ?>,
          datasets: [{
            label: 'คน',
            data: <?php echo json_encode($kidney_hn); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          },
          {
            label: 'ครั้ง',
            data: <?php echo json_encode($kidney_visit); ?>,
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
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#kidney_price_pttype'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($kidney_month); ?>,
          datasets: [{
            label: 'UCS',
            data: <?php echo json_encode($kidney_price_ucs); ?>,
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)'
            ],
            borderColor: [
              'rgb(255, 99, 132)'
            ],
            borderWidth: 1
          },
          {
            label: 'ข้าราชการ',
            data: <?php echo json_encode($kidney_price_ofc); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          }, 
          {
            label: 'อปท.',
            data: <?php echo json_encode($kidney_price_lgo); ?>,
            backgroundColor: [
              'rgba(255, 205, 86, 0.2)'
            ],
            borderColor: [
              'rgb(255, 205, 86)'
            ],
            borderWidth: 1
          }, 
          {
            label: 'อื่น ๆ',
            data: <?php echo json_encode($kidney_price_outher); ?>,
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
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#kidney_visit_pttype'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($kidney_month); ?>,
          datasets: [{
            label: 'UCS',
            data: <?php echo json_encode($kidney_visit_ucs); ?>,
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)'
            ],
            borderColor: [
              'rgb(255, 99, 132)'
            ],
            borderWidth: 1
          },
          {
            label: 'ข้าราชการ',
            data: <?php echo json_encode($kidney_visit_ofc); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          }, 
          {
            label: 'อปท.',
            data: <?php echo json_encode($kidney_visit_lgo); ?>,
            backgroundColor: [
              'rgba(255, 205, 86, 0.2)'
            ],
            borderColor: [
              'rgb(255, 205, 86)'
            ],
            borderWidth: 1
          }, 
          {
            label: 'อื่น ๆ',
            data: <?php echo json_encode($kidney_visit_outher); ?>,
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

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
