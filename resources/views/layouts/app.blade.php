<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/app.js') }}" defer></script>
    <script src="{{ asset('assets/js/jquery-3.2.1.min.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">

     <!-- Font Awesome 5 -->
     <link href="{{ asset('assets/font-awesome-5/all.css') }}" rel="stylesheet">

    <!-- Intl Dialing Code -->
    <link href="{{ asset('assets/intl-tel-input/css/intlTelInput.min.css') }}" rel="stylesheet" />
    <script type="text/javascript" src="{{ asset('assets/intl-tel-input/js/intlTelInput.js') }}"></script> 

    <!-- Datetimepicker -->
    <link href="{{ asset('/assets/datetimepicker/jquery.datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('/assets/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('/assets/datetimepicker/js/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script> 

    <!-- Image Uploader -->
    <link href="{{ asset('assets/image-uploader/dist/image-uploader.min.css') }}" rel="stylesheet" />
    <script type="text/javascript" src="{{ asset('assets/image-uploader/dist/image-uploader.min.js') }}"></script> 

</head>
<body>
    <!--Loading Bar-->
    <div class="div-loading">
      <div id="loader" style="display: none;"></div>  
    </div> 

    <div id="app">
        @php $x = false; if(Request::segment(1) == 'login' || Request::segment(1) == 'register') $x = true; @endphp
       
        @if($x == false)
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('account') }}">Accounts</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('contact') }}">Contact Us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('scan') }}">Connect WA</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
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
        @endif

        <main class="@if(Request::segment(1) == 'login') pt-4 bg-login @elseif(Request::segment(1) == 'register')pt-4 bg-register @else py-4 @endif">
            @if(Request::segment(1) == 'login' || Request::segment(1) == 'register')<div class="wave-white"></div>@endif
                @yield('content')
        </main>

        <!-- footer -->
        <div class="footer w-100 bg-white px-5 py-3 mt-2">
            <div class="container text-center">
                <div class="d-inline">
                    <a href="#">FAQ</a>
                    <a href="#">TERMS</a>
                </div>
                <div class="mt-2"><small>Copyright&copy; 2022 Giveaway</small></div>
            </div>
        </div>
    </div>

    @if(Request::segment(1) == 'scan' || Request::segment(1) == 'c')
        <script src="{{ asset('/assets/intl-tel-input/callback.js') }}" type="text/javascript"></script>
    @endif
    </body>
</html>
