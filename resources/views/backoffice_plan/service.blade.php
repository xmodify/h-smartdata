@extends('layouts.app')

@section('content')
<div class="container-fluid">  
    <form method="POST" enctype="multipart/form-data">
        @csrf            
        <div class="row" >
                <label class="col-md-3 col-form-label text-md-end my-1">{{ __('วันที่') }}</label>
            <div class="col-md-2">
                <input type="date" name="start_date" class="form-control my-1" placeholder="Date" value="{{ $start_date }}" > 
            </div>
                <label class="col-md-1 col-form-label text-md-end my-1">{{ __('ถึง') }}</label>
            <div class="col-md-2">
                <input type="date" name="end_date" class="form-control my-1" placeholder="Date" value="{{ $end_date }}" > 
            </div>                     
            <div class="col-md-1" >                            
                <button type="submit" class="btn btn-primary my-1 ">{{ __('ค้นหา') }}</button>
            </div>
        </div>
    </form>    
</div>
<br>
<!--row-->
<div class="container-fluid">
  <div class="row justify-content-center">  
    <div class="col-md-12">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลงานบริการโรงพยาบาลวันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
          <div class="card-body"> 
            <table class="table table-bordered table-striped">
                <tr class="table-success">
                    <td align="center"><b>ข้อมูลบริการ</b></td> 
                    <td align="center" colspan= "2"><b>รวม</b></td> 
                    <td align="center" colspan= "2"><b>สิทธิประกันสุขภาพ</b></td> 
                    <td align="center" colspan= "2"><b>ข้าราชการ</b></td> 
                    <td align="center" colspan= "2"><b>ประกันสังคม</b></td> 
                    <td align="center" colspan= "2"><b>อปท.</b></td> 
                    <td align="center" colspan= "2"><b>ต่างด้าว</b></td> 
                    <td align="center" colspan= "2"><b>Stateless</b></td> 
                    <td align="center" colspan= "2"><b>ชำระเงิน/พรบ.</b></td> 
                </tr>     
                <tr class="table-secondary"> 
                    <td align="center"></td>                
                    <td align="center">ครั้ง</td> 
                    <td align="center">คน</td> 
                    <td align="center">ครั้ง</td> 
                    <td align="center">คน</td> 
                    <td align="center">ครั้ง</td> 
                    <td align="center">คน</td> 
                    <td align="center">ครั้ง</td> 
                    <td align="center">คน</td> 
                    <td align="center">ครั้ง</td> 
                    <td align="center">คน</td> 
                    <td align="center">ครั้ง</td> 
                    <td align="center">คน</td> 
                    <td align="center">ครั้ง</td> 
                    <td align="center">คน</td> 
                    <td align="center">ครั้ง</td> 
                    <td align="center">คน</td> 
                </tr>     
                <tr>@foreach($opd_vn as $vn) @foreach($opd_hn as $hn)
                    <td align="left">จำนวนผู้ป่วยนอก OPD Visit</td> 
                    <td align="center">{{number_format($vn->visit)}}</td>     
                    <td align="center">{{number_format($hn->visit)}}</td>  
                    <td align="center">{{number_format($vn->ucs)}}</td>     
                    <td align="center">{{number_format($hn->ucs)}}</td>  
                    <td align="center">{{number_format($vn->ofc)}}</td>     
                    <td align="center">{{number_format($hn->ofc)}}</td>  
                    <td align="center">{{number_format($vn->sss)}}</td>     
                    <td align="center">{{number_format($hn->sss)}}</td>  
                    <td align="center">{{number_format($vn->lgo)}}</td>     
                    <td align="center">{{number_format($hn->lgo)}}</td> 
                    <td align="center">{{number_format($vn->fss)}}</td>     
                    <td align="center">{{number_format($hn->fss)}}</td> 
                    <td align="center">{{number_format($vn->stp)}}</td>     
                    <td align="center">{{number_format($hn->stp)}}</td> 
                    <td align="center">{{number_format($vn->pay)}}</td>     
                    <td align="center">{{number_format($hn->pay)}}</td>              
                </tr>@endforeach @endforeach
                <tr>@foreach($refer_vn as $vn) @foreach($refer_hn as $hn)
                    <td align="left">จำนวนผู้ป่วยส่งต่อ Refer</td> 
                    <td align="center">{{number_format($vn->visit)}}</td>     
                    <td align="center">{{number_format($hn->visit)}}</td>  
                    <td align="center">{{number_format($vn->ucs)}}</td>     
                    <td align="center">{{number_format($hn->ucs)}}</td>  
                    <td align="center">{{number_format($vn->ofc)}}</td>     
                    <td align="center">{{number_format($hn->ofc)}}</td>  
                    <td align="center">{{number_format($vn->sss)}}</td>     
                    <td align="center">{{number_format($hn->sss)}}</td>  
                    <td align="center">{{number_format($vn->lgo)}}</td>     
                    <td align="center">{{number_format($hn->lgo)}}</td> 
                    <td align="center">{{number_format($vn->fss)}}</td>     
                    <td align="center">{{number_format($hn->fss)}}</td> 
                    <td align="center">{{number_format($vn->stp)}}</td>     
                    <td align="center">{{number_format($hn->stp)}}</td> 
                    <td align="center">{{number_format($vn->pay)}}</td>     
                    <td align="center">{{number_format($hn->pay)}}</td>   
                </tr>@endforeach @endforeach
                <tr>@foreach($er_vn as $vn) @foreach($er_hn as $hn)
                    <td align="left">จำนวนผู้ป่วยอุบัติเหตุ-ฉุกเฉิน</td> 
                    <td align="center">{{number_format($vn->visit)}}</td>     
                    <td align="center">{{number_format($hn->visit)}}</td>  
                    <td align="center">{{number_format($vn->ucs)}}</td>     
                    <td align="center">{{number_format($hn->ucs)}}</td>  
                    <td align="center">{{number_format($vn->ofc)}}</td>     
                    <td align="center">{{number_format($hn->ofc)}}</td>  
                    <td align="center">{{number_format($vn->sss)}}</td>     
                    <td align="center">{{number_format($hn->sss)}}</td>  
                    <td align="center">{{number_format($vn->lgo)}}</td>     
                    <td align="center">{{number_format($hn->lgo)}}</td> 
                    <td align="center">{{number_format($vn->fss)}}</td>     
                    <td align="center">{{number_format($hn->fss)}}</td> 
                    <td align="center">{{number_format($vn->stp)}}</td>     
                    <td align="center">{{number_format($hn->stp)}}</td> 
                    <td align="center">{{number_format($vn->pay)}}</td>     
                    <td align="center">{{number_format($hn->pay)}}</td>    
                </tr>@endforeach @endforeach
                <tr>@foreach($dmht_vn as $vn) @foreach($dmht_hn as $hn)
                    <td align="left">จำนวนผู้ป่วยเบาหวาน-ความดัน</td> 
                    <td align="center">{{number_format($vn->visit)}}</td>     
                    <td align="center">{{number_format($hn->visit)}}</td>  
                    <td align="center">{{number_format($vn->ucs)}}</td>     
                    <td align="center">{{number_format($hn->ucs)}}</td>  
                    <td align="center">{{number_format($vn->ofc)}}</td>     
                    <td align="center">{{number_format($hn->ofc)}}</td>  
                    <td align="center">{{number_format($vn->sss)}}</td>     
                    <td align="center">{{number_format($hn->sss)}}</td>  
                    <td align="center">{{number_format($vn->lgo)}}</td>     
                    <td align="center">{{number_format($hn->lgo)}}</td> 
                    <td align="center">{{number_format($vn->fss)}}</td>     
                    <td align="center">{{number_format($hn->fss)}}</td> 
                    <td align="center">{{number_format($vn->stp)}}</td>     
                    <td align="center">{{number_format($hn->stp)}}</td> 
                    <td align="center">{{number_format($vn->pay)}}</td>     
                    <td align="center">{{number_format($hn->pay)}}</td>   
                </tr>@endforeach @endforeach
                <tr>@foreach($physic_vn as $vn) @foreach($physic_hn as $hn)
                    <td align="left">จำนวนผู้มารับบริการกายภาพบำบัด</td> 
                    <td align="center">{{number_format($vn->visit)}}</td>     
                    <td align="center">{{number_format($hn->visit)}}</td>  
                    <td align="center">{{number_format($vn->ucs)}}</td>     
                    <td align="center">{{number_format($hn->ucs)}}</td>  
                    <td align="center">{{number_format($vn->ofc)}}</td>     
                    <td align="center">{{number_format($hn->ofc)}}</td>  
                    <td align="center">{{number_format($vn->sss)}}</td>     
                    <td align="center">{{number_format($hn->sss)}}</td>  
                    <td align="center">{{number_format($vn->lgo)}}</td>     
                    <td align="center">{{number_format($hn->lgo)}}</td> 
                    <td align="center">{{number_format($vn->fss)}}</td>     
                    <td align="center">{{number_format($hn->fss)}}</td> 
                    <td align="center">{{number_format($vn->stp)}}</td>     
                    <td align="center">{{number_format($hn->stp)}}</td> 
                    <td align="center">{{number_format($vn->pay)}}</td>     
                    <td align="center">{{number_format($hn->pay)}}</td>         
                </tr>@endforeach @endforeach
                <tr>@foreach($healthmed_vn as $vn) @foreach($healthmed_hn as $hn)
                    <td align="left">จำนวนผู้มารับบริการแพทย์แผนไทย</td> 
                    <td align="center">{{number_format($vn->visit)}}</td>     
                    <td align="center">{{number_format($hn->visit)}}</td>  
                    <td align="center">{{number_format($vn->ucs)}}</td>     
                    <td align="center">{{number_format($hn->ucs)}}</td>  
                    <td align="center">{{number_format($vn->ofc)}}</td>     
                    <td align="center">{{number_format($hn->ofc)}}</td>  
                    <td align="center">{{number_format($vn->sss)}}</td>     
                    <td align="center">{{number_format($hn->sss)}}</td>  
                    <td align="center">{{number_format($vn->lgo)}}</td>     
                    <td align="center">{{number_format($hn->lgo)}}</td> 
                    <td align="center">{{number_format($vn->fss)}}</td>     
                    <td align="center">{{number_format($hn->fss)}}</td> 
                    <td align="center">{{number_format($vn->stp)}}</td>     
                    <td align="center">{{number_format($hn->stp)}}</td> 
                    <td align="center">{{number_format($vn->pay)}}</td>     
                    <td align="center">{{number_format($hn->pay)}}</td>     
                </tr>@endforeach @endforeach
                <tr>@foreach($dent_vn as $vn) @foreach($dent_hn as $hn)
                    <td align="left">จำนวนผู้มารับบริการทันตกรรม</td> 
                    <td align="center">{{number_format($vn->visit)}}</td>     
                    <td align="center">{{number_format($hn->visit)}}</td>  
                    <td align="center">{{number_format($vn->ucs)}}</td>     
                    <td align="center">{{number_format($hn->ucs)}}</td>  
                    <td align="center">{{number_format($vn->ofc)}}</td>     
                    <td align="center">{{number_format($hn->ofc)}}</td>  
                    <td align="center">{{number_format($vn->sss)}}</td>     
                    <td align="center">{{number_format($hn->sss)}}</td>  
                    <td align="center">{{number_format($vn->lgo)}}</td>     
                    <td align="center">{{number_format($hn->lgo)}}</td> 
                    <td align="center">{{number_format($vn->fss)}}</td>     
                    <td align="center">{{number_format($hn->fss)}}</td> 
                    <td align="center">{{number_format($vn->stp)}}</td>     
                    <td align="center">{{number_format($hn->stp)}}</td> 
                    <td align="center">{{number_format($vn->pay)}}</td>     
                    <td align="center">{{number_format($hn->pay)}}</td>         
                </tr>@endforeach @endforeach
                <tr>@foreach($ipd as $ip) @foreach($ipd_ucs as $ip_ucs) @foreach($ipd_ofc as $ip_ofc) 
                    @foreach($ipd_sss as $ip_sss) @foreach($ipd_lgo as $ip_lgo) @foreach($ipd_fss as $ip_fss) 
                    @foreach($ipd_stp as $ip_stp) @foreach($ipd_pay as $ip_pay)
                    <td align="left">จำนวนผู้ป่วยใน (AN)</td> 
                    <td align="center" colspan= "2">{{number_format($ip->an)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_ucs->an)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_ofc->an)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_sss->an)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_lgo->an)}}</td> 
                    <td align="center" colspan= "2">{{number_format($ip_fss->an)}}</td>
                    <td align="center" colspan= "2">{{number_format($ip_stp->an)}}</td> 
                    <td align="center" colspan= "2">{{number_format($ip_pay->an)}}</td>   
                </tr>
                <tr>
                    <td align="left">วันนอนรวม</td> 
                    <td align="center" colspan= "2">{{number_format($ip->admdate)}}</td>   
                    <td align="center" colspan= "2">{{number_format($ip_ucs->admdate)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_ofc->admdate)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_sss->admdate)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_lgo->admdate)}}</td> 
                    <td align="center" colspan= "2">{{number_format($ip_fss->admdate)}}</td>
                    <td align="center" colspan= "2">{{number_format($ip_stp->admdate)}}</td> 
                    <td align="center" colspan= "2">{{number_format($ip_pay->admdate)}}</td>   
                </tr>
                <tr>
                    <td align="left">อัตราครองเตียง</td> 
                    <td align="center" colspan= "2">{{number_format($ip->bed_occupancy,2)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_ucs->bed_occupancy,2)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_ofc->bed_occupancy,2)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_sss->bed_occupancy,2)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_lgo->bed_occupancy,2)}}</td> 
                    <td align="center" colspan= "2">{{number_format($ip_fss->bed_occupancy,2)}}</td>
                    <td align="center" colspan= "2">{{number_format($ip_stp->bed_occupancy,2)}}</td> 
                    <td align="center" colspan= "2">{{number_format($ip_pay->bed_occupancy,2)}}</td>  
                </tr>
                </tr>
                <tr>
                    <td align="left">Active Base</td> 
                    <td align="center" colspan= "2">{{number_format($ip->active_bed,2)}}</td>   
                    <td align="center" colspan= "2">{{number_format($ip_ucs->active_bed,2)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_ofc->active_bed,2)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_sss->active_bed,2)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_lgo->active_bed,2)}}</td> 
                    <td align="center" colspan= "2">{{number_format($ip_fss->active_bed,2)}}</td>
                    <td align="center" colspan= "2">{{number_format($ip_stp->active_bed,2)}}</td> 
                    <td align="center" colspan= "2">{{number_format($ip_pay->active_bed,2)}}</td>  
                </tr>
                <tr>
                    <td align="left">AdjRW</td> 
                    <td align="center" colspan= "2">{{number_format($ip->adjrw,2)}}</td>   
                    <td align="center" colspan= "2">{{number_format($ip_ucs->adjrw,2)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_ofc->adjrw,2)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_sss->adjrw,2)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_lgo->adjrw,2)}}</td> 
                    <td align="center" colspan= "2">{{number_format($ip_fss->adjrw,2)}}</td>
                    <td align="center" colspan= "2">{{number_format($ip_stp->adjrw,2)}}</td> 
                    <td align="center" colspan= "2">{{number_format($ip_pay->adjrw,2)}}</td>  
                </tr>
                <tr>
                    <td align="left">CMI</td> 
                    <td align="center" colspan= "2">{{number_format($ip->cmi,2)}}</td>   
                    <td align="center" colspan= "2">{{number_format($ip_ucs->cmi,2)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_ofc->cmi,2)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_sss->cmi,2)}}</td>  
                    <td align="center" colspan= "2">{{number_format($ip_lgo->cmi,2)}}</td> 
                    <td align="center" colspan= "2">{{number_format($ip_fss->cmi,2)}}</td>
                    <td align="center" colspan= "2">{{number_format($ip_stp->cmi,2)}}</td> 
                    <td align="center" colspan= "2">{{number_format($ip_pay->cmi,2)}}</td>  
                </tr>@endforeach @endforeach @endforeach @endforeach 
                @endforeach @endforeach @endforeach @endforeach 
            </table>   
          </div>       
      </div>      
    </div>
  </div>
</div>
<br>
@endsection