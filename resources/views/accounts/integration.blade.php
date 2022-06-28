<div class="container">
    <h3 class="mb-4 account-title"><b><i class="fas fa-plug main-color"></i>&nbsp;{{ Lang::get('table.api') }}</b></h3>

    <form id="api">
    <!-- +++ temp ACIVRESPON
    <div class="row border-top py-4">
        <div class="col-lg-3 text-center">
            <img class="act" src="{{ asset('assets/img/activrespon.png') }}" />
        </div>
        <div class="col-lg-9">
            <h5 class="info">Activrespon</h5>
            <input value="{{ $user->activrespon_api }}" type="text" name="act_api" class="form-control form-control-lg" />
            <div class="text-black-50 mt-2"><small>{{ Lang::get('table.can') }} activrespon list</small></div>
        </div>
    </div> +++ -->

    <!-- MAILCHIMP -->
    <div class="row border-top py-4">
        <div class="col-lg-3 text-center">
            <img class="act" src="{{ asset('assets/img/int-logo-mailchip.svg') }}" />
        </div>
        <div class="col-lg-9">
            <h5 class="info"><a class="text-dark" target="_blank" norefferer noopener href="https://mailchimp.com/">Mailchimp</a></h5>
            <input value="{{ $user->mailchimp_api }}" type="text" name="mail_api" class="form-control form-control-lg" />
            <div class="text-black-50 mt-2"><small>{{ Lang::get('table.click') }} mailchimp <a class="main-color" target="_blank" norefferer noopener href="https://mailchimp.com/">{{ Lang::get('table.click.here') }}</a></small></div>
        </div>
    </div>

    <!-- SENDFOX -->
    <div class="row border-top py-4">
        <div class="col-lg-3 text-center">
            <img class="act" src="{{ asset('assets/img/int-logo-sendfox.svg') }}" />
        </div>
        <div class="col-lg-9">
            <h5 class="info"><a class="text-dark" target="_blank" norefferer noopener href="https://sendfox.com/">Sendfox</a></h5>
            <input value="{{ $user->sendfox_api }}" type="text" name="sendfox_api" class="form-control form-control-lg" />
            <div class="text-black-50 mt-2"><small>{{ Lang::get('table.click') }} sendfox <a class="main-color" target="_blank" norefferer noopener href="https://sendfox.com/">{{ Lang::get('table.click.here') }}</a></small></div>
        </div>
    </div>

    <!-- WABLAS & WAFONNTE -->
    @if($user->membership !== 'free' || $user->is_admin == 1)
    <div class="row border-top py-4">
        <div class="col-lg-3 text-center">
            <!-- <img class="img_wablas if(!is_null($phone) && $phone->service_id == 2) d-none endif" src="{{ asset('assets/img/wablas.png') }}" /> -->
            <!-- <img class="img_fonnte if(!is_null($phone) && $phone->service_id == 1 || is_null($phone)) d-none endif" src="{{ asset('assets/img/wafonnte.png') }}" /> -->
            <img class="img_fonnte" src="{{ asset('assets/img/wafonnte.png') }}" />
        </div>
        <div class="col-lg-9">
            <h5 class="info">Connect FONNTE</span></h5>
            <small class="text-black-50">{{ Lang::get('table.click') }} FONNTE <a class="main-color" target="blank" href="https://md.fonnte.com/register?ref=16">{{ Lang::get('table.click.here') }}</a></small>
            <span id="msg_phone"><!-- validation error --></span>
           
            <div class="form-group mt-3 mb-4">
                <label>Service</label>
                <select class="form-select" name="service">
                   <!--  <option if(!is_null($phone) && $phone->service_id == 1) selected endif value="1">WABLAS</option> -->
                    <option @if(!is_null($phone) && $phone->service_id == 2) selected @endif value="2">FONNTE</option>
                </select>
                <span class="text-danger err_service"><!-- error --></span>
            </div>
           <!--  <div class="form-group mb-4 wablas-server">
                <label>WABLAS Server</label>
                <select class="form-select" name="wablas"> 
                    foreach($helper::get_wablas() as $value=>$row)
                        <option if(!is_null($phone) && $phone->device_id == $value) selected endif value=" value "> row </option>
                    endforeach
                </select>
                <span class="text-danger err_wablas"></span>
            </div> -->
            @if(!is_null($phone))
                <div class="form-group mb-4">
                    <label>Nomor Saya</label>
                    <input readonly disabled value="{{ $phone->number }}" type="text" class="form-control my_phone"/>
                </div>
            @endif
            <!-- in case if user create phone -->
            <div class="mph form-group mb-4 d-none">
                <label>Nomor Saya</label>
                <input readonly disabled type="text" class="form-control my_phone"/>
            </div>
            <div class="form-group mb-4">
                <label>Nomor WA</label>
                <input id="phone" name="phone" type="text" class="form-control"/>
                @if(!is_null($phone))<small class="text-black-50">{{ Lang::get('auth.phone.empty') }}</small>@endif
                <span class="text-danger err_phone"><!-- error --></span>
            </div>
            <div class="form-group mb-4"> 
                <label>API KEY</label> 
                <input name="api_key" value="@if(!is_null($phone)) {{ $phone->device_key }} @endif" type="text" class="form-control"/>
                <span class="text-danger err_api_key"><!-- error --></span>
            </div>
            <!--  -->
            @if(!is_null($phone))
                <button id="del_phone" type="button" class="btn btn-sm btn-danger">{{ Lang::get('auth.phone.del') }}</button>
            @endif
        </div>
    </div>
    @endif

    <button class="btn bg-custom btn-lg text-white">{{ Lang::get('table.update') }} API</button>
    </form>
</div>