@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-12">
        <form method="POST" enctype="multipart/form-data">
        @csrf
            <div class="row">
                <div class="col-md-9" align="left">
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
<!-- row -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลบริการผู้ป่วยในรวม ปีงบประมาณ {{ $budget_year }}</div>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-secondary">
                        <th class="text-center">เดือน</th>
                        <th class="text-center">จำนวน AN</th>
                        <th class="text-center">วันนอนรวม</th>
                        <th class="text-center">อัตราครองเตียง</th>
                        <th class="text-center">Active Base</th>
                        <th class="text-center">AdjRW</th>
                        <th class="text-center">CMI</th>
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    @foreach($ipd as $row)
                    <tr>
                        <td align="center">{{ $row->month }}</td>
                        <td align="right">{{ number_format($row->an) }}</td>
                        <td align="right">{{ number_format($row->admdate) }}</td>
                        <td align="right">{{ $row->bed_occupancy }}</td>
                        <td align="right">{{ $row->active_bed }}</td>
                        <td align="right">{{ $row->adjrw }}</td>
                        <td align="right">{{ $row->cmi }}</td>
                    </tr>
                    <?php $count++; ?> 
                    @endforeach
                    @foreach($ipd_sum as $row)
                    <tr>
                        <td align="center"><strong>รวม</strong></td>
                        <td align="right"><strong>{{ number_format($row->an) }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->admdate) }}</strong></td>
                        <td align="right"><strong>{{ $row->bed_occupancy }}</strong></td>
                        <td align="right"><strong>{{ $row->active_bed }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->adjrw,2) }}</strong></td>
                        <td align="right"><strong>{{ $row->cmi }}</strong></td>
                    </tr>
                    @endforeach
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
                <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลบริการผู้ป่วยใน-อายุรกรรมโรคไต ปีงบประมาณ {{ $budget_year }}</div>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-secondary">
                        <th class="text-center">เดือน</th>
                        <th class="text-center">จำนวน AN</th>
                        <th class="text-center">วันนอนรวม</th>
                        <th class="text-center">อัตราครองเตียง</th>
                        <th class="text-center">Active Base</th>
                        <th class="text-center">AdjRW</th>
                        <th class="text-center">CMI</th>
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    @foreach($ipd_kidney as $row)
                    <tr>
                        <td align="center">{{ $row->month }}</td>
                        <td align="right">{{ number_format($row->an) }}</td>
                        <td align="right">{{ number_format($row->admdate) }}</td>
                        <td align="right">{{ $row->bed_occupancy }}</td>
                        <td align="right">{{ $row->active_bed }}</td>
                        <td align="right">{{ $row->adjrw }}</td>
                        <td align="right">{{ $row->cmi }}</td>
                    </tr>
                    <?php $count++; ?> 
                    @endforeach
                    @foreach($ipd_sum_kidney as $row)
                    <tr>
                        <td align="center"><strong>รวม</strong></td>
                        <td align="right"><strong>{{ number_format($row->an) }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->admdate) }}</strong></td>
                        <td align="right"><strong>{{ $row->bed_occupancy }}</strong></td>
                        <td align="right"><strong>{{ $row->active_bed }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->adjrw,2) }}</strong></td>
                        <td align="right"><strong>{{ $row->cmi }}</strong></td>
                    </tr>
                    @endforeach
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
                <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลบริการผู้ป่วยใน-นรีเวชกรรม ปีงบประมาณ {{ $budget_year }}</div>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-secondary">
                        <th class="text-center">เดือน</th>
                        <th class="text-center">จำนวน AN</th>
                        <th class="text-center">วันนอนรวม</th>
                        <th class="text-center">อัตราครองเตียง</th>
                        <th class="text-center">Active Base</th>
                        <th class="text-center">AdjRW</th>
                        <th class="text-center">CMI</th>
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    @foreach($ipd_obs as $row)
                    <tr>
                        <td align="center">{{ $row->month }}</td>
                        <td align="right">{{ number_format($row->an) }}</td>
                        <td align="right">{{ number_format($row->admdate) }}</td>
                        <td align="right">{{ $row->bed_occupancy }}</td>
                        <td align="right">{{ $row->active_bed }}</td>
                        <td align="right">{{ $row->adjrw }}</td>
                        <td align="right">{{ $row->cmi }}</td>
                    </tr>
                    <?php $count++; ?> 
                    @endforeach
                    @foreach($ipd_sum_obs as $row)
                    <tr>
                        <td align="center"><strong>รวม</strong></td>
                        <td align="right"><strong>{{ number_format($row->an) }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->admdate) }}</strong></td>
                        <td align="right"><strong>{{ $row->bed_occupancy }}</strong></td>
                        <td align="right"><strong>{{ $row->active_bed }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->adjrw,2) }}</strong></td>
                        <td align="right"><strong>{{ $row->cmi }}</strong></td>
                    </tr>
                    @endforeach
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
                <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลบริการผู้ป่วยใน-DKA ปีงบประมาณ {{ $budget_year }}</div>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-secondary">
                        <th class="text-center">เดือน</th>
                        <th class="text-center">จำนวน AN</th>
                        <th class="text-center">วันนอนรวม</th>
                        <th class="text-center">อัตราครองเตียง</th>
                        <th class="text-center">Active Base</th>
                        <th class="text-center">AdjRW</th>
                        <th class="text-center">CMI</th>
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    @foreach($ipd_dka as $row)
                    <tr>
                        <td align="center">{{ $row->month }}</td>
                        <td align="right">{{ number_format($row->an) }}</td>
                        <td align="right">{{ number_format($row->admdate) }}</td>
                        <td align="right">{{ $row->bed_occupancy }}</td>
                        <td align="right">{{ $row->active_bed }}</td>
                        <td align="right">{{ $row->adjrw }}</td>
                        <td align="right">{{ $row->cmi }}</td>
                    </tr>
                    <?php $count++; ?> 
                    @endforeach
                    @foreach($ipd_sum_dka as $row)
                    <tr>
                        <td align="center"><strong>รวม</strong></td>
                        <td align="right"><strong>{{ number_format($row->an) }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->admdate) }}</strong></td>
                        <td align="right"><strong>{{ $row->bed_occupancy }}</strong></td>
                        <td align="right"><strong>{{ $row->active_bed }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->adjrw,2) }}</strong></td>
                        <td align="right"><strong>{{ $row->cmi }}</strong></td>
                    </tr>
                    @endforeach
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
                <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลบริการผู้ป่วยใน-HHS ปีงบประมาณ {{ $budget_year }}</div>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-secondary">
                        <th class="text-center">เดือน</th>
                        <th class="text-center">จำนวน AN</th>
                        <th class="text-center">วันนอนรวม</th>
                        <th class="text-center">อัตราครองเตียง</th>
                        <th class="text-center">Active Base</th>
                        <th class="text-center">AdjRW</th>
                        <th class="text-center">CMI</th>
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    @foreach($ipd_hhs as $row)
                    <tr>
                        <td align="center">{{ $row->month }}</td>
                        <td align="right">{{ number_format($row->an) }}</td>
                        <td align="right">{{ number_format($row->admdate) }}</td>
                        <td align="right">{{ $row->bed_occupancy }}</td>
                        <td align="right">{{ $row->active_bed }}</td>
                        <td align="right">{{ $row->adjrw }}</td>
                        <td align="right">{{ $row->cmi }}</td>
                    </tr>
                    <?php $count++; ?> 
                    @endforeach
                    @foreach($ipd_sum_hhs as $row)
                    <tr>
                        <td align="center"><strong>รวม</strong></td>
                        <td align="right"><strong>{{ number_format($row->an) }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->admdate) }}</strong></td>
                        <td align="right"><strong>{{ $row->bed_occupancy }}</strong></td>
                        <td align="right"><strong>{{ $row->active_bed }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->adjrw,2) }}</strong></td>
                        <td align="right"><strong>{{ $row->cmi }}</strong></td>
                    </tr>
                    @endforeach
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
                <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลบริการผู้ป่วยใน-Septic Shock ปีงบประมาณ {{ $budget_year }}</div>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-secondary">
                        <th class="text-center">เดือน</th>
                        <th class="text-center">จำนวน AN</th>
                        <th class="text-center">วันนอนรวม</th>
                        <th class="text-center">อัตราครองเตียง</th>
                        <th class="text-center">Active Base</th>
                        <th class="text-center">AdjRW</th>
                        <th class="text-center">CMI</th>
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    @foreach($ipd_septicshock as $row)
                    <tr>
                        <td align="center">{{ $row->month }}</td>
                        <td align="right">{{ number_format($row->an) }}</td>
                        <td align="right">{{ number_format($row->admdate) }}</td>
                        <td align="right">{{ $row->bed_occupancy }}</td>
                        <td align="right">{{ $row->active_bed }}</td>
                        <td align="right">{{ $row->adjrw }}</td>
                        <td align="right">{{ $row->cmi }}</td>
                    </tr>
                    <?php $count++; ?> 
                    @endforeach
                    @foreach($ipd_sum_septicshock as $row)
                    <tr>
                        <td align="center"><strong>รวม</strong></td>
                        <td align="right"><strong>{{ number_format($row->an) }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->admdate) }}</strong></td>
                        <td align="right"><strong>{{ $row->bed_occupancy }}</strong></td>
                        <td align="right"><strong>{{ $row->active_bed }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->adjrw,2) }}</strong></td>
                        <td align="right"><strong>{{ $row->cmi }}</strong></td>
                    </tr>
                    @endforeach
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
                <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลบริการผู้ป่วยใน-Pneumonia ปีงบประมาณ {{ $budget_year }}</div>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-secondary">
                        <th class="text-center">เดือน</th>
                        <th class="text-center">จำนวน AN</th>
                        <th class="text-center">วันนอนรวม</th>
                        <th class="text-center">อัตราครองเตียง</th>
                        <th class="text-center">Active Base</th>
                        <th class="text-center">AdjRW</th>
                        <th class="text-center">CMI</th>
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    @foreach($ipd_pneumonia as $row)
                    <tr>
                        <td align="center">{{ $row->month }}</td>
                        <td align="right">{{ number_format($row->an) }}</td>
                        <td align="right">{{ number_format($row->admdate) }}</td>
                        <td align="right">{{ $row->bed_occupancy }}</td>
                        <td align="right">{{ $row->active_bed }}</td>
                        <td align="right">{{ $row->adjrw }}</td>
                        <td align="right">{{ $row->cmi }}</td>
                    </tr>
                    <?php $count++; ?> 
                    @endforeach
                    @foreach($ipd_sum_pneumonia as $row)
                    <tr>
                        <td align="center"><strong>รวม</strong></td>
                        <td align="right"><strong>{{ number_format($row->an) }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->admdate) }}</strong></td>
                        <td align="right"><strong>{{ $row->bed_occupancy }}</strong></td>
                        <td align="right"><strong>{{ $row->active_bed }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->adjrw,2) }}</strong></td>
                        <td align="right"><strong>{{ $row->cmi }}</strong></td>
                    </tr>
                    @endforeach
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
                <div class="card-header bg-primary bg-opacity-75 text-white">ข้อมูลบริการผู้ป่วยใน-จักษุวิทยา ปีงบประมาณ {{ $budget_year }}</div>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-secondary">
                        <th class="text-center">เดือน</th>
                        <th class="text-center">จำนวน AN</th>
                        <th class="text-center">วันนอนรวม</th>
                        <th class="text-center">อัตราครองเตียง</th>
                        <th class="text-center">Active Base</th>
                        <th class="text-center">AdjRW</th>
                        <th class="text-center">CMI</th>
                    </tr>
                    </thead>
                    <?php $count = 1 ; ?>
                    @foreach($ipd_sur as $row)
                    <tr>
                        <td align="center">{{ $row->month }}</td>
                        <td align="right">{{ number_format($row->an) }}</td>
                        <td align="right">{{ number_format($row->admdate) }}</td>
                        <td align="right">{{ $row->bed_occupancy }}</td>
                        <td align="right">{{ $row->active_bed }}</td>
                        <td align="right">{{ $row->adjrw }}</td>
                        <td align="right">{{ $row->cmi }}</td>
                    </tr>
                    <?php $count++; ?> 
                    @endforeach
                    @foreach($ipd_sum_sur as $row)
                    <tr>
                        <td align="center"><strong>รวม</strong></td>
                        <td align="right"><strong>{{ number_format($row->an) }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->admdate) }}</strong></td>
                        <td align="right"><strong>{{ $row->bed_occupancy }}</strong></td>
                        <td align="right"><strong>{{ $row->active_bed }}</strong></td>
                        <td align="right"><strong>{{ number_format($row->adjrw,2) }}</strong></td>
                        <td align="right"><strong>{{ $row->cmi }}</strong></td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
</br>
@endsection
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
