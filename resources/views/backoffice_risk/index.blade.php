@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">      
    <div class="col-md-12">
        <form method="POST" enctype="multipart/form-data">
        @csrf
          <div class="row">                          
              <div class="col-md-3" align="left">
                <ul>                  
                  <a  href="{{ url('backoffice_risk/nrls') }}" target="_blank" ><li>ข้อมูลการเกิดอุบัติการณ์ความเสี่ยง ส่ง NRLS</li></a>
                  <a  href="{{ url('backoffice_risk/nrls_edit') }}" target="_blank" ><li>ข้อมูลการแก้ไขอุบัติการณ์ความเสี่ยง ส่ง NRLS</li></a>
                  <a  href="{{ url('backoffice_risk/nrls_dataset') }}" target="_blank" ><li>ข้อมูล dataset แบบรายเดือน ส่ง NRLS</li></a>
                </ul>
              </div>
              <div class="col-md-6" align="left">
                <ul>
                  <a  href="{{ url('backoffice_risk/med_error') }}" target="_blank"><li>Medication Error Report จากโปรแกรม HOSxP</li></a>
                </ul>
              </div>
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
<!-- Row -->
<div class="container-fluid">
  <div class="row justify-content-left"> 
    <!-- Column left -->
    <div class="col-md-7"> 
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">รายงานความเสี่ยงตามกลุ่มอุบัติการณ์แยกรายเดือน ปีงบประมาณ {{$budget_year}}</div>
        <div id="risk_clinic" style="width: 100%; height: 350px"></div>
      </div>            
    </div> 
    <!-- END Column left -->
    <!-- Column Rigth -->
    <div class="col-md-5"> 
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">รายงานความเสี่ยงตามกลุ่มอุบัติการณ์ ปีงบประมาณ {{$budget_year}} </div>
        <div id="risk_clinic_year" style="width: 100%; height: 350px"></div>  
      </div>            
    </div> 
    <!-- End Column Rigth -->
  </div>
</div>
<br>
<!-- row -->  
<div class="container-fluid">
    <div class="row justify-content-center">       
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">รายงานความเสี่ยงตามกลุ่มระดับความรุนแรง ปีงบประมาณ {{ $budget_year }}</div>
                <div id="consequence" style="width: 100%; height: 350px"></div>    
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
        <div class="card-header bg-primary bg-opacity-75 text-white">รายงานความเสี่ยงตามระดับความรุนแรง ปีงบประมาณ {{$budget_year}}</div>
          <table class="table table-bordered table-striped">
            <thead>
            <tr class="table-secondary">
                <th class="text-center">เดือน</th>
                <th class="text-center">รวม</th>
                <th class="text-center">A</th>
                <th class="text-center">B</th>
                <th class="text-center">C</th>
                <th class="text-center">D</th>
                <th class="text-center">E</th>
                <th class="text-center">F</th>
                <th class="text-center">G</th>
                <th class="text-center">H</th>
                <th class="text-center">I</th>
                <th class="text-center">1</th>
                <th class="text-center">2</th>
                <th class="text-center">3</th>
                <th class="text-center">4</th>
                <th class="text-center">5</th>
                <th class="text-center">Null</th>                
            </tr>     
            </thead> 
            <?php $sum_a = 0 ; ?>
            <?php $sum_b = 0 ; ?>
            <?php $sum_c = 0 ; ?>
            <?php $sum_d = 0 ; ?>
            <?php $sum_e = 0 ; ?>
            <?php $sum_f = 0 ; ?>
            <?php $sum_g = 0 ; ?>
            <?php $sum_h = 0 ; ?>
            <?php $sum_i = 0 ; ?>
            <?php $sum_g1 = 0 ; ?>
            <?php $sum_g2 = 0 ; ?>
            <?php $sum_g3 = 0 ; ?>
            <?php $sum_g4 = 0 ; ?>
            <?php $sum_g5 = 0 ; ?>
            <?php $sum_null = 0 ; ?>
            <?php $sum_total = 0 ; ?>
            @foreach($risk_clinic as $row)          
            <tr>
                <td align="left">{{ $row->month }}</td> 
                <td align="center">{{ $row->total }}</td>  
                <td align="center">{{ $row->a }}</td>
                <td align="center">{{ $row->b }}</td>
                <td align="center">{{ $row->c }}</td>
                <td align="center">{{ $row->d }}</td>
                <td align="center">{{ $row->e }}</td> 
                <td align="center">{{ $row->f }}</td>
                <td align="center">{{ $row->g }}</td>
                <td align="center">{{ $row->h }}</td> 
                <td align="center">{{ $row->i }}</td>   
                <td align="center">{{ $row->g1 }}</td> 
                <td align="center">{{ $row->g2 }}</td>
                <td align="center">{{ $row->g3 }}</td>
                <td align="center">{{ $row->g4 }}</td> 
                <td align="center">{{ $row->g5 }}</td> 
                <td align="center">{{ $row->null }}</td>                                  
            </tr>
            <?php $sum_a += $row->a ; ?>
            <?php $sum_b += $row->b ; ?>
            <?php $sum_c += $row->c ; ?>
            <?php $sum_d += $row->d ; ?>
            <?php $sum_e += $row->e ; ?>
            <?php $sum_f += $row->f ; ?>
            <?php $sum_g += $row->g ; ?>
            <?php $sum_h += $row->h ; ?>
            <?php $sum_i += $row->i ; ?>
            <?php $sum_g1 += $row->g1 ; ?>
            <?php $sum_g2 += $row->g2 ; ?>
            <?php $sum_g3 += $row->g3 ; ?>
            <?php $sum_g4 += $row->g4 ; ?>
            <?php $sum_g5 += $row->g5 ; ?>
            <?php $sum_null += $row->null ; ?>
            <?php $sum_total += $row->total ; ?>
            @endforeach
            <tr>
                <td align="center"><strong>รวม</strong></td> 
                <td align="center"><strong>{{ $sum_total }}</strong></td>  
                <td align="center"><strong>{{ $sum_a }}</strong></td> 
                <td align="center"><strong>{{ $sum_b }}</strong></td> 
                <td align="center"><strong>{{ $sum_c }}</strong></td> 
                <td align="center"><strong>{{ $sum_d }}</strong></td> 
                <td align="center"><strong>{{ $sum_e }}</strong></td> 
                <td align="center"><strong>{{ $sum_f }}</strong></td> 
                <td align="center"><strong>{{ $sum_g }}</strong></td> 
                <td align="center"><strong>{{ $sum_h }}</strong></td>  
                <td align="center"><strong>{{ $sum_i }}</strong></td> 
                <td align="center"><strong>{{ $sum_g1 }}</strong></td>  
                <td align="center"><strong>{{ $sum_g2 }}</strong></td> 
                <td align="center"><strong>{{ $sum_g3 }}</strong></td> 
                <td align="center"><strong>{{ $sum_g4 }}</strong></td>  
                <td align="center"><strong>{{ $sum_g5 }}</strong></td> 
                <td align="center"><strong>{{ $sum_null }}</strong></td>                                   
            </tr>
          </table>        
      </div>      
    </div>
  </div>
</div>
<br>
<!-- Row -->
<div class="container-fluid">
  <div class="row justify-content-center">  
    <div class="col-md-12">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">รายงานความเสี่ยงตามโปรแกรมหลัก ปีงบประมาณ {{$budget_year}}</div>
          <table class="table table-bordered table-striped">
            <thead>
            <tr class="table-secondary">
                <th class="text-center">โปรแกรมหลัก</th>
                <th class="text-center">รวม</th>
                <th class="text-center">A</th>
                <th class="text-center">B</th>
                <th class="text-center">C</th>
                <th class="text-center">D</th>
                <th class="text-center">E</th>
                <th class="text-center">F</th>
                <th class="text-center">G</th>
                <th class="text-center">H</th>
                <th class="text-center">I</th>
                <th class="text-center">1</th>
                <th class="text-center">2</th>
                <th class="text-center">3</th>
                <th class="text-center">4</th>
                <th class="text-center">5</th>
                <th class="text-center">Null</th>                
            </tr>     
            </thead> 
            <?php $sum_a = 0 ; ?>
            <?php $sum_b = 0 ; ?>
            <?php $sum_c = 0 ; ?>
            <?php $sum_d = 0 ; ?>
            <?php $sum_e = 0 ; ?>
            <?php $sum_f = 0 ; ?>
            <?php $sum_g = 0 ; ?>
            <?php $sum_h = 0 ; ?>
            <?php $sum_i = 0 ; ?>
            <?php $sum_g1 = 0 ; ?>
            <?php $sum_g2 = 0 ; ?>
            <?php $sum_g3 = 0 ; ?>
            <?php $sum_g4 = 0 ; ?>
            <?php $sum_g5 = 0 ; ?>
            <?php $sum_null = 0 ; ?>
            <?php $sum_total = 0 ; ?>
            @foreach($risk_program as $row)          
            <tr>
                <td align="left"> <a href="{{ url('backoffice_risk/program_sub/'.$row->id)}}" target="_blank">{{ $row->RISK_REPPROGRAM_NAME }}</a></td> 
                <td align="center"><a href="{{ url('backoffice_risk/program_detail/'.$row->id)}}" target="_blank">{{ $row->total }}</a></td>  
                <td align="center">{{ $row->a }}</td>
                <td align="center">{{ $row->b }}</td>
                <td align="center">{{ $row->c }}</td>
                <td align="center">{{ $row->d }}</td>
                <td align="center">{{ $row->e }}</td> 
                <td align="center">{{ $row->f }}</td>
                <td align="center">{{ $row->g }}</td>
                <td align="center">{{ $row->h }}</td> 
                <td align="center">{{ $row->i }}</td>   
                <td align="center">{{ $row->g1 }}</td> 
                <td align="center">{{ $row->g2 }}</td>
                <td align="center">{{ $row->g3 }}</td>
                <td align="center">{{ $row->g4 }}</td> 
                <td align="center">{{ $row->g5 }}</td> 
                <td align="center">{{ $row->null }}</td>                 
            </tr>
            <?php $sum_a += $row->a ; ?>
            <?php $sum_b += $row->b ; ?>
            <?php $sum_c += $row->c ; ?>
            <?php $sum_d += $row->d ; ?>
            <?php $sum_e += $row->e ; ?>
            <?php $sum_f += $row->f ; ?>
            <?php $sum_g += $row->g ; ?>
            <?php $sum_h += $row->h ; ?>
            <?php $sum_i += $row->i ; ?>
            <?php $sum_g1 += $row->g1 ; ?>
            <?php $sum_g2 += $row->g2 ; ?>
            <?php $sum_g3 += $row->g3 ; ?>
            <?php $sum_g4 += $row->g4 ; ?>
            <?php $sum_g5 += $row->g5 ; ?>
            <?php $sum_null += $row->null ; ?>
            <?php $sum_total += $row->total ; ?>
            @endforeach
            <tr>
                <td align="center"><strong>รวม</strong></td> 
                <td align="center"><strong>{{ $sum_total }}</strong></td>   
                <td align="center"><strong>{{ $sum_a }}</strong></td> 
                <td align="center"><strong>{{ $sum_b }}</strong></td> 
                <td align="center"><strong>{{ $sum_c }}</strong></td> 
                <td align="center"><strong>{{ $sum_d }}</strong></td> 
                <td align="center"><strong>{{ $sum_e }}</strong></td> 
                <td align="center"><strong>{{ $sum_f }}</strong></td> 
                <td align="center"><strong>{{ $sum_g }}</strong></td> 
                <td align="center"><strong>{{ $sum_h }}</strong></td>  
                <td align="center"><strong>{{ $sum_i }}</strong></td> 
                <td align="center"><strong>{{ $sum_g1 }}</strong></td>  
                <td align="center"><strong>{{ $sum_g2 }}</strong></td> 
                <td align="center"><strong>{{ $sum_g3 }}</strong></td> 
                <td align="center"><strong>{{ $sum_g4 }}</strong></td>  
                <td align="center"><strong>{{ $sum_g5 }}</strong></td> 
                <td align="center"><strong>{{ $sum_null }}</strong></td>
            </tr>
          </table>        
      </div>      
    </div>
  </div>
</div>
<br>
<!-- Row -->
<div class="container-fluid">
  <div class="row justify-content-left">  
    <div class="col-md-12">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">Risk Assessment Matrix แผนผังประเมินความเสี่ยงทางคลินิก </div>
          <table class="table table-bordered" cellspacing="0" cellpadding="2">
            <tr class="info">
            <th class="">ระดับความรุนแรงความเสี่ยงทางคลินิก : ความถี่</th>
                <td> 
                    <center>มากกว่า 5 ปี ครั้ง</center>
                </td>
                <td>
                    <center>2-5 ปี ครั้ง</center>
                </td>
                <td> 
                    <center>1 ปี ครั้ง</center>
                </td>
                <td> 
                    <center>2-5 เดือน ครั้ง</center>
                </td>
                <td> 
                    <center>ทุกสัปดาห์/เดือน</center>
                </td>
            </tr>

            <tr>
            <th class="">สูงมาก/หายนะ ระดับความรุนแรง I</th>
                <td style="background-color:yellow">
                  <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical5_1')}}" target="_blank">{{ $matrix_c5_1 }}</a></font></center>
                </td>
                <td style="background-color:#ff9900">
                  <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical5_2')}}" target="_blank">{{ $matrix_c5_2 }}</a></font></center>
                </td>
                <td style="background-color:#ff9900">
                  <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical5_3')}}" target="_blank">{{ $matrix_c5_3 }}</a></font></center>
                </td>
                <td style="background-color:red">
                  <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical5_4')}}" target="_blank">{{ $matrix_c5_4 }}</a></font></center>
                </td>
                <td style="background-color:red">
                  <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical5_5')}}" target="_blank">{{ $matrix_c5_5 }}</a></font></center>
                </td>
            </tr>
            <tr>
            <th class="">สูง/วิกฤต ระดับความรุนแรง G,H</th>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical4_1')}}" target="_blank">{{ $matrix_c4_1 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical4_2')}}" target="_blank">{{ $matrix_c4_2 }}</a></font></center>
                </td>
                <td style="background-color:orange">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical4_3')}}" target="_blank">{{ $matrix_c4_3 }}</a></font></center>
                </td>
                <td style="background-color:orange">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical4_4')}}" target="_blank">{{ $matrix_c4_4 }}</a></font></center>
                </td>
                <td style="background-color:red">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical4_5')}}" target="_blank">{{ $matrix_c4_5 }}</a></font></center>
                </td>
            </tr>
            <tr>
            <th class="">ปานกลาง ระดับความรุนแรง E,F</th>
                <td style="background-color:#33CC33">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical3_1')}}" target="_blank">{{ $matrix_c3_1 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical3_2')}}" target="_blank">{{ $matrix_c3_2 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical3_3')}}" target="_blank">{{ $matrix_c3_3 }}</a></font></center>
                </td>
                <td style="background-color:orange">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical3_4')}}" target="_blank">{{ $matrix_c3_4 }}</a></font></center>
                </td>
                <td style="background-color:orange">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical3_5')}}" target="_blank">{{ $matrix_c3_5 }}</a></font></center>
                </td>
            </tr>
            <tr>
                <th class="">ต่ำ/น้อย ระดับความรุนแรง B,C,D</th>
                <td style="background-color:#33CC33">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical2_1')}}" target="_blank">{{ $matrix_c2_1 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical2_2')}}" target="_blank">{{ $matrix_c2_2 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical2_3')}}" target="_blank">{{ $matrix_c2_3 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical2_4')}}" target="_blank">{{ $matrix_c2_4 }}</a></font></center>
                </td>
                <td style="background-color:orange">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical2_5')}}" target="_blank">{{ $matrix_c2_5 }}</a></font></center>
                </td>
            </tr> 
            <tr>
                <th class="">ไม่เป็นสาระสำคัญ ระดับความรุนแรง A</th>
                <td style="background-color:#33CC33">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical1_1')}}" target="_blank">{{ $matrix_c1_1 }}</a></font></center>
                </td>
                <td style="background-color:#33CC33">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical1_2')}}" target="_blank">{{ $matrix_c1_2 }}</a></font></center>
                </td>
                <td style="background-color:#33CC33">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical1_3')}}" target="_blank">{{ $matrix_c1_3 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical1_4')}}" target="_blank">{{ $matrix_c1_4 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/Clinical1_5')}}" target="_blank">{{ $matrix_c1_5 }}</a></font></center>
                </td>
            </tr>
          </table>
      </div>      
    </div>
  </div>
</div>
<br>
<!-- Row -->
<div class="container-fluid">
  <div class="row justify-content-left">  
    <div class="col-md-12">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">Risk Assessment Matrix แผนผังประเมินความเสี่ยงทั่วไป </div>
          <table class="table table-bordered" cellspacing="0" cellpadding="2">
            <tr class="info">
            <th class="">ระดับความรุนแรงความเสี่ยงทั่วไป : ความถี่</th>
                <td> 
                    <center>มากกว่า 5 ปี ครั้ง</center>
                </td>
                <td>
                    <center>2-5 ปี ครั้ง</center>
                </td>
                <td> 
                    <center>1 ปี ครั้ง</center>
                </td>
                <td> 
                    <center>2-5 เดือน ครั้ง</center>
                </td>
                <td> 
                    <center>ทุกสัปดาห์/เดือน</center>
                </td>
            </tr>

            <tr>
            <th class="">สูงมาก/หายนะ ระดับความรุนแรง 5</th>
                <td style="background-color:yellow">
                  <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General5_1')}}" target="_blank">{{ $matrix_g5_1 }}</a></font></center>
                </td>
                <td style="background-color:#ff9900">
                  <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General5_2')}}" target="_blank">{{ $matrix_g5_2 }}</a></font></center>
                </td>
                <td style="background-color:#ff9900">
                  <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General5_3')}}" target="_blank">{{ $matrix_g5_3 }}</a></font></center>
                </td>
                <td style="background-color:red">
                  <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General5_4')}}" target="_blank">{{ $matrix_g5_4 }}</a></font></center>
                </td>
                <td style="background-color:red">
                  <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General5_5')}}" target="_blank">{{ $matrix_g5_5 }}</a></font></center>
                </td>
            </tr>
            <tr>
            <th class="">สูง/วิกฤต ระดับความรุนแรง 4</th>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General4_1')}}" target="_blank">{{ $matrix_g4_1 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General4_2')}}" target="_blank">{{ $matrix_g4_2 }}</a></font></center>
                </td>
                <td style="background-color:orange">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General4_3')}}" target="_blank">{{ $matrix_g4_3 }}</a></font></center>
                </td>
                <td style="background-color:orange">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General4_4')}}" target="_blank">{{ $matrix_g4_4 }}</a></font></center>
                </td>
                <td style="background-color:red">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General4_5')}}" target="_blank">{{ $matrix_g4_5 }}</a></font></center>
                </td>
            </tr>
            <tr>
            <th class="">ปานกลาง ระดับความรุนแรง 3</th>
                <td style="background-color:#33CC33">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General3_1')}}" target="_blank">{{ $matrix_g3_1 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General3_2')}}" target="_blank">{{ $matrix_g3_2 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General3_3')}}" target="_blank">{{ $matrix_g3_3 }}</a></font></center>
                </td>
                <td style="background-color:orange">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General3_4')}}" target="_blank">{{ $matrix_g3_4 }}</a></font></center>
                </td>
                <td style="background-color:orange">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General3_5')}}" target="_blank">{{ $matrix_g3_5 }}</a></font></center>
                </td>
            </tr>
            <tr>
                <th class="">ต่ำ/น้อย ระดับความรุนแรง 2</th>
                <td style="background-color:#33CC33">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General2_1')}}" target="_blank">{{ $matrix_g2_1 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General2_2')}}" target="_blank">{{ $matrix_g2_2 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General2_3')}}" target="_blank">{{ $matrix_g2_3 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General2_4')}}" target="_blank">{{ $matrix_g2_4 }}</a></font></center>
                </td>
                <td style="background-color:orange">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General2_5')}}" target="_blank">{{ $matrix_g2_5 }}</a></font></center>
                </td>
            </tr>
            <tr>
                <th class="">ไม่เป็นสาระสำคัญ ระดับความรุนแรง 1</th>
                <td style="background-color:#33CC33">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General1_1')}}" target="_blank">{{ $matrix_g1_1 }}</a></font></center>
                </td>
                <td style="background-color:#33CC33">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General1_2')}}" target="_blank">{{ $matrix_g1_2 }}</a></font></center>
                </td>
                <td style="background-color:#33CC33">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General1_3')}}" target="_blank">{{ $matrix_g1_3 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General1_4')}}" target="_blank">{{ $matrix_g1_4 }}</a></font></center>
                </td>
                <td style="background-color:yellow">
                <center><font color="blue"><a href="{{ url('backoffice_risk/risk_matrix_detail/General1_5')}}" target="_blank">{{ $matrix_g1_5 }}</a></font></center>
                </td>
            </tr>
          </table>
      </div>      
    </div>
  </div>
</div>
<br>
<!-- Row -->
<div class="container-fluid">
  <div class="row justify-content-center">  
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ระดับความเสี่ยง</div>
          <table class="table table-bordered" cellspacing="0" cellpadding="2">
            <tr>
              <td style="background-color:#33CC33">1-3 Low
              </td>
              <td style="background-color:#ffffff">แก้ไขที่หน่วยงาน
              </td>
            </tr> 
            <tr>
              <td style="background-color:yellow">4-9 Medium
              </td>
              <td style="background-color:#ffffff">แก้ไขที่หน่วยงานและระหว่างหน่วยงานที่เกี่ยวข้อง
              </td>
            </tr> 
            <tr>
              <td style="background-color:#ff9900">10-19 Hight
              </td>
              <td style="background-color:#ffffff">ทบทวนและวางระบบโดยทีมคร่อมสายงานและหน่วยงาน
              </td>
            </tr> 
            <tr>
              <td style="background-color:red">20-25 Unacceptable
              </td>
              <td style="background-color:#ffffff">ทบทวนละวางระบบโดยทีมคร่อมสายงานและทีมนำ
              </td>
            </tr> 
          </table>
        </div>      
    </div>
  </div>
</div>
@endsection
<!-- Line Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#risk_clinic"), {
            
            series: [{
                name: 'Clinical',
                data: <?php echo json_encode($risk_clinical); ?>,
                    },
                    {
                name: 'General',
                data: <?php echo json_encode($risk_general); ?>,
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
            colors: [ '#0066FF','#00CC99'],
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
                categories: <?php echo json_encode($risk_clinic_m); ?>,
            }
            }).render();
        });
</script>
<!-- End Line Chart -->
<!-- Pie Chart -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#risk_clinic_year"), {
      series: [{{$risk_clinical_y}}, {{$risk_general_y}},{{$risk_null_y}}],
      chart: {
        height: 350,
        type: 'pie',
        toolbar: {
          show: true
        }
      },
      labels: ['Clinical', 'General', 'Non-Program']
    }).render();
  });
</script>
<!-- End Pie Chart -->
<!-- Column Chart -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    new ApexCharts(document.querySelector("#consequence"), {
      series: [{
        name: 'Near Miss',
        data: <?php echo json_encode($risk_lavel_near_miss); ?>
      }, {
        name: 'Low Risk',
        data: <?php echo json_encode($risk_lavel_low_risk); ?>
      }, {
        name: 'Moderate Risk',
        data: <?php echo json_encode($risk_lavel_moderate_risk); ?>
      }, {
        name: 'High Risk',
        data: <?php echo json_encode($risk_lavel_high_risk); ?>
      }],
      chart: {
        type: 'bar',
        height: 350
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '90%',
          endingShape: 'rounded'
        },
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
      },
      xaxis: {
        categories: <?php echo json_encode($risk_clinic_m); ?>,
      },
      yaxis: {
        title: {
          text: 'ครั้ง'
        }
      },
      fill: {
        opacity: 1
      },
      tooltip: {
        y: {
          formatter: function(val) {
            return  val + " ครั้ง"
          }
        }
      }
    }).render();
  });
</script>
<!-- End Column Chart -->

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
