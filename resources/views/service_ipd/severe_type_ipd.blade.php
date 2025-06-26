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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยในสามัญ แยกระดับความรุนแรง (แรกรับ) ปีงบประมาณ {{ $budget_year }} </div>
        <div id="severe_type_month" style="width: 100%; height: 350px"></div>             
      </div>      
    </div> 
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยในสามัญ แยกระดับความรุนแรง (แรกรับ) 5 ปีงบประมาณย้อนหลัง </div>
        <div id="severe_type_year" style="width: 100%; height: 350px"></div>             
      </div>      
    </div>       
  </div>
</div>  
<br>
<!-- row -->
<div class="container-fluid">   
  <div class="card">
    <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วยในสามัญ ที่ไม่ระบุความรุนแรง (แรกรับ) ปีงบประมาณ {{$budget_year}} </div>
    <div class="card-body">
      <table id="severe_type_list_null" class="table table-bordered table-striped my-3">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">ลำดับ</th>
            <th class="text-center">HN</th>
            <th class="text-center">AN</th>
            <th class="text-center">ชื่อ-สกุล</th>   
            <th class="text-center">สิทธิการรักษา</th>
            <th class="text-center">วัน-เวลาที่ Admit</th>
            <th class="text-center">วัน-เวลาที่ Discharge</th>  
            <th class="text-center">อาการสำคัญ</th>   
            <th class="text-center">โรคหลัก</th>      
        </tr>     
        </thead> 
        <?php $count = 1 ; ?> 
        @foreach($severe_type_list_null as $row)          
        <tr>
            <td align="center">{{ $count }}</td> 
            <td align="center">{{ $row->hn }}</td>
            <td align="center">{{ $row->an }}</td>
            <td align="left">{{ $row->ptname }}</td> 
            <td align="center">{{ $row->pttype }}</td>
            <td align="right">{{ DateThai($row->regdate) }} เวลา {{ $row->regtime }}</td>    
            <td align="right">{{ DateThai($row->dchdate) }} เวลา {{ $row->dchtime }}</td> 
            <td align="left">{{ $row->prediag }}</td>    
            <td align="center">{{ $row->pdx }}</td>  
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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยในสามัญ แยกระดับความรุนแรง (จำหน่าย) ปีงบประมาณ {{ $budget_year }} </div>
        <div id="dch_severe_type_month" style="width: 100%; height: 350px"></div>             
      </div>      
    </div> 
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยในสามัญ แยกระดับความรุนแรง (จำหน่าย) 5 ปีงบประมาณย้อนหลัง </div>
        <div id="dch_severe_type_year" style="width: 100%; height: 350px"></div>             
      </div>      
    </div>       
  </div>
</div>  
<br>
<!-- row -->
<div class="container-fluid">   
  <div class="card">
    <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วยในสามัญ ที่ไม่ระบุความรุนแรง (จำหน่าย) ปีงบประมาณ {{$budget_year}} </div>
    <div class="card-body">
      <table id="dch_severe_type_list_null" class="table table-bordered table-striped my-3">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">ลำดับ</th>
            <th class="text-center">HN</th>
            <th class="text-center">AN</th>
            <th class="text-center">ชื่อ-สกุล</th>   
            <th class="text-center">สิทธิการรักษา</th>
            <th class="text-center">วัน-เวลาที่ Admit</th>
            <th class="text-center">วัน-เวลาที่ Discharge</th>  
            <th class="text-center">อาการสำคัญ</th>   
            <th class="text-center">โรคหลัก</th>      
        </tr>     
        </thead> 
        <?php $count = 1 ; ?> 
        @foreach($dch_severe_type_list_null as $row)          
        <tr>
            <td align="center">{{ $count }}</td> 
            <td align="center">{{ $row->hn }}</td>
            <td align="center">{{ $row->an }}</td>
            <td align="left">{{ $row->ptname }}</td> 
            <td align="center">{{ $row->pttype }}</td>
            <td align="right">{{ DateThai($row->regdate) }} เวลา {{ $row->regtime }}</td>    
            <td align="right">{{ DateThai($row->dchdate) }} เวลา {{ $row->dchtime }}</td> 
            <td align="left">{{ $row->prediag }}</td>    
            <td align="center">{{ $row->pdx }}</td>  
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
        $('#severe_type_list_null').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#dch_severe_type_list_null').DataTable();
    });
</script>
<!-- Column Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new ApexCharts(document.querySelector("#severe_type_month"), {
        series: [{
          name: 'ระดับ 1 (ขาว)',
          data: <?php echo json_encode($severe_type_1); ?>,
        }, {
          name: 'ระดับ 2 (เขียว)',
          data: <?php echo json_encode($severe_type_2); ?>,
        }, {
          name: 'ระดับ 3 (เหลือง)',
          data: <?php echo json_encode($severe_type_3); ?>,
        }, {
          name: 'ระดับ 4 (แดง)',
          data: <?php echo json_encode($severe_type_4); ?>,
        }, {
          name: 'ไม่ระบุ',
          data: <?php echo json_encode($severe_type_null); ?>,
        }],
        chart: {
          type: 'bar',
          height: 350,
          background: '#fff2f2'
        },
        colors: ['#FFFFFF','#009900','#FFCC00','#FF0000','#FF00FF'],
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '90%',
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: true
        },
        stroke: {
          show: true,
          width: 1,
          colors: ['transparent']
        },
        xaxis: {
          categories:  <?php echo json_encode($severe_type_m); ?>,    
        },
        yaxis: {
          title: {
            text: 'AN'
          }
        },
        fill: {
          opacity: 1
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val + " AN"
            }
          }
        }
      }).render();
    });
</script>
<!-- End Column Chart -->
<!-- Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#severe_type_year"), {
            
            series: [{
                name: 'ระดับ 1 (ขาว)',
                data: <?php echo json_encode($severe_type_1_y); ?>,
                    },{
                name: 'ระดับ 2 (เขียว)',
                data: <?php echo json_encode($severe_type_2_y); ?>,
                    },{
                name: 'ระดับ 3 (เหลือง)',
                data: <?php echo json_encode($severe_type_3_y); ?>,
                    },{
                name: 'ระดับ 4 (แดง)',
                data: <?php echo json_encode($severe_type_4_y); ?>,
                    },{
                name: 'ไม่ระบุ',
                data: <?php echo json_encode($severe_type_null_y); ?>,
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
            colors: ['#FFFFFF','#009900','#FFCC00','#FF0000','#FF00FF'],
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
                categories: <?php echo json_encode($severe_type_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
<!-- Column Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new ApexCharts(document.querySelector("#dch_severe_type_month"), {
        series: [{
          name: 'ระดับ 1 (ขาว)',
          data: <?php echo json_encode($dch_severe_type_1); ?>,
        }, {
          name: 'ระดับ 2 (เขียว)',
          data: <?php echo json_encode($dch_severe_type_2); ?>,
        }, {
          name: 'ระดับ 3 (เหลือง)',
          data: <?php echo json_encode($dch_severe_type_3); ?>,
        }, {
          name: 'ระดับ 4 (แดง)',
          data: <?php echo json_encode($dch_severe_type_4); ?>,
        }, {
          name: 'ไม่ระบุ',
          data: <?php echo json_encode($dch_severe_type_null); ?>,
        }],
        chart: {
          type: 'bar',
          height: 350,
          background: '#fff2f2'
        },
        colors: ['#FFFFFF','#009900','#FFCC00','#FF0000','#FF00FF'],
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '90%',
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: true
        },
        stroke: {
          show: true,
          width: 1,
          colors: ['transparent']
        },
        xaxis: {
          categories:  <?php echo json_encode($severe_type_m); ?>,    
        },
        yaxis: {
          title: {
            text: 'AN'
          }
        },
        fill: {
          opacity: 1
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val + " AN"
            }
          }
        }
      }).render();
    });
</script>
<!-- End Column Chart -->
<!-- Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#dch_severe_type_year"), {
            
            series: [{
                name: 'ระดับ 1 (ขาว)',
                data: <?php echo json_encode($dch_severe_type_1_y); ?>,
                    },{
                name: 'ระดับ 2 (เขียว)',
                data: <?php echo json_encode($dch_severe_type_2_y); ?>,
                    },{
                name: 'ระดับ 3 (เหลือง)',
                data: <?php echo json_encode($dch_severe_type_3_y); ?>,
                    },{
                name: 'ระดับ 4 (แดง)',
                data: <?php echo json_encode($dch_severe_type_4_y); ?>,
                    },{
                name: 'ไม่ระบุ',
                data: <?php echo json_encode($dch_severe_type_null_y); ?>,
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
            colors: ['#FFFFFF','#009900','#FFCC00','#FF0000','#FF00FF'],
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
                categories: <?php echo json_encode($severe_type_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
