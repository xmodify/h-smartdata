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
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนการให้บริการ ปีงบประมาณ {{$budget_year}}</div>
        <canvas id="count" style="width: 100%; height: 300px"></canvas>           
      </div>      
    </div> 
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวน(ครั้ง)การให้บริการแยกรายสิทธิ ปีงบประมาณ {{$budget_year}}</div>
        <canvas id="visit_pttype" style="width: 100%; height: 300px"></canvas>          
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
        <div class="card-header bg-primary bg-opacity-75 text-white">ค่าฟอกไต ปีงบประมาณ {{$budget_year}}</div>
        <canvas id="dialysis" style="width: 100%; height: 300px"></canvas>          
      </div>      
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ค่าฟอกไตแยกรายสิทธิ ปีงบประมาณ {{$budget_year}}</div>
        <canvas id="dialysis_pttype" style="width: 100%; height: 300px"></canvas>           
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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนการให้บริการ ปีงบประมาณ {{$budget_year}}</div>
        <div style="overflow-x:auto;">  
          <table class="table table-bordered table-striped">
            <thead>
              <tr class="table-success">
                  <th class="text-center text-primary">เดือน</th>
                  <th class="text-center text-primary">HN</th> 
                  <th class="text-center text-primary">Visit</th> 
                  <th class="text-center text-primary">ค่าฟอก</th> 
                  <th class="text-center text-primary">Visit UCS</th> 
                  <th class="text-center text-primary">ค่าฟอก UCS</th> 
                  <th class="text-center text-primary">Visit OFC</th>  
                  <th class="text-center text-primary">ค่าฟอก OFC</th>  
                  <th class="text-center text-primary">Visit LGO</th>  
                  <th class="text-center text-primary">ค่าฟอก LGO</th>  
                  <th class="text-center text-primary">Visit Outher</th> 
                  <th class="text-center text-primary">ค่าฟอก Outher</th>             
              </tr>  
            </thead> 
            <tbody>
              <?php $sum_hn = 0 ; ?>   
              <?php $sum_visit = 0 ; ?> 
              <?php $sum_dialysis = 0 ; ?> 
              <?php $sum_visit_ucs = 0 ; ?>
              <?php $sum_dialysis_ucs = 0 ; ?> 
              <?php $sum_visit_ofc = 0 ; ?>
              <?php $sum_dialysis_ofc = 0 ; ?>  
              <?php $sum_visit_lgo = 0 ; ?> 
              <?php $sum_dialysis_lgo = 0 ; ?> 
              <?php $sum_visit_outher = 0 ; ?> 
              <?php $sum_dialysis_outher = 0 ; ?> 
              @foreach($kidney as $row) 
              <tr>
                <td align="center">{{ $row->month }}</td>
                <td align="right">{{ number_format($row->hn) }}</td>
                <td align="right">{{ number_format($row->visit) }}</td>
                <td align="right">{{ number_format($row->dialysis,2) }}</td>
                <td align="right">{{ number_format($row->visit_ucs) }}</td>
                <td align="right">{{ number_format($row->dialysis_ucs,2) }}</td>
                <td align="right">{{ number_format($row->visit_ofc) }}</td>
                <td align="right">{{ number_format($row->dialysis_ofc,2) }}</td>
                <td align="right">{{ number_format($row->visit_lgo) }}</td>
                <td align="right">{{ number_format($row->dialysis_lgo,2) }}</td>
                <td align="right">{{ number_format($row->visit_outher) }}</td>
                <td align="right">{{ number_format($row->dialysis_outher,2) }}</td>
              </tr>
              <?php $sum_hn += $row->hn ; ?>
              <?php $sum_visit += $row->visit ; ?>
              <?php $sum_dialysis += $row->dialysis ; ?>
              <?php $sum_visit_ucs += $row->visit_ucs ; ?>
              <?php $sum_dialysis_ucs += $row->dialysis_ucs ; ?>
              <?php $sum_visit_ofc += $row->visit_ofc ; ?>
              <?php $sum_dialysis_ofc += $row->dialysis_ofc ; ?>
              <?php $sum_visit_lgo += $row->visit_lgo ; ?>
              <?php $sum_dialysis_lgo += $row->dialysis_lgo ; ?>
              <?php $sum_visit_outher += $row->visit_outher ; ?>
              <?php $sum_dialysis_outher += $row->dialysis_outher ; ?>
              @endforeach
            </tbody>
            <tbody>
              <tr>
                <td align="center"><strong>รวม</strong></td>
                <td align="right"><strong>{{ number_format($sum_hn)}}</strong></td>             
                <td align="right"><strong>{{ number_format($sum_visit)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_dialysis,2)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_visit_ucs)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_dialysis_ucs,2)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_visit_ofc)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_dialysis_ofc,2)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_visit_lgo)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_dialysis_lgo,2)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_visit_outher)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_dialysis_outher,2)}}</strong></td>
              </tr>
            </tbody>
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
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ค่า Lab ปีงบประมาณ {{$budget_year}}</div>
        <canvas id="lab" style="width: 100%; height: 300px"></canvas>          
      </div>      
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ค่า Lab แยกรายสิทธิ ปีงบประมาณ {{$budget_year}}</div>
        <canvas id="lab_pttype" style="width: 100%; height: 300px"></canvas>           
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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนการให้บริการ ปีงบประมาณ {{$budget_year}}</div>
        <div style="overflow-x:auto;">  
          <table class="table table-bordered table-striped">
            <thead>
              <tr class="table-success">
                  <th class="text-center text-primary">เดือน</th>
                  <th class="text-center text-primary">HN</th> 
                  <th class="text-center text-primary">Visit</th> 
                  <th class="text-center text-primary">ค่า Lab</th> 
                  <th class="text-center text-primary">Visit UCS</th> 
                  <th class="text-center text-primary">ค่า Lab UCS</th> 
                  <th class="text-center text-primary">Visit OFC</th>  
                  <th class="text-center text-primary">ค่า lab OFC</th>  
                  <th class="text-center text-primary">Visit LGO</th>  
                  <th class="text-center text-primary">ค่า Lab LGO</th>  
                  <th class="text-center text-primary">Visit Outher</th> 
                  <th class="text-center text-primary">ค่า Lab Outher</th>             
              </tr>  
            </thead> 
            <tbody>
              <?php $sum_hn = 0 ; ?>   
              <?php $sum_visit = 0 ; ?> 
              <?php $sum_lab = 0 ; ?> 
              <?php $sum_visit_ucs = 0 ; ?>
              <?php $sum_lab_ucs = 0 ; ?> 
              <?php $sum_visit_ofc = 0 ; ?>
              <?php $sum_lab_ofc = 0 ; ?>  
              <?php $sum_visit_lgo = 0 ; ?> 
              <?php $sum_lab_lgo = 0 ; ?> 
              <?php $sum_visit_outher = 0 ; ?> 
              <?php $sum_lab_outher = 0 ; ?> 
              @foreach($kidney as $row) 
              <tr>
                <td align="center">{{ $row->month }}</td>
                <td align="right">{{ number_format($row->hn) }}</td>
                <td align="right">{{ number_format($row->visit) }}</td>
                <td align="right">{{ number_format($row->lab,2) }}</td>
                <td align="right">{{ number_format($row->visit_ucs) }}</td>
                <td align="right">{{ number_format($row->lab_ucs,2) }}</td>
                <td align="right">{{ number_format($row->visit_ofc) }}</td>
                <td align="right">{{ number_format($row->lab_ofc,2) }}</td>
                <td align="right">{{ number_format($row->visit_lgo) }}</td>
                <td align="right">{{ number_format($row->lab_lgo,2) }}</td>
                <td align="right">{{ number_format($row->visit_outher) }}</td>
                <td align="right">{{ number_format($row->lab_outher,2) }}</td>
              </tr>
              <?php $sum_hn += $row->hn ; ?>
              <?php $sum_visit += $row->visit ; ?>
              <?php $sum_lab += $row->lab ; ?>
              <?php $sum_visit_ucs += $row->visit_ucs ; ?>
              <?php $sum_lab_ucs += $row->lab_ucs ; ?>
              <?php $sum_visit_ofc += $row->visit_ofc ; ?>
              <?php $sum_lab_ofc += $row->lab_ofc ; ?>
              <?php $sum_visit_lgo += $row->visit_lgo ; ?>
              <?php $sum_lab_lgo += $row->lab_lgo ; ?>
              <?php $sum_visit_outher += $row->visit_outher ; ?>
              <?php $sum_lab_outher += $row->lab_outher ; ?>
              @endforeach
            </tbody>
            <tbody>
              <tr>
                <td align="center"><strong>รวม</strong></td>
                <td align="right"><strong>{{ number_format($sum_hn)}}</strong></td>             
                <td align="right"><strong>{{ number_format($sum_visit)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_lab,2)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_visit_ucs)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_lab_ucs,2)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_visit_ofc)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_lab_ofc,2)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_visit_lgo)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_lab_lgo,2)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_visit_outher)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_lab_outher,2)}}</strong></td>
              </tr>
            </tbody>
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
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ค่ายา ปีงบประมาณ {{$budget_year}}</div>
        <canvas id="drug" style="width: 100%; height: 300px"></canvas>          
      </div>      
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ค่ายา แยกรายสิทธิ ปีงบประมาณ {{$budget_year}}</div>
        <canvas id="drug_pttype" style="width: 100%; height: 300px"></canvas>           
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
        <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนการให้บริการ ปีงบประมาณ {{$budget_year}}</div>
        <div style="overflow-x:auto;">  
          <table class="table table-bordered table-striped">
            <thead>
              <tr class="table-success">
                  <th class="text-center text-primary">เดือน</th>
                  <th class="text-center text-primary">HN</th> 
                  <th class="text-center text-primary">Visit</th> 
                  <th class="text-center text-primary">ค่ายา</th> 
                  <th class="text-center text-primary">Visit UCS</th> 
                  <th class="text-center text-primary">ค่ายา UCS</th> 
                  <th class="text-center text-primary">Visit OFC</th>  
                  <th class="text-center text-primary">ค่ายา OFC</th>  
                  <th class="text-center text-primary">Visit LGO</th>  
                  <th class="text-center text-primary">ค่ายา LGO</th>  
                  <th class="text-center text-primary">Visit Outher</th> 
                  <th class="text-center text-primary">ค่ายา Outher</th>             
              </tr>  
            </thead> 
            <tbody>
              <?php $sum_hn = 0 ; ?>   
              <?php $sum_visit = 0 ; ?> 
              <?php $sum_drug = 0 ; ?> 
              <?php $sum_visit_ucs = 0 ; ?>
              <?php $sum_drug_ucs = 0 ; ?> 
              <?php $sum_visit_ofc = 0 ; ?>
              <?php $sum_drug_ofc = 0 ; ?>  
              <?php $sum_visit_lgo = 0 ; ?> 
              <?php $sum_drug_lgo = 0 ; ?> 
              <?php $sum_visit_outher = 0 ; ?> 
              <?php $sum_drug_outher = 0 ; ?> 
              @foreach($kidney as $row) 
              <tr>
                <td align="center">{{ $row->month }}</td>
                <td align="right">{{ number_format($row->hn) }}</td>
                <td align="right">{{ number_format($row->visit) }}</td>
                <td align="right">{{ number_format($row->drug,2) }}</td>
                <td align="right">{{ number_format($row->visit_ucs) }}</td>
                <td align="right">{{ number_format($row->drug_ucs,2) }}</td>
                <td align="right">{{ number_format($row->visit_ofc) }}</td>
                <td align="right">{{ number_format($row->drug_ofc,2) }}</td>
                <td align="right">{{ number_format($row->visit_lgo) }}</td>
                <td align="right">{{ number_format($row->drug_lgo,2) }}</td>
                <td align="right">{{ number_format($row->visit_outher) }}</td>
                <td align="right">{{ number_format($row->drug_outher,2) }}</td>
              </tr>
              <?php $sum_hn += $row->hn ; ?>
              <?php $sum_visit += $row->visit ; ?>
              <?php $sum_drug += $row->drug ; ?>
              <?php $sum_visit_ucs += $row->visit_ucs ; ?>
              <?php $sum_drug_ucs += $row->drug_ucs ; ?>
              <?php $sum_visit_ofc += $row->visit_ofc ; ?>
              <?php $sum_drug_ofc += $row->drug_ofc ; ?>
              <?php $sum_visit_lgo += $row->visit_lgo ; ?>
              <?php $sum_drug_lgo += $row->drug_lgo ; ?>
              <?php $sum_visit_outher += $row->visit_outher ; ?>
              <?php $sum_drug_outher += $row->drug_outher ; ?>
              @endforeach
            </tbody>
            <tbody>
              <tr>
                <td align="center"><strong>รวม</strong></td>
                <td align="right"><strong>{{ number_format($sum_hn)}}</strong></td>             
                <td align="right"><strong>{{ number_format($sum_visit)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_drug,2)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_visit_ucs)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_drug_ucs,2)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_visit_ofc)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_drug_ofc,2)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_visit_lgo)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_drug_lgo,2)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_visit_outher)}}</strong></td>
                <td align="right"><strong>{{ number_format($sum_drug_outher,2)}}</strong></td>
              </tr>
            </tbody>
          </table>  
        </div>    
      </div>      
    </div>         
  </div>
</div> 
<br>
@endsection
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>

<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#count'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($month); ?>,
          datasets: [{
            label: 'คน',
            data: <?php echo json_encode($hn); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          },
          {
            label: 'ครั้ง',
            data: <?php echo json_encode($visit); ?>,
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
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#visit_pttype'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($month); ?>,
          datasets: [{
            label: 'UCS',
            data: <?php echo json_encode($visit_ucs); ?>,
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)'
            ],
            borderColor: [
              'rgb(255, 99, 132)'
            ],
            borderWidth: 1
          },
          {
            label: 'ข้าราชการ',
            data: <?php echo json_encode($visit_ofc); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          }, 
          {
            label: 'อปท.',
            data: <?php echo json_encode($visit_lgo); ?>,
            backgroundColor: [
              'rgba(255, 205, 86, 0.2)'
            ],
            borderColor: [
              'rgb(255, 205, 86)'
            ],
            borderWidth: 1
          }, 
          {
            label: 'อื่น ๆ',
            data: <?php echo json_encode($visit_outher); ?>,
            backgroundColor: [
              'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
              'rgb(75, 192, 192)'
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
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#dialysis'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($month); ?>,
          datasets: [{
            label: 'ค่าฟอก',
            data: <?php echo json_encode($dialysis); ?>,
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
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#dialysis_pttype'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($month); ?>,
          datasets: [{
            label: 'UCS',
            data: <?php echo json_encode($dialysis_ucs); ?>,
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)'
            ],
            borderColor: [
              'rgb(255, 99, 132)'
            ],
            borderWidth: 1
          },
          {
            label: 'ข้าราชการ',
            data: <?php echo json_encode($dialysis_ofc); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          }, 
          {
            label: 'อปท.',
            data: <?php echo json_encode($dialysis_lgo); ?>,
            backgroundColor: [
              'rgba(255, 205, 86, 0.2)'
            ],
            borderColor: [
              'rgb(255, 205, 86)'
            ],
            borderWidth: 1
          }, 
          {
            label: 'อื่น ๆ',
            data: <?php echo json_encode($dialysis_outher); ?>,
            backgroundColor: [
              'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
              'rgb(75, 192, 192)'
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
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#lab'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($month); ?>,
          datasets: [{
            label: 'ค่า LAB',
            data: <?php echo json_encode($lab); ?>,
            backgroundColor: [
              'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
              'rgb(75, 192, 192)'
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
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#lab_pttype'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($month); ?>,
          datasets: [{
            label: 'UCS',
            data: <?php echo json_encode($lab_ucs); ?>,
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)'
            ],
            borderColor: [
              'rgb(255, 99, 132)'
            ],
            borderWidth: 1
          },
          {
            label: 'ข้าราชการ',
            data: <?php echo json_encode($lab_ofc); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          }, 
          {
            label: 'อปท.',
            data: <?php echo json_encode($lab_lgo); ?>,
            backgroundColor: [
              'rgba(255, 205, 86, 0.2)'
            ],
            borderColor: [
              'rgb(255, 205, 86)'
            ],
            borderWidth: 1
          }, 
          {
            label: 'อื่น ๆ',
            data: <?php echo json_encode($lab_outher); ?>,
            backgroundColor: [
              'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
              'rgb(75, 192, 192)'
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
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#drug'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($month); ?>,
          datasets: [{
            label: 'ค่ายา',
            data: <?php echo json_encode($drug); ?>,
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)'
            ],
            borderColor: [
              'rgb(255, 99, 132)'
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
<!-- Bar Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
      new Chart(document.querySelector('#drug_pttype'), {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($month); ?>,
          datasets: [{
            label: 'UCS',
            data: <?php echo json_encode($drug_ucs); ?>,
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)'
            ],
            borderColor: [
              'rgb(255, 99, 132)'
            ],
            borderWidth: 1
          },
          {
            label: 'ข้าราชการ',
            data: <?php echo json_encode($drug_ofc); ?>,
            backgroundColor: [
              'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
              'rgb(153, 102, 255)'
            ],
            borderWidth: 1
          }, 
          {
            label: 'อปท.',
            data: <?php echo json_encode($drug_lgo); ?>,
            backgroundColor: [
              'rgba(255, 205, 86, 0.2)'
            ],
            borderColor: [
              'rgb(255, 205, 86)'
            ],
            borderWidth: 1
          }, 
          {
            label: 'อื่น ๆ',
            data: <?php echo json_encode($drug_outher); ?>,
            backgroundColor: [
              'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
              'rgb(75, 192, 192)'
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






