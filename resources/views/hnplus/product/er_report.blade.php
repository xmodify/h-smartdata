@extends('layouts.hnplus')


@section('content')
<div class="container-fluid">
    <h5 class="alert alert-primary"><strong>รายงานผลิตภาพทางการพยาบาลแผนกอุบัติเหตุ-ฉุกเฉิน ER</strong></h5>  
</div> 
<div class="container-fluid">  
    <form method="POST" enctype="multipart/form-data">
        @csrf            
        <div class="row" >
                <label class="col-md-3 col-form-label text-md-end my-1">{{ __('วันที่') }}</label>
            <div class="col-md-2">
                <input type="date" name="start_date" class="form-control my-1" placeholder="Date" value="{{ $start_date }}" > 
            </div>
                <label class="col-md-1 col-form-label text-md-end my-1">{{ __('ถึง') }}</label>
            <div class="col-md-2">
                <input type="date" name="end_date" class="form-control my-1" placeholder="Date" value="{{ $end_date }}" > 
            </div>                     
            <div class="col-md-1" >                            
                <button type="submit" class="btn btn-primary my-1 ">{{ __('ค้นหา') }}</button>
            </div>
        </div>
    </form>    
</div>
<!--row-->
<div class="container-fluid"> 
    <div class="card">
        <div class="card-header text-white" style="background-color:#23A7A7;"><strong>รายงานสรุปผลิตภาพทางการพยาบาล วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
        <table class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-primary">
                        <th class="text-center">เวร</th>
                        <th class="text-center">ผู้ป่วยในเวร</th>
                        <th class="text-center">Emergent</th>
                        <th class="text-center">Urgent</th>
                        <th class="text-center">Acute illness</th>
                        <th class="text-center">Non Acute illness</th>
                        <th class="text-center">ชม.การพยาบาล</th>
                        <th class="text-center">อัตรากำลัง Oncall</th>
                        <th class="text-center">อัตรากำลังเสริม</th>
                        <th class="text-center">อัตรากำลังปกติ</th>
                        <th class="text-center">ชม.การทำงาน</th>
                        <th class="text-center">Productivity</th>
                        <th class="text-center">HHPUOS</th>
                        <th class="text-center">พยาบาลที่ต้องการ</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($er_product_summary as $row)
                    <tr>
                        <td align="right">{{ $row->shift_time }} {{ $row->shift_time_sum }} เวร</td>
                        <td align="right">{{ $row->patient_all }}</td>
                        <td align="right">{{ $row->emergent }}</td>
                        <td align="right">{{ $row->urgent }}</td>
                        <td align="right">{{ $row->acute_illness }}</td> 
                        <td align="right">{{ $row->non_acute_illness }}</td> 
                        <td align="right">{{ number_format($row->patient_hr,2) }}</td> 
                        <td align="right">{{ $row->nurse_oncall }}</td> 
                        <td align="right">{{ $row->nurse_partime }}</td> 
                        <td align="right">{{ $row->nurse_fulltime }}</td> 
                        <td align="right">{{ number_format($row->nurse_hr,2) }}</td> 
                        <td align="right">{{ number_format($row->productivity,2) }}</td> 
                        <td align="right">{{ number_format($row->hhpuos,2) }}</td> 
                        <td align="right">{{ number_format($row->nurse_shift_time,2) }}</td>
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
    <div class="col-md-12">
      <div class="card">
        <div class="card-header text-white" style="background-color:#23A7A7;">Productivity วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <canvas id="productivity" style="width: 100%; height: 350px"></canvas>
      </div>
    </div>    
  </div>
</div>
<br>
<!--row-->
<div class="container-fluid"> 
    <div class="card">
        <div class="card-header text-white" style="background-color:#23A7A7;"><strong>การบันทึกข้อมูลผลิตภาพทางการพยาบาล วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">
            <div class="row mb-3"> 
                @if ($message = Session::get('danger'))
                <div class="alert alert-danger text-center">
                <h5><strong>{{ $message }}</strong></h5>
                </div>
                @endif
            </div>     
            <table id="nurse_productivity_er" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-primary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">วันที่</th>
                        <th class="text-center">เวร</th>
                        <th class="text-center">ผู้ป่วยในเวร</th>
                        <th class="text-center">Emergent</th>
                        <th class="text-center">Urgent</th>
                        <th class="text-center">Acute illness</th>
                        <th class="text-center">Non Acute illness</th>
                        <th class="text-center">ชม.การพยาบาล</th>
                        <th class="text-center">อัตรากำลัง Oncall</th>
                        <th class="text-center">อัตรากำลังเสริม</th>
                        <th class="text-center">อัตรากำลังปกติ</th>
                        <th class="text-center">ชม.การทำงาน</th>
                        <th class="text-center">Productivity</th>
                        <th class="text-center">HHPUOS</th>
                        <th class="text-center">พยาบาลที่ต้องการ</th>
                        <th class="text-center">ผู้บันทึก</th>
                        <th class="text-center">หมายเหตุ</th>
                        @if(Auth::user()->username == $del_product)  
                        <th class="text-center">Action</th>
                        @endif
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($er_product as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="right">{{ DateThai($row->report_date) }}</td>   
                        <td align="right">{{ $row->shift_time }}</td>
                        <td align="right">{{ $row->patient_all }}</td>
                        <td align="right">{{ $row->emergent }}</td>
                        <td align="right">{{ $row->urgent }}</td>
                        <td align="right">{{ $row->acute_illness }}</td> 
                        <td align="right">{{ $row->non_acute_illness }}</td> 
                        <td align="right">{{ number_format($row->patient_hr,2) }}</td> 
                        <td align="right">{{ $row->nurse_oncall }}</td> 
                        <td align="right">{{ $row->nurse_partime }}</td> 
                        <td align="right">{{ $row->nurse_fulltime }}</td> 
                        <td align="right">{{ number_format($row->nurse_hr,2) }}</td> 
                        <td align="right">{{ number_format($row->productivity,2) }}</td> 
                        <td align="right">{{ number_format($row->hhpuos,2) }}</td> 
                        <td align="right">{{ number_format($row->nurse_shift_time,2) }}</td> 
                        <td align="left">{{ $row->recorder }}</td> 
                        <td align="left">{{ $row->note }}</td>                  
                        @if(Auth::user()->username == $del_product)   
                        <td class="text-center">
                            <form action="{{ url('hnplus/product/er_product_delete/'.$row->id) }}" method="POST" onsubmit="return confirm('ต้องการลบข้อมูล {{ DateThai($row->report_date) }} {{ $row->shift_time }} Product {{ number_format($row->productivity,2) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                        @endif      
                    </tr>
                <?php $count++; ?>
                @endforeach
            </table>
        </div>         
    </div>
</div>
@endsection

<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#nurse_productivity_er').DataTable();
    });
</script>

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>

<!-- Bar Chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.querySelector('#productivity');
    
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($report_date); ?>,
        datasets: [
          {
            label: 'เวรดึก',
            data: <?php echo json_encode($night); ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgb(255, 99, 132)',
            borderWidth: 1
          },
          {
            label: 'เวรเช้า',
            data: <?php echo json_encode($morning); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgb(75, 192, 192)',
            borderWidth: 1
          },
          {
            label: 'เวรบ่าย',
            data: <?php echo json_encode($afternoon); ?>,
            backgroundColor: 'rgba(255, 159, 64, 0.2)',
            borderColor: 'rgb(255, 159, 64)',
            borderWidth: 1
          }
        ]
      },
      options: {
        plugins: {
          datalabels: {
            anchor: 'end',
            align: 'top',
            color: '#000',
            font: {
              weight: 'bold'
            },
            formatter: (value) => value // แสดงค่าตัวเลขบนแท่ง
          },
          legend: {
            position: 'top'
          },
          title: {
            display: true,
            text: 'สรุปจำนวนเวรแต่ละช่วงเวลา'
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        }
      },
      plugins: [ChartDataLabels] // 🔹 เปิดใช้งานปลั๊กอินแสดงค่า
    });
  });
</script>
<!-- End Bar CHart -->