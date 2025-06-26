<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >Single Queue</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    
    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">
</head>

<body>  
<div class="card">
    <div class="card-header bg-primary text-white text-center"><h5><strong>โรงพยาบาลหัวตะพาน</strong></h5></div>
</div>
      
<div class="container">
    <div class="row justify-content-center">        
        <div class="card">
            <div class="card-body">
                <div class="pt-6 pb-2">
                    <h5 class="card-title text-center text-primary pb-1 fs-2">{{$ptname}}</h5>
                    <p class="text-center fs-6 ">วันที่รับบริการ {{DateThai($vstdate)}} <br>เวลา {{$vsttime}}</p>
                    <hr class="text-center">                    
                    <h5 class="card-title text-center text-danger pb-1 fs-1">{{$queue_slot_number}}</h5>
                    <h5 class="card-title text-center pb-1 fs-2">{{$department}}</h5>
                    <p class="text-center fs-6 ">ขณะนี้เวลา {{$cur_time}} รอแล้ว {{$wait}}</p>
                </div>             
            </div>
        </div>        
    </div>
</div>        
</body>
</html>