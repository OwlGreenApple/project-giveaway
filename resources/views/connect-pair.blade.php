@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center px-5">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">{{ Lang::get('title.pair') }}</h1>
        </div>

        <!-- FORM -->
        <div class="col-md-8">
            <span id="msg"><!-- message --></span>
            
            <div class="card px-5 py-5">
                <div class="card-body p-0">
                    <div id="waiting" class="alert alert-info font-bold d-none">
                        <span>{{ Lang::get('custom.wait') }}</span>
                        <div class="fw-bold">
                            <span id="min"></span> :
                            <span id="secs"></span>
                        </div>
                    </div>

                    {{-- qr-code --}}
                    <div class="text-center"><span id="scan"><!-- display scan --></span></div>
                    <div id="notes_scan" class="text-center mt-1 text-capitalize"><!-- display notes --></div>

                <!--  -->
                </div>
            </div>
          
        <!-- end col -->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        scan();
        // check_connect()
    });

    function scan()
    {
        getqr();
        waitingTime();
        $("#device").hide();
        $("#pair").hide();
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
            data : {'phone_id':'{{ $id }}'}, 
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
            data : {'phone_id':'{{ $id }}'},
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
            data : {'phone_id':'{{ $id }}'},
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

</script>

@endsection
