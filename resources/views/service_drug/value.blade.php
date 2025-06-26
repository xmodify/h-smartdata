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
        <div class="card-header bg-primary bg-opacity-75 text-white">ต้นทุนการใช้ยา 5 ปีย้อนหลัง</div>
        <div id="drug_cost" style="width: 100%; height: 350px"></div>    
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
        <div class="card-header bg-primary bg-opacity-75 text-white">มูลค่าการใช้ยา 5 ปีย้อนหลัง</div>
        <div id="drug_price" style="width: 100%; height: 350px"></div>    
      </div>
    </div>                                  
  </div>
</div>   
@endsection
<!-- Column Chart Drug_Cost-->
<script>
                document.addEventListener("DOMContentLoaded", () => {
                  new ApexCharts(document.querySelector("#drug_cost"), {
                    series: [{
                      name: 'OPD',
                      data: <?php echo json_encode($value_opd_cost); ?>,
                    }, {
                      name: 'IPD',
                      data: <?php echo json_encode($value_ipd_cost); ?>,
                    }],
                    chart: {
                      type: 'bar',
                      height: 350                      
                    },
                    colors: ['#CC00FF','#009900'],
                    plotOptions: {
                      bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                      },
                    },
                    dataLabels: {
                      enabled: false
                    },
                    stroke: {
                      show: true,
                      width: 2,
                      colors: ['transparent']
                    },
                    xaxis: {
                      categories: <?php echo json_encode($year); ?>,
                    },
                    yaxis: {
                      title: {
                        text: 'ต้นทุนยา'
                      }
                    },
                    fill: {
                      opacity: 1
                    },
                    tooltip: {
                      y: {
                        formatter: function(val) {
                          return  val + " บาท"
                        }
                      }
                    }
                  }).render();
                });
              </script>
<!-- End Column Chart Drug_Price -->
<!-- Column Chart -->
<script>
                document.addEventListener("DOMContentLoaded", () => {
                  new ApexCharts(document.querySelector("#drug_price"), {
                    series: [{
                      name: 'OPD',
                      data: <?php echo json_encode($value_opd_price); ?>,
                    }, {
                      name: 'IPD',
                      data: <?php echo json_encode($value_ipd_price); ?>,
                    }],
                    chart: {
                      type: 'bar',
                      height: 350,                      
                    },
                    colors: ['#FF9900','#FF0000'],
                    plotOptions: {
                      bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                      },
                    },
                    dataLabels: {
                      enabled: false
                    },
                    stroke: {
                      show: true,
                      width: 2,
                      colors: ['transparent']
                    },
                    xaxis: {
                      categories: <?php echo json_encode($year); ?>,
                    },
                    yaxis: {
                      title: {
                        text: 'มูลค่ายา'
                      }
                    },
                    fill: {
                      opacity: 1
                    },
                    tooltip: {
                      y: {
                        formatter: function(val) {
                          return  val + " บาท"
                        }
                      }
                    }
                  }).render();
                });
              </script>
<!-- End Column Chart Drug_Price -->
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
