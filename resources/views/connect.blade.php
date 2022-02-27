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
                        <div class="iti-wrapper">
                        @if(is_null($phone))
                            <div class="input-group">
                                <!-- <input type="text" id="phone" name="phone" class="form-control form-control-lg" required/>
                                <span class="error phone"></span> -->
                                <button type="button" id="con" class="btn bg-custom btn-lg text-white">Connect</button>
                            </div>
                        @else
                            <button type="button" id="pair" class="btn bg-info btn-lg text-white">Scan</button>
                            <!-- <button type="button" id="status" class="btn bg-warning btn-lg text-white">Refresh</button> -->
                        @endif
                        </div>

                        <div class="text-center"><span id="scan"><!-- display scan --></span></div>

                        <div id="device">@include('connect-table')</div>
                    <!--  -->
                    </div>
                </div>
            </form>
        <!-- end col -->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        connect();
        scan();
        delete_device();
    });

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

            $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                method : 'POST',
                // data : data,
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
                    else
                    {
                        $("#msg").html('div class="alert alert-danger">{{ Lang::get("custom.error") }}</div>');
                    }
                },
                error: function(xhr){
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            });
        })
    }

    function scan()
    {
        $("#pair").click(function(){
            waitingTime();
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

                if(result.qr_code !== null)
                {
                    $("#scan").html('<img src="'+result.qr_code+'" />');
                }

                if(result.status == 'PAIRED')
                {
                    check_connect();
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
            // headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            // method : 'POST',
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
</script>

@endsection