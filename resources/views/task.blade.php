@extends('layouts.app')

@section('content')
<div class="confetti d-none"></div>

<div class="container px-0">
    <div class="col-md-9 pt-0 pb-3 wrapper">
        <!-- youtube or banner carousel -->

        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @if($ev->media == 0)
                    @if(count($banners) > 0)
                        <div class="carousel-item active">
                            <img src="{{ $banners['url'] }}" class="d-block w-100" />
                        </div>
                    @endif
                @else
                    <div class="carousel-item active">
                        <div class="yt-box">
                            <iframe class="yt-iframe" src="{{ $ev->youtube_banner }}" ></iframe>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- end carousel -->
        
        <h1 class="congrats"><b>{{ Lang::get('custom.congrats') }}</b> {{ Lang::get('custom.in') }}</h1>
        <h2 class="congrats"><b>{{ Lang::get('custom.get') }}</b> {{ Lang::get('custom.by') }} :</h2>
        <h4 class="text-center text-uppercase px-3">
            <div class="alert bg-rank">
                {{ Lang::get('custom.prize') }} : <b class="main-color">{{ $ev->currency }}&nbsp;{{ $helpers::format($ev->prize_value) }}</b>
            </div>
        </h4>

        <div class="col-lg-9 mx-auto">
            <div id="taskdata"><!-- display task here --></div>
        <!-- end col -->
        </div>
        <!-- end col -->

        <!-- footer -->
        <div class="footer-task">

            <div class="bg-task">
                <div class="desc px-0">{{ Lang::get('custom.giveaway_timezone') }} : {{ $ev->timezone }}</div>
                <div class="desc px-0 ms-auto">{{ Lang::get('custom.offered') }} : <a href="{{ $ev->owner_url }}" class="main-color">{{ $ev->owner }}</a></div>
            </div>

            {{-- rank --}}
            <div id="rank-wrapper" class="row mx-0 mt-2">
                <div class="col-12 col-lg-12 px-1">
                    <li class="list-group-item text-center bg-rank"><b>{{ Lang::get('custom.rank') }}</b></li>
                    <ul id="rank" class="overflow-auto list-group list-group-flush rounded border border-gray-300 shadow">
                        <span id="preload"><!-- {{-- preload --}} --></span>
                        <li class="list-group-item text-center mb-2"><a role="button" id="load-rank" class="btn btn-dark btn-sm d-none">{{ Lang::get('custom.load') }}</a></li>
                    </ul>
                </div>
            </div>

            @if($user->membership == 'free' || $user->membership == 'starter' || $user->membership == 'starter-3-month' || $user->membership == 'starter-yearly')
                <div class="text-center mt-2">{!! $helpers::sponsor(1) !!}</div>
            @endif
        </div>

    <!-- end warpper -->
    </div>
    <!-- end container -->
</div>

<!-- fixed timer -->
<div class="col-lg-12 text-center bg-white task_entries">
    <div class="container row mx-auto">
        <div class="col-lg-6 clearfix mx-auto point-left">
            <div class="point-entry">
                <div class="me-1 text-uppercase">
                    {{ Lang::get('giveaway.point') }} : <span class="main-color">{{$ct->entries}}</span>
                </div>
            </div>
        </div>

        <div class="col-lg-6 point-entry time point-right">
            <div class="text-uppercase">Time Left :</div>
            <!-- timer -->
            <div id="countdown" class="text-center">
                <ul>
                    <li><div class="count" id="days"></div></li>
                    <li class="count-space"><span class="count"><span class="me-1">d</span>:</span></li>
                    <li><div class="count" id="hours"></div></li>
                    <li class="count-space"><span class="count"><span class="me-1">h</span>:</span></li>
                    <li><div class="count" id="minutes"></div></li>
                    <li class="count-space"><span class="count"><span class="me-1">m</span>:</span></li>
                    <li><div class="count" id="seconds"></div></li>
                    <li class="count-space"><span class="count"><span class="me-1">s</span></span></li>
                </ul>
            </div>
            <!-- end timer -->
        </div>
        <!-- end container -->
    </div>
</div>


<!-- copy modal for type 13  -->
<div class="modal" id="copy_link">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal body -->
      <div class="modal-body">
        {!! Lang::get('giveaway.copy') !!}
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">{{ Lang::get('giveaway.close') }}</button>
      </div>

    </div>
  </div>
</div>

<script>
    var global_date = "{{ $timer }}"; //set target event date

    $(function(){
        task();
        loadtask();
        hover_plus();
        copyLink(); //type = 13
        confetti();
        loadRank();
        rank(0);
    });

    function loadRank()
    {
       $("#load-rank").click(function()
       {
            var limit = $(this).attr('limit');
            rank(limit)
       });
    }

    function rank(limit)
    {
        var elm = '';

        $.ajax({
            headers : {'X-CSRF-TOKEN' : $("meta[name='csrf-token']").attr('content')},
            method : 'POST',
            data : {'ev_id' : '{{ $ev->id }}','limit':limit},
            url : '{{ url("get-rank") }}',
            dataType : 'json',
            success : function(res){
                $.each(res,function(i,val)
                 {
                    elm+='<li class="list-group-item d-flex justify-content-between align-items-center border-0">';
                    elm+='<span>'+i+'</span>';
                    elm+='<span class="text-secondary">';
                    elm+='<div>'+val.c_name+'</div>';
                    elm+='<div>'+val.wa_number+'</div>';
                    elm+='</span>';
                    elm+='<span class="badge bg-success badge-pill">'+val.entries+'</span>';
                    elm+='</li>';
                });

                // remove button if contestant loaded well
                if(res.length < 1)
                {
                    $("#load-rank").remove();
                }
            },
            error : function(xhr)
            {
                console.log(xhr.responseText);
            },
            complete : function()
            {
                $("#preload").append(elm);
                $("#load-rank").removeClass('d-none');

                var next = parseInt('{{ $helpers::rank_display() }}');
                if(limit === 0)
                {
                    $("#load-rank").attr('limit',next);
                }
                else
                {
                    var lmn = $("#load-rank").attr('limit');
                    lmn = parseInt(lmn);
                    lmn = lmn+next;
                    $("#load-rank").attr('limit',lmn);
                }
            }
        })
    }

     function confetti()
    {
        $(document).ready(function(){
            setTimeout(function(){
                $(".confetti").removeClass('d-none');
            },1000);

            setTimeout(function(){
                $(".confetti").addClass('d-none');
            },4500);
        });
    }

    function hover_plus()
    {
        $("body").on("mouseenter",".task",function(){
            var data_type = $(this).attr('data-type');
            var data_id = $(this).attr('data-id');

            if(data_id == undefined)
            {
                $(".bg_share_"+data_type).addClass('bg-custom');
            }
            else
            {
                $(".bg_bonus_"+data_id).addClass('bg-custom');
            }

        });

        $("body").on("mouseleave",".task",function(){
            var data_type = $(this).attr('data-type');
            var data_id = $(this).attr('data-id');

            if(data_id == undefined)
            {
                $(".bg_share_"+data_type).removeClass('bg-custom');
            }
            else
            {
                $(".bg_bonus_"+data_id).removeClass('bg-custom');
            }

        });
    }

    function task()
    {
        $("body").on("click",".task",function(){
            var data_id = $(this).attr('data-id');
            var data_type = $(this).attr('data-type');
            var data_url = $(this).attr('data-url');

            if(data_id == undefined)
            {
                data_id = 0;
            }

            if(data_type == 7)
            {
                return playYoutube(data_id,data_url);
            }

            var data = {"evid": "{{ $ev->id }}","ct_id": "{{ $ct_id }}","type" : data_type, 'bid': data_id};
            task_run(data);
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

    function task_run(data)
    {
        // var windowReference = window.open();
        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method : 'POST',
            data : data,
            url : '{{ url("save-entry") }}',
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

                if(result.success == 'copy')
                {
                    $("#copy_link").modal('show');
                }
            },
            error : function(xhr)
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            },
            complete: function()
            {
                loadtask();
            }
        });
    }

    function loadtask()
    {
        var res;
        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method : 'POST',
            url : '{{ url("taskdata") }}',
            data : {'ct_id':'{{ $ct_id }}', 'ev_id': '{{ $ev->id }}'},
            dataType : "html",
            success : function(result)
            {
                res = result;
            },
            error : function()
            {
                $("#taskdata").html("<div class='alert alert-danger'>{{ Lang::get('custom.error') }}</div>");
            },
            complete: function()
            {
                $("#taskdata").html(res);
            }
        });
    }
</script>

<script type="text/javascript">

var tag = document.createElement('script');

    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    // function onYouTubePlayerAPIReady() {
    function playYoutube(id,url) {
        var player = new YT.Player('yt_ifr_'+id, {
            videoId: url,
            playerVars: { 'autoplay': 0, 'controls': 0 },
            events: {
                // 'onReady': onPlayerReady,
                'onStateChange': function(status)
                {
                    if(status.data == 0)
                    {
                        // alert('aaa');
                        var data = {"evid": "{{ $ev->id }}","ct_id": "{{ $ct_id }}","type" : 7, 'bid': id};
                        task_run(data)
                    }
                },
                // 'onError': onPlayerError
            }
        });
    }
</script>
<script src="{{ asset('assets/js/countdowntimer.js') }}"></script>
@endsection
