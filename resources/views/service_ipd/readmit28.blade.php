@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">

@section('content')
<div class="container-fluid">
  <!-- row -->
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
<!-- row -->
    <div class="row justify-content-center">
        <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วย Re-Admit ภายใน 28 วันด้วยโรคเดิม ปีงบประมาณ {{$budget_year}} </div>
            <canvas id="readmit28_month" style="width: 100%; height: 350px"></canvas>
        </div>
        </div>
        <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้ป่วย Re-Admit ภายใน 28 วันด้วยโรคเดิม 5 ปีงบประมาณย้อนหลัง</div>
            <div id="readmit28_year" style="width: 100%; height: 350px"></div>
        </div>
        </div>
    </div>
<br>
<!-- row -->
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">10 อันดับโรค Re-Admit ภายใน 28 วันด้วยโรคเดิม ปีงบประมาณ {{ $budget_year }}</div>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-secondary">
                        <th class="text-center">อันดับ</th>
                        <th class="text-center">ชื่อโรค</th>
                        <th class="text-center">ชาย</th>
                        <th class="text-center">หญิง</th>
                        <th class="text-center">รวม</th>
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    @foreach($readmit28_top as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>
                        <td align="left">{{ $row->name }}</td>
                        <td align="right">{{ number_format($row->male) }}</td>
                        <td align="right">{{ number_format($row->female) }}</td>
                        <td align="right">{{ number_format($row->sum) }}</td>
                    </tr>
                    <?php $count++; ?>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
<br>
<!-- row -->
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">รายชื่อผู้ป่วย Re-Admit ภายใน 28 วันด้วยโรคเดิม ปีงบประมาณ {{$budget_year}} </div><br>
                <table id="readmit28_list" class="table table-bordered table-striped my-3">
                    <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>
                        <th class="text-center">HN</th>
                        <th class="text-center">ชื่อ-สกุล</th>
                        <th class="text-center">ICD10</th>
                        <th class="text-center">AN</th>
                        <th class="text-center">วันที่ Admit</th>
                        <th class="text-center">วันที่ D/C</th>
                        <th class="text-center">AN เดิม</th>
                        <th class="text-center">วันที่ Admit เดิม</th>
                        <th class="text-center">วันที่ D/C เดิม</th>
                        <th class="text-center">Readmit (วัน)</th>
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    @foreach($readmit28_list as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>
                        <td align="center">{{ $row->hn }}</td>
                        <td align="left">{{ $row->ptname }}</td>
                        <td align="center">{{ $row->icd10_1 }}</td>
                        <td align="center">{{ $row->AN_new }}</td>
                        <td align="right">{{ DateThai($row->regdate_AN_New) }}</td>
                        <td align="right">{{ DateThai($row->dcdate_AN_New) }}</td>
                        <td align="center">{{ $row->AN_old }}</td>
                        <td align="right">{{ DateThai($row->regdate_AN_Old) }}</td>
                        <td align="right">{{ DateThai($row->dcdate_AN_Old) }}</td>
                        <td align="right">{{ $row->ReAdmitDate }}</td>
                    </tr>
                    <?php $count++; ?>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

@endsection
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#readmit28_month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($readmit28_m); ?>,
          datasets: [{
            label: 'ชาย',
            data: <?php echo json_encode($readmit28_male_m); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          },{
            label: 'หญิง',
            data: <?php echo json_encode($readmit28_female_m); ?>,
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
<!-- Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#readmit28_year"), {

            series: [{
                name: 'ชาย',
                data: <?php echo json_encode($readmit28_male_y); ?>,
                    },
                    {
                name: 'หญิง',
                data: <?php echo json_encode($readmit28_female_y); ?>,
                    }],

            chart: {
                height: 330,
                type: 'area',
                toolbar: {
                show: false
                },
            },
            markers: {
                size: 4
            },
            colors: [ '#CC66FF','#FF9933'],
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
                categories: <?php echo json_encode($readmit28_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->

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
        $('#readmit28_list').DataTable();
    });
</script>

