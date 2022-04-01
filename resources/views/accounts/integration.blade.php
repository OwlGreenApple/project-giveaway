<div class="container">
    <h3 class="mb-4 account-title"><b><i class="fas fa-plug main-color"></i>&nbsp;{{ Lang::get('table.api') }}</b></h3>

    <form id="api">
    <!-- API 1 -->
    <div class="row border-top py-4">
        <div class="col-lg-3 text-center">
            <img class="act" src="{{ asset('assets/img/activrespon.png') }}" />
        </div>
        <div class="col-lg-9">
            <h5 class="info">Activrespon</h5>
            <input value="{{ $user->activrespon_api }}" type="text" name="act_api" class="form-control form-control-lg" />
            <div class="text-black-50 mt-2"><small>{{ Lang::get('table.can') }} activrespon list</small></div>
        </div>
    </div>

    <!-- API 2 -->
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

    <button class="btn bg-custom btn-lg text-white">{{ Lang::get('table.update') }} API</button>
    </form>
</div>