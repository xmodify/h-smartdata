<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('/images/favicon.ico') }}" type="image/x-icon">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >RiMS</title>

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
        <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand btn btn-outline-info " href="{{ url('/hrims') }}">
                    H-RiMS
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
                            <a id="navbarDropdown" class="btn btn-outline-info dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                นำเข้าข้อมูล
                            </a>
                            <div class=" btn btn-outline-success dropdown-menu dropdown-menu-end">                                       
                                <a class="dropdown-item link-primary text-white " href="{{ url('hrims/import_stm') }}" >
                                    นำเข้า Statement
                                </a> 
                            </div>                 
                        </li> 
                       
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="btn btn-outline-info dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                เรียกเก็บ OP
                            </a>
                            <ul class="bg-success dropdown-menu dropdown-menu-end"> 
                                <!-- ชี้ขวา -->
                                <li class="dropend">
                                    <a class="link-primary dropdown-item dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">
                                        OP-UCS ประกันสุขภาพ
                                    </a>
                                    <ul class="bg-success dropdown-menu">
                                        <li>
                                            <a class="dropdown-item link-primary text-white" href="{{ url('hrims/claim_op/ucs_incup') }}"> UC-OP ใน CUP </a>
                                        </li> 
                                        <li>
                                            <a class="dropdown-item link-primary text-white" href="#"> UC-OP ในจังหวัด </a>
                                        </li> 
                                        <li>
                                            <a class="dropdown-item link-primary text-white" href="#"> UC-OP ต่างจังหวัด </a>
                                        </li> 
                                        <li>
                                            <a class="dropdown-item link-primary text-white" href="#"> UC-OP ฟอกไต </a>
                                        </li> 
                                    </ul>
                                </li>
                                <!-- เมนูอื่น -->
                                <li>
                                    <a class="dropdown-item link-primary text-white " href="#" > 
                                        OP-STP บุคคลที่มีปัญหาสถานะและสิทธิ 
                                    </a> 
                                    <a class="dropdown-item link-primary text-white " href="#" >
                                        OP-OFC กรมบัญชีกลาง
                                    </a>   
                                    <a class="dropdown-item link-primary text-white " href="#" >
                                        OP-LGO อปท.
                                    </a>       
                                    <a class="dropdown-item link-primary text-white " href="#" >
                                        OP-BKK อปท.รูปแบบพิเศษ กทม.
                                    </a>      
                                    <a class="dropdown-item link-primary text-white " href="#" >
                                        OP-BMT อปท.รูปแบบพิเศษ ขสมก.
                                    </a>
                                    <a class="dropdown-item link-primary text-white " href="#" >
                                        OP-SSS ประกันสังคม
                                    </a>    
                                    <a class="dropdown-item link-primary text-white " href="#" >
                                        OP-ชำระเงิน
                                    </a>   
                                    <a class="dropdown-item link-primary text-white " href="#" >
                                        OP-พรบ.
                                    </a>   
                                </li>
                            </ul> 
                        </li> 
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="btn btn-outline-info dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                เรียกเก็บ IP
                            </a>
                            <div class=" btn btn-outline-success dropdown-menu dropdown-menu-end">                                       
                                <a class="dropdown-item link-primary text-white " href="#" >
                                    IP-UCS ประกันสุขภาพ
                                </a> 
                                <a class="dropdown-item link-primary text-white " href="#" >
                                    IP-STP บุคคลที่มีปัญหาสถานะและสิทธิ 
                                </a> 
                                <a class="dropdown-item link-primary text-white " href="#" >
                                    IP-OFC กรมบัญชีกลาง
                                </a>   
                                <a class="dropdown-item link-primary text-white " href="#" >
                                    IP-LGO อปท.
                                </a>       
                                <a class="dropdown-item link-primary text-white " href="#" >
                                    IP-BKK อปท.รูปแบบพิเศษ กทม.
                                </a>      
                                <a class="dropdown-item link-primary text-white " href="#" >
                                    IP-BMT อปท.รูปแบบพิเศษ ขสมก.
                                </a>
                                <a class="dropdown-item link-primary text-white " href="#" >
                                    IP-SSS ประกันสังคม
                                </a>    
                                <a class="dropdown-item link-primary text-white " href="#" >
                                    IP-ชำระเงิน
                                </a>   
                                <a class="dropdown-item link-primary text-white " href="#" >
                                    IP-พรบ.
                                </a>   
                            </div> 
                        </li>  
                        <li >                            
                            <a class="btn btn-outline-info text-white" href="#">
                                ลูกหนี้ค่ารักษาพยาบาล
                            </a>       
                        </li>     
                    @endguest
                    </ul>
                    
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- H-RiMS -->
                        @auth                
                            @if (auth()->user()->hasAccessHrims('Y'))  
                                <li >                            
                                    <a class="navbar-brand btn btn-outline-info " href="{{ url('/') }}">
                                        H-SmartData
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
                                <a id="navbarDropdown" class="nav-link btn btn-outline-success dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="bg-success dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <!-- Admin --> 
                                    @auth                
                                        @if (auth()->user()->hasAccessRole('admin'))  
                                            <a class="dropdown-item text-white" href="{{ route('admin.lookup_icode.index') }}">Lookup icode</a>
                                            <a class="dropdown-item text-white" href="{{ route('admin.lookup_ward.index') }}">Lookup ward</a>
                                            <a class="dropdown-item text-white" href="{{ route('admin.lookup_hospcode.index') }}">Lookup hospcode</a>
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
