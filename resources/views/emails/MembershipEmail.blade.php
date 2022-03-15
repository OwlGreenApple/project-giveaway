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
{{ Lang::get('email.transfer') }} : {{ Config::get('view.bank_name') }} <b>{{ Config::get('view.no_rek') }}</b> {{ Config::get('view.bank_owner') }}<br>
{{ Lang::get('email.after') }} :
<br>
{{ Lang::get('email.upload.step') }} : <a href="{{ url('account') }}/payment">{{ Lang::get('email.upload') }}</a>
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
{{ Lang::get('email.help.support') }} : {{ Config::get('view.email_admin') }} <br>
{{ Lang::get('email.help.reply') }} <br>
<br>
{{ Lang::get('email.help.or') }} : {{ Config::get('view.phone_admin') }} <br>
{{ Lang::get('email.hour') }}<br>
