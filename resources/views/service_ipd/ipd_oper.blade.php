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
        <div class="card-header bg-primary bg-opacity-75 text-white">หัตถการที่สำคัญผู้ป่วยใน ปีงบประมาณ {{ $budget_year }} </div>
        <canvas id="ipd_oper_month" style="width: 100%; height: 350px"></canvas>             
      </div>      
    </div> 
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">หัตถการที่สำคัญผู้ป่วยใน  5 ปีงบประมาณย้อนหลัง </div>
        <div id="ipd_oper_year" style="width: 100%; height: 350px"></div>             
      </div>      
    </div>       
  </div>
</div>  
<br>
<!-- row -->
<div class="container-fluid">   
  <div class="card">
    <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อหัตถการใส่ท่อช่วยหายใจ ผู้ป่วยใน ปีงบประมาณ {{$budget_year}} </div>
    <div class="card-body">
      <table id="ipd_oper_list_intube" class="table table-bordered table-striped my-3">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">ลำดับ</th>
            <th class="text-center">HN</th>
            <th class="text-center">AN</th>
            <th class="text-center">ชื่อ-สกุล</th> 
            <th class="text-center">โรคหลัก</th>   
            <th class="text-center">ICD9</th>         
            <th class="text-center">หัตถการ</th>
            <th class="text-center">วัน-เวลาที่ Admit</th>
            <th class="text-center">วัน-เวลาที่ Discharge</th>
            <th class="text-center">วัน-เวลาที่ Refer</th> 
            <th class="text-center">โรคที่ Refer</th>          
        </tr>     
        </thead> 
        <?php $count = 1 ; ?> 
        @foreach($ipd_oper_list_intube as $row)          
        <tr>
            <td align="center">{{ $count }}</td> 
            <td align="center">{{ $row->hn }}</td>
            <td align="center">{{ $row->an }}</td>
            <td align="left">{{ $row->ptname }}</td>   
            <td align="center">{{ $row->pdx }}</td>  
            <td align="center">{{ $row->icd9 }}</td>      
            <td align="center">{{ $row->ipt_oper }}</td>
            <td align="right">{{ DateThai($row->regdate) }} เวลา {{ $row->regtime }}</td>    
            <td align="right">{{ DateThai($row->dchdate) }} เวลา {{ $row->dchtime }}</td>   
            <td align="right">{{ DateThai($row->refer_date) }} เวลา {{ $row->refer_time }}</td>      
            <td align="center">{{ $row->pdx_refer }}</td>                         
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
    <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อหัตถการช่วยฟื้นคืนชีพ ผู้ป่วยใน ปีงบประมาณ {{$budget_year}} </div>
    <div class="card-body">
      <table id="ipd_oper_list_cpr" class="table table-bordered table-striped my-3">
        <thead>
        <tr class="table-secondary">
            <th class="text-center">ลำดับ</th>
            <th class="text-center">HN</th>
            <th class="text-center">AN</th>
            <th class="text-center">ชื่อ-สกุล</th>   
            <th class="text-center">โรคหลัก</th>   
            <th class="text-center">ICD9</th>         
            <th class="text-center">หัตถการ</th>
            <th class="text-center">วัน-เวลาที่ Admit</th>
            <th class="text-center">วัน-เวลาที่ Discharge</th>
            <th class="text-center">วัน-เวลาที่ Refer</th>  
            <th class="text-center">โรคที่ Refer</th>         
        </tr>     
        </thead> 
        <?php $count = 1 ; ?> 
        @foreach($ipd_oper_list_cpr as $row)          
        <tr>
            <td align="center">{{ $count }}</td> 
            <td align="center">{{ $row->hn }}</td>
            <td align="center">{{ $row->an }}</td>
            <td align="left">{{ $row->ptname }}</td> 
            <td align="center">{{ $row->pdx }}</td>    
            <td align="center">{{ $row->icd9 }}</td>      
            <td align="center">{{ $row->ipt_oper }}</td>
            <td align="right">{{ DateThai($row->regdate) }} เวลา {{ $row->regtime }}</td>    
            <td align="right">{{ DateThai($row->dchdate) }} เวลา {{ $row->dchtime }}</td> 
            <td align="right">{{ DateThai($row->refer_date) }} เวลา {{ $row->refer_time }}</td>        
            <td align="center">{{ $row->pdx_refer }}</td>                        
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
        $('#ipd_oper_list_intube').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#ipd_oper_list_cpr').DataTable();
    });
</script>

<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#ipd_oper_month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($ipd_oper_m); ?>,
          datasets: [{
            label: 'ใส่ท่อช่วยหายใจ',
            data: <?php echo json_encode($ipd_oper_intube_m); ?>,
            backgroundColor: [
              'rgba(255, 205, 86, 0.2)',
            ],
            borderColor: [
              'rgb(255, 205, 86)',
            ],
            borderWidth: 1
          },{
            label: 'ช่วยฟื้นคืนชีพ',
            data: <?php echo json_encode($ipd_oper_cpr_m); ?>,
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
        new ApexCharts(document.querySelector("#ipd_oper_year"), {
            
            series: [{
                name: 'ใส่ท่อช่วยหายใจ',
                data: <?php echo json_encode($ipd_oper_intube_y); ?>,
                    },
                    {
                name: 'ช่วยฟื้นคืนชีพ',
                data: <?php echo json_encode($ipd_oper_cpr_y); ?>,
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
            colors: [ '#FFCC00','#FF0033'],
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
                categories: <?php echo json_encode($ipd_oper_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->

