@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center px-5">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">{{ Lang::get('title.connect') }}</h1>
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
                                <button type="button" id="con" class="btn bg-custom text-white w-100"><i class="fas fa-mobile-alt"></i>&nbsp;{{ Lang::get('custom.connect') }}</button>
                            @else
                                @if($phone->status == 0)
                                    <button type="button" id="pair" class="btn bg-info text-white w-100"><i class="fas fa-qrcode"></i>&nbsp;{{ Lang::get('custom.scan.btn') }}</button>
                                @endif
                            @endif
                        </div>

                        {{-- qr-code --}}
                        <div class="text-center"><span id="scan"><!-- display scan --></span></div>
                        <div id="notes_scan" class="text-center mt-1 text-capitalize"><!-- display notes --></div>

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
                            <label>{{ Lang::get('custom.number.test') }}:<span class="text-danger">*</span></label>
                            <input type="text" id="phone" name="number" class="form-control form-control-lg" required/>
                            <span class="error phone"></span>
                        </div>
                        <div class="form-group">
                            <label>{{ Lang::get('table.message') }}:<span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <textarea name="message" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ Lang::get('table.image') }}:</label>
                            <div class="input-group input-group-lg">
                                <input value="off" type="checkbox" class="form-check" name="media" />
                            </div>
                        </div>
                        <input type="hidden" name="user_id" value="{{ Auth::id() }}" />
                    </div>
                    <button type="submit" class="btn btn-info text-white">{{ Lang::get('table.test') }}</button>
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
        // check_connect()
    });

    function connect()
    {
        $("#con").click(function()
        {
            create_device();
        })
    }

    function create_device()
    {
        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method : 'POST',
            url : '{{ url("create") }}',
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

                if(result.status == 1)
                {
                   location.href="{{ url('scan') }}";
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
            getqr();
            waitingTime();
            $("#device").hide();
            $("#pair").hide();
        });

        // REFRESH TOKEN
        $("#refresh").click(function(){
            refresh();
        });
    }

    var tm, qrscan;
    function waitingTime()
    {
        var scd = 0;
        var sc = 0;
        var min = qrscan = 0;

        $("#scan").html('{{ Lang::get("custom.loading") }}');
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

            if(qrscan == 0)
            {
                if(sc % 12 == 0)
                {
                    pairing();
                }
            }

            sc++;
            scd++;
        },1000);
    };

    function getqr()
    {
        $.ajax({
            method : 'GET',
            url : '{{ url("connect") }}',
            dataType : 'json',
            success : function(result)
            {
                $('#loader').show();
                $('.div-loading').addClass('background-load');
                location.href="{{ url('scan') }}";
            },
            error: function(xhr){
               console.log(xhr.responseText);
            }
        });
    }

    function pairing()
    {
        $.ajax({
            method : 'GET',
            url : '{{ url("pair") }}',
            dataType : 'html',
            success : function(result)
            {
                if(result == 0)
                {
                    $("#scan").html('Loading...');
                }
                else
                {
                    $("#scan").html(result);
                    $("#notes_scan").html('{{ Lang::get("custom.scan") }}');
                    qrscan = 1; // to stop pairing if qrcode generated
                }
            },
            error: function(xhr){
               console.log(xhr.responseText);
            }
        });
    }

    function check_connect()
    {
        $.ajax({
            method : 'GET',
            url : '{{ url("device") }}',
            dataType : 'json',
            beforeSend : function()
            {
                $('#loader').show();
                $('.div-loading').addClass('background-load');
            },
            success : function(result)
            {
                if(result.isConnected == 1)
                {
                    location.href="{{ url('scan') }}";
                }
                else
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            }
        });
    }

    function test_message()
    {
        $("#test_message").submit(function(e){
            e.preventDefault();

            var data = $(this).serializeArray();
            var ipt = $("input[name='media']").val();
            var url;
            data.push({name : 'code', value : $(".iti__selected-flag").attr('data-code') },{name : 'test', value : 1});

            $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                method : 'POST',
                url : '{{ url("message") }}',
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

                    if(result.id !== undefined)
                    {
                        $("#msg_test").html('<div class="alert alert-success">{{ Lang::get("custom.test.success") }}</div>');
                        // $(".counter").html(result.counter);
                    }
                },
                error: function(xhr){
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            });
        });
    }

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

    function del_exe()
    {
        $.ajax({
            method : 'GET',
            url : '{{ url("del-device") }}',
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

                if(result.status == 1)
                {
                    $("#msg").html('<div class="alert alert-success">{{ Lang::get("custom.success") }}</div>');
                    setTimeout(function(){
                        location.href="{{ url('scan') }}";
                    },800);
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

    function delete_device()
    {
        $("#status").click(function(){
            check_connect();
        });

        $(".del").click(function(){
            var conf = confirm('{{ Lang::get("custom.del") }}');

            if(conf == true)
            {
                del_exe();
            }
            else
            {
                return false;
            }
        });
    }
</script>

@endsection
