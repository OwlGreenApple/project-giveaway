@extends('layouts.app')

@section('content')
<div class="container">
    <div class="col-md-9 px-0 wrapper">
        <div class="col-lg-9 mx-auto">
            <ul class="list-group">
                @if($ev->tw == 1)
                    <li class="list-group-item">Twitter Share <span class="ms-auto">+3</span></li>
                @endif
                @if($ev->fb == 1)
                    <li class="list-group-item clearfix">Facebook Share <div class="float-end">+3</div></li>
                @endif
                @if($ev->wa == 1)
                    <li class="list-group-item clearfix">Share Whatsapp <div class="float-end">+3</div></li>
                @endif
                @if($ev->ln == 1)
                    <li class="list-group-item">Share Linkedin</li>
                @endif
                @if($ev->mail == 1)
                    <li class="list-group-item">Share Email</li>
                @endif
                @if($bonus->count() > 0)
                    @foreach($bonus as $row)
                        @if($row->type == 5)
                            <li class="list-group-item clearfix"><a>{{ $row->title }}</a> <div class="float-end">+{{ $row->prize }}</div></li>
                        @elseif($row->type == 7)
                            <li class="list-group-item clearfix"><a data-bs-toggle="collapse" data-bs-target="#collapse_{{ $row->id }}">{{ $row->title }}</a> <div class="float-end">+{{ $row->prize }}</div></li>
                            <div class="yt-box mt-1 collapse" id="collapse_{{ $row->id }}">
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe class="embed-responsive-item yt-iframe" src="{{ $row->url }}" allowfullscreen></iframe>
                                </div>
                            </div>
                            
                        @else
                            <li class="list-group-item clearfix"><a data="{{ $row->url }}">{{ $row->title }}</a> <div class="float-end">+{{ $row->prize }}</div></li>
                        @endif
                    @endforeach
                @endif
            </ul>
        <!-- end col -->
        </div>
        <!-- end col -->
    </div>
    <!-- end container -->
</div>

<script>
    $(function(){
        register_contestant();
    });

    function register_contestant()
    {
        $("#save_contestant").submit(function(e){
            e.preventDefault();
            var code = $(".iti__selected-flag").attr('data-code');
            var data = $(this).serializeArray();
            save_contestant(data);
        });   
    }

    function save_contestant(data)
    {
        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method : 'POST',
            data : data,
            url : '{{ url("save-contestant") }}',
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

                if(result.err == 1)
                {
                    $(".err_"+result[0][1]).html(result[0][0]);
                    $(".err_"+result[1][1]).html(result[1][0]);
                    $(".err_"+result[2][1]).html(result[2][0]);
                }
                else
                {
                    // location.href="";
                }
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
