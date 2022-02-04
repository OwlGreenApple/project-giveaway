@extends('layouts.app')

@section('content')
<div class="container">
    <div class="col-md-9 px-0 wrapper">
            <!-- youtube or banner carousel -->
            
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                @if($event->media == 0)
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                @endif
                <div class="carousel-inner">
                @if($event->media == 0)
                    @if(count($banners) > 0)
                        @foreach($banners as $index=>$row)
                            <div class="carousel-item @if($index == 0) active @endif">
                                <img src="{{ $row }}" class="d-block w-100" />
                            </div>
                        @endforeach
                    @endif
                @else
                    <div class="carousel-item active">
                        <div class="yt-box">
                            <iframe class="yt-iframe" src="{{ $event->youtube_banner }}" ></iframe>
                        </div>
                    </div>
                @endif
                </div>
                @if($event->media == 0)
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                @endif
            </div>
            <!-- end carousel -->

            <div class="col-lg-9 mx-auto">
                <div class="contest-title mt-3">{{ $event->title }}</div>
                <div class="time-contest">
                    <div class="bg-dot"></div>
                    <div class="text">Time Left</div>
                    <div class="bg-dot"></div></div>
                <div class="container">
                    <!-- timer -->
                    <div id="countdown" class="text-center mt-3">
                        <ul>
                            <li><div class="count" id="days"></div><div class="count-label">Days</div></li>
                            <li class="count-space"><span class="count">:</span></li>
                            <li><div class="count" id="hours"></div><div class="count-label">Hours</div></li>
                            <li class="count-space"><span class="count">:</span></li>
                            <li><div class="count" id="minutes"></div><div class="count-label">Minutes</div></li>
                            <li class="count-space"><span class="count">:</span></li>
                            <li><div class="count" id="seconds"></div><div class="count-label">Seconds</div></li>
                        </ul>
                    </div>
                    <!-- end timer -->
                </div>
                <h5 class="text-center mb-3">
                    <span class="me-2">
                        <i class="fas fa-gift"></i> {{ Lang::get('custom.prize') }} : <b class="main-color"><span class="text-uppercase">{{ $user->currency }}</span> {{ $helpers::format($event->prize_value) }}</b>
                    </span>
                    <span class="ms-2">
                        <i class="fas fa-trophy"></i> {{ Lang::get('custom.winner') }} : <b class="trophy">{{ $event->winners }}</b>
                    </span>
                </h5>
                <div class="text-center mt-4 mb-3 form_title"><h4 class="mb-0">{{ Lang::get('custom.enter') }}</h4></div>
                <!-- contestant enter -->
                <form class="contest-form" id="save_contestant">
                    <div class="form-group mb-3">
                        <label>{{Lang::get('custom.name')}} <i class="far fa-id-card ct_color"></i></label>
                        <input name="contestant" required type="text" class="form-control form-control-lg" />
                        <span class="text-danger err_contestant"><!-- --></span>
                    </div>
                    <div class="form-group mb-3">
                        <label>{{Lang::get('custom.email')}} <i class="fas fa-at ct_color"></i></label>
                        <input name="email" required type="email" class="form-control form-control-lg" />
                        <span class="text-danger err_email"><!-- --></span>
                    </div>
                    <div class="iti-wrapper mb-3">
                        <label>{{Lang::get('custom.number')}} <b><i class="fab fa-whatsapp ct_color"></i></b></label>
                        <input type="text" required id="phone" name="phone" class="form-control form-control-lg" required/>
                        <span class="text-danger err_phone"><!-- --></span>
                    </div>
                    <button type="submit" class="btn bg-dark text-white">{{ Lang::get('custom.submit') }}</button>
                </form>

                <!-- description -->
                <div class="terms mt-4">{!! $event->desc !!}</div>
                
                <!-- end container -->
            </div>
            <!-- footer -->
            <div class="row px-3 py-3">
                <div class="col-lg-6 desc">{{ Lang::get('custom.giveaway_timezone') }} : {{ $event->timezone }}</div>
                <div class="col-lg-6 desc">{{ Lang::get('custom.offered') }} : <a href="{{ $event->owner_url }}" class="main-color">{{ $event->owner }}</a></div>
            </div>
        <!-- end col -->
    </div>
</div>

<script>
    var global_date = "{{ $event->end }}"; //set target event date

    $(function(){
        register_contestant();
    });

    function register_contestant()
    {
        $("#save_contestant").submit(function(e){
            e.preventDefault();
            var code = $(".iti__selected-flag").attr('data-code');
            var data = $(this).serializeArray();
            data.push({name : 'pcode', value : code});
            data.push({name : 'link', value : '{{ $link }}'});
            data.push({name : 'ref', value : '{{ $ref }}'});
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
                if(result.err == 1)
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                    
                    $(".err_"+result[0][1]).html(result[0][0]);
                    $(".err_"+result[1][1]).html(result[1][0]);
                    $(".err_"+result[2][1]).html(result[2][0]);
                }
                else
                {
                    location.href="{{ url('contest') }}";
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
<script src="{{ asset('assets/js/countdowntimer.js') }}"></script>
@endsection