@extends('layouts.app')

@section('content')
<div class="container">
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
        <h4 class="text-center text-uppercase">{{ Lang::get('custom.prize') }} : <b class="main-color">{{ $ev->currency }}&nbsp;{{ $helper::format($ev->prize_value) }}</b></h4>

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
        </div>
    </div>
    <!-- end container -->
</div>

<!-- fixed timer -->

<div class="col-lg-12 text-center bg-white task_entries">
    <div class="container row">
        <div class="col-lg-6 clearfix">
            <div class="float-end d-flex">
                <div class="me-1 text-uppercase">Your Entries :</div> 
                <div class="main-color">{{$ct->entries}}</div>
            </div>
        </div>

        <div class="col-lg-6 d-flex">
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

<script>
    var global_date = "{{ $ev->end }}"; //set target event date

    $(function(){
        task();
        loadtask();
        hover_plus();
    });

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
    
    function task_run(data)
    {
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

                if(result.success == 1)
                {
                    // code to styling after contestant doing task
                    if(result.url !== null)
                    {
                        window.open(result.url, "_blank");
                    }
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
        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method : 'POST',
            url : '{{ url("taskdata") }}',
            data : {'ct_id':'{{ $ct_id }}', 'ev_id': '{{ $ev->id }}'},
            dataType : "html",
            success : function(result)
            {
                $("#taskdata").html(result);
            },
            error : function()
            {
                $("#taskdata").html("<div class='alert alert-danger'>{{ Lang::get('custom.error') }}</div>");
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