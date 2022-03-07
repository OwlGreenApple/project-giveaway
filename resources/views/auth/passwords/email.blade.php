@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-4">
            <div class="card px-2 py-2 fix-card">

                <div class="card-body">
                    <h1 class="text-center mb-4 title">{{ __('Reset Password') }}</h1>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('error_email'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error_email') }}
                        </div>
                    @endif

                    {{-- <form method="POST" action="{{ route('password.email') }}"> --}}
                    <form method="POST" action="{{ route('pass-reset') }}">
                        @csrf

                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border-end-0 title">@</span>
                            <input placeholder="{{ Lang::get('auth.email') }}" id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="mb-4 text-center">
                                <button type="submit" class="btn bg-custom btn-lg text-white">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
