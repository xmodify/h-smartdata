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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการแผนกอุบัติเหตุ-ฉุกเฉิน ตามความเร่งด่วน 5 ระดับ ปีงบประมาณ {{ $budget_year }}</div>
        <div id="er_type_month" style="width: 100%; height: 350px"></div>    
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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการแผนกอุบัติเหตุ-ฉุกเฉิน ตามความเร่งด่วน 5 ระดับ 5ปีงบประมาณย้อนหลัง</div>
        <div id="er_type_year" style="width: 100%; height: 350px"></div>    
      </div>
    </div>  
  </div>
</div>  
@endsection

<!-- Column Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new ApexCharts(document.querySelector("#er_type_month"), {
        series: [{
          name: 'Resuscitate',
          data: <?php echo json_encode($er_Resuscitate_m); ?>,
        }, {
          name: 'Emergency',
          data: <?php echo json_encode($er_Emergency_m); ?>,
        }, {
          name: 'Urgency',
          data: <?php echo json_encode($er_Urgency_m); ?>,
        }, {
          name: 'Semi_Urgency',
          data: <?php echo json_encode($er_Semi_Urgency_m); ?>,
        }, {
          name: 'Non_Urgency',
          data: <?php echo json_encode($er_Non_Urgency_m); ?>,
        }],
        chart: {
          type: 'bar',
          height: 350,
          background: '#eff7ff'
        },
        colors: ['#FF0000','#FF00FF','#FFCC00','#009900','#FFFFFF'],
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '90%',
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          show: true,
          width: 1,
          colors: ['transparent']
        },
        xaxis: {
          categories:  <?php echo json_encode($er_m); ?>,     
        },
        yaxis: {
          title: {
            text: 'จำนวนครั้ง'
          }
        },
        fill: {
          opacity: 1
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val + " ครั้ง"
            }
          }
        }
      }).render();
    });
</script>
<!-- End Column Chart -->
<!-- Column Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new ApexCharts(document.querySelector("#er_type_year"), {
        series: [{
          name: 'Resuscitate',
          data: <?php echo json_encode($er_Resuscitate_y); ?>,
        }, {
          name: 'Emergency',
          data: <?php echo json_encode($er_Emergency_y); ?>,
        }, {
          name: 'Urgency',
          data: <?php echo json_encode($er_Urgency_y); ?>,
        }, {
          name: 'Semi_Urgency',
          data: <?php echo json_encode($er_Semi_Urgency_y); ?>,
        }, {
          name: 'Non_Urgency',
          data: <?php echo json_encode($er_Non_Urgency_y); ?>,
        }],
        chart: {
          type: 'bar',
          height: 350,
          background: '#fff2f2'
        },
        colors: ['#FF0000','#FF00FF','#FFCC00','#009900','#FFFFFF'],
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '80%',
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          show: true,
          width: 1,
          colors: ['transparent']
        },
        xaxis: {
          categories:  <?php echo json_encode($er_y); ?>,   
        },
        yaxis: {
          title: {
            text: 'จำนวนครั้ง'
          }
        },
        fill: {
          opacity: 1
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val + " ครั้ง"
            }
          }
        }
      }).render();
    });
</script>
<!-- End Column Chart -->
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
