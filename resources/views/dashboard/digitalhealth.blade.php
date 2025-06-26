<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >Digitalhealth Huataphanhospital</title>

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
</head>
<body>
<!-- row -->
<div class="container-fluid">
  <div class="card">
    <div class="card-header bg-primary bg-opacity-75 text-white">
    ข้อมูลการดำเนินงานตามนโยบาย 30 บาทรักษาทุกที่ด้วยบัตรประชาชนใบเดียว โรงพยาบาลหัวตะพาน ปีงบประมาณ {{$budget_year}}
    </div> 
    <div class="card-body">
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
      <br>
      <div class="row">        
        <div class="col-md-6"> 
          <div style="overflow-x:auto;">
            <table class="table table-striped table-bordered">
              <thead>
                <tr class="table-primary">
                  <th class="text-center" rowspan="2"><p align="center">เดือน</p></th>    
                  <th class="text-center" colspan="3"><p align="center"><a href="https://bdh-service.moph.go.th/data-opdvisit" target="blank">ข้อมูลผู้ป่วย OPD สิทธิบัตรทอง</a></p></th>             
                </tr> 
                <tr class="table-primary">         
                    <th class="text-center">LZ (OPD ในเขต)</th>    
                    <th class="text-center">OSR (OPD นอกเขตมีใบส่งต่อ)</th>
                    <th class="text-center">OSNR (OPD นอกเขตไม่มีใบส่งตัว)</th>  
                </tr>
              </thead> 
              <tbody> 
                <?php $sum_lz = 0 ; ?>
                <?php $sum_osr = 0 ; ?>
                <?php $sum_osnr = 0 ; ?>
                @foreach($opd_ucs as $row) 
                <tr>
                  <td align="right">{{$row->month}}</td> 
                  <td align="right">{{ number_format($row->lz)}}</td>
                  <td align="right">{{ number_format($row->osr)}}</td> 
                  <td align="right">{{ number_format($row->osnr)}}</td> 
                </tr>
                <?php $sum_lz += $row->lz ; ?>
                <?php $sum_osr += $row->osr ; ?>
                <?php $sum_osnr += $row->osnr ; ?>
                @endforeach 
                <tr>
                  <td align="right"><strong>รวม</strong></td>                      
                  <td align="right"><strong>{{ number_format($sum_lz)}}</strong></td>
                  <td align="right"><strong>{{ number_format($sum_osr)}}</strong></td>    
                  <td align="right"><strong>{{ number_format($sum_osnr)}}</strong></td>    
                </tr>
              </tbody>
            </table>   
          </div>          
        </div> 
        <div class="col-md-6">
          <div style="overflow-x:auto;">     
            <table class="table table-striped table-bordered">
              <thead>
                <tr class="table-success">
                  <th class="text-center" rowspan="2"><p align="center">เดือน</p></th>    
                  <th class="text-center" colspan="3"><p align="center">บริการแพทย์ทางไกล Telehealth</p></th>             
                </tr> 
                <tr class="table-success">         
                    <th class="text-center">Visit OPD</th>    
                    <th class="text-center">Telehealth</th>
                    <th class="text-center">ร้อยละ</th>  
                </tr>
              </thead> 
              <tbody> 
                <?php $sum_visit_op = 0 ; ?>
                <?php $sum_telehealth = 0 ; ?>
                @foreach($telehealth as $row) 
                <tr>
                  <td align="right">{{$row->month}}</td> 
                  <td align="right">{{ number_format($row->visit_op)}}</td>
                  <td align="right">{{ number_format($row->telehealth)}}</td> 
                  <td align="right">{{ number_format(($row->telehealth*100)/$row->visit_op,2)}}</td> 
                </tr>
                <?php $sum_visit_op += $row->visit_op ; ?>
                <?php $sum_telehealth += $row->telehealth ; ?>
                @endforeach 
                <tr>
                  <td align="right"><strong>รวม</strong></td>                      
                  <td align="right"><strong>{{ number_format($sum_visit_op)}}</strong></td>
                  <td align="right"><strong>{{ number_format($sum_telehealth)}}</strong></td>    
                  <td align="right"><strong>{{ number_format(($sum_telehealth*100)/$sum_visit_op,2)}}</strong></td>     
                </tr>
              </tbody>
            </table>  
          </div>
        </div>   
      </div> 
      <!-- row -->
      <div class="row">        
        <div class="col-md-6"> 
          <div style="overflow-x:auto;">     
            <table class="table table-striped table-bordered">
              <thead>
                <tr class="table-primary">
                  <th class="text-center" rowspan="2"><p align="center">เดือน</p></th>    
                  <th class="text-center" colspan="5"><p align="center">นัดออนไลน์ Moph Appointment</p></th>             
              </tr> 
                <tr class="table-primary">   
                    <th class="text-center">รวม</th>          
                    <th class="text-center">แพทย์แผนไทย</th>    
                    <th class="text-center">ทันตกรรม</th>   
                    <th class="text-center">กายภาพบำบัด</th>   
                    <th class="text-center">ฝากครรภ์</th>          
                </tr>
              </thead> 
              <tbody> 
                <?php $sum_healthmed = 0 ; ?>
                <?php $sum_dent = 0 ; ?>
                <?php $sum_physic = 0 ; ?>
                <?php $sum_anc = 0 ; ?>
                @foreach($moph_appointment as $row) 
                <tr>
                  <td align="right">{{$row->month}}</td> 
                  <td align="right">{{ number_format($row->healthmed+$row->dent+$row->physic+$row->anc)}}</td>
                  <td align="right">{{ number_format($row->healthmed)}}</td>
                  <td align="right">{{ number_format($row->dent)}}</td>
                  <td align="right">{{ number_format($row->physic)}}</td>
                  <td align="right">{{ number_format($row->anc)}}</td> 
                </tr>
                <?php $sum_healthmed += $row->healthmed ; ?>
                <?php $sum_dent += $row->dent ; ?>
                <?php $sum_physic += $row->physic ; ?>
                <?php $sum_anc += $row->anc ; ?>
                @endforeach 
                <tr>
                  <td align="right"><strong>รวม</strong></td>    
                  <td align="right"><strong>{{ number_format($sum_healthmed+$sum_dent+$sum_physic+$sum_anc)}}</strong></td>                     
                  <td align="right"><strong>{{ number_format($sum_healthmed)}}</strong></td>
                  <td align="right"><strong>{{ number_format($sum_dent)}}</strong></td>
                  <td align="right"><strong>{{ number_format($sum_physic)}}</strong></td> 
                  <td align="right"><strong>{{ number_format($sum_anc)}}</strong></td>          
                </tr>
              </tbody>
            </table>   
          </div>     
        </div> 
        <div class="col-md-6"> 
    
        </div>   
      </div> 
    </div>      
  </div>          
</div>      
</body>
</html>
