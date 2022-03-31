@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-4">
            <div class="position-relative gift-pos">
                <div class="gift-strap"><!-- --></div>
            </div>
            <div class="card logo px-2 py-2"><img class="logo" src="{{ asset('assets/img/logo-topleads.png') }}"/></div>
            <div class="card px-2 py-2 fix-card">
                <div class="card-body">
                    <h1 class="text-center mb-4 title">{{ Lang::get('auth.register') }}</h1>

                    <form id="form-register">
                        @csrf
                        <div class="mb-3">
                            <input placeholder="{{ Lang::get('auth.name') }}" id="name" type="text" class="form-control form-control-lg" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
                            <span class="text-danger username"></span>
                        </div>

                        <div class="mb-3">
                            <input placeholder="{{ Lang::get('auth.email') }}" id="email" type="email" class="form-control form-control-lg" name="email" value="{{ old('email') }}" required autocomplete="email">
                            <span class="text-danger email"></span>
                        </div>

                        <div class="mb-4">
                            <div class="form-inline">
                              <span class="text-muted">
                                <input type="checkbox" name="agreement" required id="check-terms" class="form-check-input me-2" /><small>{{ Lang::get('custom.agreement') }}
                                <a class="main-color" target="_blank" rel="noopener noreferrer" href="">{{ Lang::get('custom.agreement.privacy') }}</a></small>
                              </span>
                            </div>
                            <small class="text-danger tos"></small>
                        </div>

                        <div class="mb-4 text-center">
                            <button id="btn-register" type="button" class="btn bg-custom btn-lg btn-account text-white">
                            {{ Lang::get('auth.register') }}
                            </button>
                        </div>
                    </form>
                    <hr>
                    <div class="login-foot text-center">
                        <span class="text-secondary">{{ Lang::get('auth.log') }}<a href="{{ route('login') }}" class="main-color">{{ Lang::get('auth.login') }}</a></span>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

var bool = false;
$(function(){
    registerAjax();
});

  function registerAjax()
  {
    $("#btn-register").click(function(){
      var check = $("input[name='agreement']").is(":checked");

      if(check == false)
      {
        $(".tos").html('{{ Lang::get("custom.check") }}');
        return false;
      }

      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: "{{url('register')}}",
        data: $("#form-register").serializeArray(),
        dataType: 'json',
        beforeSend: function()
        {
            $(".tos").html('');
            $('#loader').show();
            $('.div-loading').addClass('background-load');
        },
        success: function(data)
        {
            if (data.success == 1)
            {
                bool = true;
                (function(w,n) {
                if (typeof(w[n]) == 'undefined'){ob=n+'Obj';w[ob]=[];w[n]=function(){w[ob].push(arguments);};
                d=document.createElement('script');d.type = 'text/javascript';d.async=1;
                d.src='https://s3.amazonaws.com/provely-public/w/provely-2.0.js';x=document.getElementsByTagName('script')[0];x.parentNode.insertBefore(d,x);}
                })(window, 'provelys', '');
                provelys('config', 'baseUrl', 'app.provely.io');
                provelys('config', 'https', 1);
                provelys('data', 'campaignId', '31367');
                provelys('config', 'track', 1);
            }
            else
            {
                 $('#loader').hide();
                 $('.div-loading').removeClass('background-load');
                 $(".error").show();
                 $(".username").html(data.username);
                 $(".email").html(data.email);
                //  $(".code_country").html(data.code_country);
                //  $(".phone").html(data.phone);
            }
        },
        complete : function(xhr)
        {
           if(bool == true)
           {
             location.href="{{ url('home') }}";
           }
        },
        error: function(xhr,attr,throwable)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          console.log(xhr.responseText);
        }
      });
    /**/
    });
 }
 </script>
@endsection
