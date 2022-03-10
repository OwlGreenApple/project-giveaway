@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">Create Broadcast</h1>
        </div>

        <div class="col-md-8">
            <div id="msg"><!-- --></div>
            <form id="create_broadcast">
                <!-- mode=0 insert, mode<>0 -> edit (id broadcastnya) -->
                <input type="hidden" name="mode" id="mode" value="0">
                <!-- form 1 -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <!--<h3 class="main-color main-theme">Giveaway Information</h3>
                        <div class="border-bottom info">Competition Information</div>-->

                        <!-- begin form -->
                        <div class="form-group mb-3">
                            <label>Title:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" name="title" @if(isset($broadcast)) value="{{ $broadcast->title }}" @endif />
                            <span class="text-danger err_title"><!-- --></span>
                        </div>
                        <div class="form-group mb-3">
                            <label>Message:<span class="text-danger">*</span></label>
                            <textarea name="message" id="divInput-description-post" class="form-control"></textarea>
                            <span class="text-danger err_desc"><!-- --></span>
                        </div>
                        <div class="form-group mb-3">
                            <label>Contestants</label>
                            <input id="contestant" autocomplete="off" type="text" class="form-control form-control-lg" name="contestant" />
                            <span class="contestants_list"><!-- --></span>
                            <span class="contestants_choosed"><!-- --></span>
                        </div>
                        <div class="row mb-3 input-daterange">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Date send:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg datetimepicker_1" name="date_send" @if(isset($broadcast)) value="{{ $broadcast->date_send }}" @endif/>
                                <span class="text-danger err_date_send"><!-- --></span>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Timezone</label>
                            <select class="form-select" name="timezone" id="timezone" required="">
                                @if(count($helper::timezone()) > 0)
                                    @foreach($helper::timezone() as $key=>$val)
                                        <option value="{{ $key }}">{{ $val }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="text-danger err_timezone"><!-- --></span>
                        </div>
                        <!-- end form -->
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <button type="button" class="btn btn-secondary btn-lg">Cancel</button>
                    <button type="submit" class="btn bg-custom btn-lg text-white">Submit</button>
                </div>

            </form>
        <!-- end col -->
        </div>

    </div>
</div>

<script src="{{ asset('assets/js/counting.js') }}" type="text/javascript"></script>
<script>
$(function() {
    emoji();
    display_contestant();
    add_contestants();
    del_contestants();
    datetimepicker();
    save_data();
    editForm();
});

function add_contestants()
{
    $("body").on("click",".contestant_data",function(){
        var btn = '';
        var name = $(this).attr('data_name');
        var id = $(this).attr('data_id');
        var phone = $(this).attr('data_phone');
        var len = $(".btct").length;
        var check = $('.bt_'+id+'').length;

        if(len >= 10 || check >= 1)
        {
            return false;
        }

        btn += '<span class="btct badge bg-info text-light p-2 mt-1 me-2 bt_'+id+'">';
        btn += '<span data-id="'+id+'" class="cid">'+name+' -- '+phone+'</span>';
        btn += '<span data-id="'+id+'" role="button" class="delc text-dark badge bg-light text-decoration-none ms-1">X</span></span>';
        $(".contestants_choosed").append(btn);
    });
}

function del_contestants()
{
    $("body").on("click",".delc",function(){
        var id = $(this).attr('data-id');
        var btn = $(".bt_"+id+"").remove();
    });
}

function display_contestant()
{
    $("#contestant").keyup(delay(function(e){
        var text = $(this).val();

        $.ajax({
            "type":"GET",
            "url":"{{ url('display-contestants') }}",
            "data": {"contestant" : text},
            "dataType":"html",
            beforeSend: function()
            {
                $('#loader').show();
                $('.div-loading').addClass('background-load');
            },
            success : function(result)
            {
                $(".contestants_list").html(result);
            },
            complete : function(xhr)
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });

    },300)
    );
}

function emoji()
{
    $("#divInput-description-post").emojioneArea({
        pickerPosition: "right",
        mainPathFolder : "{{url('')}}",
    });

    $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText('@if(isset($event)){{ $event->message }}@endif');
}


function editForm()
{
    @if(isset($broadcast))
    //edit timezone
        $("#timezone").val("<?php echo $broadcast->timezone; ?>");
    //edit id -> klo 0 save, klo <> 0 edit
        $("#mode").val("<?php echo $broadcast->id; ?>");
    //edit event
        $("#event").val("<?php echo $broadcast->event_id; ?>");
    @endif
}

function datetimepicker()
{
    var date, tdate;
    var ndate = new Date();
    var date_1 = $('.datetimepicker_1').val();

    (date_1.length == 0)?date = ndate : date = moment(date_1);


    var format_date = 'YYYY-MM-DD HH:mm';

    $('.datetimepicker_1').datetimepicker({
        format : format_date,
        minDate : date
    });
}

function save_data()
{
    $("#create_broadcast").submit(function(e){
        e.preventDefault();
        var desc = $("#editor").html();
        var form = $("#create_broadcast")[0];
        var data = new FormData(form);
        data.append('desc',desc);

        // return false;

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method:'POST',
            url : "{{ url('save-broadcast') }}",
            data : data,
            processData : false,
            cache : false,
            contentType: false,
            dataType : 'json',
            beforeSend: function()
            {
                $('#loader').show();
                $('.div-loading').addClass('background-load');
                // $(".error").hide();
            },
            success : function(result)
            {
                if(result.success == 1)
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                    $("#msg").html('<div class="alert alert-success">Data submitted</div>')
                }
                else if(result.success == 2)
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                    $("#msg").html('<div class="alert alert-danger">{{ Lang::get("custom.error") }}</div>')
                }
                else if(result.success == 'err')
                {
                }
                else
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                    $("#msg").html('<div class="alert alert-danger">{{ Lang::get("custom.error.id") }}</div>')
                }
            },
            error : function(xhr)
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    });
}


</script>
@endsection
