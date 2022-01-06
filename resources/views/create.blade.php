@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">Create Giveaway</h1>
        </div>

        <div class="col-md-8">
            <form>
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
                        <div class="row mb-3">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Start At:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="title" />
                            </div> 
                            <div class="form-group col-md-6 col-lg-6">
                                <label>End At:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="title" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Awarded At:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="title" />
                            </div> 
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Number Of Winners:<span class="text-danger">*</span></label>
                                <input type="number" min="1" class="form-control form-control-lg w-25" name="winner" />
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
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
                                <input type="text" class="form-control form-control-lg" name="title" />
                            </div> 
                            <div class="form-group col-md-6 col-lg-6">
                                <label>URL:<span class="text-danger">*</span></label>
                                <input placeholder="http://" type="text" class="form-control form-control-lg" name="title" />
                            </div>
                        </div>
                        <!-- new line -->
                        <div class="border-bottom info">What Are You Giving Away?</div>
                        <div class="row mb-3">
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Prize Name:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="title" />
                            </div> 
                            <div class="form-group col-md-6 col-lg-6">
                                <label>Prize Value:<span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text" id="inputGroup-sizing-lg">Rp</span>
                                    <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
                                </div>
                            </div>
                        </div>
                        <!-- new line -->
                        <div class="border-bottom info">Prize Images Tip: use images with a 2x1 ratio (minimum of 680px width).</div>
                        <div class="d-flex flex-wrap mb-1">
                            <div class="add-image-file image-file" style="background-image: url('/img/add-image.svg');">
                                <input type="file" name="images[]" accept=".jpg,.jpeg,.png" class="image-file-input" title="Click to edit" style="cursor: pointer;">
                                <p>Add cover image <span class="text-warning">*</span></p>
                            </div>
                        </div>
                        <!-- end form -->
                    </div>
                </div>

                <!-- form 2 -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-color main-theme">Sharing</h3>
                        <div class="text-justify">Click to select the platforms you want your contestants to use to share your giveaway:</div>
                        <div class="giveaway-icons">
                            <div class="mx-auto icon-wrapper">
                                <i class="fab fa-twitter box"></i>
                                <i class="fab fa-facebook-f box"></i>
                                <i class="fab fa-whatsapp box"></i>
                                <i class="fab fa-linkedin-in box"></i>
                                <i class="far fa-envelope box"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- form 3 -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body">
                        <h3 class="main-color main-theme">Bonus Entries</h3>
                        <div class="text-justify fst-italic border-bottom py-3 mb-4">These are actions a contestant can take to get even more entries.</div>
                        <div class="col-lg-6">
                            <select class="form-select">
                                <option>Add Entry Action</option>
                                <optgroup label="Social Follow">
                                    <option>Facebook Like</option>
                                    <option>Instagram Follow</option>
                                    <option>Twitter Follow</option>
                                    <option>Youtube Subscribe</option>
                                    <option>Podcast Subscribe</option>
                                </optgroup>
                                <optgroup label="Other">
                                    <option>Daily Entries</option>
                                    <option>Click a Link</option>
                                    <option>Watch Youtube Video</option>
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
                        <h3 class="main-theme">EU GDPR consent checkbox</h3>
                        <p class="fcs italic">Are you planning to send your entrants marketing messages after the giveaway? Are any of your contestants located in the EU? If yes, or you’re not sure, enable the checkbox option below so your contestants can give clear consent as required by EU GDPR.</p>
                        <input type="checkbox" class="form-checkbox me-1" />&nbsp;Require GDPR Consent
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
                document.execCommand("fontSize", false, "2");
            break;
            case 'h2':
                document.execCommand("fontSize", false, "4");
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
});
</script>
@endsection
