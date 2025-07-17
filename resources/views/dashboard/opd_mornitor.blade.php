<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <link rel="icon" href="{{ asset('/images/favicon.ico') }}" type="image/x-icon">
    
    {{-- <meta http-equiv="refresh" content="10; {{ url('dashboard/opd_mornitor') }}"> --}}

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >OPD Mornitor Huataphanhospital</title> 

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

<style>  

  .bg-1 {
      background-color: #3f51b5;
  }

  .bg-2 {
      background-color: #5677fc;
  }

  .bg-3 {
      background-color: #03a9f4;
  }
  .bg-4 {
      background-color: #ffc107;
  }
  .bg-5 {
      background-color: #ff9800;
  }
  .bg-6 {
      background-color: #ff5722;
  }
  .bg-7 {
      background-color: #e91e63;
  }
  .bg-8 {
      background-color: #9c27b0;
  }
  .bg-9 {
      background-color: #009688;
  }
  .bg-10 {
      background-color: #259b24;
  }
  .bg-11 {
      background-color: #8bc34a;
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
              <h4>OPD Mornitor Huataphanhospital <br>
                ณ วันที่ <font style="color:red;">{{DateThai(date('Y-m-d'))}}</font> เวลา: <font style="color:red;"><span id="realtime-clock"></span></font> 
                ทั้งหมด : <font style="color:red;">{{$total}}</font> Visit | 
                ปิดสิทธิ สปสช : <font style="color:red;">{{$endpoint}}</font> Visit               
                <!-- ปุ่มเรียก Modal -->
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#nhsoModal">
                  ดึงปิดสิทธิ สปสช.
                </button>
              </h4>
            </div>
            <div class="col-2 mt-2" align="right">
              <h4><a class="btn btn-danger text-center" href="{{ url('/dashboard/ipd_mornitor') }}" > <i class="bi bi-trash me-1"></i><strong>IPD Mornitor</strong></a></h4>
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
            OFC : รูดบัตร
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$ofc}} : {{$ofc_edc}}</h1>
              <p class="card-text">
                <a href="{{ url('/dashboard/opd_mornitor_ofc') }}"  target="_blank" class="text-white" style="text-decoration: none; "> more detail...</a>
              </p>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card text-white bg-2 mb-3" style="max-width: 18rem;">
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            ไม่ขอ AuthenCode 
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$non_authen}} </h1>
            <p class="card-text">
              <a href="{{ url('/dashboard/opd_mornitor_non_authen') }}"  target="_blank" class="text-white" style="text-decoration: none;"> more detail...</a>
            </p>
          </div>
        </div>
      </div>        
      <div class="col-sm-3">
        <div class="card text-white bg-3 mb-3" style="max-width: 18rem;">
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            ไม่บันทึกสถานพยาบาลหลัก
          </div>
          <div class="card-body">
            <h1 class="card-title text-center"> {{$non_hospmain}}</h1>
            <p class="card-text">
              <a href="{{ url('/dashboard/opd_mornitor_non_hospmain') }}"  target="_blank" class="text-white" style="text-decoration: none;"> more detail...</a>
            </p>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card text-white bg-4 mb-3" style="max-width: 18rem;">
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            TB : ปิดสิทธิ 
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$tb}} : {{$tb_endpoint}}</h1>
            <p class="card-text">
              <a href="{{ url('/dashboard/opd_mornitor_tb') }}"  target="_blank" class="text-white" style="text-decoration: none;"> more detail...</a>
            </p>
          </div>
        </div>
      </div>
    </div> <!-- //row -->

    <div class="row" align="center">
      <div class="col-sm-3">
        <div class="card text-white bg-5 mb-3" style="max-width: 18rem;" >
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            UC Anywhere : ปิดสิทธิ 
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$op_anywhere}} : {{$op_anywhere_endpoint}}</h1>
            <p class="card-text">
              <a href="{{ url('/dashboard/opd_mornitor_opanywhere') }}"  target="_blank" class="text-white" style="text-decoration: none; "> more detail...</a>
            </p>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card text-white bg-6 mb-3" style="max-width: 18rem;" >
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            UC ฟอกไต : ปิดสิทธิ
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$kidney}} : {{$kidney_endpoint}}</h1>
            <p class="card-text">
              <a href="{{ url('/dashboard/opd_mornitor_kidney') }}"  target="_blank" class="text-white" style="text-decoration: none; "> more detail...</a>
            </p>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card text-white bg-7 mb-3" style="max-width: 18rem;" >
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            UC บริการเฉพาะ : ปิดสิทธิ 
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$ucop_cr}} : {{$ucop_cr_endpoint}} </h1>
            <p class="card-text">
              <a href="{{ url('/dashboard/opd_mornitor_ucop_cr') }}"  target="_blank" class="text-white" style="text-decoration: none; "> more detail...</a>
            </p>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card text-white bg-8 mb-3" style="max-width: 18rem;" >
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            PPFS : ปิดสิทธิ 
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$ppfs}} : {{$ppfs_endpoint}} </h1>
            <p class="card-text">
              <a href="{{ url('/dashboard/opd_mornitor_ppfs') }}"  target="_blank" class="text-white" style="text-decoration: none; "> more detail...</a>
            </p>
          </div>
        </div>
      </div>
    </div> <!-- //row --> 
    <div class="row" align="center">
      <div class="col-sm-3">
        <div class="card text-white bg-9 mb-3" style="max-width: 18rem;" >
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            Homeward : AuthenCode
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$homeward}} : {{$homeward_auth}}</h1>
            <p class="card-text">
              <a href="{{ url('/dashboard/opd_mornitor_homeward') }}"  target="_blank" class="text-white" style="text-decoration: none; "> more detail...</a>
            </p>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card text-white bg-10 mb-3" style="max-width: 18rem;" >
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            แพทย์แผนไทย : ปิดสิทธิ
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$healthmed}} : {{$healthmed_endpoint}}</h1>
            <p class="card-text">
              <a href="{{ url('/dashboard/opd_mornitor_healthmed') }}"  target="_blank" class="text-white" style="text-decoration: none; "> more detail...</a>
            </p>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card text-white bg-11 mb-3" style="max-width: 18rem;" >
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            ยาสมุนไพร : ปิดสิทธิ
          </div>
          <div class="card-body">
            <h1 class="card-title text-center">{{$uc_herb}} : {{number_format($uc_herb_endpoint)}}</h1>
            <p class="card-text">
              <a href="{{ url('/dashboard/opd_mornitor_herb') }}"  target="_blank" class="text-white" style="text-decoration: none; "> more detail...</a>
            </p>
          </div>
        </div>
      </div>
    </div> <!-- //row -->
    <hr>
    <div class="row" align="center">
      <div class="col-sm-4"> 
        <div class="card border-danger" style="max-width: 25rem;">
          <div class="card-header bg-danger text-white">เวรดึก</div>
            <div class="card-body">
              <table class="table table-hover text-primary">
                <tbody>   
                  <tr>
                    <td><li>ตรวจโรคทั่วไป : <strong>{{$opd_n}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>อุบัติเหตุฉุกเฉิน : <strong>{{$er_n}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>กายภาพบำบัด : <strong>{{$physic_n}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>แพทย์แผนไทย : <strong>{{$health_med_n}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>ทันตกรรม : <strong>{{$dent_n}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>ฟอกไต รพ : <strong>{{$kidney_hos_n}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>ฟอกไต เอกชน : <strong>{{$kidney_os_n}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>ฝากครรภ์ : <strong>{{$anc_n}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>Admit : <strong>{{$admit_n}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>Refer : <strong>{{$refer_n}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>ผ่าตัด : <strong>{{$operation_n}}</strong></li></td>
                  </tr>
                </tbody>
              </table>
            </div> 
        </div>        
      </div>  
      <div class="col-sm-4"> 
        <div class="card border-success" style="max-width: 25rem;">
          <div class="card-header bg-success text-white">เวรเช้า</div>
            <div class="card-body">
              <table class="table table-hover text-primary">
                <tbody>   
                  <tr>
                    <td><li>ตรวจโรคทั่วไป : <strong>{{$opd_m}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>อุบัติเหตุฉุกเฉิน : <strong>{{$er_m}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>กายภาพบำบัด : <strong>{{$physic_m}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>แพทย์แผนไทย : <strong>{{$health_med_m}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>ทันตกรรม : <strong>{{$dent_m}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>ฟอกไต รพ : <strong>{{$kidney_hos_m}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>ฟอกไต เอกชน : <strong>{{$kidney_os_m}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>ฝากครรภ์ : <strong>{{$anc_m}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>Admit : <strong>{{$admit_m}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>Refer : <strong>{{$refer_m}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>ผ่าตัด : <strong>{{$operation_m}}</strong></li></td>
                  </tr>
                </tbody>
              </table>
            </div> 
        </div>        
      </div>  
      <div class="col-sm-4"> 
        <div class="card border-warning" style="max-width: 25rem;">
          <div class="card-header bg-warning text-white">เวรบ่าย</div>
            <div class="card-body">
              <table class="table table-hover text-primary">
                <tbody>   
                  <tr>
                    <td><li>ตรวจโรคทั่วไป : <strong>{{$opd_a}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>อุบัติเหตุฉุกเฉิน : <strong>{{$er_a}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>กายภาพบำบัด : <strong>{{$physic_a}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>แพทย์แผนไทย : <strong>{{$health_med_a}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>ทันตกรรม : <strong>{{$dent_a}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>ฟอกไต รพ : <strong>{{$kidney_hos_a}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>ฟอกไต เอกชน : <strong>{{$kidney_os_a}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>ฝากครรภ์ : <strong>{{$anc_a}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>Admit : <strong>{{$admit_a}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>Refer : <strong>{{$refer_a}}</strong></li></td>
                  </tr>
                  <tr>
                    <td><li>ผ่าตัด : <strong>{{$operation_a}}</strong></li></td>
                  </tr>
                </tbody>
              </table>
            </div> 
        </div>        
      </div>  
    </div> <!-- //row --> 
    <hr>
    <div class="row" align="center">
      <div id="op_visit" style="width: 100%; height: 200px"><font color="#4154f1"><strong>OP Visit วันที่ {{DateThai(date('Y-m-d'))}}</strong></font></div>
    </div> <!-- //row --> 
    <hr>
  </div> <!-- //container --> 

  <!-- Modal -->
  <div class="modal fade" id="nhsoModal" tabindex="-1" aria-labelledby="nhsoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center">
        <div class="modal-header">
          <h5>เลือกวันที่เข้ารับบริการ</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form id="nhsoForm">
          <div class="modal-body">         
            <input type="date" id="vstdate" name="vstdate" class="form-control"  value="{{ date('Y-m-d') }}" required>

            <div id="loadingSpinner" class="mt-4 d-none">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2">กำลังดึงข้อมูลจาก สปสช....</p>
            </div>

            <div id="resultMessage" class="mt-3 d-none"></div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">ดึงข้อมูล</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
          </div>
        </form>
      </div>
    </div>
  </div>

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

    // รีโหลดหน้าทุก 1.5 นาที (90000 ms)
    setTimeout(function() {
        location.reload();
    }, 90000);
</script>

</body>

</body>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("nhsoForm");
    const spinner = document.getElementById("loadingSpinner");
    const resultMessage = document.getElementById("resultMessage");
    const nhsoModal = document.getElementById('nhsoModal');

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        spinner.classList.remove("d-none");
        resultMessage.classList.add("d-none");
        resultMessage.innerHTML = "";

        const formData = new FormData(form);

        fetch("{{ url('medicalrecord_opd/nhso_endpoint_pull') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: formData
        })
        .then(response => {
            spinner.classList.add("d-none");
            if (!response.ok) throw new Error("โหลดล้มเหลว");
            return response.json();
        })
        .then(data => {
            resultMessage.classList.remove("d-none");
            resultMessage.classList.add("text-success");
            resultMessage.innerHTML = "✅ " + (data.message || "ดึงข้อมูลสำเร็จ");
        })
        .catch(err => {
            resultMessage.classList.remove("d-none");
            resultMessage.classList.add("text-danger");
            resultMessage.innerHTML = "❌ ดึงข้อมูลล้มเหลว";
        });
    });

    nhsoModal.addEventListener('hide.bs.modal', function () {
        // ✅ Redirect ไปหน้า /home เมื่อปิด Modal
        window.location.href = "{{ url('/dashboard/opd_mornitor') }}";
    });
});
</script>

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#op_visit"), {
      series: [{
        name: 'OPvisit',
        data: <?php echo json_encode($op_visit); ?>,
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
        type: 'datetime',
        categories: <?php echo json_encode($vstdate); ?>
      },
      tooltip: {
        x: {
          format: 'dd/MM/yy HH:mm'
        },
      }
    }).render();
  });
</script>

</html>
