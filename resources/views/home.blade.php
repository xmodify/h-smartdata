@extends('layouts.app')

@section('content')

<div class="container-fluid"> 
    <div class="row justify-content-center">      
      <div class="col-md-12">
        <form method="POST" enctype="multipart/form-data">
        @csrf
          <div class="row">                          
              <div class="col-md-10" align="left">
                <h5 class="alert alert-primary"><strong>ระบบข้อมูลอัจฉริยะโรงพยาบาลหัวตะพาน </strong></h5>
              </div>
              <div class="col-lg-2 d-flex justify-content-lg-end">
                <div class="d-flex align-items-center gap-3">
                  <select class="form-select" name="budget_year">
                    @foreach ($budget_year_select as $row)
                      <option value="{{ $row->LEAVE_YEAR_ID }}"
                        {{ (int)$budget_year === (int)$row->LEAVE_YEAR_ID ? 'selected' : '' }}>
                        {{ $row->LEAVE_YEAR_NAME }}
                      </option> 
                    @endforeach
                  </select>
                  <button type="submit" class="btn btn-primary ">{{ __('ค้นหา') }}</button>
                </div>
              </div>
          </div>
        </form>
      </div>    
    </div>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยนอก ปีงบประมาณ {{ $budget_year }}</div>
                <div id="opd" style="width: 100%; height: 350px"></div>    
            </div>
        </div>  
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">10 อันดับโรคผู้ป่วยนอก ปีงบประมาณ {{ $budget_year }}</div>
                <div id="opd_diag_top" style="width: 100%; height: 350px"></div>    
            </div>
        </div>              
    </div>
</div>  
<!-- row -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยใน ปีงบประมาณ {{ $budget_year }}</div>
                <div id="ipd" style="width: 100%; height: 350px"></div>    
            </div>
        </div>  
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">10 อันดับโรคผู้ป่วยใน ปีงบประมาณ {{ $budget_year }}</div>
                <div id="ipd_diag_top" style="width: 100%; height: 350px"></div>    
            </div>
        </div>              
    </div>
</div>
<!-- row -->  
<div class="container-fluid">
    <div class="row justify-content-center">       
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยนอกแยกแผนก ปีงบประมาณ {{ $budget_year }}</div>
                <div id="opd_dep" style="width: 100%; height: 350px"></div>    
            </div>
        </div>              
    </div>
</div>  
<!-- row -->
<div class="container-fluid">
    <div class="row justify-content-center">  
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการแผนกอุบัติเหตุ-ฉุกเฉิน ปีงบประมาณ {{ $budget_year }}</div>
                <div id="er_type" style="width: 100%; height: 350px"></div>    
            </div>
        </div>                 
    </div>
</div>  
@endsection
<!-- Line Chart OPD -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#opd"), {
            
            series: [{
                name: 'ครั้ง',
                data: <?php echo json_encode($opd_visit); ?>,
                    },{
                name: 'คน',
                data: <?php echo json_encode($opd_hn); ?>,
                    }
                    ],
          
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                show: false
                },
            },
            markers: {
                size: 4
            },
            colors: ['#FF3333','#0099FF'],
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
                categories:  ['ต.ค.', 'พ.ย.', 'ธ.ค.', 'ม.ค', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.','ก.ค.', 'ส.ค.', 'ก.ย.'],
            }
            }).render();
        });
</script>
<!-- End Line Chart OPD -->
<!-- Column Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new ApexCharts(document.querySelector("#er_type"), {
        series: [{
          name: 'Resuscitate',
          data: <?php echo json_encode($er_Resuscitate); ?>,
        }, {
          name: 'Emergency',
          data: <?php echo json_encode($er_Emergency); ?>,
        }, {
          name: 'Urgency',
          data: <?php echo json_encode($er_Urgency); ?>,
        }, {
          name: 'Semi_Urgency',
          data: <?php echo json_encode($er_Semi_Urgency); ?>,
        }, {
          name: 'Non_Urgency',
          data: <?php echo json_encode($er_Non_Urgency); ?>,
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
          categories:  ['ต.ค.', 'พ.ย.', 'ธ.ค.', 'ม.ค', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.','ก.ค.', 'ส.ค.', 'ก.ย.']     
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
<!-- Bar Chart -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#opd_diag_top"), {
      series: [{
        data: <?php echo json_encode($opd_diag_top_sum); ?>
      }],
      chart: {
        type: 'bar',
        height: 350
      },
      plotOptions: {
        bar: {
          borderRadius: 4,
          horizontal: true,
        }
      },
      dataLabels: {
        enabled: true
      },
      xaxis: {
        categories: <?php echo json_encode($opd_diag_top_name); ?>,
      }
    }).render();
  });
</script>
<!-- End Bar Chart -->
<!-- End Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#ipd"), {
            
            series: [{
                name: 'AN',
                data: <?php echo json_encode($ipd_visit); ?>,
                    },{
                name: 'วันนอนรวม',
                data: <?php echo json_encode($ipd_admdate); ?>,
                    },{
                name: 'อัตราครองเตียง',
                data: <?php echo json_encode($ipd_bed_occupancy); ?>,
                    }],
          
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                show: false
                },
            },
            markers: {
                size: 4
            },
            colors: ['#FF9900','#FF00FF','#2eca6a'],
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
                categories:  ['ต.ค.', 'พ.ย.', 'ธ.ค.', 'ม.ค', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.','ก.ค.', 'ส.ค.', 'ก.ย.'],
            }
            }).render();
        });
</script>
<!-- End Line Chart-->
<!-- Bar Chart -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#ipd_diag_top"), {
      series: [{
        data: <?php echo json_encode($ipd_diag_top_sum); ?>
      }],
      chart: {
        type: 'bar',
        height: 350
      },
      plotOptions: {
        bar: {
          borderRadius: 4,
          horizontal: true,
        }
      },
      dataLabels: {
        enabled: true
      },
      xaxis: {
        categories: <?php echo json_encode($ipd_diag_top_name); ?>,
      }
    }).render();
  });
</script>
<!-- Column Chart -->
<script>
                document.addEventListener("DOMContentLoaded", () => {
                  new ApexCharts(document.querySelector("#opd_dep"), {
                    series: [{
                      name: 'กายภาพ',
                      data: <?php echo json_encode($physic_visit); ?>,
                    }, {
                      name: 'แพทบ์แผนไทย',
                      data: <?php echo json_encode($health_med_visit); ?>,
                    }, {
                      name: 'ทันตกรรม',
                      data: <?php echo json_encode($dent_visit); ?>,
                    }, {
                      name: 'ANC',
                      data: <?php echo json_encode($anc_visit); ?>,
                    }, {
                      name: 'Refer',
                      data: <?php echo json_encode($refer_visit); ?>,
                    }],
                    chart: {
                      type: 'bar',
                      height: 350,
                      background: '#eff7ff'
                    },
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
                      width: 2,
                      colors: ['transparent']
                    },
                    xaxis: {
                      categories:  ['ต.ค.', 'พ.ย.', 'ธ.ค.', 'ม.ค', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.','ก.ค.', 'ส.ค.', 'ก.ย.'],
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


