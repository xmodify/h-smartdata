@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="row">            
                    <div class="col-md-10" align="left"> 
                        <h4 class="card-title my-3">เพิ่มข้อมูลบัตรสังฆประชาร่วมใจ ใบที่ {{ $count+1 }}</h4>
                    </div> 
                    <div class="col-md-2"  align="right">
                        <a href="{{ route('skpcard.index') }}"  class="btn btn-primary my-1">ย้อนกลับ</a>
                    </div>
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif
                </div>

            <form action="{{ route('skpcard.store') }}" method="POST" enctype="multipart/form-data">
                @csrf                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group my-2">
                            <strong>เลขบัตรประชาชน</strong>
                            <input type="text" name="cid" class="form-control" placeholder="เลขบัตรประชาชน">
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-2">
                            <strong>ชื่อ-สกุล</strong>
                            <input type="text" name="name" class="form-control" placeholder="ชื่อ-สกุล">
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-2">
                            <strong>เกิดวันที่</strong>
                            <input type="date" name="birthday" class="form-control" placeholder="วดป.เกิด">
                            @error('birthday')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-2">
                            <strong>ที่อยู่</strong>
                            <input type="text" name="address" class="form-control" placeholder="ที่อยู่">
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-2">
                            <strong>เบอร์โทรศัพท์</strong>
                            <input type="text" name="phone" class="form-control" placeholder="เบอร์โทรศัพท์">
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-2">
                            <strong>ซื้อบัตรวันที่</strong>
                            <input type="date" name="buy_date" class="form-control" placeholder="Date"
                            value="{{ date('Y-m-d') }}">
                            @error('bdate')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-2">
                            <strong>จำนวนเงิน</strong>
                            <!-- <input type="text" name="price" class="form-control" placeholder="จำนวนเงิน"> -->
                            <select class="form-select" name="price">
                            <option value="1000">1000 บาท</option>
                            <option value="1500">1500 บาท</option>
                            </select>
                            @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-2">
                            <strong>เลขที่ใบเสร็จ</strong>
                            <input type="text" name="rcpt" class="form-control" placeholder="เลขที่ใบเสร็จ">
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-12"  align="right">
                        <button type="submit" class="mt-3 btn btn-primary">ตกลง</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endsection