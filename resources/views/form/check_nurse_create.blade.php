<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >ระบบบันทึกเวรตรวจการพยาบาล</title>

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
        <h5 class="alert alert-primary text-center"><strong>ระบบบันทึกเวรตรวจการพยาบาล<br>
        @if($depart =='er') งานอุบัติเหตุ-ฉุกเฉิน ER @elseif($depart =='opd') งานผู้ป่วยนอก @elseif($depart =='ipd') งานผู้ป่วยในสามัญ
        @elseif($depart =='vip') งานผู้ป่วยใน VIP @elseif($depart =='hd') ศูนย์ฟอกไต HD รพ.
        @elseif($depart =='hd_outsource') ศูนย์ฟอกไต HD เอกชน @elseif($depart =='lr') งานห้องคลอด @endif </strong><br><br>
        วันที่ {{ DatetimeThai(date('Y-m-d H:i:s')) }}</h5> 
        <div class="card-body">         
            <div class="row mb-3"> 
                @if ($message = Session::get('success'))
                <div class="alert alert-success text-center">
                <h5><strong>{{ $message }}</strong></h5>
                </div>
                @endif
            </div>            
            <form action="{{ route('check_nurse_save') }}" method="POST" enctype="multipart/form-data">
                @csrf 
                <input type="hidden" id="depart" name="depart" value="{{ $depart }}">
                <div class="mb-3">
                    <label label for="detail" class="form-label"><strong>ความเสี่ยง/เหตุการณ์ในเวร</strong></label>
                    <textarea class="form-control" name ="risk" rows="4"></textarea>           
                </div>  
                <div class="mb-3">
                    <label label for="detail" class="form-label"><strong>การแก้ไขจัดการ</strong></label>
                    <textarea class="form-control" name ="correct" rows="4"></textarea>           
                </div>  
                <div class="mb-3">
                    <label label for="detail" class="form-label"><strong>นิเทศ/แนะนำในขณะตรวจเวร</strong></label>
                    <textarea class="form-control" name ="complain" rows="4"></textarea>           
                </div>    
                <div class="mb-3">
                    <label label for="detail" class="form-label"><strong>หมายเหตุ</strong></label>
                    <textarea class="form-control" name ="note" rows="4"></textarea>           
                </div>  
                <div class="mb-3">
                    <label class="form-label"><strong>พยาบาลเวรตรวจการ</strong></label>  
                    <select required class="form-select my-1" name="supervisor">                                                             
                        <option value="นางละออ บาระมี">นางละออ บาระมี</option>                        
                        <option value="นางนพพร แก้วกล้า">นางนรารัตน์ ทองแสง</option>                        
                        <option value="นางปุณยวีร์ อามาตย์มนตรี">นางปุณยวีร์ อามาตย์มนตรี</option> 
                        <option value="น.ส.จุไร บาระมี">น.ส.จุไร บาระมี</option>    
                        <option value="นางดลนภัส กลิ่นหวาน">นางดลนภัส กลิ่นหวาน</option>   
                        <option value="นางเพ็ญประภา ดวงจิตร์">นางเพ็ญประภา ดวงจิตร์</option>                                
                    </select>              
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