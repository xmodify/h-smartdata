@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <div class="row justify-content-center">  
    <div class="col-md-12">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">{{$RISK_REPPROGRAMSUB_NAME}} ปีงบประมาณ {{$budget_year}}  </div>
          <table class="table table-bordered table-striped">
            <thead>
            <tr class="table-secondary">
                <th class="text-center">โปรแกรมย่อย 2</th>
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
            @foreach($risk_program_subsub as $row)          
            <tr>
                <td align="left">{{ $row->RISK_REPPROGRAMSUBSUB_NAME }}</td> 
                <td align="center"><a href="{{ url('backoffice_risk/program_subsub_detail/'.$row->id.'_'.$row->id2)}}" target="_blank">{{ $row->total }}</a></td>      
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
@endsection