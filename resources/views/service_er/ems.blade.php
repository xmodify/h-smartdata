@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">

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
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยให้บริการ EMS ปีงบประมาณ {{$budget_year}} </div>
        <canvas id="ems_month" style="width: 100%; height: 350px"></canvas>             
      </div>      
    </div>      
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยให้บริการ EMS 5 ปีงบประมาณย้อนหลัง </div>
        <div id="ems_year" style="width: 100%; height: 350px"></div>             
      </div>      
    </div>    
  </div>
  <br>
<!-- row -->
  <div class="row justify-content-center"> 
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">20 อันดับโรคผู้ป่วยให้บริการ EMS-ALS ปีงบประมาณ {{ $budget_year }}</div>
        <div id="ems_diag_als" style="width: 100%; height: 600px"></div>    
      </div>
    </div> 
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">20 อันดับโรคผู้ป่วยให้บริการ EMS-ILS ปีงบประมาณ {{ $budget_year }}</div>
        <div id="ems_diag_ils" style="width: 100%; height: 600px"></div>    
      </div>
    </div>   
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">20 อันดับโรคผู้ป่วยให้บริการ EMS-FR ปีงบประมาณ {{ $budget_year }}</div>
        <div id="ems_diag_fr" style="width: 100%; height: 600px"></div>    
      </div>
    </div>    
  </div>
  <br>
<!-- row -->
  <div class="row justify-content-center"> 
    <div class="col-md-12">
      <div class="card">         
        <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วยให้บริการ EMS ปีงบประมาณ {{ $budget_year }}</div>      
        <div class="card-body"> 
          <table id="ems_list" class="table table-bordered table-striped my-3">
            <thead>
            <tr class="table-secondary">
                <th class="text-center">ลำดับ</th>
                <th class="text-center">วันที่รับบริการ</th>          
                <th class="text-center">Queue</th>
                <th class="text-center">HN</th>
                <th class="text-center">ชื่อ-สกุล</th>
                <th class="text-center">อายุ</th>
                <th class="text-center">สิทธิ</th>
                <th class="text-center">ICD10</th>
                <th class="text-center">อาการสำคัญ</th>
                <th class="text-center">แพทย์ผู้ตรวจ</th>
                <th class="text-center">EMS</th>
                <th class="text-center">ประเภท</th>
                <th class="text-center">Admit</th>
                <th class="text-center">Refer</th>
            </tr>
            </thead>
            <?php $count = 1 ; ?>
            @foreach($ems_list as $row)
            <tr>
                <td align="center">{{ $count }}</td>
                <td align="right">{{ DateThai($row->vstdate) }}</td>
                <td align="center">{{ $row->oqueue }}</td>
                <td align="center">{{ $row->hn }}</td>
                <td align="left">{{ $row->ptname }}</td>
                <td align="center">{{ $row->age_y }}</td>
                <td align="center">{{ $row->pttype }}</td>
                <td align="right">{{ $row->pdx }}</td>
                <td align="left">{{ $row->cc }}</td>
                <td align="left">{{ $row->dx_doctor }}</td>
                <td align="center">{{ $row->ems }}</td>
                <td align="center">{{ $row->er_emergency_type }}</td>
                <td align="center">{{ $row->admit }}</td>
                <td align="left">{{ $row->refer }}</td>
            </tr>
            <?php $count++; ?>
            @endforeach
          </table>
        </div>            
      </div>
    </div>
  </div>
  <!-- row -->
</div>
<br>
<!-- row -->
<div class="container-fluid">
    <div class="row justify-content-center">  
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยให้บริการ EMS ALS แยกประเภทความเร่งด่วน ปีงบประมาณ {{ $budget_year }}</div>
                <div id="ems_als_type" style="width: 100%; height: 350px"></div>    
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
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยให้บริการ EMS ILS แยกประเภทความเร่งด่วน ปีงบประมาณ {{ $budget_year }}</div>
                <div id="ems_ils_type" style="width: 100%; height: 350px"></div>    
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
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยให้บริการ EMS FR แยกประเภทความเร่งด่วน ปีงบประมาณ {{ $budget_year }}</div>
                <div id="ems_fr_type" style="width: 100%; height: 350px"></div>    
            </div>
        </div>                 
    </div>
</div>  
<br>
@endsection
<!-- Datatable -->
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#ems_list').DataTable();
    });
</script>

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>

<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#ems_month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($ems_m); ?>,
          datasets: [{
            label: 'ALS',
            data: <?php echo json_encode($ems_als_m); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          },{
            label: 'ILS',
            data: <?php echo json_encode($ems_ils_m); ?>,
            backgroundColor: [
              'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
              'rgb(255, 159, 64)'
            ],
            borderWidth: 1
          },{
            label: 'FR',
            data: <?php echo json_encode($ems_fr_m); ?>,
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
<!-- Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#ems_year"), {
            
            series: [{
                name: 'ALS',
                data: <?php echo json_encode($ems_als_y); ?>,
                    },
                    {
                name: 'ILS',
                data: <?php echo json_encode($ems_ils_y); ?>,
                    },
                    {
                name: 'FR',
                data: <?php echo json_encode($ems_fr_y); ?>,
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
            colors: [ '#CC66FF','#FF9933','#33CC99'],
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
                categories: <?php echo json_encode($ems_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
<!-- Bar Chart -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#ems_diag_als"), {
      series: [{
        data: <?php echo json_encode($ems_diag_als_sum); ?>
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
        categories: <?php echo json_encode($ems_diag_als_name); ?>,
      }
    }).render();
  });
</script>
<!-- Column Chart -->
<!-- Bar Chart -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#ems_diag_ils"), {
      series: [{
        data: <?php echo json_encode($ems_diag_ils_sum); ?>
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
        categories: <?php echo json_encode($ems_diag_ils_name); ?>,
      }
    }).render();
  });
</script>
<!-- Column Chart -->
<!-- Bar Chart -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#ems_diag_fr"), {
      series: [{
        data: <?php echo json_encode($ems_diag_fr_sum); ?>
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
        categories: <?php echo json_encode($ems_diag_fr_name); ?>,
      }
    }).render();
  });
</script>
<!-- Column Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new ApexCharts(document.querySelector("#ems_als_type"), {
        series: [{
          name: 'Resuscitate',
          data: <?php echo json_encode($ems_als_Resuscitate); ?>,
        }, {
          name: 'Emergency',
          data: <?php echo json_encode($ems_als_Emergency); ?>,
        }, {
          name: 'Urgency',
          data: <?php echo json_encode($ems_als_Urgency); ?>,
        }, {
          name: 'Semi_Urgency',
          data: <?php echo json_encode($ems_als_Semi_Urgency); ?>,
        }, {
          name: 'Non_Urgency',
          data: <?php echo json_encode($ems_als_Non_Urgency); ?>,
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
          categories:  <?php echo json_encode($ems_als_month); ?>,    
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
      new ApexCharts(document.querySelector("#ems_ils_type"), {
        series: [{
          name: 'Resuscitate',
          data: <?php echo json_encode($ems_ils_Resuscitate); ?>,
        }, {
          name: 'Emergency',
          data: <?php echo json_encode($ems_ils_Emergency); ?>,
        }, {
          name: 'Urgency',
          data: <?php echo json_encode($ems_ils_Urgency); ?>,
        }, {
          name: 'Semi_Urgency',
          data: <?php echo json_encode($ems_ils_Semi_Urgency); ?>,
        }, {
          name: 'Non_Urgency',
          data: <?php echo json_encode($ems_ils_Non_Urgency); ?>,
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
          categories:  <?php echo json_encode($ems_ils_month); ?>,    
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
      new ApexCharts(document.querySelector("#ems_fr_type"), {
        series: [{
          name: 'Resuscitate',
          data: <?php echo json_encode($ems_fr_Resuscitate); ?>,
        }, {
          name: 'Emergency',
          data: <?php echo json_encode($ems_fr_Emergency); ?>,
        }, {
          name: 'Urgency',
          data: <?php echo json_encode($ems_fr_Urgency); ?>,
        }, {
          name: 'Semi_Urgency',
          data: <?php echo json_encode($ems_fr_Semi_Urgency); ?>,
        }, {
          name: 'Non_Urgency',
          data: <?php echo json_encode($ems_fr_Non_Urgency); ?>,
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
          categories:  <?php echo json_encode($ems_fr_month); ?>,    
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
