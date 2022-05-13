@extends('layouts.app')

@section('content')
<div class="container">
    <div class="col-md-9 px-0 wrapper">
            <h1 class="congrats mt-3">{{ Lang::get('giveaway.promo.congrats') }}</h1>
            <h3 class="congrats text-center mb-4">{!! Lang::get('giveaway.promo.share') !!}</h3>

            <div id="taskdata" class="col-lg-9 mx-auto mb-5">
                <ul class="list-group">
                    <li class="list-group-item clearfix"><span class="circle-tw">{!! $helper::get_marks(0,$ev->id,'tw',0,true,1) !!}</span>
                        <a href="https://twitter.com/share?url={{ $share }}&hashtags=winner,giveaway" target="_blank" rel="noopener noreferrer" class="task" data-type="tw">
                            <i class="fab fa-twitter"></i>&nbsp;{{ Lang::get('custom.tw.share') }}
                        </a>
                    </li>
                    <li class="list-group-item clearfix"><span class="circle-fb">{!! $helper::get_marks(0,$ev->id,'fb',0,true,1) !!}</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{$share}}" target="_blank" rel="noopener noreferrer" class="task" data-type="fb">
                            <i class="fab fa-facebook-f"></i>&nbsp;{{ Lang::get('custom.fb.share') }}
                        </a>
                    </li>
                    <li class="list-group-item clearfix"><span class="circle-mail">{!! $helper::get_marks(0,$ev->id,'mail',0,true,1) !!}</span>
                        <a href="mailto:?subject={{ $ev->title }}&amp;body={{ $share }}" target="_blank" rel="noopener noreferrer" class="task" data-type="mail">
                            <i class="fas fa-envelope"></i>&nbsp;{{ Lang::get('custom.email.share') }}
                        </a>
                    </li>
                    <li class="list-group-item clearfix"><span class="circle-wa">{!! $helper::get_marks(0,$ev->id,'wa',0,true,1) !!}</span>
                        <a href="https://api.whatsapp.com/send?text={{ $share }}" target="_blank" rel="noopener noreferrer" class="task" data-type="wa">
                            <i class="fab fa-whatsapp"></i>&nbsp;{{ Lang::get('custom.wa.share') }}
                        </a>
                    </li>
                    <li class="list-group-item clearfix"><span class="circle-tg">{!! $helper::get_marks(0,$ev->id,'tg',0,true,1) !!}</span>
                        <a href="https://t.me/share/url?url={{ $share }}" target="_blank" rel="noopener noreferrer" class="task" data-type="tg">
                            <i class="fab fa-telegram"></i>&nbsp;{{ Lang::get('custom.tg.share') }}
                        </a>
                    </li>
                    <li class="list-group-item clearfix"><span class="circle-copy">{!! $helper::get_marks(0,$ev->id,'copy',0,true,1) !!}</span>
                        <a class="task btn-copy" data-link="{!! $share !!}" data-type="copy">
                            <i class="fas fa-link"></i></i>&nbsp;{{ Lang::get('custom.copy.link') }}
                        </a>
                    </li>
                    <li class="list-group-item clearfix"><span class="circle-wd">{!! $helper::get_marks(0,$ev->id,'wd',0,true,1) !!}</span>
                        <a class="task btn-copy" data-link="{!! $widget !!}" data-type="wd">
                            <i class="fas fa-code"></i></i>&nbsp;{{ Lang::get('custom.wd') }}
                        </a>
                    </li>
                </ul>
            </div>
        <!-- end col -->
    </div>

    <div class="mt-3 text-center">
        <div class="mb-2"><a class="main-color" target="_blank" rel="noopener noreferer" href="{{ url('c') }}/{{ $ev->url_link }}">{{ Lang::get('giveaway.promo.page') }}</a></div>
        <div class="mb-2"><a class="main-color" target="_blank" rel="noopener noreferer" href="{{ url('edit-event') }}/{{ $ev->id }}">{{ Lang::get('giveaway.promo.edit') }}</a></div>
        <div><a class="main-color" target="_blank" rel="noopener noreferer" href="{{ url('home') }}">{{ Lang::get('giveaway.manage') }}</a></div>
    </div>

</div>

<!-- copy modal for type 13  -->
<div class="modal" id="copy_link">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal body -->
      <div class="modal-body">
        {!! Lang::get('giveaway.promo') !!}
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">{{ Lang::get('giveaway.close') }}</button>
      </div>

    </div>
  </div>
</div>

<script type="text/javascript">
    $(function(){
        share();
        copyLink();
    });

    function share()
    {
        $("body").on("click",".task",function(){
            var data_type = $(this).attr('data-type');
            var data = {"evid": "{{ $ev->id }}","type" : data_type};
            sharing_run(data);
        });
    }

    function copyLink(){
      $( "body" ).on("click",".btn-copy",function(e)
      {
        e.preventDefault();
        e.stopPropagation();

        var link = $(this).attr("data-link");

        var tempInput = document.createElement("input");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = link;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
      });
    }

    function sharing_run(data)
    {
        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method : 'POST',
            data : data,
            url : '{{ url("save-promo") }}',
            dataType : 'json',
            beforeSend : function()
            {
                $('#loader').show();
                $('.div-loading').addClass('background-load');
            },
            success : function(result)
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');

                if(result.success == 'copy' || result.success == 'wd')
                {
                    $("#copy_link").modal('show');
                }
                $(".circle-"+result.type).html('<i class="fas fa-check-circle main-color"></i>');
            },
            error : function(xhr)
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    }
</script>

@endsection
