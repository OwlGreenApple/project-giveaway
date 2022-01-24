@extends('layouts.app')

@section('content')
<div class="container">
    <div class="col-md-9 px-0 wrapper">
        <div class="col-lg-9 mx-auto">
            <ul class="list-group">
                @if($ev->tw == 1)
                    <li data-type="8" class="task list-group-item">Twitter Share <span class="ms-auto">+3</span></li>
                @endif
                @if($ev->fb == 1)
                    <li data-type="9" class="task list-group-item clearfix">Facebook Share <div class="float-end">+3</div></li>
                @endif
                @if($ev->wa == 1)
                    <li data-type="10" class="task list-group-item clearfix">Share Whatsapp <div class="float-end">+3</div></li>
                @endif
                @if($ev->ln == 1)
                    <li data-type="11" class="task list-group-item">Share Linkedin</li>
                @endif
                @if($ev->mail == 1)
                    <li data-type="12" class="task list-group-item">Share Email</li>
                @endif
                @if($bonus->count() > 0)
                    @foreach($bonus as $row)
                        @if($row->type == 5)
                            <li class="list-group-item clearfix"><a data-type="{{ $row->type }}" class="task" data-id="{{ $row->id }}">{{ $row->title }}</a> <div class="float-end">+{{ $row->prize }}</div></li>
                        @elseif($row->type == 7)
                            <li class="list-group-item clearfix"><a data-type="{{ $row->type }}" data-id="{{ $row->id }}" data-bs-toggle="collapse" href="#collapse_{{ $row->id }}">{{ $row->title }}</a> <div class="float-end">+{{ $row->prize }}</div></li>
                            <div id="collapse_{{ $row->id }}" class="collapse">
                                <div class="yt-box mt-1">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <div class="embed-responsive-item yt-iframe" id="yt_ifr_{{ $row->id }}"></div>
                                        <!-- <iframe frameborder="0"
        style="border: solid 4px #37474F" id="yt_ifr_{{ $row->id }}" class="embed-responsive-item yt-iframe"  src="{{ $row->url }}?enablejsapi=1" allowfullscreen></iframe> -->
                                    </div>
                                </div>
                            </div>    
                        @else
                            <li class="list-group-item clearfix"><a data-type="{{ $row->type }}" data-id="{{ $row->id }}" class="task" >{{ $row->title }}</a> <div class="float-end">+{{ $row->prize }}</div></li>
                        @endif
                    @endforeach
                @endif
            </ul>
        <!-- end col -->
        </div>
        <!-- end col -->
    </div>
    <div id="player"></div>
    <div id="player_2"></div>
    <!-- end container -->
</div>

<script>
    $(function(){
        task();
    });

    function task()
    {
        $(".task").click(function(){
            var data_id = $(this).attr('data-id');
            var data_type = $(this).attr('data-type');

            if(data_id == undefined)
            {
                data_id = 0;
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
                    // code to styling after user's task done
                }

                window.open(result.url, "_blank");
            },
            error : function(xhr)
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    }

</script>

<script type="text/javascript">

var tag = document.createElement('script');

    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    function onYouTubePlayerAPIReady() {
        var player = new YT.Player('yt_ifr_3', {
            videoId: 'gURWZyzQDZQ',
            playerVars: { 'autoplay': 0, 'controls': 1 },
            events: {
                // 'onReady': onPlayerReady,
                'onStateChange': function(status)
                {
                    if(status.data == 0)
                    {
                        alert('aaa');
                        // var data = {"evid": "{{ $ev->id }}","ct_id": "{{ $ct_id }}","type" : 7, 'bid': data_id};
                        // task_run(data)
                    }
                },
                // 'onError': onPlayerError
            }
        });
        var player2 = new YT.Player('yt_ifr_8', {
            videoId: 'gURWZyzQDZQ',
            playerVars: { 'autoplay': 0, 'controls': 1 },
            events: {
                'onStateChange': function(status)
                {
                    if(status.data == 0)
                    {
                        alert('vvvv');
                        // var data = {"evid": "{{ $ev->id }}","ct_id": "{{ $ct_id }}","type" : 7, 'bid': data_id};
                        // task_run(data)
                    }
                },
            }
        });
    }

</script>
@endsection
