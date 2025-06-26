@extends('layouts.app')
<style>
  table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
  border: 1px solid #ddd;
  }
  th, td {
  padding: 8px;
  }  
</style>
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
        <div class="card-header bg-primary text-white">จำนวนผู้มารับบริการแพทย์แผนไทย ปีงบประมาณ {{$budget_year}} </div>
        <canvas id="healthmed_month" style="width: 100%; height: 350px"></canvas>             
      </div>      
    </div>   
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary text-white">จำนวนผู้มารับบริการแพทย์แผนไทย 5 ปีงบประมาณย้อนหลัง </div>
        <div id="healthmed_year" style="width: 100%; height: 350px"></div>             
      </div>      
    </div>      
  </div>
</div> 
<br>
<!--row-->
<div class="container-fluid">
  <div class="row justify-content-center">  
    <div class="col-md-12">
      <div class="card">
        <div class="card-header bg-primary text-white">จำนวนครั้งผู้มารับบริการแพทย์แผนไทยแยกรายสิทธิ ปีงบประมาณ {{$budget_year}} </div>
        <div class="card-body"> 
          <div style="overflow-x:auto;">   
            <table class="table table-bordered table-striped">
                <tr class="table-success">
                    <td align="center"><b>เดือน</b></td> 
                    <td align="center"><b>รวม</b></td> 
                    <td align="center"><b>ประกันสุขภาพ</b></td> 
                    <td align="center"><b>ข้าราชการ</b></td> 
                    <td align="center"><b>ประกันสังคม</b></td> 
                    <td align="center"><b>อปท.</b></td> 
                    <td align="center"><b>ต่างด้าว</b></td> 
                    <td align="center"><b>Stateless</b></td> 
                    <td align="center"><b>ชำระเงิน/พรบ.</b></td> 
                </tr>               
                <?php $sum_visit = 0 ; ?>  
                <?php $sum_ucs = 0 ; ?>   
                <?php $sum_ofc = 0 ; ?>   
                <?php $sum_sss = 0 ; ?>   
                <?php $sum_lgo = 0 ; ?>   
                <?php $sum_fss = 0 ; ?>   
                <?php $sum_stp = 0 ; ?>   
                <?php $sum_pay = 0 ; ?>    
                @foreach($healthmed_month as $row)   
                <tr>
                    <td align="left">{{($row->month)}}</td> 
                    <td align="center">{{number_format($row->visit)}}</td>
                    <td align="center">{{number_format($row->ucs)}}</td>  
                    <td align="center">{{number_format($row->ofc)}}</td> 
                    <td align="center">{{number_format($row->sss)}}</td>  
                    <td align="center">{{number_format($row->lgo)}}</td> 
                    <td align="center">{{number_format($row->fss)}}</td> 
                    <td align="center">{{number_format($row->stp)}}</td>     
                    <td align="center">{{number_format($row->pay)}}</td>  
                </tr>
                <?php $sum_visit += $row->visit ; ?>
                <?php $sum_ucs += $row->ucs ; ?>
                <?php $sum_ofc += $row->ofc ; ?>
                <?php $sum_sss += $row->sss ; ?>
                <?php $sum_lgo += $row->lgo ; ?>
                <?php $sum_fss += $row->fss ; ?>
                <?php $sum_stp += $row->stp ; ?>
                <?php $sum_pay += $row->pay ; ?>
                @endforeach 
                <tr>     
                  <td align="right"><strong>รวม</strong></td>
                  <td align="center"><strong>{{ number_format($sum_visit) }}</strong></td>
                  <td align="center"><strong>{{ number_format($sum_ucs) }}</strong></td>
                  <td align="center"><strong>{{ number_format($sum_ofc) }}</strong></td>
                  <td align="center"><strong>{{ number_format($sum_sss) }}</strong></td>
                  <td align="center"><strong>{{ number_format($sum_lgo) }}</strong></td>
                  <td align="center"><strong>{{ number_format($sum_fss) }}</strong></td>
                  <td align="center"><strong>{{ number_format($sum_stp) }}</strong></td>
                  <td align="center"><strong>{{ number_format($sum_pay) }}</strong></td>
              </tr>
            </table>   
          </div> 
        </div>       
      </div>      
    </div>
  </div>
</div> <!--row-->
<br>
<!--row-->
<div class="container-fluid">
  <div class="row justify-content-center">  
    <div class="col-md-12">
      <div class="card">
        <div class="card-header bg-primary text-white">จำนวนการทำหัตถการแพทย์แผนไทยแยกรายสิทธิ ปีงบประมาณ {{$budget_year}} </div>
        <div class="card-body"> 
          <div style="overflow-x:auto;">   
            <table class="table table-bordered table-striped">
                <tr class="table-success">
                    <td align="center"><b>หัตถการ</b></td> 
                    <td align="center"><b>รวม</b></td> 
                    <td align="center"><b>ประกันสุขภาพ</b></td> 
                    <td align="center"><b>ข้าราชการ</b></td> 
                    <td align="center"><b>ประกันสังคม</b></td> 
                    <td align="center"><b>อปท.</b></td> 
                    <td align="center"><b>ต่างด้าว</b></td> 
                    <td align="center"><b>Stateless</b></td> 
                    <td align="center"><b>ชำระเงิน/พรบ.</b></td> 
                </tr>                  
                @foreach($healthmed_operation as $row)   
                <tr>
                    <td align="left">{{($row->health_med_operation_item_name)}}</td> 
                    <td align="center">{{number_format($row->total)}}</td>
                    <td align="center">{{number_format($row->ucs)}}</td>  
                    <td align="center">{{number_format($row->ofc)}}</td> 
                    <td align="center">{{number_format($row->sss)}}</td>  
                    <td align="center">{{number_format($row->lgo)}}</td> 
                    <td align="center">{{number_format($row->fss)}}</td> 
                    <td align="center">{{number_format($row->stp)}}</td>     
                    <td align="center">{{number_format($row->pay)}}</td>  
                </tr>
                @endforeach 
            </table>   
          </div> 
        </div>       
      </div>      
    </div>
  </div>
</div><!--row-->
<br>
@endsection
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#healthmed_month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($healthmed_m); ?>,
          datasets: [{
            label: 'ครั้ง',
            data: <?php echo json_encode($healthmed_visit_m); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          },{
            label: 'คน',
            data: <?php echo json_encode($healthmed_hn_m); ?>,
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
        new ApexCharts(document.querySelector("#healthmed_year"), {
            
            series: [{
                name: 'ครั้ง',
                data: <?php echo json_encode($healthmed_visit_y); ?>,
                    },
                    {
                name: 'คน',
                data: <?php echo json_encode($healthmed_hn_y); ?>,
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
                categories: <?php echo json_encode($healthmed_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
