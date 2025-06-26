<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >เจ้าหน้าที่ที่ยังไม่คัดกรองสุขภาพ</title>

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
<!--row--> 
<div class="container-fluid"> 
    <div class="card">   
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>รายชื่อเจ้าหน้าที่ที่ยังไม่คัดกรองสุขภาพ ระหว่างวันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>    
            <table class="table table-bordered table-striped">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th> 
                        <th class="text-center">หน่วยงาน</th>  
                        <th class="text-center">ชื่อ-สกุล</th>
                        <th class="text-center">เพศ</th>  
                        <th class="text-center">อายุ</th> 
                        <th class="text-center">เบอร์โทร.</th>   
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($health_notscreen as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                        <td align="left">{{ $row->hrd_name }} </td>
                        <td align="center">{{ $row->SEX }} </td>
                        <td align="center">{{ $row->AGE }} </td>   
                        <td align="center">{{ $row->HR_PHONE }}</td>
                    </tr>
                <?php $count++; ?>
                @endforeach
            </table>        
    </div>
</div>
</body>
</html>