@extends('layouts.app')

@section('content')
<link href="{{ asset('assets/css/thankyou.css') }}" rel="stylesheet">
  <div class="container konten">
    <div class="offset-sm-2 col-sm-8">
      <div class="card h-80 card-payment" style="margin-bottom: 50px">
        <div class="card-body">
          @if(isset($ct) && $ct == 1)
            <span class="icon-thankyou" style="font-size: 60px;color: #106BC8">
              <i class="fas fa-check-circle"></i>
            </span>
            <h1>{{ Lang::get('custom.thx') }}</h1>
            <p>{{ Lang::get('auth.conf') }}</p>
          @else
            <span class="icon-thankyou" style="font-size: 60px;color: #106BC8">
              <i class="fas fa-check-circle"></i>
            </span>
            <h1>{{ Lang::get('custom.thx') }}<br> {{ Lang::get('custom.conf') }}</h1>
            <hr class="orn" style="color: #106BC8">
            <h4>{{ Lang::get('custom.conf.notify') }}</h4>
            <a class="btn btn-success" href="{{ url('contest') }}">{{ Lang::get('custom.conf.btn') }}</a>
          @endif
        </div>
      </div>  
    </div>
  </div>
@endsection