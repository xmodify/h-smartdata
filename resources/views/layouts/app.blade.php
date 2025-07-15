<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('/images/favicon.ico') }}" type="image/x-icon">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >SmartData</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- DataTables + Buttons + Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .dropdown-menu .dropend:hover > .dropdown-menu {
        display: block;
        top: 0;
        left: 100%;
        margin-top: -1px;
        }
    </style>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand btn btn-outline-info " href="{{ url('/') }}">
                     {{ config('app.name', 'H-SmartData') }} 
                </a>
                <!-- ปุ่มเมนูมือถือ -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- เมนูหลัก -->
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
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_opd') }}" >
                                    ผู้ป่วยนอก
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_ipd') }}" >
                                    ผู้ป่วยใน
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_er') }}" >
                                    อุบัติเหตุ-ฉุกเฉิน
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_drug') }}" >
                                    เภสัชกรรม
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_mental') }}" >
                                    สุขภาพจิต/ยาเสพติด
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_physic') }}" >
                                    กายภาพบำบัด
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_healthmed') }}">
                                    แพทย์แผนไทย
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_dent') }}" >
                                    ทันตกรรม
                                </a> 
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_ncd') }}" >
                                    คลินิกโรคเรื้อรัง
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_pcu') }}" >
                                    งานเชิงรุก
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_xray') }}" >
                                    รังสีวิทยา
                                </a> 
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_lab') }}" >
                                    เทคนิคการแพทย์
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_operation') }}" >
                                    ห้องผ่าผ่าตัด
                                </a>  
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_refer') }}" >
                                    ข้อมูลการส่งต่อ
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_death') }}" >
                                    ข้อมูลเสียชีวิต
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/service_diag') }}" >
                                    ข้อมูลเฉพาะโรค
                                </a>  
                            </div>                 
                        </li> 
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link btn btn-outline-info dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                ข้อมูลงานสนับสนุน
                            </a>
                            <div class=" btn btn-outline-primary dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item link-primary text-white " href="{{ url('/backoffice_asset') }}" >
                                    งานทรัพย์สิน
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/backoffice_hrd') }}" >
                                    บุคลากร
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/backoffice_plan') }}" >
                                    แผนยุทธศาสตร์
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/backoffice_risk') }}" >
                                    ความเสี่ยง
                                </a>
                                <a class="dropdown-item link-primary text-white " href="{{ url('/skpcard') }}" >
                                    บัตรสังฆประชาร่วมใจ
                                </a>   
                                <a class="dropdown-item link-primary text-white " href="{{ url('/form') }}" >
                                    ระบบตรวจสอบ/ประเมิน
                                </a>     
                            </div>                 
                        </li> 
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link btn btn-outline-info dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                งานเวชระเบียน
                            </a>
                            <ul class="bg-primary dropdown-menu dropdown-menu-end">   
                                <!-- เมนูอื่น -->
                                <li>
                                    <a class="link-primary dropdown-item text-white " href="{{ url('/hosxp_setting') }}">
                                       ข้อมูลพื้นฐาน HOSxP
                                    </a>
                                    <a class="link-primary dropdown-item text-white " href="{{ url('/medicalrecord_opd') }}" >
                                        เวชระเบียนผู้ป่วยนอก
                                    </a> 
                                </li>
                                <!-- ชี้ขวา -->
                                <li class="dropend">
                                    <a class="link-primary dropdown-item dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">
                                        เวชระเบียนผู้ป่วยใน
                                    </a>
                                    <ul class="bg-primary dropdown-menu">
                                        <li><a class="dropdown-item link-primary text-white" href="{{ url('medicalrecord_ipd/dchsummary') }}">D/C Summary</a></li> 
                                    </ul>
                                </li>
                            </ul>           
                        </li>
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link btn btn-info dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                ศูนย์จัดเก็บรายได้
                            </a>
                            <div class=" btn btn-outline-primary dropdown-menu dropdown-menu-end">    
                                <a class="dropdown-item link-primary text-white " href="{{ url('/finance_claim') }}" >
                                    เรียกเก็บค่ารักษาพยาบาล
                                </a>                                      
                                <a class="dropdown-item link-primary text-white " href="{{ url('/finance_debtor') }}" >
                                    ลูกหนี้ค่ารักษาพยาบาล
                                </a>            
                            </div>                 
                        </li>
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link btn btn-outline-info dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Dashboard
                            </a>
                            <div class=" btn btn-outline-primary dropdown-menu dropdown-menu-end">  
                                <a class="dropdown-item link-primary text-white " href="{{ url('/dashboard/opd_mornitor') }}" target="_blank">
                                    OPD Mornitor
                                </a>   
                                <a class="dropdown-item link-primary text-white " href="{{ url('/dashboard/ipd_mornitor') }}" target="_blank">
                                    IPD Mornitor
                                </a>   
                                <a class="dropdown-item link-primary text-white " href="{{ url('/dashboard/digitalhealth') }}" target="_blank">
                                    นโยบาย 30 บาท
                                </a> 
                            </div>                 
                        </li>

                    @endguest
                    </ul>
                    
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- H-RiMS -->
                        @auth                
                            @if (auth()->user()->hasAccessHrims('Y'))  
                                <li >                            
                                    <a class="navbar-brand btn btn-outline-info " href="{{ url('/hrims') }}">
                                        H-RiMS
                                    </a>       
                                </li>
                            @endif
                        @endauth
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
                                    <!-- Admin --> 
                                    @auth                
                                        @if (auth()->user()->hasAccessRole('admin'))
                                        <a class="dropdown-item text-white" href="{{ route('admin.main_setting') }}">Main Setting</a>                                                                                            
                                            <a class="dropdown-item text-white" href="{{ route('admin.user_access.index') }}">Manage User</a>
                                        @endif
                                    @endauth
                                    <!-- -->                                    
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- DataTables core -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Buttons + Export -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <!-- JSZip (required for Excel export) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- Stack for per-page script -->
    @stack('scripts')

</body>
</html>
