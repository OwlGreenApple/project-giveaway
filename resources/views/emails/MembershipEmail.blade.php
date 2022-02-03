{{ Lang::get('email.hi') }} {{$name}},
<br>
<br>
{{ Lang::get('email.thanks') }},
<br>
{{ Lang::get('email.detail') }} : 
<br>
<br>
{{ Lang::get('email.no') }} : <b>{{$no}}</b>
<br>
{{ Lang::get('email.package') }} : {{$package}}
<br>
{{ Lang::get('email.price') }} : Rp.{{ $ct::format($price) }}
<br>
Total : Rp.<b>{{ $ct::format($total) }}</b>
<br>
<br/>
{{ Lang::get('email.transfer') }} : {{ env('BANK_NAME') }} <b>{{ env('NO_REK') }}</b> Sugiarto Lasjim<br>
{{ Lang::get('email.after') }} :
<br>
{{ Lang::get('email.upload.step') }} : <a href="{{ url('account') }}/1">{{ Lang::get('email.upload') }}</a>
<br>
<br>
{{ Lang::get('email.help.if') }}<br>
<strong>{{ Lang::get('email.help.contact') }}<br>
Telegram</strong>: @activomni_cs<br>
<br>
<br> {{ Lang::get('email.thank') }}, 
<br>
Team {{ env('APP_NAME') }}<br>
<span style="font-style: italic;">*{{ env('APP_NAME') }} {{ Lang::get('email.part') }}</span>
<br>
<br>
{{ Lang::get('email.help.ask') }},<br>
{{ Lang::get('email.help.support') }} : {{ env('ADMIN_EMAIL') }} <br>
{{ Lang::get('email.help.reply') }} <br>
<br>
{{ Lang::get('email.help.or') }} : {{ env('ADMIN_PHONE') }} <br>
{{ Lang::get('email.hour') }}<br>
