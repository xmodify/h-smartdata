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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยนอกโรค Ashtma ปีงบประมาณ {{$budget_year}} </div>
        <canvas id="diag_month" style="width: 100%; height: 350px"></canvas>             
      </div>      
    </div>   
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยนอกโรค Ashtma 5 ปีงบประมาณย้อนหลัง </div>
        <div id="diag_year" style="width: 100%; height: 350px"></div>             
      </div>      
    </div>      
  </div>
</div> 
<br>
<!-- row -->
<div class="container-fluid">   
  <div class="card">
    <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วยนอกโรค Ashtma ปีงบประมาณ {{$budget_year}} </div>
    <div class="card-body" style="overflow-x:auto;">
      <table id="diag_list" class="table table-bordered table-striped my-3">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">ลำดับ</th>
            <th class="text-center">วัน-เวลาที่มารับบริการ</th>  
            <th class="text-center">ประเภทการมา</th>  
            <th class="text-center">Queue</th>  
            <th class="text-center">HN</th>
            <th class="text-center">ชื่อ-สกุล</th>
            <th class="text-center">อายุ</th>
            <th class="text-center">สิทธิ</th>
            <th class="text-center">อาการสำคัญ</th>
            <th class="text-center">โรคหลัก</th> 
            <th class="text-center">หัตถการ/โรคร่วม</th>
            <th class="text-center">แพทย์ผู้ตรวจ</th>
            <th class="text-center">Refer</th>          
        </tr>     
        </thead> 
        <?php $count = 1 ; ?> 
        @foreach($diag_list as $row)          
        <tr>
            <td align="center">{{ $count }}</td> 
            <td align="right">{{ DateThai($row->vstdate) }} เวลา {{ $row->vsttime }}</td> 
            <td align="left">{{ $row->ovstist }}</td> 
            <td align="center">{{ $row->oqueue }}</td> 
            <td align="center">{{ $row->hn }}</td>
            <td align="left">{{ $row->ptname }}</td>
            <td align="left">{{ $row->age_y }}</td>
            <td align="left">{{ $row->pttype }}</td>
            <td align="left">{{ $row->cc }}</td>
            <td align="right">{{ $row->pdx }}</td>
            <td align="right">{{ $row->dx }}</td>
            <td align="left">{{ $row->dx_doctor }}</td>              
            <td align="left">{{ $row->refer }}</td>                        
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
  <div class="row justify-content-center">  
    <div class="col-md-7">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยในโรค Ashtma ปีงบประมาณ {{$budget_year}} </div>
        <canvas id="diag_month_ipd" style="width: 100%; height: 350px"></canvas>             
      </div>      
    </div>   
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยในโรค Ashtma 5 ปีงบประมาณย้อนหลัง </div>
        <div id="diag_year_ipd" style="width: 100%; height: 350px"></div>             
      </div>      
    </div>      
  </div>
</div> 
<br>
<!-- row -->
<div class="container-fluid">   
  <div class="card">
    <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วยในโรค Ashtma ปีงบประมาณ {{$budget_year}} </div>
    <div class="card-body" style="overflow-x:auto;">
      <table id="diag_list_ipd" class="table table-bordered table-striped my-3">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">ลำดับ</th>
            <th class="text-center">วัน-เวลาที่ Admit</th>  
            <th class="text-center">วัน-เวลาที่ Discharge</th>
            <th class="text-center">AN</th> 
            <th class="text-center">HN</th>
            <th class="text-center">ชื่อ-สกุล</th>
            <th class="text-center">อายุ</th>
            <th class="text-center">สิทธิ</th>
            <th class="text-center">อาการสำคัญ</th>
            <th class="text-center">โรคหลัก</th> 
            <th class="text-center">หัตถการ/โรคร่วม</th>
            <th class="text-center">แพทย์ผู้ตรวจ</th>
            <th class="text-center">Refer</th>          
        </tr>     
        </thead> 
        <?php $count = 1 ; ?> 
        @foreach($diag_list_ipd as $row)          
        <tr>
            <td align="center">{{ $count }}</td> 
            <td align="right">{{ DateThai($row->regdate) }} เวลา {{ $row->regtime }}</td> 
            <td align="right">{{ DateThai($row->dchdate) }} เวลา {{ $row->dchtime }}</td> 
            <td align="center">{{ $row->an }}</td> 
            <td align="center">{{ $row->hn }}</td>
            <td align="left">{{ $row->ptname }}</td>
            <td align="left">{{ $row->age_y }}</td>
            <td align="left">{{ $row->pttype }}</td>
            <td align="left">{{ $row->prediag }}</td>
            <td align="right">{{ $row->pdx }}</td>
            <td align="right">{{ $row->dx }}</td>
            <td align="left">{{ $row->dx_doctor }}</td>              
            <td align="left">{{ $row->refer }}</td>                        
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
  <div class="row justify-content-center">  
    <div class="col-md-7">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วย Refer ด้วยโรค Ashtma ปีงบประมาณ {{$budget_year}} </div>
        <canvas id="refer_month" style="width: 100%; height: 350px"></canvas>             
      </div>      
    </div>  
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วย Refer ด้วยโรค Ashtma 5 ปีงบประมาณย้อนหลัง </div>
        <div id="refer_year" style="width: 100%; height: 350px"></div>             
      </div>      
    </div>    
  </div>
</div>  
<br>
<!-- row -->
<div class="container-fluid">   
  <div class="card">
    <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วยส่งต่อ Refer ด้วยโรค Ashtma ปีงบประมาณ {{$budget_year}} </div>
    <div class="card-body" style="overflow-x:auto;">
      <table id="refer_list" class="table table-bordered table-striped my-3">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">ลำดับ</th>
            <th class="text-center">HN</th>
            <th class="text-center">ชื่อ-สกุล</th>
            <th class="text-center">โรคประจำตัว</th>
            <th class="text-center">โรคเรื้อรัง</th>
            <th class="text-center">ประเภทผู้ป่วย</th>
            <th class="text-center">จุดส่งต่อ</th>               
            <th class="text-center">วัน-เวลาที่มารับบริการ</th>           
            <th class="text-center">โรคหลัก</th>
            <th class="text-center">วัน-เวลาที่ Refer</th> 
            <th class="text-center">โรคที่ Refer</th>
            <th class="text-center">วินิจฉัยโรคขั้นต้น</th>
            <th class="text-center">สถานพยาบาล</th>            
        </tr>     
        </thead> 
        <?php $count = 1 ; ?> 
        @foreach($refer_list as $row)          
        <tr>
            <td align="center">{{ $count }}</td> 
            <td align="center">{{ $row->hn }}</td>
            <td align="left">{{ $row->ptname }}</td>
            <td align="left">{{ $row->pmh }}</td>
            <td align="left">{{ $row->clinic }}</td>
            <td align="center">{{ $row->department }}</td>
            <td align="center">{{ $row->refer_point }}</td>
            <td align="right">{{ DateThai($row->vstdate) }} เวลา {{ $row->vsttime }}</td>    
            <td align="center">{{ $row->pdx }}</td>
            <td align="right">{{ DateThai($row->refer_date) }} เวลา {{ $row->refer_time }}</td>  
            <td align="center">{{ $row->pdx_refer }}</td>
            <td align="left">{{ $row->pre_diagnosis }}</td>
            <td align="right">{{ $row->refer_hos }}</td>                                 
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
<!-- DataTable -->
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#diag_list').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#diag_list_ipd').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#refer_list').DataTable();
    });
</script>
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#diag_month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($diag_m); ?>,
          datasets: [{
            label: 'ครั้ง',
            data: <?php echo json_encode($diag_visit_m); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          },{
            label: 'คน',
            data: <?php echo json_encode($diag_hn_m); ?>,
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
        new ApexCharts(document.querySelector("#diag_year"), {
            
            series: [{
                name: 'ครั้ง',
                data: <?php echo json_encode($diag_visit_y); ?>,
                    },
                    {
                name: 'คน',
                data: <?php echo json_encode($diag_hn_y); ?>,
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
                categories: <?php echo json_encode($diag_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#diag_month_ipd'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($diag_m_ipd); ?>,
          datasets: [{
            label: 'ครั้ง',
            data: <?php echo json_encode($diag_an_m_ipd); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          },{
            label: 'คน',
            data: <?php echo json_encode($diag_hn_m_ipd); ?>,
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
        new ApexCharts(document.querySelector("#diag_year_ipd"), {
            
            series: [{
                name: 'ครั้ง',
                data: <?php echo json_encode($diag_an_y_ipd); ?>,
                    },
                    {
                name: 'คน',
                data: <?php echo json_encode($diag_hn_y_ipd); ?>,
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
                categories: <?php echo json_encode($diag_y_ipd); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
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
              'rgba(255, 99, 132, 0.2)',
            ],
            borderColor: [
              'rgb(255, 99, 132)',
            ],
            borderWidth: 1
          },{
            label: 'IPD',
            data: <?php echo json_encode($refer_ipd_m); ?>,
            backgroundColor: [
              'rgba(54, 162, 235, 0.2)',
            ],
            borderColor: [
              'rgb(54, 162, 235)',
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
                    },
                    {
                name: 'IPD',
                data: <?php echo json_encode($refer_ipd_y); ?>,
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
            colors: [ '#FF6699','#0099FF'],
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

