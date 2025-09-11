<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('/images/hrims.ico') }}" type="image/x-icon">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >N-PluS</title>

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
        .text-hnplus {
        color: #03a9f4 !important;
        }
    </style>
    

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color:#23A7A7;">
            <div class="container-fluid">                
                <a class="navbar-brand btn btn-outline-info " href="{{ url('/hnplus') }}">
                    HN-PluS
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
                                ผลิตภาพทางการพยาบาล
                            </a>
                            <div class=" btn btn-outline-info dropdown-menu dropdown-menu-end">                                       
                                <a class="dropdown-item link-primary text-white " href="#" >
                                    แผนกผู้ป่วยนอก OPD
                                </a> 
                            </div>                 
                        </li>                       
                       
                    @endguest
                    </ul>
                    
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- H-RiMS -->
                        @auth                
                            @if (auth()->user()->hasAccessHnplus('Y'))                                
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
                                <a id="navbarDropdown" class="nav-link btn btn-outline-info dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="bg-info dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <!-- Admin --> 
                                    @auth                
                                        @if (auth()->user()->hasAccessRole('admin'))  
                                            <a class="dropdown-item text-white link-primary" href="#">HN-Plus Setting</a>                                            
                                        @endif
                                    @endauth
                                    <!-- -->                                    
                                    <a class="dropdown-item text-white link-primary" href="{{ route('logout') }}"
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
