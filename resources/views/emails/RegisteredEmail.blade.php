@if($destination == null)
	{{ Lang::get('email.subject.registered') }}
	<br>
	<br>
    {{ Lang::get('email.greet') }} {{$name}},
	<br>
	<br>
	{{ Lang::get('email.welcome') }} {{ env('APP_NAME') }}
	<br>
	<strong>{{ Lang::get('email.pass') }} : </strong>{{ $password }}
	<br>
	<br>
	<strong>{{ Lang::get('email.link') }} :</strong>
	<br>
	<a href="{{ url('login') }}">{{ url('login') }}</a>
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
	<br>
	<span style="font-style: italic;">*{{ env('APP_NAME') }} {{ Lang::get('email.part') }}</span>
	<br>
	<br>
	{{ Lang::get('email.help.ask') }}<br>
	{{ Lang::get('email.help.contact') }} : <a href="mailto:{{ Config::get('view.email_admin'); }}">{{ Config::get('view.email_admin') }}</a>
	<br>
	<br>
	{{ Lang::get('email.help.reply') }}.<br>
	{{ Lang::get('email.hour') }}
	<br>
	<br>
	{{ Lang::get('email.close') }}
	<br>
	{{ Lang::get('email.team') }} {{ env('APP_NAME') }}

@else
	<!-- password reset -->
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
	<br>
	{{ Lang::get('email.close') }}
	<br>
	Team {{ env('APP_NAME') }}
@endif


