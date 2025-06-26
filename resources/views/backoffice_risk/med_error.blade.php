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
    <div class="col-md-12">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">Medication Error จากโปรแกรม HOSxP ปีงบประมาณ {{$budget_year}} </div>
        <div id="med_error" style="width: 100%; height: 350px"></div>             
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
        <div class="card-header bg-primary bg-opacity-75 text-white">Medication Error จากโปรแกรม HOSxP แยกขั้นตอนของความคลาดเคลื่อน (ผู้ป่วยนอก) ปีงบประมาณ {{$budget_year}}</div>
          <table class="table table-bordered table-striped">
            <thead>
            <tr class="table-secondary">
                <th class="text-center">เดือน</th>
                <th class="text-center">ความคลาดเคลื่อนในการสั่งใช้ยา (Prescribing error)</th>
                <th class="text-center">ความคลาดเคลื่อนในการคัดลอกคำสั่งใช้ยา (Transcribing error)</th>
                <th class="text-center">ความคลาดเคลื่อนก่อนการจ่ายยา (Pre-dispensing error)</th>
                <th class="text-center">ความคลาดเคลื่อนการจ่ายยา (Dispensing error)</th>
                <th class="text-center">ความคลาดเคลื่อนในการให้หรือบริหารยา (Administration error)</th>
            </tr>     
            </thead> 
            <?php $sum_1 = 0 ; ?>
            <?php $sum_2 = 0 ; ?>
            <?php $sum_3 = 0 ; ?>
            <?php $sum_4 = 0 ; ?>
            <?php $sum_5 = 0 ; ?>
            @foreach($med_error as $row)          
            <tr>
                <td align="center">{{ $row->month }}</td> 
                <td align="center">{{ $row->po_1 }}</td>
                <td align="center">{{ $row->po_2 }}</td>
                <td align="center">{{ $row->po_3 }}</td>
                <td align="center">{{ $row->po_4 }}</td>
                <td align="center">{{ $row->po_5 }}</td>                  
            </tr>
            <?php $sum_1 += $row->po_1 ; ?>
            <?php $sum_2 += $row->po_2 ; ?>
            <?php $sum_3 += $row->po_3 ; ?>
            <?php $sum_4 += $row->po_4 ; ?>
            <?php $sum_5 += $row->po_5 ; ?>
            @endforeach
            <tr>
                <td align="center"><strong>รวม</strong></td> 
                <td align="center"><strong>{{ $sum_1 }}</strong></td>
                <td align="center"><strong>{{ $sum_2 }}</strong></td>
                <td align="center"><strong>{{ $sum_3 }}</strong></td>
                <td align="center"><strong>{{ $sum_4 }}</strong></td>
                <td align="center"><strong>{{ $sum_5 }}</strong></td>                 
            </tr>
          </table>        
      </div>      
    </div>  
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">Medication Error จากโปรแกรม HOSxP แยกขั้นตอนของความคลาดเคลื่อน (ผู้ป่วยใน) ปีงบประมาณ {{$budget_year}}</div>
        <table class="table table-bordered table-striped">
            <thead>
            <tr class="table-secondary">
                <th class="text-center">เดือน</th>
                <th class="text-center">ความคลาดเคลื่อนในการสั่งใช้ยา (Prescribing error)</th>
                <th class="text-center">ความคลาดเคลื่อนในการคัดลอกคำสั่งใช้ยา (Transcribing error)</th>
                <th class="text-center">ความคลาดเคลื่อนก่อนการจ่ายยา (Pre-dispensing error)</th>
                <th class="text-center">ความคลาดเคลื่อนการจ่ายยา (Dispensing error)</th>
                <th class="text-center">ความคลาดเคลื่อนในการให้หรือบริหารยา (Administration error)</th>
            </tr>     
            </thead> 
            <?php $sum_1 = 0 ; ?>
            <?php $sum_2 = 0 ; ?>
            <?php $sum_3 = 0 ; ?>
            <?php $sum_4 = 0 ; ?>
            <?php $sum_5 = 0 ; ?>
            @foreach($med_error as $row)          
            <tr>
                <td align="center">{{ $row->month }}</td> 
                <td align="center">{{ $row->pi_1 }}</td>
                <td align="center">{{ $row->pi_2 }}</td>
                <td align="center">{{ $row->pi_3 }}</td>
                <td align="center">{{ $row->pi_4 }}</td>
                <td align="center">{{ $row->pi_5 }}</td>                  
            </tr>
            <?php $sum_1 += $row->pi_1 ; ?>
            <?php $sum_2 += $row->pi_2 ; ?>
            <?php $sum_3 += $row->pi_3 ; ?>
            <?php $sum_4 += $row->pi_4 ; ?>
            <?php $sum_5 += $row->pi_5 ; ?>
            @endforeach
            <tr>
                <td align="center"><strong>รวม</strong></td> 
                <td align="center"><strong>{{ $sum_1 }}</strong></td>
                <td align="center"><strong>{{ $sum_2 }}</strong></td>
                <td align="center"><strong>{{ $sum_3 }}</strong></td>
                <td align="center"><strong>{{ $sum_4 }}</strong></td>
                <td align="center"><strong>{{ $sum_5 }}</strong></td>                 
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
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">Medication Error จากโปรแกรม HOSxP แยกความคลาดเคลื่อนทางยา (ผู้ป่วยนอก) ปีงบประมาณ {{$budget_year}}</div>
          <table class="table table-bordered table-striped">
            <thead>
            <tr class="table-secondary">
                <th class="text-center">เดือน</th>
                <th class="text-center">A</th>
                <th class="text-center">B</th>
                <th class="text-center">C</th>
                <th class="text-center">D</th>
                <th class="text-center">E</th>
                <th class="text-center">F</th>
                <th class="text-center">G</th>
                <th class="text-center">H</th>
                <th class="text-center">I</th>
                <th class="text-center">รวม</th>
            </tr>     
            </thead> 
            <?php $sum_a = 0 ; ?>
            <?php $sum_b = 0 ; ?>
            <?php $sum_c = 0 ; ?>
            <?php $sum_d = 0 ; ?>
            <?php $sum_e = 0 ; ?>
            <?php $sum_f = 0 ; ?>
            <?php $sum_g = 0 ; ?>
            <?php $sum_h = 0 ; ?>
            <?php $sum_i = 0 ; ?>
            <?php $sum_total = 0 ; ?>
            @foreach($med_error as $row)          
            <tr>
                <td align="center">{{ $row->month }}</td> 
                <td align="center">{{ $row->o_a }}</td>
                <td align="center">{{ $row->o_b }}</td>
                <td align="center">{{ $row->o_c }}</td>
                <td align="center">{{ $row->o_d }}</td>
                <td align="center">{{ $row->o_e }}</td> 
                <td align="center">{{ $row->o_f }}</td>
                <td align="center">{{ $row->o_g }}</td>
                <td align="center">{{ $row->o_h }}</td> 
                <td align="center">{{ $row->o_i }}</td>   
                <td align="center">{{ $row->opd }}</td>                    
            </tr>
            <?php $sum_a += $row->o_a ; ?>
            <?php $sum_b += $row->o_b ; ?>
            <?php $sum_c += $row->o_c ; ?>
            <?php $sum_d += $row->o_d ; ?>
            <?php $sum_e += $row->o_e ; ?>
            <?php $sum_f += $row->o_f ; ?>
            <?php $sum_g += $row->o_g ; ?>
            <?php $sum_h += $row->o_h ; ?>
            <?php $sum_i += $row->o_i ; ?>
            <?php $sum_total += $row->opd ; ?>
            @endforeach
            <tr>
                <td align="center"><strong>รวม</strong></td> 
                <td align="center"><strong>{{ $sum_a }}</strong></td>
                <td align="center"><strong>{{ $sum_b }}</strong></td>
                <td align="center"><strong>{{ $sum_c }}</strong></td>
                <td align="center"><strong>{{ $sum_d }}</strong></td>
                <td align="center"><strong>{{ $sum_e }}</strong></td> 
                <td align="center"><strong>{{ $sum_f }}</strong></td>
                <td align="center"><strong>{{ $sum_g }}</strong></td>
                <td align="center"><strong>{{ $sum_h }}</strong></td> 
                <td align="center"><strong>{{ $sum_i }}</strong></td>   
                <td align="center"><strong>{{ $sum_total }}</strong></td>                    
            </tr>
          </table>        
      </div>      
    </div>  
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">Medication Error จากโปรแกรม HOSxP แยกความคลาดเคลื่อนทางยา (ผู้ป่วยใน) ปีงบประมาณ {{$budget_year}}</div>
          <table class="table table-bordered table-striped">
            <thead>
            <tr class="table-secondary">
                <th class="text-center">เดือน</th>
                <th class="text-center">A</th>
                <th class="text-center">B</th>
                <th class="text-center">C</th>
                <th class="text-center">D</th>
                <th class="text-center">E</th>
                <th class="text-center">F</th>
                <th class="text-center">G</th>
                <th class="text-center">H</th>
                <th class="text-center">I</th>
                <th class="text-center">รวม</th>
            </tr>     
            </thead> 
            <?php $sum_a = 0 ; ?>
            <?php $sum_b = 0 ; ?>
            <?php $sum_c = 0 ; ?>
            <?php $sum_d = 0 ; ?>
            <?php $sum_e = 0 ; ?>
            <?php $sum_f = 0 ; ?>
            <?php $sum_g = 0 ; ?>
            <?php $sum_h = 0 ; ?>
            <?php $sum_i = 0 ; ?>
            <?php $sum_total = 0 ; ?>
            @foreach($med_error as $row)          
            <tr>
                <td align="center">{{ $row->month }}</td> 
                <td align="center">{{ $row->i_a }}</td>
                <td align="center">{{ $row->i_b }}</td>
                <td align="center">{{ $row->i_c }}</td>
                <td align="center">{{ $row->i_d }}</td>
                <td align="center">{{ $row->i_e }}</td> 
                <td align="center">{{ $row->i_f }}</td>
                <td align="center">{{ $row->i_g }}</td>
                <td align="center">{{ $row->i_h }}</td> 
                <td align="center">{{ $row->i_i }}</td>   
                <td align="center">{{ $row->ipd }}</td>                    
            </tr>
            <?php $sum_a += $row->i_a ; ?>
            <?php $sum_b += $row->i_b ; ?>
            <?php $sum_c += $row->i_c ; ?>
            <?php $sum_d += $row->i_d ; ?>
            <?php $sum_e += $row->i_e ; ?>
            <?php $sum_f += $row->i_f ; ?>
            <?php $sum_g += $row->i_g ; ?>
            <?php $sum_h += $row->i_h ; ?>
            <?php $sum_i += $row->i_i ; ?>
            <?php $sum_total += $row->ipd ; ?>
            @endforeach
            <tr>
                <td align="center"><strong>รวม</strong></td> 
                <td align="center"><strong>{{ $sum_a }}</strong></td>
                <td align="center"><strong>{{ $sum_b }}</strong></td>
                <td align="center"><strong>{{ $sum_c }}</strong></td>
                <td align="center"><strong>{{ $sum_d }}</strong></td>
                <td align="center"><strong>{{ $sum_e }}</strong></td> 
                <td align="center"><strong>{{ $sum_f }}</strong></td>
                <td align="center"><strong>{{ $sum_g }}</strong></td>
                <td align="center"><strong>{{ $sum_h }}</strong></td> 
                <td align="center"><strong>{{ $sum_i }}</strong></td>   
                <td align="center"><strong>{{ $sum_total }}</strong></td>                    
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
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">20 อันดับ Medication Error จากโปรแกรม HOSxP (ผู้ป่วยนอก) ปีงบประมาณ {{$budget_year}}</div>
        <div id="med_error_top" style="width: 100%; height: 538px"></div>    
      </div>      
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">20 อันดับ Medication Error จากโปรแกรม HOSxP (ผู้ป่วยใน) ปีงบประมาณ {{$budget_year}}</div>
        <div id="med_error_top_ipd" style="width: 100%; height: 538px"></div>    
      </div>      
    </div>
  </div>
</div>
<br> 
@endsection

<!-- Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#med_error"), {
            
            series: [{
                name: 'OPD',
                data: <?php echo json_encode($med_error_opd); ?>,
                    },
                    {
                name: 'IPD',
                data: <?php echo json_encode($med_error_ipd); ?>,
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
                categories: <?php echo json_encode($med_error_m); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
<!-- Bar Chart -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#med_error_top"), {
      series: [{
        data: <?php echo json_encode($med_error_total); ?>
      }],
      chart: {
        type: 'bar',
        height: 538
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
        categories: <?php echo json_encode($med_error_drug); ?>,
      }
    }).render();
  });
</script>
<!-- Column Chart -->
<!-- Bar Chart -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#med_error_top_ipd"), {
      series: [{
        data: <?php echo json_encode($med_error_total_ipd); ?>
      }],
      chart: {
        type: 'bar',
        height: 538
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
        categories: <?php echo json_encode($med_error_drug_ipd); ?>,
      }
    }).render();
  });
</script>
<!-- Column Chart -->

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
