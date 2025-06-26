@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">

@section('content')
<div class="container-fluid">
    <h5 class="alert alert-primary"><strong>รายงานแผนงานโครงการแยกประเภทแผนงาน</strong></h5>  
</div> 
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
        <div class="col-md-12">  
            <div class="card">          
                <div class="card-header bg-primary bg-opacity-75 text-white">แผนงานโครงการประเภทแผนปฎิบัติราชการ คปสอ.หัวตะพาน ปีงบประมาณ {{$budget_year}}</div>                            
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-secondary">
                        <th rowspan="2" class="text-center">ลำดับ</th>
                        <th rowspan="2" class="text-center">หน่วยงาน</th>
                        <th rowspan="2" class="text-center">จำนวน</th>  
                        <th rowspan="2" class="text-center">อนุมัติ</th>  
                        <th rowspan="2" class="text-center">ดำเนินการ</th>        
                        <th rowspan="2" class="text-center">งบประมาณ</th>  
                        <th rowspan="2" class="text-center">ใช้จริง</th>                                  
                        <th colspan="7" class="text-center">แยกตามประเภทงบประมาณ</th> 
                    </tr> 
                    <tr class="table-secondary">                                  
                        <th class="text-center">งบประมาณ</th>
                        <th class="text-center">งบค่าเสื่อม</th>
                        <th class="text-center">เงินบำรุง</th>
                        <th class="text-center">เงินบริจาค</th>
                        <th class="text-center">เงิน อปท.</th>
                        <th class="text-center">งบลงทุน</th>
                        <th class="text-center">ไม่ระบุ</th>
                    </tr>   
                    </thead>                 
                    <?php $count = 1 ; ?>
                    <?php $sumproject = 0 ; ?>
                    <?php $sumstatus_app = 0 ; ?>
                    <?php $sumstatus_track = 0 ; ?>
                    <?php $sumbudget_price = 0 ; ?>
                    <?php $sumbudget_real = 0 ; ?>
                    <?php $sumbudget_1 = 0 ; ?>
                    <?php $sumbudget_2 = 0 ; ?>
                    <?php $sumbudget_3 = 0 ; ?>
                    <?php $sumbudget_4 = 0 ; ?>
                    <?php $sumbudget_5 = 0 ; ?>
                    <?php $sumbudget_6 = 0 ; ?>
                    <?php $sumbnonbudget = 0 ; ?>
                    @foreach($plan_project_type1 as $row)          
                    <tr>
                        <td align="center">{{ $count }}</td>                   
                        <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>                        
                        <td align="right">{{ number_format($row->project) }}</td>
                        <td align="right">{{ number_format($row->status_app) }}</td>
                        <td align="right">{{ number_format($row->status_track) }}</td>
                        <td align="right">{{ number_format($row->BUDGET_PICE,2) }}</td>
                        <td align="right">{{ number_format($row->BUDGET_PICE_REAL,2) }}</td>  
                        <td align="right">{{ number_format($row->budget_1,2) }}</td>
                        <td align="right">{{ number_format($row->budget_2,2) }}</td>
                        <td align="right">{{ number_format($row->budget_3,2) }}</td>
                        <td align="right">{{ number_format($row->budget_4,2) }}</td>
                        <td align="right">{{ number_format($row->budget_5,2) }}</td>
                        <td align="right">{{ number_format($row->budget_6,2) }}</td>
                        <td align="right">{{ number_format($row->nonbudget,2) }}</td>                  
                    </tr>
                    <?php $count++; ?>
                    <?php $sumproject += $row->project ; ?>
                    <?php $sumstatus_app += $row->status_app ; ?>
                    <?php $sumstatus_track += $row->status_track ; ?>
                    <?php $sumbudget_price += $row->BUDGET_PICE ; ?>
                    <?php $sumbudget_real += $row->BUDGET_PICE_REAL ; ?>
                    <?php $sumbudget_1 += $row->budget_1 ; ?>
                    <?php $sumbudget_2 += $row->budget_2 ; ?>
                    <?php $sumbudget_3 += $row->budget_3 ; ?>
                    <?php $sumbudget_4 += $row->budget_4 ; ?>
                    <?php $sumbudget_5 += $row->budget_5 ; ?>
                    <?php $sumbudget_6 += $row->budget_6 ; ?>
                    <?php $sumbnonbudget += $row->nonbudget ; ?>
                    @endforeach  
                    <tr>   
                    <td colspan= "2" align="right"><strong>รวม </strong></td>  
                        <td align="right"><strong>{{ number_format($sumproject) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumstatus_app) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumstatus_track) }}</strong></td>   
                        <td align="right"><strong>{{ number_format($sumbudget_price,2) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumbudget_real,2) }}</strong></td>  
                        <td align="right"><strong>{{ number_format($sumbudget_1,2) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumbudget_2,2) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumbudget_3,2) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumbudget_4,2) }}</strong></td>                     
                        <td align="right"><strong>{{ number_format($sumbudget_5,2) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumbudget_6,2) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumbnonbudget,2) }}</strong></td>                         
                    </tr>
                </table>
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
                <div class="card-header bg-primary bg-opacity-75 text-white">แผนงานโครงการประเภทแผนปฏิบัติราชการ รพ.หัวตะพาน ปีงบประมาณ {{$budget_year}}</div>                            
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-secondary">
                        <th rowspan="2" class="text-center">ลำดับ</th>
                        <th rowspan="2" class="text-center">หน่วยงาน</th>
                        <th rowspan="2" class="text-center">จำนวน</th>  
                        <th rowspan="2" class="text-center">อนุมัติ</th>  
                        <th rowspan="2" class="text-center">ดำเนินการ</th>        
                        <th rowspan="2" class="text-center">งบประมาณ</th>  
                        <th rowspan="2" class="text-center">ใช้จริง</th>                                  
                        <th colspan="7" class="text-center">แยกตามประเภทงบประมาณ</th> 
                    </tr> 
                    <tr class="table-secondary">                                  
                        <th class="text-center">งบประมาณ</th>
                        <th class="text-center">งบค่าเสื่อม</th>
                        <th class="text-center">เงินบำรุง</th>
                        <th class="text-center">เงินบริจาค</th>
                        <th class="text-center">เงิน อปท.</th>
                        <th class="text-center">งบลงทุน</th>
                        <th class="text-center">ไม่ระบุ</th>
                    </tr>   
                    </thead>                 
                    <?php $count = 1 ; ?>
                    <?php $sumproject = 0 ; ?>
                    <?php $sumstatus_app = 0 ; ?>
                    <?php $sumstatus_track = 0 ; ?>                  
                    <?php $sumbudget_price = 0 ; ?>
                    <?php $sumbudget_real = 0 ; ?>
                    <?php $sumbudget_1 = 0 ; ?>
                    <?php $sumbudget_2 = 0 ; ?>
                    <?php $sumbudget_3 = 0 ; ?>
                    <?php $sumbudget_4 = 0 ; ?>
                    <?php $sumbudget_5 = 0 ; ?>
                    <?php $sumbudget_6 = 0 ; ?>
                    <?php $sumbnonbudget = 0 ; ?>
                    @foreach($plan_project_type2 as $row)          
                    <tr>
                        <td align="center">{{ $count }}</td>                   
                        <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>                        
                        <td align="right">{{ number_format($row->project) }}</td>
                        <td align="right">{{ number_format($row->status_app) }}</td>
                        <td align="right">{{ number_format($row->status_track) }}</td>                      
                        <td align="right">{{ number_format($row->BUDGET_PICE,2) }}</td>
                        <td align="right">{{ number_format($row->BUDGET_PICE_REAL,2) }}</td>   
                        <td align="right">{{ number_format($row->budget_1,2) }}</td>
                        <td align="right">{{ number_format($row->budget_2,2) }}</td>
                        <td align="right">{{ number_format($row->budget_3,2) }}</td>
                        <td align="right">{{ number_format($row->budget_4,2) }}</td>
                        <td align="right">{{ number_format($row->budget_5,2) }}</td>
                        <td align="right">{{ number_format($row->budget_6,2) }}</td>
                        <td align="right">{{ number_format($row->nonbudget,2) }}</td>                 
                    </tr>
                    <?php $count++; ?>
                    <?php $sumproject += $row->project ; ?>
                    <?php $sumstatus_app += $row->status_app ; ?>
                    <?php $sumstatus_track += $row->status_track ; ?>                   
                    <?php $sumbudget_price += $row->BUDGET_PICE ; ?>
                    <?php $sumbudget_real += $row->BUDGET_PICE_REAL ; ?>
                    <?php $sumbudget_1 += $row->budget_1 ; ?>
                    <?php $sumbudget_2 += $row->budget_2 ; ?>
                    <?php $sumbudget_3 += $row->budget_3 ; ?>
                    <?php $sumbudget_4 += $row->budget_4 ; ?>
                    <?php $sumbudget_5 += $row->budget_5 ; ?>
                    <?php $sumbudget_6 += $row->budget_6 ; ?>
                    <?php $sumbnonbudget += $row->nonbudget ; ?>
                    @endforeach  
                    <tr>   
                    <td colspan= "2" align="right"><strong>รวม </strong></td>  
                        <td align="right"><strong>{{ number_format($sumproject) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumstatus_app) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumstatus_track) }}</strong></td>                       
                        <td align="right"><strong>{{ number_format($sumbudget_price,2) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumbudget_real,2) }}</strong></td>  
                        <td align="right"><strong>{{ number_format($sumbudget_1,2) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumbudget_2,2) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumbudget_3,2) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumbudget_4,2) }}</strong></td>                     
                        <td align="right"><strong>{{ number_format($sumbudget_5,2) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumbudget_6,2) }}</strong></td>
                        <td align="right"><strong>{{ number_format($sumbnonbudget,2) }}</strong></td>                           
                    </tr>
                </table>
            </div>
        </div>
    </div>  
</div>
<br> 
<!--row-->
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>รายละเอียดแผนงานโครงการ รพ.หัวตะพาน ปีงบประมาณ {{$budget_year}}</strong></div> 
        <div class="card-body">
            <table id="plan_project_detail" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th>                        
                        <th class="text-center">หน่วยงาน</th>
                        <th class="text-center">รหัส</th>
                        <th class="text-center">ชื่อโครงการ</th>
                        <th class="text-center">ประเภทแผน</th>
                        <th class="text-center">ยุทธศาสตร์</th>
                        <th class="text-center">เป้าประสงค์</th>
                        <th class="text-center">ตัวชี้วัด</th>
                        <th class="text-center">ประเภทงบ</th>
                        <th class="text-center">งบประมาณ</th>
                        <th class="text-center">ใช้จริง</th>
                        <th class="text-center">ผู้รับผิดชอบ</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center">การติดตาม</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($plan_project_detail as $row)
                    <tr>
                        <td align="center">{{ $count }}</td>                   
                        <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                        <td align="left">{{ $row->PRO_NUMBER }}</td>
                        <td align="left">{{ $row->PRO_NAME }}</td>
                        <td align="left">{{ $row->PLAN_TYPE_NAME }}</td>
                        <td align="left">{{ $row->STRATEGIC_NAME }}</td>     
                        <td align="left">{{ $row->TARGET_NAME }}</td>   
                        <td align="left">{{ $row->KPI_NAME }}</td>   
                        <td align="center">{{ $row->BUDGET_NAME }}</td>   
                        <td align="right">{{ number_format($row->BUDGET_PICE,2) }}</td>   
                        <td align="right">{{ number_format($row->BUDGET_PICE_REAL,2) }}</td>   
                        <td align="left">{{ $row->PRO_TEAM_HR_NAME }}</td>    
                        <td align="center">{{ $row->PRO_STATUS }}</td> 
                        <td align="left">{{ $row->PLAN_TRACKING_NAME }}</td>                         
                    </tr>
                <?php $count++; ?>
                @endforeach
            </table> 
        </div>         
    </div>
</div>
<br>
@endsection
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#plan_project_detail').DataTable();
    });
</script>