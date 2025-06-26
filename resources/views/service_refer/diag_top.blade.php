@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <!-- row -->
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
<!-- row -->

  <div class="row justify-content-center"> 
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">20 อันดับโรคผู้ป่วยส่งต่อ Refer OPD ปีงบประมาณ {{ $budget_year }}</div>
        <div id="diag_top_opd" style="width: 100%; height: 600px"></div>    
      </div>
    </div> 
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">20 อันดับโรคผู้ป่วยส่งต่อ Refer ER ปีงบประมาณ {{ $budget_year }}</div>
        <div id="diag_top_er" style="width: 100%; height: 600px"></div>    
      </div>
    </div>   
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">20 อันดับโรคผู้ป่วยส่งต่อ Refer IPD ปีงบประมาณ {{ $budget_year }}</div>
        <div id="diag_top_ipd" style="width: 100%; height: 600px"></div>    
      </div>
    </div>    
  </div>
<!-- row -->
</div>
@endsection
<!-- Bar Chart -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#diag_top_opd"), {
      series: [{
        data: <?php echo json_encode($diag_top_opd_sum); ?>
      }],
      chart: {
        type: 'bar',
        height: 600
      },
      plotOptions: {
        bar: {
          borderRadius: 4,
          horizontal: true,
        }
      },
      dataLabels: {
        enabled: false
      },
      xaxis: {
        categories: <?php echo json_encode($diag_top_opd_name); ?>,
      }
    }).render();
  });
</script>
<!-- Column Chart -->
<!-- Bar Chart -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#diag_top_er"), {
      series: [{
        data: <?php echo json_encode($diag_top_er_sum); ?>
      }],
      chart: {
        type: 'bar',
        height: 600
      },
      plotOptions: {
        bar: {
          borderRadius: 4,
          horizontal: true,
        }
      },
      dataLabels: {
        enabled: false
      },
      xaxis: {
        categories: <?php echo json_encode($diag_top_er_name); ?>,
      }
    }).render();
  });
</script>
<!-- Column Chart -->
<!-- Bar Chart -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#diag_top_ipd"), {
      series: [{
        data: <?php echo json_encode($diag_top_ipd_sum); ?>
      }],
      chart: {
        type: 'bar',
        height: 600
      },
      plotOptions: {
        bar: {
          borderRadius: 4,
          horizontal: true,
        }
      },
      dataLabels: {
        enabled: false
      },
      xaxis: {
        categories: <?php echo json_encode($diag_top_ipd_name); ?>,
      }
    }).render();
  });
</script>
<!-- Column Chart -->
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
