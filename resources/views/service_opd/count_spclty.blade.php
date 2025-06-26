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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยนอกที่ตรวจโดยศัลยแพทย์ทั่วไป ปีงบประมาณ {{ $budget_year }}</div>
        <canvas id="month" style="width: 100%; height: 320px"></canvas>             
      </div>      
    </div>   
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยนอกแยกกลุ่มสิทธิหลักโดยศัลยแพทย์ทั่วไป (ครั้ง) ปีงบประมาณ {{ $budget_year }}</div>
        <div id="year_pttype_visit" style="width: 100%; height: 320px"></div>             
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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้มารับบริการผู้ป่วยนอกแยกกลุ่มสิทธิหลักโดยศัลยแพทย์ทั่วไป ปีงบประมาณ {{ $budget_year }}</div>
        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead>
            <tr class="table-secondary">
                <th class="text-center">เดือน</th>
                <th class="text-center">รวม HN</th>
                <th class="text-center">รวม Visit</th>
                <th class="text-center">ประกันสุขภาพ</th>
                <th class="text-center">ข้าราชการ</th>
                <th class="text-center">ประกันสังคม</th>
                <th class="text-center">อปท.</th>
                <th class="text-center">ต่างด้าว</th>
                <th class="text-center">Stateless</th>
                <th class="text-center">ชำระเงิน/พรบ.</th>             
            </tr>     
            </thead> 
            <?php $sum_hn_total_surgeon = 0 ; ?>
            <?php $sum_visit_total_surgeon = 0 ; ?>
            <?php $sum_visit_ucs_surgeon = 0 ; ?>
            <?php $sum_visit_ofc_surgeon = 0 ; ?>
            <?php $sum_visit_sss_surgeon = 0 ; ?>
            <?php $sum_visit_lgo_surgeon = 0 ; ?>
            <?php $sum_visit_fss_surgeon = 0 ; ?>
            <?php $sum_visit_stp_surgeon = 0 ; ?>
            <?php $sum_visit_pay_surgeon = 0 ; ?>        
            @foreach($month_surgeon as $row )                    
            <tr>
                <td align="center">{{ $row->month }}</td> 
                <td align="center">{{ number_format($row->hn) }}</td>     
                <td align="center">{{ number_format($row->visit) }}</td>     
                <td align="center">{{ number_format($row->ucs) }}</td>
                <td align="center">{{ number_format($row->ofc) }}</td>
                <td align="center">{{ number_format($row->sss) }}</td>
                <td align="center">{{ number_format($row->lgo) }}</td>
                <td align="center">{{ number_format($row->fss) }}</td> 
                <td align="center">{{ number_format($row->stp) }}</td>
                <td align="center">{{ number_format($row->pay) }}</td>                             
            </tr>
            <?php $sum_hn_total_surgeon += $row->hn; ?>  
            <?php $sum_visit_total_surgeon += $row->visit; ?>  
            <?php $sum_visit_ucs_surgeon += $row->ucs; ?>
            <?php $sum_visit_ofc_surgeon += $row->ofc; ?>
            <?php $sum_visit_sss_surgeon += $row->sss; ?>
            <?php $sum_visit_lgo_surgeon += $row->lgo; ?>
            <?php $sum_visit_fss_surgeon += $row->fss; ?>
            <?php $sum_visit_stp_surgeon += $row->stp; ?>
            <?php $sum_visit_pay_surgeon += $row->pay; ?>                
            @endforeach          
            <tr>
                <td align="center"><strong>รวม</strong></td> 
                <td align="center"><strong>{{ number_format($sum_hn_total_surgeon) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_total_surgeon) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_ucs_surgeon) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_ofc_surgeon) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_sss_surgeon) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_lgo_surgeon) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_fss_surgeon) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_stp_surgeon) }}</strong></td> 
                <td align="center"><strong>{{ number_format($sum_visit_pay_surgeon) }}</strong></td>                 
            </tr>
          </table>  
        </div>          
      </div>      
    </div>    
  </div>
</div>
<br>
@endsection
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($m_surgeon); ?>,
          datasets: [{
            label: 'ครั้ง',
            data: <?php echo json_encode($visit_m_surgeon); ?>,
            backgroundColor: [
              'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
              'rgb(75, 192, 192)'
            ],
            borderWidth: 1
          },{
            label: 'คน',
            data: <?php echo json_encode($hn_m_surgeon); ?>,
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
    new ApexCharts(document.querySelector("#year_pttype_visit"), {
      series: [{{$sum_visit_ucs_surgeon}},{{$sum_visit_ofc_surgeon}},{{$sum_visit_sss_surgeon}},{{$sum_visit_lgo_surgeon}},
               {{$sum_visit_fss_surgeon}},{{$sum_visit_stp_surgeon}},{{$sum_visit_pay_surgeon}}],
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

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
