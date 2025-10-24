@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-12">
        <form method="POST" enctype="multipart/form-data">
        @csrf
            <div class="row">
                <div class="col-md-10" align="left">
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
</div>
<!-- row -->
<div class="container-fluid">
  <div class="row justify-content-center">    
    <div class="col-md-12">
      <div class="card">            
        <div style="overflow-x:auto;">
          <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลบริการผู้ป่วยในรวม ปีงบประมาณ {{ $budget_year }}</div>
          <table class="table table-bordered table-striped">
              <thead>
              <tr class="table-primary">
                  <th class="text-center">เดือน</th>
                  <th class="text-center">จำนวน AN</th>
                  <th class="text-center">วันนอนรวม</th>
                  <th class="text-center">อัตราครองเตียง (%)</th>
                  <th class="text-center">Active Base (เตียง)</th>
                  <th class="text-center">AdjRW</th>
                  <th class="text-center">ต้นทุน/AdjRW</th>
                  <th class="text-center">CMI</th>
              </tr>
              </thead>
              <?php $count = 1 ; ?>
              @foreach($ipd_month as $row)
              <tr>
                  <td align="center">{{ $row->month }}</td>
                  <td align="right">{{ number_format($row->an) }}</td>
                  <td align="right">{{ number_format($row->admdate) }}</td>
                  <td align="right">{{ $row->bed_occupancy }}</td>
                  <td align="right">{{ $row->active_bed }}</td>
                  <td align="right">{{ $row->adjrw }}</td>
                  <td align="right">{{ number_format($row->income_rw,2) }}</td>
                  <td align="right">{{ $row->cmi }}</td>
              </tr>
              <?php $count++; ?> 
              @endforeach
              @foreach($ipd_month_sum as $row)
              <tr>
                  <td align="center"><strong>รวม</strong></td>
                  <td align="right"><strong>{{ number_format($row->an) }}</strong></td>
                  <td align="right"><strong>{{ number_format($row->admdate) }}</strong></td>
                  <td align="right"><strong>{{ $row->bed_occupancy }}</strong></td>
                  <td align="right"><strong>{{ $row->active_bed }}</strong></td>
                  <td align="right"><strong>{{ number_format($row->adjrw,2) }}</strong></td>
                  <td align="right"><strong>{{ number_format($row->income_rw,2) }}</strong></td>
                  <td align="right"><strong>{{ $row->cmi }}</strong></td>
              </tr>
              @endforeach
          </table>
        </div>             
      </div>
    </div>
  </div>
</div>
<br>
<!-- row -->
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div style="overflow-x:auto;">
          <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลบริการผู้ป่วยในทั่วไป ปีงบประมาณ {{ $budget_year }}</div>
          <table class="table table-bordered table-striped">
              <thead>
              <tr class="table-danger">
                  <th class="text-center">เดือน</th>
                  <th class="text-center">จำนวน AN</th>
                  <th class="text-center">วันนอนรวม</th>
                  <th class="text-center">อัตราครองเตียง (%)</th>
                  <th class="text-center">Active Base (เตียง)</th>
                  <th class="text-center">AdjRW</th>
                  <th class="text-center">ต้นทุน/AdjRW</th>
                  <th class="text-center">CMI</th>
              </tr>
              </thead>
              <?php $count = 1 ; ?>
              @foreach($ipd_month_normal as $row)
              <tr>
                  <td align="center">{{ $row->month }}</td>
                  <td align="right">{{ number_format($row->an) }}</td>
                  <td align="right">{{ number_format($row->admdate) }}</td>
                  <td align="right">{{ $row->bed_occupancy }}</td>
                  <td align="right">{{ $row->active_bed }}</td>
                  <td align="right">{{ $row->adjrw }}</td>
                  <td align="right">{{ number_format($row->income_rw,2) }}</td>
                  <td align="right">{{ $row->cmi }}</td>
              </tr>
              <?php $count++; ?> 
              @endforeach
              @foreach($ipd_month_normal_sum as $row)
              <tr>
                  <td align="center"><strong>รวม</strong></td>
                  <td align="right"><strong>{{ number_format($row->an) }}</strong></td>
                  <td align="right"><strong>{{ number_format($row->admdate) }}</strong></td>
                  <td align="right"><strong>{{ $row->bed_occupancy }}</strong></td>
                  <td align="right"><strong>{{ $row->active_bed }}</strong></td>
                  <td align="right"><strong>{{ number_format($row->adjrw,2) }}</strong></td>
                  <td align="right"><strong>{{ number_format($row->income_rw,2) }}</strong></td>
                  <td align="right"><strong>{{ $row->cmi }}</strong></td>
              </tr>
              @endforeach
          </table>
        </div> 
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div style="overflow-x:auto;">
          <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลบริการผู้ป่วยใน Homeward ปีงบประมาณ {{ $budget_year }}</div>
          <table class="table table-bordered table-striped">
              <thead>
              <tr class="table-success">
                  <th class="text-center">เดือน</th>
                  <th class="text-center">จำนวน AN</th>
                  <th class="text-center">วันนอนรวม</th>
                  <th class="text-center">อัตราครองเตียง (%)</th>
                  <th class="text-center">Active Base (เตียง)</th>
                  <th class="text-center">AdjRW</th>
                  <th class="text-center">ต้นทุน/AdjRW</th>
                  <th class="text-center">CMI</th>
              </tr>
              </thead>
              <?php $count = 1 ; ?>
              @foreach($ipd_month_homeward as $row)
              <tr>
                  <td align="center">{{ $row->month }}</td>
                  <td align="right">{{ number_format($row->an) }}</td>
                  <td align="right">{{ number_format($row->admdate) }}</td>
                  <td align="right">{{ $row->bed_occupancy }}</td>
                  <td align="right">{{ $row->active_bed }}</td>
                  <td align="right">{{ $row->adjrw }}</td>
                  <td align="right">{{ number_format($row->income_rw,2) }}</td>
                  <td align="right">{{ $row->cmi }}</td>
              </tr>
              <?php $count++; ?> 
              @endforeach
              @foreach($ipd_month_homeward_sum as $row)
              <tr>
                  <td align="center"><strong>รวม</strong></td>
                  <td align="right"><strong>{{ number_format($row->an) }}</strong></td>
                  <td align="right"><strong>{{ number_format($row->admdate) }}</strong></td>
                  <td align="right"><strong>{{ $row->bed_occupancy }}</strong></td>
                  <td align="right"><strong>{{ $row->active_bed }}</strong></td>
                  <td align="right"><strong>{{ number_format($row->adjrw,2) }}</strong></td>
                  <td align="right"><strong>{{ number_format($row->income_rw,2) }}</strong></td>
                  <td align="right"><strong>{{ $row->cmi }}</strong></td>
              </tr>
              @endforeach
          </table>
        </div> 
      </div>
    </div>
  </div>
</div>
<br>
 <!-- row -->
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลบริการผู้ป่วยใน ปีงบประมาณ {{ $budget_year }} </div>
        <canvas id="ipd_month" style="width: 100%; height: 400px"></canvas>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวน Admit ผู้ป่วยในแยกตามเวร ปีงบประมาณ {{ $budget_year }} </div>
        <div id="ipd_shift" style="width: 100%; height: 385px"></div>
      </div>
    </div>
  </div>
</div>
<br>
<!-- Row -->
<div class="container-fluid">
  <div class="row justify-content-center">  
    <div class="col-md-12">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยในแยกกลุ่มสิทธิหลัก ปีงบประมาณ {{ $budget_year }}</div>
        <table class="table table-bordered table-striped">
            <thead>
            <tr class="table-secondary">
                <th class="text-center">เดือน</th>
                <th class="text-center">ประกันสุขภาพ</th>
                <th class="text-center">ข้าราชการ</th>
                <th class="text-center">ประกันสังคม</th>
                <th class="text-center">อปท.</th>
                <th class="text-center">ต่างด้าว</th>
                <th class="text-center">Stateless</th>
                <th class="text-center">ชำระเงิน/พรบ.</th>
                <th class="text-center">รวม</th>
            </tr>     
            </thead> 
            <?php $sum_visit_ucs = 0 ; ?>
            <?php $sum_visit_ofc = 0 ; ?>
            <?php $sum_visit_sss = 0 ; ?>
            <?php $sum_visit_lgo = 0 ; ?>
            <?php $sum_visit_fss = 0 ; ?>
            <?php $sum_visit_stp = 0 ; ?>
            <?php $sum_visit_pay = 0 ; ?>
            <?php $sum_visit_total = 0 ; ?>
            @foreach($ipd_month_pttype as $visit )                    
            <tr>
                <td align="center">{{ $visit->month }}</td> 
                <td align="right">{{ number_format($visit->ucs) }}</td>
                <td align="right">{{ number_format($visit->ofc) }}</td>
                <td align="right">{{ number_format($visit->sss) }}</td>
                <td align="right">{{ number_format($visit->lgo) }}</td>
                <td align="right">{{ number_format($visit->fss) }}</td> 
                <td align="right">{{ number_format($visit->stp) }}</td>
                <td align="right">{{ number_format($visit->pay) }}</td>
                <td align="right">{{ number_format($visit->an) }}</td>                   
            </tr>
            <?php $sum_visit_ucs += $visit->ucs ; ?>
            <?php $sum_visit_ofc += $visit->ofc ; ?>
            <?php $sum_visit_sss += $visit->sss ; ?>
            <?php $sum_visit_lgo += $visit->lgo ; ?>
            <?php $sum_visit_fss += $visit->fss ; ?>
            <?php $sum_visit_stp += $visit->stp ; ?>
            <?php $sum_visit_pay += $visit->pay ; ?>
            <?php $sum_visit_total += $visit->an ; ?>           
            @endforeach          
            <tr>
                <td align="center"><strong>รวม</strong></td> 
                <td align="right"><strong>{{ number_format($sum_visit_ucs) }}</strong></td> 
                <td align="right"><strong>{{ number_format($sum_visit_ofc) }}</strong></td> 
                <td align="right"><strong>{{ number_format($sum_visit_sss) }}</strong></td> 
                <td align="right"><strong>{{ number_format($sum_visit_lgo) }}</strong></td> 
                <td align="right"><strong>{{ number_format($sum_visit_fss) }}</strong></td> 
                <td align="right"><strong>{{ number_format($sum_visit_stp) }}</strong></td> 
                <td align="right"><strong>{{ number_format($sum_visit_pay) }}</strong></td> 
                <td align="right"><strong>{{ number_format($sum_visit_total) }}</strong></td> 
            </tr>
          </table>       
      </div>      
    </div>      
  </div>
</div>
<br> 
 <!-- row -->
 <div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วยในแยกกลุ่มสิทธิหลัก ปีงบประมาณ {{ $budget_year }}</div>
        <div id="ipd_year_pttype_visit" style="width: 100%; height: 420px"></div>             
      </div>      
    </div>   
    <div class="col-md-7">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยในแยกแผนก ปีงบประมาณ {{ $budget_year }} </div>
        <div id="ipd_spclty" style="width: 100%; height: 350px"></div>
      </div>
    </div>
  </div>
</div>
<br>
<!-- row -->
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยใน 5 ปีงบประมาณย้อนหลัง </div>
        <div id="ipd_year_an" style="width: 100%; height: 350px"></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนวันนอนรวมผู้ป่วยใน 5 ปีงบประมาณย้อนหลัง </div>
        <div id="ipd_year_admdate" style="width: 100%; height: 350px"></div>
      </div>
    </div>
  </div>
</div>
<br>
<!-- row -->
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">AdjRW 5 ปีงบประมาณย้อนหลัง </div>
        <div id="ipd_year_adjrw" style="width: 100%; height: 350px"></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">CMI 5 ปีงบประมาณย้อนหลัง </div>
        <div id="ipd_year_cmi" style="width: 100%; height: 350px"></div>
      </div>
    </div>
  </div>
</div>
<br>
@endsection
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#ipd_month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($ipd_m); ?>,
          datasets: [{
            label: 'จำนวน AN',
            data: <?php echo json_encode($ipd_an_m); ?>,
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)'
            ],
            borderColor: [
              'rgb(255, 99, 132)'
            ],
            borderWidth: 1
          },{
            label: 'วันนอนรวม',
            data: <?php echo json_encode($ipd_admdate_m); ?>,
            backgroundColor: [
              'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
              'rgb(255, 159, 64)'
            ],
            borderWidth: 1
          },{
            label: 'อัตราครองเตียง',
            data: <?php echo json_encode($ipd_bed_occupancy_m); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          },{
            label: 'Active Base',
            data: <?php echo json_encode($ipd_active_bed_m); ?>,
            backgroundColor: [
              'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
              'rgb(75, 192, 192)'
            ],
            borderWidth: 1
          },{
            label: 'AdjRW',
            data: <?php echo json_encode($ipd_adjrw_m); ?>,
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
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#ipd_shift"), {
      series: [{
        name: 'เวรเช้า',
        data: <?php echo json_encode($ipd_visit_i); ?>
      }, {
        name: 'เวรบ่าย',
        data: <?php echo json_encode($ipd_visit_o); ?>
      }, {
        name: 'เวรดึก',
        data: <?php echo json_encode($ipd_visit_s); ?>
      }],
      chart: {
        type: 'bar',
        height: 385
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
        categories: <?php echo json_encode($ipd_m); ?>
      },
      yaxis: {
        title: {
          text: '$ (thousands)'
        }
      },
      fill: {
        opacity: 1
      },
      tooltip: {
        y: {
          formatter: function(val) {
            return  val 
          }
        }
      }
    }).render();
  });
</script>
<!-- End Column Chart -->
<!-- End Column Chart -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#ipd_spclty"), {
      series: [{
        name: 'ครั้ง',
        data: <?php echo json_encode($ipd_spclty_an); ?>
      }, {
        name: 'คน',
        data: <?php echo json_encode($ipd_spclty_hn); ?>
      }],
      chart: {
        type: 'bar',
        height: 400
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
        categories: <?php echo json_encode($ipd_spclty_name); ?>
      },
      yaxis: {
        title: {
          text: '$ (thousands)'
        }
      },
      fill: {
        opacity: 1
      },
      tooltip: {
        y: {
          formatter: function(val) {
            return val 
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
        new ApexCharts(document.querySelector("#ipd_year_an"), {

            series: [{
                name: 'จำนวน AN',
                data: <?php echo json_encode($ipd_visit_y); ?>,
                    }],

            chart: {
                height: 250,
                type: 'area',
                toolbar: {
                show: false
                },
            },
            markers: {
                size: 4
            },
            colors: ['#0033FF'],
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
                categories: <?php echo json_encode($ipd_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
<!-- Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#ipd_year_admdate"), {

            series: [{
                name: 'วันนอนรวม',
                data: <?php echo json_encode($ipd_admdate_y); ?>,
                    }],

            chart: {
                height: 250,
                type: 'area',
                toolbar: {
                show: false
                },
            },
            markers: {
                size: 4
            },
            colors: ['#FF33FF'],
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
                categories: <?php echo json_encode($ipd_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
<!-- Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#ipd_year_adjrw"), {

            series: [{
                name: 'AdjRW',
                data: <?php echo json_encode($ipd_adjrw_y); ?>,
                    }],

            chart: {
                height: 250,
                type: 'area',
                toolbar: {
                show: false
                },
            },
            markers: {
                size: 4
            },
            colors: ['#FF6600'],
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
                categories: <?php echo json_encode($ipd_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
<!-- Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#ipd_year_cmi"), {

            series: [{
                name: 'CMI',
                data: <?php echo json_encode($ipd_cmi_y); ?>,
                    }],

            chart: {
                height: 250,
                type: 'area',
                toolbar: {
                show: false
                },
            },
            markers: {
                size: 4
            },
            colors: ['#00CC00'],
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
                categories: <?php echo json_encode($ipd_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
<!-- Pie Chart -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#ipd_year_pttype_visit"), {
      series: [{{$sum_visit_ucs}},{{$sum_visit_ofc}},{{$sum_visit_sss}},{{$sum_visit_lgo}},
               {{$sum_visit_fss}},{{$sum_visit_stp}},{{$sum_visit_pay}}],
      chart: {
        height: 400,
        type: 'pie',
        toolbar: {
          show: true
        }
      },
      labels: ['ประกันสุขภาพ','ข้าราชการ','ประกันสังคม','อปท.','ต่างด้าว','Stateless','ชำระเงิน/พรบ.'],
      colors: ["#0080FF","#00CC66","#CCCC00", "#FF8000", "#CC0066","#2B908F", "#9933FF"]
    }).render();
  });
</script>
<!-- End Pie Chart -->

