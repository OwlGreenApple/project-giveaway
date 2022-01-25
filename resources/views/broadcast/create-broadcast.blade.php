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
                <!-- form 1 -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <!--<h3 class="main-color main-theme">Giveaway Information</h3>
                        <div class="border-bottom info">Competition Information</div>-->
                        
                        <!-- begin form -->
                        <div class="form-group mb-3">
                            <label>Title:<span class="text-danger">*</span></label>
                            <input type="text" @if(isset($event)) value="{{ $event->title }}" @endif class="form-control form-control-lg" name="title" />
                            <span class="text-danger err_title"><!-- --></span>
                        </div> 
                        <div class="form-group mb-3">
                            <label>Description:<span class="text-danger">*</span></label>
                            <div id="editparent">
                                <div id='editControls' class="py-2">
                                    <div class='btn-group'>
                                        <select class="fontsize form-select form-select-sm">
                                            <option value='normal'>Normal</option>
                                            <option value='h1'>Large</option>
                                            <option value='h2'>Medium</option>
                                            <option value='h3'>Small</option>
                                        </select>
                                    </div>
                                    <div class='btn-group'>
                                    <a class='btn' data-role='bold'><b>Bold</b></a>
                                    <a class='btn' data-role='italic'><em>Italic</em></a>
                                    <a class='btn' data-role='underline'><u><b>U</b></u></a>
                                    <a class='btn' data-role='strikeThrough'><strike>abc</strike></a>
                                    </div>
                                    <div class='btn-group'>
                                    <a class='btn' data-role='justifyLeft'><i class="fas fa-align-left"></i></a>
                                    <a class='btn' data-role='justifyCenter'><i class="fas fa-align-center fa-flip-vertical"></i></a>
                                    <a class='btn' data-role='justifyRight'><i class="fas fa-align-right"></i></a>
                                    <a class='btn' data-role='justifyFull'><i class="fas fa-align-justify"></i></a>
                                    </div>
                                    <div class='btn-group'>
                                    <a class='btn' data-role='indent'><i class="fas fa-indent"></i></a>
                                    <a class='btn' data-role='outdent'><i class="fas fa-indent fa-flip-horizontal"></i></a>
                                    </div>
                                </div>
                                <!-- textarea editor -->
                                <div id='editor' contenteditable></div>
                            </div>
                            <span class="text-danger err_desc"><!-- --></span>
                        </div> 
                        <div class="row mb-3 input-daterange">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Start At:<span class="text-danger">*</span></label>
                                <input @if(isset($event)) value="{{ $event->start }}" @endif type="text" class="form-control form-control-lg datetimepicker_1" name="start" />
                                <span class="text-danger err_start"><!-- --></span>
                            </div> 
                            <div class="form-group col-md-6 col-lg-6">
                                <label>End At:<span class="text-danger">*</span></label>
                                <input @if(isset($event)) value="{{ $event->end }}" @endif type="text" class="form-control form-control-lg datetimepicker_2" name="end" />
                                <span class="text-danger err_end"><!-- --></span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Awarded At:<span class="text-danger">*</span></label>
                                <input @if(isset($event)) value="{{ $event->award }}" @endif type="text" class="form-control form-control-lg datetimepicker_3" name="award" />
                                <span class="text-danger err_award"><!-- --></span>
                            </div> 
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Number Of Winners:<span class="text-danger">*</span></label>
                                <input @if(isset($event)) value="{{ $event->winners }}" @endif type="number" min="1" class="form-control form-control-lg w-25" name="winner" />
                                <span class="text-danger err_winner"><!-- --></span>
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input @if(isset($event) && $event->unlimited == 1) checked value="on" @endif class="form-check-input" type="checkbox" name="unl_cam" id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault">
                            Unlimited Campaign
                            </label>
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
                    <button type="submit" class="btn bg-custom btn-lg text-white">Save</button>
                </div>

            </form>
        <!-- end col -->
        </div>

    </div>
</div>



<script>
$(function() {
    save_data();
});

function save_data()
{
    $("#create_broadcast").submit(function(e){
        e.preventDefault();
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
                    location.href="{{ url('edit-event') }}/"+result.id;
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
<script src="{{ asset('assets/js/counting.js') }}" type="text/javascript"></script>
@endsection
