@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center px-5">
        <div class="col-md-12 mb-3">
            <h2 class="big-theme" align="center">{{ Lang::get('auth.error404') }}</h2>
        </div>

        <!-- FORM -->
        <div class="col-md-9 text-center">
            <h5 class="alert alert-warning">{!! Lang::get('auth.error404.note') !!}</h5>
            <div class="text-center"><a class="btn btn-primary btn-sm" href="{{ url('pricing') }}">{{ Lang::get('auth.error404.back') }}</a></div>
        <!-- end col -->
        </div>
    </div>
</div>
@endsection