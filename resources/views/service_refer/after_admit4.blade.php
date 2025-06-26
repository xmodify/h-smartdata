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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยส่งต่อ Refer ภายใน 4 ชม. หลังadmit ปีงบประมาณ {{$budget_year}} </div>
        <canvas id="after_admit4_month" style="width: 100%; height: 350px"></canvas>             
      </div>      
    </div> 
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยส่งต่อ Refer ภายใน 4 ชม. หลังadmit Refer 5 ปีงบประมาณย้อนหลัง </div>
        <div id="after_admit4_year" style="width: 100%; height: 350px"></div>             
      </div>      
    </div>         
  </div>
</div>  
<br>
<!-- row -->
<div class="container-fluid">   
  <div class="card">
    <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วยส่งต่อ Refer ภายใน 4 ชม. หลังadmit ปีงบประมาณ {{$budget_year}} </div>
    <div class="card-body">
      <table id="after_admit4" class="table table-bordered table-striped my-3">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">ลำดับ</th>
            <th class="text-center">HN</th>
            <th class="text-center">ชื่อ-สกุล</th>   
            <th class="text-center">วันที่ Admit</th> 
            <th class="text-center">เวลา Admit</th>
            <th class="text-center">Admit PDX</th>
            <th class="text-center">วันที่ Refer</th> 
            <th class="text-center">เวลา Refer</th>
            <th class="text-center">Refer PDX</th>
            <th class="text-center">สถานพยาบาล</th>
            <th class="text-center">ชั่วโมง Admit</th>
        </tr>     
        </thead> 
        <?php $count = 1 ; ?> 
        @foreach($after_admit4 as $row)          
        <tr>
            <td align="center">{{ $count }}</td> 
            <td align="center">{{ $row->hn }}</td>
            <td align="left">{{ $row->ptname }}</td>
            <td align="right">{{ DateThai($row->regdate) }}</td>
            <td align="center">{{ $row->regtime }}</td>
            <td align="center">{{ $row->admit_pdx }}</td>
            <td align="right">{{ DateThai($row->dchdate) }}</td>
            <td align="center">{{ $row->dchtime }}</td>
            <td align="center">{{ $row->refer_pdx }}</td>
            <td align="right">{{ $row->refer_hos }}</td>
            <td align="center">{{ $row->admit_hour }}</td>                         
        </tr>                
        <?php $count++; ?>
        @endforeach                
      </table>  
    </div>         
  </div>
</div>      
@endsection

<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#after_admit4').DataTable();
    });
</script>

<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#after_admit4_month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($after_admit4_m); ?>,
          datasets: [{
            label: 'AN',
            data: <?php echo json_encode($after_admit4_total_m); ?>,
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
        new ApexCharts(document.querySelector("#after_admit4_year"), {
            
            series: [{
                name: 'AN',
                data: <?php echo json_encode($after_admit4_total_y); ?>,
                    }],
          
            chart: {
                height: 330,
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
                categories: <?php echo json_encode($after_admit4_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
