<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('/images/favicon.ico') }}" type="image/x-icon">
    
    {{-- <meta http-equiv="refresh" content="10; {{ url('dashboard/ipd_mornitor') }}"> --}}

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >IPD Mornitor Huataphanhospital</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
<style>
  table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
  border: 1px solid #ddd;
  }
  th, td {
  padding: 8px;
  }
</style>
<style>  
  .btn {
      background: #f8bbd0;
      border: solid 1px #fce4ec;
      background: linear-gradient(180deg, #f48fb1 0%, #ec407a 35%, #880e4f 100%);
      border-radius: 8px;
      color: white;
      padding: .75rem 1rem;
  }

  .bg-1 {
      background-color: #009688;
  }

  .bg-2 {
      background-color: #259b24;
  }

  .bg-3 {
      background-color: #8bc34a;
  }
  .bg-4 {
      background-color: #cddc39 ;
  }
  .bg-5 {
      background-color: #ffc107;
  }
  .bg-6 {
      background-color: #ff9800;
  }
  .bg-7 {
      background-color: #ff5722;
  }
  .bg-8 {
      background-color: #e51c23;
  }
</style>
</head>
<body>
  <div class="container">
    <div class="row" >
      <div class="col-sm-12">
        <div class="alert alert-primary" role="alert">
          <div class="row" >
            <div class="col-10 mt-2" align="left">
              <h4>IPD Mornitor Huataphanhospital <br>
                 ณ วันที่ <font style="color:red;">{{DateThai(date('Y-m-d'))}}</font> เวลา: <font style="color:red;"><span id="realtime-clock"></span></font> 
                 Admit ปัจจุบัน: <font color="#e91e63"><strong>{{$total}}</strong></font> AN </h4>
            </div>
            <div class="col-2 mt-2" align="right">
              <h4><a class="btn text-center" href="{{ url('/dashboard/opd_mornitor') }}" ><strong>OPD Mornitor</strong></a></h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row" align="center">
      <div class="col-sm-3">
        <div class="card text-white bg-1 mb-3" style="max-width: 18rem;" >
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            ผู้ป่วยใน สามัญ
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$ipd}}</h1>            
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card text-white bg-2 mb-3" style="max-width: 18rem;">
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            ผู้ป่วยใน VIP
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$vip}} </h1>  
          </div>
        </div>
      </div>        
      <div class="col-sm-3">
        <div class="card text-white bg-3 mb-3" style="max-width: 18rem;">
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            ห้องคลอด
          </div>
          <div class="card-body">
            <h1 class="card-title text-center"> {{$lr}}</h1>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card text-white bg-4 mb-3" style="max-width: 18rem;">
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            Homeward
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$homeward}} </h1>  
          </div>
        </div>
      </div>
    </div> <!-- //row --> 

    <div class="row" align="center">
      <div class="col-sm-3">
        <div class="card text-white bg-5 mb-3" style="max-width: 18rem;" >
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            Chart รอแพทย์สรุป
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$non_diagtext}} </h1>
            <p class="card-text">
              <a href="{{ url('/medicalrecord_ipd/non_dchsummary') }}"  target="_blank" class="text-white" style="text-decoration: none; "> more detail...</a>
            </p>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card text-white bg-6 mb-3" style="max-width: 18rem;" >
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            Chart รอลงรหัสโรค ICD10 
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$non_icd10}}</h1>
            <p class="card-text">
              <a href="{{ url('/medicalrecord_ipd/wait_icd_coder') }}"  target="_blank" class="text-white" style="text-decoration: none; "> more detail...</a>
            </p>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card text-white bg-7 mb-3" style="max-width: 18rem;" >
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            รอโอนค่าใช้จ่าย
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$not_transfer}}</h1>
            <p class="card-text">
              <a href="{{ url('/medicalrecord_ipd/finance_chk_opd_wait_money') }}"  target="_blank" class="text-white" style="text-decoration: none; "> more detail...</a>
            </p>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card text-white bg-8 mb-3" style="max-width: 18rem;" >
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            รอชำระเงินสด : จำนวนเงิน
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$wait_paid_money}} : {{number_format($sum_wait_paid_money,2)}}</h1>
            <p class="card-text">
              <a href="{{ url('/medicalrecord_ipd/finance_chk_wait_rcpt_money') }}"  target="_blank" class="text-white" style="text-decoration: none; "> more detail...</a>
            </p>
          </div>
        </div>
      </div>
    </div> <!-- //row --> 
    <hr>
    <form method="POST" enctype="multipart/form-data">
    @csrf
      <div class="row">                          
          <div class="col-md-9" align="center">  
                
          </div>
          <div class="col-lg-3 d-flex justify-content-lg-end">
            <div class="d-flex align-items-center gap-3">
              <select class="form-select" name="budget_year">
                @foreach ($budget_year_select as $row)
                  <option value="{{ $row->LEAVE_YEAR_ID }}"
                    {{ (int)$budget_year === (int)$row->LEAVE_YEAR_ID ? 'selected' : '' }}>
                    {{ $row->LEAVE_YEAR_NAME }}
                  </option> 
                @endforeach
              </select>
              <button type="submit" class="btn btn-primary btn-sm">{{ __('ค้นหา') }}</button>
            </div>
          </div>
      </div>
    </form>
    <div class="row" align="center">
      <div id="bed_occupancy" style="width: 100%; height: 200px"><font color="#4154f1"><strong>อัตราครองเตียงรวม Homeward ปีงบประมาณ {{ $budget_year }}</strong></font></div>
    </div> <!-- //row -->  
    <hr>    
    <div class="row" align="center">  
      <div class="col-sm-12">
        <h6 class="text-primary" align="left"><strong>ข้อมูลผู้ปวยในรวม Homeward</strong></h6>     
        <div style="overflow-x:auto;">          
          <table class="table table-bordered table-striped">
              <thead>
              <tr class="table-primary">
                  <th class="text-center">เดือน</th>
                  <th class="text-center">AN</th>
                  <th class="text-center">วันนอนรวม</th>
                  <th class="text-center">อัตราครองเตียง</th>
                  <th class="text-center">ActiveBase</th>
                  <th class="text-center">CMI</th>
                  <th class="text-center">RW</th>
                  <th class="text-center">Cost/RW</th>                  
              </tr>
              </thead>        
              @foreach($sql4 as $row)
              <tr>
                  <td align="center">{{ $row->month }}</td>
                  <td align="right">{{ number_format($row->an) }}</td>
                  <td align="right">{{ number_format($row->admdate) }}</td>
                  <td align="right">{{ $row->bed_occupancy }}</td>
                  <td align="right">{{ $row->active_bed }}</td>
                  <td align="right">{{ $row->cmi }}</td>
                  <td align="right">{{ $row->adjrw }}</td>
                  <td align="right">{{ number_format($row->income_rw,2) }}</td>                 
              </tr>            
              @endforeach              
          </table>
        </div> 
      </div> 
    </div> <!-- //row -->
    <hr>
    <div class="row" align="center">  
      <div class="col-sm-6">
        <h6 class="text-danger" align="left"><strong>ข้อมูลผู้ปวยใน ไม่รวม Homeward</strong></h6>     
        <div style="overflow-x:auto;">          
          <table class="table table-bordered table-striped">
              <thead>
              <tr class="table-danger">
                  <th class="text-center">เดือน</th>
                  <th class="text-center">AN</th>
                  <th class="text-center">วันนอนรวม</th>
                  <th class="text-center">อัตราครองเตียง</th>
                  <th class="text-center">ActiveBase</th>
                  <th class="text-center">CMI</th>
                  <th class="text-center">RW</th> 
                  <th class="text-center">Cost/RW</th>                  
              </tr>
              </thead>        
              @foreach($ip_normal as $row)
              <tr>
                  <td align="center">{{ $row->month }}</td>
                  <td align="right">{{ number_format($row->an) }}</td>
                  <td align="right">{{ number_format($row->admdate) }}</td>
                  <td align="right">{{ $row->bed_occupancy }}</td>
                  <td align="right">{{ $row->active_bed }}</td>
                  <td align="right">{{ $row->cmi }}</td>
                  <td align="right">{{ $row->adjrw }}</td>
                  <td align="right">{{ number_format($row->income_rw,2) }}</td>                 
              </tr>            
              @endforeach              
          </table>
        </div> 
      </div>  
      <div class="col-sm-6">
        <h6 class="text-success" align="left"><strong>ข้อมูลผู้ปวย Homeward</strong></h6>     
        <div style="overflow-x:auto;">          
          <table class="table table-bordered table-striped">
              <thead>
              <tr class="table-success">
                  <th class="text-center">เดือน</th>
                  <th class="text-center">AN</th>
                  <th class="text-center">วันนอนรวม</th>
                  <th class="text-center">อัตราครองเตียง</th>
                  <th class="text-center">ActiveBase</th>
                  <th class="text-center">CMI</th>
                  <th class="text-center">RW</th>
                  <th class="text-center">Cost/RW</th>                  
              </tr>
              </thead>        
              @foreach($ip_homeward as $row)
              <tr>
                  <td align="center">{{ $row->month }}</td>
                  <td align="right">{{ number_format($row->an) }}</td>
                  <td align="right">{{ number_format($row->admdate) }}</td>
                  <td align="right">{{ $row->bed_occupancy }}</td>
                  <td align="right">{{ $row->active_bed }}</td>
                  <td align="right">{{ $row->cmi }}</td>
                  <td align="right">{{ $row->adjrw }}</td>
                  <td align="right">{{ number_format($row->income_rw,2) }}</td>                 
              </tr>            
              @endforeach              
          </table>
        </div> 
      </div>     
    </div> <!-- //row -->
    <hr>
  </div> <!-- //container -->
<!-- ionicon -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    // ฟังก์ชันแสดงเวลาปัจจุบัน
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const time = `${hours}:${minutes}:${seconds}`;
        document.getElementById('realtime-clock').textContent = time;
    }

    // อัปเดตทุกวินาที
    setInterval(updateClock, 1000);
    updateClock();

    // รีโหลดหน้าทุก 1 นาที (60000 ms)
    setTimeout(function() {
        location.reload();
    }, 60000);
</script>

</body>

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
      new ApexCharts(document.querySelector("#bed_occupancy"), {
          
          series: [{
              name: 'อัตราครองเตียง',
              data: <?php echo json_encode($bed_occupancy); ?>,
                  }],
        
          chart: {
              height: 200,
              type: 'area',
              toolbar: {
              show: false
              },
          },
          markers: {
              size: 4
          },
          colors: ['#4154f1'],
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
              categories:  <?php echo json_encode($month); ?>,
          }
          }).render();
      });
</script>

</html>
