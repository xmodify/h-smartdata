<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >{{ config('app.name', 'H-SmartData') }}</title>

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
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand btn btn-outline-info " href="{{ url('/') }}">
                     {{ config('app.name', 'H-SmartData') }} 
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->                                    
                    <ul class="navbar-nav me-auto">
                    @guest
                    @else 
                        
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link btn btn-outline-info dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                ข้อมูลงานบริการ
                            </a>
                            <div class=" btn btn-outline-primary dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item text-white " href="{{ url('/service_opd') }}" >
                                    - ผู้ป่วยนอก
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/service_ipd') }}" >
                                    - ผู้ป่วยใน
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/service_er') }}" >
                                    - อุบัติเหตุ-ฉุกเฉิน
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/service_drug') }}" >
                                    - เภสัชกรรม
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/service_mental') }}" >
                                    - สุขภาพจิต/ยาเสพติด
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/service_physic') }}" >
                                    - กายภาพบำบัด
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/service_healthmed') }}">
                                    - แพทย์แผนไทย
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/service_dent') }}" >
                                    - ทันตกรรม
                                </a> 
                                <a class="dropdown-item text-white " href="{{ url('/service_ncd') }}" >
                                    - คลินิกโรคเรื้อรัง
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/service_pcu') }}" >
                                    - งานเชิงรุก
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/service_xray') }}" >
                                    - รังสีวิทยา
                                </a> 
                                <a class="dropdown-item text-white " href="{{ url('/service_lab') }}" >
                                    - เทคนิคการแพทย์
                                </a> 
                                <a class="dropdown-item text-white " href="{{ url('/service_refer') }}" >
                                    - ข้อมูลการส่งต่อ
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/service_death') }}" >
                                    - ข้อมูลเสียชีวิต
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/service_diag') }}" >
                                    - ข้อมูลเฉพาะโรค
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/service_operation') }}" >
                                    - ข้อมูลผ่าตัด
                                </a>   
                            </div>                 
                        </li> 
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link btn btn-outline-info dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                ข้อมูลงานสนับสนุน
                            </a>
                            <div class=" btn btn-outline-primary dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item text-white " href="{{ url('/backoffice_asset') }}" >
                                    - งานทรัพย์สิน
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/backoffice_hrd') }}" >
                                    - บุคลากร
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/backoffice_plan') }}" >
                                    - แผนยุทธศาสตร์
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/backoffice_risk') }}" >
                                    - ความเสี่ยง
                                </a>
                                <a class="dropdown-item text-white " href="{{ url('/skpcard') }}" >
                                    - บัตรสังฆประชาร่วมใจ
                                </a>   
                                <a class="dropdown-item text-white " href="{{ url('/form') }}" >
                                    - ระบบตรวจสอบ/ประเมิน
                                </a>     
                            </div>                 
                        </li> 
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link btn btn-outline-info dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                งานเวชระเบียน
                            </a>
                            <div class=" btn btn-outline-primary dropdown-menu dropdown-menu-end">                                       
                                <a class="dropdown-item text-white " href="{{ url('/hosxp_setting') }}" >
                                    - ข้อมูลพื้นฐาน HOSxP
                                </a>    
                                <a class="dropdown-item text-white " href="{{ url('/medicalrecord_opd') }}" >
                                    - เวชระเบียนผู้ป่วยนอก
                                </a>   
                                <a class="dropdown-item text-white " href="{{ url('/medicalrecord_ipd') }}" >
                                    - เวชระเบียนผู้ป่วยใน
                                </a>                
                            </div>                 
                        </li> 
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link btn btn-outline-info dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                ศูนย์จัดเก็บรายได้
                            </a>
                            <div class=" btn btn-outline-primary dropdown-menu dropdown-menu-end">    
                                <a class="dropdown-item text-white " href="{{ url('/finance_claim') }}" >
                                    - เรียกเก็บค่ารักษาพยาบาล
                                </a>                                      
                                <a class="dropdown-item text-white " href="{{ url('/finance_debtor') }}" >
                                    - ลูกหนี้ค่ารักษาพยาบาล
                                </a>         
                                <a class="dropdown-item text-white " href="{{ url('/finance_stm') }}" >
                                    - นำเข้า Statement
                                </a>             
                            </div>                 
                        </li> 
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link btn btn-outline-info dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Dashboard
                            </a>
                            <div class=" btn btn-outline-primary dropdown-menu dropdown-menu-end">  
                                <a class="dropdown-item text-white " href="{{ url('/dashboard/opd_mornitor') }}" target="_blank">
                                    - OPD Mornitor
                                </a>   
                                <a class="dropdown-item text-white " href="{{ url('/dashboard/ipd_mornitor') }}" target="_blank">
                                    - IPD Mornitor
                                </a>   
                                <a class="dropdown-item text-white " href="{{ url('/dashboard/digitalhealth') }}" target="_blank">
                                    - นโยบาย 30 บาท
                                </a> 
                            </div>                 
                        </li> 
                        <!-- <div class="btn btn-outline-info text-white">
                                <a class="dropdown-item text-white " href="{{ url('/finance') }}">
                                    ศูนย์จัดเก็บรายได้
                                </a>
                            </div> -->
                            
                        <!-- dropdown munu -->

                        <!-- <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="btn btn-outline-info dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                ข้อมูลบริการ
                            </a>
                            <div class="btn btn-outline-info dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ url('/hos_opd') }}" >
                                    OPD
                                </a>
                                <a class="dropdown-item" href="{{ url('/hos_ipd') }}" >
                                    IPD
                                </a>
                                <a class="dropdown-item" href="{{ url('/hos_ipd') }}" >
                                    ER
                                </a>
                            </div>
                        </li> -->
                    @endguest
                    </ul>
                    
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            <!-- @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif -->
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link btn btn-outline-info dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="btn btn-outline-primary dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item text-white" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

</body>
</html>
