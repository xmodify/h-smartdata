@extends('layouts.app')
 
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
    <div class="col-md-8">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยนอกทั้งหมด ปีงบประมาณ {{ $budget_year }}</div>
        <canvas id="opd_month" style="width: 100%; height: 320px"></canvas>             
      </div>      
    </div>   
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยนอกแยกกลุ่มสิทธิหลัก (ครั้ง) ปีงบประมาณ {{ $budget_year }}</div>
        <div id="opd_year_pttype_visit" style="width: 100%; height: 320px"></div>             
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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยนอกแยกกลุ่มสิทธิหลัก (ครั้ง) ปีงบประมาณ {{ $budget_year }}</div>
        <div class="card-body">
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
            @foreach($opd_month_visit as $visit )                    
            <tr>
                <td align="center">{{ $visit->month }}</td> 
                <td align="center">{{ number_format($visit->ucs) }}</td>
                <td align="center">{{ number_format($visit->ofc) }}</td>
                <td align="center">{{ number_format($visit->sss) }}</td>
                <td align="center">{{ number_format($visit->lgo) }}</td>
                <td align="center">{{ number_format($visit->fss) }}</td> 
                <td align="center">{{ number_format($visit->stp) }}</td>
                <td align="center">{{ number_format($visit->pay) }}</td>
                <td align="center">{{ number_format($visit->visit) }}</td>                   
            </tr>
            <?php $sum_visit_ucs += $visit->ucs ; ?>
            <?php $sum_visit_ofc += $visit->ofc ; ?>
            <?php $sum_visit_sss += $visit->sss ; ?>
            <?php $sum_visit_lgo += $visit->lgo ; ?>
            <?php $sum_visit_fss += $visit->fss ; ?>
            <?php $sum_visit_stp += $visit->stp ; ?>
            <?php $sum_visit_pay += $visit->pay ; ?>
            <?php $sum_visit_total += $visit->visit ; ?>           
            @endforeach          
            <tr>
                <td align="center"><strong>รวม</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_ucs) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_ofc) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_sss) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_lgo) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_fss) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_stp) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_pay) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_total) }}</strong></td> 
            </tr>
          </table>  
        </div>          
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
        <div class="card-header bg-primary bg-opacity-75 text-white">มูลค่าการใช้ยา ผู้ป่วยนอกแยกกลุ่มสิทธิหลัก ปีงบประมาณ {{ $budget_year }}</div>
        <div class="card-body">
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
            <?php $sum_visit_ucs_inc_drug = 0 ; ?>
            <?php $sum_visit_ofc_inc_drug = 0 ; ?>
            <?php $sum_visit_sss_inc_drug = 0 ; ?>
            <?php $sum_visit_lgo_inc_drug = 0 ; ?>
            <?php $sum_visit_fss_inc_drug = 0 ; ?>
            <?php $sum_visit_stp_inc_drug = 0 ; ?>
            <?php $sum_visit_pay_inc_drug = 0 ; ?>
            @foreach($opd_month_visit as $visit )                    
            <tr>
                <td align="center">{{ $visit->month }}</td> 
                <td align="center">{{ number_format($visit->ucs_inc_drug,2) }}</td>
                <td align="center">{{ number_format($visit->ofc_inc_drug,2) }}</td>
                <td align="center">{{ number_format($visit->sss_inc_drug,2) }}</td>
                <td align="center">{{ number_format($visit->lgo_inc_drug,2) }}</td>
                <td align="center">{{ number_format($visit->fss_inc_drug,2) }}</td> 
                <td align="center">{{ number_format($visit->stp_inc_drug,2) }}</td>
                <td align="center">{{ number_format($visit->pay_inc_drug,2) }}</td>
                <td align="center">{{ number_format($visit->ucs_inc_drug+$visit->ofc_inc_drug+$visit->sss_inc_drug
                  +$visit->lgo_inc_drug+$visit->fss_inc_drug+$visit->stp_inc_drug+$visit->pay_inc_drug,2) }}</td>                   
            </tr>
            <?php $sum_visit_ucs_inc_drug += $visit->ucs_inc_drug ; ?>
            <?php $sum_visit_ofc_inc_drug += $visit->ofc_inc_drug ; ?>
            <?php $sum_visit_sss_inc_drug += $visit->sss_inc_drug ; ?>
            <?php $sum_visit_lgo_inc_drug += $visit->lgo_inc_drug ; ?>
            <?php $sum_visit_fss_inc_drug += $visit->fss_inc_drug ; ?>
            <?php $sum_visit_stp_inc_drug += $visit->stp_inc_drug ; ?>
            <?php $sum_visit_pay_inc_drug += $visit->pay_inc_drug ; ?>        
            @endforeach          
            <tr>
                <td align="center"><strong>รวม</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_ucs_inc_drug) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_ofc_inc_drug) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_sss_inc_drug) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_lgo_inc_drug) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_fss_inc_drug) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_stp_inc_drug) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_pay_inc_drug) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_ucs_inc_drug+$sum_visit_ofc_inc_drug
                  +$sum_visit_sss_inc_drug+$sum_visit_lgo_inc_drug+$sum_visit_fss_inc_drug+$sum_visit_stp_inc_drug
                  +$sum_visit_pay_inc_drug,2) }}</strong></td> 
            </tr>
          </table>  
        </div>          
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
        <div class="card-header bg-primary bg-opacity-75 text-white">มูลค่าการตรวจวินิจฉัยทางเทคนิคการแพทย์และพยาธิวิทยา(LAB) ผู้ป่วยนอกแยกกลุ่มสิทธิหลัก ปีงบประมาณ {{ $budget_year }}</div>
        <div class="card-body">
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
            <?php $sum_visit_ucs_inc_lab = 0 ; ?>
            <?php $sum_visit_ofc_inc_lab = 0 ; ?>
            <?php $sum_visit_sss_inc_lab = 0 ; ?>
            <?php $sum_visit_lgo_inc_lab = 0 ; ?>
            <?php $sum_visit_fss_inc_lab = 0 ; ?>
            <?php $sum_visit_stp_inc_lab = 0 ; ?>
            <?php $sum_visit_pay_inc_lab = 0 ; ?>
            @foreach($opd_month_visit as $visit )                    
            <tr>
                <td align="center">{{ $visit->month }}</td> 
                <td align="center">{{ number_format($visit->ucs_inc_lab,2) }}</td>
                <td align="center">{{ number_format($visit->ofc_inc_lab,2) }}</td>
                <td align="center">{{ number_format($visit->sss_inc_lab,2) }}</td>
                <td align="center">{{ number_format($visit->lgo_inc_lab,2) }}</td>
                <td align="center">{{ number_format($visit->fss_inc_lab,2) }}</td> 
                <td align="center">{{ number_format($visit->stp_inc_lab,2) }}</td>
                <td align="center">{{ number_format($visit->pay_inc_lab,2) }}</td>
                <td align="center">{{ number_format($visit->ucs_inc_lab+$visit->ofc_inc_lab+$visit->sss_inc_lab
                  +$visit->lgo_inc_lab+$visit->fss_inc_lab+$visit->stp_inc_lab+$visit->pay_inc_lab,2) }}</td>                   
            </tr>
            <?php $sum_visit_ucs_inc_lab += $visit->ucs_inc_lab ; ?>
            <?php $sum_visit_ofc_inc_lab += $visit->ofc_inc_lab ; ?>
            <?php $sum_visit_sss_inc_lab += $visit->sss_inc_lab ; ?>
            <?php $sum_visit_lgo_inc_lab += $visit->lgo_inc_lab ; ?>
            <?php $sum_visit_fss_inc_lab += $visit->fss_inc_lab ; ?>
            <?php $sum_visit_stp_inc_lab += $visit->stp_inc_lab ; ?>
            <?php $sum_visit_pay_inc_lab += $visit->pay_inc_lab ; ?>        
            @endforeach          
            <tr>
                <td align="center"><strong>รวม</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_ucs_inc_lab) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_ofc_inc_lab) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_sss_inc_lab) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_lgo_inc_lab) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_fss_inc_lab) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_stp_inc_lab) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_pay_inc_lab) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_ucs_inc_lab+$sum_visit_ofc_inc_lab
                  +$sum_visit_sss_inc_lab+$sum_visit_lgo_inc_lab+$sum_visit_fss_inc_lab+$sum_visit_stp_inc_lab
                  +$sum_visit_pay_inc_lab,2) }}</strong></td> 
            </tr>
          </table>  
        </div>          
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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยนอกแยกกลุ่มสิทธิหลัก (คน) ปีงบประมาณ {{ $budget_year }}</div>
        <div class="card-body">
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
            @foreach($opd_month_hn as $hn )                    
            <tr>
                <td align="center">{{ $hn->month }}</td> 
                <td align="center">{{ number_format($hn->ucs) }}</td>
                <td align="center">{{ number_format($hn->ofc) }}</td>
                <td align="center">{{ number_format($hn->sss) }}</td>
                <td align="center">{{ number_format($hn->lgo) }}</td>
                <td align="center">{{ number_format($hn->fss) }}</td> 
                <td align="center">{{ number_format($hn->stp) }}</td>
                <td align="center">{{ number_format($hn->pay) }}</td>
                <td align="center">{{ number_format($hn->hn) }}</td>                   
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
    <div class="col-md-12">
      <div class="card">
      <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยนอกแยกแผนก ปีงบประมาณ {{$budget_year}}</div>
        <div id="opd_spclty" style="width: 100%; height: 400px"></div>             
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
      <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยนอกทั้งหมด 5 ปีงบประมาณย้อนหลัง</div>
        <div id="opd_year" style="width: 100%; height: 400px"></div>             
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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยนอกแยกกลุ่มสิทธิหลัก (ครั้ง) 5 ปีงบประมาณย้อนหลัง</div>
        <div id="opd_year5_pttype_visit" style="width: 100%; height: 400px"></div>             
      </div>      
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยนอกแยกกลุ่มสิทธิหลัก (คน) 5 ปีงบประมาณย้อนหลัง</div>
        <div id="opd_year5_pttype_hn" style="width: 100%; height: 40px"></div>             
      </div>      
    </div>           
  </div>
</div>  
<br>
@endsection
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#opd_month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($opd_m); ?>,
          datasets: [{
            label: 'ครั้ง',
            data: <?php echo json_encode($opd_visit_m); ?>,
            backgroundColor: [
              'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
              'rgb(75, 192, 192)'
            ],
            borderWidth: 1
          },{
            label: 'คน',
            data: <?php echo json_encode($opd_hn_m); ?>,
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
<!-- Pie Chart -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#opd_year_pttype_visit"), {
      series: [{{$sum_visit_ucs}},{{$sum_visit_ofc}},{{$sum_visit_sss}},{{$sum_visit_lgo}},
               {{$sum_visit_fss}},{{$sum_visit_stp}},{{$sum_visit_pay}}],
      chart: {
        height: 300,
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
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#opd_spclty"), {
      series: [{
        name: 'ครั้ง',
        data: <?php echo json_encode($opd_spclty_visit); ?>
      }, {
        name: 'คน',
        data: <?php echo json_encode($opd_spclty_hn); ?>
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
        categories: <?php echo json_encode($opd_spclty_name); ?>
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
            return "$ " + val 
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
        new ApexCharts(document.querySelector("#opd_year"), {
            
            series: [{
                name: 'ครั้ง',
                data: <?php echo json_encode($opd_visit_y); ?>,
                    },
                    {
                name: 'คน',
                data: <?php echo json_encode($opd_hn_y); ?>,
                    }],
          
            chart: {
                height: 400,
                type: 'area',
                toolbar: {
                show: false
                },
            },
            markers: {
                size: 4
            },
            colors: [ '#33CC00','#FF3300'],
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
                categories: <?php echo json_encode($opd_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
<!-- Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#opd_year5_pttype_visit"), {
            
            series: [{
                name: 'ประกันสุขภาพ',
                data: <?php echo json_encode($opd_visit_ucs_y); ?>,
                    },{
                name: 'ข้าราชการ',
                data: <?php echo json_encode($opd_visit_ofc_y); ?>,
                    },{
                name: 'ประกันสังคม',
                data: <?php echo json_encode($opd_visit_sss_y); ?>,
                    },{
                name: 'อปท.',
                data: <?php echo json_encode($opd_visit_lgo_y); ?>,
                    },{
                name: 'ต่างด้าว',
                data: <?php echo json_encode($opd_visit_fss_y); ?>,
                    },{
                name: 'Stateless',
                data: <?php echo json_encode($opd_visit_stp_y); ?>,
                    },{
                name: 'ชำระเงิน/พรบ.',
                data: <?php echo json_encode($opd_visit_pay_y); ?>,
                    }],
          
            chart: {
                height: 400,
                type: 'area',
                toolbar: {
                show: false
                },
            },
            markers: {
                size: 4
            },
            colors: ["#0080FF","#00CC66","#CCCC00", "#FF8000", "#CC0066","#2B908F", "#9933FF"],
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
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                type: 'text',
                categories: <?php echo json_encode($opd_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
<!-- Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#opd_year5_pttype_hn"), {
            
            series: [{
                name: 'ประกันสุขภาพ',
                data: <?php echo json_encode($opd_hn_ucs_y); ?>,
                    },{
                name: 'ข้าราชการ',
                data: <?php echo json_encode($opd_hn_ofc_y); ?>,
                    },{
                name: 'ประกันสังคม',
                data: <?php echo json_encode($opd_hn_sss_y); ?>,
                    },{
                name: 'อปท.',
                data: <?php echo json_encode($opd_hn_lgo_y); ?>,
                    },{
                name: 'ต่างด้าว',
                data: <?php echo json_encode($opd_hn_fss_y); ?>,
                    },{
                name: 'Stateless',
                data: <?php echo json_encode($opd_hn_stp_y); ?>,
                    },{
                name: 'ชำระเงิน/พรบ.',
                data: <?php echo json_encode($opd_hn_pay_y); ?>,
                    }],
          
            chart: {
                height: 400,
                type: 'area',
                toolbar: {
                show: false
                },
            },
            markers: {
                size: 4
            },
            colors: ["#0080FF","#00CC66","#CCCC00", "#FF8000", "#CC0066","#2B908F", "#9933FF"],
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
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                type: 'text',
                categories: <?php echo json_encode($opd_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
