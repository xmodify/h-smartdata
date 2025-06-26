@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h5 class="alert alert-primary"><strong>รายงานการแสดงความคิดเห็น/เสนอแนะ/ร้องเรียน ของผู้มารับบริการ</strong></h5>  
</div> 
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
<!--row-->   
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div> 
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr class="table-primary">
                        <th class="text-center" >ลำดับ</th> 
                        <th class="text-center" >วันที่-เวลา</th>
                        <th class="text-center" >ประเภท</th>
                        <th class="text-center" >ชื่อสกุล</th>
                        <th class="text-center" >รายละเอียด</th>
                        <th class="text-center" >ติดต่อกลับ</th>
                        <th class="text-center" >โทรศัพท์</th>
                        <th class="text-center" >Email</th>
                    </tr> 
                <?php $count = 1 ; ?>
                @foreach($complain as $row)
                    <tr>
                        <td>{{ $count }}</td>
                        <td>{{ DatetimeThai($row->created_at) }}</td>   
                        <td>{{ $row->type }}</td>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->detail }}</td>
                        <td>{{ $row->call_back }}</td>
                        <td>{{ $row->phone }}</td>
                        <td>{{ $row->email }}</td>
                    </tr>
                <?php $count++; ?>
                @endforeach
                </tbody>
            </table>
    </div>
</div>

@endsection
