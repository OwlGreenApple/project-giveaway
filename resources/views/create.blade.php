@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">Create Giveaway</h1>
        </div>

        <div class="col-md-8">
            <form id="create_event">
                <!-- form 1 -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-color main-theme">Giveaway Information</h3>
                        <div class="border-bottom info">Competition Information</div>
                        
                        <!-- begin form -->
                        <div class="form-group mb-3">
                            <label>Title:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" name="title" />
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
                        </div> 
                        <div class="row mb-3 input-daterange">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Start At:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg datetimepicker_1" name="start" />
                            </div> 
                            <div class="form-group col-md-6 col-lg-6">
                                <label>End At:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg datetimepicker_2" name="end" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Awarded At:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg datetimepicker_3" name="award" />
                            </div> 
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Number Of Winners:<span class="text-danger">*</span></label>
                                <input type="number" min="1" class="form-control form-control-lg w-25" name="winner" />
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="unl_cam" id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault">
                            Unlimited Campaign
                            </label>
                        </div>
                        <div class="form-group mb-3">
                            <label>Timezone</label>
                            <select class="form-select" name="timezone" id="timezone" required="">
                                <option value="Pacific/Auckland">(UTC -11) Auckland</option>
                                <option value="Pacific/Tahiti">(UTC -10) Papeete</option>
                                <option value="America/Anchorage">(UTC -9) Anchorage </option>
                                <option value="America/Los_Angeles">(UTC -8) San Francisco</option>
                                <option value="America/Denver">(UTC -7) Salt Lake City</option>
                                <option value="America/Chicago">(UTC -6) Dallas</option>
                                <option value="America/New_York" selected="">(UTC -5) New York</option>
                                <option value="America/Guyana">(UTC -4) Georgetown</option>
                                <option value="America/Sao_Paulo">(UTC -3) Rio De Janeiro</option>
                                <option value="Atlantic/South_Georgia">(UTC -2) King Edward Point</option>
                                <option value="Atlantic/Cape_Verde">(UTC -1) Praia</option>
                                <option value="Europe/Dublin">(UTC +0) Dublin</option>
                                <option value="Europe/Paris">(UTC +1) Paris</option>
                                <option value="Europe/Helsinki">(UTC +2) Helsinki</option>
                                <option value="Europe/Moscow">(UTC +3) Moscow</option>
                                <option value="Asia/Dubai">(UTC +4) Abu Dhabi</option>
                                <option value="Asia/Karachi">(UTC +5) Islamabad</option>
                                <option value="Asia/Dhaka">(UTC +6) Dhaka</option>
                                <option value="Asia/Bangkok">(UTC +7) Bangkok</option>
                                <option value="Asia/Hong_Kong">(UTC +8) Hong Kong</option>
                                <option value="Asia/Tokyo">(UTC +9) Tokyo</option>
                                <option value="Australia/Brisbane">(UTC +10) Cairns</option>
                                <option value="Pacific/Efate">(UTC +11) Port Vila</option>
                                <option value="Asia/Anadyr">(UTC +12) Anadyr</option>
                        </select>
                        </div> 
                        <!-- new line -->
                        <div class="border-bottom info">Who's Running This Giveaway?</div>
                        <div class="row mb-3">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Name:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="owner_name" />
                            </div> 
                            <div class="form-group col-md-6 col-lg-6">
                                <label>URL:<span class="text-danger">*</span></label>
                                <input placeholder="http://" type="text" class="form-control form-control-lg" name="owner_url" />
                            </div>
                        </div>
                        <!-- new line -->
                        <div class="border-bottom info">What Are You Giving Away?</div>
                        <div class="row mb-3">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Prize Name:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="prize_name" />
                            </div> 
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Prize Value:<span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text" id="inputGroup-sizing-lg">Rp</span>
                                    <input name="prize_amount" id="amount" maxlength="8" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
                                </div>
                            </div>
                        </div>
                        <!-- new line -->
                        <div class="border-bottom info">Prize Images / Youtube Video.</div>
                        <div class="text-justify title mb-3">Tip: use images with a 2x1 ratio (minimum of 680px width)</div>
                        <div class="form-check form-switch mb-2">
                            <input name="media_option" class="form-check-input" type="checkbox" id="media_option">
                            <label class="form-check-label" for="media_option">Youtube Video</label>
                            <span class="text-danger">*</span>
                        </div>

                        <div class="upload_banner form-group d-none">
                            <label>Youtube URL:</label>
                            <input type="text" class="form-control form-control-lg" name="youtube_url" />
                        </div>

                        <div class="input-images"><!-- display preview here --></div>
                        @if(count($data) > 0)
                            <!-- important to show which image would be deleted -->
                            @foreach($data as $id=>$row)
                                <input type="hidden" value="{{ $id }}" name="list[]" />
                            @endforeach
                        @endif
                       
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
                                <i data-id="tw" class="fab fa-twitter box box-color"></i>
                                <i data-id="fb" class="fab fa-facebook-f box"></i>
                                <i data-id="wa" class="fab fa-whatsapp box box-color"></i>
                                <i data-id="ln" class="fab fa-linkedin-in box"></i>
                                <i data-id="mail" class="far fa-envelope box"></i>
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
                        <div class="row mb-3">
                            <div class="border-bottom info">Facebook Like <a><i class="far fa-trash-alt title"></i></a></div>
                            <div class="form-group col-md-6 col-lg-6 mb-2">
                                <label>Action Text:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="title" />
                            </div> 
                            <div class="form-group col-md-6 col-lg-6 mb-2">
                                <label>URL<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="winner" />
                            </div>
                        
                            <div class="form-group col-md-12 col-lg-12">
                                <label>Number Of Entries<span class="text-danger">*</span></label>
                                <div class="row g1">
                                    <div class="col-auto">
                                        <input type="number" min="1" class="form-control form-control-lg" name="winner" />
                                    </div>
                                    <div class="col-auto">
                                        <span class="form-text title">
                                            How many entries this action is worth
                                        </span>
                                    </div>
                                </div>
                                <!--  -->
                            </div>
                        </div>
                        <input type="hidden" name="entries[]" />
                        @endif
                        <!-- end logic bonus entry -->

                        <!-- display bonus entry column -->
                        <div id="bonus_entry"><!-- --></div>
                        
                        <div class="col-lg-6 mt-4">
                            <select id="bonus" class="form-select">
                                <option>Add Entry Action</option>
                                <optgroup label="Social Follow">
                                    <option value="fb">Facebook Like</option>
                                    <option value="ig">Instagram Follow</option>
                                    <option value="tw">Twitter Follow</option>
                                    <option value="yt">Youtube Subscribe</option>
                                    <option value="pt">Podcast Subscribe</option>
                                </optgroup>
                                <optgroup label="Other">
                                    <option value="de">Daily Entries</option>
                                    <option value="cl">Click a Link</option>
                                    <option value="wyt">Watch Youtube Video</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- form 4 -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-color main-theme">Integration</h3>
                        <div class="border-bottom info">Activrespon</div>
                        <select class="form-select">
                            <option>List 1</option>
                            <option>List 2</option>
                        </select>
                    </div>
                </div>

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
});

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
        $(".pos_"+pos).remove();
    });
}

function column_entry(val)
{
    var title, col_1, col_2;
    var col_3 = 'Number Of Entries';

    if(val == 'fb')
    {
        title = 'Facebook Like';
        col_1 = 'Action Text';
        col_2 = 'URL';
    }     
    else if(val == 'ig')
    {
        title = 'Instagram Follow';
        col_1 = 'Action Text';
        col_2 = 'Instagram Username';
    }
    else if(val == 'tw')
    {
        title = 'Twitter Follow';
        col_1 = 'Action Text';
        col_2 = 'Twitter Username';
    }
    else if(val == 'yt')
    {
        title = 'YouTube Subscribe';
        col_1 = 'Action Text';
        col_2 = 'YouTube Channel URL';
    }
    else if(val == 'pt')
    {
        title = 'Podcast Subscribe';
        col_1 = 'Action Text';
        col_2 = 'Podcast URL';
    }
    else if(val == 'de')
    {
        title = 'Daily Entries';
        col_1 = 'Action Text';
    }
    else if(val == 'cl')
    {
        title = 'Click a Link';
        col_1 = 'Action Text';
        col_2 = 'Action URL';
    }
    else if(val == 'wyt')
    {
        title = 'Watch a YouTube Video';
        col_1 = 'Action Text';
        col_2 = 'YouTube Video URL';
    }
    else
    {
        return false;
    }

    var len = $(".entries").length;

    $column = '';
    $column += '<div class="row mb-3 entries pos_'+len+'">';
    $column += '<div class="border-bottom info">'+title+' <a del_new_id='+len+' class="del-entry"><i class="far fa-trash-alt title"></i></a></div>';
   
    $column += '<div class="form-group col-md-6 col-lg-6 mb-2">';
    $column += '<label>'+col_1+':<span class="text-danger">*</span></label>';
    $column += '<input type="text" class="form-control form-control-lg" name="new_text_'+val+'[]" />';
    $column += '</div>';

    if(val !== 'de')
    {
        $column += '<div class="form-group col-md-6 col-lg-6 mb-2">';
        $column += '<label>'+col_2+'<span class="text-danger">*</span></label>';
        $column += '<input type="text" class="form-control form-control-lg" name="new_url_'+val+'[]" />';
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
    $("#media_option").click(function(){
        var val = $(this).val();
        if(val == 'on')
        {
            $(this).val('off');
        }
        else
        {
            $(this).val('on');
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
    });
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
                // $('#loader').show();
                // $('.div-loading').addClass('background-load');
                // $(".error").hide();
            },
            success : function(result)
            {

            },
            error : function(xhr)
            {

            }
        });
    });
}

function count_logic()
    {
        $("#amount").on("keyup",delay(function(e){
            var coin = $(this).val();
            $("#amount").val(formatNumber(coin));
        },100));
    }

function datetimepicker()
{
    var date = new Date();
    var format_date = 'YYYY-MM-DD HH:mm';

    $('.datetimepicker_1').datetimepicker({
        format : format_date,
        minDate : date
    });
    
    $('.datetimepicker_2, .datetimepicker_3').on('focusin', function(e){ 
        $(this).datetimepicker({
            format : format_date,
            defaultDate : date.setDate(date.getDate() + 2)
        });
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
