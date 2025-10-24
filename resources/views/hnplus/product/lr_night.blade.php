<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >ระบบบันทึกผลิตภาพทางการพยาบาล</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<!-- <div class="container"> -->
    <div class="card">        
        <h5 class="alert alert-primary text-center"><strong>ระบบบันทึกผลิตภาพทางการพยาบาล<br>แผนกห้องคลอด</strong></h5> 
        <div class="card-body">
            <div class="row mb-3">  
                @if ($message = Session::get('success'))
                <div class="alert alert-success text-center">
                <h5><strong>{{ $message }}</strong></h5>
                </div>
                @endif
            </div>
            <h5 class="text-primary">วันที่ {{DateThai(date('Y-m-d'))}} <strong>สรุปเวรดึก</strong></h5><br>
            <form action="{{ route('nurse_productivity_lr_night_save') }}" method="POST" enctype="multipart/form-data">
                @csrf 
                    <input type="hidden" name="report_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="shift_time" value="เวรดึก">
                <div class="mb-3">
                    @foreach($shift_night_opd_normal as $row) @foreach($shift_night_opd_high as $row1)
                    <label class="form-label">จำนวนผู้ป่วยนอกในเวร <strong>{{ $row->opd_normal+$row1->opd_high }}</strong> ราย</label>  
                    @endforeach  @endforeach 
                </div>
                <div class="mb-3">
                    @foreach($shift_night_opd_normal as $row)
                    <label class="form-label">- ความเร่งด่วนปกติ <strong>{{ $row->opd_normal }}</strong> ราย</label>  
                    <input type="hidden" name="opd_normal" value="{{ $row->opd_normal }}">
                    @endforeach 
                </div>
                <div class="mb-3">
                    @foreach($shift_night_opd_high as $row)
                    <label class="form-label">- ความเร่งด่วนมาก <strong>{{ $row->opd_high }}</strong> ราย</label>  
                    <input type="hidden" name="opd_high" value="{{ $row->opd_high }}">
                    @endforeach 
                </div>    
                <div class="mb-3">
                    @foreach($shift_night as $row)
                    <label class="form-label">จำนวนผู้ป่วยสูติกรรมในเวร <strong>{{ $row->patient_all }}</strong> ราย</label>  
                    <input type="hidden" name="patient_all" value="{{ $row->patient_all }}">
                    @endforeach 
                </div>  
                <div class="mb-3">
                    @foreach($shift_night as $row)
                    <label class="form-label"> - Convalescent <strong>{{ $row->convalescent }}</strong> ราย</label>  
                    <input type="hidden" name="convalescent" value="{{ $row->convalescent }}">
                    @endforeach 
                </div>  
                <div class="mb-3">
                    @foreach($shift_night as $row)
                    <label class="form-label">- Moderate ill <strong>{{ $row->moderate_ill }}</strong> ราย</label>                    
                    <input type="hidden" name="moderate_ill" value="{{ $row->moderate_ill }}">
                    @endforeach
                </div>  
                <div class="mb-3">
                    @foreach($shift_night as $row)
                    <label class="form-label">- Semi critical ill <strong>{{ $row->semi_critical_ill }}</strong> ราย</label>                    
                    <input type="hidden" name="semi_critical_ill" value="{{ $row->semi_critical_ill }}">
                    @endforeach
                </div>  
                <div class="mb-3">
                    @foreach($shift_night as $row)
                    <label class="form-label">- Critical ill <strong>{{ $row->critical_ill }}</strong> ราย</label>                    
                    <input type="hidden" name="critical_ill" value="{{ $row->critical_ill }}">
                    @endforeach  
                </div>
                <div class="mb-3">
                    @foreach($shift_night as $row)
                    <label class="form-label">- ไม่ระบุความรุนแรง <strong>{{ $row->severe_type_null }}</strong> ราย</label>                    
                    <input type="hidden" name="severe_type_null" value="{{ $row->severe_type_null }}">
                    @endforeach  
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>อัตรากำลัง Oncall (ไม่มีใส่ 0)</strong></label>                    
                    <input type="text" name="nurse_oncall" class="form-control" placeholder="อัตรากำลัง Oncall">
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>อัตรากำลังเสริม (ไม่มีใส่ 0)</strong></label>                    
                    <input type="text" name="nurse_partime" class="form-control" placeholder="อัตรากำลังเสริม">
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>อัตรากำลังปกติ</strong></label>                    
                    <input type="text" name="nurse_fulltime" class="form-control" placeholder="อัตรากำลังปกติ">
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>ผู้บันทึก</strong></label>                    
                    <input type="text" name="recorder" class="form-control" placeholder="ชื่อ-สกุล ผู้บันทึก">
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>หมายเหตุ</strong></label>                    
                    <input type="text" name="note" class="form-control" placeholder="หมายเหตุ">
                </div>
                <div class="mb-3 text-center">
                    <button type="submit" class="btn btn-primary mt-3">ส่งข้อมูล</button>
                    <button type="reset" class="btn btn-secondary mt-3">Reset</button>
                </div>                
            </form>
        </div>
    </div>
</body>
</html>