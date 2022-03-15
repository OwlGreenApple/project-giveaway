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
          <h1>{{ Lang::get('order.thank') }} {{ Lang::get('order.thank.order') }}</h1>
          <hr class="orn" style="color: #106BC8">
          <p class="card-text">
            {{ Lang::get('order.pay') }} :
          </p>
          <h2>{!! Config::get('view.no_rek') !!}</h2>
          <p class="card-text">
            {!! Config::get('view.bank_name') !!} <b>{!! Config::get('view.bank_owner') !!}</b>
          </p>
          <p class="card-text">
            {{ Lang::get('order.pay.proof') }} <b>{{ Config::get('view.email_admin') }}</b> <br>
            {{ Lang::get('order.admin') }}
          </p>
          <p class="card-text">
            <a class="btn btn-success btn-confirm-thankyou" href="{{url('account')}}/payment">
                {{ Lang::get('order.confirm.order') }}
             </a>
          </p>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-4">
        <div class="card h-80">
          <div class="card-body">
            <span style="font-size: 48px; color: Dodgerblue;"><i class="fas fa-envelope-open-text"></i></span>
            <h5 class="card-title">1. {{ Lang::get('order.email') }}</h5>
            <p class="card-text">{{ Lang::get('order.choosing') }} {{ env('APP_NAME') }}. {{ Lang::get('order.email.check') }}</p>
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card h-80">
          <div class="card-body">
            <span style="font-size: 48px; color: Dodgerblue;"><i class="fas fa-search"></i></span>
            <h5 class="card-title">2. {{ Lang::get('order.email.find') }}</h5>
            <p class="card-text">{{ Lang::get('order.email.find.conf') }}</p>
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card h-80">
          <div class="card-body">
            <span style="font-size: 48px; color: Dodgerblue;"><i class="far fa-credit-card"></i></span>
            <h5 class="card-title">3. {{ Lang::get('order.payment') }}</h5>
            <p class="card-text">{{ Lang::get('order.payment.link') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- <div class="container konten">
    <div class="offset-sm-2 col-sm-8">
      <div class="card h-80 card-payment" style="margin-bottom: 50px
      ">
        <div class="card-body">
          <span class="icon-thankyou" style="font-size: 60px;color: #106BC8">
            <i class="fas fa-check-circle"></i>
          </span>
          <h1>Terima Kasih<br> Atas Order Anda</h1>
          <hr class="orn" style="color: #106BC8">
        </div>
      </div>
    </div>
  </div> -->

@endsection
