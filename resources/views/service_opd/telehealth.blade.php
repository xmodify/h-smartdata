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
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนการให้บริการแพทย์ทางไกล Telehealth ปีงบประมาณ {{$budget_year}} </div>
                <canvas id="telehealth_month" style="width: 100%; height: 350px"></canvas>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนการให้บริการแพทย์ทางไกล Telehealth แยกคลินิก ปีงบประมาณ {{$budget_year}} </div>
                <div id="telehealth_clinic" style="width: 100%; height: 350px"></div>
            </div>
        </div>
    </div>
</div>
<br>
<!-- row -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">รายชื่อการให้บริการแพทย์ทางไกล Telehealth ปีงบประมาณ {{ $budget_year }}</div>
        <div class="card-body">
            <table id="telehealth_list" class="table table-bordered table-striped my-3">
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
                    <th class="text-center">นัดหมาย</th>
                    <th class="text-center">ห้องตรวจ</th>
                    <th class="text-center">คลินิก</th>
                    <th class="text-center">แพทย์ผู้ตรวจ</th>
                    <th class="text-center">AuthenCode</th>
                </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($telehealth_list as $row)
                <tr>
                    <td align="center">{{ $count }}</td>
                    <td align="right">{{ DateThai($row->vstdate) }}</td>                    
                    <td align="center">{{ $row->oqueue }}</td>
                    <td align="center">{{ $row->hn }}</td>
                    <td align="left">{{ $row->ptname }}</td>
                    <td align="center">{{ $row->age_y }}</td>
                    <td align="center">{{ $row->pttype }}</td>
                    <td align="right">{{ $row->pdx }}</td>
                    <td align="center">{{ $row->oapp }}</td>
                    <td align="left">{{ $row->oapp_dep }}</td>
                    <td align="left">{{ $row->oapp_clinic }}</td>
                    <td align="left">{{ $row->dx_doctor }}</td>
                    <td align="center">{{ $row->auth_code }}</td>
                </tr>
                <?php $count++; ?>
                @endforeach
            </table>
        </div>
    </div>
 </div>
@endsection
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
<!-- Datatable -->
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#telehealth_list').DataTable();
    });
</script>
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#telehealth_month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($telehealth_m); ?>,
          datasets: [{
            label: 'ครั้ง',
            data: <?php echo json_encode($telehealth_visit_m); ?>,
            backgroundColor: [
              'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
              'rgb(75, 192, 192)'
            ],
            borderWidth: 1
          },{
            label: 'คน',
            data: <?php echo json_encode($telehealth_hn_m); ?>,
            backgroundColor: [
              'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
              'rgb(255, 159, 64)'
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
      new ApexCharts(document.querySelector("#telehealth_clinic"), {
        series: <?php echo json_encode($telehealth_visit_clinic); ?>,
        chart: {
          height: 350,
          type: 'pie',
          toolbar: {
            show: true
          }
        },
        labels: <?php echo json_encode($telehealth_c_clinic); ?>,
      }).render();
    });
</script>
