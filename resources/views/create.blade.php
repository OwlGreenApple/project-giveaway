@extends('layouts.app')

<style>.text-danger {font-size: 14px;}</style>

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">{{ Lang::get('title.create') }}</h1>
        </div>

        <div id="err_scroll" class="col-md-8">
            <div id="msg">@if(Cookie::get('url') !== null) <div class="alert alert-info text-center">{{ Lang::get('custom.link') }} : <a target="_blank" rel="noopener noreferrer" class="main-color" href="{!! Cookie::get('url') !!}">{{ Lang::get('custom.click') }}</a></div> @endif</div>
            <form id="create_event">
                <!-- form 1 -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-color main-theme">{{ Lang::get('giveaway.information') }}</h3>
                        <div class="border-bottom info">{{ Lang::get('giveaway.information.competiton') }}</div>

                        <!-- begin form -->
                        <div class="form-group mb-3">
                            <label>{{ Lang::get('giveaway.title') }}:<span class="text-danger">*</span></label>
                            <input type="text" @if(isset($event)) value="{{ $event->title }}" @endif class="form-control form-control-lg" name="title" />
                            <span class="text-danger err_title"><!-- --></span>
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ Lang::get('giveaway.description') }}:<span class="text-danger">*</span></label>
                            <div id="editparent">
                                <div id='editControls' class="py-2">
                                    <div class='btn-group'>
                                        <select class="fontsize form-select form-select-sm">
                                            <option value='normal'>{{ Lang::get('giveaway.font.normal') }}</option>
                                            <option value='h1'>{{ Lang::get('giveaway.font.large') }}</option>
                                            <option value='h2'>{{ Lang::get('giveaway.font.medium') }}</option>
                                            <option value='h3'>{{ Lang::get('giveaway.font.small') }}</option>
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
                                <div id='editor' contenteditable>@if(isset($event)) {!! $editor !!} @endif</div>
                            </div>
                            <span class="text-danger err_desc"><!-- --></span>
                        </div>
                        <div class="row mb-3 input-daterange">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>{{ Lang::get('giveaway.start') }}:<span class="text-danger">*</span></label>
                                <input @if(isset($event)) value="{{ $event->start }}" @endif type="text" class="form-control form-control-lg datetimepicker_1" name="start" />
                                <span class="text-danger err_start"><!-- --></span>
                            </div>
                            <div class="form-group col-md-6 col-lg-6">
                                <label>{{ Lang::get('giveaway.end') }}:<span class="text-danger">*</span></label>
                                <input @if(isset($event)) value="{{ $event->end }}" @endif type="text" class="form-control form-control-lg datetimepicker_2" name="end" />
                                <span class="text-danger err_end"><!-- --></span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>{{ Lang::get('giveaway.award') }}:<span class="text-danger">*</span></label>
                                <input @if(isset($event)) value="{{ $event->award }}" @endif type="text" class="form-control form-control-lg datetimepicker_3" name="award" />
                                <span class="text-danger err_award"><!-- --></span>
                            </div>
                            <div class="form-group col-md-6 col-lg-6">
                                <label>{{ Lang::get('giveaway.winner') }}:<span class="text-danger">*</span></label>
                                <input @if(isset($event)) value="{{ $event->winners }}" @endif type="number" min="1" max="50" class="form-control form-control-lg w-25" name="winner" />
                                <span class="text-danger err_winner"><!-- --></span>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>{{ Lang::get('giveaway.timezone') }}</label>
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
                        <div class="border-bottom info">{{ Lang::get('giveaway.who') }}</div>
                        <div class="row mb-3">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>{{ Lang::get('giveaway.name') }}:<span class="text-danger">*</span></label>
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
                        <div class="border-bottom info">{{ Lang::get('giveaway.what') }}</div>
                        <div class="row mb-3">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>{{ Lang::get('giveaway.prize.name') }}:<span class="text-danger">*</span></label>
                                <input name="prize_name" @if(isset($event)) value="{{ $event->prize_name }}" @endif type="text" class="form-control form-control-lg" />
                                <span class="text-danger err_prize_name"><!-- --></span>
                            </div>
                            <div class="form-group col-md-6 col-lg-6">
                                <label>{{ Lang::get('giveaway.prize.value') }}:<span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                @if(count($helper::currency()) > 0)
                                <div class="input-group mb-3">
                                    <select style="max-width:95px" class="form-select form-select-lg bg-custom text-white" name="currency">
                                        @foreach($helper::currency() as $idt=>$row)
                                            <option value="{{ $idt }}" @if((isset($event) && ($event->currency == $idt))) selected @endif>{{ $row }}</option>
                                        @endforeach
                                    </select>
                                    <input name="prize_amount" @if(isset($event)) value="{{ $event->prize_value }}" @endif id="amount" maxlength="8" type="text" class="form-control form-control-lg" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
                                    <span class="text-danger err_prize_amount"><!-- --></span>
                                    <span class="text-danger err_currency"><!-- --></span>
                                </div>
                                @endif
                                </div>
                            </div>
                        </div>
                        <!-- new line -->
                        <div class="border-bottom info">{{ Lang::get('giveaway.banner') }} / {{ Lang::get('giveaway.youtube') }}</div>
                        <div class="text-justify title mb-3">{{ Lang::get('giveaway.tips') }}</div>
                        <div class="form-check form-switch mb-2">
                            <input @if(isset($event) && $event->media == 1) value="on" checked @else value="off" @endif name="media_option" class="form-check-input" type="checkbox" id="media_option">
                            <label class="form-check-label" for="media_option">{{ Lang::get('giveaway.youtube') }}</label>
                        </div>

                        <div class="upload_banner form-group">
                            <label>Youtube URL:</label>
                            <input name="youtube_url" value="@if(isset($event)) {{ $event->youtube_banner }} @endif" type="text" class="form-control form-control-lg" />
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

                <!-- share link -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-color main-theme">{{ Lang::get('giveaway.sharing') }}</h3>
                        <div class="text-justify title">{{ Lang::get('giveaway.sharing.click') }}:</div>
                        <div class="giveaway-icons">
                            <div class="mx-auto icon-wrapper">
                                <i data-id="tw" class="fab fa-twitter box @if(isset($event)) @if($event->tw == 1) box-color @endif @else box-color @endif"></i>
                                <i data-id="fb" class="fab fa-facebook-f box @if(isset($event)) @if($event->fb == 1) box-color @endif @endif"></i>
                                <i data-id="wa" class="fab fa-whatsapp box @if(isset($event)) @if($event->wa == 1) box-color @endif @else box-color @endif"></i>
                                <i data-id="ln" class="fab fa-linkedin-in box @if(isset($event)) @if($event->ln == 1) box-color @endif @endif"></i>
                                <i data-id="mail" class="far fa-envelope box @if(isset($event)) @if($event->mail == 1) box-color @endif @endif"></i>
                                <i data-id="lnk" class="fas fa-link box @if(isset($event)) @if($event->link == 1) box-color @endif @else box-color @endif"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bonus Point -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-color main-theme">{{ Lang::get('giveaway.bonus') }}</h3>
                        <div class="title text-justify fst-italic border-bottom py-3 mb-4">{{ Lang::get('giveaway.bonus.desc') }}</div>

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
                                        <input @if($row['type'] == '7') placeholder="Youtube URL" @endif value="{{ $row['url'] }}" type="text" class="@if($row['type'] == '7')em_{{ $row['id'] }}@endif form-control form-control-lg emb" name="edit_url_{{ $row['mod'] }}[{{ $row['id'] }}]" />
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

                <!-- Integration -->
                @if($apicheck == true)
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-color main-theme">{{ Lang::get('giveaway.integration') }}</h3>

                         <!-- Activrespon -->
                        @if(count($act) > 0)
                        <div class="mb-2">
                            <div class="border-bottom info">Activrespon</div>
                            <select id="act_api_id" name="act_api_id" class="form-select">
                            <option value="0">Choose</option>
                                @foreach($act as $row)
                                    <option value="{{ $row['id'] }}" @if(isset($event) && $event->act_api_id == $row['id']) selected @endif>{{ $row['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Mailchimp -->
                        @if(count($mlc) > 0)
                        <div class="mb-2">
                            <div class="border-bottom info">Mailchimp</div>
                            <select id="mlc_api_id" name="mlc_api_id" class="form-select">
                            <option value="0">Choose</option>
                                @foreach($mlc as $row)
                                    <option value="{{ $row->id }}" @if(isset($event) && $event->mlc_api_id == $row->id) selected @endif>{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Sendfox -->
                        @if(count($sdf) > 0)
                        <div class="mb-2">
                            <div class="border-bottom info">Sendfox</div>
                            <select id="sdf_api_id" name="sdf_api_id" class="form-select">
                            <option value="0">Choose</option>
                                @foreach($sdf['data'] as $index=>$row) 
                                    <option value="{{ $row['id'] }}" @if(isset($event) && $event->sdf_api_id == $row['id']) selected @endif>{{ $row['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- admin contact / host contact -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-color main-theme">{{ Lang::get('giveaway.admin') }}</h3>
                        <p class="text-justify title">{{ Lang::get('giveaway.admin.desc') }}</p>
                        <input type="text" id="phone" name="phone" class="form-control form-control-lg" />
                        @if(isset($event))
                            <small class="main-color">{{ Lang::get('custom.phone') }}</small>
                            <div class="form-control disabled"> {{$event->admin_contact}}</div>
                        @endif
                        <span class="text-danger err_phone"><!-- --></span>
                    </div>
                </div>

                <!-- whatsapp message -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-color main-theme">{{ Lang::get('giveaway.wa') }}</h3>
                        <div class="text-justify title mb-4">{{ Lang::get('giveaway.wa.desc') }} <!--, {{ Lang::get('giveaway.connect') }}  <a target="_blank" class="main-color" href="{{ url('scan') }}">{{ Lang::get('giveaway.wa.connect') }}</a> --></div>

                        <div class="mb-3">
                            <div class="form-group mb-3">
                                <label>{{ Lang::get('giveaway.message') }}:<span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control form-control-lg">@if(isset($event)){{ $event->message }}@endif</textarea>
                                <span class="text-danger err_message"><!-- --></span>
                            </div>
                            <!-- <div class="form-group"> +++ temp +++
                                <label>{{ Lang::get('giveaway.message.img') }}</label>
                                <div class="mb-2">
                                    @if(isset($event) && $event->img_url == null)
                                        -
                                    @else
                                        @if(isset($event) && $obj->check_s3_image($event->img_url) !== null)
                                            <img src="{{$obj->check_s3_image($event->img_url)}}" width="100" />
                                        @endif
                                    @endif
                                </div>
                                <input type="file" class="form-control form-control-lg" name="media" />
                                <span class="text-danger err_media"></span>
                            </div> +++ temp +++ --> 
                        </div>
                    </div>
                </div>

                <!-- WA winner message -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-color main-theme">{{ Lang::get('giveaway.wa.winner') }}</h3>
                        <div class="text-justify title mb-4">{{ Lang::get('giveaway.wa.winner.desc') }}. <!-- {{ Lang::get('giveaway.connect') }} <a target="_blank" class="main-color" href="{{ url('scan') }}">{{ Lang::get('giveaway.wa.connect') }}</a>--></div>

                        <div class="mb-3">
                            <div class="form-check form-switch mb-2">
                                <input name="run_winner" class="form-check-input" type="checkbox" id="run_winner" @if(isset($event) && $event->winner_run == 1) value="on" checked @else value="off" @endif />
                                <label class="form-check-label">{{ Lang::get('giveaway.activate') }}</label>
                            </div>
                            <div class="form-group mb-3">
                                <label>{{ Lang::get('giveaway.message') }}:<span class="text-danger">*</span></label>
                                <textarea name="message_winner" class="form-control form-control-lg">@if(isset($event)){{ $event->winner_message }}@endif</textarea>
                                <span class="text-danger err_message_winner"><!-- --></span>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!isset($event) || (isset($event) && $event->status < 2))
                <div class="mt-5 text-center">
                    <span class="err_package"><!-- --></span>
                    <button type="button" class="btn btn-secondary btn-lg">{{ Lang::get('giveaway.cancel') }}</button>
                    <button type="submit" class="btn bg-custom btn-lg text-white">{{ Lang::get('giveaway.save') }}</button>
                </div>
                @endif

            </form>
        <!-- end col -->
        </div>

    </div>
</div>

<script src="{{ asset('/assets/intl-tel-input/callback.js') }}" type="text/javascript"></script>
<script>
$(function() {
    editor();
    // emoji();
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

function emoji()
{
    $("#divInput-description-post").emojioneArea({
        pickerPosition: "right",
        mainPathFolder : "{{url('')}}",
    });

    // $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText('if(isset($event)) $event->message endif');
}

function select_timezone()
{
    var timezone;
    @if(isset($timezone))
        timezone = '{{ $timezone }}';
    @else
        timezone = 'Asia/Jakarta';
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
      var pasted_data = pastedData.split("=");
      var data;

      // https://youtu.be/xxxxxx
      if(pasted_data[1] == undefined)
      {
        data = pastedData.split(".be");
        try
        {
            data = data[1].split("/");
            data = data[1];
        }
        catch(e)
        {
            data = pastedData.split("/");
            data = data[4];
        }
      }
      else
      {
        data = pasted_data[1];
      }

      setTimeout(function(){
        $(".em_"+id).val(data);
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
    var err_len = $(".errt_"+val).length; //for error messsage class

    $column = '';
    $column += '<div class="row mb-4 errt_'+val+' entries pos_'+len+'">';
    $column += '<div class="border-bottom info">'+title+' <a del_new_id='+len+' class="del-entry"><i class="far fa-trash-alt title"></i></a></div>';

    // activity
    $column += '<div class="form-group col-md-6 col-lg-6 mb-2">';
    $column += '<label>'+col_1+':<span class="text-danger">*</span></label>';
    $column += '<input type="text" class="form-control form-control-lg" name="new_text_'+val+'[]" />';
    $column += '<span class="text-danger err_new_text_'+val+'_'+err_len+'"></span>';//error activity
    $column += '</div>';

    // url
    if(val !== 'de')
    {
        $column += '<div class="form-group col-md-6 col-lg-6 mb-2">';
        $column += '<label>'+col_2+'<span class="text-danger">*</span></label>';

        if(val == 'wyt')
        {
            $column += '<input placeholder="your youtube url" autocomplete="off" type="text" class="em_new_'+len+' form-control form-control-lg emb" name="new_url_'+val+'[]" />';

        }
        else if(val == 'cl')
        {
            $column += '<input placeholder="{{ Lang::get("table.ex") }}" autocomplete="off" type="text" class="form-control form-control-lg" name="new_url_'+val+'[]" />';
        }
        else
        {
            $column += '<input type="text" autocomplete="off" class="form-control form-control-lg" name="new_url_'+val+'[]" />';
        }

        $column += '<span class="text-danger err_new_url_'+val+'_'+err_len+'"></span>';//error text
        $column += '</div>';
        $column += '<div class="form-group col-md-12 col-lg-12">';
    }
    else{
        $column += '<div class="form-group col-md-6 col-lg-6 mb-2">';
    }

    // point
    $column += '<label>'+col_3+'<span class="text-danger">*</span></label>'; 
    $column += '<div class="row g1">';
    $column += '<div class="col-auto">';
    $column += '<input type="number" min="1" class="form-control form-control-lg" name="new_entries_'+val+'[]" />';
    $column += '</div>';
    $column += '<span class="text-danger err_new_entries_'+val+'_'+err_len+'"></span>'; // error point

    if(val !== 'de')
    {
        $column += '<div class="col-auto">';
        $column += '<span class="form-text title">';
        $column += '{{ Lang::get("giveaway.worth") }}';
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

// DISPLAY VIDEO OR BANNER / RUNNING WINNER MESSAGE
function display_media()
{
    var val = $("#media_option").val();
    detect_video_or_banner(val);

    $("#media_option").click(function(){
        var value = $(this).val();
        detect_video_or_banner(value);
    });

    $("#run_winner").click(function(){
        var val = $(this).val();
        if(val == 'on')
        {
            $(this).val('off');
        }
        else
        {
            $(this).val('on');
        }
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
        $(".upload_banner").removeClass('d-none');
        $(".input-images").addClass('d-none');
    }
    else
    {
        $(".upload_banner").addClass('d-none');
        $(".input-images").removeClass('d-none');
    }
}

function image_uploader()
{
    $('.input-images').imageUploader({
        label :'{{ Lang::get("giveaway.max") }}',
        "{{ $preloaded }}": [
            @if(count($data) > 0)
                @foreach($data as $id=>$row)
                    {id: "{{ $id }}", src: "{{ $row }}"},
                @endforeach
            @endif
        ]
    });

    $("body").on("click",".delete-image",function(){
        var val = $("input[name='preloaded]").val();
        // console.log(val);
    });
}

function save_data()
{
    $("#create_event").submit(function(e){
        e.preventDefault();
        
        var desc = $("#editor").html();
        var len = $(".box-color").length;
        var form = $("#create_event")[0];
        var code = $(".iti__selected-flag").attr('data-code');
        var error = 0;
        
        var data = new FormData(form);
        data.append('desc',desc);
        data.append('pcode', code);

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
                    if(result.edit === 0)
                    {
                        location.href="{{ url('promo') }}/"+result.link;
                    }
                    else
                    {
                        location.href="{{ url('edit-event') }}/"+result.id;
                    }
                }
                else if(result.success == 2)
                {
                    error = 1;
                    $("#msg").html('<div class="alert alert-danger">{{ Lang::get("custom.error") }}</div>')
                }
                else if(result.success == 'err_end')
                {
                    error = 1;
                    $(".err_package").show();
                    $(".err_package").html('<div class="text-danger mb-3">'+result.message+'</div>');
                }
                else if(result.success == 'err_package')
                {
                    error = 1;
                    $(".err_package").show();
                    $(".err_package").html('<div class="text-danger mb-3">'+result.package+'</div>');
                }
                else if(result.success == 'err')
                {
                    error = 1;
                    $(".err_package").hide();
                    $(".text-danger").html('');
                    $.each(result,function(key, value)
                    {
                        $(".err_"+key).html(value);

                        // validation for tambah point / add task 
                        for($e=1;$e<=$(".entries").length;$e++)
                        {
                            $('.'+key).html(value); 
                        }

                        // new bonus entry validation
                        /* (result.err_fb !== undefined)?display_bonus_error(result.err_fb):false;
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
                     */});

                    $('html, body').animate({
                        scrollTop: $("#err_scroll").offset().top
                    }, 700);
                }
                else
                {
                    error = 1;
                    $("#msg").html('<div class="alert alert-danger">{{ Lang::get("custom.error.id") }}</div>');
                }
            },
            error : function(xhr)
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            },
            complete : function(){
                if(error == 1)
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            }
        });
    });
}

function display_bonus_error(key, ...results)
{
    console.log(results.length);
    var len = results.length;
    var err_bonus = '';

    // console.log(key +':'+ results+' -- '+len);

    /* for(x=0;x<len;x++){
        if(err_bonus.length !==  len)
        {
            err_bonus += '<li class="list-group-item border-0 text-danger">'+results[x]+'</li>';
        }
    }
    $("#error_bonus_entry").html(err_bonus); */
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
    var date, tdate, adate;
    var ndate = new Date();
   
    var date_2 = $('.datetimepicker_2');
    var date_3 = $('.datetimepicker_3').val();
    var format_date = 'YYYY-MM-DD HH:mm';

    $('.datetimepicker_1').on('focusin', function(e){
        $(this).datetimepicker({
            format : format_date,
            defaultDate : ndate,
            // debug: true
        });
    });

    $('.datetimepicker_2').on('focusin', function(e){
        $(this).datetimepicker({
            format : format_date,
            defaultDate : ndate
        });
    });

    $('.datetimepicker_3').datetimepicker({
        format : format_date,
    });
}

function editor()
{
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
