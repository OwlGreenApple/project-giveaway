@extends('layouts.app')

@section('content')
<link href="{{ asset('assets/css/thankyou.css') }}" rel="stylesheet">
  <div class="container konten">
    <div class="offset-sm-2 col-sm-8">
      <div class="card h-80 card-payment" style="margin-bottom: 50px">
        <div class="card-body">
          <span class="icon-thankyou" style="font-size: 60px;color: #106BC8">
            <i class="fas fa-check-circle"></i>
          </span>
          <h1>{{ Lang::get('custom.thx') }}<br> {{ Lang::get('custom.conf') }}</h1>
          <hr class="orn" style="color: #106BC8">
        </div>
      </div>  
    </div>
@endsection