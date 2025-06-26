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
        <div class="col-md-6"> 
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนใบสั่งยาผู้ป่วยนอก ปีงบประมาณ {{ $budget_year }} </div>                       
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr class="table-secondary">
                            <th class="text-center">เดือน</th>
                            <th class="text-center">จำนวนใบสั่งยาผู้ป่วยนอก</th>        
                            <th class="text-center">จำนวนรายการยาผู้ป่วยนอก</th>                      
                            <th class="text-center">ต้นทุนยาผู้ป่วยนอก</th>
                            <th class="text-center">มูลค่ายาผู้ป่วยนอก</th>
                        </tr>     
                        </thead>  
                        <?php $sum_opd = 0 ; ?>  
                        <?php $sum_drugopd = 0 ; ?>  
                        <?php $sum_cost = 0 ; ?> 
                        <?php $sum_price = 0 ; ?> 
                        @foreach($prescription_opd as $row)  
                        <tr>
                            <td align="center">{{ $row->month }}</td>  
                            <td align="right">{{ number_format($row->opd) }}</td>
                            <td align="right">{{ number_format($row->drugopd) }}</td>
                            <td align="right">{{ number_format($row->sum_cost,2) }}</td>  
                            <td align="right">{{ number_format($row->sum_price,2) }}</td> 
                        </tr>
                        <?php $sum_opd += $row->opd ; ?>
                        <?php $sum_drugopd += $row->drugopd ; ?>
                        <?php $sum_cost += $row->sum_cost ; ?>
                        <?php $sum_price += $row->sum_price ; ?>
                        @endforeach 
                        <tr>
                            <td align="right"><strong>รวม</strong></td>  
                            <td align="right"><strong>{{ number_format($sum_opd) }}</strong></td>
                            <td align="right"><strong>{{ number_format($sum_drugopd) }}</strong></td>
                            <td align="right"><strong>{{ number_format($sum_cost,2) }}</strong></td>  
                            <td align="right"><strong>{{ number_format($sum_price,2) }}</strong></td> 
                        </tr>
                    </table>
                </div>
            </div>
        </div>        
        <div class="col-md-6"> 
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">จำนวนใบสั่งยาผู้ป่วยใน ปีงบประมาณ {{ $budget_year }} </div>                       
                <div class="card-body">
                <table class="table table-bordered table-striped">
                        <thead>
                        <tr class="table-secondary">
                            <th class="text-center">เดือน</th>
                            <th class="text-center">จำนวนใบสั่งยาผู้ป่วยใน</th> 
                            <th class="text-center">จำนวนรายการยาผู้ป่วยใน</th>                            
                            <th class="text-center">ต้นทุนยาผู้ป่วยใน</th>
                            <th class="text-center">มูลค่ายาผู้ป่วยใน</th>
                        </tr>     
                        </thead>  
                        <?php $sum_ipd = 0 ; ?>  
                        <?php $sum_drugipd = 0 ; ?>  
                        <?php $sum_cost_ipd = 0 ; ?> 
                        <?php $sum_price_ipd = 0 ; ?> 
                        @foreach($prescription_ipd as $row)  
                        <tr>
                            <td align="center">{{ $row->month }}</td>  
                            <td align="right">{{ number_format($row->ipd) }}</td>
                            <td align="right">{{ number_format($row->drugipd) }}</td>
                            <td align="right">{{ number_format($row->sum_cost,2) }}</td>  
                            <td align="right">{{ number_format($row->sum_price,2) }}</td> 
                        </tr>
                        <?php $sum_ipd += $row->ipd ; ?>
                        <?php $sum_drugipd += $row->drugipd ; ?>
                        <?php $sum_cost_ipd += $row->sum_cost ; ?>
                        <?php $sum_price_ipd += $row->sum_price ; ?>
                        @endforeach 
                        <tr>
                            <td align="right"><strong>รวม</strong></td>  
                            <td align="right"><strong>{{ number_format($sum_ipd) }}</strong></td>
                            <td align="right"><strong>{{ number_format($sum_drugipd) }}</strong></td>
                            <td align="right"><strong>{{ number_format($sum_cost_ipd,2) }}</strong></td>  
                            <td align="right"><strong>{{ number_format($sum_price_ipd,2) }}</strong></td> 
                        </tr>
                    </table>
                </div>
            </div>
        </div>            
    </div>
</div>

@endsection

