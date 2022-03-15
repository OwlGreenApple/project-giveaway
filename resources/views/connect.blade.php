@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center px-5">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">Connect WA</h1>
        </div>

        <!-- FORM -->
        <div class="col-md-8">
            <span id="msg"><!-- message --></span>
            <form id="connect">
                <div class="card px-5 py-5">
                    <div class="card-body p-0">
                        <div id="waiting" class="alert alert-info font-bold d-none">
                            <span>{{ Lang::get('custom.wait') }}</span>
                            <div class="fw-bold">
                                <span id="min"></span> :
                                <span id="secs"></span>
                            </div>
                        </div>

                        {{-- button --}}
                        <div class="input-group">
                            @if(is_null($phone))
                                <button type="button" id="con" class="btn bg-custom btn-lg text-white">Connect</button>
                            @else
                                <button type="button" id="pair" class="btn bg-info btn-lg text-white">Scan</button>
                            @endif
                            <button type="button" id="refresh" class="btn bg-warning btn-lg ms-2">Refresh</button>
                        </div>

                        {{-- qr-code --}}
                        <div class="text-center"><span id="scan"><!-- display scan --></span></div>

                        {{-- table --}}
                        <div id="device">@include('connect-table')</div>
                    <!--  -->
                    </div>
                </div>
            </form>

            @if(!is_null($phone) && $phone->status == 1)
            <!-- TEST SEND MESSAGE -->
            <div class="container mt-4 card p-3">
                <span id="msg_test"><!-- --></span>
                <form id="test_message">
                    <div class="mb-3">
                        <div class="form-group iti-wrapper">
                            <label>Number:<span class="text-danger">*</span></label>
                            <input type="text" id="phone" name="number" class="form-control form-control-lg" required/>
                            <span class="error phone"></span>
                        </div>
                        <div class="form-group">
                            <label>Message:<span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <textarea name="message" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Image:</label>
                            <div class="input-group input-group-lg">
                                <input value="off" type="checkbox" class="form-check" name="media" />
                            </div>
                        </div>
                        <input type="hidden" name="user_id" value="{{ Auth::id() }}" />
                    </div>
                    <button type="submit" class="btn btn-info text-white">TEST</button>
                </form>
            </div>
            <!-- END TEST SEND -->
            @endif

        <!-- end col -->
        </div>
    </div>
</div>

@if(!is_null($phone) && $phone->status == 1)
    <script src="{{ asset('/assets/intl-tel-input/callback.js') }}" type="text/javascript"></script>
@endif

<script type="text/javascript">
    $(document).ready(function(){
        connect();
        scan();
        delete_device();
        test_message();
        checkbox();
    });

    function checkbox(){
        $("input[name='media']").click(function(){
            var val = $(this).val();

            if(val == 'off')
            {
                $(this).val('https://cdn.pixabay.com/photo/2017/06/10/07/18/list-2389219_960_720.png');
            }
            else
            {
                $(this).val('off');
            }
        });
    }

    var tm;
    function waitingTime()
    {
        var scd = 0;
        var sc = 0;
        var min = 0;

        $("#waiting").removeClass('d-none');

        tm = setInterval(function(){
            $("#secs").html(sc);
            $("#min").html('0'+min);

            if(sc < 10)
            {
                $("#secs").html('0'+sc);
            }

            if(sc == 60){
                min = min + 1;
                $("#min").html('0'+min);
                sc = 0;
                $("#secs").html('0'+sc);
            }

            if(sc == 3 || sc == 10 || sc == 15 ||sc == 30 || sc == 59)
            {
                pairing();
            }

            if(sc % 6 == 0)
            {
                check_connect();
            }

            if(min == 1)
            {
                $("#secs").html('0'+0);
                clearInterval(tm);
                location.href="{{ url('scan') }}";
            }

            sc++;
            scd++;
        },1000);
    };

    function connect()
    {
        $("#con").click(function(){
            // e.preventDefault();
            // var data = $(this).serializeArray();
            // data.push({name : 'code', value : $(".iti__selected-flag").attr('data-code') });
            register_login_device();
        })
    }

    function register_login_device()
    {
        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method : 'POST',
            url : '{{ url("connect") }}',
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

                if(result.status == 'success')
                {
                    $("#con").remove();
                    waitingTime();
                }
                else if(result.status == 'etoken')
                {
                    register_login_device();
                }
                else
                {
                    $("#msg").html('<div class="alert alert-danger">{{ Lang::get("custom.error") }}</div>');
                }
            },
            error: function(xhr){
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    }

    function scan()
    {
        $("#pair").click(function(){
            waitingTime();
            $("#pair").hide();
        });

        // REFRESH TOKEN
        $("#refresh").click(function(){
            refresh();
        });
    }

    function refresh()
    {
        $.ajax({
            method : 'GET',
            url : '{{ url("refresh") }}',
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

                if(result.err == 0)
                {
                    $("#msg").html('<div class="alert alert-success">{{ Lang::get("custom.success") }}</div>');
                    setTimeout(function(){
                        $('#loader').show();
                        $('.div-loading').addClass('background-load');
                        location.href="{{ url('scan') }}";
                    },1000);
                }
                else
                {
                    $("#msg").html('<div class="alert alert-danger">{{ Lang::get("custom.error") }}</div>');
                }
            },
            error: function(xhr){
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
               console.log(xhr.responseText);
            }
        });
    }

    function pairing()
    {
        $.ajax({
            method : 'GET',
            url : '{{ url("pair") }}',
            dataType : 'json',
            // beforeSend : function()
            // {
            //     $('#loader').show();
            //     $('.div-loading').addClass('background-load');
            // },
            success : function(result)
            {
                // $('#loader').hide();
                // $('.div-loading').removeClass('background-load');

                if(result.status == 'IDLE')
                {
                    $("#scan").html('Loading...');
                }
                else if(result.status == 'PAIRING')
                {
                    $("#scan").html(result.qr_code);
                }
                else if(result.status == 'PAIRED')
                {
                    check_connect();
                }
                else
                {
                    // error scan usually because of expired token
                    $("#scan").html('<div class="alert alert-warning mt-2">{{ Lang::get("custom.scan") }}</div>');
                    clearInterval(tm);
                }
            },
            error: function(xhr){
               console.log(xhr.responseText);
            }
        });
    }

    function delete_device()
    {
        $("#status").click(function(){
            check_connect();
        });

        $(".del").click(function(){
            var conf = confirm('{{ Lang::get("custom.del") }}');

            if(conf == true)
            {
                var del = 1;
                check_connect(del);
            }
            else
            {
                return false;
            }
        });
    }

    function check_connect(data)
    {
        if(data !== 1)
        {
            data = null;
        }

        $.ajax({
            method : 'GET',
            url : '{{ url("device") }}',
            data : {'del':data},
            dataType : 'json',
            beforeSend : function()
            {
                $('#loader').show();
                $('.div-loading').addClass('background-load');
            },
            success : function(result)
            {
                if(result.status == 'success')
                {
                    location.href="{{ url('scan') }}";
                }
                else
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            },
            error: function(xhr){
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    }

    function test_message()
    {
        $("#test_message").submit(function(e){
            e.preventDefault();

            var data = $(this).serializeArray();
            data.push({name : 'code', value : $(".iti__selected-flag").attr('data-code') },{name : 'test', value : 1});

            var ipt = $("input[name='media']").val();
            var url;

            if(ipt == 'off')
            {
                url = '{{ url("message") }}';
            }
            else
            {
                url = '{{ url("media") }}';
            }

            $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                method : 'POST',
                url : url,
                data : data,
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
                        $("#msg_test").html('<div class="alert alert-danger">{{ Lang::get("custom.test") }}</div>');
                    }
                    else if(result.err == 2)
                    {
                        $("#msg_test").html('<div class="alert alert-danger">{{ Lang::get("custom.test.invalid") }}</div>');
                    }
                    else
                    {
                        $("#msg_test").html('<div class="alert alert-success">{{ Lang::get("custom.test.success") }}</div>');
                        $(".counter").html(result.counter);
                    }
                },
                error: function(xhr){
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            });
        });
    }
</script>

@endsection
