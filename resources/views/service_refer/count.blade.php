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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยส่งต่อ ปีงบประมาณ {{$budget_year}} </div>
        <canvas id="refer_month" style="width: 100%; height: 350px"></canvas>             
      </div>      
    </div> 
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยส่งต่อ 5 ปีงบประมาณย้อนหลัง </div>
        <div id="refer_year" style="width: 100%; height: 350px"></div>             
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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยส่งต่อแยกสถานพยาบาลปลายทาง ปีงบประมาณ {{$budget_year}} </div>
        <canvas id="hosp_month" style="width: 100%; height: 350px"></canvas>             
      </div>      
    </div> 
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยส่งต่อแยกสถานพยาบาลปลายทาง 5 ปีงบประมาณย้อนหลัง </div>
        <div id="hosp_year" style="width: 100%; height: 350px"></div>             
      </div>      
    </div>         
  </div>
</div>  
<br>
<!-- row -->
<div class="container-fluid">   
  <div class="card">
    <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วยส่งต่อประเภท OPD ปีงบประมาณ {{$budget_year}} </div>
    <div class="card-body">
      <table id="refer_list_opd" class="table table-bordered table-striped my-3">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">ลำดับ</th>
            <th class="text-center">HN</th>
            <th class="text-center">ชื่อ-สกุล</th> 
            <th class="text-center">โรคประจำตัว</th>
            <th class="text-center">โรคเรื้อรัง</th>
            <th class="text-center">จุดส่งต่อ</th>               
            <th class="text-center">วัน-เวลาที่มารับบริการ</th>           
            <th class="text-center">โรคหลัก</th>
            <th class="text-center">วัน-เวลาที่ Refer</th> 
            <th class="text-center">โรคที่ Refer</th>
            <th class="text-center">วินิจฉัยโรคขั้นต้น</th>
            <th class="text-center">สถานพยาบาล</th>  
            <th class="text-center">ใช้รถพยาบาล</th>                  
        </tr>     
        </thead> 
        <?php $count = 1 ; ?> 
        @foreach($refer_list_opd as $row)          
        <tr>
            <td align="center">{{ $count }}</td> 
            <td align="center">{{ $row->hn }}</td>
            <td align="left">{{ $row->ptname }}</td>
            <td align="left">{{ $row->pmh }}</td>
            <td align="left">{{ $row->clinic }}</td>
            <td align="center">{{ $row->refer_point }}</td>
            <td align="right">{{ DateThai($row->vstdate) }} เวลา {{ $row->vsttime }}</td>    
            <td align="center">{{ $row->pdx }}</td>
            <td align="right">{{ DateThai($row->refer_date) }} เวลา {{ $row->refer_time }}</td>  
            <td align="center">{{ $row->pdx_refer }}</td>
            <td align="left">{{ $row->pre_diagnosis }}</td>
            <td align="right">{{ $row->refer_hos }}</td> 
            <td align="right">{{ $row->with_ambulance }}</td>                                  
        </tr>                
        <?php $count++; ?>
        @endforeach                
      </table>  
    </div>         
  </div>
</div>     
<br>
<!-- row -->
<div class="container-fluid">   
  <div class="card">
    <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วยส่งต่อประเภท IPD ปีงบประมาณ {{$budget_year}} </div>
    <div class="card-body">
      <table id="refer_list_ipd" class="table table-bordered table-striped my-3">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">ลำดับ</th>
            <th class="text-center">HN</th>
            <th class="text-center">ชื่อ-สกุล</th>
            <th class="text-center">โรคประจำตัว</th>
            <th class="text-center">โรคเรื้อรัง</th>
            <th class="text-center">จุดส่งต่อ</th>               
            <th class="text-center">วัน-เวลาที่ Admit</th>           
            <th class="text-center">โรคหลัก</th>
            <th class="text-center">วัน-เวลาที่ Refer</th> 
            <th class="text-center">โรคที่ Refer</th>
            <th class="text-center">วินิจฉัยโรคขั้นต้น</th>
            <th class="text-center">สถานพยาบาล</th>
            <th class="text-center">ใช้รถพยาบาล</th>            
        </tr>     
        </thead> 
        <?php $count = 1 ; ?> 
        @foreach($refer_list_ipd as $row)          
        <tr>
            <td align="center">{{ $count }}</td> 
            <td align="center">{{ $row->hn }}</td>
            <td align="left">{{ $row->ptname }}</td>
            <td align="left">{{ $row->pmh }}</td>
            <td align="left">{{ $row->clinic }}</td>
            <td align="center">{{ $row->refer_point }}</td>
            <td align="right">{{ DateThai($row->regdate) }} เวลา {{ $row->regtime }}</td>    
            <td align="center">{{ $row->pdx }}</td>
            <td align="right">{{ DateThai($row->refer_date) }} เวลา {{ $row->refer_time }}</td>  
            <td align="center">{{ $row->pdx_refer }}</td>
            <td align="left">{{ $row->pre_diagnosis }}</td>
            <td align="right">{{ $row->refer_hos }}</td> 
            <td align="right">{{ $row->with_ambulance }}</td>                                   
        </tr>                
        <?php $count++; ?>
        @endforeach                
      </table>  
    </div>         
  </div>
</div>    
<br> 
@endsection
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
<!-- Datatable -->
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#refer_list_opd').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#refer_list_ipd').DataTable();
    });
</script>
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#refer_month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($refer_m); ?>,
          datasets: [{
            label: 'OPD',
            data: <?php echo json_encode($refer_opd_m); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          },{
            label: 'IPD',
            data: <?php echo json_encode($refer_ipd_m); ?>,
            backgroundColor: [
              'rgba(54, 162, 235, 0.2)'
            ],
            borderColor: [
              'rgb(54, 162, 235)'
            ],
            borderWidth: 1
          },{
            label: 'Ambulance',
            data: <?php echo json_encode($refer_ambu_m); ?>,
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)',
            ],
            borderColor: [
              'rgb(255, 99, 132)',
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
        new ApexCharts(document.querySelector("#refer_year"), {
            
            series: [{
                name: 'OPD',
                data: <?php echo json_encode($refer_opd_y); ?>,
                    },{
                name: 'IPD',
                data: <?php echo json_encode($refer_ipd_y); ?>,
                    },{
                name: 'Ambulance',
                data: <?php echo json_encode($refer_ambu_y); ?>,
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
            colors: [ '#7e57c2','#03a9f4','#e84e40'],
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
                categories: <?php echo json_encode($refer_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#hosp_month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($refer_m); ?>,
          datasets: [{
            label: 'รพ.อำนาจเจริญ',
            data: <?php echo json_encode($refer_r_10703_m); ?>,
            backgroundColor: [
              'rgba(255, 159, 64, 0.2)',
            ],
            borderColor: [
              'rgb(255, 159, 64)',
            ],
            borderWidth: 1
          },{
            label: 'รพ.สรรพสิทธิประสงค์',
            data: <?php echo json_encode($refer_r_10669_m); ?>,
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)',
            ],
            borderColor: [
              'rgb(255, 99, 132)',
            ],
            borderWidth: 1
          },{
            label: 'รพ.พระศรีมหาโพธิ์',
            data: <?php echo json_encode($refer_r_12269_m); ?>,
            backgroundColor: [             
              'rgba(75, 192, 192, 0.2)',
            ],
            borderColor: [         
              'rgb(75, 192, 192)',
            ],
            borderWidth: 1
          },{
            label: 'อื่น ๆ',
            data: <?php echo json_encode($refer_r_outher_m); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)',
            ],
            borderColor: [
              'rgb(153, 102, 255)',
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
        new ApexCharts(document.querySelector("#hosp_year"), {
            
            series: [{
                name: 'รพ.อำนาจเจริญ',
                data: <?php echo json_encode($refer_r_10703_y); ?>,
                    },{
                name: 'รพ.สรรพสิทธิประสงค์',
                data: <?php echo json_encode($refer_r_10669_y); ?>,
                    },{
                name: 'รพ.พระศรีมหาโพธิ์',
                data: <?php echo json_encode($refer_r_12269_y); ?>,
                    },{
                name: 'อื่น ๆ',
                data: <?php echo json_encode($refer_r_outher_y); ?>,
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
            colors: [ '#ffa000','#e84e40','#009688','#7e57c2'],
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
                categories: <?php echo json_encode($refer_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->

