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
                        {{-- button --}}
                        <div class="input-group">
                            @if($waphone->count() < 3)
                                <button type="button" id="con" class="btn bg-custom text-white w-100"><i class="fas fa-mobile-alt"></i>&nbsp;{{ Lang::get('custom.connect') }}</button>
                            @endif
                        </div>

                        {{-- table --}}
                        <div id="device">@include('connect-table')</div>
                    <!--  -->
                    </div>
                </div>
            </form>

            {{-- test message --}}
                @if($waphone->count() > 0)
                <!-- TEST SEND MESSAGE -->
                <div class="container mt-4 card p-3">
                    <h3 class="account-title text-capitalize mb-2"><b><i class="fab fa-whatsapp main-color"></i>&nbsp;{{ Lang::get('table.test.message') }}</b></h3>
                    <span id="msg_test"><!-- --></span>
                    <form id="test_message">
                        <div class="mb-3">
                            <div class="form-group mb-2">
                                <label>{{ Lang::get('custom.number.sender') }}:<span class="text-danger">*</span></label>
                                <select name="sender" class="form-select">
                                    @foreach($phone as $row)
                                        @if($row->status > 0)
                                            <option value="{{ $row->number }}">{{ $row->number }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group iti-wrapper mb-2">
                                <label>{{ Lang::get('custom.number.test') }}:<span class="text-danger">*</span></label>
                                <input type="text" id="phone" name="number" class="form-control form-control-lg" required/>
                                <span class="error phone"></span>
                            </div>
                            
                            <div class="form-group mb-2">
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
                        <button type="submit" class="btn btn-outline-secondary w-25">{{ Lang::get('table.test') }}</button>
                    </form>
                </div>
            <!-- END TEST SEND -->
            @endif
        <!-- end col -->
        </div>
    </div>
</div>

@if($waphone->count() > 0)
    <script src="{{ asset('/assets/intl-tel-input/callback.js') }}" type="text/javascript"></script>
@endif
<script type="text/javascript">
    $(document).ready(function(){
        connect();
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

        // prevent user to open new tab
        $('.scanqr').bind("contextmenu",function(e){
            return false;
        });
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
                else if(result.status == 'max')
                {
                    $("#msg").html('<div class="alert alert-warning">{{ Lang::get("table.service.max") }}</div>');
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
                    
                    if(result.error === 1)
                    {
                        $("#msg_test").html('<div class="alert alert-warning">{{ Lang::get("custom.test.phone") }}</div>');
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

    function del_exe(phone_id)
    {
        $.ajax({
            method : 'GET',
            url : '{{ url("del-device") }}',
            data : {'phone_id':phone_id},
            dataType : 'json',
            beforeSend : function()
            {
                $('#loader').show();
                $('.div-loading').addClass('background-load');
            },
            success : function(result)
            {
                if(result.status == 1)
                {
                    $("#msg").html('<div class="alert alert-success">{{ Lang::get("custom.success") }}</div>');
                    setTimeout(function(){
                        location.href="{{ url('scan') }}";
                    },800);
                }
                else
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
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
                var phone_id = $(this).attr('id');
                phone_id = phone_id.split('-');
                phone_id = phone_id[1];
                del_exe(phone_id);
            }
            else
            {
                return false;
            }
        });
    }
</script>

@endsection
