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
<br> 
<!-- row -->
<div class="container-fluid">  
    <div class="row justify-content-center">  
        <div class="col-md-12">  
            <div class="card">          
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนผู้เสียชีวิต วินิจฉัยตามรหัส ICD10 ปีงบประมาณ {{ $budget_year }}</div>                                                      
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
                        @foreach($diag_icd10 as $row)          
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
</div>
<br>
@endsection

