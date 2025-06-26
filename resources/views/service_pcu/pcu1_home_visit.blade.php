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
        <div class="card-header bg-primary bg-opacity-75 text-white">รายงานการเยี่ยมบ้าน-ประชากรในเขตรับผิดชอบ ปีงบประมาณ {{$budget_year}} </div>
        <canvas id="home_visit_month" style="width: 100%; height: 350px"></canvas>             
      </div>      
    </div>   
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">รายงานการเยี่ยมบ้าน-ประชากรในเขตรับผิดชอบ 5 ปีงบประมาณย้อนหลัง </div>
        <div id="home_visit_year" style="width: 100%; height: 350px"></div>             
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
                <div class="card-header bg-primary bg-opacity-75 text-white">รายงานการเยี่ยมบ้าน-ประชากรในเขตรับผิดชอบ แยกหมู่บ้าน ปีงบประมาณ {{ $budget_year }}</div>
                <div id="home_visit_moo" style="width: 100%; height: 400px"></div>    
            </div>
        </div>              
    </div>
</div>  
<br>
<!-- row -->
<div class="container-fluid">  
    <div class="card">         
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>รายชื่อเยี่ยมบ้าน-ประชากรในเขตรับผิดชอบ ปีงบประมาณ {{ $budget_year }}</strong></div>      
        <div class="card-body">
            <table id="home_visit_list" class="table table-bordered table-striped my-3">
                <thead>
                <tr class="table-secondary">
                    <th class="text-center">ลำดับ</th>
                    <th class="text-center">หมู่บ้าน</th>
                    <th class="text-center">บ้านเลขที่</th>
                    <th class="text-center">HN</th>
                    <th class="text-center">ชื่อ-สกุล</th>    
                    <th class="text-center">วินิฉัย</th>                 
                    <th class="text-center">วันที่ออกเยี่ยม</th>
                    <th class="text-center">ผู้เยี่ยม</th>
                    <th class="text-center">บันทึกการเยี่ยม</th>
                    <th class="text-center">อาการ</th>
                    <th class="text-center">การพยาบาล</th>
                    <th class="text-center">คำแนะนำ</th>            
                </tr>     
                </thead> 
                <?php $count = 1 ; ?> 
                @foreach($home_visit_list as $row)          
                <tr>
                    <td align="center">{{ $count }}</td> 
                    <td align="left">{{ $row->village_name }}</td>
                    <td align="left">{{ $row->address }}</td>
                    <td align="center">{{ $row->patient_hn }}</td>
                    <td align="left">{{ $row->ptname }}</td>
                    <td align="left">{{ $row->dx1 }}</td>
                    <td align="left">{{ DateThai($row->visit_date) }}</td>      
                    <td align="left">{{ $row->visit_staff }}</td>
                    <td align="left">{{ $row->visit_note }}</td> 
                    <td align="left">{{ $row->visit_problem }}</td>
                    <td align="left">{{ $row->visit_service }}</td>
                    <td align="left">{{ $row->visit_advice }}</td> 
                </tr>                
                <?php $count++; ?>
                @endforeach  
            </table> 
        </div>      
    </div>
 </div>
 <br>
@endsection
<!-- Datatable -->
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#home_visit_list').DataTable();
    });
</script>
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>


<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#home_visit_month'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($home_visit_m); ?>,
          datasets: [{
            label: 'ครั้ง',
            data: <?php echo json_encode($home_visit_visit_m); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          },{
            label: 'คน',
            data: <?php echo json_encode($home_visit_hn_m); ?>,
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
<!-- Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#home_visit_year"), {
            
            series: [{
                name: 'ครั้ง',
                data: <?php echo json_encode($home_visit_visit_y); ?>,
                    },
                    {
                name: 'คน',
                data: <?php echo json_encode($home_visit_hn_y); ?>,
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
                categories: <?php echo json_encode($home_visit_y); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
<!-- Column Chart -->
<script>
        document.addEventListener("DOMContentLoaded", () => {
          new ApexCharts(document.querySelector("#home_visit_moo"), {
            series: [{
              name: 'รัตนวารี หมู่ 1',
              data: <?php echo json_encode($home_visit_moo1); ?>,
            }, {
              name: 'เสียว หมู่ 2',
              data: <?php echo json_encode($home_visit_moo2); ?>,
            }, {
              name: 'เสียว หมู่ 3',
              data: <?php echo json_encode($home_visit_moo3); ?>,
            }, {
              name: 'โต่งโต้น หมู่ 4',
              data: <?php echo json_encode($home_visit_moo4); ?>,
            }, {
              name: 'หนองเดิ่น หมู่ 5',
              data: <?php echo json_encode($home_visit_moo5); ?>,
            }, {
              name: 'หนองแสง หมู่ 6',
              data: <?php echo json_encode($home_visit_moo6); ?>,
            }, {
              name: 'รัตนวารี หมู่ 7',
              data: <?php echo json_encode($home_visit_moo7); ?>,
            }],
            chart: {
              type: 'bar',
              height: 400,
              background: '#eff7ff'
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
              categories: <?php echo json_encode($home_visit_m); ?>,
            },
            yaxis: {
              title: {
                text: 'จำนวนครั้ง'
              }
            },
            fill: {
              opacity: 1
            },
            tooltip: {
              y: {
                formatter: function(val) {
                  return val + " ครั้ง"
                }
              }
            }
          }).render();
        });
</script>
<!-- End Column Chart -->

