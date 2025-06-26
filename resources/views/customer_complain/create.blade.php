<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >แสดงความคิดเห็น/เสนอแนะ</title>

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
        <div class="card-header bg-primary text-white text-center"><h5><strong>แบบฟอร์มแสดงความคิดเห็น/เสนอแนะ</strong></h5></div>
        <div class="card-body">
            <div class="row mb-3"> 
                @if ($message = Session::get('success'))
                <div class="alert alert-success text-center">
                <h5><strong>{{ $message }}</strong></h5>
                </div>
                @endif
            </div>
            <form action="{{ route('customer_complain.store') }}" method="POST" enctype="multipart/form-data">
                @csrf 
                <div class="mb-3">  
                    <label class="form-label"><strong>ประเภทของความคิดเห็น</strong></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" id="type1" value="คำชมเชย" checked>
                        <label class="form-check-label" for="type1">คำชมเชย</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" id="type2" value="ข้อเสนอแนะ">
                        <label class="form-check-label" for="type2">ข้อเสนอแนะ</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" id="type3" value="ข้อร้องเรียน">
                        <label class="form-check-label" for="type3">ข้อร้องเรียน</label>
                    </div>                  
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" id="type4" value="อื่น ๆ">
                        <label class="form-check-label" for="type4">อื่น ๆ</label>
                    </div> 
                </div> 
                <div class="mb-3">
                    <label class="form-label" for="name"><strong>ชื่อ-สกุล</strong></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="กรุณาระบุชื่อ-สกุล หรือพิมพ์ ไม่ระบุ">
                    @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>                  
                <div class="mb-3">
                    <label for="detail" class="form-label"><strong>รายละเอียด</strong></label>
                    <textarea class="form-control" name ="detail" id="detail" rows="4" 
                    placeholder="กรุณาระบุรายละเอียดที่ท่านต้องการแสดงความคิดเห็น/เสนอแนะ"></textarea>
                </div> 
                <div class="mb-3">  
                    <label class="form-label"><strong>ท่านต้องการให้ติดต่อกลับหรือไม่</strong></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="call_back" id="call_back2" value="ไม่ต้องการ" checked>
                        <label class="form-check-label" for="call_back2">ไม่ต้องการ</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="call_back" id="call_back1" value="ต้องการ" >
                        <label class="form-check-label" for="call_back1">ต้องการ</label>
                    </div>
                </div> 
                <div class="mb-3">
                    <label class="form-label" for="phone"><strong>หมายเลขโทรศัพท์</strong></label>
                    <input type="tel" name="phone" id="phone" class="form-control" maxlength="10" placeholder="ถ้าต้องการให้ติดต่อกลับทางโทรศัพท์ ระบุหมายเลขโทรศัพท์ 10 หลัก">
                    @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>     
                <div class="mb-3">
                    <label class="form-label" for="email"><strong>Email Address</strong></label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="ถ้าต้องการให้ติดต่อกลับทาง Email Address (ตัวอย่าง name@example.com)">
                    @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div> 
                <h4 align=center><font color = "red">ข้อมูลชื่อ-สกุล และเบอร์โทรของท่านจะเป็นความลับ<br>ไม่มีผลใด ๆ ต่อท่านที่แสดงความคิดเห็น/เสนอแนะ</font></h4>         
                <div class="mb-3 text-center">
                    <button type="submit" class="btn btn-primary mt-3">ส่งข้อมูล</button>
                    <button type="reset" class="btn btn-secondary mt-3">Reset</button>
                </div>                
            </form>
        </div>
    </div>
<!-- </div> -->
</body>
</html>