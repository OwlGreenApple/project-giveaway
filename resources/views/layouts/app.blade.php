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
    <link href="{{ asset('assets/css/order.css') }}" rel="stylesheet">

     <!-- Font Awesome 5 -->
     <link href="{{ asset('assets/font-awesome-5/all.css') }}" rel="stylesheet">

    <!-- Intl Dialing Code -->
    <link href="{{ asset('assets/intl-tel-input/css/intlTelInput.min.css') }}" rel="stylesheet" />
    <script type="text/javascript" src="{{ asset('assets/intl-tel-input/js/intlTelInput.js') }}"></script>

    <!-- Datetimepicker -->
    <link href="{{ asset('/assets/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('/assets/datetimepicker/js/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>

    <!-- Image Uploader -->
    <link href="{{ asset('assets/image-uploader/dist/image-uploader.min.css') }}" rel="stylesheet" />
    <script type="text/javascript" src="{{ asset('assets/image-uploader/dist/image-uploader.min.js') }}"></script>

    <!-- Data Table -->
    <link href="{{ asset('assets/DataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/DataTables/Responsive/css/responsive.dataTables.min.css') }}" rel="stylesheet">

    <script defer type="text/javascript" src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
    <script defer type="text/javascript" src="{{ asset('assets/DataTables/Responsive/js/dataTables.responsive.min.js') }}"></script>

    <!-- Emoji -->
    <link href="{{ asset('/assets/emoji/css/emojionearea.min.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('/assets/emoji/js/prettify.js') }}"></script>
    @if (env('APP_ENV')=='local')
    <script type="text/javascript" src="{{ asset('/assets/emoji/js/emojionearea.js') }}"></script>
    @else
    <script type="text/javascript" src="{{ asset('/assets/emoji/js/emojionearea-production.js') }}"></script>
    @endif

</head>
<body>
    <!--Loading Bar-->
    <div class="div-loading">
      <div id="loader" style="display: none;"></div>
    </div>

    <div id="app">
        @php
            $x = false;
            if(Request::segment(1) == 'login' || Request::segment(1) == 'register' || Request::segment(1) == 'c'|| Request::segment(1) == 'contest' || Request::segment(1) == 'summary' || Request::segment(1) == 'checkout' || Request::segment(1) == 'confirmation')
            {
                $x = true;
            }
        @endphp

        @if($x == false)
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img class="logoheader" src="{{ asset('assets/img/logo.png') }}"/>
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
                                    <a class="nav-link" href="{{ route('login') }}">{{ Lang::get('auth.login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ Lang::get('auth.register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">{{ Lang::get('auth.dashboard') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('account') }}">{{ Lang::get('auth.account.layout') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('contact') }}">{{ Lang::get('auth.contact') }}</a>
                            </li>
                            @php $disable = 1 @endphp <!-- +++ temp +++ -->
                            @if($disable == 0)<!-- +++ temp +++ -->
                            <li class="nav-item"> 
                                <a class="nav-link" href="{{ url('scan') }}">{{ Lang::get('auth.wa') }}</a>
                            </li> 
                            <li class="nav-item dropdown">
                                <a id="bcDropdown" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>{{ Lang::get('auth.bc') }}</a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="bcDropdown">
                                    <a class="dropdown-item" href="{{ url('create-broadcast') }}">{{ Lang::get('auth.bc.create') }}</a>
                                    <a class="dropdown-item" href="{{ url('broadcast') }}">{{ Lang::get('auth.bc.list') }}</a>
                                </div>
                            </li>
                            @endif<!-- +++ temp +++-->
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    @if(Auth::user()->is_admin == 1)
                                        <a class="dropdown-item" href="{{ url('list-user') }}">{{ Lang::get('auth.list.user') }}</a>
                                        <a class="dropdown-item" href="{{ url('list-order') }}">{{ Lang::get('auth.list.order') }}</a>
                                        <a class="dropdown-item" href="{{ url('affiliate-admin') }}">{{ Lang::get('auth.list.redeem') }}</a>
                                    @else
                                        <a class="dropdown-item" href="{{ url('affiliate') }}">
                                            {{ Lang::get('auth.affiliate') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ url('redeem-money') }}">
                                            {{ Lang::get('title.redeem') }} 
                                        </a>
                                        <a class="dropdown-item" href="{{ url('packages') }}">
                                            {{ Lang::get('auth.package') }}
                                        </a>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ Lang::get('auth.logout') }}
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
                    <a href="{{ url('privacy') }}">{{ Lang::get('auth.terms') }}</a>
                </div>
                <div class="mt-2"><small>Copyright&copy; 2022 {{env('APP_NAME')}}.app</small></div>
            </div>
        </div>
    </div>

    </body>
</html>
