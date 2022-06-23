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
            <h5 class="info">Mailchimp</h5>
            <input value="{{ $user->mailchimp_api }}" type="text" name="mail_api" class="form-control form-control-lg" />
            <div class="text-black-50 mt-2"><small>{{ Lang::get('table.can') }} mailchimp</small></div>
        </div>
    </div>

    <!-- SENDFOX -->
    <div class="row border-top py-4">
        <div class="col-lg-3 text-center">
            <img class="act" src="{{ asset('assets/img/int-logo-sendfox.svg') }}" />
        </div>
        <div class="col-lg-9">
            <h5 class="info">Sendfox</h5>
            <input value="{{ $user->sendfox_api }}" type="text" name="sendfox_api" class="form-control form-control-lg" />
            <div class="text-black-50 mt-2"><small>{{ Lang::get('table.can') }} sendfox</small></div>
        </div>
    </div>

    <!-- WABLAS & WAFONNTE -->
    <div class="row border-top py-4">
        <div class="col-lg-3 text-center">
            <img class="act" src="{{ asset('assets/img/int-logo-sendfox.svg') }}" />
        </div>
        <div class="col-lg-9">
            <h5 class="info">PHONE</h5>
            <span id="msg_phone"><!--  --></span>
            <form id="admin_phone"> 
                <div class="form-group mb-4">
                    <label>Service</label>
                    <select class="form-select" name="service">
                        <option value="1">WABLAS</option>
                        <option value="2">WAFONNTE</option>
                    </select>
                </div>
                <div class="form-group mb-4 wablas-server">
                    <label>WABLAS Server</label>
                    <select class="form-select" name="wablas"> 
                        @foreach($helper::get_wablas() as $value=>$row)
                            <option value="{{ $value }}">{{ $row }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-4">
                    <label>Nomor WA</label>
                    <input name="phone" required="true" type="text" class="form-control"/>
                </div>
                <div class="form-group mb-4"> 
                    <label>API KEY</label> 
                    <input name="api_key" required="true" type="text" class="form-control"/>
                </div>
                <input name="phone_id" type="hidden" class="form-control"/>
                <button type="button" id="wa" class="btn btn-success">Tambah No WA</button>
                <button type="button" id="cancel" class="btn btn-secondary">Batal</button>
            </form>
            <!--  -->
        </div>
    </div>

    <button class="btn bg-custom btn-lg text-white">{{ Lang::get('table.update') }} API</button>
    </form>
</div>