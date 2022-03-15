@if($destination == null)

    {{ Lang::get('email.greet') }} {{$name}},
	<br>
	<br>
	{{ Lang::get('email.welcome') }} {{ env('APP_NAME') }}
	<br>
	<strong>{{ Lang::get('email.pass') }} : </strong>: {{ $password }}
	<br>
	<br>
	<strong>{{ Lang::get('email.link') }} :</strong>
	<br>
	{{ url('login') }}
	<br>
	<br>
	{{ Lang::get('email.help.if') }}
	<br>
	<strong>{{ Lang::get('email.help.contact') }}
	<br>
	Telegram</strong>: @activomni_cs
	<br>
	<br>
	<br> {{ Lang::get('email.thank') }},
	<br>
	Team {{ env('APP_NAME') }}<br>
	<span style="font-style: italic;">*{{ env('APP_NAME') }} {{ Lang::get('email.part') }}</span>
	<br>
	<br>
	{{ Lang::get('email.help.ask') }}<br>
	{{ Lang::get('email.help.contact') }} : {{ Config::get('view.email_admin') }} <br>
	{{ Lang::get('email.help.reply') }}. <br>
	<br>
	{{ Lang::get('email.help.or') }} : {{ Config::get('view.phone_admin') }} <br>
	{{ Lang::get('email.hour') }}<br>

@else
    {{ Lang::get('email.greet') }} {{$name}},
	<br>
	<br>
	{{ Lang::get('email.reset') }} :
	<br>
	<strong>{{ $password }} </strong>
    <br>
	<br>
	{{ Lang::get('email.help.if') }}
	<br>
	<strong>{{ Lang::get('email.help.contact') }}
	<br>
	Telegram</strong>: @activomni_cs
	<br>
	<br>
	{{ Lang::get('email.thank') }},
	<br>
	Team {{ env('APP_NAME') }}
@endif


