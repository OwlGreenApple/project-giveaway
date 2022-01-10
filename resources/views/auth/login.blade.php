@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-4">
            <div class="position-relative gift-pos">
                <div class="gift-strap"><!-- --></div>
            </div>
            <div class="card logo px-2 py-4">LOGO</div>
            <div class="card px-2 py-2 fix-card">
                <div class="card-body">
                    <h1 class="text-center mb-4 title">Login</h1>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border-end-0 title">@</span>
                            <input placeholder="Your Email" id="email" type="email" class="form-control form-control-lg bg-white border-start-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus/>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-key title"></i></span>
                            <input placeholder="Password" id="password" type="password" class="form-control form-control-lg bg-white border-start-0 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" />
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-check mb-3 text-left">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>

                        <div class="mb-4 text-center">
                            <button type="submit" class="btn bg-custom btn-lg text-white">
                                {{ __('Login') }}
                            </button>
                        </div>
                    </form>
                    <hr>

                    <div class="clearfix login-foot">
                        <div class="float-start"><a href="{{ route('password.request') }}" class="main-color">Forgot Password</a></div>
                        <div class="float-end"><a class="main-color">Register</a></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
