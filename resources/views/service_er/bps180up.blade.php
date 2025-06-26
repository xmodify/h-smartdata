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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยที่มีความดันโลหิตค่าบนมากกว่า 180 ปีงบประมาณ {{$budget_year}} </div>
        <canvas id="bps180up_month" style="width: 100%; height: 350px"></canvas>             
      </div>      
    </div>   
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยที่มีความดันโลหิตค่าบนมากกว่า 180 5 ปีงบประมาณย้อนหลัง </div>
        <div id="bps180up_year" style="width: 100%; height: 350px"></div>             
      </div>      
    </div>      
  </div>
</div> 
<br>
<!-- row --> 
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">รายชื่อผู้ป่วยที่มีความดันโลหิตค่าบนมากกว่า 180 ปีงบประมาณ {{ $budget_year }}</div>
        <div class="card-body">
            <table id="bps180up" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center">ลำดับ</th>
                    <th class="text-center">วันที่รับบริการ</th> 
                    <th class="text-center">ห้องตรวจ</th>
                    <th class="text-center">Queue</th>                  
                    <th class="text-center">HN</th>
                    <th class="text-center">ชื่อ-สกุล</th>
                    <th class="text-center">อายุ</th>
                    <th class="text-center">โรคหลัก</th>
                    <th class="text-center">DiagText</th>
                    <th class="text-center">อาการสำคัญ</th>                   
                    <th class="text-center">ความดัน</th>
                    <th class="text-center">น้ำหนัก</th>
                    <th class="text-center">ส่วนสูง</th>
                    <th class="text-center">สถานะ Clinic HT</th>
                    <th class="text-center">Admit</th>
                    <th class="text-center">Refer</th>
                </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($bps180up_list as $row)
                <tr>
                    <td align="center">{{ $count }}</td>
                    <td align="right">{{ DateThai($row->vstdate) }}</td>    
                    <td align="center">{{ $row->depart }}</td>    
                    <td align="center">{{ $row->oqueue }}</td>     
                    <td align="center">{{ $row->hn }}</td>
                    <td align="left">{{ $row->ptname }}</td>
                    <td align="center">{{ $row->age_y }}</td>
                    <td align="right">{{ $row->pdx }}</td>
                    <td align="left">{{ $row->diag_text }}</td>
                    <td align="left">{{ $row->cc }}</td>                   
                    <td align="center">{{ $row->bp }}</td>
                    <td align="center">{{ $row->bw }}</td>
                    <td align="center">{{ $row->height }}</td>
                    <td align="center">{{ $row->clinic }}</td>
                    <td align="center">{{ $row->admit }}</td>
                    <td align="center">{{ $row->refer }}</td>
                </tr>
                <?php $count++; ?>
                @endforeach
            </table>
        </div>
    </div>
 </div>
@endsection
<!-- DataTable -->
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#bps180up').DataTable();
    });
</script>
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#bps180up_month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($bps180up_m); ?>,
          datasets: [{
            label: 'ALL',
            data: <?php echo json_encode($bps180up_all_m); ?>,
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)',
            ],
            borderColor: [
              'rgb(255, 99, 132)',
            ],
            borderWidth: 1
          },{
            label: 'Admit',
            data: <?php echo json_encode($bps180up_admit_m); ?>,
            backgroundColor: [
              'rgba(54, 162, 235, 0.2)',
            ],
            borderColor: [
              'rgb(54, 162, 235)',
            ],
            borderWidth: 1
          },{
            label: 'Refer',
            data: <?php echo json_encode($bps180up_refer_m); ?>,
            backgroundColor: [
            'rgba(255, 159, 64, 0.2)',
            ],
            borderColor: [
            'rgb(255, 159, 64)',
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
        new ApexCharts(document.querySelector("#bps180up_year"), {
            
            series: [{
                name: 'ALL',
                data: <?php echo json_encode($bps180up_all_y); ?>,
                    },{
                name: 'Admit',
                data: <?php echo json_encode($bps180up_admit_y); ?>,
                    },{
                name: 'Refer',
                data: <?php echo json_encode($bps180up_refer_y); ?>,
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
            colors: [ '#FF6699','#0099FF','#FFCC00'],
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
                categories: <?php echo json_encode($bps180up_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
