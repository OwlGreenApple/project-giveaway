@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">Create Giveaway</h1>
        </div>

        <div class="col-md-8">
            <div id="msg">@if(Cookie::get('url') !== null) <div class="alert alert-info text-center">{{ Lang::get('custom.link') }} : <a class="main-color" href="{!! Cookie::get('url') !!}">{{ Lang::get('custom.click') }}</a></div> @endif</div>
            <form id="create_event">
                <!-- form 1 -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-color main-theme">Giveaway Information</h3>
                        <div class="border-bottom info">Competition Information</div>
                        
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
                        <!-- new line -->
                        <div class="border-bottom info">Who's Running This Giveaway?</div>
                        <div class="row mb-3">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Name:<span class="text-danger">*</span></label>
                                <input name="owner_name" @if(isset($event)) value="{{ $event->owner }}" @endif type="text" class="form-control form-control-lg" />
                                <span class="text-danger err_owner_name"><!-- --></span>
                            </div> 
                            <div class="form-group col-md-6 col-lg-6">
                                <label>URL:<span class="text-danger">*</span></label>
                                <input name="owner_url" @if(isset($event)) value="{{ $event->owner_url }}" @endif placeholder="http://" type="text" class="form-control form-control-lg" />
                                <span class="text-danger err_owner_url"><!-- --></span>
                            </div>
                        </div>
                        <!-- new line -->
                        <div class="border-bottom info">What Are You Giving Away?</div>
                        <div class="row mb-3">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Prize Name:<span class="text-danger">*</span></label>
                                <input name="prize_name" @if(isset($event)) value="{{ $event->prize_name }}" @endif type="text" class="form-control form-control-lg" />
                                <span class="text-danger err_prize_name"><!-- --></span>
                            </div> 
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Prize Value:<span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text text-uppercase" id="inputGroup-sizing-lg">{{ $user->currency }}</span>
                                    <input name="prize_amount" @if(isset($event)) value="{{ $event->prize_value }}" @endif id="amount" maxlength="8" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
                                    <span class="text-danger err_prize_amount"><!-- --></span>
                                </div>
                            </div>
                        </div>
                        <!-- new line -->
                        <div class="border-bottom info">Prize Images / Youtube Video.</div>
                        <div class="text-justify title mb-3">Tip: use images with a 2x1 ratio (minimum of 680px width)</div>
                        <div class="form-check form-switch mb-2">
                            <input @if(isset($event) && $event->media == 1) value="on" checked @endif name="media_option" class="form-check-input" type="checkbox" id="media_option">
                            <label class="form-check-label" for="media_option">Youtube Video</label>
                        </div>

                        <div class="upload_banner form-group d-none">
                            <label>Youtube URL:</label>
                            <input name="youtube_url" value="@if(isset($event) && $event->media == 1) {{ $event->youtube_banner }} @endif" type="text" class="form-control form-control-lg" />
                            <small>{{Lang::get('custom.youtube_banner')}} : <span class="main-color">https://www.youtube.com/embed/xxxx</span></small>
                            <span class="text-danger err_youtube_url"><!-- --></span>
                        </div>

                        <div class="input-images"><!-- display preview here --></div>
                        @if(count($data) > 0)
                            <!-- important to show which image would be deleted -->
                            @foreach($data as $id=>$row)
                                <input type="hidden" value="{{ $id }}" name="list[]" />
                            @endforeach
                        @endif
                        <span class="text-danger err_images"><!-- --></span>
                        <!-- end form -->
                    </div>
                </div>

                <!-- form 2 -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-color main-theme">Sharing</h3>
                        <div class="text-justify title">Click to select the platforms you want your contestants to use to share your giveaway:</div>
                        <div class="giveaway-icons">
                            <div class="mx-auto icon-wrapper"> 
                                <i data-id="tw" class="fab fa-twitter box @if(isset($event)) @if($event->tw == 1) box-color @endif @else box-color @endif"></i>
                                <i data-id="fb" class="fab fa-facebook-f box @if(isset($event)) @if($event->fb == 1) box-color @endif @endif"></i>
                                <i data-id="wa" class="fab fa-whatsapp box @if(isset($event)) @if($event->wa == 1) box-color @endif @else box-color @endif"></i>
                                <i data-id="ln" class="fab fa-linkedin-in box @if(isset($event)) @if($event->ln == 1) box-color @endif @endif"></i>
                                <i data-id="mail" class="far fa-envelope box @if(isset($event)) @if($event->mail == 1) box-color @endif @endif"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- form 3 -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-color main-theme">Bonus Entries</h3>
                        <div class="title text-justify fst-italic border-bottom py-3 mb-4">These are actions a contestant can take to get even more entries.</div>
                        
                        @if(count($bonus) > 0)
                        <!-- entries column -->
                            @foreach($bonus as $row)
                                <div class="row mb-4 entries pos_edit_{{ $row['id'] }}">
                                    <div class="border-bottom info">{{ $row['name'] }}&nbsp;<a del_edit_id="{{ $row['id'] }}" class="del-entry"><i class="far fa-trash-alt title"></i></a></div>
                                    <div class="form-group col-md-6 col-lg-6 mb-2">
                                        <label>{{ Lang::get('custom.act') }}<span class="text-danger">*</span></label>
                                        <input value="{{ $row['title'] }}" type="text" class="form-control form-control-lg" name="edit_text_{{ $row['mod'] }}[{{ $row['id'] }}]" />
                                    </div> 

                                    @if($row['type'] !== '5')
                                    <div class="form-group col-md-6 col-lg-6 mb-2">
                                        <label>{{ $row['col_name'] }}<span class="text-danger">*</span></label>
                                        <input @if($row['type'] == '7') placeholder="https://www.youtube.com/watch?v=xxxxxx" @endif value="{{ $row['url'] }}" type="text" class="@if($row['type'] == '7')em_{{ $row['id'] }}@endif form-control form-control-lg emb" name="edit_url_{{ $row['mod'] }}[{{ $row['id'] }}]" />
                                    </div>
                                    @endif
                                
                                    @if($row['type'] !== '5')
                                    <div class="form-group col-md-12 col-lg-12">
                                    @else
                                    <div class="form-group col-md-6 col-lg-6 mb-2">
                                    @endif
                                        <label>{{ Lang::get('custom.entry') }}<span class="text-danger">*</span></label>
                                        <div class="row g1">
                                            <div class="col-auto">
                                                <input value="{{ $row['prize'] }}" type="number" min="1" class="form-control form-control-lg" name="edit_entries_{{ $row['mod'] }}[{{ $row['id'] }}]" />
                                            </div>
                                            @if($row['type'] !== '5')
                                            <div class="col-auto">
                                                <span class="form-text title">
                                                    {{Lang::get('custom.clue')}}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                        <!--  -->
                                    </div>
                                    <input type="hidden" name="entries[]" value="{{ $row['id'] }}" />
                                </div>
                                <input type="hidden" name="compare[]" value="{{ $row['id'] }}" />
                            @endforeach
                        @endif
                        <!-- end logic bonus entry -->

                        <!-- display bonus entry column -->
                        <div id="error_bonus_entry" class="mb-2"><!-- error here --></div>

                        <div id="bonus_entry"><!-- --></div>
                        
                        <div class="col-lg-6 mt-4">
                            <select id="bonus" class="form-select">
                                <option>{{ Lang::get('custom.add_entry') }}</option>
                                <optgroup label="Social Follow">
                                    <option value="fb">{{ Lang::get('custom.fb') }}</option>
                                    <option value="ig">{{ Lang::get('custom.ig') }}</option>
                                    <option value="tw">{{ Lang::get('custom.tw') }}</option>
                                    <option value="yt">{{ Lang::get('custom.yt') }}</option>
                                    <option value="pt">{{ Lang::get('custom.pt') }}</option>
                                </optgroup>
                                <optgroup label="Other">
                                    <option value="de">{{ Lang::get('custom.de') }}</option>
                                    <option value="cl">{{ Lang::get('custom.cl') }}</option>
                                    <option value="wyt">{{ Lang::get('custom.wyt') }}</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- form 4 -->
                @if($apicheck == true)
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-color main-theme">Integration</h3>
                        
                        @if(count($act) > 0)
                            <div class="border-bottom info">Activrespon</div>
                            <select id="act_api_id" name="act_api_id" class="form-select">
                            <option value="0">Choose</option>
                                @foreach($act as $row)
                                    <option value="{{ $row['id'] }}" @if(isset($event) && $event->act_api_id == $row['id']) selected @endif>{{ $row['label'] }}</option>
                                @endforeach
                            </select>
                        @endif

                        @if(count($mlc) > 0)
                            <div class="border-bottom info">Mailchimp</div>
                            <select id="mlc_api_id" name="mlc_api_id" class="form-select">
                            <option value="0">Choose</option>
                                @foreach($mlc as $row)
                                    <option value="{{ $row->id }}" @if(isset($event) && $event->mlc_api_id == $row->id) selected @endif>{{ $row->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
                @endif

                <!-- form 5 -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-theme title">EU GDPR consent checkbox</h3>
                        <p class="fcs italic title">Are you planning to send your entrants marketing messages after the giveaway? Are any of your contestants located in the EU? If yes, or you’re not sure, enable the checkbox option below so your contestants can give clear consent as required by EU GDPR.</p>
                        <input type="checkbox" class="form-checkbox me-1" />&nbsp;<span class="title">GDPR Consent</span>
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
    editor();
    datetimepicker();
    count_logic();
    save_data();
    image_uploader();
    display_media();
    setup_sharing();
    add_bonus_entry();
    delete_bonus_entry();
    select_timezone();
    pastePreview();
});

var err_bonus = '';

function select_timezone()
{
    var timezone;
    @if(isset($timezone))
        timezone = '{{ $timezone }}';
    @else
        timezone = 'Pacific/Auckland';
    @endif

    $("#timezone option[value='"+timezone+"']").prop('selected',true);
}

// GET ONLY YOUTUBE CODE URL WHEN USER PASTE YOUTUBE URL ON BONUS ENTRY
function pastePreview()
  {
    $("body").on("paste",".emb",function(e){
      var cl = $(this).attr('class');
      cl = cl.split(' ');
      var counter = cl[0].split('_');
      var id;

      if(counter[1] == "new")
      {
          id = counter[1]+"_"+counter[2];
      }
      else
      {
          id = counter[1];
      }

      var pastedData = e.originalEvent.clipboardData.getData('text');
      pastedData = pastedData.split("=");

      setTimeout(function(){
        $(".em_"+id).val(pastedData[1]);
      },100);      
    })
  }

// ADDING COLUMN BONUS ENTRY
function add_bonus_entry()
{
    $("#bonus").change(function(){
        var val = $(this).val();
        var column = column_entry(val);

        $("#bonus_entry").append(column);
        $("#bonus option").eq(0).prop('selected',true);
    });
}

function delete_bonus_entry()
{
    $("body").on("click",".del-entry",function(){
        var pos = $(this).attr('del_new_id');
        var edit = $(this).attr('del_edit_id');

        $(".pos_"+pos).remove();
        $(".pos_edit_"+edit).remove();
    });
}

function column_entry(val)
{
    var title, col_2;
    var col_1 = "{{ Lang::get('custom.act') }}";
    var col_3 = '{{ Lang::get("custom.entry") }}';

    if(val == 'fb')
    {
        title = "{{ Lang::get('custom.fb') }}";
        col_2 = "{{ Lang::get('custom.fb.col') }}";
    }     
    else if(val == 'ig')
    {
        title = "{{ Lang::get('custom.ig') }}";
        col_2 = "{{ Lang::get('custom.ig.col') }}";
    }
    else if(val == 'tw')
    {
        title = "{{ Lang::get('custom.tw') }}";
        col_2 = "{{ Lang::get('custom.tw.col') }}";
    }
    else if(val == 'yt')
    {
        title = "{{ Lang::get('custom.yt') }}";
        col_2 = "{{ Lang::get('custom.yt.col') }}";
    }
    else if(val == 'pt')
    {
        title = "{{ Lang::get('custom.pt') }}";
        col_2 = "{{ Lang::get('custom.pt.col') }}";
    }
    else if(val == 'de')
    {
        title = "{{ Lang::get('custom.de') }}";
    }
    else if(val == 'cl')
    {
        title = "{{ Lang::get('custom.cl') }}";
        col_2 = "{{ Lang::get('custom.cl.col') }}";
    }
    else if(val == 'wyt')
    {
        title = "{{ Lang::get('custom.wyt') }}";
        col_2 = "{{ Lang::get('custom.wyt.col') }}";
    }
    else
    {
        return false;
    }

    var len = $(".entries").length;

    $column = '';
    $column += '<div class="row mb-4 entries pos_'+len+'">';
    $column += '<div class="border-bottom info">'+title+' <a del_new_id='+len+' class="del-entry"><i class="far fa-trash-alt title"></i></a></div>';
   
    $column += '<div class="form-group col-md-6 col-lg-6 mb-2">';
    $column += '<label>'+col_1+':<span class="text-danger">*</span></label>';
    $column += '<input type="text" class="form-control form-control-lg" name="new_text_'+val+'[]" />';
    $column += '</div>';

    if(val !== 'de')
    {
        $column += '<div class="form-group col-md-6 col-lg-6 mb-2">';
        $column += '<label>'+col_2+'<span class="text-danger">*</span></label>';

        if(val == 'wyt')
        {
            $column += '<input placeholder="https://www.youtube.com/watch?v=xxxxxx" type="text" class="em_new_'+len+' form-control form-control-lg emb" name="new_url_'+val+'[]" />';
            
        }
        else
        {
            $column += '<input type="text" class="form-control form-control-lg" name="new_url_'+val+'[]" />';
        }
        $column += '</div>';
        $column += '<div class="form-group col-md-12 col-lg-12">';
    }
    else{
        $column += '<div class="form-group col-md-6 col-lg-6 mb-2">';
    }            
    
    $column += '<label>'+col_3+'<span class="text-danger">*</span></label>';
    $column += '<div class="row g1">';
    $column += '<div class="col-auto">';
    $column += '<input type="number" min="1" class="form-control form-control-lg" name="new_entries_'+val+'[]" />';
    $column += '</div>';

    if(val !== 'de')
    {
        $column += '<div class="col-auto">';
        $column += '<span class="form-text title">';
        $column += 'How many entries this action is worth';
        $column += '</span></div>';
    }
    
    $column += '</div>';
    $column += '</div></div>';

    return $column;
}

// SHARING CHANGE COLOR
function setup_sharing()
{
    $(".box").click(function(){
        var box = $(this);
        if(box.hasClass('box-color') == true)
        {
            $(this).removeClass('box-color');
        }
        else
        {
            $(this).addClass('box-color');
        }
    });
}

// DISPLAY VIDEO OR BANNER
function display_media()
{
    var val = $("#media_option").val();
    detect_video_or_banner(val);

    $("#media_option").click(function(){
        var value = $(this).val();
        detect_video_or_banner(value);
    });
}

function detect_video_or_banner(val)
{
    var target = $("#media_option");
    if(val == 'on')
    {
        target.val('off');
    }
    else
    {
        target.val('on');
    }
    
    if(val == 'on'){
        $(".upload_banner").addClass('d-none');
        $(".input-images").removeClass('d-none');
    }
    else
    {
        $(".upload_banner").removeClass('d-none');
        $(".input-images").addClass('d-none');
    }
}

function image_uploader()
{
    $('.input-images').imageUploader({
        label :'Maximum image is 5, must jpg, png or gif',
        "{{ $preloaded }}": [
            @if(count($data) > 0)
                @foreach($data as $id=>$row)
                    {id: "{{ $id }}", src: "{{ $row }}"},
                @endforeach
            @endif
        ],
        maxFiles : 5
    });

    $("body").on("click",".delete-image",function(){
        var val = $("input[name='preloaded]").val();
        console.log(val);
    });
}

function save_data()
{
    $("#create_event").submit(function(e){
        e.preventDefault();
        var desc = $("#editor").html();
        var len = $(".box-color").length;
        var form = $("#create_event")[0];
        var data = new FormData(form);
        data.append('desc',desc);

        @if(isset($event))
            data.append('edit','{{ $event->id }}');
        @endif

        // retrieve data from sharing
        for(x=0;x<len;x++)
        {
            var fab = $(".box-color").eq(x).attr('data-id');
            data.append(fab,1);
        }

        // return false;

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method:'POST',
            url : "{{ url('save-events') }}",
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
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                    
                    $(".err_"+result[0][1]).html(result[0][0]);
                    $(".err_"+result[1][1]).html(result[1][0]);
                    $(".err_"+result[2][1]).html(result[2][0]);
                    $(".err_"+result[3][1]).html(result[3][0]);
                    $(".err_"+result[4][1]).html(result[4][0]);
                    $(".err_"+result[5][1]).html(result[5][0]);
                    $(".err_"+result[6][1]).html(result[6][0]);
                    $(".err_"+result[7][1]).html(result[7][0]);
                    $(".err_"+result[8][1]).html(result[8][0]);
                    $(".err_"+result[9][1]).html(result[9][0]);
                    $(".err_"+result[10][1]).html(result[10][0]);
                    $(".err_"+result[11][1]).html(result[11][0]);
                    $(".err_"+result[12][1]).html(result[12][0]);

                    err_bonus =''; //to clear error message

                    // new bonus entry validation
                    (result.err_fb !== undefined)?display_bonus_error(result.err_fb):false;
                    (result.err_ig !== undefined)?display_bonus_error(result.err_ig):false;
                    (result.err_tw !== undefined)?display_bonus_error(result.err_tw):false;
                    (result.err_yt !== undefined)?display_bonus_error(result.err_yt):false;
                    (result.err_pt !== undefined)?display_bonus_error(result.err_pt):false;
                    (result.err_de !== undefined)?display_bonus_error(result.err_de):false;
                    (result.err_cl !== undefined)?display_bonus_error(result.err_cl):false;
                    (result.err_wyt !== undefined)?display_bonus_error(result.err_wyt):false;
                   
                    // edit bonus entry validation
                    (result.err_edit_fb !== undefined)?display_bonus_error(result.err_edit_fb):false;
                    (result.err_edit_ig !== undefined)?display_bonus_error(result.err_edit_ig):false;
                    (result.err_edit_tw !== undefined)?display_bonus_error(result.err_edit_tw):false;
                    (result.err_edit_yt !== undefined)?display_bonus_error(result.err_edit_yt):false;
                    (result.err_edit_pt !== undefined)?display_bonus_error(result.err_edit_pt):false;
                    (result.err_edit_cl !== undefined)?display_bonus_error(result.err_edit_cl):false;
                    (result.err_edit_wyt !== undefined)?display_bonus_error(result.err_edit_wyt):false;
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

function display_bonus_error(results)
{
    var len = results.length;
    for(x=0;x<len;x++){
        if(err_bonus.length !==  len)
        {
            err_bonus += '<li class="list-group-item border-0 text-danger">'+results[x]+'</li>';
        }
    }
    $("#error_bonus_entry").html(err_bonus);
}

function count_logic()
    {
        var amount = $("#amount").val();
        if(amount.length > 0)
        {
            $("#amount").val(formatNumber(amount));
        }

        $("#amount").on("keyup",delay(function(e){
            var coin = $(this).val();
            $("#amount").val(formatNumber(coin));
        },100));
    }

function datetimepicker()
{
    var date, tdate;
    var ndate = new Date();
    var date_1 = $('.datetimepicker_1').val();
    var date_2 = $('.datetimepicker_2').val();
    var date_3 = $('.datetimepicker_3').val();

    (date_1.length == 0)?date = ndate : date = moment(date_1);
    (date_2.length == 0)?tdate = ndate.setDate(date.getDate() + 2) : tdate = moment(date_2);
    (date_3.length == 0)?adate = ndate.setDate(date.getDate() + 2) : adate = moment(date_3);
    

    var format_date = 'YYYY-MM-DD HH:mm';

    $('.datetimepicker_1').datetimepicker({
        format : format_date,
        minDate : date
    });
    
    $('.datetimepicker_2').on('focusin', function(e){ 
        $(this).datetimepicker({
            format : format_date,
            defaultDate : tdate
        });
    });

    $('.datetimepicker_3').datetimepicker({
        format : format_date,
        minDate : adate
    });
}

function editor()
{
    @if(isset($event))
        var editor = '{!! $editor !!}';
        $("#editor").html(editor);
    @endif

    $('#editControls a').click(function(e) {
        switch($(this).data('role')) {
        default:
            document.execCommand($(this).data('role'), false, null);
            break;
        }
    });
    $('#editControls .fontsize').change(function(e) {
        console.log($(this).val());
        switch($(this).val()) {
            case 'h3':
                document.execCommand("fontSize", false, "1");
            break;
            case 'h2':
                document.execCommand("fontSize", false, "5");
                break;
            case 'h1':
                document.execCommand("fontSize", false, "7");
                break;
            case 'normal':
                document.execCommand("removeFormat", false);
                break;
            default:
                document.execCommand("fontSize", false, null);
                break;
        }
    });
}
</script>
<script src="{{ asset('assets/js/counting.js') }}" type="text/javascript"></script>
@endsection
