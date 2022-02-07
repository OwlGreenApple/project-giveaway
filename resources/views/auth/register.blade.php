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
                    <h1 class="text-center mb-4 title">{{ __('Register') }}</h1>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="input-group mb-3">
                            <input placeholder="Your Name" id="name" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="input-group mb-3">
                            <input placeholder="Your Email" id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="input-group mb-3">
                            <input placeholder="Password" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="input-group mb-3">
                            <input placeholder="Confirm Password" id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        </div>

                        <div class="mb-4 text-center">
                            <button type="submit" class="btn bg-custom btn-lg btn-account text-white">
                                {{ __('Register') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
