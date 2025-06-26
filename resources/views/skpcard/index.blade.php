@extends('layouts.app')

@section('content')
    
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
            @foreach($count as $row)
                <div class="row">                                             
                    <div class="col-md-6" align="left"> 
                        <h5 class="card-title text-primary">บัตรสังฆประชาร่วมใจ {{ number_format($row->total) }} ใบ</h5>   
                        <h5 class="card-title text-primary">หมดอายุแล้ว {{ number_format($row->exprie) }} ใบ</h5> 
                        <h5 class="card-title text-primary">ใช้งานปกติ {{ number_format($row->normal) }} ใบ</h5>                      
                    </div>                                                            
                    <div class="col-md-6"  align="right">
                        <a href="{{ route('skpcard.create') }}" class="btn btn-success ">เพิ่มข้อมูลบัตร</a>
                    </div>
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <H5>{{ $message }}</H5>
                    </div>
                    @endif
                </div> 
            @endforeach 
            <form method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row" >
                    <!-- <label class="col-md-2 col-form-label text-md-end">{{ __('วันที่') }}</label>
                    <div class="col-md-2">
                    <input type="date" name="begin_date" class="form-control" placeholder="Date" value="{{ date('Y-m-d') }}"> 
                    </div>
                    <label class="col-md-1 col-form-label text-md-end">{{ __('ถึง') }}</label>
                    <div class="col-md-2">
                    <input type="date" name="end_date" class="form-control" placeholder="Date" value="{{ date('Y-m-d') }}"> 
                    </div> -->
                    <label class="col-md-9 col-form-label text-md-end text-primary">{{ __('ค้นตามเลขบัตรประชาชน หรือ ชื่อ-สกุล') }}</label>
                    <div class="col-md-2" >
                        <input id="search" type="text" class="form-control my-1" name="search" value="{{ old('search') }}" autocomplete="search" autofocus>
                    </div>
                    <div class="col-md-1"  align="right">                            
                        <button type="submit" class="btn btn-primary my-1">{{ __('ค้นหา') }}</button>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-striped">
                <tbody>
                    <tr class="table-primary">
                        <th width="5%">รหัส</th>
                        <th width="12%" class="text-center">เลขบัตรประชาชน</th>
                        <th width="29%" >ชื่อ-สกุล</th>
                        <th width="9%" class="text-center">อายุ</th>
                        <th width="9%" class="text-center">ซื้อบัตร</th>
                        <th width="9%" class="text-center">วันหมดอายุ</th>
                        <th width="9%" class="text-center">ราคา</th>
                        <th width="9%" class="text-center">สถานะ</th>
                        <th width="9%" class="text-center">ทำรายการ</th>
                    </tr>     
                   
                @foreach($skpcard as $row1)
                    <tr>
                        <td>{{ $row1->id }}</td>
                        <td class="text-center">{{ $row1->cid }}</td>
                        <td>{{ $row1->name }}</td>
                        <td class="text-center">{{ $row1->age }}</td>
                        <td class="text-center">{{ DateThai($row1->buy_date) }}</td>
                        <td class="text-center">{{ DateThai($row1->ex_date) }}</td>
                        <td class="text-center">{{ number_format($row1->price) }}</td>
                        <td class="text-center">{{ $row1->status }}</td>
                        <td class="text-center">                      
                            <a href="{{ route('skpcard.edit', $row1->id) }}" class="btn btn-warning">แก้ไข</a> 
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
                {!! $skpcard->links('pagination::bootstrap-4') !!}        
            </div>
        </div>
    </div>
<br>
<!-- row -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="row">                          
                <div class="col-md-12" align="left"> 
                    <h5 class="card-title my-3 text-primary ">จำนวนบัตรสังฆประชาร่วมใจแยกรายเดือนตามปีงบประมาณ</h5>          
                </div>               
            </div>   

            <table class="table table-bordered table-striped">
                <tbody>
                    <tr class="table-primary">
                        <th width="6%">ปีงบประมาณ</th>
                        <th width="7%" class="text-center">ต.ค.</th>
                        <th width="7%" class="text-center">พ.ย.</th>
                        <th width="7%" class="text-center">ธ.ค.</th>
                        <th width="7%" class="text-center">ม.ค.</th>
                        <th width="7%" class="text-center">ก.พ.</th>
                        <th width="7%" class="text-center">มี.ค.</th>
                        <th width="7%" class="text-center">เม.ย.</th>
                        <th width="7%" class="text-center">พ.ค.</th>
                        <th width="7%" class="text-center">มิ.ย.</th>
                        <th width="7%" class="text-center">ก.ค.</th>
                        <th width="7%" class="text-center">ส.ค.</th>
                        <th width="7%" class="text-center">ก.ย.</th>
                        <th width="10%" class="text-center">รวม(ใบ)</th>
                    </tr>                                  
                @foreach($sum as $row2)
                    <tr>
                        <td class="text-center">{{ $row2->year_bud }}</td>
                        <td class="text-center">{{ $row2->month_10 }}</td>
                        <td class="text-center">{{ $row2->month_11 }}</td>
                        <td class="text-center">{{ $row2->month_12 }}</td>
                        <td class="text-center">{{ $row2->month_1 }}</td>
                        <td class="text-center">{{ $row2->month_2 }}</td>
                        <td class="text-center">{{ $row2->month_3 }}</td>
                        <td class="text-center">{{ $row2->month_4 }}</td>
                        <td class="text-center">{{ $row2->month_5 }}</td>
                        <td class="text-center">{{ $row2->month_6 }}</td>
                        <td class="text-center">{{ $row2->month_7 }}</td>
                        <td class="text-center">{{ $row2->month_8 }}</td>
                        <td class="text-center">{{ $row2->month_9 }}</td>
                        <td class="text-center">{{ $row2->total }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table> 
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="row">                          
                <div class="col-md-12" align="left"> 
                    <h5 class="card-title my-3 text-primary ">รายได้บัตรสังฆประชาร่วมใจแยกรายเดือนตามปีงบประมาณ</h5>          
                </div>               
            </div>   
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr class="table-primary">
                        <th width="6%">ปีงบประมาณ</th>
                        <th width="7%" class="text-center">ต.ค.</th>
                        <th width="7%" class="text-center">พ.ย.</th>
                        <th width="7%" class="text-center">ธ.ค.</th>
                        <th width="7%" class="text-center">ม.ค.</th>
                        <th width="7%" class="text-center">ก.พ.</th>
                        <th width="7%" class="text-center">มี.ค.</th>
                        <th width="7%" class="text-center">เม.ย.</th>
                        <th width="7%" class="text-center">พ.ค.</th>
                        <th width="7%" class="text-center">มิ.ย.</th>
                        <th width="7%" class="text-center">ก.ค.</th>
                        <th width="7%" class="text-center">ส.ค.</th>
                        <th width="7%" class="text-center">ก.ย.</th>
                        <th width="10%" class="text-center">รวม(บาท)</th>
                    </tr>   
                @foreach($sum as $row2)
                    <tr>
                        <td class="text-center">{{ $row2->year_bud }}</td>
                        <td class="text-center">{{ number_format($row2->price_10) }}</td>
                        <td class="text-center">{{ number_format($row2->price_11) }}</td>
                        <td class="text-center">{{ number_format($row2->price_12) }}</td>
                        <td class="text-center">{{ number_format($row2->price_1) }}</td>
                        <td class="text-center">{{ number_format($row2->price_2) }}</td>
                        <td class="text-center">{{ number_format($row2->price_3) }}</td>
                        <td class="text-center">{{ number_format($row2->price_4) }}</td>
                        <td class="text-center">{{ number_format($row2->price_5) }}</td>
                        <td class="text-center">{{ number_format($row2->price_6) }}</td>
                        <td class="text-center">{{ number_format($row2->price_7) }}</td>
                        <td class="text-center">{{ number_format($row2->price_8) }}</td>
                        <td class="text-center">{{ number_format($row2->price_9) }}</td>
                        <td class="text-center">{{ number_format($row2->price) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table> 
        </div>
    </div>
</div>
@endsection

