{{ Lang::get('email.greet') }},
<br>
{{ Lang::get('email.accept') }}
<br>
{{ Lang::get('email.detail') }} :
<br>
<br>
{{ Lang::get('email.no') }} : <b>{{ $order->no_order }}</b>
<br>
{{ Lang::get('email.name') }}  : <b>{{$case}}</b>
<br>
{{ Lang::get('email.status') }} : <b>{{ Lang::get('email.confirm') }}</b>
<br>
{{ Lang::get('email.package') }} : <b>{{ $order->package }}</b> <b>{{ Lang::get('email.currency') }}.{{ $pc::format($order->total_price) }}</b>
<br>
<br>
{{ Lang::get('email.activated') }} {{ env('APP_NAME') }}.
<br />
{{ Lang::get('email.help.ask') }} {{ Lang::get('email.help.support') }} : {{ Config::get('view.email_admin'); }}</a>
<br>
<br>
{{ Lang::get('email.regards') }}
<br>
Team {{ env('APP_NAME') }}
